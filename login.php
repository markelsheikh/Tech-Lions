<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
if($user_ok == true){
	header("location: backend");
}
else
{
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$username = $_POST["username"];
	$password = $_POST["password"];
	$stmt = $dbcon->prepare("SELECT * FROM PERSON WHERE EMAIL = ? AND PASSWD = ? LIMIT 1");
	$stmt->bind_param("ss", $username, $password);
	$stmt->execute();
	$result = $stmt->get_result();
	//echo("hello".$result->fetch_array());
	//$off = false;
	$result = $result->fetch_array();
	if($result)
	{

		$_SESSION['username'] = $result['EMAIL'];
		$_SESSION['password'] = $result['PASSWD'];
		if(isset($_POST['remember']) &&  $_POST['remember'] == 'YES')
		{
			setcookie("user", $result['EMAIL'], strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $result['PASSWD'], strtotime( '+30 days' ), "/", "", "", TRUE); 
		}
		/*if ($result['PERSON_TYPE'] == 'STUDENT')
		{
			echo $twig->render('backend.html',array('type' => 'STUDENT', 'fname'=>$result['FNAME'],'Lname'=>$result['LNAME']));
		}
		else if($result['PERSON_TYPE'] == 'FACULTY')
		{
			echo $twig->render('backend.html',array('type' => 'FACULTY'));
								
		}
		else if($result['PERSON_TYPE'] == 'ADMIN')
		{
			echo $twig->render('backend.html',array('type' => 'ADMIN'));

		}
		else if($result['PERSON_TYPE'] == 'RESEARCHER')
        		{
        			echo $twig->render('backend.html',array('type' => 'RESEARCHER')); // ADDED RESEARCHER

        		}*/
		header("location: backend");
		$dbcon->close();

	}
	else
	{
		$error ="You have entered the wrong user name and password";
		echo $twig->render('login.html',array('error' => $error));
	
	}


}
else{
		echo $twig->render('login.html');
	}

}




?>