<?php

$row = 0;
$start; 
$end; 

$username;
$topic;
$type = $_GET["type"]? $_GET["type"] : "w-rating";

$join = "OST_R 
        JOIN `ost_user` OST_U ON OST_R.user_id = OST_U.id
        JOIN `ost_staff` OST_S ON OST_R.staff_id = OST_S.staff_id
        JOIN `ost_help_topic` OST_HT ON OST_R.topic_id = OST_HT.topic_id";

$referralRating =
"(
SELECT GROUP_CONCAT(`username` SEPARATOR ',' ) 
FROM `ost_thread` OST_TR JOIN `ost_thread_referral` 
OST_THR ON OST_THR.thread_id = OST_TR.id 
JOIN `ost_staff` OST_S ON OST_S.staff_id = OST_THR.object_id
WHERE OST_TR.object_id = OST_R.ticket_id AND OST_THR.object_type='S'
) As `referrals` ,
(
SELECT GROUP_CONCAT(`name` SEPARATOR ',' ) 
FROM `ost_thread` OST_TR JOIN `ost_thread_referral` 
OST_THR ON OST_THR.thread_id = OST_TR.id 
JOIN `ost_department` OST_D ON OST_D.id = OST_THR.object_id 
WHERE OST_TR.object_id = OST_R.ticket_id AND OST_THR.object_type='D'
) As `referrals_dept` ,
(
SELECT GROUP_CONCAT(`name` SEPARATOR ',' ) 
FROM `ost_thread` OST_TR JOIN `ost_thread_referral` 
OST_THR ON OST_THR.thread_id = OST_TR.id 
JOIN `ost_team` OST_TEAM ON OST_TEAM.team_id = OST_THR.object_id 
WHERE OST_TR.object_id = OST_R.ticket_id AND OST_THR.object_type='E'
) As `referrals_team` 
";

$referral = "(
SELECT GROUP_CONCAT(`username` SEPARATOR ',' ) 
FROM `ost_thread` OST_TR JOIN `ost_thread_referral` 
OST_THR ON OST_THR.thread_id = OST_TR.id 
JOIN `ost_staff` OST_S ON OST_S.staff_id = OST_THR.object_id
WHERE OST_TR.object_id = OST_T.ticket_id AND OST_THR.object_type='S'
) As `referrals` ,
(
SELECT GROUP_CONCAT(`name` SEPARATOR ',' ) 
FROM `ost_thread` OST_TR JOIN `ost_thread_referral` 
OST_THR ON OST_THR.thread_id = OST_TR.id 
JOIN `ost_department` OST_D ON OST_D.id = OST_THR.object_id 
WHERE OST_TR.object_id = OST_T.ticket_id AND OST_THR.object_type='D'
) As `referrals_dept` ,
(
SELECT GROUP_CONCAT(`name` SEPARATOR ',' ) 
FROM `ost_thread` OST_TR JOIN `ost_thread_referral` 
OST_THR ON OST_THR.thread_id = OST_TR.id 
JOIN `ost_team` OST_TEAM ON OST_TEAM.team_id = OST_THR.object_id 
WHERE OST_TR.object_id = OST_T.ticket_id AND OST_THR.object_type='E'
) As `referrals_team` 
";


$noRatingSql = "
    SELECT * , " . $referral . " FROM `ost_ticket` OST_T
    JOIN `ost_user` OST_U ON OST_T.user_id = OST_U.id
    JOIN `ost_staff` OST_S ON OST_T.staff_id = OST_S.staff_id
    JOIN `ost_help_topic` OST_HT ON OST_T.topic_id = OST_HT.topic_id
    LEFT JOIN `ost_ratings` OST_R ON OST_T.ticket_id = OST_R.ticket_id
    JOIN `ost_ticket` OST_TT ON OST_TT.ticket_id = OST_T.ticket_id
    WHERE OST_R.ticket_id IS NULL ";

$allSql = "
    SELECT * , " . $referral . " FROM `ost_ticket` OST_T
    JOIN `ost_user` OST_U ON OST_T.user_id = OST_U.id
    JOIN `ost_staff` OST_S ON OST_T.staff_id = OST_S.staff_id
    JOIN `ost_help_topic` OST_HT ON OST_T.topic_id = OST_HT.topic_id
    LEFT JOIN `ost_ratings` OST_R ON OST_T.ticket_id = OST_R.ticket_id
    JOIN `ost_ticket` OST_TT ON OST_TT.ticket_id = OST_T.ticket_id
    ";



$sql = "SELECT `timestamp`,`number`,`topic`,`username`,`rating`,`user_id`,`user_ip`,`name`, ". $referralRating." FROM `ost_ratings` ".$join." ORDER BY timestamp DESC";


$res = db_query($sql);
$items = db_assoc_array($res);

