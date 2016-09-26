<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function research_facultyinformation_render(){
		global $dbcon,$user_data,$twig;
		$sql="SELECT F.FAC_ID , P.FNAME , P.LNAME , P.PHONE , P.EMAIL , P.ADDRESS , P.ZIP , P.STATE,F.CREDITS_TAUGHT FROM FACULTY F , PERSON P  WHERE F.FAC_ID = P.ID";
		$faculty_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($faculty_result,MYSQLI_ASSOC);
		$sql2 ="SELECT DEPARTMENT.FAC_ID , PERSON.FNAME , PERSON.LNAME , DEPARTMENT.DEPT_NAME , DEPARTMENT.DEPT_CODE FROM DEPARTMENT LEFT JOIN PERSON  ON DEPARTMENT.FAC_ID = PERSON.ID";
		$department_result=mysqli_query($dbcon,$sql2);
		mysqli_fetch_all($department_result,MYSQLI_ASSOC);
		$sql3 ="SELECT P.FNAME , P.LNAME , C.CRS_TITLE , C.CRS_CREDIT , CL.CRS_CODE , CL.TERM_ID,  T.TERM_TITLE  FROM PERSON P , COURSE C , TERM_YEAR T , CLASS CL  WHERE P.ID = CL.FAC_ID AND CL.CRS_CODE = C.CRS_CODE AND CL.TERM_ID = T.TERM_ID";
        $class_result=mysqli_query($dbcon,$sql3);
        mysqli_fetch_all($class_result,MYSQLI_ASSOC);
		$dbcon->close();
		echo $twig->render('research_facultyinformation.html',array('user' => $user_data,'faculty'=>$faculty_result,'department'=>$department_result,'class'=>$class_result));

}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){

	research_facultyinformation_render();
}

else{
	if($user_ok == true){
	research_facultyinformation_render();
	}
else
{
	header("location: login");
}

	}


?>