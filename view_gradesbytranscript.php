<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function view_gradesbytranscript_render(){
		global $dbcon,$user_data,$twig;
		$stu_id = $user_data['ID'];
		$sql = "SELECT COURSE.CRS_TITLE ,ENROLL.MID_TERM_GRADE, ENROLL.ENROLL_GRADE FROM COURSE JOIN ENROLL ON COURSE.CRS_CODE = ENROLL.CRS_CODE WHERE ENROLL.STU_ID = $stu_id";
		$transcript_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($transcript_result,MYSQLI_ASSOC);
		$dbcon->close();
        echo $twig->render('view_gradesbytranscript.html',array('user' => $user_data,'grades'=>$transcript_result));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    	view_gradesbytranscript_render();
}

else
{
	header("location: login");
}

?>