<?php
$id = $_SESSION['user_id'];
$user = DB::getRow('SELECT * FROM `users` WHERE `id` = ?',array($id));
$position = DB::getRow('SELECT * FROM `positions` WHERE `id` = ?', array($user['position_id']));


