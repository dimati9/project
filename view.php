<?php
if(empty($page) || $page == 'auth') {
	include_once './templates/auth.php';
} else {
	if($page == 'admin' && !empty($action)) {
		if(is_file('./templates/'.$action.'.php')) {
			include_once './templates/'.$action.'.php';
		} else {
			include_once './templates/'.$page.'.php';
		}

	} else {
		include_once './templates/'.$page.'.php';
	}

}


