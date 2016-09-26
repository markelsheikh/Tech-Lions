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
function view_studentdetailschedule_render($message=null,$error=null ,$course_result=null){
		global $dbcon,$user_data,$twig;
		$sql = "SELECT * FROM TERM_YEAR";
		$result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($result,MYSQLI_ASSOC);
	    echo $twig->render('view_studentdetailschedule.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error,'term'=>$result));

}

function get_course(){
		global $dbcon,$user_data,$twig;
		$stu_id = $user_data['ID'];
		$term_id = $_POST['term_id'];
		$sql = "SELECT C.CRS_CODE, C2.CRS_TITLE, C2.CRS_DESCRIPTION , C2.CRS_CREDIT , C.ROOM_NUM , C.BLDG_CODE,T.DAYS , T.START_TIME , T.END_TIME,P.ID,P.FNAME,P.LNAME FROM  CLASS C , COURSE C2 , TIMESLOT T, PERSON P, ENROLL E WHERE C.CRS_CODE  = C2.CRS_CODE AND C.TIMESLOT_ID = T.TIMESLOT_ID AND C.FAC_ID = P.ID AND E.CRS_CODE = C.CRS_CODE AND E.CLASS_SECTION = C.CLASS_SECTION AND E.STU_ID = $stu_id AND E.TERM_ID = $term_id";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			view_studentdetailschedule_render('This is your detail schedule',false,$result);
		}
		else{
		    view_studentdetailschedule_render('You are not currently registered for this semester',true,null);
		}
		$dbcon->close();

}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    		
    		if(isset($_POST['view_studentdetailschedule'])){
    			get_course();
    		}
			
		
}


else{
	if($user_ok == true){
	view_studentdetailschedule_render(null,false,null);
	}
else
{
	header("location: login");
}

	}


?>