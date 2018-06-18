<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

} else {
   echo "Cette page est seulement pour les utilisateurs.<br>";
   echo "<br><a href='login.html'>Login</a>";
   echo "<br><br><a href='index.html'>S'enregistrer</a>";

exit;
}

$now = time();


if($now > $_SESSION['expire']) {
session_destroy();

echo "Votre session est terminée,
<a href='login.html'>Vous devez vous connecter</a>";
exit;
}


//$filtered_user = 
$conexion_2 = mysqli_connect("localhost","root","root","vm_database_v2");
$user = "SELECT id_user from users_vm where alias = '$_SESSION[alias]'";
$sql_code="SELECT * from installed_vm where id_user = ($user)"; 

$selection = mysqli_query($conexion_2,$sql_code);

echo"<table border='1'>";
echo"<tr><td>id_vm</td><td>name</td><td>systeme_de_exploitation</td><td>nombre_processeurs</td><td>quantite_de_memoire</td><td>taille_du_disque_dur</td><td>date_de_creation</td><td>quantite_de_machines</td><td>description</td><td>ip_address</td></tr>";
while ($row = mysqli_fetch_assoc($selection)) {
	echo "<tr><td>{$row['id_vm']}</td><td>{$row['name']}</td><td>{$row['systeme_de_exploitation']}</td><td>{$row['nombre_de_processeurs']}</td><td>{$row['quantite_de_memoire']}</td><td>{$row['taille_du_disque_dur']}</td><td>{$row['date_de_creation']}</td><td>{$row['quantite_de_machines']}</td><td>{$row['description']}</td><td>{$row['ip_address']}</td><tr>\n";
	# code...
}
echo "</table>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>Panneau de configuration</title>
</head>

<body>
<FORM action="appliquer-effacer-machine.php" method="post">
<label>ID de la machine dont vous voulez desinstaller</label><br>
 <input type="text" name="machine_a_desinstaller" required>
	</body>
<input type="submit" name="submit" value="effacer_la_machine">
</FORM>
</html>

