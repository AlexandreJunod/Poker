<!--====================================================================================================================
||  Author : Junod Alexandre                                                                                           ||
||  Creation : 16.02.2018                                                                                              ||
||  Summary : The player can create his account or go to the page index.php for login                                  ||
||====================================================================================================================-->


<?php
//----------------------------- Start SESSION ----------------------------------------

session_start();
require_once("includes/functions.php");
ConnectDB();

//----------------------------- Processing POST ------------------------------------------

if(isset($_POST['PseudoForm'])) //Check if datas were received by the form
{
    $Pseudo = $_POST['PseudoForm']; //Pseudo gived by the user who tries to sign up
    $Password = $_POST['PasswordForm']; //Password gived by the user who tries to sign up
    
    //Chearch the password in the database
    $query = "SELECT idPlayer, PseudoPlayer, PasswordPlayer FROM poker.player WHERE PseudoPlayer = '$Pseudo'";
    $signups = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    //Create the account of the user
    $query2 = "INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('$Pseudo', PASSWORD('$Password'))";
    
    if($signups->rowCount() > 0) //If no datas are returned, the pseudo exists yet. The user can't take this pseudo
    {
        echo "<div class='ErrorMsg'>Ce pseudo existe déjà</div>";
    }
    else //No Datas were returned, the pseudo doesn't exists. He can créate his account
    {
        if (preg_match("#[^a-zA-Z0-9]#", $Password)) //Check if the password matches with the required criterias
        { 
            $dbh->query($query2) or die ("SQL Error in:<br> $query2 <br>Error message:".$dbh->errorInfo()[2]); //execute the request of create account on the database
            $_SESSION['Pseudo'] = $Pseudo; //Save the pseudo of the user in a SESSION
            header('Location: home.php'); //The user is redirected to the home
        }
        else //The password doesn't matches with the required criterias
        {
            echo "<div class='ErrorMsg'>Le mot ne correspond pas aux critères</div>";
        }
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
    <body>
        <div class="FormContainerSignup">
            <div class="FormTitle">Inscription</div>
            <div class="FormDesign">
                <div class="FormFieldsSignup"><form method="post" id="FormSignup">Pseudo<input type="text" id="InputSignup" name="PseudoForm" minlength="6" maxlength="13" required autofocus><br><br><br>Mot de passe<input type="password" id="InputSignup" name="PasswordForm" minlength="6" required></form><br></div>
                <div class="FormCritereaSignup"><br>Doit contenir 6 à 14 caractères<br><br><br> Doit contenir :<br>&nbsp;&nbsp;- 6 caractères ou +<br>&nbsp;&nbsp;- 1 caractère spécial</div>
                <div class="FormButton"><button type="submit" form="FormSignup" name="Signup">Inscription</button></div>
            </div>
            <div class="FormLink"><a href="index.php">Déjà un compte ? Connectez-vous !</a></div>
        </div>
    </body>
</html>