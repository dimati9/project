<h1>Добавить пользователя: </h1>
<?php if(!empty($id)) : ?>
	<h2>Пользователь успешно добавлен</h2>
<p>Ему отправлена персональная ссылка: <?=$site . "?l="  . $link?></p>
<?php endif;?>
<form action="" method="post">
	<input type="hidden" name="action" value="add_user">
	<label for="positions">Должность</label>
	<select name="positions">
		<?php foreach ($positions as $position) : ?>
		<option value="<?=$position['id']?>"><?=$position['name']?></option>
		<?php endforeach;?>
	</select>
	<label>
		E-mail
		<input type="text" name="email">
	</label>
	<label>
		ФИО
		<input type="text" name="name">
	</label>
	<input type="submit" value="Добавить">
</form>

