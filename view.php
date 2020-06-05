<?php
if(empty($page) || $page == 'auth') {
	include_once './templates/auth.php';
} else {
	include_once './templates/'.$page.'.php';
}
