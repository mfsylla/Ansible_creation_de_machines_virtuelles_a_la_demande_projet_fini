<?php
session_start();
?>

<?php

$host_db = "localhost";
$user_db = "root";
$pass_db = "root";
$db_name = "vm_database_v2";
$tbl_name = "users_vm";

$conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

if ($conexion->connect_error) {
 die("La conexion falló: " . $conexion->connect_error);
}

$alias = $_SESSION['alias'];

$query = "UPDATE $tbl_name SET password = '$_POST[pass2]'
WHERE alias = '$alias'";

 if ($conexion->query($query) === TRUE) {
 
 echo "<br />" . "<h2>" . "Modification du mot de pass faite avec succés!" . "</h2>";
 echo "<h4>" . "Bienvenue: " . $_SESSION['alias'] . "</h4>" . "\n\n";
 echo "<h5>" . "Se connecter: " . "<a href='index.html'>Login</a>" . "</h5>"; 
 }

 else {
 echo "Erreur lors de la modification du mot de pass." . $query . "<br>" . $conexion->error; 
   }
 
 mysqli_close($conexion);

 ?>
