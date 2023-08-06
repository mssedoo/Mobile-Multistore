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
DEFINE ('DB_NAME', 'salesretail');

/*our approach is based on the following:
operation takes values 1,2,...
1 - query book details based on title
2 - insert a new book
...
*/

//define variables for the submitted parameters
$operation=null;
$price=null;

/*extract the values submitted by the web client (HTML page) using $_GET for get method or $_POST in case post method
format is as follows: localhost:<portname>/foldername/index.php?operation=<value>&title=<value>&author=<value>
check if the parameter has been submitted in the query, then extract the corresponding values.
The empty checks can be delayed until the variable is required, but has been done for simplicity here.
*/
if(!empty($_GET['operation'])){
	$operation=$_GET['operation'];
}

if(!empty($_GET['username'])){
	$username=$_GET['username'];
}

if(!empty($_GET['password'])){
	$password=$_GET['password'];
}

if(!empty($_GET['location'])){
	$location=$_GET['location'];
}

if(!empty($_GET['latitude'])){
	$latitude=$_GET['latitude'];
}

if(!empty($_GET['longitude'])){
	$longitude=$_GET['longitude'];
}

if(!empty($_GET['product_id'])){
	$product_id=$_GET['product_id'];
}

if(!empty($_GET['quantity'])){
	$qty=$_GET['quantity'];
}

if(!empty($_GET['retail_id'])){
	$r_id=$_GET['retail_id'];
}




//check value of submitted parameter

if($operation == 1){
	if($product_id != null ){
		getProduct($product_id);

	}
	else {
		getAllProducts();
	}

}elseif($operation == 2){
	if($username !=null && $password!=null && $location != null){
		insertRetail($username, $password, $location, $latitude, $longitude);
	}
}
elseif ($operation == 3) {
	getAllProducts();
}
elseif($operation == 4) {
	if ($username != null && $password != null) {
		retailLogin($username, $password);
	}
}
elseif ($operation == 5) {
	if ($qty != null && $r_id != null && $product_id != null) {
		recordSale($qty, $r_id, $product_id);
	}
}
elseif ($operation == 6) {
	getAllSales($r_id);
}
else{
	echo ("operation not defined. Specify value 1 or 2");
}



function getProduct($p_id_){

	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//variables which take null values
	$product_ = null;
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from products where product_id=".$p_id_;
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

	$str ='';
	$str2 ='';

  	//echo "<br>len $result->num_rows";
  	if($result){
    	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			$half_qty = $row['qty'] / 2;
			$str = '<div style="text-align: center"><h1>'.$row['product_name'].'</h1><img width="300px;" src="'.$row['image'].'"></div>';
			$str2 = '<li><strong>Price: Rs </strong>'.$row['price'].'</li><li><strong>Description: </strong>'.$row['description'].'</li>';
			$str3 = '<h3 style="color: white;">Enter quantity</h3><input type="range" name="qty" id="qty" data-highlight="true" min="0" max="'.$row['qty'].'" value="'.(int)$half_qty.'"><br><input type="submit" value="Record sale" onclick="insertSale()">';
    	}


		echo $str."||".$str2."||".$str3;

	}else{
		$product_ = "not found";
	}
	$conn->close();
	
}


function retailLogin($username_, $password_) {
	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	$query = "SELECT * from accounts where username='".$username_."' and password='".$password_."'";

	$result = mysqli_query($conn, $query);

	$query3 = "SELECT * from admin where user_admin='".$username_."' and pass_admin='".$password_."'";

	$result3 = mysqli_query($conn, $query3);


	$found = false;
	$retail_loc = "";

	if ($result) {
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			$retail_id_ = $row['retail_id'];

			$query2 = "SELECT * from retails where retail_id='".$retail_id_."'";

			$result2 = mysqli_query($conn, $query2);

			while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
				$retail_loc = $row2['location'];
				echo $retail_loc."||".$retail_id_;
			}
    	}
	}


	if ($result3) {
		while($row3 = mysqli_fetch_array($result3,MYSQLI_ASSOC)){
			echo 'Admin||';
    	}
	}


	$conn->close();
}


