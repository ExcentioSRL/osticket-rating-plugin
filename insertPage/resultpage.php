<?php
//http://localhost:8888/include/plugins/rating/insertPage/resultpage.php?rating=1&ticket=2334&operatore=Utente-Test&categoria=categoria%20di%20test
include("resultpagecontroller.php");

?>

<!DOCTYPE html>
<html>

<head>
    <title>Thanks for rating</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container">
        <div class="col-sm-12" style="width:'100%'; text-align:center">
            
                <?php 

                    if($success){?>
                        <h2 style="color: green">Thanks for your vote</h2>
                    <?php   
                    }else { ?>
                        <h2 style="color: red">Operation not allowed</h2>
                    <?php } ?>

                <button onclick="window.location.href='https:\/\/aftersales.osai-as.com\/'">Go back</button>   <!-- Redirect alla pagina giusta --> 
        
       </div>

</body>

</html>