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
 die("La connection a échoué: " . $conexion->connect_error);
}

$alias = $_POST['alias'];
$password = $_POST['password'];
 
$sql = "SELECT * FROM $tbl_name WHERE alias = '$alias'";

$result = $conexion->query($sql);


if ($result->num_rows > 0) {     
 }
 $row = $result->fetch_array(MYSQLI_ASSOC);
 /*if (password_verify($password, $row['password'])) { */
 if($password == $row['password']){
    $_SESSION['loggedin'] = true;
    $_SESSION['alias'] = $alias;
    $_SESSION['start'] = time();
    $_SESSION['expire'] = $_SESSION['start'] + (5 * 60);

    echo "Bienvenue! " . $_SESSION['alias'];
    echo "<br><br><a href=panneau-control.php>Machines Virtuels</a>"; 

  //  echo "<br><br><a href=effacer-utilisateur.php>Panneau de configuration</a>";

    echo "<br><br><a href=editer-profile.php>Editer profile</a>";
    echo "<br><br><a href=logout.php>Fermer la session </a>";
 } else { 
   echo "Le alias ou le password est incorrect";
   echo "<br><a href='index.html'> Reessayer </a>";
 }
 mysqli_close($conexion); 
 ?>
