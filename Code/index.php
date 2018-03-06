<!--====================================================================================================================
||  Auteur : Junod Alexandre                                                                                           ||
||  Derniere modification : 06.03.2018                                                                                 ||
||  Résumé : Page de connexion, le joueur peut se connecter a l'aide de son compte où aller sur la page d'inscription  ||
||====================================================================================================================-->

<?php
//----------------------------- Démarrage SESSION ----------------------------------------

session_start();
require_once("includes/fonctions.php");
ConnectDB();

//----------------------------- Traitement POST ------------------------------------------

if(isset($_POST['PseudoForm'])) //Données reçues par le formulaire rempli par l'utilisateur
{
    $Pseudo = $_POST['PseudoForm']; // Contient le pseudo rentré par l'utilisateur
    $MotDePasse = $_POST['MotDePasseForm']; // Contient le mot de passe rentré par l'utilisateur
    
    //Récupére le mot de passe haché pour le pseudo selectionné.
    $query = "SELECT idJoueur, PseudoJoueur, MotDePasseJoueur, PASSWORD('$MotDePasse') as HashPassword FROM poker.joueurs WHERE PseudoJoueur = '$Pseudo'";
    $connexions = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    if($connexions->rowCount() > 0) //Compte le nombre de colonnes reçues
    {
        $connexion = $connexions->fetch(); //fetch -> aller chercher
        extract($connexion); //$idJoueurs, $PseudoJoueurs, $MotDePasse, $HashPassword
        
        if($MotDePasseJoueur == $HashPassword) //Compare le mot de passe haché dans le base de donnée avec le mot de passe haché que l'utilisateur a entré
        {
            $_SESSION['Pseudo'] = $Pseudo;
            header('Location: table.php');
        }        
        else
        {
            echo "<div class='ErrorMsg'>Le mot de passe est erroné</div>";
        }
    } //Si aucune colonne n'est trouvée, le pseudo est faux 
    else
    {
        echo "<div class='ErrorMsg'>Le pseudo est erroné</div>";
    }
}

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
        <div class="FormContainerIndex">
            <div class="FormTitle">Poker online</div>
            <div class="FormDesign">
                <div class="FormFieldsIndex"><form method="post" id="FormConnexion">Pseudo<input type="text" id="InputIndex" name="PseudoForm" minlength="6" maxlength="13" required autofocus><br><br><br>Mot de passe<input type="password" id="InputIndex" name="MotDePasseForm" minlength="6" required></form><br></div>
                <div class="FormButton"><button type="submit" form="FormConnexion" name="Connexion">Connexion</button></div>
            </div>
            <div class="FormLink"><a href="inscription.php">Pas encore de compte ? Inscrivez-vous !</a></div>
        </div>
    </body>
</html>