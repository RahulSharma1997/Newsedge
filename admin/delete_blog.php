<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "db_connect.php";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id =  trim($_GET["id"]);
    
    $sql_select = "SELECT image FROM blogs WHERE id = ?";
    if($stmt_select = $conn->prepare($sql_select)){
        $stmt_select->bind_param("i", $id);
        if($stmt_select->execute()){
            $result = $stmt_select->get_result();
            if($result->num_rows == 1){
                $row = $result->fetch_assoc();
                $image_to_delete = $row['image'];
                if(!empty($image_to_delete) && file_exists("../" . $image_to_delete)){
                    unlink("../" . $image_to_delete);
                }
            }
        }
        $stmt_select->close();
    }

    $sql = "DELETE FROM blogs WHERE id = ?";
    
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $param_id);
        $param_id = $id;
        
        if($stmt->execute()){
            header("location: blog_list.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    $stmt->close();
    $conn->close();
} else{
    header("location: blog_list.php");
    exit();
}
?>