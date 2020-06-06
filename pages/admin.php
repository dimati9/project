<?php

$users = DB::getAll('SELECT `users`.*, `position`.`name` `position` FROM `users` `users` LEFT JOIN `positions` `position` ON `position`.`id` = `users`.`position_id`');
$positions = DB::getAll('SELECT * FROM `positions`');
