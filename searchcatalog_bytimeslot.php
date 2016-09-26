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
function searchcatalog_bytimeslot_render($message=null,$error=null ,$time=null,$course_result=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM TIMESLOT";
		$timeslot_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($timeslot_result,MYSQLI_ASSOC);
		$sql2="SELECT * FROM TERM_YEAR";
		$term_result = mysqli_query($dbcon,$sql2);
		mysqli_fetch_all($term_result,MYSQLI_ASSOC);
	    echo $twig->render('searchcatalog_bytimeslot.html',array('user' => $user_data,'time'=>$timeslot_result,'time_selected'=>$time,'course'=>$course_result,'message'=>$message,'error'=>$error,'term'=>$term_result));

}

function get_course(){
		global $dbcon,$user_data,$twig;
		$time_id = $_POST['time_id'];
		$term_id = $_POST['term_id'];
		$sql = "SELECT C.CRS_CODE, C2.CRS_TITLE, C2.CRS_DESCRIPTION , C2.CRS_CREDIT , C.ROOM_NUM , C.BLDG_CODE , T.START_TIME , T.END_TIME FROM  CLASS C , COURSE C2 , TIMESLOT T WHERE C.CRS_CODE  = C2.CRS_CODE AND C.TIMESLOT_ID = T.TIMESLOT_ID AND T.TIMESLOT_ID = $time_id AND C.TERM_ID = $term_id";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			searchcatalog_bytimeslot_render('Your search produced the following results',false,null,$result);
		}
		else{
		    searchcatalog_bytimeslot_render('Your Search provided no results , please try a different Time Slot',true,null,null);
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
	searchcatalog_bytimeslot_render(null,false,null,null);
	}
else
{
	header("location: login");
}

	}


?>