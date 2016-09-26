<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function view_gradesbysemester_render($message=null,$error=null ,$grades_result=null){
		global $dbcon,$user_data,$twig;
		$sql2 = "SELECT * FROM PERSON WHERE PERSON.PERSON_TYPE = 'STUDENT'";
		$student_result = mysqli_query($dbcon,$sql2);
		mysqli_fetch_all($student_result,MYSQLI_ASSOC);
		$dbcon->close();
        echo $twig->render('viewstudentgrades_bytranscript.html',array('user' => $user_data,'grades'=>$grades_result,'term_selected'=>$term_id,'message'=>$message,'error'=>$error,'student'=>$student_result));
}

function view_grades(){
		global $dbcon,$user_data,$twig;
		$student_id = $_POST['student_id'];
		$sql = "SELECT COURSE.CRS_TITLE ,ENROLL.MID_TERM_GRADE, ENROLL.ENROLL_GRADE FROM COURSE JOIN ENROLL ON COURSE.CRS_CODE = ENROLL.CRS_CODE WHERE ENROLL.STU_ID = $student_id";
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