<meta charset='utf-8'/>
<link rel="stylesheet" href="FontAwesome/css/font-awesome.min.css">
<?php
@session_start();

//Création d'une variable supergloable qui permet de changer le titre de l'onglet
$GLOBALS['TitleTab'] = 'Joutes - Intranet CPNV';

//Connection à base de données
function ConnectDB()
{
    // Toutes les infos nécessaires pour la connexion à une base de donnée
    $hostname = 'localhost';
    $dbname = 'joutes';
    $username = 'root';
    $password = '';

    // PDO = Persistant Data Object
    // Entre "" = Connection String
    $connectionString = "mysql:host=$hostname; dbname=$dbname";

    global $dbh; 

    try
    {
        $dbh = new PDO($connectionString, $username, $password);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->exec("SET NAMES UTF8");
    }
    catch(PDOException $e)
    {
        die("Erreur de connexion au serveur (".$e->getMessage().")");
    }
}

if(isset($_POST['deconnexion']))
{
    unset($_SESSION['idConnexion']);
    unset($_SESSION['UserName']);
}

if(isset($_GET['deconnexion']))
{
    unset($_SESSION['idConnexion']);
    unset($_SESSION['UserName']);
    header('Location: index.php');
}

function LoginBox()
{
    global $dbh;
    extract($_SESSION);

    $res = '';

    $res .= "<div id='intranet_content' class='content-without-sidemenu content-without-sidebar'>

                        <form accept-encoding='UTF-8' class='simple-box boxed formtastic user_session' method='post'><div style='margin:0;padding:0;display:inline'><input name='_utf8' type='hidden' value='&#9731;' /><input name='authenticity_token' type='hidden' value='BqmorgZKPUXY/9ST1iuIAL2ZfcKUsMmBnpS2IPmJ3SM=' /></div>

                            <fieldset class='inputs'>
                                <ol>
                                    <li class='string required' id='user_session_email_input'>
                                        <label for='user_session_email'>
                                            Identifiant<abbr title='required'>*</abbr>
                                        </label>
                                        <input type='text' name='nom'/>
                                    </li>
                                    <li class='password required' id='user_session_password_input'>
                                        <label for='user_session_password'>
                                            Mot de passe<abbr title='required'>*</abbr>
                                        </label><input type='password' name='password'/>
                                    </li>                                    
                                </ol>
                            </fieldset>
                            <fieldset class='buttons'>
                                <ol>
                                    <li class='commit'><input type='submit' name='connexion' value='Connexion'></button></li>
                                </ol>
                            </fieldset>
                        </form>
                    </div>";    
    return $res;    
}

function LoggedBox()
{
    echo "<div id='intranet_header'>
        <div class='intranet-inner'>
            <div id='intranet_logo'>
                <a href='/'><span>Intranet CPNV</span></a>
            </div>

            <div id='intranet_topforms'>
                <form accept-encoding='UTF-8' action='/preferences' id='intranet_topforms_context' method='post'><div style='margin:0;padding:0;display:inline'><input name='_utf8' type='hidden' value='&#9731;' /><input name='_method' type='hidden' value='put' /><input name='authenticity_token' type='hidden' value='e4VBO5Y7WVHTPjCnYFWjaDwUkKMUE5iF2lloKFY25ek=' /></div>
                    <label for='person_intranet_prefs_current_sections'>Contexte</label>
                    <select id='person_intranet_prefs_current_sections' name='person[intranet_prefs_current_sections]' onchange='this.form.submit();'><option value='' selected='selected'>Mon contexte (CFC, EMSC, Filière Info)</option><option value='cpnv' selected='selected'>CPNV</option><optgroup label='Sites'><option value='cr'>César-Roux</option>
                        <option value='payerne'>CPNV Payerne</option>
                        <option value='sainte-croix'>CPNV Sainte-Croix</option>
                        <option value='yverdon'>CPNV Yverdon</option>
                        <option value='morges'>Morges</option>
                        <option value='saint-loup'>Saint-Loup</option></optgroup><optgroup label='Ecoles'><option value='em'>EM</option>
                        <option value='emsc'>EMSC</option>
                        <option value='emy'>EMY</option>
                        <option value='epai'>EPAI</option>
                        <option value='epc'>EPC</option>
                        <option value='epcb'>EPCB</option>
                        <option value='epcy'>EPCY</option>
                        <option value='epsy'>EPSY</option>
                        <option value='essc'>ESSC</option>
                        <option value='esy'>ESY</option>
                        <option value='log-cr'>LOG-César-Roux</option></optgroup><optgroup label='Filières'><option value='auto'>Filière Automatique</option>
                        <option value='bois'>Filière Bois</option>
                        <option value='sante'>Filière de la Santé</option>
                        <option value='technique'>Filière de la Technique</option>
                        <option value='vente'>Filière de la Vente</option>
                        <option value='commerce'>Filière du Commerce</option>
                        <option value='social'>Filière du Social</option>
                        <option value='elon'>Filière Electronique</option>
                        <option value='info'>Filière Info</option>
                        <option value='logistique'>Filière Logistique</option>
                        <option value='matu'>Filière Matu Pro</option>
                        <option value='meca'>Filière Méca</option>
                        <option value='media'>Filière Média</option></optgroup></select>
                </form>


                <form accept-encoding='UTF-8' action='/rechercher' id='intranet_topforms_search' method='get'><div style='margin:0;padding:0;display:inline'><input name='_utf8' type='hidden' value='&#9731;' /></div>
                    <input id='q' name='q' type='text' />
                    <button class=' submit' id='search_submit' type='submit' value='Rechercher'><span>Rechercher</span></button>
                </form>
            </div>

            <div id='intranet_user'>";

    if(isset($_SESSION["idConnexion"]))
    {
        $UserName = $_SESSION["UserName"];
        echo "$UserName";
        echo "<a href='indextest.php?deconnexion'>Déconnexion</a>";
    }
    else
    {
        echo "<a href='index.php'>Connexion</a>";
    }

    echo "</div>
            <ul id='intranet_topmenu'>
                <li><a href='indextest.php' id='intranet_topmenu_information'>Équipes</a></li>                         
            </ul>


        </div>";

    
    echo "</div>";
}

