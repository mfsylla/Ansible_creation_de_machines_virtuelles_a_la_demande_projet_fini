<?php

 $host_db = "localhost";// 
 $user_db = "root";
 $pass_db = "root";
 $db_name = "vm_database_v2";
 $tbl_name = "users_vm"; 
 
 $conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

 if ($conexion->connect_error) {
 die("La conexion a échoué: " . $conexion->connect_error);
}

//a) Verifier que le Alias de l'utilisateur n'est pas déjà pris par n autre utilsateur
 $chercherutilisateur = "SELECT * FROM $tbl_name
 WHERE alias = '$_POST[surnom]' ";
 $result = $conexion->query($chercherutilisateur);
 $count = mysqli_num_rows($result);

 if ($count == 1) {
 echo "<br />". "Le alias a été pris par un aitre utilisateur." . "<br />";
 echo "<a href='index.html'>Veuillez choisir un autre alias</a>";
 }
 else{
//b) si c'est validé, donc on enregistre l'utilisateur
 $query = "INSERT INTO users_vm (nom, prenom, profession, ecole, email,alias,password,date_de_creation)
           VALUES ('$_POST[last_name]','$_POST[username]','$_POST[profession]','$_POST[ecole]' ,'$_POST[user_email]','$_POST[surnom]','$_POST[password]',NOW())";

 if ($conexion->query($query) === TRUE) {
 
 echo "<br />" . "<h2>" . "Utilisateur créé avec succés!" . "</h2>";
 echo "<h4>" . "Bienvenue: " . $_POST['username'] . "</h4>" . "\n\n";
 echo "<h5>" . "Se connecter: " . "<a href='index.html'>Login</a>" . "</h5>"; 
 }

 else {
 echo "Erreur lors de la creation de l'utilisateur." . $query . "<br>" . $conexion->error; 
   }
 }
 mysqli_close($conexion);
?>