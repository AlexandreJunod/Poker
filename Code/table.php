<!--====================================================================================================================
||  Author : Junod Alexandre                                                                                           ||
||  Creation : 02.03.2018                                                                                              ||
||  Summary : This is the page where the users will play a poker game                                                  ||
||====================================================================================================================-->

<?php
//----------------------------- Start SESSION --------------------------------------------

session_start();
require_once("includes/functions.php");
ConnectDB();
date_default_timezone_set('Europe/Berlin'); //Set the hour to UTC+01:00
$StartMoney = 150000; //Variable to define the amount of money gived when the users join the table
$NbTotalSeats = 6; //Variable to define the number total of seats

//----------------------------- Processing SESSION ---------------------------------------

if(isset($_SESSION['Pseudo'])) //Recover the pseudo of the user saved on the SESSION
{
    $Pseudo = $_SESSION['Pseudo'];
} 
else //The user isn't logged
{
    header('Location: index.php'); //The user is redirect to the log in page
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

//----------------------------- SQL REQUEST ----------------------------------------------

//Takes informations about the money, the hand and the order of the player logged
$query = "SELECT MoneySeat, HandSeat, OrderSeat FROM poker.seat WHERE fkPlayerSeat = (SELECT idPlayer FROM poker.player WHERE PseudoPlayer = '$Pseudo')";
$InfoPlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Gives the number of 1 free seat out of the game
$query = "SELECT idSeat FROM poker.seat WHERE fkPlayerSeat IS NULL AND fkStatusSeat = '1' ORDER BY fkPlayerSeat ASC LIMIT 1";
$FreePositions = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Count how many seats are free 
$query = "SELECT COUNT(fkGameSeat) AS NbFreeSeats FROM poker.seat WHERE fkPlayerSeat IS NULL";
$FreeSeats = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

if($InfoPlayers->rowCount() > 0) //Check if informations about the user were returned
{
    $InfoPlayer = $InfoPlayers->fetch();
    extract($InfoPlayer); //$MoneySeat, $HandSeat, $OrderSeat
    $MoneySeat = number_format ($MoneySeat, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Number format, for distinguish easier the thousands
}
else //There is no informations, the player isn't on the table
{
    if($FreePositions->rowCount() > 0) //Check if there is a free seat out of the game
    {
        $FreePosition = $FreePositions->fetch();
        extract($FreePosition); //$idSeat
        $FreeSeat = $FreeSeats->fetch();
        extract($FreeSeat); //$NbFreeSeats
        
        $OrderSeatGiven = $NbTotalSeats - $NbFreeSeats; //The user takes everytime the first place available. The number total of seats minus the number of seats free gives the order

        //Gives the money, a seat and an order to the player
        $query = "UPDATE poker.seat SET MoneySeat='$StartMoney', OrderSeat = '$OrderSeatGiven', fkPlayerSeat = (SELECT idPlayer FROM poker.player WHERE PseudoPlayer = '$Pseudo') WHERE idSeat = '$idSeat'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        header('Location: table.php'); //Refresh the page for prevent errors about undifined variable 
    }
    else //There is no free seats, or the game has started
    {
        $_SESSION['Error'] = '1'; //Gives authorization to show an error, and tell to the user the table is full
        header('Location: home.php'); //The user is redirected to the home
    }
}




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
        <div class="InfosPlayer"><?php echo "$Pseudo<br>$MoneySeat"; ?>
            <form method="post" id="GetupForm"></form>
            <button type="submit" form="GetupForm" name="Getup">Se lever</button>
        </div>
        <?php 
        if($Pseudo == 'Alexandre') // The button is visible only if the pseudo is Alexandre
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
//----------------------------- Saving SESSION --------------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>