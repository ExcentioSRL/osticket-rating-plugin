<?php
 require_once  '../../class.signal.php';


    $rating = $_GET["rating"];
    $userId = $_GET["userid"];

    Signal::send("rating",null,  $_GET["rating"])
   
?>

<div style="display: flex; flex-direction: row; justify-content:center" >
    <p style="font-size: 20; font-wight:bold">
    Thanks for your vote
    </p>   
</div>
<div style="display: flex; flex-direction: row; justify-content:center" >
    <button>Go back</button>
</div>