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

//Takes information about the tables
$query = "SELECT idGame, PotGame, BoardGame, BlindGame, DealerGame, HourStartGame, (SELECT ValueInt FROM poker.settings WHERE NameSettings = 'TimeToIncreaseBlind') as TimeToIncreaseBlind FROM poker.game";
$InfoTables = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

//----------------------------- PHP  ------------------------------------------------------

if($InfoTables->rowCount() > 0) //Check if informations about the tables were returned
{
    foreach($InfoTables as $InfoTable)
    {
        $NbTableGame = $InfoTable['idGame']; //Take the number of the game
        $GameNB = 'Game'.$NbTableGame; //Create a variable, for get the name of the array to build
        $TableNB = 'Table'.$NbTableGame; //Create a variable, for get the name of the array to build
            
            
        $$TableNB = array( //Create the array with informations about the table. $$ Uses the value of the variable as variable
        array('PotGame' => $InfoTable['PotGame'], 'BoardGame' => $InfoTable['BoardGame'], 'BlindGame' => $InfoTable['BlindGame'], 'DealerGame' => $InfoTable['DealerGame'], 'HourStartGame' => $InfoTable['HourStartGame'], 'TimeToIncreaseBlind' => $InfoTable['TimeToIncreaseBlind']));
        
        //Takes all the informations of all users
        $query = "SELECT PseudoPlayer, MoneySeat, BetSeat, HandSeat, OrderSeat, fkGameSeat, DescriptionStatus FROM poker.seat INNER JOIN poker.player ON fkPlayerSeat = idPlayer INNER JOIN poker.status ON fkStatusSeat = idStatus WHERE fkGameSeat = '$NbTableGame'";
        $InfoPlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
        $$GameNB = array(); //Create the array with informations about the users. $$ Uses the value of the variable as variable
        
        foreach($InfoPlayers as $InfoPlayer)
        {
            //Add informationns about the users in the array. 
            array_push($$GameNB, array('PseudoPlayer' => $InfoPlayer['PseudoPlayer'], 'MoneySeat' => $InfoPlayer['MoneySeat'], 'BetSeat' => $InfoPlayer['BetSeat'], 'HandSeat' => $InfoPlayer['HandSeat'], 'OrderSeat' => $InfoPlayer['OrderSeat'], 'fkGameSeat' => $InfoPlayer['fkGameSeat'], 'DescriptionStatus' => $InfoPlayer['DescriptionStatus']));
        }
    }
}
?>


<!DOCTYPE html>
<html>
    <head>
        <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 25%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
        </style>
    </head>
    <body>
        <?php
        
        for($i = 1; $i <= $InfoTables->rowCount(); $i++) //For every table counted, build an array
        {
            $GameNB = 'Game'.$i; //Create a variable, for get the name of the array to show
            $TableNB = 'Table'.$i; //Create a variable, for get the name of the array to show
            
            // Show games
            echo "<h2>Game $i</h2>
            <table border='1'>
                <tr>
                   <td>PseudoPlayer</td>
                   <td>MoneySeat</td>
                   <td>BetSeat</td>
                   <td>HandSeat</td>
                   <td>OrderSeat</td>
                   <td>fkGameSeat</td>
                   <td>DescriptionStatus</td>
               </tr>";

            foreach($$GameNB as $GameShow) //Lecture de chaque ligne du tableau
            { 
                echo "<tr>";
                foreach($GameShow as $cle=>$valeur){ //Lecture de chaque tableau de chaque ligne
                    echo "<td>".$valeur."</td>"; //echo $cle.': '.$valeur.'&nbsp&nbsp&nbsp&nbsp&nbsp';  //Affichage
                }
                echo "</tr>";
            }
            echo "</table>";

            // Show tables
            echo "<h2>Table $i</h2>
            <table border='1'>
                <tr>
                   <td>PotGame</td>
                   <td>BoardGame</td>
                   <td>BlindGame</td>
                   <td>DealerGame</td>
                   <td>HourStartGame</td>
                   <td>TimeToIncreaseBlind</td>
               </tr>";

            foreach($$TableNB as $TableShow) //Lecture de chaque ligne du tableau
            { 
                echo "<tr>";
                foreach($TableShow as $cle=>$valeur){ //Lecture de chaque tableau de chaque ligne
                    echo "<td>".$valeur."</td>"; //echo $cle.': '.$valeur.'&nbsp&nbsp&nbsp&nbsp&nbsp';  //Affichage
                }
                echo "</tr>";
            }
            echo "</table><br><br>";
        }
        ?>
    </body>
</html>


<?php
//----------------------------- Saving SESSION --------------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>