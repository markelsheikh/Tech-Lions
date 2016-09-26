<?php 
DEFINE ('DB_USER','root');
DEFINE ('DB_PASSWORD','password');
DEFINE ('DB_HOST','/cloudsql/techlionscollege:registration');
DEFINE ('DB_NAME','techlionscollege');
DEFINE ('IP_STRING','173.194.236.107:3306');
DEFINE ('DB_USER_IP','school');
DEFINE ('DB_PASSWORD_IP','school');

if (isset($_SERVER['SERVER_SOFTWARE']) &&
  strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {
    // Connect from App Engine.
    try{
       	$dbcon = new  mysqli(
							  'localhost', // host
							  DB_USER, // username
							  DB_PASSWORD,     // password
							  DB_NAME, // database name
							  null,
							  DB_HOST
							  );
    }
    catch(mysqli_sql_exception $ex){
        die(json_encode(
            array('outcome' => false, 'message' => 'Unable to connect.')
            )
        );
    }
  }
   else {
    // Connect from a development environment.
    try{
		       $dbcon = new mysqli(IP_STRING,
								  DB_USER_IP,
								  DB_PASSWORD_IP,
								  DB_NAME
								  );
       }
    catch(mysqli_sql_exception $ex){
        die(json_encode(
            array('outcome' => false, 'message' => 'Unable to connect')
            )
        );
    }
  }
?>
