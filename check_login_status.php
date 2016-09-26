<?php
session_start();
include_once('connect.php');
// Files that inculde this file at the very top would NOT require 
// connection to database or session_start(), be careful.
// Initialize some vars
$user_ok = false;
$log_username = "";
$log_password = "";
// User Verify function
$user_data;
$current_semester;
$Hold = false;
function evalLoggedUser($conx,$u,$p){
	global $current_semester,$Hold;
	$stmt = $conx->prepare("SELECT * FROM PERSON WHERE EMAIL = ? AND PASSWD = ? LIMIT 1");
	$stmt->bind_param("ss", $u, $p);
	$stmt->execute();
	$result = $stmt->get_result();
	$result = $result->fetch_array();
	$current_semester = current_semester($conx);
	$Hold = get_hold($conx,$result);
	return $result;
}
function get_hold($conx,$result){
	$user_id = $result['ID'];
	$sql="SELECT HOLD.STU_ID,HOLD.HOLD_TYPE,PERSON.ID,PERSON.FNAME,PERSON.LNAME FROM HOLD INNER JOIN PERSON ON HOLD.STU_ID = PERSON.ID WHERE PERSON.ID = $user_id";
	$hold_result=mysqli_query($conx,$sql);
	mysqli_fetch_all($hold_result,MYSQLI_ASSOC);
	if(mysqli_num_rows($hold_result)>0){
		return ture;
	}
	return false;

}
function current_semester($conx){
	$stmt = $conx->prepare("SELECT * FROM TERM_YEAR WHERE TERM_YEAR.FIRST_DAY <= CURDATE() AND TERM_YEAR.LAST_DAY >= CURDATE() LIMIT 1");
	//$stmt->bind_param("ss", $u, $p);
	$stmt->execute();
	$result = $stmt->get_result();
	$result = $result->fetch_array();
	return $result;
}
if(isset($_SESSION['username']) && isset($_SESSION['password'])) {
	$log_username = $_SESSION['username'];
	$log_password = $_SESSION['password'];
	// Verify the user
	$user_data = evalLoggedUser($dbcon,$log_username,$log_password);
	$user_data['Hold'] = $Hold;
	if($user_data){
		$user_ok = true;
	}
	} 
else if(isset($_COOKIE['user']) && isset($_COOKIE['pass'])){
    $_SESSION['username'] = $_COOKIE['user'];
    $_SESSION['password'] =$_COOKIE['pass'];
	$log_username = $_SESSION['username'];
	$log_password = $_SESSION['password'];
	// Verify the user
	$user_data = evalLoggedUser($dbcon,$log_username,$log_password);
	$user_data['Hold'] = $Hold;
	if($user_data){
		$user_ok = true;
	}
	
}
?>