function AddActivities()
{
    global $dbh;
    extract($_POST);
    
    if(isset($_POST['appliquer']))
    {
        $FlagModif = 0;

        $query = "UPDATE activity SET NameActivity = '$NameActivity', PlaceActivity = '$PlaceActivity', MinParticipantActivity = $MinParticipantActivity, MaxParticipantActivity = $MaxParticipantActivity, TypeSubscribeActivity = $TypeSubscribeActivity, MinTeamSizeActivity = $MinTeamSizeActivity, MaxTeamSizeActivity = $MaxTeamSizeActivity, MedicalCertificatActivity = $MedicalCertificatActivity WHERE idActivity = $appliquer;"; 
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    }

    if(isset($_POST['insert']))
    {        
        $query = "INSERT INTO activity (NameActivity, PlaceActivity, MinParticipantActivity, MaxParticipantActivity, TypeSubscribeActivity, MinTeamSizeActivity, MaxTeamSizeActivity, MedicalCertificatActivity) VALUES ('$NameActivity', '$PlaceActivity', '$MinParticipantActivity', '$MaxParticipantActivity', '$TypeSubscribeActivity', '$MinTeamSizeActivity', '$MaxTeamSizeActivity', '$MedicalCertificatActivity')"; 
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    if(isset($_POST['delete']))
    {
        $delete = $_POST['delete'];
        $query = "DELETE FROM activity WHERE idActivity = $delete";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    }

    // Affiche les activités existantes
    $query = "SELECT idActivity, NameActivity, PlaceActivity, MinParticipantActivity, MaxParticipantActivity, StartTimeSlot, MinTeamSizeActivity, MaxTeamSizeActivity, MedicalCertificatActivity
    FROM activity
    INNER JOIN timeslot ON TypeSubscribeActivity = idTimeSlot;";  
    $activities = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    // Affiche le moment de l'activité
    $query = "SELECT idTimeSlot, StartTimeSlot FROM timeslot;";
    $ActivitiesTime = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    // Affiche le moment de l'activité, en doublon pour éviter un bug lorsqu'un prof est en modification
    $query = "SELECT idTimeSlot, StartTimeSlot FROM timeslot;";
    $ActivitiesTime2 = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    echo "<div id='Left100'><br><br><h2>Ajouter, modifier ou supprimer une activité</h2><br>
    <table>
        <tr>
            <td>
                Activité
            </td>
            <td>
                Lieu de l'activité                            
            </td>
            <td>
                Équipes minimum           
            </td>
            <td>
                Équipes maximum
            </td>
            <td>
                Moment de l'activité
            </td>
            <td>
                Taille équipe minimum
            </td>
            <td>
                Taille équipe maximum
            </td>
            <td>
                Certificat requis
            </td>
        </tr>";

        while($activity = $activities->fetch()) //fetch = aller chercher
        {
            extract($activity); // $idActivity, $NameActivity, $PlaceActivity, $MinParticipantActivity, $MaxParticipantActivity, $StartTimeSlot, $MinTeamSizeActivity, $MaxTeamSizeActivity, $MedicalCertificatActivity

            if(isset($_POST['modifier']))
            { 
                $modifier = $_POST['modifier'];

                if($modifier == $idActivity)
                {
                    $FlagModif = 1;
                    echo "<tr>
                            <form method='post'> 
                            <td>
                                <input type='text' name='NameActivity' Value='$NameActivity'/>
                            </td>
                            <td>
                                <input type='text' name='PlaceActivity' Value='$PlaceActivity'/>                            
                            </td>
                            <td>
                                <input type='text' name='MinParticipantActivity' Value='$MinParticipantActivity'/>            
                            </td>
                            <td>
                                <input type='text' name='MaxParticipantActivity' Value='$MaxParticipantActivity'/>
                            </td>
                            <td>
                                <select name='TypeSubscribeActivity'>";
                                while($ActivityTime = $ActivitiesTime->fetch())
                                {
                                    extract($ActivityTime); // $idTimeSlot, $StartTimeSlot
                                    echo "<option value='$idTimeSlot'>$StartTimeSlot</option>";
                                }
                                echo "</select>
                            </td>
                            <td>
                                <input type='text' name='MinTeamSizeActivity' Value='$MinTeamSizeActivity'/>
                            </td>
                            <td>
                                <input type='text' name='MaxTeamSizeActivity' Value='$MaxTeamSizeActivity'/>
                            </td>
                            <td>
                                <select name='MedicalCertificatActivity'>
                                    <option value='0'>Non</option>
                                    <option value='1'>Oui</option>
                                </select>
                            </td>
                            <td>
                                <button name='appliquer' value='$idActivity'>Appliquer</button>
                            </td>
                            </form>
                        </tr>";
                }
                else
                {
                    echo "<tr>
                        <form method='post'> 
                        <td>
                            $NameActivity
                        </td>
                        <td>
                            $PlaceActivity
                        </td>
                        <td>
                            $MinParticipantActivity        
                        </td>
                        <td>
                            $MaxParticipantActivity
                        </td>
                        <td>
                            $StartTimeSlot
                        </td>
                        <td>
                            $MinTeamSizeActivity
                        </td>
                        <td>
                            $MaxTeamSizeActivity
                        </td>
                        <td>";
                            if($MedicalCertificatActivity == 0)
                            {
                                echo "Non";
                            }
                            else
                            {
                                echo "Oui";
                            }
                        echo "</td>
                        <td>
                            <button name='modifier' value='$idActivity'>Modifier</button>
                            <button name='delete' value='$idActivity'>Delete</button>
                        </td>
                        </form>
                    </tr>";
                }                
            }
            else
            {
                if (@$FlagModif != 1)
                {
                    echo "<tr>
                        <form method='post'> 
                        <td>
                            $NameActivity
                        </td>
                        <td>
                            $PlaceActivity
                        </td>
                        <td>
                            $MinParticipantActivity        
                        </td>
                        <td>
                            $MaxParticipantActivity
                        </td>
                        <td>
                            $StartTimeSlot
                        </td>
                        <td>
                            $MinTeamSizeActivity
                        </td>
                        <td>
                            $MaxTeamSizeActivity
                        </td>
                        <td>";
                            if($MedicalCertificatActivity == 0)
                            {
                                echo "Non";
                            }
                            else
                            {
                                echo "Oui";
                            }
                        echo"</td>
                        <td>
                            <button name='modifier' value='$idActivity'>Modifier</button>
                            <button name='delete' value='$idActivity'>Delete</button>
                        </td>
                        </form>
                    </tr>";
                }
            }                
        }        

        echo "<tr>
            <form method='post'> 
            <td>
                <input type='text' name='NameActivity'>
            </td>
            <td>
                <input type='text' name='PlaceActivity'>
            </td>
            <td>
                <input type='text' name='MinParticipantActivity'>                      
            </td>
            <td>
                <input type='text' name='MaxParticipantActivity'>                      
            </td>
            <td>
                <select name='TypeSubscribeActivity'>";
                while($ActivityTime2 = $ActivitiesTime2->fetch())
                {
                    extract($ActivityTime2); // $idTimeSlot, $StartTimeSlot
                    echo "<option value='$idTimeSlot'>$StartTimeSlot</option>";
                }
            echo "</select>                
            </td>
            <td>
                <input type='text' name='MinTeamSizeActivity'>                      
            </td>
            <td>
                <input type='text' name='MaxTeamSizeActivity'>                      
            </td>
            <td>
                <select name='MedicalCertificatActivity'>
                    <option value='0'>Non</option>
                    <option value='1'>Oui</option>
                </select>                
            </td>
            <td>
                <button name='insert'>Ajouter</button> 
            </td>
            </form>
        </tr>
    </table></div>";
}

function AcceptedTeam()
{
    global $dbh;
    extract($_SESSION);

    if(isset($_POST['valider']))
    {
        $valider = $_POST['valider'];

        $query = "UPDATE team SET AcceptedTeam='1' WHERE idTeam='$valider';"; 
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    }

    if(isset($_POST['refuser']))
    {
        $refuser = $_POST['refuser'];
        $query = "DELETE FROM team WHERE idTeam = '$refuser'";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    }

    /*$query = "SELECT idTeam, NameTeam, AcceptedTeam, NameActivity FROM team inner join activity on fkActivity = idActivity WHERE AcceptedTeam = 0;";*/
    $query = "SELECT idTeam, NameTeam, CONCAT(personLastName, ' ',personFirstName) AS User, NameActivity FROM team 
	inner join person on idPerson = fkPerson
	inner join activity on team.fkActivity = idActivity
    WHERE AcceptedTeam = '0';";
    $teams = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    echo "<div id='Left60'><h2>Valider ou refuser une équipe</h2><br>
    <table>
        <tr>
            <td>
                <b>Nom de l'équipe</b>
            </td>
            <td>
                <b>Capitaine de l'équipe</b>
            </td>
            <td>
                <b>Activité</b>
            </td>
            <td>
                <b>Action</b>
            </td>
          </tr>";
    while($team = $teams->fetch()) //fetch = aller chercher
    {
        extract($team); // $idTeam, $NameTeam, $User, $NameActivity

        echo "<tr>
                <form method='post'> 
                <td>
                    $NameTeam
                </td>
                <td>
                    $User          
                </td>
                <td>
                    $NameActivity   
                </td>
                <td>
                    <button name='valider' value='$idTeam'>Valider</button>
                    <button name='refuser' value='$idTeam'>Refuser</button>
                </td>
                </form>
            </tr>";    
    }
    echo "</table></div>";
    
    echo "<br><br><br><div id='Right34'><a href='Administration.php'><h1>Liste des élèves et envoyer des emails</h1></a></div>";
}

function Administration()
{
    global $dbh;
    extract($_POST);
    
    if(isset($delete))
    {    
        // Suppression de l'équipe dans la base de donnée
        $query = "DELETE FROM team WHERE idTeam = $delete";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    if(isset($AddCertif))
    {
        // Ajout du certificat pour l'élève dans la base de donnée
        $query = "UPDATE person SET MedicalCertificatPerson = '1' WHERE person.idPerson = '$AddCertif';"; 
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    if(isset($DeleteCertif))
    {
        // Suppression du certificat pour l'élève dans la base de donnée
        $query = "UPDATE person SET MedicalCertificatPerson = '0' WHERE person.idPerson = '$DeleteCertif';"; 
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    // Afficher les équipes inscrites et validées
    $query = "SELECT idTeam, NameTeam, CONCAT(personLastName, ' ', personFirstName) AS User, NameActivity
    FROM team
    INNER JOIN person ON fkPerson = idPerson
    INNER JOIN teamperson ON idTeam = fkTeam
    INNER JOIN activity ON team.fkActivity = idActivity
    WHERE AcceptedTeam = '1' 
    GROUP BY NameTeam;";
    $Teams = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    // Afficher les élèves avec/sans certificat médical
    $query = "SELECT idPerson, CONCAT(personLastName, ' ', personFirstName) AS User, MedicalCertificatPerson FROM person WHERE intranetUserid != 0 order by User asc;";
    $Certificats = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);    

    echo "<div id='Left60'><br><br><h2>Supprimer équipe</h2><br>";
        // Supprimer les équipes
        
    echo "<table>
        <tr>
            <td>
                Nom équipe
            </td>
            <td>
                Capitaine                           
            </td>
            <td>
                Activité     
            </td>
            <td>
                Action(s)
            </td>
        </tr>";
        while($Team = $Teams->fetch()) //fetch = aller chercher
        {
            extract($Team); // $idTeam, $NameTeam, $User, $NameActivity

            echo "<tr>
                <form method='post'> 
                <td>
                    $NameTeam
                </td>
                <td>
                    $User                       
                </td>
                <td>
                    $NameActivity       
                </td>
                <td>
                    <button name='delete' value='$idTeam'>Supprimer</button>
                </td>
            </tr>";
        }
        echo "</table></div>";

    echo "<div id='Left60'><br><br><h2>Ajouter/retirer un certificat médical</h2><br></div><div id='Left60Scroll'>";
        // Supprimer les équipes
        
        echo "<table>
            <tr>
                <td>
                    Élève
                </td>
                <td>
                    Certificat médical                           
                </td>
                <td>
                    Action(s)
                </td>
            </tr>";
        while($Certificat = $Certificats->fetch()) //fetch = aller chercher
        {
            extract($Certificat); // $idPerson, $User, $MedicalCertificatPerson

            echo "<tr>
                <form method='post'> 
                <td>
                    $User
                </td>";
            if($MedicalCertificatPerson == 0) // Une croix rouge pour ceux qui ne possèdent pas le certificat médical, un vue vert pour ceux qui en possèdent
            {
                echo"<td>
                    <i class='fa fa-times' style='color:red' aria-hidden='true'></i>
                </td>
                <td><button name='AddCertif' value='$idPerson'>Ajouter</button>
                </td>
            </tr>";
            }
            else
            {
                echo"<td>
                    <i class='fa fa-check' style='color:green' aria-hidden='true'></i>
                </td>
                <td><button name='DeleteCertif' value='$idPerson'>Retirer</button>
                </td>
            </tr>";
            }
        }
        echo "</table></div>";        
}

function AdminProf()
{
    global $dbh;
    extract($_SESSION);

    $query = "select idPerson, role from person where idPerson = $idConnexion;";
    $roles = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    if($roles->rowCount() > 0)
    {
        $role = $roles->fetch(); 
        extract($role); //$idPerson, $login, $MDP, $HashPassword
    }

    if($role != 0)
    {
        CreateTeam();
        echo "<fieldset id='liste_activite'>
            <legend> Administration </legend>";
        AcceptedTeam();
        Administration();
        AddActivities();
        echo "</fieldset>";
    }
    else 
    {
        CreateTeam();
    }
}

function AddPlayer()
{
    global $dbh;

    if(isset($_GET['team']))
    {
        $idTeamPerson = $_GET['team'];
    }

    if(isset($_GET['activity']))
    {
        $NameActivity = $_GET['activity'];
    }

    if(isset($_POST['add']))
    {
        $add = $_POST['add'];

        $query = "UPDATE teamperson SET AcceptedPerson = '1' WHERE idTeamPerson = $add";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    if(isset($_POST['delete']))
    {        
        $delete = $_POST['delete'];

        $query = "DELETE FROM teamperson WHERE idTeamPerson = $delete";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);    
    }

    /*$query = "SELECT idTeam, idPerson, (SELECT fkPerson FROM team  WHERE idTeam = $idTeamPerson) AS Capitaine, idTeamPerson, teamperson.fkPerson AS Player, AcceptedPerson, NameTeam, CONCAT(personLastName, ' ', personFirstName) AS NamePlayer, (SELECT NameActivity FROM activity WHERE idActivity = $NameActivity) as NameActivity FROM teamperson
                INNER JOIN person on idPerson = teamperson.fkPerson
                INNER JOIN team on idTeam = fkTeam
                WHERE AcceptedPerson = 0;"; */

    $query = "SELECT idTeam, idPerson, idTeamPerson, teamperson.fkPerson AS Player, AcceptedPerson, NameTeam, CONCAT(personLastName, ' ', personFirstName) AS NamePlayer, (SELECT NameActivity FROM activity WHERE idActivity = $NameActivity) as NameActivity FROM teamperson
                INNER JOIN person on idPerson = teamperson.fkPerson
                INNER JOIN team on idTeam = fkTeam
                WHERE AcceptedPerson = 0 and idTeam = $idTeamPerson;"; 

    $Teams = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 

    echo "<table>";
    echo "<tr>
        <td>
            Statut
        </td>
        <td>
            Joueurs                            
        </td>
        <td>
            Classe        
        </td>
        <td>
            Sport
        </td>
        <td>
            Actions
        </td>
    </tr>";

    while($Team = $Teams->fetch())
    {
        extract($Team); //$idTeamPerson, $AcceptedPerson, $NameTeam, $NamePlayer, $NameActivity

        echo "<tr>
            <td>
                <i class='fa fa-spinner' aria-hidden='true'>
            </td>
            <td>
                $NamePlayer                           
            </td>
            <td>

            </td>
            <td>
                $NameActivity
            </td>
            <td>
                <form method='post'>
                    <button name='add' value='$idTeamPerson'>Ajouter dans l'équipe</button>
                    <button name='delete' value='$idTeamPerson'>Refuser dans l'équipe</button>
                </form>
            </td>                
        </tr>";
    }

    echo "</table>";
}

function GererTeam()
{
    global $dbh;
    extract($_SESSION);

    if(isset($_GET['team']))
    {
        $idTeamPerson = $_GET['team'];
    }

    if(isset($_GET['activity']))
    {
        $NameActivity = $_GET['activity'];
    }

    if(isset($_POST['quitter']))
    {
        $quitter = $_POST['quitter'];
        // Suppression de l'équipe dans la base de donnée
        $query = "DELETE FROM teamperson WHERE idTeamPerson = $quitter;";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    if(isset($_POST['delete']))
    {    
        $delete = $_POST['delete'];
        // Suppression de l'équipe dans la base de donnée
        $query = "DELETE FROM team WHERE idTeam = $delete";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        header('Location: indextest.php');
    }

    if(isset($_POST['NewCap']))
    {    
        $NewCap = $_POST['NewCap'];
        // Suppression de l'équipe dans la base de donnée
        $query = "UPDATE team SET fkPerson = $NewCap";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    $query = "SELECT fkPerson FROM team;";
    $capitaines = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    if($capitaines->rowCount() > 0)
    {
        $capitaine = $capitaines->fetch(); 
        extract($capitaine); //$fkPerson
    }

    $query = "SELECT idTeam, idPerson, (SELECT fkPerson FROM team WHERE idTeam = $idTeamPerson) AS Capitaine, idTeamPerson, teamperson.fkPerson AS Player, AcceptedPerson, NameTeam, CONCAT(personLastName, ' ', personFirstName) AS NamePlayer, (SELECT NameActivity FROM activity WHERE idActivity = $NameActivity) as NameActivity FROM teamperson INNER JOIN person on idPerson = teamperson.fkPerson INNER JOIN team on fkTeam = idTeam WHERE AcceptedPerson = 1 and idTeam = $idTeamPerson;"; 

    $Teams = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 

    echo "<table>";
    echo "<tr>
                <td>
                    Rôle
                </td>
                <td>
                    Joueurs                            
                </td>
                <td>
                    Classe        
                </td>
                <td>
                    Équipe
                </td>
                <td>
                    Sport
                </td>
                <td>
                    Actions
                </td>
            </tr>";

    while($Team = $Teams->fetch())
    {
        extract($Team); //$idTeamPerson, $AcceptedPerson, $NameTeam, $NamePlayer, $NameActivity

        echo "<tr>
                <td>";
                    if($Capitaine == $Player)
                    {
                        echo "<i class='fa fa-star' aria-hidden='true'></i>";
                    }
                    else
                    {
                        echo "<i class='fa fa-user' aria-hidden='true'></i>";
                    }
        echo   "</td>
                <td>
                    $NamePlayer                           
                </td>
                <td>

                </td>
                <td>
                    $NameTeam
                </td>
                <td>
                    $NameActivity
                </td>";

        if($fkPerson == $idConnexion)
        {
            if($Capitaine == $Player)
            {
                echo "<td>
                        <form method='post'>
                            <button name='delete' value='$idTeam'>Dissoudre l'équipe</button>

                        </form>
                    </td>";
            }
            else
            {
                echo "<td>
                        <form method='post'>
                            <button name='NewCap' value='$idPerson'>Nouveau Capitaine</button>
                        </form>
                    </td>";
            }              
        }
        else 
        {
            if($idConnexion == $Player)
            {
                echo "<td>
                        <form method='post'>
                            <button name='quitter' value='$idTeamPerson'>Quitter l'équipe</button>
                        </form>
                    </td>";
            }
        }  
        echo "</tr>";
    }
    echo "</table>";    
}

function CreateTeam()
{
    global $dbh;
    // ---------------------------- Traitement (POST) ----------------------------------------

    extract($_POST); // $SubmitTeam, $ActivityChoice, $rejoindre
    extract($_SESSION); //idConnexion, $UserName

    if(isset($_POST['CancelTeam']))
    {
        $cancelTeam = $_POST['CancelTeam'];    
        $query = "DELETE FROM teamperson WHERE idTeamPerson='$cancelTeam';";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    }

    if(isset($_POST['CancelNameTeam']))
    {
        $cancelNameTeam = $_POST['CancelNameTeam'];    
        $query = "DELETE FROM team WHERE idTeam='$cancelNameTeam';";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]); 
    }

    if(isset($SubmitTeam))
    {    
        extract($_SESSION); // $UserName
        // Ajout de l'équipe dans la base de donnée
        $query = "INSERT INTO team (NameTeam,fkPerson,fkActivity) VALUES ('$TeamName', (SELECT idPerson FROM person WHERE CONCAT(personFirstName, '.', personLastName, '@cpnv.ch') = '$UserName'),(SELECT idActivity FROM activity WHERE NameActivity = '$ActivityChoice'));";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

        // Ajout du capitaine dans l'équipe
        $query = "INSERT INTO teamperson (AcceptedPerson,fkPerson,fkTeam) VALUES ('1', (SELECT idPerson FROM person WHERE CONCAT(personFirstName, '.', personLastName, '@cpnv.ch') = '$UserName'), (SELECT idTeam FROM team WHERE NameTeam = '$TeamName'));";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    if(isset($rejoindre))
    {
        extract($_SESSION); // $UserName
        $query = "INSERT INTO teamperson (fkPerson, fkTeam) VALUES ((SELECT idPerson FROM person WHERE CONCAT(personFirstName, '.', personLastName, '@cpnv.ch') = '$UserName'), '$rejoindre');";
        $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }

    // Résumé des activités
    $query = "SELECT idActivity, NameActivity, COUNT(fkActivity) AS SubscribedTeams, MaxParticipantActivity, MinParticipantActivity, AcceptedTeam 
    FROM activity
    INNER JOIN team ON idActivity = fkActivity
    WHERE AcceptedTeam = '1'
    GROUP BY NameActivity;";  
    $activities = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    // Liste des activités présente sur la base de donnée
    $query = "SELECT NameActivity FROM activity;";
    $Activites = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    // Liste des équipes
    $query = "SELECT idTeam, NameTeam, CONCAT(personLastName, ' ', personFirstName) AS User, COUNT(teamperson.fkPerson) AS NbPlayers, NameActivity, StartTimeSlot, teamperson.fkPerson
                FROM team
                INNER JOIN person ON fkPerson = idPerson
                INNER JOIN teamperson ON idTeam = fkTeam
                INNER JOIN activity ON team.fkActivity = idActivity
                INNER JOIN timeslot ON TypeSubscribeActivity = idTimeSlot
                WHERE AcceptedTeam = '1' 
                AND AcceptedPerson = '1'
                GROUP BY idTeam;";
    $Teams = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    $query = "SELECT fkPerson as Player from teamperson;";
    $Players = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);


    // Liste des équipes d'un joueur
    $query = "SELECT idTeam, idActivity, fkTeam, idTeamPerson, NameActivity, StartTimeSlot, NameTeam, AcceptedPerson, AcceptedTeam FROM teamperson
    INNER JOIN team ON fkTeam = idTeam
    INNER JOIN activity ON fkActivity = idActivity
    INNER JOIN timeslot ON TypeSubscribeActivity = idTimeSlot
    WHERE teamperson.fkPerson = '$idConnexion'
    GROUP BY NameTeam;";
    $Inscriptions = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);



    echo "<fieldset style='padding:10px;'>
    <legend>Inscriptions restantes</legend>";
    // Affichage des activités 

    echo "<table>
        <tr>
            <td>
                Activités
            </td>
            <td>
                Equipes inscrites                            
            </td>
            <td>
                Nombre d'équipes maximum           
            </td>
            <td>
                Nombre d'équipes nécessaires
            </td>
        </tr>";

    while($activity = $activities->fetch()) //fetch = aller chercher
    {
        extract($activity); // $idActivity, $NameActivity, $SubscribedTeams, $MaxParticipantActivity, $MinParticipantActivity, $AcceptedTeam

        echo "<tr>
            <form method='post'> 
            <td>
                $NameActivity
            </td>
            <td>
                $SubscribedTeams                           
            </td>
            <td>
                $MaxParticipantActivity           
            </td>
            <td>
                $MinParticipantActivity
            </td>
        </tr>";
    }
    echo "</table></fieldset><br><br>";

    echo "<fieldset style='padding:10px;'>
    <legend>Résumé de vos inscriptions</legend>";
    // Résumé des inscriptions 

    echo "<table>
        <tr>
            <td>
                Acitivités
            </td>
            <td>
                Période                         
            </td>
            <td>
                Equipe
            </td>
            <td>
                Action(s)
            </td>
        </tr>";
    while($Inscription = $Inscriptions->fetch()) //fetch = aller chercher
    {
        extract($Inscription); // idTeamPerson, $NameActivity, $StartTimeSlot, $NameTeam, $AcceptedPerson
        if($AcceptedPerson == 0)
        {
            echo "<tr>
                <form method='post'> 
                <td>
                    $NameActivity
                </td>
                <td>
                    $StartTimeSlot                      
                </td>
                <td>
                    $NameTeam      
                </td>
                <td>
                    En attente d'acceptation du capitaine
                    <button name='CancelTeam' value='$idTeamPerson'>Annuler</button>
                </td>
            </tr>";
        }
        else
        {            
            echo "<tr>
                    <form method='post'> 
                    <td>
                        $NameActivity
                    </td>
                    <td>
                        $StartTimeSlot                      
                    </td>
                    <td>
                        $NameTeam      
                    </td>";

            if($AcceptedTeam == 0)
            {
                echo "<td>
                    Nom de l'équipe en attende d'être accéptée par l'eseignant
                    <button name='CancelNameTeam' value='$idTeam'>Annuler</button> 
                </td>";
            }
            else
            {
                echo "<td>
                    <button name='ShowTeam' value='$idTeamPerson'><a href='EquipeCapitaine.php?team=$fkTeam&activity=$idActivity'>Voir/modifier équipe</a></button>
                </td>";
            }                    
            echo "</tr>";

        }
    }
    echo "</table></fieldset><br><br>";

    echo "<fieldset style='padding:10px;'>
    <legend>Nouvelle inscription</legend>";
    // Inscription a une équipe

    echo "<div id='Left60'><h2>Rejoindre une équipe</h2><br>
    <table>
        <tr>
            <td>
                Nom<br>(équipe)
            </td>
            <td>
                Capitaine                         
            </td>
            <td>
                Nombre de joueurs
            </td>
            <td>
                Activité
            </td>
            <td>
                Rejoindre
            </td>
        </tr>";
    while($Team = $Teams->fetch()) //fetch = aller chercher
    {
        extract($Team); // $idTeam, $NameTeam, $User, $NbPlayers, $NameActivity, $StartTimeSlot, $fkPerson
        $FlagOnTeam = 0;

        if($fkPerson == $idConnexion)
        {
            $FlagOnTeam = 1;
        }
        else 
        {
            if($FlagOnTeam != 1)
            {
                echo "<tr>
                <form method='post'> 
                <td>
                    $NameTeam
                </td>
                <td>
                    $User                           
                </td>
                <td>
                    $NbPlayers           
                </td>
                <td>
                    $NameActivity<br>($StartTimeSlot)
                </td>";
                echo "<td>
                    <button name='rejoindre' value='$idTeam'>Rejoindre</button>
                </td>";
            }
            $FlagOnTeam = 0;
        }

        echo "</tr>";
    }
    echo "</table></div>";

    // Formulaire de création d'équipe
    echo "<div id='Right34'><h2>Créer une équipe</h2><br>
        <form method='post'>
        <input type='text' name='TeamName' placeholder='Entrez un nom pour votre équipe' size=30 maxlength=20/><br><br>
        <label>Activité :</label>
        <select name='ActivityChoice'>";

    while($Activite = $Activites->fetch())
    {
        extract($Activite); // $NameActivity
        echo "<option value='$NameActivity'>$NameActivity</option>";
    }

    echo "</select><br>
        <br><input type='submit' name='SubmitTeam' value='Créer l équipe'/>
    </form></div>
</fieldset><br><br>";
}

function AdminAndMail()
{
    global $dbh;
    extract($_POST);
    
    if(isset($ValiderTri))
    {
        // Affichage selon le tri
        $query = "SELECT idPerson, personFirstName, personLastName, MedicalCertificatPerson, NameTeam, NameActivity, team.fkPerson, StartTimeSlot 
        FROM person
        INNER JOIN teamperson ON idPerson = teamperson.fkPerson
        INNER JOIN team ON idPerson = team.fkPerson
        INNER JOIN activity ON team.fkActivity = idActivity
        INNER JOIN timeslot ON TypeSubscribeActivity = idTimeSlot
        WHERE NameActivity = '$TriChoice'
        GROUP BY StartTimeSlot;";
        $Tris = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
    else
    {
        // Affichage sans tri
        $query = "SELECT idPerson, personFirstName, personLastName, MedicalCertificatPerson, NameTeam, NameActivity, team.fkPerson, StartTimeSlot 
        FROM person
        INNER JOIN teamperson ON idPerson = teamperson.fkPerson
        INNER JOIN team ON idPerson = team.fkPerson
        INNER JOIN activity ON team.fkActivity = idActivity
        INNER JOIN timeslot ON TypeSubscribeActivity = idTimeSlot
        GROUP BY StartTimeSlot;";
        $Tris = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
    
    if(isset($SendMail))
    {
    // Liste des élèves présent dans une certaine activitée
    $query = "SELECT idActivity, NameActivity, CONCAT(personFirstName, '.', personLastName, '@cpnv.ch') AS User
    FROM activity
    INNER JOIN team ON fkActivity = idActivity
    INNER JOIN teamperson ON fkTeam = idTeam
    INNER JOIN person ON idPerson = teamperson.fkPerson
    WHERE NameActivity = '$TriChoice2'";
    $ActivityPlayers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    }
    
    // Liste des activités présente sur la base de donnée
    $query = "SELECT NameActivity FROM activity;";
    $Activites = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    // Liste des activités présente sur la base de donnée, doublon pour éviter un bug dans le quel le select est vide
    $query = "SELECT NameActivity FROM activity;";
    $Activites2 = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

    if(isset($SendMail)) //$subject, $message,
    {
        $to = " ";
        $i = "0"; 
        while($ActivityPlayer = $ActivityPlayers->fetch())
        {
            $i++;
            extract($ActivityPlayer); // $idActivity, $NameActivity, $User
            if($i==1) // Evite le ";" avant la première adresse mail
            {
                $to.="$User";
            }
            else
            {
                $to.=", $User";
            }
        }
        echo "$to, $subject, $message";
        mail($to, $subject, $message);
        unset($to);
        unset($i);
    }
    echo "<fieldset style='padding:10px;'>
    <legend>Affichage & mails</legend>";
    // Afficher selon un critère, envoyé un email
    echo "<h2>Tri</h2>";
    echo "<form method='post'>
    Trié par :
    <select name='TriChoice'>";
                  
    while($Activite = $Activites->fetch())
    {
        extract($Activite); // $NameActivity
        echo "<option value='$NameActivity'>$NameActivity</option>";
    }
            
    echo "</select>
    <br><br><button name='ValiderTri'>Valider le tri</button><br><br>
    </form>
        <table>
        <tr>
            <td>
                Prenom
            </td>
            <td>
                Nom                           
            </td>
            <td>
                Classe         
            </td>
            <td>
                Certificat médical
            </td>
            <td>
                Equipe
            </td>
            <td>
                Activité
            </td>
            <td>
                Capitaine
            </td>
            <td>
                Moment de la journée
            </td>
        </tr>";
            
        while($Tri = $Tris->fetch())
        {
            extract($Tri); // $idPerson, $personFirstName, $personLastName, $MedicalCertificatPerson, $NameTeam, $NameActivity, $fkPerson, $StartTimeSlot 
            echo "<tr>
                <td>
                    $personFirstName
                </td>
                <td>
                    $personLastName                           
                </td>
                <td>

                </td>
                <td>";
                    if($MedicalCertificatPerson == 0) // Une croix rouge pour ceux qui ne possèdent pas le certificat médical, un vue vert pour ceux qui en possèdent
                    {
                        echo"<i class='fa fa-times' style='color:red' aria-hidden='true'></i>";
                    }
                    else
                    {
                        echo"<i class='fa fa-check' style='color:green' aria-hidden='true'></i>";
                    }
                echo "</td>
                <td>
                    $NameTeam
                </td>
                <td>
                    $NameActivity
                </td>
                <td>";
                    if($idPerson == $fkPerson) // Affiche qui est capitaine et qui ne l'est pas
                    {
                        echo "Oui";
                    }
                    else
                    {
                        echo "Non";
                    }
                echo"</td>
                <td>
                    $StartTimeSlot
                </td>
            </tr>";
        }
        echo "</table>";
    
        echo "<br><h2>Mail</h2>";
        echo "<table>
            <tr>
                <td><form method='post'>Envoyer à : </td>
                <td><select name='TriChoice2'>"; 
                while($Activite2 = $Activites2->fetch())
                {
                    extract($Activite2); // $NameActivity
                    echo "<option value='$NameActivity'>$NameActivity</option>";
                }
                echo"</select></td>
            </tr>
            <tr>
                <td>Sujet : </td>
                <td><input type='text' name='subject' required></td>
            </tr>
            <tr>
                <td>Contenu : </td>
                <td><textarea name='message' rows='4' cols='50' required></textarea></td>
            </tr>
            <tr>
                <td><button name='SendMail'>Envoyer l'email</button></form></td>
            </tr>
        </table>";    
    echo "</fieldset>";    
}
?>
