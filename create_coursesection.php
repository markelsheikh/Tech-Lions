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
function create_course_section_render($message=null,$error=null,$course=null,$building=null,$room=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM TERM_YEAR";
		$sql2="SELECT * FROM COURSE";
		$sql3="SELECT * FROM TIMESLOT";
		$sql4="SELECT * FROM PERSON WHERE PERSON.PERSON_TYPE = 'FACULTY'";
		$sql5="SELECT * FROM BUILDING";
		$term_year_result = mysqli_query($dbcon,$sql);
		$course_result = mysqli_query($dbcon, $sql2);
		$timeslot_result = mysqli_query($dbcon, $sql3);
		$faculty_result = mysqli_query($dbcon, $sql4);
		$building_result = mysqli_query($dbcon, $sql5);
		mysqli_fetch_all($term_year_result,MYSQLI_ASSOC);
		mysqli_fetch_all($course_result,MYSQLI_ASSOC);
		mysqli_fetch_all($timeslot_result,MYSQLI_ASSOC);
		mysqli_fetch_all($faculty_result,MYSQLI_ASSOC);
		mysqli_fetch_all($building_result,MYSQLI_ASSOC);//helloss
		$dbcon->close();
		if($course && $building ){

		echo $twig->render('create_coursesection.html',array('user' => $user_data,'course'=>$course_result,'building'=>$building_result,'room'=>$room,'term'=>$term_year_result,'slot'=>$timeslot_result,'faculty'=>$faculty_result,'course_selected'=>$course,'building_selected'=>$building,'message'=>$message,'error'=>$error));
				
				
		}
		else{
		echo $twig->render('create_coursesection.html',array('user' => $user_data,'course'=>$course_result,'building'=>$building_result,'term'=>$term_year_result,'slot'=>$timeslot_result,'faculty'=>$faculty_result,'message'=>$message,'error'=>$error));
		}

}
function get_building_course(){
		global $dbcon,$user_data,$twig;
		$course_code = $_POST['course_code'];
		$building_code = $_POST['building_code'];
		//$sql= "SELECT * FROM ROOM WHERE BLDG_CODE ='$_building_code'";//.$building_code."";//*********
		$stmt = $dbcon->prepare('SELECT * FROM ROOM WHERE BLDG_CODE =?');
		$stmt->bind_param("s",$building_code);
		$stmt->execute();
		$resultSet = $stmt->get_result();
		$result = $resultSet->fetch_all();
		if($result){//$room = mysqli_query($dbcon,$sql)){
			//mysqli_fetch_all($room,MYSQLI_ASSOC);
			create_course_section_render('You can choose the room Now',false,$course_code,$building_code,$result);
		}
		else{
			create_course_section_render('The Buidling does not exist '.$building_code,true);
		}
		
}
function create_coursesection(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$course_code = $_POST['course_code'];
			$building_code = $_POST['building_code'];
			$room_num = $_POST['room_num'];
			$term_code = $_POST['term_code'];
			$slot_code = $_POST['slot_code'];
			$faculty_code = $_POST['faculty_code'];
			$course_section = $_POST['course_section'];
		    $stmt = $dbcon->prepare("INSERT INTO CLASS VALUES(?,?,?,?,?,?,?)");
			$stmt->bind_param("siiiiss", $course_section,$slot_code,$course_code,$term_code,$faculty_code,$room_num,$building_code);
			if($stmt->execute()){
				create_course_section_render('Course Section  '.$course_section.' For Course Code '.$course_code.' has been added.',false);
			}
			else{
				create_course_section_render('We have encountered an error please try again.'.$stmt->error,true);
			}

		}

	else{
		header("location: login");

	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	
		if(isset($_POST['create_coursesection'])){
			create_coursesection();
		}
		else{
			if(isset($_POST['course_code']) && isset($_POST['building_code']))
			{
				get_building_course();
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