if (isset($_GET["export"])) {
    if (isset($_GET["start"]) && $_GET["start"] != "")
        $start = date("Y-m-d H:i:s", strtotime($_GET["start"]));
    if (isset($_GET["end"]) && $_GET["end"] != "")
        $end = date("Y-m-d H:i:s", strtotime($_GET["end"]));
    $username = $_GET["username"];
    $topic = $_GET["topic"];

    $sql = createQuery($start, $end, $noRatingSql, $allSql,$join, $referralRating, $referral);
    
    $res = db_query($sql);
    $items = db_assoc_array($res);

    $fileName = "Ticket_ratings_" . date('d-m-Y') . ".csv";
    $csv = fopen($fileName, "w") or die("Unable to open file!");

    $heading = false;

    if (!empty($items)) {
        foreach ($items as $item) {
            if (!$heading) {
                fputcsv($csv,explode("\t", "Date\tTicket\tTopic\tOperator\tRating\tUser\tUser IP\tReferrals" . "\n"));
                $heading = true;
            }
            $tmpRow = ($item['timestamp'] != "" ? date("d/m/Y H:i:s", strtotime($item['timestamp'])) : "-" ). "\t" . $item["number"] . "\t" . $item["topic"] . "\t" . $item["username"] . "\t" . $item["rating"] ."\t". $item["name"] . "\t" . $item["user_ip"] . "\t". formatReferrals($item['referrals'], $item['referrals_dept'], $item['referrals_team'])."\n";
            fputcsv($csv, explode("\t", $tmpRow));
        }
    } else {
        fputcsv($csv,explode("\t", "Date\tTicket\tTopic\tOperator\tRating\tUser\tUser IP\tReferrals" . "\n"));
    }
    fclose($csv);

    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename='.basename($fileName));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fileName));
    header('Content-Type: application/vnd.ms-excel');
    readfile($fileName);
    exit();
}

if (isset($_GET["filter"])) {
    if (isset($_GET["start"]) && $_GET["start"] != "")
        $start = date("Y-m-d H:i:s", strtotime($_GET["start"]));
    if (isset($_GET["end"]) && $_GET["end"] != "")
        $end = date("Y-m-d H:i:s", strtotime($_GET["end"]));
    $username = $_GET["username"];
    $topic = $_GET["topic"];

    $sql = createQuery($start, $end, $noRatingSql, $allSql, $join, $referralRating, $referral);
    
    $res = db_query($sql);
    $items = db_assoc_array($res);
}

if (isset($_GET["sort"])) {

    $sort = $_GET["sort"];
    if($sort == "number")
        $sort = "OST_TT.number";
    else if($sort == "user_id")
        $sort = "OST_TT.user_id";

    if($_GET["type"] == "w-rating")
        $sql = "SELECT `timestamp`,`number`,`topic`,`username`,`rating`,`user_id`,`user_ip`,`name`, ". $referralRating." FROM `ost_ratings` ".$join;
    else if($_GET["type"] == "wo-rating")
        $sql = $noRatingSql;
    else if($_GET["type"] == "all")
        $sql = $allSql;
    
    if ($_GET["dir"] == 0)
        $sql = $sql.' ORDER BY ' . $sort . ' DESC';
    else if ($_GET["dir"] == 1)
        $sql = $sql.' ORDER BY ' . $sort . ' ASC';
    else
        $sql = $sql.' ORDER BY timestamp DESC';

    
    $res = db_query($sql);
    $items = db_assoc_array($res);
   
}

function formatReferrals($user,$dept,$team){
    $res = "";
    if($user)
        $res .= $user.",";
    if($dept)
        $res .= $dept.",";
    if($team)
        $res .= $team.",";

    $res = rtrim($res, ',');

    return $res;

}


function getClass($sort)
{
    if (isset($_GET["sort"]) != null)
        if ($sort == $_GET["sort"])
            if (isset($_GET["dir"]) != null)
                if ($_GET["dir"] == 0)
                    return "desc";
                else
                    return "asc";
    return "";
}

function getSort($sort)
{
    if (isset($_GET["sort"]) == null)
        return "1";

    if ($_GET["sort"] != $sort)
        return "1";

    if (isset($_GET["dir"]) != null)
        if ($_GET["dir"] == 1)
            return "0";
        else
            return "1";
    else
        return "1";
}


function createQuery($start, $end, $noRatingSql, $allSql, $join, $referralRating, $referral){
    $first = true;
    
    if(trim($_GET["username"]) == "" && trim($_GET["topic"]) == "" && $start == null && $end == null){
        if($_GET["type"] == "w-rating")
            $sql = "SELECT * , ". $referralRating." FROM `ost_ratings` ".$join." ORDER BY timestamp DESC";
        else if($_GET["type"] == "wo-rating")
            $sql = $noRatingSql;
        else if($_GET["type"] == "all")
            $sql = $allSql;
    }
    else{

        if($_GET["type"] == "wo-rating")
            $sql = $noRatingSql. " AND ";
        else if($_GET["type"] == "w-rating")
            $sql = "SELECT * , ". $referralRating." FROM `ost_ratings` ".$join." WHERE ";
        else if($_GET["type"] == "all")
            $sql = $allSql." WHERE ";

        if ($start != null && $end != null) {
            $sql = $sql . "timestamp >= '" . $start . "' AND  timestamp <= '" . $end . "' ";
            $first = false;
        }

        if (trim($_GET["username"]) != ""){
            if($first)
                $sql = $sql . "username LIKE '%" . $_GET["username"] . "%'";
            else
                $sql = $sql . " AND username LIKE '%" . $_GET["username"] . "%'";
            $first = false;
        }

        if (trim($_GET["topic"]) != ""){
            if($first)
                $sql = $sql . "topic LIKE '%" . $_GET["topic"] . "%'";
            else
                $sql = $sql . "AND topic LIKE '%" . $_GET["topic"] . "%'";
            $first = false;
        }
    


        $sql = $sql . " ORDER BY timestamp DESC";
    }
    return $sql;

}