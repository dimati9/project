<?php
@session_start();

if(!empty($_POST) && !empty($_POST['login'])) {
	if($_POST['login'] == '122' && $_POST['password'] == '123456') {
		$_SESSION['login'] = '122';
	}
}

if(empty($_SESSION['login'])) {
	$page = 'auth';
} else {
	$page = 'admin';
}
