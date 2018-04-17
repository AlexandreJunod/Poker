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
    header('Location: index.php'); //The user is redirected to the log in page
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

//Used to show the players, pseudo, money, bets, hand and take the order
$query = "SELECT PseudoPlayer, MoneySeat, BetSeat, HandSeat, OrderSeat, fkPlayerSeat FROM poker.player INNER JOIN poker.seat ON player.idPlayer = seat.fkPlayerSeat";
$ShowPlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Gives the number of the dealer and the amount of the blind 
$query = "SELECT BlindGame, DealerGame FROM poker.game ";
$ShowDealers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Check if some one have to pay a blind
$query = "SELECT idSeat FROM poker.seat WHERE fkStatusSeat = '4'";
$PayTheBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Check if some one is eliminated
$query = "SELECT OrderSeat as OrderEliminatedPlayer FROM poker.seat WHERE fkStatusSeat = '99'";
$EliminatePlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Check the hour of start game
$query = "SELECT HourStartGame FROM poker.game";
$StartHours = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//----------------------------- PHP  ------------------------------------------------------

if($FreeSeats->rowCount() > 0) //Check if there is free seats
{
    $FreeSeat = $FreeSeats->fetch();
    extract($FreeSeat); //$NbFreeSeats
    
    if($NbFreeSeats >= 1) //Check if the game has started, show the message only if the game hasn't start yet
    {
        echo "<div class='ErrorMsg'>En attente de $NbFreeSeats joueurs</div>"; //Show how many free seats are available
    }
    else //The game starts
    {
        //All the status of the seats are updated to "In Game"
        $query = "UPDATE poker.seat SET fkStatusSeat = '2' WHERE fkStatusSeat = '1'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
}

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

if($StartHours->rowCount() > 0) //Check if there is an hour of start
{
    $StartHour = $StartHours->fetch();
    extract($StartHour); //$HourStartGame
    
    if($HourStartGame == NULL)
    {
        $HourStartGame = date('H:i:s'); //Hour of start game
        
        //Update the table, to get the hour of start
        $query = "UPDATE poker.game SET HourStartGame = '$HourStartGame'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
    else
    {
        $HourNow = date('H:i:s'); //Hour of the moment
        $HourDiff = $HourNow - $HourStartGame; //Hour difference
        echo "$HourDiff";
    }
}

$PersonnalView = $NbTotalSeats - $OrderSeat; //The number total of seats less the seat where I am, is the number of times I've to change place to be at first place. $OrderSeat comes from $InfoPlayers

if($ShowPlayers->rowCount() > 0) //Check if there is players to show
{
    foreach($ShowPlayers as $ShowPlayer)
    {
        $ShowPseudoPlayer = $ShowPlayer['PseudoPlayer'];
        $ShowMoneySeat = $ShowPlayer['MoneySeat'];
        $ShowBetSeat = $ShowPlayer['BetSeat']; //NOT ALREADY USED. CREATE DIVS
        $ShowHandSeat = $ShowPlayer['HandSeat']; //NOT ALREADY USED. CREATE DIVS
        $ShowOrderSeat = $ShowPlayer['OrderSeat'];
        $ShowfkPlayerSeat = $ShowPlayer['fkPlayerSeat'];
        
        $ShowMoneySeat = number_format ($ShowMoneySeat, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Number format, for distinguish easier the thousands
        $ShowOrderSeat = ($ShowOrderSeat + $PersonnalView)%$NbTotalSeats; //Make the player go to the first place. %$NbTotalSeats do the number come back at 0 when he is at the end of the last place of the table
        
        echo "<div class='SeatPlayer$ShowOrderSeat'>$ShowPseudoPlayer<br>$ShowMoneySeat
            <form method='post' id='EliminateForm'></form>
            <button type='submit' form='EliminateForm' name='Eliminate' value='$ShowfkPlayerSeat'>Eliminer</button>
        </div>"; //Show the players
    }
}

$TokensView = $NbTotalSeats; // - $NbFreeSeats; //The number total of seats less the number of seats free, tells wich places can take a token

if($ShowDealers->rowCount() > 0) //Check if there is a dealer to show
{
    $ShowDealer = $ShowDealers->fetch();
    extract($ShowDealer); //$BlindGame, $DealerGame
    
    $BetSmallBlind = $BlindGame/2; //Select the amount of a small blind. $BlindGame comes from the query $ShowDealers
    $BetBigBlind = $BlindGame; //Select the amount of a big blind. $BlindGame comes from the query $ShowDealers
    $WhereIsTheSmallBlind = ($DealerGame + 1)%$TokensView; //Select the place of the small blind
    $WhereIsTheBigBlind = ($DealerGame + 2)%$TokensView; //Select the place of the big blind
    
    $ShowDealerGame = ($DealerGame + $PersonnalView)%$TokensView; //Add to the dealer, the number of rotations needed for keep the right vue, and do modulo who corresponds with the taken seats
    $ShowSmallBlindGame = ($DealerGame + $PersonnalView + 1)%$TokensView;
    $ShowBigBlindGame = ($DealerGame + $PersonnalView + 2)%$TokensView;
    
    echo "<div class='TokenPlayer$ShowBigBlindGame'>BB</div>"; //Show the big blind
    echo "<div class='TokenPlayer$ShowSmallBlindGame'>SB</div>"; //Show the small blind
    echo "<div class='TokenPlayer$ShowDealerGame'>D</div>"; //Show the dealer
}

if($PayTheBlinds->rowCount() > 0) //Check if some one have to pay the small blind
{
    //Bet automatically the money of the small blind
    $query = "UPDATE poker.seat SET MoneySeat = MoneySeat-'$BetSmallBlind', BetSeat = BetSeat+'$BetSmallBlind', fkStatusSeat = '2' WHERE OrderSeat = '$WhereIsTheSmallBlind'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    
    //Bet automatically the money of the big blind
    $query = "UPDATE poker.seat SET MoneySeat = MoneySeat-'$BetBigBlind', BetSeat = BetSeat+'$BetBigBlind', fkStatusSeat = '2' WHERE OrderSeat = '$WhereIsTheBigBlind'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    header('Location: table.php'); //Refresh the page, for show immediatly the bet
}

if($EliminatePlayers->rowCount() > 0) //Check if a user is eliminated
{
    $EliminatePlayer = $EliminatePlayers->fetch();
    extract($EliminatePlayer); //$OrderEliminatedPlayer
    
    if($OrderEliminatedPlayer == $OrderSeat) //Check if I am the player eliminated
    {
        $_SESSION['Error'] = '2'; //Gives authorization to show an error, and tell to the user he is eliminated
    
        // Possible de rentrer dans le post getup, au lieu de refaire un copier coller ? Le OrderEliminatedPlayer peut être remplacer par $OrderSeat 
        //==========================================================================================================================================================
        //Select the order of all the seats with an higher number than the user who is eliminated
        $query = "SELECT OrderSeat FROM poker.seat WHERE OrderSeat > '$OrderEliminatedPlayer'"; 
        $AfterMePlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

        //Delete the informations on the seat, about the user who is eliminated
        $query = "UPDATE poker.seat SET MoneySeat = NULL, HandSeat = NULL, OrderSeat = NULL, fkPlayerSeat = NULL, fkStatusSeat = '2' WHERE OrderSeat = '$OrderEliminatedPlayer'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

        foreach($AfterMePlayers as $AfterMePlayer) //Change the number of the OrderSeat one by one, but conserves the real order of playing
        {
            $AfterMeSeat = $AfterMePlayer['OrderSeat']; //Takes the order of a seat after me
            $AfterMeNewSeat = $AfterMeSeat - 1; //The new order is equal at the order of a player after me less 1

            //Update the order of the players after me. 
            $query = "UPDATE poker.seat SET OrderSeat = '$AfterMeNewSeat' WHERE OrderSeat = '$AfterMeSeat'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        //==========================================================================================================================================================        
        header('Location: home.php'); //The user is redirected to the home
    }
}

//----------------------------- Processing POST ------------------------------------------

if(isset($_POST['Getup'])) //Check if the user clicked on the get up button
{
    //Select the order of all the seats with an higher number than the user who leaves the table
    $query = "SELECT OrderSeat FROM poker.seat WHERE OrderSeat > '$OrderSeat'"; //We got $OrderSeat by the sql request $InfoPlayers
    $AfterMePlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Delete the informations on the seat, about the user who is leaving the table
    $query = "UPDATE poker.seat SET MoneySeat = NULL, HandSeat = NULL, OrderSeat = NULL, fkPlayerSeat = NULL, fkStatusSeat = '2' WHERE OrderSeat = '$OrderSeat'"; //We got $OrderSeat by the sql request $InfoPlayers
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    foreach($AfterMePlayers as $AfterMePlayer) //Change the number of the OrderSeat one by one, but conserves the real order of playing
    {
        $AfterMeSeat = $AfterMePlayer['OrderSeat']; //Takes the order of a seat after me
        $AfterMeNewSeat = $AfterMeSeat - 1; //The new order is equal at the order of a player after me less 1
        
        //Update the order of the players after me. 
        $query = "UPDATE poker.seat SET OrderSeat = '$AfterMeNewSeat' WHERE OrderSeat = '$AfterMeSeat'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
    
    if($NbFreeSeats <= 4) //The game has ended
    {
        //Set the seats in "Waiting"
        $query = "UPDATE poker.seat SET fkStatusSeat = '1' WHERE fkStatusSeat != '1'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        //Reset de dealer
        $query = "UPDATE poker.game SET PotGame = '0', BoardGame = NULL, BlindGame = '3000', DealerGame = '0', HourStartGame = NULL";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
    
    header('Location: home.php'); //The user is redirected to the home
}

if(isset($_POST['NextHand'])) //Check if the user clicked to go to the next hand
{    
    //Put the status on bet a blind
    $query = "UPDATE poker.seat SET fkStatusSeat = '4' WHERE OrderSeat = '$WhereIsTheSmallBlind'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Put the status on bet a blind
    $query = "UPDATE poker.seat SET fkStatusSeat = '4' WHERE OrderSeat = '$WhereIsTheBigBlind'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    $DealerGame++; //The next player will be the dealer
    
    //Update the player who is the dealer, after a new hand
    $query = "UPDATE poker.game SET DealerGame = '$DealerGame'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    header('Location: table.php'); //Prevent to send the form in a loop
}

if(isset($_POST['Eliminate'])) //Check if the user clicked on the eliminate button
{
    $PlayerToEliminiate = $_POST['Eliminate'];
    
    //Put the status of the player selected, on eliminated
    $query = "UPDATE poker.seat SET fkStatusSeat = '99' WHERE fkPlayerSeat = '$PlayerToEliminiate'"; //We got $OrderSeat by the sql request $InfoPlayers
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
    header('Location: table.php'); //Prevent to send the form in a loop
}

//echo "POST: "; print_r($_POST); echo "<br>";

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
        if($Pseudo == 'Alexandre' && $FreePositions->rowCount() == 0) // The button is visible only if the pseudo is Alexandre and the game has started
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