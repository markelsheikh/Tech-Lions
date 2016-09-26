<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function view_gradesbysemester_render($message=null,$error=null ,$grades_result=null){
		global $dbcon,$user_data,$twig;
		$sql = "SELECT * FROM TERM_YEAR";
		$term_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($term_result,MYSQLI_ASSOC);
		$dbcon->close();
        echo $twig->render('view_gradesbysemester.html',array('user' => $user_data,'term'=>$term_result,'grades'=>$grades_result,'term_selected'=>$term_id,'message'=>$message,'error'=>$error));
}

function view_grades(){
		global $dbcon,$user_data,$twig;
		$term = $_POST['term_id'];
		$sql = "SELECT COURSE.CRS_TITLE ,ENROLL.MID_TERM_GRADE, ENROLL.ENROLL_GRADE FROM COURSE JOIN ENROLL ON COURSE.CRS_CODE = ENROLL.CRS_CODE WHERE ENROLL.TERM_ID = $term AND ENROLL.STU_ID =".(int)$user_data['ID']."";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
		mysqli_fetch_all($result,MYSQLI_ASSOC);
		view_gradesbysemester_render('Your search produced the following results',false,$result);
		}
		else{
		    view_gradesbysemester_render('Your Search provided no results , please try a different Term',true,null);
		}


}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    		if(isset($_POST['viewGrade'])){
			view_grades();
		}
}


else{
	if($user_ok == true){
	view_gradesbysemester_render(null,false,null);
	}
else
{
	header("location: login");
}

	}


?>