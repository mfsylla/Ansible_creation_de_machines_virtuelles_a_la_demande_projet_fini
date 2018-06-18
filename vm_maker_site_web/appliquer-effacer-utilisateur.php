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

//Conection à la base de données
$conexion_2 = mysqli_connect("localhost","root","root","vm_database_v2");

//****ON SUPRIMME TOUTES LES MACHINES VIRTUELS DE L'UTILISATEUR DANS LE ESXI***//
//a) ON selectionne les adresses ip des machines à supprimer dans la table vm_ip_adress
$user = "SELECT id_user from users_vm where alias = '$_SESSION[alias]'";
$a = "SELECT ip_address from installed_vm where id_user = ($user)";
$selection_des_vm=mysqli_query($conexion_2,$a);
while($row=mysqli_fetch_assoc($selection_des_vm))
{
//b) On selection le nom de la machine a desinstaller dans le serveur
$name_machine="SELECT name FROM installed_vm where ip_address = '$row[ip_address]'";
echo "la adresse ip est $row[ip_address] ";	
$selection_name_machine=mysqli_query($conexion_2,$name_machine);
$row2=mysqli_fetch_assoc($selection_name_machine);
//echo "la machine à desinstaller est $row2[name] ";	
//c) on definit le code query qui mettra à jour la table des adresses ip disponibles
$b="UPDATE vm_ip_adress SET available='yes',id_vm=NULL WHERE ip_adress='$row[ip_address]'";
//d) on supprime la vm du serveur ESXI et on se sert du nom de la machine avec "$row[name]"
#############code ansible qui efface toutes les machines dans cette boucle while##############
$myfile = fopen("deleteallvm.yml", "w");
$txt = "---
# Suppression d'une machine virtuelle

- hosts: 10.104.10.100
  gather_facts: false
  connection: local
  user: root
  sudo: false
  serial: 1

  tasks:
    - vsphere_guest:
        vcenter_hostname: 10.104.10.101
        username: administrator@vsphere.local
        password: VmMaker*1
        validate_certs: no
        guest: $row2[name]
        state: absent
        force: yes
  
        esxi:
          datacenter: Datacenter
          hostname: 10.104.10.100
...";
fwrite($myfile, $txt);
fclose($myfile);
echo system('ansible-playbook -i host deleteallvm.yml');
unlink('deleteallvm.yml'); 
//e) on met à jour la table des adresses ip disponibles dans la table vm_ip_adress
if ($conexion_2->query($b) === TRUE) { 
//echo 'Votre machine a été suprimée du serveur';
}else{ echo "Erreur lors de la suppression de votre machine du serveur, peut être vous n'avez pas la machine installée" . $b . "<br>" . $conexion_2->error;}

}
//-------------------------------------------------------------------------//


//****DANS LA BASE DE DONNÉES on supprime l'utilisateur et toutes les machines créées par l'utilsateur***//

//a) On efface d'abord les machines de l'utilisateur de la table "installed_vm"
$delete_all_machines = "DELETE FROM installed_vm where id_user = ($user)";
if ($conexion_2->query($delete_all_machines) === TRUE) { 
echo 'Vos machines ont été supprimées';
 }else {
 echo "Erreur lors de la suppression de vos machines machines de la table, peut être vous n'avez pas de machines installées" . $query . "<br>" . $conexion_2->error; 
   }

//b) Ensuite on efface l'utilisateur de la table "users_vm"
$sql = "DELETE FROM users_vm where alias = '$_SESSION[alias]'";
if ($conexion_2->query($sql) === TRUE) {
echo 'Votre compte a été supprimée avec vos machines aussi';
 }else {
 echo "Erreur lors de la suppression de votre compte" . $query . "<br>" . $conexion_2->error;}


?>
<!DOCTYPE html>

<html lang="en">

<head>
 <meta charset = "utf-8">
</head>

<body>

<a href="index.html" target=_blank >Retourner au site principale</a>

</body>
</html>
