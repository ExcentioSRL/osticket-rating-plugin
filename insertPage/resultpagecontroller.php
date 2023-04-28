<?php
    require_once('./../../../../main.inc.php');
    require_once(INCLUDE_DIR.'class.ticket.php');
    require_once(INCLUDE_DIR.'class.dept.php');
    require_once(INCLUDE_DIR.'class.filter.php');
    require_once(INCLUDE_DIR.'class.canned.php');
    require_once(INCLUDE_DIR.'class.json.php');
    require_once(INCLUDE_DIR.'class.dynamic_forms.php');
    require_once(INCLUDE_DIR.'class.rating.php');

    $resultPage = RatingPlugin::$result_url;
    
    $cookie_name = "OSTSESSID";
    $rating; 
    $ticket; 
    $number; 
    $staff_id; 
    $topic_id; 
    $session_id;
    $user_id;
    $user_ip;

    $success = false;

    if(!isset($_COOKIE[$cookie_name])) {
        echo "Cookie named '" . $cookie_name . "' is not set!";
    } else {
        $session_id =  $_COOKIE[$cookie_name];
    }

    if(isset($_GET["rating"]))
        $rating = $_GET["rating"];
    else
        die("Error. Missing parameter");

    if(isset($_GET["ticket"]))
        $ticket = $_GET["ticket"];
    else
        die("Error. Missing parameter");
    
     if(isset($_GET["number"]))
        $number = $_GET["number"];
    else
        die("Error. Missing parameter");

    if(isset($_GET["staff_id"]))
        $staff_id = $_GET["staff_id"];
    else
        die("Error. Missing parameter");

    if(isset($_GET["topic_id"]))
        $topic_id = $_GET["topic_id"];
    else
        die("Error. Missing parameter");

    if(isset($_COOKIE[$cookie_name])) 
        $session_id =  $_COOKIE[$cookie_name];
    else
        die("Error. Operation not allowed");   

    $getUserInfoSql = "SELECT * FROM `ost_session` WHERE session_id ='".$session_id."'";
    $info = db_query($getUserInfoSql);
    $infoRes = db_assoc_array($info);

    if( count($infoRes) != 0 ){

        $user_id = $infoRes[0]['user_id'];
        $user_ip = $infoRes[0]['user_ip'];

        $checkSql = "SELECT * FROM `ost_ratings` WHERE ticket_id = ".$ticket." AND user_id= '".$user_id."'";
        $res = db_query($checkSql);

        $count = count(db_assoc_array($res));
       
        if( $count == 0 ){
            $sql = "INSERT INTO `ost_ratings`(`rating`, `ticket_id`, `staff_id`, `user_ip`, `user_id`, `topic_id`, `number`) VALUES (".$rating.",".$ticket.",".$staff_id.",'".$user_ip."','".$user_id."',".$topic_id.",'".$number."')";
                echo($sql);
                if (db_query($sql)){
                        $success = true;
                        //Redirect to custom page after vote
                        header("Location: ".$resultPage);
                        exit();
                    }
                else
                    echo "Error!";   
        }
    }

    


?>