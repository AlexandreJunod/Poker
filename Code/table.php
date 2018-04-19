<!--====================================================================================================================
||  Author : Junod Alexandre                                                                                           ||
||  Creation : 02.03.2018                                                                                              ||
||  Summary : This is the page where the users will play a poker game. He actually can Call, All in and Drop. When the ||
||            player isn't playing, he stills receiving the menu for play, because the game doesn't check if is        ||
||            waiting next hand. The game doesn't work at less than 6 and need interaction of the user "Alexandre"     ||
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
$query = "SELECT MoneySeat, HandSeat, OrderSeat, BetSeat FROM poker.seat WHERE fkPlayerSeat = (SELECT idPlayer FROM poker.player WHERE PseudoPlayer = '$Pseudo')";
$InfoPlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Takes informations about the status of the player logged
$query = "SELECT fkStatusSeat FROM poker.seat WHERE fkPlayerSeat = (SELECT idPlayer FROM poker.player WHERE PseudoPlayer = '$Pseudo')";
$InfoStatus = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

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
$query = "SELECT HourStartGame, PotGame FROM poker.game";
$StartHours = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Select the bigger bet
$query = "SELECT BetSeat as BetBeforeMe FROM poker.seat GROUP BY BetSeat DESC LIMIT 1";
$BeforeBets = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//Select the player playing, for show him
$query = "SELECT OrderSeat as ShowWhoPlays FROM poker.seat WHERE fkStatusSeat = '3'";
$WhoPlays = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//----------------------------- PHP  ------------------------------------------------------

