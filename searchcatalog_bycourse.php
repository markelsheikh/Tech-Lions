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
function searchcatalog_bydepartment_render($message=null,$error=null ,$course_result=null){
		global $dbcon,$user_data,$twig;
		$dbcon->close();
		
        echo $twig->render('searchcatalog_bycourse.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error));
        		
        

}

function get_course(){
		global $dbcon,$user_data,$twig;
		$course_name = $_POST['course_name'];
		$sql = "SELECT * FROM COURSE WHERE COURSE.CRS_TITLE LIKE '%$course_name%'";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			searchcatalog_bydepartment_render('Your search produced the following results',false,$result);
		}
		else{
		    searchcatalog_bydepartment_render('Your Search provided no results , please try a different department',true);
		}
		

}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    		if(isset($_POST['searchCatalog'])){
			get_course();
		}
}


else{
	if($user_ok == true){
	searchcatalog_bydepartment_render(null,false);
	}
else
{
	header("location: login");
}

	}


?>