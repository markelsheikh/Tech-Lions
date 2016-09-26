<?php

require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);

function view_facultymemberadviseelist_render($message=null,$error=null ,$advisee_list=null){
		global $dbcon,$user_data,$twig;
		$dbcon->close();
        echo $twig->render('view_facultymemberadviseelist.html',array('user' => $user_data,'student'=>$advisee_list,'message'=>$message,'error'=>$error));
}

function view_advisee_list(){
		global $dbcon,$user_data,$twig;
		$faculty_id = $user_data['ID'];
		$sql = "SELECT P.ID,P.FNAME,P.LNAME,P.EMAIL,P.PHONE FROM PERSON P, ADVISING A WHERE P.ID = A.STU_ID AND A.FAC_ID = $faculty_id";
		$result = mysqli_query($dbcon,$sql);
        if($result && mysqli_num_rows($result) > 0){
		mysqli_fetch_all($result,MYSQLI_ASSOC);
		view_facultymemberadviseelist_render('This is your advisee list',false,$result);
		}
		else{
		    view_facultymemberadviseelist_render('You do not have any student to be advised right now!',true,null);
		}


}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    view_advisee_list();
}
else{
	if($user_ok == true){
	view_facultymemberadviseelist_render(null,false);
	}
else
{
	header("location: login");
}

	}


?>