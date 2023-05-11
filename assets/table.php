<?php
//Add script to read MySQL data and export to excel


include(RATING_ASSET_DIR . "export.php");

global $ost;
global $cfg;
require('staff.inc.php');

$nav = new \StaffNav($thisstaff);

$nav->setTabActive('apps', (RATINGS_WEB_ROOT . 'settings/forms'));
require(STAFFINC_DIR . 'header.inc.php');


?>


<!DOCTYPE html>
<html>

<head>
    <title>Osai Rating</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

    <div class="container">
        <div class="pull-left flush-left">
            <h2>Tickets Rating</h2>
        </div>
        <div class="clear"></div>

        <div class="col-sm-12">
            <div style="display:flex; flex-direction: row; ">

                <form action="#" method="get">
                    <div>
                        <div style="display:flex; flex-direction: row; justify-content: space-between; width:100%;">
                            <div>
                                <label for="start">Start date:</label>
                                <input type="datetime-local" id="start" name="start" value="<?php echo $start; ?>">
                            </div>

                            <div>&nbsp;</div>
                            <div>&nbsp;</div>

                            <div>
                                <label for="end">End date:</label>
                                <input type="datetime-local" id="end" name="end" value="<?php echo $end; ?>">
                            </div>

                            <div>&nbsp;</div>
                            <div>&nbsp;</div>

                            <div>
                                <label for="username">Operator:</label>
                                <input type="text" id="username" name="username" value="<?php echo $username; ?>">
                            </div>

                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            
                            <div >
                                <label for="topic">Topic:</label>
                                <input type="text" id="topic" name="topic" value="<?php echo $topic; ?>">
                            </div>
                        </div>
                    </div>
                   
                    <div style="display:flex; flex-direction: row; justify-content: center; width:100%; margin-top:3rem;">

                        <a href="?"><button class="red button action-button" id="clear" name="clear" value="Clear" class="attached button">Clear filter</button></a>
                        <button class="red button action-button muted" type="submit" id="filter" name="filter" value="Filter" class="attached button"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                        <button class="green button action-button muted" type="submit" id="export" name="export" value="Export to csv" class="attached button"> Export to csv</button>
                    
                    </div>
                    
                </form>
            </div>
        </div>
        <br />
        <?php
        if (count($items) != 0) { ?>
            <div class="pull-middle flush-center" style='width:100%; text-align:center; margin-top:2rem'>
                <h2><?php echo count($items); ?> results</h2>
            </div>
        <?php } ?>
        <table id="" class="list queue tickets  dashboard-stats " style="width:100%">
            <thead>
                <tr>
                    <th style="width:12px"></th>
                    <th><a class=<?php echo "'" . getClass("timestamp") . "'"; ?> href="?sort=timestamp&dir=<?php echo getSort("timestamp") ?>"> <strong>Date</strong> </a></th>
                    <th><a class=<?php echo "'" . getClass("number") . "'"; ?> href="?sort=number&dir=<?php echo getSort("number") ?>"> <strong>Ticket</strong></a></th>
                    <th><a class=<?php echo "'" . getClass("topic") . "'"; ?> href="?sort=topic&dir=<?php echo getSort("topic") ?>"> <strong>Topic</strong></a></th>
                    <th><a class=<?php echo "'" . getClass("username") . "'"; ?> href="?sort=username&dir=<?php echo getSort("username") ?>"> <strong>Operator</strong></a></th>
                    <th><a class=<?php echo "'" . getClass("rating") . "'"; ?> href="?sort=rating&dir=<?php echo getSort("rating") ?>"> <strong>Rating</strong></a></th>
                    <th><a class=<?php echo "'" . getClass("user_id") . "'"; ?> href="?sort=user_id&dir=<?php echo getSort("user_id") ?>"> <strong>User</strong></a></th>
                    <th><a class=<?php echo "'" . getClass("user_ip") . "'"; ?> href="?sort=user_ip&dir=<?php echo getSort("user_ip") ?>"> <strong>User IP</strong></a></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 1;
                foreach ($items as $item) { ?>
                    <tr>
                        <td><strong><?php echo $index; ?></strong></td>
                        <td><strong><?php echo date("d/m/Y H:i:s", strtotime($item['timestamp'])); ?></strong></td>
                        <td><?php echo $item['number']; ?></td>
                        <td><?php echo $item['topic']; ?></td>
                        <td><?php echo $item['username'];?></td>
                        <td><?php echo $item['rating']; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['user_ip']; ?></td>
                    </tr>
                <?php
                    $index++;
                } ?>
            </tbody>

        </table>
        <?php
        if (count($items) == 0) { ?>
            <div class="pull-middle flush-center" style='width:100%; text-align:center; margin-top:2rem'>
                <h2>There are no results</h2>
            </div>
        <?php } ?>

    </div>

</body>

</html>


<?php
include(STAFFINC_DIR . 'footer.inc.php');
?>