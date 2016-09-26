<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function view_adviser_render(){
		global $dbcon,$user_data,$twig;
		$sql="SELECT FNAME , LNAME , PHONE , EMAIL FROM PERSON JOIN ADVISING ON PERSON.ID = ADVISING.FAC_ID WHERE ADVISING.STU_ID = ".(int)$user_data['ID']."";
		$adviser_result=mysqli_query($dbcon,$sql);
		if(mysqli_num_rows($adviser_result) >0){
		mysqli_fetch_all($adviser_result,MYSQLI_ASSOC);
		$dbcon->close();
		echo $twig->render('view_adviser.html',array('user' => $user_data,'adviser'=>$adviser_result));
		}
		else{
		echo $twig->render('view_adviser.html',array('user' => $user_data));//.
		}

}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){

	view_adviser_render();
}

else{
	if($user_ok == true){
	view_adviser_render();
	}
else
{
	header("location: login");
}

	}


?>