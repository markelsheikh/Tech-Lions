<?php


require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function drop_student_render($message= null,$error =null,$courses=null,$student_selected=null,$term_selected=null,$students=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM TERM_YEAR";
		$term_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($term_result,MYSQLI_ASSOC);
		$dbcon->close();
		
		echo $twig->render('drop_student.html',array('user' => $user_data,'student'=>$students,'course'=>$courses,'message'=>$message,'error'=>$error,'student_selected'=>$student_selected,'term'=>$term_result,'term_selected'=>$term_selected));
		

}

function get_students(){
		global $dbcon,$user_data,$twig;
		$term_id = $_POST['term_id'];
		$sql="SELECT DISTINCT PERSON.ID,PERSON.FNAME,PERSON.LNAME,ENROLL.STU_ID FROM PERSON JOIN ENROLL ON PERSON.ID = ENROLL.STU_ID AND ENROLL.TERM_ID = $term_id";
		$student_result=mysqli_query($dbcon,$sql);

	    if($student_result && $student_result->num_rows >0){
	    	mysqli_fetch_all($student_result,MYSQLI_ASSOC);
			drop_student_render('You can now select a student to drop',false,null,null,$term_id,$student_result);
		}
		else{
		    drop_student_render('We have encountered an error please try again!',true);
		}
	}


function get_course(){
		global $dbcon,$user_data,$twig;
		$student_id = $_POST['student_id'];
		$term_id = $_POST['term_id'];
		$sql = "SELECT COURSE.CRS_CODE,COURSE.CRS_TITLE,ENROLL.CRS_CODE,ENROLL.STU_ID FROM COURSE JOIN ENROLL ON COURSE.CRS_CODE = ENROLL.CRS_CODE WHERE ENROLL.STU_ID = $student_id AND ENROLL.TERM_ID = $term_id";
		$result=mysqli_query($dbcon,$sql);
		$sql2="SELECT DISTINCT PERSON.ID,PERSON.FNAME,PERSON.LNAME,ENROLL.STU_ID FROM PERSON JOIN ENROLL ON PERSON.ID = ENROLL.STU_ID AND ENROLL.TERM_ID = $term_id";
		$student_result=mysqli_query($dbcon,$sql2);
	    if($result){
	    	mysqli_fetch_all($student_result,MYSQLI_ASSOC);
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			drop_student_render('You can drop student from a course now',false,$result,$student_id,$term_id,$student_result);
		}
		else{
		    drop_student_render('We have encountered an error please try again!',true);
		}

}
function drop_student(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$student_id = $_POST['student_id'];
			$course_code = $_POST['course_code'];
			$term_id = $_POST['term_id'];
		    $stmt = $dbcon->prepare("DELETE FROM ENROLL WHERE ENROLL.STU_ID = ? AND ENROLL.CRS_CODE = ? AND ENROLL.TERM_ID = ?");
			$stmt->bind_param("iii",$student_id,$course_code,$term_id);
			if($stmt->execute()){
				drop_student_render('Course'.$course_code.' Has Been Removed For Student '.$student_id.'.',false);
			}
			else{
				drop_student_render('We have encountered an error please try again.',true);
			}

		}

	else{
		header("location: login");

	}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['student_id']) && !isset($_POST['drop_student'])){
	  get_course();
    }
    elseif (!isset($_POST['student_id']) && !isset($_POST['drop_student']) && isset($_POST['term_id'])) {
    	get_students();
    }
     else{
        drop_student();
      }

}
else{
	if($user_ok == true){
	drop_student_render(null,false);
	}

else
{
	header("location: login");
}

	}
?>