<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function view_studentinformation_render($message=null,$error=null){
		global $dbcon,$user_data,$twig;
		if($user_data['PERSON_TYPE']=='STUDENT'){
			$stu_id = $user_data['ID'];
			$stmt = $dbcon->prepare("SELECT * FROM STUDENT WHERE STUDENT.STU_ID = $stu_id LIMIT 1");
			//$stmt->bind_param("ss", $u, $p);
			$stmt->execute();
			$result = $stmt->get_result();
			$result = $result->fetch_array();
			if($result['MINOR_NAME'] == null){
				$user_data['MINOR'] = 'N/A';
			}
			else{
			$user_data['MINOR']=$result['MINOR_NAME'];
			}
			$user_data['STU_GPA'] = $result['STU_GPA'];
			$sql="SELECT * FROM STUDENT_MAJOR WHERE STUDENT_MAJOR.STU_ID = $stu_id";
			$major=mysqli_query($dbcon,$sql);
			mysqli_fetch_all($major,MYSQLI_ASSOC);
			$user_data['MAJOR'] = $major;
			
			
		}
	    echo $twig->render('view_studentinformation.html',array('user' => $user_data,'message'=>$message,'error'=>$error));

}


if($user_ok == true){
	view_studentinformation_render(null,false);
	}
else{
	header("location: login");
}



?>