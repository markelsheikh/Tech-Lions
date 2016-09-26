<?php



require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function drop_course_render($message= null,$error =null,$courses=null,$student_selected=null){
		global $dbcon,$user_data,$twig;
		$student_id = $user_data['ID'];
		$sql="SELECT C.CRS_CODE,C.CRS_TITLE,E.STU_ID,E.CRS_CODE FROM COURSE C, ENROLL E, TIMELINE T WHERE C.CRS_CODE = E.CRS_CODE AND E.STU_ID = $student_id AND T.TERM_ID = E.TERM_ID AND T.START <= CURDATE() AND T.END >= CURDATE() AND T.TIMELINE_TYPE = 'DROP CLASS TIMELINE'";
		$course_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		$dbcon->close();
		
		echo $twig->render('drop_course.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error));
		

}
function drop_course(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$student_id = $user_data['ID'];
			$course_code = $_POST['course_code'];
		    $stmt = $dbcon->prepare("DELETE FROM ENROLL WHERE ENROLL.STU_ID = ? AND ENROLL.CRS_CODE = ?");
			$stmt->bind_param("ii",$student_id,$course_code);
			if($stmt->execute()){
				drop_course_render('Course'.$course_code.' Has Been Removed For Student '.$student_id.'.',false);
			}
			else{
				drop_course_render('We have encountered an error please try again.',true);
			}

		}

	else{
		header("location: login");

	}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['drop_course'])){
	  drop_course();
    }
     else{
		drop_course_render(null,false);

      }

}
else{
	if($user_ok == true){
	drop_course_render(null,false);
	}

else
{
	header("location: login");
}

	}


?>