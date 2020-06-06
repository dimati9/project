<div class="flex">
	<?= include_once 'left-panel.php'; ?>

	<div class="right">
		<h1>Пользователи: </h1>
		<table>
			<tr>
				<td>ФИО</td>
				<td>Должность</td>
				<td align="center">Отправить пользователю ссылку</td>
			</tr>
			<?php foreach($users as $user) : ?>
				<tr>
					<td width="30%"><?=$user['lastName'] .' '. mb_substr($user['name'], 0, 1) .' '. mb_substr($user['patronymic'], 0, 1) ?></td>
					<td width="40%"><?=$user['position'] ?></td>
					<td align="center"><a href="#">Отправить ссылку</a></td>
				</tr>
			<?php endforeach;?>
		</table>
	</div>

</div>
