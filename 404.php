<?php

require 'vendor/autoload.php';

$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

echo $twig->render('404.html');


?>