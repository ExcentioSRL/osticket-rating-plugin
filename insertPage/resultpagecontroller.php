<?php
    require_once('./../../../../main.inc.php');
    require_once(INCLUDE_DIR.'class.ticket.php');
    require_once(INCLUDE_DIR.'class.dept.php');
    require_once(INCLUDE_DIR.'class.filter.php');
    require_once(INCLUDE_DIR.'class.canned.php');
    require_once(INCLUDE_DIR.'class.json.php');
    require_once(INCLUDE_DIR.'class.dynamic_forms.php');
    require_once(INCLUDE_DIR.'class.rating.php');


    $cookie_name = "OSTSESSID";
    $rating;
    $ticket;
    $operatore;
    $categoria;
    $session_id;

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

    if(isset($_GET["operatore"]))
        $operatore = $_GET["operatore"];
    else
        die("Error. Missing parameter");

    if(isset($_GET["categoria"]))
        $categoria = $_GET["categoria"];
    else
        die("Error. Missing parameter");

    if(isset($_COOKIE[$cookie_name])) 
        $session_id =  $_COOKIE[$cookie_name];
    else
        die("Error. Operation not allowed");   


    $checkSql = "SELECT * FROM `ost_ratings` WHERE session_id ='".$session_id."'";
    $res = db_query($checkSql);

    if( count(db_assoc_array($res)) == 0 ){
       
   

        $sql = "INSERT INTO `ost_ratings`(`rating`, `ticket`, `operatore`, `categoria`, `session_id`) VALUES ('".$rating."','".$ticket."','".$operatore."','".$categoria."','".$session_id."')";
    
        if (db_query($sql)){
                $success = true;
            }
        else
            echo "Error!";
        
    }


?>