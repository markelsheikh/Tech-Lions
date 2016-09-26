<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
if($user_ok == true){
	echo $twig->render('backend.html',array('user' => $user_data));
}
else 
{
	header("location: login");
}



?>