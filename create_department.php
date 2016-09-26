<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function create_department_render($message,$error){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM SCHOOL";
		$school_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($school_result,MYSQLI_ASSOC);
		//$sql="SELECT * FROM PERSON WHERE PERSON.PERSON_TYPE = 'FACULTY'";
		//$faculty_result=mysqli_query($dbcon,$sql);
		//mysqli_fetch_all($faculty_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('create_department.html',array('user' => $user_data,'school'=>$school_result,'faculty'=>$faculty_result,'message'=>$message,'error'=>$error));
		}
		else{
		echo $twig->render('create_department.html',array('user' => $user_data,'school'=>$school_result));//'faculty'=>$faculty_result));
		}

}
function create_department(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$department_name = $_POST["department_name"];
		$school_code = $_POST["school_code"];
		//$faculty_id = $_POST["faculty_id"];
		$stmt = $dbcon->prepare("SELECT * FROM DEPARTMENT WHERE DEPARTMENT.DEPT_NAME = ? LIMIT 1");
		$stmt->bind_param("s", $department_name);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
		create_department_render('Deparment '.$department_name.' Already Exist',true);
		}
		else{
			$stmt = $dbcon->prepare("INSERT INTO DEPARTMENT(DEPT_NAME,SCHOOL_CODE) VALUES(?,?)");
			$stmt->bind_param("ss",$department_name,$school_code);//$faculty_id);
			if($stmt->execute()){
				create_department_render('Department '.$department_name.' Successfuly Added.',false);
			}
			else{
				create_department_render('We have encoutered an error, please try again!',true);
			}
			
		}
	}
	else{
		header("location: login");

	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	create_department();
}

else{
	if($user_ok == true){
	create_department_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>