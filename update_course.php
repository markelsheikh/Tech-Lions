<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
/**
* This function renders the update_course html page with corresponding message.
*@param string $message is the custom message that is displayed to the user.
*@param boolean $error states weather the custom $message is an error or not.
*@param data object $course is an object that contains the information for a particular course.
*/
function update_course_render($message,$error,$course){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM COURSE";
		$course_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
				if($course){
					echo $twig->render('update_course.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error,'course_selected'=>$course,'state'=>'yes'));
				}
				else{
					echo $twig->render('update_course.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error));
				}
		}
		else{
		echo $twig->render('update_course.html',array('user' => $user_data,'course'=>$course_result));
		}

}
function get_course(){
		global $dbcon,$user_data,$twig;
		$course_name = $_POST['course_name'];
		$stmt = $dbcon->prepare("SELECT * FROM COURSE WHERE COURSE.CRS_TITLE = ? LIMIT 1");
		$stmt->bind_param("s", $course_name);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
		update_course_render('You can Update '.$course_name.' Now',false,$result);
		}
		else{
			update_course_render('The course'.$course_name.' does not exist',true,null);
		}
		$dbcon->close();

}
function update_course(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$new_course_name = $_POST['new_course_name'];
			$course_name = $_POST['selected_name'];
			$course_description = $_POST['course_description'];
			$course_credit = $_POST['course_credit'];
		    $stmt = $dbcon->prepare("UPDATE COURSE SET CRS_TITLE = ?,CRS_DESCRIPTION = ?, CRS_CREDIT = ? WHERE COURSE.CRS_TITLE = ?");
			$stmt->bind_param("ssis", $new_course_name, $course_description,$course_credit,$course_name);
			if($stmt->execute()){
				update_course_render('Course '.$course_name.' Successfully Updated.',false,null);
			}
			else{
				update_course_render('We have encountered an error please try again.',true,null);
			}

		}
	
	else{
		header("location: login");

	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	if($_POST['course_name']){
		
		if(isset($_POST['updatecourse'])){
			update_course();
		}
		else{
			get_course();
		}
		
	}
	
	
}

else{
	if($user_ok == true){
	update_course_render(null,false,null);
	}
else
{
	header("location: login");
}

	}


?>