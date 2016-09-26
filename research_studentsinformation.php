<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function research_studentsinformation_render(){
		global $dbcon,$user_data,$twig;
		$sql="SELECT ST.STU_ID,S.FNAME , S.LNAME ,S.PHONE , S.EMAIL, ST.STU_GPA , ST.TOTAL_CREDITS FROM PERSON S , STUDENT ST WHERE ST.STU_ID = S.ID";
		$student_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($student_result,MYSQLI_ASSOC);
		$sql2 ="SELECT S.FNAME , S.LNAME , S.PHONE , S.EMAIL, H.HOLD_TYPE FROM PERSON S , HOLD H WHERE H.STU_ID = S.ID";
		$hold_result=mysqli_query($dbcon,$sql2);
		mysqli_fetch_all($hold_result,MYSQLI_ASSOC);
		$sql3 ="SELECT S.FNAME AS STU_FNAME , S.LNAME AS STU_LNAME, A.STU_ID ,  F.FNAME , F.LNAME , A.FAC_ID , A.STU_ID FROM PERSON S , PERSON F , ADVISING A WHERE A.STU_ID = S.ID AND A.FAC_ID = F.ID ";
        $adviser_result=mysqli_query($dbcon,$sql3);
        mysqli_fetch_all($adviser_result,MYSQLI_ASSOC);
		$dbcon->close();
		echo $twig->render('research_studentsinformation.html',array('user' => $user_data,'student'=>$student_result,'hold'=>$hold_result,'adviser'=>$adviser_result));

}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){

	research_studentsinformation_render();
}

else{
	if($user_ok == true){
	research_studentsinformation_render();
	}
else
{
	header("location: login");
}

	}


?>