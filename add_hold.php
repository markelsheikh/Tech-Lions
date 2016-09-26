<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function add_hold_render($message,$error){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM PERSON WHERE PERSON.PERSON_TYPE = 'STUDENT'";
		$student_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($student_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('add_hold.html',array('user' => $user_data,'student'=>$student_result,'message'=>$message,'error'=>$error));
		}
		else{
		echo $twig->render('add_hold.html',array('user' => $user_data,'student'=>$student_result));//'faculty'=>$faculty_result));
		}

}
//Remove a course from the data base. 
function add_hold(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$student_id = $_POST["student_id"];
		$hold_id = $_POST['hold_id'];
		//$faculty_id = $_POST["faculty_id"];
		$stmt = $dbcon->prepare("SELECT * FROM PERSON WHERE PERSON.ID = ? LIMIT 1");
		$stmt->bind_param("i", $student_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
			$stmt = $dbcon->prepare("INSERT INTO HOLD VALUES (?,?) ");
			$stmt->bind_param("is",$student_id,$hold_id);//$faculty_id);
			if($stmt->execute()){
			
				add_hold_render('Student '.$result['FNAME'].' Has been add to hold '.$hold_id,false);
			}
			else{
				add_hold_render('We have encoutered an error, please try again!',true);
			}
		}
		else{
			add_hold_render('Student '.$student_id.' Does not Exist',true);
			
		}
	}
	else{
		header("location: login");

	}//k
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	if(isset($_POST['add_hold']))
	{
		add_hold();
	}
}

else{
	if($user_ok == true){
	add_hold_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>