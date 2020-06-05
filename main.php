<?php
@session_start();

require 'pdo.php';
$site = $_SERVER['DOCUMENT_ROOT'];

if(!empty($_POST) && !empty($_POST['login'])) {
	if($_POST['login'] == '122' && $_POST['password'] == '123456') {
		$_SESSION['login'] = '122';
	}
}

if(!empty($_POST['action'])) {
	if($_POST['action'] == 'add_user') {
		$link = $site . '/' . time('siHdny');
		$lastName = explode(" ",$_POST['name'])[0];
		$name = explode(" ",$_POST['name'])[1];
		$patronymic = explode(" ",$_POST['name'])[2];
		$id = DB::add('INSERT INTO `users` SET `position_id` = ?, `link` = ?, `name` = ?, `lastName` = ?,
		`patronymic` = ?', array($_POST['positions'], $link, $name, $lastName, $patronymic));
	}
}

if(empty($_SESSION['login'])) {
	$page = 'auth';
} else {
	$page = 'admin';
	include_once './pages/'.$page.'.php';
}
