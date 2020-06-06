<div class="flex">
	<?= include_once 'user-panel.php' ?>


	<div class="right">
		<a href="<?=$site?>?out">Выйти</a>
		<h3>Добро пожаловать
			<span style="font-weight: bold">
				 <?=$user['lastName']?> <?=$user['name']?> <?=$user['patronymic']?>
			</span>
		</h3>
		<p>Ваша должность
			<span style="font-weight: bold">
			<?=$currentPosition?>
			</span>
		</p>
		<div class="document">
			<p>Ваш основной документ: </p>
			<span style="font-weight: bold">
			<?php if(!empty($int)) : ?>
				<?=$int?>
			<?php endif;?>

			<span> <?=$currentPosition?> </span>
			<span> (<a href="<?=$site . "/" . $filename ?>"><?=$filename?></a>) </span>
			</span>
			<div class="tags">
				<h4>Ключевые функции: </h4>
				<p class="position"><?=$currentPosition?>: </p>
				<ul>
				<?php foreach($tags as $key => $tag) : ?>
					<li><a href="#key<?=$key?>"><?=$tag?></a></li>
				<?php endforeach; ?>
				</ul>
			</div>
			<div class="main">
				<h4>По порядку: </h4>
				<?php foreach ($tagsArray as $key => $tagArray) : ?>
					<a name="key<?=$key?>"></a>

					<h5><?=$tags[$key]?>:</h5>
				<ul>
				<?php foreach($tagArray as $tag) : ?>
				<li><?=$tag?></li>
				<?php endforeach; ?>
				</ul>

				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>


