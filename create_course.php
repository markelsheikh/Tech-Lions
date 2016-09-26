<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function create_course_render($message,$error){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM DEPARTMENT";
		$department_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($department_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
				echo $twig->render('create_course.html',array('user' => $user_data,'department'=>$department_result,'message'=>$message,'error'=>$error));
		}
		else{
		echo $twig->render('create_course.html',array('user' => $user_data,'department'=>$department_result));
		}

}
function create_course(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$course_name = $_POST["course_name"];
		$course_description = $_POST["course_description"];
		$course_credit = $_POST["course_credit"];
		$department_name = $_POST["department_name"];
		$stmt = $dbcon->prepare("SELECT * FROM COURSE WHERE COURSE.CRS_TITLE = ? LIMIT 1");
		$stmt->bind_param("s", $course_name);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
		create_course_render('Course '.$course_name.' Already Exist',true);
		}
		else{
			$stmt = $dbcon->prepare("INSERT INTO COURSE (CRS_TITLE,CRS_DESCRIPTION,CRS_CREDIT,DEPT_CODE) VALUES (?,?,?,?)");
			$stmt->bind_param("ssis", $course_name, $course_description,$course_credit,$department_name);
			if($stmt->execute()){
				create_course_render('Course '.$course_name.' Successfully Added.',false);
			}
			else{
				create_course_render('We have encountered an error please try again.',true);
			}
			
		}
	}
	else{
		header("location: login");

	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	create_course();
}

else{
	if($user_ok == true){
	create_course_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>