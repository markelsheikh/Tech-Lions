<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function update_view_hold_render(){
		global $dbcon,$user_data,$twig;
		$sql="SELECT HOLD.STU_ID,HOLD.HOLD_TYPE,PERSON.ID,PERSON.FNAME,PERSON.LNAME FROM HOLD INNER JOIN PERSON ON HOLD.STU_ID = PERSON.ID WHERE PERSON.ID = ".(int)$user_data['ID']."";
		
		if($hold_result=mysqli_query($dbcon,$sql)){
		mysqli_fetch_all($hold_result,MYSQLI_ASSOC);
		$dbcon->close();
		$message = null;
		echo $twig->render('view_hold.html',array('user' => $user_data,'hold'=>$hold_result,'message'=>$message));
		}
		else{
		$message = 'Congrats there is no holds currently on your account';
		echo $twig->render('view_hold.html',array('user' => $user_data,'message'=>$message));//'faculty'=>$faculty_result));
		}

}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){

	update_view_hold_render();
}

else{
	if($user_ok == true){
	update_view_hold_render();
	}
else 
{
	header("location: login");
}

	}	


?>