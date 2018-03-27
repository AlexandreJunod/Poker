<!--====================================================================================================================
||  Author : Junod Alexandre                                                                                           ||
||  Creation : 02.03.2018                                                                                              ||
||  Summary : This is the page where the users will play a poker game                                                  ||
||====================================================================================================================-->

<?php
//----------------------------- Start SESSION ----------------------------------------

session_start();
require_once("includes/functions.php");
ConnectDB();
date_default_timezone_set('Europe/Berlin'); //Set the hour to UTC+01:00

//----------------------------- Processing SESSION ---------------------------------------

if(isset($_SESSION['Pseudo'])) //Recover the pseudo of the user saved on the SESSION
{
    $Pseudo = $_SESSION['Pseudo'];
}

//----------------------------- Processing POST ------------------------------------------


if(isset($_POST['Getup'])) //Check if the user clicked on the get up button
{
    header('Location: home.php'); //The user is redirect to the home
}

if(isset($_POST['NextHand'])) //Check if the user clicked to go to the next hand
{    
    header('Location: table.php'); //Prevent to send the form in a loop
}

//----------------------------- Processing GET -------------------------------------------

// ONLY PHP UP UNTIL NOW
//----------------------------- Generation of the page-------------------------------------
// HTML + PHP FROM HERE

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="includes/style.css"/>
        <title><?php echo $TitleTab; ?></title>
    </head>
    <body background="includes\images\TablePoker.jpg">
        <div class="InfosPlayer"><?php echo "$Pseudo<br>Money in coming"; ?>
            <form method="post" id="GetupForm"></form>
            <button type="submit" form="GetupForm" name="Getup">Se lever</button>
        </div>
        <?php 
        if($Pseudo == 'Alexandre') // The button is visible only of the pseudo is Alexandre
        {
            ?>
            <div class="Button">
                <form method="post" id="NextHandForm"></form>
                <button type="submit" form="NextHandForm" name="NextHand">Prochaine main</button>
            </div>
            <?php 
        }
        ?>
    </body>
    <script>setInterval(function(){location.reload()},3000);</script> <!-- //Refresh the page. Code gived by my projet manager --> 
</html>

<?php
//----------------------------- Saving SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>