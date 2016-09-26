<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
/**
* This function renders the update_course html page with corresponding message.
*@param string $message is the custom message that is displayed to the user.
*@param boolean $error states weather the custom $message is an error or not.
*@param data object $course is an object that contains the information for a particular course.
*/
function create_course_section_render($message=null,$error=null,$building_result=null,$room=null,$faculty_result=null,$timeslot_result=null,$course_selected=null,$class_selected=null,$term_selected=null,$building_selected=null,$room_selected=null,$timeslot_selected=null,$faculty_selected=null){
		global $dbcon,$user_data,$twig;
		$sql = "SELECT C.CLASS_SECTION , C.TERM_ID , CR.CRS_TITLE,T.TERM_TITLE,C.CRS_CODE FROM CLASS C, COURSE CR, TERM_YEAR T WHERE C.CRS_CODE = CR.CRS_CODE AND C.TERM_ID = T.TERM_ID";
		$course_result = mysqli_query($dbcon,$sql);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		$dbcon->close();
		echo $twig->render('update_coursesection.html',array('user' => $user_data,'course'=>$course_result,'building'=>$building_result,'room'=>$room,'slot'=>$timeslot_result,'faculty'=>$faculty_result,'course_selected'=>$course_selected,'building_selected'=>$building_selected,'class_selected'=>$class_selected,'message'=>$message,'error'=>$error,'term_selected'=>$term_selected,'room_selected'=>$room_selected,'faculty_selected'=>$faculty_selected,'timeslot_selected'=>$timeslot_selected));

}
function get_all(){
		global $dbcon,$user_data,$twig;
		$course = $_POST['course'];
		$course = explode(',', $course);
		$course_selected = $course[0];
		$class_selected = $course[1];
		$term_selected = $course[2];
		$sql2="SELECT * FROM ROOM";
		$sql3="SELECT * FROM TIMESLOT";
		$sql4="SELECT * FROM PERSON WHERE PERSON.PERSON_TYPE = 'FACULTY'";
		$sql5="SELECT * FROM BUILDING";
		$room_result = mysqli_query($dbcon, $sql2);
		$timeslot_result = mysqli_query($dbcon, $sql3);
		$faculty_result = mysqli_query($dbcon, $sql4);
		$building_result = mysqli_query($dbcon, $sql5);
		mysqli_fetch_all($room_result,MYSQLI_ASSOC);
		mysqli_fetch_all($timeslot_result,MYSQLI_ASSOC);
		mysqli_fetch_all($faculty_result,MYSQLI_ASSOC);
		mysqli_fetch_all($building_result,MYSQLI_ASSOC);//helloss
		$stmt = $dbcon->prepare("SELECT * FROM CLASS WHERE CLASS.TERM_ID = ? AND CLASS.CLASS_SECTION=? AND CLASS.CRS_CODE=? LIMIT 1");
		$stmt->bind_param("isi",$term_selected,$class_selected,$course_selected);
		$stmt->execute();
		$course_result = $stmt->get_result();
		$course_result = $course_result->fetch_array();
		$building_selected = $course_result['BLDG_CODE'];
		$room_selected = $course_result['ROOM_NUM'];
		$timeslot_selected = $course_result['TIMESLOT_ID'];
		$faculty_selected = $course_result['FAC_ID'];

		create_course_section_render('Now you can edit the course section',false,$building_result,$room_result,$faculty_result,$timeslot_result,$course_selected,$class_selected,$term_selected,$building_selected,$room_selected,$timeslot_selected,$faculty_selected);
		
	}

function get_building_course(){
		global $dbcon,$user_data,$twig;
		$course = $_POST['course'];
		$course = explode(',', $course);
		$course_selected = $course[0];
		$class_selected = $course[1];
		$term_selected = $course[2];
		$building_code = $_POST['building_code'];
		$timeslot_selected = $_POST['slot_code'];
		$faculty_selected = $_POST['faculty_code'];
		$sql3="SELECT * FROM TIMESLOT";
		$sql4="SELECT * FROM PERSON WHERE PERSON.PERSON_TYPE = 'FACULTY'";
		$sql5="SELECT * FROM BUILDING";
		$sql2="SELECT * FROM ROOM WHERE ROOM.BLDG_CODE = '$building_code'";
		$room_result = mysqli_query($dbcon, $sql2);
		$timeslot_result = mysqli_query($dbcon, $sql3);
		$faculty_result = mysqli_query($dbcon, $sql4);
		$building_result = mysqli_query($dbcon, $sql5);
		mysqli_fetch_all($timeslot_result,MYSQLI_ASSOC);
		mysqli_fetch_all($faculty_result,MYSQLI_ASSOC);
		mysqli_fetch_all($building_result,MYSQLI_ASSOC);//helloss
		mysqli_fetch_all($room_result,MYSQLI_ASSOC);
		
		if(mysqli_num_rows($room_result)){//$room = mysqli_query($dbcon,$sql)){
			//mysqli_fetch_all($room,MYSQLI_ASSOC);
			create_course_section_render('Now you can edisfdsft the course section',false,$building_result,$room_result,$faculty_result,$timeslot_result,$course_selected,$class_selected,$term_selected,$building_code,null,$timeslot_selected,$faculty_selected);
		}
		else{
			create_course_section_render('We have encountered an error',true,$building_result,null,$faculty_result,$timeslot_result,$course_selected,$class_selected,$term_selected,$building_code,null,$timeslot_selected,$faculty_selected);
		}
		
}
function create_coursesection(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$course = $_POST['course'];
			$course = explode(',', $course);
			$course_code = $course[0];
			$course_section = $course[1];
			$term_code = $course[2];
			$building_code = $_POST['building_code'];
			$room_num = $_POST['room_num'];
			$slot_code = $_POST['slot_code'];
			$faculty_code = $_POST['faculty_code'];
		    $stmt = $dbcon->prepare("UPDATE CLASS SET BLDG_CODE = ?, ROOM_NUM=?,TIMESLOT_ID =?,FAC_ID = ? WHERE TERM_ID=? AND CLASS_SECTION = ? AND CRS_CODE = ?");
			$stmt->bind_param("siiiisi", $building_code,$room_num,$slot_code,$faculty_code,$term_code,$course_section,$course_code);
			if($stmt->execute()){
				create_course_section_render('Course Section  '.$course_section.' For Course Code '.$course_code.' has been updated.',false);
			}
			else{
				create_course_section_render('We have encountered an error please try again.',true);
			}

		}

	else{
		header("location: login");

	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	
		if(isset($_POST['update_coursesection'])){
			create_coursesection();
		}
		else{
			if(isset($_POST['building_code']))
			{
				get_building_course();
			}
			else{
				get_all();
			}
		}

}

else{
	if($user_ok == true){
	create_course_section_render();
	}
else
{
	header("location: login");
}

	}
?>