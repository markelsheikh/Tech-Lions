<?php
require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function declare_major_render($message= null,$error =null,$major = null,$selected=null){
		global $dbcon,$user_data,$twig;
		$dbcon->close();
		echo $twig->render('declare_major.html',array('user' => $user_data,'major'=>$major,'message'=>$message,'error'=>$error,'selected'=>$selected));


}
function get_majors(){
	global $dbcon,$user_data,$twig;
	$major_type = $_POST['type'];
	if($major_type == 'Minor'){
		$sql2="SELECT * FROM MINOR";
		$minor_result=mysqli_query($dbcon,$sql2);
		mysqli_fetch_all($minor_result,MYSQLI_ASSOC);
		declare_major_render('Select the minors from the list.',false,$minor_result,$major_type);
	}
	elseif($major_type == 'Major'){
		$sql2="SELECT * FROM MAJOR";
		$minor_result=mysqli_query($dbcon,$sql2);
		mysqli_fetch_all($minor_result,MYSQLI_ASSOC);
		declare_major_render('Select the major from the list.',false,$minor_result,$major_type);
	}
	else{
		declare_major_render('we have encountered an error',true);
	}
}

function add_major(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$major_type = $_POST['type'];
			$major = $_POST['major'];
			$stu_id = $user_data['ID'];
			if($major_type == 'Minor'){
			$stmt = $dbcon->prepare("UPDATE STUDENT SET MINOR_NAME = ? WHERE STU_ID = ?");
			$stmt->bind_param("si",$major,$stu_id);
			if($stmt->execute()){
				declare_major_render('The minor has been successfully been added or changed .',false);
			}
			else{
				declare_major_render('We have encountered an error please try again.'.$stmt->error,true);
			}
			}
			elseif($major_type == 'Major'){
			$stmt = $dbcon->prepare("INSERT INTO STUDENT_MAJOR(STU_ID,MAJOR_NAME)VALUES(?,?)");
			$stmt->bind_param("is",$stu_id ,$major);
			if($stmt->execute()){
				declare_major_render('Major has been successfully added.',false);
			}
			else{
				declare_major_render('We have encountered an error please try again.'.$stmt->error,true);
			}
			}
			else{
				declare_major_render('We have encountered an error please try again.',true);

			}
		    

		}

	else{
		header("location: login");

	}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['type']) && !isset($_POST['add_major'])){
        get_majors();
      }
      else{
      	add_major();
      }
  }

else{
	if($user_ok == true){
	declare_major_render(null,false);
	}

else
{
	header("location: login");
}

	}


?>