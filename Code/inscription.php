<!--====================================================================================================================
||  Auteur : Junod Alexandre                                                                                           ||
||  Derniere modification : 06.03.2018                                                                                 ||
||  Résumé : Page d'inscription, le joueur peut s'inscrire où aller sur la page de connexion                           ||
||====================================================================================================================-->

<?php
//----------------------------- Démarrage SESSION ----------------------------------------

session_start();
require_once("includes/fonctions.php");
ConnectDB();

//----------------------------- Traitement SESSION ---------------------------------------

//extract($_SESSION);

//----------------------------- Traitement POST ------------------------------------------

if(isset($_POST['PseudoForm']))
{
    $Pseudo = $_POST['PseudoForm'];
    $MotDePasse = $_POST['MotDePasseForm'];
    
    if (preg_match("#[^a-zA-Z0-9]#", $MotDePasse))
    { 
        if (preg_match("#[A-Z]{1,}#", $MotDePasse))
        {
            $query = "SELECT idJoueur, PseudoJoueur, MotDePasseJoueur FROM poker.joueur WHERE PseudoJoueur = '$Pseudo'";
            $connexions = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

            //Créer le compte de l'utilisateur. Remplis les fk de la table joueur, afin de savoir quel est l'état du joueur et la page dans la quelle il se trouve
            $query2 = "INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur, fkEtatJoueur, fkPageJoueur) VALUES ('$Pseudo', PASSWORD('$MotDePasse'), '1', '1')";

            if($connexions->rowCount() > 0)
            {
                echo "<div class='ErrorMsg'>Ce pseudo existe déjà</div>";
            }
            else
            {
                $dbh->query($query2) or die ("SQL Error in:<br> $query2 <br>Error message:".$dbh->errorInfo()[2]);
                $_SESSION['Pseudo'] = $Pseudo;
                header('Location: accueil.php');
            }
        }
        {
            echo "<div class='ErrorMsg'>Le mot de passe n'a pas de majuscule</div>";
        }
    }
    else
    {
        echo "<div class='ErrorMsg'>Le mot de passe n'a pas de caractère spécial</div>";
    }
}

//----------------------------- Traitement GET -------------------------------------------

// QUE DU PHP JUSQU'ICI
//----------------------------- Génération de la page-------------------------------------
// HTML + PHP depuis ici

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="includes/style.css"/>
        <title><?php echo $TitleTab; ?></title>
    </head>
    <body>
        
        <div class="FormContainerInscription">
            <div class="FormTitle">Inscription</div>
            <div class="FormDesign">
                <div class="FormFieldsInscription"><form method="post" id="FormInscription">Pseudo<input type="text" id="InputInscription" name="PseudoForm" minlength="6" maxlength="13" required autofocus><br><br><br>Mot de passe<input type="password" id="InputInscription" name="MotDePasseForm" minlength="6" required></form><br></div>
                <div class="FormContraintesInscription"><br>Doit contenir 6 caractères ou +<br><br><br> Doit contenir :<br>&nbsp;&nbsp;- 6 caractères ou +<br>&nbsp;&nbsp;- 1 majuscule<br>&nbsp;&nbsp;- 1 caractère spécial</div>
                <div class="FormButton"><button type="submit" form="FormInscription" name="Inscription">Inscription</button></div>
            </div>
            <div class="FormLink"><a href="index.php">Déjà un compte ? Connectez-vous !</a></div>
        </div>
    </body>
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------
//$_SESSION[''] = $;
?>