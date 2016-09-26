<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function searchschedule_bycourse_render($message=null,$error=null ,$course_result=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM TERM_YEAR";
        $term_result = mysqli_query($dbcon,$sql);
        mysqli_fetch_all($term_result,MYSQLI_ASSOC);
		$dbcon->close();

        echo $twig->render('searchschedule_bycourse.html',array('user' => $user_data,'term'=>$term_result,'course'=>$course_result,'message'=>$message,'error'=>$error));
}

function get_course(){
		global $dbcon,$user_data,$twig;
		$term_id = $_POST['term_id'];
		$course_name = $_POST['course_name'];
		$sql = "SELECT C.CRS_CODE , C.CRS_TITLE, C.CRS_DESCRIPTION, C.CRS_CREDIT , CL.ROOM_NUM , CL.BLDG_CODE, CL.CLASS_SECTION , T.START_TIME, T.END_TIME , P.FNAME , P.LNAME FROM COURSE C , CLASS CL , TIMESLOT T , PERSON P WHERE C.CRS_CODE = CL.CRS_CODE AND CL.TIMESLOT_ID = T.TIMESLOT_ID AND CL.FAC_ID = P.ID AND CL.TERM_ID = $term_id AND C.CRS_TITLE LIKE '%$course_name%'";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			searchschedule_bycourse_render('Your search produced the following results',false,$result);
		}
		else{
		    searchschedule_bycourse_render('Your Search provided no results , please try a different department',true);
		}


}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    		if(isset($_POST['searchCatalog'])){
			get_course();
		}
}


else{
	if($user_ok == true){
	searchschedule_bycourse_render(null,false);
	}
else
{
	header("location: login");
}

	}


?>