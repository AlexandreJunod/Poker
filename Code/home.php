<!--====================================================================================================================
||  Author : Junod Alexandre                                                                                           ||
||  Creation : 02.03.2018                                                                                              ||
||  Summary : This page is used by the user to get the error massages, join tables or disconnect                       ||
||====================================================================================================================-->

<?php
//----------------------------- Start SESSION ----------------------------------------

session_start();
require_once("includes/functions.php");
ConnectDB();

//----------------------------- Processing SESSION ---------------------------------------

if(isset($_SESSION['Pseudo'])) //Recover the pseudo of the user saved on the SESSION
{
    $Pseudo = $_SESSION['Pseudo'];
}

if(isset($_SESSION['Error'])) //Error massage, if the user joined a table full or in game
{
    echo "<div class='ErrorMsg'>Partie en cours, veuillez ressayer plus tard</div>";
    echo "<script>setInterval(function(){location.reload()},2000);</script>"; //Refresh the page. Code gived by my projet manager
}

//----------------------------- Processing POST ------------------------------------------

if(isset($_POST['JoinTable'])) //Check if the user clicked on the button to join the table
{
    header('Location: table.php'); //The user is trying to join the table
}

if(isset($_POST['Signout'])) //Check if the user clicked on the sign out button
{
    unset($_SESSION); //Unset all the SESSIONS saved of the user
    header('Location: index.php'); //The user is redirect to the log in page
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
        <div class="InfosPlayer"><?php echo $Pseudo; ?>
            <form method="post" id="SignoutForm"></form>
            <button type="submit" form="SignoutForm" name="Signout">Déconnexion</button>
        </div>
        <form method="post" id="JoinTableForm"></form>
        <div class="ContainerHome"><button type="submit" form="JoinTableForm" name="JoinTable">Rejoindre la table</button></div>
    </body>
</html>

<?php
//----------------------------- Saving SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
unset($_SESSION['Error']); //Unset the SESSION Error, to stop to show the massage at next refresh
?>