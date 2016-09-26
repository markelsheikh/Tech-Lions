<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function delete_major_render($message,$error){
		global $dbcon,$user_data,$twig;
		$stu_id = $user_data['ID'];
		$sql="SELECT * FROM STUDENT_MAJOR WHERE STU_ID = $stu_id";
		$major_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($major_result,MYSQLI_ASSOC);
		$dbcon->close();
		echo $twig->render('delete_major.html',array('user' => $user_data,'major'=>$major_result,'message'=>$message,'error'=>$error));
		

}
//Remove a course from the data base. 
function delete_major(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
		$major = $_POST['major'];
		$stu_id = $user_data['ID'];
		$stmt = $dbcon->prepare("DELETE FROM STUDENT_MAJOR WHERE STU_ID = ? AND MAJOR_NAME = ?");
		$stmt->bind_param("is", $stu_id,$major);
		if($stmt->execute()){			
				delete_major_render('Congrant major '.$major.' has been successfuly removed',false);
			}
			else{
				delete_major_render('We have encoutered an error, please try again!'.$stmt->error,true);
			}
		
		
	}
	else{
		header("location: login");

	}//k
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

	if(isset($_POST['delete_major']))
	{
		delete_major();
	}
}

else{
	if($user_ok == true){
	delete_major_render(null,false);
	}
else 
{
	header("location: login");
}

	}	


?>