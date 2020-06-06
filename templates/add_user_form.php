<div class="row add_user_form">
	<div class="col-md-16">
		<div class="card-box">
			<h4 class="head_text">Добавить пользователя</h4>
			<?php if(!empty($id)) : ?>
				<h2>Пользователь успено добавлен</h2>
				<p>Ему отправлена персональная ссылка: </p>
			<p><a href="<?=$site . "lc/"  . $link?>"><?=$site . "lc/"  . $link?></a></p>
			<?php endif;?>
			<form role="form" method="post">
				<input type="hidden" name="action" value="add_user">
				<div class="form-group">
					<label for="Surname" class="form_text">Фамилия</label>
					<input type="text" name="lastname" class="form-control" id="Surname" placeholder="Иванов">
				</div>
				<div class="form-group">
					<label for="Firstname" class="form_text">Имя</label>
					<input type="text" name="firstname" class="form-control" id="Firstname" placeholder="Иван">
				</div>
				<div class="form-group">
					<label for="Middlename" class="form_text">Отчество</label>
					<input type="text" name="patronymic" class="form-control" id="Middlename" placeholder="Иванович">
				</div>
				<div class="form-group">
					<label for="positions" class="form_text">Должность</label>
					<select name="positions"  class="positions form-control">
						<?php foreach ($positions as $position) : ?>
							<option value="<?=$position['id']?>"><?=$position['name']?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-group">
					<label for="InputEmail" class="form_text">Email</label>
					<input type="email" name="email" class="form-control" id="InputEmail" placeholder="Email@email.ru">
				</div>
				<div class="text-center">
					<button type="submit" class="btn btn-primary custom_btn">Отправить</button>
				</div>
			</form>
		</div>
	</div>
</div>
