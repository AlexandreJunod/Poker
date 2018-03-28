<!--====================================================================================================================
||  Author : Junod Alexandre                                                                                           ||
||  Creation : 16.02.2018                                                                                              ||
||  Summary : First page displayed to the user. The player can connect with his account or go to the page signup       ||
||====================================================================================================================-->

<?php
//----------------------------- Start SESSION --------------------------------------------

session_start();
require_once("includes/functions.php");
ConnectDB();

//----------------------------- Processing POST ------------------------------------------

if(isset($_POST['PseudoForm'])) //Check if datas were received by the form
{
    $Pseudo = $_POST['PseudoForm']; //Pseudo gived by the user who tries to log in
    $Password = $_POST['PasswordForm']; //Password gived by the user who tries to log in
    
    //Takes the hash password of the pseudo gived by the user
    $query = "SELECT idPlayer, PseudoPlayer, PasswordPlayer, PASSWORD('$Password') as HashPassword FROM poker.player WHERE PseudoPlayer = '$Pseudo'";
    $Logins = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
    if($Logins->rowCount() > 0) //If datas are returned, the pseudo exists
    {
        $Login = $Logins->fetch();
        extract($Login); //$idPlayer, $PseudoPlayer, $PasswordPlayer, $HashPassword
        
        if($PasswordPlayer == $HashPassword) //Check if the password gived by the user is the same than the password hashed of the data base
        {
            $_SESSION['Pseudo'] = $Pseudo; //Save the pseudo of the user in a SESSION
            header('Location: table.php'); //The user is redirected to a table
        }        
        else
        {
            echo "<div class='ErrorMsg'>Le mot de passe est erroné</div>";
        }
    } 
    else //No datas were returned, the pseudo was not find
    {
        echo "<div class='ErrorMsg'>Le pseudo est erroné</div>";
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
        <div class="FormContainerIndex">
            <div class="FormTitle">Connexion</div>
            <div class="FormDesign">
                <div class="FormFieldsIndex"><form method="post" id="FormLogin">Pseudo<input type="text" id="InputIndex" name="PseudoForm" minlength="6" maxlength="13" required autofocus><br><br><br>Mot de passe<input type="password" id="InputIndex" name="PasswordForm" minlength="6" required></form><br></div>
                <div class="FormButton"><button type="submit" form="FormLogin" name="Login">Connexion</button></div>
            </div>
            <div class="FormLink"><a href="signup.php">Pas encore de compte ? Inscrivez-vous !</a></div>
        </div>
    </body>
</html>