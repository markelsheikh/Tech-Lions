<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function update_department_render($message,$error,$result=null,$department = null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM DEPARTMENT";
		$department_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($department_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('update_department.html',array('user' => $user_data,'department'=>$department_result,'message'=>$message,'error'=>$error,'faculty'=>$result,'department_selected'=>$department));
		}
		else{
		echo $twig->render('update_department.html',array('user' => $user_data,'department'=>$department_result));//'faculty'=>$faculty_result));
		}

}
function get_department(){
		global $dbcon,$user_data,$twig;
		$department_code = $_POST['department_code'];
		$sql = "SELECT FACULTY.FAC_ID,FACULTY.DEPT_CODE,PERSON.FNAME,PERSON.LNAME,PERSON.ID FROM PERSON INNER JOIN FACULTY ON PERSON.ID=FACULTY.FAC_ID WHERE FACULTY.DEPT_CODE = $department_code";
		$result=mysqli_query($dbcon,$sql);
		
		
		if($result){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			$stmt = $dbcon->prepare("SELECT * FROM DEPARTMENT WHERE DEPARTMENT.DEPT_CODE = ? LIMIT 1");
			$stmt->bind_param("i", $department_code);
			$stmt->execute();
			$dep_name = $stmt->get_result();
			$dep_name = $dep_name->fetch_array();
			update_department_render('You can Update department '.$dep_name['DEPT_NAME'].' Now',false,$result,$department_code);
		}
		else{
			update_department_render('The department_code'.$department_code.' does not exist',true,null);
		}
		$dbcon->close();

}
function update_department(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$department_code = $_POST["department_code"];
		$faculty_code = $_POST["faculty_code"];
		//$faculty_id = $_POST["faculty_id"];
		$stmt = $dbcon->prepare("SELECT * FROM DEPARTMENT WHERE DEPARTMENT.DEPT_CODE = ? LIMIT 1");
		$stmt->bind_param("i", $department_code);
		$stmt->execute();
		$result = $stmt->get_result();
		$result = $result->fetch_array();
		if($result){
			$stmt = $dbcon->prepare("UPDATE DEPARTMENT SET FAC_ID = ? WHERE DEPARTMENT.DEPT_CODE = ?");
			$stmt->bind_param("si",$faculty_code,$department_code);//$faculty_id);
			if($stmt->execute()){
			$stmt = $dbcon->prepare("SELECT * FROM DEPARTMENT WHERE DEPARTMENT.DEPT_CODE = ? LIMIT 1");
			$stmt->bind_param("i", $department_code);
			$stmt->execute();
			$dep_name = $stmt->get_result();
			$dep_name = $dep_name->fetch_array();
				update_department_render('Department '.$dep_name['DEPT_NAME'].' Successfuly Updated.',false);
			}
			else{
				update_department_render('We have encoutered an error, please try again!',true);
			}
		}
		else{
			update_department_render('Deparment '.$department_code.' Does not Exist',true);
			
		}
	}
	else{
		header("location: login");

	}//k
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	if(isset($_POST['update_department']))
	{
		update_department();
	}
	else {
		get_department();
	}
}

else{
	if($user_ok == true){
	update_department_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>