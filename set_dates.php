<?php



require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function set_dates_render($message= null,$error =null,$courses=null,$student_selected=null){
		global $dbcon,$user_data,$twig;
		$student_id = $user_data['ID'];
		$sql="SELECT * FROM TERM_YEAR";
		$term_year=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($term_year,MYSQLI_ASSOC);
		$dbcon->close();
		
		echo $twig->render('set_dates.html',array('user' => $user_data,'term'=>$term_year,'message'=>$message,'error'=>$error));
		

}
function set_dates(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$timeline_id = $_POST['timeline_id'];
			$term_id = $_POST['term_id'];
			$start = $_POST['start'];
			$end = $_POST['end'];
			$start = date("Y-m-d", strtotime($start));
			$end = date("Y-m-d", strtotime($end));
		    $stmt = $dbcon->prepare("INSERT INTO TIMELINE VALUES(?,?,?,?)");
			$stmt->bind_param("siss",$timeline_id,$term_id,$start,$end);
			if($stmt->execute()){
				set_dates_render('Dates time line have been set for '.$timeline_id.$start,false);
			}
			else{
				set_dates_render('We have encountered an error please try again.',true);
			}

		}

	else{
		header("location: login");

	}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['set_dates'])){
	  set_dates();
    }
     else{
		set_dates_render(null,false);

      }

}
else{
	if($user_ok == true){
	set_dates_render(null,false);
	}

else
{
	header("location: login");
}

	}


?>