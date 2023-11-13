<?php
require_once('./../../../../main.inc.php');
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once(INCLUDE_DIR . 'class.dept.php');
require_once(INCLUDE_DIR . 'class.filter.php');
require_once(INCLUDE_DIR . 'class.canned.php');
require_once(INCLUDE_DIR . 'class.json.php');
require_once(INCLUDE_DIR . 'class.dynamic_forms.php');

$resultPage = RatingPlugin::$result_url;
$errorStaffPage = RatingPlugin::$error_staff_url;
$errorNoUserPage = RatingPlugin::$error_no_user_url;
$genericErrorPage = RatingPlugin::$generic_error_url;

$cookie_name = "OSTSESSID";
$rating;
$ticket;
$number;
$staff_id;
$topic_id;
$session_id;
$user_id;
$user_ip;
$c_experience;

$success = false;


if (!isset($_COOKIE[$cookie_name])) {
    echo "Cookie named '" . $cookie_name . "' is not set!";
} else {
    $session_id =  $_COOKIE[$cookie_name];
}

if (isset($_GET["rating"]))
    $rating = $_GET["rating"];
else
    die("Error. Missing parameter");

if (isset($_GET["ticket"]))
    $ticket = $_GET["ticket"];
else
    die("Error. Missing parameter");

if (isset($_GET["number"]))
    $number = $_GET["number"];
else
    die("Error. Missing parameter");

if (isset($_GET["staff_id"]))
    $staff_id = $_GET["staff_id"];
else
    die("Error. Missing parameter");

if (isset($_GET["topic_id"]))
    $topic_id = $_GET["topic_id"];
else
    die("Error. Missing parameter");

if (isset($_GET["c_experience"]))
    $c_experience = $_GET["c_experience"];
else
    die("Error. Missing parameter");

if (isset($_COOKIE[$cookie_name]))
    $session_id =  $_COOKIE[$cookie_name];

$user_id = $_SESSION["_auth"]["user"]["id"];
$auth_staff_id = $_SESSION["_auth"]["staff"]["id"];


if ($auth_staff_id != null) {
    header("Location: " . $errorStaffPage);
    exit();
}

if ($user_id == null) {
    header("Location: " . $errorNoUserPage);
    exit();
}

$getUserInfoSql = "SELECT * FROM `ost_session` WHERE session_id ='" . $session_id . "'";
$info = db_query($getUserInfoSql);
$infoRes = db_assoc_array($info);

if (count($infoRes) != 0) {

    $user_ip = $infoRes[0]['user_ip'];

    $checkSql = "SELECT * FROM `ost_ratings` WHERE ticket_id = " . $ticket . " AND user_id= '" . $user_id . "'";
    $res = db_query($checkSql);

    $count = count(db_assoc_array($res));


    if ($count == 0) {
        $sql = "INSERT INTO `ost_ratings`(`rating`, `ticket_id`, `staff_id`, `user_ip`, `user_id`, `topic_id`, `number`,`c_experience`) VALUES (" . $rating . "," . $ticket . "," . $staff_id . ",'" . $user_ip . "','" . $user_id . "'," . $topic_id . ",'" . $number . "'," . $c_experience . ")";
        error_log($sql, 4);
        if (db_query($sql)) {
            $success = true;
            //Redirect to custom page after vote
            header("Location: " . $resultPage);
            exit();
        } else {
            header("Location: " . $genericErrorPage);
            exit();
        }
    } else {
        header("Location: " . $genericErrorPage);
        exit();
    }
} else {
    header("Location: " . $genericErrorPage);
    exit();
}
