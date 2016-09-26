<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function remove_hold_render($message,$error){
		global $dbcon,$user_data,$twig;
		$sql="SELECT HOLD.STU_ID,HOLD.HOLD_TYPE,PERSON.ID,PERSON.FNAME,PERSON.LNAME FROM HOLD INNER JOIN PERSON ON HOLD.STU_ID = PERSON.ID";
		$hold_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($hold_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('remove_hold.html',array('user' => $user_data,'hold'=>$hold_result,'message'=>$message,'error'=>$error));
		}
		else{
		echo $twig->render('remove_hold.html',array('user' => $user_data,'hold'=>$hold_result));//'faculty'=>$faculty_result));
		}

}
//Remove a course from the data base. 
function remove_hold(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$hold_id = $_POST["hold_id"];
		$hold_id = explode(',', $hold_id);
		$student_id = $hold_id[0];
		$hold_id = $hold_id[1];
		//$faculty_id = $_POST["faculty_id"];
		$stmt = $dbcon->prepare("SELECT * FROM HOLD WHERE HOLD.STU_ID = ? AND HOLD.HOLD_TYPE = ? LIMIT 1");
		$stmt->bind_param("is", $student_id,$hold_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
			$stmt = $dbcon->prepare("DELETE FROM HOLD WHERE HOLD.STU_ID = ? AND HOLD.HOLD_TYPE = ?");
			$stmt->bind_param("is", $student_id,$hold_id);//$faculty_id);
			if($stmt->execute()){
			
				remove_hold_render('Hold '.$result['HOLD_TYPE'].' For Student '.$result['STU_ID'].'  Has been Deleted.',false);
			}
			else{
				remove_hold_render('We have encoutered an error, please try again!',true);
			}
		}
		else{
			remove_hold_render('Hold  '.$hold_id.' Does not Exist',true);
			
		}
	}
	else{
		header("location: login");

	}//k
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	if(isset($_POST['remove_hold']))
	{
		remove_hold();
	}
}

else{
	if($user_ok == true){
	remove_hold_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>