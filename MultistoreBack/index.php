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
        
    }
    
?>