function recordSale($qty_, $retail_id_, $product_id_) {
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	$query = "INSERT INTO sales (product_id, retail_id, qty, date) VALUES(".$product_id_.", ".$retail_id_.", '".$qty_."', NOW())";

	if (mysqli_query($conn, $query)) {
		echo "New sale recorded successfully";
	 } else {
		echo "Error";
	 }

	 $query2 = "SELECT qty FROM products WHERE product_id = ".$product_id_;

	 $result2 = mysqli_query($conn, $query2);
	 $qty_fetched;

			while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
				$qty_fetched = $row2['qty'];
				$rem_qty = $qty_fetched - $qty_;
				$query3 = "UPDATE products SET qty = '".$rem_qty."' WHERE product_id='".$product_id_."'";

				if (mysqli_query($conn, $query3)) {
					echo "Update recorded successfully";
				 } else {
					echo "Error";
				 }
			}
			$conn->close();
}

function getAllProducts() {
	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//variables which take null values
	$product_ = null;
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from products";
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

	$str ='';

  	//echo "<br>len $result->num_rows";
  	if($result){
    	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			$product_ = $row['product_name'];
			$product_id = $row['product_id'];

			$str .= '<li><a onclick="productClicked('.$product_id.')" href="#"><img style="width:200px;height:128px;" src="'.$row['image'].'"><h2>'.$row['product_name'].'</h2><h3>Price: Rs '.$row['price'].'</h3></a></li><br>';
    	}

		$str.= '</ul>';
		echo $str;

	}else{
		$product_ = "not found";
	}
	$conn->close();
}


function getAllSales($retail_id_) {
	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//variables which take null values
	$product_ = null;
	
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from sales where retail_id =".$retail_id_;
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

	$str ='';

  	//echo "<br>len $result->num_rows";
  	if($result){
    	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			$sales_id = $row['sales_id'];
			$product_id = $row['product_id'];
			$qty = $row['qty'];
			$date_time = $row['date'];

			$query2 = "SELECT * from products where product_id =".$product_id;

			$result2 = mysqli_query($conn, $query2);

			if ($result2) {
				while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
					$str .= '<li><a href="#"><img style="width:200px;height:128px;" src="'.$row2['image'].'"><h2>'.$row2['product_name'].'</h2><p>Sales ID: '.$sales_id.'</p><p>Price: Rs '.$row2['price'].'</p><p>Quantity: '.$qty.'</p><p>Total: Rs '.($qty*$row2['price']).'</p><p>Date: '.$date_time.'</p></a></li><br>';
				}
			}

			
    	}

		$str.= '</ul>';
		echo $str;

	}
	$conn->close();
}

function insertRetail($username_, $password_, $location_, $latitude_, $longitude_){
	//the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//define insert query
	$query = "INSERT INTO retails (location, latitude, longitude)VALUES ('".$location_."', '".$latitude_."', '".$longitude_."')";
		

	//execute query on connection object (conn) and echo result
	if (mysqli_query($conn, $query)) {
		echo "New retail created successfully";
	 } else {
		echo "Error";
	 }

	 //SQL Query - the dot (.) is used for concatenation
	 $query3 = "SELECT retail_id from retails where latitude='".$latitude_."' and longitude='".$longitude_."'";
	
	 //Executing the query on the connection object, which returns a resultset
	   $result = mysqli_query($conn, $query3);

	   $retail_id;

	 if($result){
    	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			$retail_id = $row['retail_id'];
    	}

	}else{
		$product_ = "not found";
		alert('retail not found');
	}

	$query2 = "INSERT INTO accounts (retail_id, username, password) VALUES ('".$retail_id."', '".$username_."', '".$password_."')";

	 if (mysqli_query($conn, $query2)) {
		echo "New account created successfully";
	 } else {
		echo "Error";
	 }

	 $conn->close();
}

?>
