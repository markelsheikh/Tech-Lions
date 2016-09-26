<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function remove_coursesection_render($message,$error){
		global $dbcon,$user_data,$twig;
		$sql="SELECT CL.CLASS_SECTION,CL.CRS_CODE,CL.TERM_ID,C.CRS_CODE,C.CRS_TITLE,T.TERM_TITLE FROM CLASS CL, COURSE C, TERM_YEAR T WHERE  CL.CRS_CODE = C.CRS_CODE AND CL.TERM_ID = T.TERM_ID";
		$course_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('remove_coursesection.html',array('user' => $user_data,'section'=>$course_result,'message'=>$message,'error'=>$error));
		}
		else{
		echo $twig->render('remove_coursesection.html',array('user' => $user_data,'section'=>$course_result));//'faculty'=>$faculty_result));
		}

}
//Remove a course from the data base. 
function remove_coursesection(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$course= $_POST["course"];
		$course = explode(',', $course);
		$course_code = $course[1];
		$class_section = $course[0];
		$term_id = $course[2];
		//$faculty_id = $_POST["faculty_id"];
		$stmt = $dbcon->prepare("SELECT * FROM CLASS WHERE CLASS.CLASS_SECTION = ?  AND CLASS.CRS_CODE = ? AND CLASS.TERM_ID = ? LIMIT 1");
		$stmt->bind_param("sii", $class_section,$course_code,$term_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
			$stmt = $dbcon->prepare("DELETE FROM CLASS WHERE CLASS.CLASS_SECTION = ?  AND CLASS.CRS_CODE = ? AND CLASS.TERM_ID = ?");
			$stmt->bind_param("sii", $class_section,$course_code,$term_id);
			if($stmt->execute()){
			
				remove_coursesection_render('Course Section'.$result['CLASS_SECTION'].' FOR Course '.$result['CRS_CODE'].' has been Successfuly Deleted.',false);
			}
			else{
				remove_coursesection_render('We have encoutered an error, please try again!',true);
			}
		}
		else{
			remove_coursesection_render('COURSE Code '.$course_code.' Does not Exist',true);
			
		}
	}
	else{
		header("location: login");

	}//k
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	if(isset($_POST['remove_coursesection']))
	{
		remove_coursesection();
	}
}

else{
	if($user_ok == true){
	remove_coursesection_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>