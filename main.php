<?php
@session_start();

ini_set('error_reporting', E_ERROR);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

require 'pdo.php';
$site = "https://amazing-projects.ru/projectx/";

if(isset($_GET['out'])) {
	$_SESSION = [];
	header('Location: '.$site);
}


if(isset($_GET['l'])) {
	$user = DB::getRow('SELECT * FROM `users` WHERE `link` = ?',array(trim((int)$_GET['l'])));
	if(!empty($user['id'])) {
		$_SESSION['user_id'] = $user['id'];
		header('Location: '.$site .'/personal/');
	}
}

if(!empty($_POST) && !empty($_POST['login'])) {
	if($_POST['login'] == '122' && $_POST['password'] == '123456') {
		$_SESSION['login'] = '122';
	}
}

if(!empty($_POST['action'])) {
	if($_POST['action'] == 'add_user') {
		$link = time('siHdny');
		$lastName = trim($_POST['lastname']);
		$name = trim($_POST['firstname']);
		$patronymic = trim($_POST['patronymic']);
		$id = DB::add('INSERT INTO `users` SET `position_id` = ?, `link` = ?, `name` = ?, `lastName` = ?,
		`patronymic` = ?', array($_POST['positions'], $link, $name, $lastName, $patronymic));
	}
}



if(!empty($_GET['action'])) {
	$action = trim($_GET['action']);
}

if(empty($_SESSION['login']) && empty($_SESSION['user_id'])) {
	$page = 'auth';
} else {
	if(!empty($_SESSION['user_id'])) {
		$page = 'personal';
	} else {
		if(empty($_GET['page'])) {
			$page = 'admin';
			@header('Location: '.$site .'/admin/');
		} else {
			$page = $_GET['page'];
		}
	}

	if(!empty($page)) {
		include_once './pages/'.$page.'.php';
	}
}


