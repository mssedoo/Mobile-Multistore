<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header("Access-Control-Allow-Headers: X-Requested-With");
    
    DEFINE ('DB_USER', 'root');
    DEFINE ('DB_PASSWORD', '');
    DEFINE ('DB_HOST', 'localhost');
    DEFINE ('DB_NAME', 'multi-store');
    $operation=null;
    $username=null;
    $password=null;
    
    if(!empty($_GET['operation'])){
        $operation=$_GET['operation'];
    }

    if((!empty($_GET['username'])) && (!empty($_GET['password']))){
        $username=$_GET['username'];
        $password=$_GET['password'];
    }

    if($operation == 1){
        //Login
        login($username,$password);
        
    }elseif($operation == 2){
        display_prod_sales();

    }elseif($operation == 3){
        display_product_stocks();

    }elseif($operation == 4){
        MNG_view_sales();

    }elseif($operation == 5){
        ;

    }else{
        echo ("operation not defined. Specify value");
    }

    function login($user,$pass){
        $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        OR die ('Could not connect to MySQL: '.mysqli_connect_error() );
        
        $sql = "SELECT store_username,store_password FROM store 
        WHERE store_username = '$user' AND store_password = '$pass'";
        
        $sqlMNG = "SELECT manager_username,manager_password FROM manager 
        WHERE manager_username = '$user' AND manager_password = '$pass'";

        $username="";
        $password="''";
        $result = mysqli_query($conn, $sql);
        $resultMNG = mysqli_query($conn, $sqlMNG);

        if ($result) {
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $username = $row['store_username'];
                $password= $row['store_password'];
            }
            if (($username != '') && ($password == $pass)){
                echo 's';
            } else{
                while($row = mysqli_fetch_array($resultMNG,MYSQLI_ASSOC)){
                    $username = $row['manager_username'];
                    $password= $row['manager_password'];
                }
                if (($username != '') && ($password == $pass)){
                    echo 'm';
                }else{
                    echo "Null";
                }
            }
        }
        $conn->close();
    }

    function display_prod_sales(){
        $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

        $product="";
        $category="";
        $price="";

        $sql = "SELECT * FROM product";
        $result = mysqli_query($conn, $sql);
        if ($result){
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $product=$row["product_name"];
                $category=$row["product_category"];
                $price=$row["product_price"];
                echo '
                <div class="card">
                    <p id="productname" style="padding: 0; margin:0 0 5px 0 ; text-align: center;">'.$product.'</p>
                    <div class="d-flex">
                        <img src="imgs/cloth/shirt.png" class="list-img">
    
                        <div class="info center-h center-v">
                            <div class="name-categ">
                                <p id="category">'.$category.'</p>
                                <p id="productprice" style="font-weight: 600;">'.$price.'</p>
                            </div>
                        </div>
                        <div class="center-v">
                                <input name="quantity" type="number" class="form-control form" value="0" style="text-align: center;">
                        </div>
                    </div>
                </div>
                <br>';
            }
        }

        $conn->close();
    }
    
    function display_product_stocks(){
        $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

        $product="";
        $category="";
        $price="";
        $string="";

        $sql = "SELECT * FROM product";
        $result = mysqli_query($conn, $sql);
        if ($result){
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $product=$row["product_name"];
                $category=$row["product_category"];
                $price=$row["product_price"];
                $string .= '
                <li>
                    <div class="card">
                        <p id="productname" style="padding: 0; margin:0 0 5px 0 ; text-align: center;">'.$product.'</p>
                        <div class="d-flex">
                            <img src="imgs/cloth/shirt.png" class="list-img">
                    
                            <div class="info center-h center-v">
                                <div class="name-categ">
                                    <p id="category">'.$category.'</p>
                                    <p id="productprice" style="font-weight: 600;">'.$price.'</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </li>';
            }
            echo $string;
        }

        $conn->close();
    }

    function MNG_view_sales(){
        $conn = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        OR die ('Could not connect to MySQL: '.mysqli_connect_error() );

        $total =0.0;
        $shopname = "";
        $total_sales =0.0;
        $shop ="";
        $top= "";


        $sql = "
        SELECT st.store_name,s.sales_total 
        FROM sales s, store st
        where st.store_id = s.store_id
        order by s.store_id
        ";
        $result = mysqli_query($conn, $sql);
        if ($result){
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $total +=$row["sales_total"];
                $shopname=$row["store_name"];
                $total_sales=$row["sales_total"];
                $shop .= '
                <!-- shop -->
                <div class="card d-flex mt-30">
                    <img src="imgs/shops.png" class="list-img ">
    
                    <div class="info center-h ">
                        <div class="name-categ">
                            <p id="shopname" style="margin: 0;">'.$shopname.'</p>
                        </div>
                    </div> 
                    <div class="info center-v ">
                        <div class="quant ">
                            <p id="quantity">'.$total_sales.'</p>
                        </div>
                    </div> 
                </div>
                ';
            }
            $top ='
            <a href="#shopsales" data-transition="slide">
                <div class="top d-flex">
                    <p class="sum m-0" style="width: 75%;">Sales Summary</p>
                    <p id="date" class="m-0 center-v"></p>
                </div>
                <div class="bottom d-flex">
                    <p class="sum m-0" style="width: 50%;">Total Sales</p>
                    <div class="d-flex" style="width: 50%; justify-content: flex-end;">
                        <p id="date" class="sum m-0 center-v" >$'.$total.'</p>
                    </div>
                </div>
            </a>
            ';

            echo "$top || $shop";
        }

        $conn->close();
    }
?>