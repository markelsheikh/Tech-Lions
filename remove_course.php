<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function remove_course_render($message,$error){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM COURSE";
		$course_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('remove_course.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error));
		}
		else{
		echo $twig->render('remove_course.html',array('user' => $user_data,'course'=>$course_result));//'faculty'=>$faculty_result));
		}

}
//Remove a course from the data base. 
function remove_course(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$course_code = $_POST["course_code"];
		//$faculty_id = $_POST["faculty_id"];
		$stmt = $dbcon->prepare("SELECT * FROM COURSE WHERE COURSE.CRS_CODE = ? LIMIT 1");
		$stmt->bind_param("i", $course_code);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
			$stmt = $dbcon->prepare("DELETE FROM COURSE WHERE COURSE.CRS_CODE = ?");
			$stmt->bind_param("i",$course_code);//$faculty_id);
			if($stmt->execute()){
			
				remove_course_render('Course '.$result['CRS_TITILE'].' Successfuly Deleted.',false);
			}
			else{
				remove_course_render('We have encoutered an error, please try again!',true);
			}
		}
		else{
			remove_course_render('COURSE '.$course_code.' Does not Exist',true);
			
		}
	}
	else{
		header("location: login");

	}//k
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	if(isset($_POST['remove_course']))
	{
		remove_course();
	}
}

else{
	if($user_ok == true){
	remove_course_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>