<?php
$id = $_SESSION['user_id'];
$user = DB::getRow('SELECT * FROM `users` WHERE `id` = ?',array($id));
$position = DB::getRow('SELECT * FROM `positions` WHERE `id` = ?', array($user['position_id']));

//require_once dirname(__FILE__) . '/php-rtf-parser-master/extract-text-from-rtf.php';


function rtf_isPlainText($s) {
	$failAt = array("*", "fonttbl", "colortbl", "datastore", "themedata", "stylesheet", "info", "picw", "pich");
	for ($i = 0; $i < count($failAt); $i++)
		if (!empty($s[$failAt[$i]])) return false;
	return true;
}

# Mac Roman charset for czech layout
function from_macRoman($c) {
	$table = array(
		0x83 => 0x00c9, 0x84 => 0x00d1, 0x87 => 0x00e1, 0x8e => 0x00e9, 0x92 => 0x00ed,
		0x96 => 0x00f1, 0x97 => 0x00f3, 0x9c => 0x00fa, 0xe7 => 0x00c1, 0xea => 0x00cd,
		0xee => 0x00d3, 0xf2 => 0x00da
	);
	if (isset($table[$c]))
		$c = "&#x".sprintf("%04x", $table[$c]).";";
	return $c;
}

function rtf2text($filename) {
	// Пытаемся прочить данные из переданного нам rtf-файла
	$text = file_get_contents($filename);
	if (!strlen($text))
		return "";

	# Speeding up via cutting binary data from large rtf's.
	if (strlen($text) > 1024 * 1024) {
		$text = preg_replace("#[\r\n]#", "", $text);
		$text = preg_replace("#[0-9a-f]{128,}#is", "", $text);
	}

	# For Unicode escaping
	$text = str_replace("\\'3f", "?", $text);
	$text = str_replace("\\'3F", "?", $text);


	$document = "";
	$stack = array();
	$j = -1;

	$fonts = array();

	// Читаем посимвольно данные...
	for ($i = 0, $len = strlen($text); $i < $len; $i++) {
		$c = $text[$i];

		// исходя из текущего символа выбираем, что мы с данными будем делать.
		switch ($c) {
			// итак, самый важный ключ "обратный слеш"
			case "\\":
				// читаем следующий символ, чтобы понять, что нам делать дальше
				$nc = $text[$i + 1];

				// Если это другой бэкслеш, или неразрывный пробел, или обязательный
				// дефис, то мы вставляем соответствующие данные в выходной поток
				// (здесь и далее, в поток втавляем только в том случае, если перед
				// нами именно текст, а не шрифтовая вставка, к примеру).
				if ($nc == '\\' && rtf_isPlainText($stack[$j])) $document .= '\\';
				elseif ($nc == '~' && rtf_isPlainText($stack[$j])) $document .= ' ';
				elseif ($nc == '_' && rtf_isPlainText($stack[$j])) $document .= '-';
				// Если перед нами символ звёздочки, то заносим информацию о нём в стек.
				elseif ($nc == '*') $stack[$j]["*"] = true;
				// Если же одинарная кавычка, то мы должны прочитать два следующих
				// символа, которые являются hex-ом символа, который мы должны
				// вставить в наш выходной поток.
				elseif ($nc == "'") {
					$hex = substr($text, $i + 2, 2);
					if (rtf_isPlainText($stack[$j])) {
						#echo $hex." ";
						#dump($stack[$j], false);
						#dump($fonts, false);
						if (!empty($stack[$j]["mac"]) || @$fonts[$stack[$j]["f"]] == 77)
							$document .= from_macRoman(hexdec($hex));
						elseif (@$stack[$j]["ansicpg"] == "1251" || @$stack[$j]["lang"] == "1029")
							$document .= chr(hexdec($hex));
						else
							$document .= "&#".hexdec($hex).";";
					}
					#dump($stack[$j], false);
					// Мы прочитали два лишних символа, должны сдвинуть указатель.
					$i += 2;
					// Так перед нами буква, а это значит, что за \ идёт упраляющее слово
					// и возможно некоторый циферный параметр, которые мы должны прочитать.
				} elseif ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
					$word = "";
					$param = null;

					// Начинаем читать символы за бэкслешем.
					for ($k = $i + 1, $m = 0; $k < strlen($text); $k++, $m++) {
						$nc = $text[$k];
						// Если текущий символ буква и до этого не было никаких цифр,
						// то мы всё ещё читаем управляющее слово, если же были цифры,
						// то по документации мы должны остановиться - ключевое слово
						// так или иначе закончилось.
						if ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
							if (empty($param))
								$word .= $nc;
							else
								break;
							// Если перед нами цифра, то начинаем записывать параметр слова.
						} elseif ($nc >= '0' && $nc <= '9')
							$param .= $nc;
						// Минус может быть только перед цифровым параметром, поэтому
						// проверяем параметр на пустоту или в противном случае
						// мы вылезли за пределы слова с параметром.
						elseif ($nc == '-') {
							if (empty($param))
								$param .= $nc;
							else
								break;
							// В любом другом случае - конец.
						} else
							break;
					}
					// Сдвигаем указатель на количество прочитанных нами букв/цифр.
					$i += $m - 1;

					// Начинаем разбираться, что же мы такое начитали. Нас интересует
					// именно управляющее слово.
					$toText = "";
					switch (strtolower($word)) {
						// Если слово "u", то параметр - это десятичное представление
						// unicode-символа, мы должны добавить его в выход.
						// Но мы должны учесть, что за символом может стоять его
						// замена, в случае, если программа просмотрщик не может работать
						// с Unicode, поэтому при наличии \ucN в стеке, мы должны откусить
						// "лишние" N символов из исходного потока.
						case "u":
							$toText .= html_entity_decode("&#x".sprintf("%04x", $param).";");
							$ucDelta = !empty($stack[$j]["uc"]) ? @$stack[$j]["uc"] : 1;
							/*for ($k = 1, $m = $i + 2; $k <= $ucDelta && $m < strlen($text); $k++, $m++) {
								$d = $text[$m];
								if ($d == '\\') {
									$dd = $text[$m + 1];
									if ($dd == "'")
										$m += 3;
									elseif($dd == '~' || $dd == '_')
										$m++;
								}
							}
							$i = $m - 2;*/
							#$i += $m - 2;
							if ($ucDelta > 0)
								$i += $ucDelta;
							break;
						// Обработаем переводы строк, различные типы пробелов, а также символ
						// табуляции.
						case "par": case "page": case "column": case "line": case "lbr":
						$toText .= "\n ||";
						break;
						case "emspace": case "enspace": case "qmspace":
						$toText .= " ";
						break;
						case "tab": $toText .= "\t"; break;
						// Добавим вместо соответствующих меток текущие дату или время.
						case "chdate": $toText .= date("m.d.Y"); break;
						case "chdpl": $toText .= date("l, j F Y"); break;
						case "chdpa": $toText .= date("D, j M Y"); break;
						case "chtime": $toText .= date("H:i:s"); break;
						// Заменим некоторые спецсимволы на их html-аналоги.
						case "emdash": $toText .= html_entity_decode("&mdash;"); break;
						case "endash": $toText .= html_entity_decode("&ndash;"); break;
						case "bullet": $toText .= html_entity_decode("&#149;"); break;
						case "lquote": $toText .= html_entity_decode("&lsquo;"); break;
						case "rquote": $toText .= html_entity_decode("&rsquo;"); break;
						case "ldblquote": $toText .= html_entity_decode("&laquo;"); break;
						case "rdblquote": $toText .= html_entity_decode("&raquo;"); break;

						# Skipping binary data...
						case "bin":
							$i += $param;
							break;

						case "fcharset":
							$fonts[@$stack[$j]["f"]] = $param;
							break;

						// Всё остальное добавим в текущий стек управляющих слов. Если у текущего
						// слова нет параметров, то приравляем параметр true.
						default:
							$stack[$j][strtolower($word)] = empty($param) ? true : $param;
							break;
					}
					// Если что-то требуется вывести в выходной поток, то выводим, если это требуется.
					if (rtf_isPlainText($stack[$j]))
						$document .= $toText;
				} else $document .= " ";

				$i++;
				break;
			// Перед нами символ { - значит открывается новая подгруппа, поэтому мы должны завести
			// новый уровень стека с переносом значений с предыдущих уровней.
			case "{":
				if ($j == -1)
					$stack[++$j] = array();
				else
					array_push($stack, $stack[$j++]);
				break;
			// Закрывающаяся фигурная скобка, удаляем текущий уровень из стека. Группа закончилась.
			case "}":
				array_pop($stack);
				$j--;
				break;
			// Всякие ненужности отбрасываем.
			case "\0": case "\r": case "\f": case "\b": case "\t": break;
			// Остальное, если требуется, отправляем на выход.
			case "\n":
				$document .= " ";
				break;
			default:
				if (rtf_isPlainText($stack[$j]))
					$document .= $c;
				break;
		}
	}
	// Возвращаем, что получили.
	return html_entity_decode(iconv("windows-1251", "utf-8", $document), ENT_QUOTES, "UTF-8");
}

