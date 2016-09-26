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
function searchcatalog_bydepartment_render($message=null,$error=null ,$department=null,$course_result=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM DEPARTMENT";
		$department_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($department_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
        		echo $twig->render('searchcatalog_bydepartment.html',array('user' => $user_data,'department'=>$department_result,'course'=>$course_result,'department_selected'=>$department,'message'=>$message,'error'=>$error));
        		}
        		else{
        		echo $twig->render('searchcatalog_bydepartment.html',array('user' => $user_data,'message'=>$message,'error'=>$error,'department'=>$department_result,'course'=>$course_result,'department_selected'=>$department));
        		}

}

function get_course(){
		global $dbcon,$user_data,$twig;
		$department_code = $_POST['department_code'];
		$sql = "SELECT * FROM COURSE WHERE COURSE.DEPT_CODE = $department_code";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			searchcatalog_bydepartment_render('Your search produced the following results',false,$department_code,$result);
		}
		else{
		    searchcatalog_bydepartment_render('Your Search provided no results , please try a different department',true,null,null);
		}
		$dbcon->close();

}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    		if(isset($_POST['searchCatalog'])){
			get_course();
		}
}


else{
	if($user_ok == true){
	searchcatalog_bydepartment_render(null,false,$department,$course);
	}
else
{
	header("location: login");
}

	}


?>