if($FreeSeats->rowCount() > 0) //Check if there is free seats
{
    $FreeSeat = $FreeSeats->fetch();
    extract($FreeSeat); //$NbFreeSeats
    
    $StartHour = $StartHours->fetch();
    extract($StartHour); //$HourStartGame, $PotGame
    
    $PotGame = number_format ($PotGame, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Number format, for distinguish easier the thousands
    
    if($NbFreeSeats >= 1 && $HourStartGame == NULL) //Check if the game has started, show the message only if the game hasn't start yet
    {
        echo "<div class='ErrorMsg'>En attente de $NbFreeSeats joueurs</div>"; //Show how many free seats are available
    }
    else //The game starts
    {
        //All the status of the seats are updated to "In Game"
        $query = "UPDATE poker.seat SET fkStatusSeat = '2' WHERE fkStatusSeat = '1'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        if($HourStartGame == NULL)
        {
            $HourStartGame = date('H:i:s'); //Hour of start game

            //Update the table, to get the hour of start
            $query = "UPDATE poker.game SET HourStartGame = '$HourStartGame'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
    }
}

if($InfoPlayers->rowCount() > 0) //Check if informations about the user were returned
{
    $InfoPlayer = $InfoPlayers->fetch();
    extract($InfoPlayer); //$MoneySeat, $HandSeat, $OrderSeat, BetSeat
    $BrutMoneySeat = $MoneySeat; //Keeps the amount of money of the player, without format edit
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
        $query = "UPDATE poker.seat SET MoneySeat='$StartMoney', BetSeat = '0', OrderSeat = '$OrderSeatGiven', fkPlayerSeat = (SELECT idPlayer FROM poker.player WHERE PseudoPlayer = '$Pseudo') WHERE idSeat = '$idSeat'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        header('Location: table.php'); //Refresh the page for prevent errors about undifined variable 
    }
    else //There is no free seats, or the game has started
    {
        $_SESSION['Error'] = 1; //Gives authorization to show an error, and tell to the user the table is full
        header('Location: home.php'); //The user is redirected to the home
    }
}

if($InfoStatus->rowCount() > 0) //Check if informations about status were returned
{
    $InfoStatu = $InfoStatus->fetch();
    extract($InfoStatu); //$fkStatusSeat
    
    //Switch for design wich action can the player do, the $fkStatusSeat comes from the $InfoPlayers request.
    switch ($fkStatusSeat){
        case 3: //Player can play
            echo "<div class='BettingButtons'><form method='post' id='BettingForm'></form>
            <button type='submit' form='BettingForm' name='Call'>Suivre</button>
            <button type='submit' form='BettingForm' name='AllIn'>Tapis</button>
            <button type='submit' form='BettingForm' name='Drop'>Se coucher</button>
            <br><form method='post' id='BettingForm2'></form>
            <input type='text' name='AmountRaised' value='Pas fonctionnel' required></input>
            <button type='submit' form='BettingForm2' name='Raise'>Relancer</button></div>";
            break;
        case 5:
            echo "<div class='ErrorMsg'>Attendez la prochain main</div>";
            break;
    }
}

$PersonnalView = $NbTotalSeats - $OrderSeat; //The number total of seats less the seat where I am, is the number of times I've to change place to be at first place. $OrderSeat comes from $InfoPlayers

if($WhoPlays->rowCount() > 0)//Check if there is some one playing
{
    $WhoPlay = $WhoPlays->fetch();
    extract($WhoPlay); //$ShowWhoPlays
    
    $ShowWhoPlays = ($ShowWhoPlays + $PersonnalView)%$NbTotalSeats; //Show for every one, who is the player playing
    echo "<div class='SeatPlaying$ShowWhoPlays'></div>"; //Show the player playing
    echo $ShowWhoPlays;
}

if($ShowPlayers->rowCount() > 0) //Check if there is players to show
{
    foreach($ShowPlayers as $ShowPlayer)
    {
        $ShowPseudoPlayer = $ShowPlayer['PseudoPlayer'];
        $ShowMoneySeat = $ShowPlayer['MoneySeat'];
        $ShowBetSeat = $ShowPlayer['BetSeat'];
        $ShowHandSeat = $ShowPlayer['HandSeat']; //NOT ALREADY USED. CREATE DIVS
        $ShowOrderSeat = $ShowPlayer['OrderSeat'];
        $ShowfkPlayerSeat = $ShowPlayer['fkPlayerSeat'];
        
        $PotValue = @$PotValue + $ShowBetSeat; //For know the value of the pot, we need to know the money beted by each player
        
        $ShowMoneySeat = number_format ($ShowMoneySeat, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Number format, for distinguish easier the thousands
        $ShowBetSeat = number_format ($ShowBetSeat, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Number format, for distinguish easier the thousands
        $ShowOrderSeat = ($ShowOrderSeat + $PersonnalView)%$NbTotalSeats; //Make the player go to the first place. %$NbTotalSeats do the number come back at 0 when he is at the end of the last place of the table
        
        echo "<div class='SeatPlayer$ShowOrderSeat'>$ShowPseudoPlayer<br>$ShowMoneySeat
            <form method='post' id='EliminateForm'></form>
            <button type='submit' form='EliminateForm' name='Eliminate' value='$ShowfkPlayerSeat'>Eliminer</button>
        </div>"; //Show the players
        
        echo "<div class='BetPlayer$ShowOrderSeat'>$ShowBetSeat</div>"; //Show the bet of each player
    }
}

if($ShowDealers->rowCount() > 0) //Check if there is a dealer to show
{
    $ShowDealer = $ShowDealers->fetch();
    extract($ShowDealer); //$BlindGame, $DealerGame
    
    $BetSmallBlind = $BlindGame/2; //Select the amount of a small blind. $BlindGame comes from the query $ShowDealers
    $BetBigBlind = $BlindGame; //Select the amount of a big blind. $BlindGame comes from the query $ShowDealers
    $WhereIsTheSmallBlind = ($DealerGame + 1)%$NbTotalSeats; //Select the place of the small blind
    $WhereIsTheBigBlind = ($DealerGame + 2)%$NbTotalSeats; //Select the place of the big blind
    $PlayerUnderTheGun = ($DealerGame + 3)%$NbTotalSeats; //Select the place of the first player who plays
        
    $ShowDealerGame = ($DealerGame + $PersonnalView)%$NbTotalSeats; //Add to the dealer, the number of rotations needed for keep the right vue, and do modulo who corresponds with the number of seats
    $ShowSmallBlindGame = ($DealerGame + $PersonnalView + 1)%$NbTotalSeats;
    $ShowBigBlindGame = ($DealerGame + $PersonnalView + 2)%$NbTotalSeats;
    
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
    
    //Update the status of one player, for get the bet buttons
    $query = "UPDATE poker.seat SET fkStatusSeat = '3' WHERE OrderSeat = '$PlayerUnderTheGun'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    header('Location: table.php'); //Refresh the page, for show immediatly the bet
}

if($EliminatePlayers->rowCount() > 0) //Check if a user is eliminated
{
    $EliminatePlayer = $EliminatePlayers->fetch();
    extract($EliminatePlayer); //$OrderEliminatedPlayer
    
    if($OrderEliminatedPlayer == $OrderSeat) //Check if I am the player eliminated
    {
        $_SESSION['Error'] = 2; //Gives authorization to show an error, and tell to the user he is eliminated
        $_POST['Getup'] = 1; //Gives a value to the post getup, for enter on the isset
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
    
    if($NbFreeSeats >= 4) //The game has ended
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
    $DealerGame++; //The next player will be the dealer
    $WhereIsTheSmallBlind = $WhereIsTheSmallBlind + 1 %$NbTotalSeats; //Small blind + 1, is the place where the small blind will be next turn
    $WhereIsTheBigBlind = $WhereIsTheBigBlind + 1 %$NbTotalSeats; //Big blind + 1, is the place where the big blind will be next turn
    
    //Update the player who is the dealer, after a new hand
    $query = "UPDATE poker.game SET DealerGame = '$DealerGame'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Put the status on bet a blind
    $query = "UPDATE poker.seat SET fkStatusSeat = '4' WHERE OrderSeat = '$WhereIsTheSmallBlind'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Put the status on bet a blind
    $query = "UPDATE poker.seat SET fkStatusSeat = '4' WHERE OrderSeat = '$WhereIsTheBigBlind'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    if($PotValue != 0) //When there is a new hand, the money goes to the pot. In a real game the money is won by the players before a new hand. The bet of the players goes to 0.
    {
        //Increment the pot 
        $query = "UPDATE poker.game SET PotGame = PotGame+'$PotValue'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        //All the bets goes to 0, because it's a new hand
        $query = "UPDATE poker.seat SET BetSeat = '0' WHERE fkGameSeat = '1'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
            
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

if(isset($_POST['Call'])) //If the users call, he check the money of the player before him, less his money, put the difference and next he tell to the next player he can play
{        
    $BeforeBet = $BeforeBets->fetch();
    extract($BeforeBet); //$BetBeforeMe
    
    $MoneyForCall = $BetBeforeMe - $BetSeat; //How many is needed for call
    
    //Takes the money needed for the call
    $query = "UPDATE poker.seat SET MoneySeat = MoneySeat-'$MoneyForCall', BetSeat = BetSeat+'$MoneyForCall', fkStatusSeat = '2' WHERE OrderSeat = '$OrderSeat'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    $_POST['NextPlayerCanPlay'] = 1; //Gives a value to the post NextPlayerCanPlay, for enter on the isset
}

if(isset($_POST['AllIn'])) //If the users all in, he puts all the money on the table and wait the next hand, he tell to the next player he can play
{
    //Takes the money needed for the all in and wait the next hand
    $query = "UPDATE poker.seat SET MoneySeat = '0', BetSeat = '$BrutMoneySeat', fkStatusSeat = '5' WHERE OrderSeat = '$OrderSeat'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    $_POST['NextPlayerCanPlay'] = 1; //Gives a value to the post NextPlayerCanPlay, for enter on the isset
}

if(isset($_POST['Drop'])) //If the users drop, he gives his cards, wait the next hand and he tell to the next player he can play
{
    //The player give everything he has, and wait the next hand
    $query = "UPDATE poker.seat SET BetSeat = '0', fkStatusSeat = '5' WHERE OrderSeat = '$OrderSeat'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    $_POST['NextPlayerCanPlay'] = 1; //Gives a value to the post NextPlayerCanPlay, for enter on the isset
}

if(isset($_POST['Raise'])) //The raise is not used for the moment, the refresh don't let the time to input money and next send the form. It can be used after adding ajax
{
    
}

if(isset($_POST['NextPlayerCanPlay']))
{
    //Select the player after me, who can play
    $query = "SELECT OrderSeat as PlayerAfterMe FROM poker.seat WHERE OrderSeat > '$OrderSeat' AND fkStatusSeat = '2' GROUP BY OrderSeat ASC LIMIT 1";
    $PlayingAfterMePlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    if($PlayingAfterMePlayers->rowCount() > 0) //Check if there is a player after me
    {
        $PlayingAfterMePlayer = $PlayingAfterMePlayers->fetch();
        extract($PlayingAfterMePlayer); //$PlayerAfterMe
    }
    else 
    {
        //Select the player after me, but in starting by 0
        $query = "SELECT OrderSeat as PlayerAfterMe FROM poker.seat WHERE OrderSeat < '$OrderSeat' AND fkStatusSeat = '2' GROUP BY OrderSeat ASC LIMIT 1";
        $PlayingAfterMePlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        $PlayingAfterMePlayer = $PlayingAfterMePlayers->fetch();
        extract($PlayingAfterMePlayer); //$PlayerAfterMe
    }
    
    //Tell to the next player, he can play
    $query = "UPDATE poker.seat SET fkStatusSeat = '3' WHERE OrderSeat = '$PlayerAfterMe'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    header('Location: table.php'); //Prevent to send the form in a loop
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
        <div class='Pot'><?php echo "$PotGame";?></div>
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
    <?php if($fkStatusSeat != 3){ ?>
        <script>setInterval(function(){location.reload()},3000);</script> <!-- //Refresh the page. Code gived by my projet manager --> 
    <?php } ?>
</html>

<?php
//----------------------------- Saving SESSION --------------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>