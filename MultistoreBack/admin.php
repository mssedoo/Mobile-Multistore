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

$operation=null;
$price=null;
$retail_id=null;

if(!empty($_GET['operation'])){
	$operation=$_GET['operation'];
}
if(!empty($_GET['retail_id'])) {
    $retail_id = $_GET['retail_id'];
}
if(!empty($_GET['sales_id'])) {
    $sales_id = $_GET['sales_id'];
}
if (!empty($_GET['qty'])) {
    $quantity = $_GET['qty'];
}
if (!empty($_GET['date'])) {
    $date = $_GET['date'];
}
if (!empty($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
}
if (!empty($_GET['price'])) {
    $price = $_GET['price'];
}
if (!empty($_GET['name'])) {
    $name = $_GET['name'];
}
if (!empty($_GET['desc'])) {
    $description = $_GET['desc'];
}
if (!empty($_GET['image'])) {
    $image = $_GET['image'];
}


if ($operation == 1) {
    listAllSales($retail_id);
}
elseif ($operation == 2) {
    loadRetails();
}
elseif ($operation == 3) {
    getSale($sales_id);
}
elseif($operation == 4) {
    confirmUpdate($sales_id, $quantity);
}
elseif($operation == 5) {
    deleteRecord($sales_id);
}
elseif($operation == 6) {
    dateSales($date);
}
elseif($operation == 7) {
    getAllProducts();
}
elseif($operation == 8) {
    if($product_id != null ){
        getProduct($product_id);
    
    }
    else {
        getAllProducts();
    }
}
elseif($operation == 9) {
    createShoe($name, $price, $description, $quantity, $image);
}
elseif($operation == 10) {
    getRetailLocation($retail_id);
}
elseif($operation == 11) {
    createChart();
}


function createChart() {
    //the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "select p.product_id, p.product_name, sum(s.qty) as total_sales
      from sales s right join products p on
      s.product_id = p.product_id
      group by p.product_name
      order by p.product_id;";
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

    $shoe_array = array();
    $shoe_sales = array();

    $str2 = "[";
    $str3 = "[";

    if ($result) {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $str2.= "'".$row['product_name']."',";

            if ($row['total_sales'] == "") {
                $str3.= "0,";
            }
            else {
                $str3.= $row['total_sales'].",";
            }
            
        }


        $str2 = rtrim($str2, ",");
        $str3 = rtrim($str3, ",");

        $str2.= ']';
        $str3.= ']';

        $str = "src='https://quickchart.io/chart?height=200&chart={type:'horizontalBar',data:{labels:$str2, datasets:[{label: 'Shoe', data: $str3}]}}'";

        $str = substr_replace($str ,'src="', 0, 5);
        $str = substr_replace($str ,'"', -1);

        $str = "<img ".$str." />";

        echo $str;
    }
    $conn->close();
}


function getRetailLocation($re_id) {

     //the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

    $str = '';
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from retails WHERE retail_id = ".$re_id;
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

    if ($result) {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $str = $row['latitude']."||".$row['longitude'];
        }

        echo $str;
    }
    $conn->close();
}



function createShoe($name_, $price_, $desc_, $qty_, $image_) {
    $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

    //SQL Query - the dot (.) is used for concatenation
    $query = "INSERT INTO products(product_name, price, description, qty, image) VALUES('".$name_."', '".$price_."', '".$desc_."', '".$qty_."', '".$image_."')";
	
    //Executing the query on the connection object, which returns a 		resultset
    if (mysqli_query($conn, $query)) {
		echo "New shoe created successfully";
	 } else {
		echo "Error";
	 }
}

function listAllSales($r_id) {

    //the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );
    $str = '';
    $str2 = '';

    if ($r_id == '') {
        $str2 = ' <h3 style="color: white;">Select a day</h3><input onchange="dateChange(this)" type="date" disabled name="date" id="date" value="">';
        echo $str."||".$str2;
        
    }
    else {
        //SQL Query - the dot (.) is used for concatenation
  	    $query = "SELECT * from sales WHERE retail_id=".$r_id;
	
      //Executing the query on the connection object, which returns a 		resultset
        $result = mysqli_query($conn, $query);
      
  
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
                      $str .= '<li><img style="width:200px;height:128px;" src="'.$row2['image'].'"><h2>'.$row2['product_name'].'</h2><p>Sales ID: '.$sales_id.'</p><p>Price: Rs '.$row2['price'].'</p><p>Quantity: '.$qty.'</p><p>Total: Rs '.($qty*$row2['price']).'</p><p>Date: '.$date_time.'</p><div style="display: flex; justify-content: space-between;"><div><a href="#" onclick="updateRecord('.$sales_id.')" style="width: 50px; background-color: black; color: white;" class="ui-btn ui-mini ui-corner-all" data-transition="slide" data-direction="reverse">Update</a></div><div><a href="#" onclick="deleteRecord('.$sales_id.')" style="width: 50px; background-color: red; color: white;" class="ui-btn ui-mini ui-corner-all" data-transition="slide" data-direction="reverse">Delete</a></div></div></li><br>';
                  }
                  $str2 = ' <h3 style="color: white;">Select a day</h3><input onchange="dateChange(this)" type="date" name="date" id="date" value="">';
              }
  
              
          }
  
          $str.= '</ul>';
          echo $str."||".$str2;
  
      }
    }
    $conn->close();
	
}



