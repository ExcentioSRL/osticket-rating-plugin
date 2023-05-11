<?php

$row = 0;
$start; 
$end; 

$username;
$topic;

$join = "OST_R 
        JOIN `ost_user` OST_U ON OST_R.user_id = OST_U.id
        JOIN `ost_staff` OST_S ON OST_R.staff_id = OST_S.staff_id
        JOIN `ost_help_topic` OST_HT ON OST_R.topic_id = OST_HT.topic_id";

$sql = 'SELECT * FROM `ost_ratings` '.$join.' ORDER BY timestamp DESC';

$res = db_query($sql);
$items = db_assoc_array($res);


if (isset($_GET["export"])) {
    $first = true;

    if (trim($_GET["start"]) != "")
        $start = date("Y-m-d H:i:s", strtotime($_GET["start"]));

    if (trim($_GET['end']) != "")
        $end = date("Y-m-d H:i:s", strtotime($_GET["end"]));


    $sql = 'SELECT * FROM `ost_ratings` '.$join.' WHERE ';

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
    
    if(trim($_GET["username"]) == "" && trim($_GET["topic"]) == "" && $start == null && $end == null)
        $sql = 'SELECT * FROM `ost_ratings` '.$join.' ORDER BY timestamp DESC';


    $res = db_query($sql);
    $items = db_assoc_array($res);

    $fileName = "Ticket_ratings_" . date('d-m-Y') . ".csv";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=' . $fileName);

    $heading = false;

    if (!empty($items)) {
        foreach ($items as $item) {
            if (!$heading) {
                echo "Date\tTicket\tTopic\tOperator\tRating\tUser\tUser IP" . "\n";
                $heading = true;
            }
            echo date("d/m/Y H:i:s", strtotime($item['timestamp'])) . "\t" . $item["number"] . "\t" . $item["topic"] . "\t" . $item["username"] . "\t" . $item["rating"] ."\t". $item["name"] . "\t" . $item["user_ip"] . "\n";
        }
    } else {
        echo "Date\tTicket\tTopic\tOperator\tRating\tUser\tUser IP" . "\n";
    }
    exit();
}

if (isset($_GET["filter"])) {
    if (isset($_GET["start"]) && $_GET["start"] != "")
        $start = date("Y-m-d H:i:s", strtotime($_GET["start"]));
    if (isset($_GET["end"]) && $_GET["end"] != "")
        $end = date("Y-m-d H:i:s", strtotime($_GET["end"]));
    $username = $_GET["username"];
    $topic = $_GET["topic"];


    $first = true;

    $sql = 'SELECT * FROM `ost_ratings` '.$join.' WHERE ';

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

    if(trim($_GET["username"]) == "" && trim($_GET["topic"]) == "" && $start == null && $end == null)
        $sql = 'SELECT * FROM `ost_ratings` '.$join.' ORDER BY timestamp DESC';




    $res = db_query($sql);
    $items = db_assoc_array($res);
}

if (isset($_GET["sort"])) {

    if ($_GET["dir"] == 0)
        $sql = 'SELECT * FROM `ost_ratings` '.$join.' ORDER BY ' . $_GET["sort"] . ' DESC';
    else if ($_GET["dir"] == 1)
        $sql = 'SELECT * FROM `ost_ratings` '.$join.' ORDER BY ' . $_GET["sort"] . ' ASC';
    else
        $sql = 'SELECT * FROM `ost_ratings` '.$join.' ORDER BY timestamp DESC';


    $res = db_query($sql);
    $items = db_assoc_array($res);
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
