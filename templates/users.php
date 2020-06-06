<div class="flex">
	<?= include_once 'left-panel.php'; ?>

	<div class="right">
		<table>
			<tr>
				<td>ФИО</td>
				<td>Должность</td>
				<td>Отправить ссылку</td>
			</tr>
			<?php foreach($users as $user) : ?>
				<tr>
					<td><?=$user['lastName'] .' '. mb_substr($user['name'], 0, 1) .' '. mb_substr($user['patronymic'], 0, 1) ?></td>
					<td><?=$user['position'] ?></td>
					<td><a href="#">Отправить ссылку</a></td>
				</tr>
			<?php endforeach;?>
		</table>
	</div>

</div>
