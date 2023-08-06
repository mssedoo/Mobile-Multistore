<?php
/*Author: Dr. Geerish Suddul
For UTM lecture - Mobile Application Development
August 2019
*/

//access control headears
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");


//creating constants which are parametric access to the database
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'mynewbookstore');

/*our approach is based on the following:
operation takes values 1,2,...
1 - query book details based on title
2 - insert a new book
...
*/

//define variables for the submitted parameters
$operation=null;
$title=null;
$author=null;

/*extract the values submitted by the web client (HTML page) using $_GET for get method or $_POST in case post method
format is as follows: localhost:<portname>/foldername/index.php?operation=<value>&title=<value>&author=<value>
check if the parameter has been submitted in the query, then extract the corresponding values.
The empty checks can be delayed until the variable is required, but has been done for simplicity here.
*/
if(!empty($_GET['operation'])){
	$operation=$_GET['operation'];
}

if(!empty($_GET['title'])){
	$title=$_GET['title'];
}

if(!empty($_GET['author'])){
	$author=$_GET['author'];
}


//check value of submitted parameter

if($operation == 1){
	if($title !=null ){
		$author_name = getAuthor($title);
		echo $author_name;
	}
	

}elseif($operation == 2){
	if($author !=null && $title!=null){
		insertRecord($title, $author);
	}
}else{
	echo ("operation not defined. Specify value 1 or 2");
}



function getAuthor($title_){

	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//variables which take null values
  	$book_details = null;
	$author_ = null;
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from books where title='".$title_."'";
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);


  	//echo "<br>len $result->num_rows";
  	if($result){
    	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
   		$author_ = $row['author'];
    	}

	}else{
		$author_ = "not found";
	}
	return $author_;
	$conn->close();
}

function insertRecord($title_, $author_){
	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//define insert query
	$query = "INSERT INTO books (title, author)VALUES ('".$title_."', '".$author_."')";

	//execute query on connection object (conn) and echo result
	if (mysqli_query($conn, $query)) {
		echo "New record created successfully";
	 } else {
		echo "Error: " . $query . "" . mysqli_error($conn);
	 }
	 $conn->close();
}

?>