function dateSales($date_) {
     //the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

    $str = '';
    $str2 = '';
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from sales WHERE date LIKE '%".$date_."%'";
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

    if ($result) {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $query2 = "SELECT * from products WHERE product_id = ".$row['product_id'];
            $sales_id = $row['sales_id'];
            $date_time = $row['date'];
            $qty = $row['qty'];

            $result2 = mysqli_query($conn, $query2);

            while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                $str .= '<li><img style="width:200px;height:128px;" src="'.$row2['image'].'"><h2>'.$row2['product_name'].'</h2><p>Sales ID: '.$sales_id.'</p><p>Price: Rs '.$row2['price'].'</p><p>Quantity: '.$qty.'</p><p>Total: Rs '.($qty*$row2['price']).'</p><p>Date: '.$date_time.'</p><div style="display: flex; justify-content: space-between;"><div><a href="#" onclick="updateRecord('.$sales_id.')" style="width: 50px; background-color: black; color: white;" class="ui-btn ui-mini ui-corner-all" data-transition="slide" data-direction="reverse">Update</a></div><div><a href="#" onclick="deleteRecord('.$sales_id.')" style="width: 50px; background-color: red; color: white;" class="ui-btn ui-mini ui-corner-all" data-transition="slide" data-direction="reverse">Delete</a></div></div></li><br>';
            }
        }

        $str.= '</ul>';
        echo $str;
    }
    $conn->close();
}



function loadRetails() {
    //the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );
  
	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from retails";
	
	//Executing the query on the connection object, which returns a 		resultset
  	$result = mysqli_query($conn, $query);

    $str = '<option value="">Choose ...</option>';

    if ($result) {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $str .= '<option value="'.$row['retail_id'].'">'.$row['location'].'</option>';
        }
        
        echo $str;
    }
    $conn->close();
}





function getSale($s_id_) {
    //the connection string object
	$conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//SQL Query - the dot (.) is used for concatenation
  	$query = "SELECT * from sales WHERE sales_id = ".$s_id_;

    $result = mysqli_query($conn, $query);

    $str = '';
    $str2 = '';
    $str3 = '';

    if ($result) {
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $query2 = "SELECT * FROM products WHERE product_id = ".$row['product_id'];

            $result2 = mysqli_query($conn, $query2);

            while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                $str = '<div style="text-align: center"><h1>'.$row2['product_name'].'</h1><img width="300px;" src="'.$row2['image'].'"></div>';
                $str2 = '<h2 style="color: black;">Quantity</h2><input type="range" name="qty3" id="qty3" data-highlight="true" min="0" max="'.$row2['qty'].'" value="'.$row['qty'].'">';
                $str3 = '<input  type="submit" value="Confirm Update" onclick="confirmUpdate('.$s_id_.')">';
            }
           
        }

        echo $str."||".$str2."||".$str3."||Update Sale ".$s_id_;
    } 
    $conn->close();
}

function confirmUpdate($s_id_, $qty_) {
    $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

	//SQL Query - the dot (.) is used for concatenation
  	$query = "UPDATE sales SET qty = ".$qty_." WHERE sales_id = ".$s_id_;

      if (mysqli_query($conn, $query)) {
        echo "Update recorded successfully";
     } else {
        echo "Error";
     }
     $conn->close();
}


function deleteRecord($s_id_) {
    $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

    $query2 = "SELECT * FROM sales WHERE sales_id = ".$s_id_;

    $result2 = mysqli_query($conn, $query2);

    while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
        $sales_qty = $row2['qty'];
        $query3 = "SELECT * FROM products WHERE product_id = ".$row2['product_id'];

        $result3 = mysqli_query($conn, $query3);

        while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
            $new_qty = $row3['qty'] + $sales_qty;

            echo $new_qty;

            $query4 = "UPDATE products SET qty = ".$new_qty." WHERE product_id = ".$row3['product_id'];

            echo $query4;

            if (mysqli_query($conn, $query4)) {
                echo "Updated successfully";
             } else {
                echo "Error";
             }
        }

    }

	//SQL Query - the dot (.) is used for concatenation
  	$query = "DELETE FROM sales WHERE sales_id = ".$s_id_;

      if (mysqli_query($conn, $query)) {
        echo "Record deleted successfully";
     } else {
        echo "Error";
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

			$str .= '<li><a onclick="productClickedAdmin('.$product_id.')" href="#"><img style="width:200px;height:128px;" src="'.$row['image'].'"><h2>'.$row['product_name'].'</h2><h3>Price: Rs '.$row['price'].'</h3></a></li><br>';
    	}

		$str.= '</ul>';
		echo $str;

	}else{
		$product_ = "not found";
	}
	$conn->close();
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
			$str3 = '<h3 style="color: white;">Enter quantity</h3><input type="range" name="qty5" id="qty5" data-highlight="true" min="0" max="'.$row['qty'].'" value="'.(int)$half_qty.'"><br><input type="submit" value="Record sale" onclick="insertSaleAdmin()">';
    	}


		echo $str."||".$str2."||".$str3;

	}else{
		$product_ = "not found";
	}
	$conn->close();
	
}
?>