$text = rtf2text( dirname(__FILE__).'/404.rtf');
//$text = mb_convert_encoding($text, 'windows-1251', 'utf-8');



function Parse($p1, $p2, $p3)
{
	$num1 = strpos($p1, $p2);
	if ($num1 === false) return 0;
	$num2 = substr($p1, $num1);
	return substr($num2, 0, strpos($num2, $p3));
}

$text = explode("||", $text);




$filename = '404.rtf';
$mainTags = [];
$inst = '';
$currentPosition = '';
$currentTag = 0;

$tags = [];
$tagsArray = [];

foreach ($text as $key => $line) {
	if(strpos(mb_strtolower($line), 'инструкция')) {
		$inst = $line;
		$currentPosition = $text[$key+1];
		break;
	}
	if(strpos($line, ':')) {
		$line = str_replace("·", "",$line);
		if(!empty(trim($line))) {
			$mainTags[$key]['name'] = trim($line);
		}
		foreach ($text as $i => $l) {
			if($i > $key) {
				if(strpos($l, ':')) {
					break;
				} else {
					$l = trim(str_replace("·", "",$l));
					$l = trim(str_replace(",", "",$l));
					if(!empty(trim($l))) {
						$mainTags[$key]['elements'][] = trim($l);

						$tag = explode(" ", $l)[0];
						if(in_array($tag, $tags)) {
							$key = array_search($tag, $tags);
							$tagsArray[$key][] = $l;
						} else {
							$tags[] = $tag;
							$key = array_search($tag, $tags);
							$tagsArray[$key][] = $l;
						}
					}

				}
			}
		}
	}
}
if(empty($currentPosition)) {
	$currentPosition = empty($text[0]) ? trim($text[1]) : trim($text[0]);
}

//echo '<pre>';
//print_r($mainTags);
//print_r($inst);
//print_r($currentPosition);
//print_r($tags);
//print_r($tagsArray);
//echo '</pre>';
