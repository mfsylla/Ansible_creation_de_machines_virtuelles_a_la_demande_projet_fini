<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
//shell_exec('mkdir /home/elias/Bureau/ caca');
/*Ici j'essaye de creer un dossier apres de se logger*/
} else {
   echo "Cette page est seulement pour les utilisateurs.<br>";
   echo "<br><a href='login.html'>Login</a>";
   echo "<br><br><a href='index.html'>S'enregistrer</a>";

exit;
}

$now = time();
mkdir("/home/elias/Bureau/NouveauDossier",0777);

if($now > $_SESSION['expire']) {
session_destroy();

echo "Votre session est terminée,
<a href='login.html'>Vous devez vous connecter</a>";
exit;
}


//*** Affichage de la table des machines créées par l'utilisateur **/
$conexion_2 = mysqli_connect("localhost","root","root","vm_database_v2");
$user = "SELECT id_user from users_vm where alias = '$_SESSION[alias]'";//ça marche avec 1
$sql_code="SELECT * from installed_vm where id_user = ($user)"; 
$selection = mysqli_query($conexion_2,$sql_code);
echo"\n\nListe de vos machines virtuels\n\n";
echo"<table border='1'>";
echo"<tr><td>id_vm</td><td>name</td><td>systeme_de_exploitation</td><td>nombre_processeurs</td><td>quantite_de_memoire</td><td>taille_du_disque_dur</td><td>date_de_creation</td><td>quantite_de_machines</td><td>description</td><td>ip_address</td></tr>";
while ($row = mysqli_fetch_assoc($selection)) {
	echo "<tr><td>{$row['id_vm']}</td><td>{$row['name']}</td><td>{$row['systeme_de_exploitation']}</td><td>{$row['nombre_de_processeurs']}</td><td>{$row['quantite_de_memoire']}</td><td>{$row['taille_du_disque_dur']}</td><td>{$row['date_de_creation']}</td><td>{$row['quantite_de_machines']}</td><td>{$row['description']}</td><td>{$row['ip_address']}</td></tr>\n";
}
echo "</table>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>Création de(s) machine(s) virtuel(s)</title>
</head>

<body>
<h1>Création de(s) machine(s) virtuel(s)</h1> 

<FORM action="installer_machine.php" method="post">

<label>Nom des machines (ils s'apelleront tous pareils suivis du numero qui s'incremente):</label><br>
 <input type="text" name="nom_de_machines" required>

<p>Systeme d'exploitation</p>
<SELECT name="Operating_System" size="1" required>
<OPTION>Ubuntu
<OPTION>Windows_Server_2012_R2
<OPTION>Debian 9.2.1
</SELECT>
 <br/><br/>

 <p>Nombre de processeurs</p>
<SELECT name="nombre_processeurs" size="1" required>
<OPTION>1
<OPTION>2
</SELECT>
 <br/><br/>

<p>Quantité de memoire RAM (mb)</p>
<SELECT name="ram" size="1" required>
<OPTION>1024
<OPTION>2048
</SELECT>
 <br/><br/>

 <p>Taille du disque dur</p>
<SELECT name="taille_disque" size="1" required>
<OPTION>10
<OPTION>15
</SELECT>
 <br/><br/>


<label>Quantité de machines (1 à 10):</label><br>
 <input type="text" name="quantite" required>

<label>Description obligatoire (Projet?,TP?,TD?,reason d'installation des machines):</label><br>
<textarea name="description" cols="40" rows="10" required></textarea>

 <p>Logiciels par defaut</p>
<SELECT name="Defaults_softwares" size="1">
<OPTION>LAMP + VIM
<OPTION>LAMP
<OPTION>VIM
</SELECT>

<br><br>
<input type="submit" name="submit" value="Installer_la_machine">
</FORM>

<!-- Bouton -->
<form>
    <button type="submit" formaction="effacer-machine.php">Supprimer une machine</button>
</form>
<!-- Fin du bouton -->


<br><br>
<a href=logout.php>Fermer la session X </a>
</body>
</html>
