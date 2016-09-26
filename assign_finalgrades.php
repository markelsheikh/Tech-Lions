<?php
require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function assign_finalgrades_render($message= null,$error =null,$course_selected=null,$class_selected=null,$students = null){
		global $dbcon,$user_data,$twig,$current_semester;
		$faculty_id = $user_data['ID'];
		$sql="SELECT CR.CRS_TITLE,C.CRS_CODE,C.CLASS_SECTION FROM CLASS C, COURSE CR, TIMELINE TL WHERE C.CRS_CODE = CR.CRS_CODE  AND C.FAC_ID = $faculty_id AND TL.TERM_ID = C.TERM_ID AND TL.START <= CURDATE() AND TL.END >= CURDATE() AND TL.TIMELINE_TYPE = 'ASSIGN_FINAL_TERM_GRADE'";
		$course_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($course_result and mysqli_num_rows($course_result) < 1){
			$message = 'You cannot assing the final grades for students at this time';
			$error = true;
		}
		echo $twig->render('assign_finalgrades.html',array('user' => $user_data,'course'=>$course_result,'message'=>$message,'error'=>$error,'student'=>$students,'course_selected'=>$course_selected,'class_selected'=>$class_selected));
		

}
function assign_finalgrades(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$course = $_POST['course_code'];
			$course= explode(',', $course);
			$course_code = $course[0];
			$class_section = $course[1];
			$student_id = $_POST['student_id'];
			$grade = $_POST['grade'];
		    $stmt = $dbcon->prepare("UPDATE ENROLL SET ENROLL_GRADE = ? WHERE ENROLL.CRS_CODE = ? AND ENROLL.CLASS_SECTION = ? AND ENROLL.STU_ID = ?");
			$stmt->bind_param("sisi",$grade,$course_code,$class_section,$student_id);
			if($stmt->execute()){
				assign_finalgrades_render('The Grade has been Assigned for the Following Student',false);
			}
			else{
				assign_finalgrades_render('We have encountered an error please try again.',true);
			}

		}

	else{
		header("location: login");

	}
}

function get_students(){
		global $dbcon,$user_data,$twig;
		$course = $_POST['course_code'];
		$course= explode(',', $course);
		$course_code = $course[0];
		$class_section = $course[1];
		$faculty_id = $user_data['ID'];
		$sql = "SELECT S.ID,S.FNAME,S.LNAME,E.ENROLL_GRADE FROM PERSON S, CLASS C, ENROLL E WHERE C.CRS_CODE = E.CRS_CODE AND C.CLASS_SECTION = E.CLASS_SECTION AND S.ID = E.STU_ID AND C.TERM_ID = E.TERM_ID AND C.FAC_ID  = $faculty_id AND C.CRS_CODE = $course_code AND C.CLASS_SECTION = '$class_section'";
		$result=mysqli_query($dbcon,$sql);

	    if($result){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			assign_finalgrades_render('You can select the course now',false,$course_code,$class_section,$result);
		}
		else{
		    assign_finalgrades_render('You cannot Assign Grades At the moment',true);
		}

}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['course_code']) && !isset($_POST['assign_finalgrades'])){
		get_students();
    }
     else{
		assign_finalgrades();

      }

}
else{
	if($user_ok == true){
	assign_finalgrades_render(null,false);
	}

else
{
	header("location: login");
}

	}


?>