<?php

date_default_timezone_set('Europe/Rome'); 
$row = 0;
$start; 
$end; 

$operator;
$category;

$sql = 'SELECT * FROM `ost_ratings` ORDER BY timestamp DESC';

$res = db_query($sql);
$items = db_assoc_array($res);


if (isset($_GET["export"])) {
    if (trim($_GET["start"]) != "")
        $start = date("Y-m-d H:i:s", strtotime($_GET["start"]));

    if (trim($_GET['end']) != "")
        $end = date("Y-m-d H:i:s", strtotime($_GET["end"]));



    if ($start != null && $end != null) {
        $sql = "SELECT * FROM `ost_ratings` WHERE timestamp >= '" . $start . "' AND  timestamp <= '" . $end . "' ";
        if (trim($_GET["operator"]) != "")
            $sql = $sql . " AND operatore LIKE '%" . $_GET["operator"] . "%'";
        if (trim($_GET["category"]) != "")
            $sql = $sql . " AND categoria LIKE '%" . $_GET["category"] . "%'";

        $sql = $sql . " ORDER BY timestamp DESC";
    } else
        $sql = 'SELECT * FROM `ost_ratings` ORDER BY timestamp DESC';




    $res = db_query($sql);
    $items = db_assoc_array($res);

    $fileName = "Ticket_ratings_" . date('d-m-Y') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=' . $fileName);

    $heading = false;

    if (!empty($items)) {
        foreach ($items as $item) {
            if (!$heading) {
                echo "Informazioni cronologiche\tTicket\tOperatore\tCategoria\tRating\tSessione" . "\n";
                $heading = true;
            }
            echo date("d/m/Y H:i:s", strtotime($item['timestamp'])) . "\t" . $item["ticket"] . "\t" . $item["operatore"] . "\t" . $item["categoria"] . "\t" . $item["rating"] . "\t" . $item["session_id"] . "\n";
        }
    } else {
        echo "Informazioni cronologiche\tTicket\tOperatore\tCategoria\tRating\tSessione" . "\n";
    }
    exit();
}

if (isset($_GET["filter"])) {
    if (isset($_GET["start"]) && $_GET["start"] != "")
        $start = date("Y-m-d H:i:s", strtotime($_GET["start"]));
    if (isset($_GET["end"]) && $_GET["end"] != "")
        $end = date("Y-m-d H:i:s", strtotime($_GET["end"]));
    $operator = $_GET["operator"];
    $category = $_GET["category"];


    $first = true;

    $sql = "SELECT * FROM `ost_ratings` WHERE ";
    if ($start != null && $end != null) {
        $sql = $sql . "timestamp >= '" . $start . "' AND  timestamp <= '" . $end . "' ";
        $first = false;
    }

    if (trim($_GET["operator"]) != ""){
        if($first)
            $sql = $sql . "operatore LIKE '%" . $_GET["operator"] . "%'";
        else
             $sql = $sql . " AND operatore LIKE '%" . $_GET["operator"] . "%'";
        $first = false;
    }

    if (trim($_GET["category"]) != ""){
        if($first)
            $sql = $sql . "categoria LIKE '%" . $_GET["category"] . "%'";
        else
            $sql = $sql . "AND categoria LIKE '%" . $_GET["category"] . "%'";
        $first = false;
    }
  

    $sql = $sql . " ORDER BY timestamp DESC";

    if(trim($_GET["operator"]) == "" && trim($_GET["category"]) == "" && $start == null && $end == null)
        $sql = 'SELECT * FROM `ost_ratings` ORDER BY timestamp DESC';



    echo($sql);


    $res = db_query($sql);
    $items = db_assoc_array($res);
}

if (isset($_GET["sort"])) {

    if ($_GET["dir"] == 0)
        $sql = 'SELECT * FROM `ost_ratings` ORDER BY ' . $_GET["sort"] . ' DESC';
    else if ($_GET["dir"] == 1)
        $sql = 'SELECT * FROM `ost_ratings` ORDER BY ' . $_GET["sort"] . ' ASC';
    else
        $sql = 'SELECT * FROM `ost_ratings` ORDER BY timestamp DESC';


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
