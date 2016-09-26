<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function searchschedule_bydepartment_render($message=null,$error=null ,$department=null,$course_result=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM DEPARTMENT";
		$department_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($department_result,MYSQLI_ASSOC);
		$sql2="SELECT * FROM TERM_YEAR";
        $term_result = mysqli_query($dbcon,$sql2);
        mysqli_fetch_all($term_result,MYSQLI_ASSOC);
		$dbcon->close();
        echo $twig->render('searchschedule_bydepartment.html',array('user' => $user_data,'department'=>$department_result,'course'=>$course_result,'term'=>$term_result,'department_selected'=>$department_code ,'term_selected'=>$term_id,'message'=>$message,'error'=>$error));

}

function get_course(){
		global $dbcon,$user_data,$twig;
		$term_id = $_POST['term_id'];
		$department_code = $_POST['department_code'];
		$sql = "SELECT TR.TERM_TITLE , C.CRS_CODE, D.DEPT_NAME , C2.CRS_TITLE, C2.CRS_DESCRIPTION , C2.CRS_CREDIT , C.ROOM_NUM , C.BLDG_CODE , T.START_TIME , T.END_TIME , P.FNAME , P.LNAME FROM  CLASS C , COURSE C2 , TIMESLOT T , FACULTY F , PERSON P , TERM_YEAR TR , DEPARTMENT D WHERE C.CRS_CODE  = C2.CRS_CODE AND C.TIMESLOT_ID = T.TIMESLOT_ID AND C.FAC_ID = F.FAC_ID  AND F.FAC_ID = P.ID AND D.DEPT_CODE = C2.DEPT_CODE AND D.DEPT_CODE  = $department_code  AND TR.TERM_ID = $term_id";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			searchschedule_bydepartment_render('Your search produced the following results',false,$department_code,$result);
		}
		else{
		    searchschedule_bydepartment_render('Your Search provided no results , please try a different department or term',true,null,null);
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
	searchschedule_bydepartment_render(null,false,$department,$course);
	}
else
{
	header("location: login");
}

	}


?>