<?php



require 'vendor/autoload.php';
include_once('check_login_status.php');
$loader = new Twig_Loader_Filesystem("views");
$twig = new Twig_Environment($loader);
function add_course_render($message= null,$error =null,$department_code=null,$courses=null,$class_section=null,$course_code=null,$term_selected=null){
		global $dbcon,$user_data,$twig;
		$sql="SELECT * FROM DEPARTMENT";
		$dept_result=mysqli_query($dbcon,$sql);
		mysqli_fetch_all($dept_result,MYSQLI_ASSOC);
		$sql3="SELECT * FROM TERM_YEAR";
		$term_result=mysqli_query($dbcon,$sql3);
		mysqli_fetch_all($term_result,MYSQLI_ASSOC);
		$dbcon->close();
		if($message){
		echo $twig->render('add_course.html',array('user' => $user_data,'department'=>$dept_result,'course'=>$courses,'section'=>$class_section,'message'=>$message,'error'=>$error,'department_selected'=>$department_code,'course_selected'=>$course_code,'term'=>$term_result,'term_selected'=>$term_selected));
		
		}
		else{
		echo $twig->render('add_course.html',array('user' => $user_data,'department'=>$dept_result,'course'=>$course_result,'section'=>$section_result,'message'=>$message,'error'=>$error,'department_selected'=>$department,'term'=>$term_result,'term_selected'=>$term_selected));
		}

}
function get_course(){
		global $dbcon,$user_data,$twig;
		$department_code = $_POST['department_code'];
		$term_id = $_POST['term_id'];
		$sql = "SELECT C.CRS_CODE,C.CRS_TITLE,CL.CRS_CODE,C.DEPT_CODE,CL.TERM_ID FROM COURSE C, CLASS CL, TIMELINE T WHERE C.DEPT_CODE = $department_code AND C.CRS_CODE = CL.CRS_CODE AND CL.TERM_ID = $term_id AND CL.TERM_ID = T.TERM_ID AND T.TIMELINE_TYPE = 'ADD CLASS TIMELINE' AND T.START <= CURDATE() AND T.END >= CURDATE()";
		$result=mysqli_query($dbcon,$sql);

	    if($result && $result->num_rows > 0){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			add_course_render('You can select the course now',false,$department_code,$result,null,null,$term_id);
		}
		else{
		    add_course_render('You cannot register for this semster, the registration period is over!',true);
		}
		$dbcon->close();

}
function get_section(){
		global $dbcon,$user_data,$twig;
		$course_code = $_POST['course_code'];
		$department_code =$_POST['department_code'];
		$term_id = $_POST['term_id'];
		$sql = "SELECT * FROM CLASS WHERE CLASS.CRS_CODE = $course_code AND CLASS.TERM_ID = $term_id";
		$result=mysqli_query($dbcon,$sql);
		$sql2 = "SELECT * FROM COURSE WHERE COURSE.DEPT_CODE = $department_code";
        $result2=mysqli_query($dbcon,$sql2);
	    if($result){
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			add_course_render('You can select the course section now',false,$department_code,$result2,$result,$course_code,$term_id);
		}
		else{
		    add_course_render('The department_code'.$department_code.' does not exist',true);
		}
		$dbcon->close();

}
function add_course(){
	global $dbcon,$user_data,$user_ok;
	if($user_ok == true){
			$course_code = $_POST['course_code'];
			$course_section = $_POST['course_section'];
			$stu_id = $user_data['ID'];
			$term_id = $_POST['term_id'];
		    $stmt = $dbcon->prepare("INSERT INTO ENROLL(ENROLL_DATE,STU_ID,CRS_CODE,CLASS_SECTION,TERM_ID)VALUES(now(),?,?,?,?)");
			$stmt->bind_param("iisi",$stu_id ,$course_code,$course_section,$term_id);
			if($stmt->execute()){
				add_course_render('Course Section  '.$course_section.' For Course Code '.$course_code.' has been added.',false);
			}
			else{
				add_course_render('We have encountered an error please try again.'.$stmt->error,true);
			}

		}

	else{
		header("location: login");

	}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['department_code']) && !isset($_POST['add_course'])){
	  if(isset($_POST['course_code'])){
	  	get_section();
	  }
	  else{

	  get_course();
	  }
    }
     else{
        add_course();
      }

}
else{
	if($user_ok == true){
	add_course_render(null,false);
	}

else
{
	header("location: login");
}

	}


?>