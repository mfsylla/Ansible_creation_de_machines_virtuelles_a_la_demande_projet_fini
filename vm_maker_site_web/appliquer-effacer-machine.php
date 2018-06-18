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


$conexion_2 = mysqli_connect("localhost","root","root","vm_database_v2");
$number_machine_a_desinstaller = (int)$_POST['machine_a_desinstaller'];

//***Mettre en disponible la adresse ip de la machine que sera supprimée dans la tabme 'vm_ip_adress***//
$chercher_ip_adress = "SELECT ip_address FROM installed_vm where id_vm = $number_machine_a_desinstaller";
$turn_available_ip_adress = "UPDATE vm_ip_adress SET available='yes',id_vm=NULL WHERE ip_adress= ($chercher_ip_adress)";

if ($conexion_2->query($turn_available_ip_adress) === TRUE){}else{echo " La id de la vm a désinstaller n'est pas valide";}
////////-------------------------------------------------------------------/////////


//********Supprimer la machine de la table 'installed_vm' et du serveur ***************////
//a) on selection le nom de la machine à desinstaller
$name_machine="SELECT name FROM installed_vm where id_vm = $number_machine_a_desinstaller";
$selection_des_vm=mysqli_query($conexion_2,$name_machine);
$row=mysqli_fetch_assoc($selection_des_vm);
//echo "la machine à desinstaller est $row[name] ";

//b) Suppression de la machine de la table 'installed_vm'
$sql = "DELETE FROM installed_vm where id_vm = $number_machine_a_desinstaller"; //effacer la machine à desinstaller de la table .
if ($conexion_2->query($sql) === TRUE) {
/*######CODE ANSIBLE POUR SUPPRIMER LA MACHINE############*/
$myfile = fopen("deletevm.yml", "w");
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
        guest: $row[name]
        state: absent
        force: yes
  
        esxi:
          datacenter: Datacenter
          hostname: 10.104.10.100
...";
fwrite($myfile, $txt);
fclose($myfile);
exec('ansible-playbook -i host deletevm.yml');
unlink('deletevm.yml');
//echo ' machine supprimée ';
//echo "$row[name]"; //nom de la machine à desinstaller.
echo "Votre machine appelée $row[name] a été supprimée ";
echo "<br>"; 
 }else {
 echo "Erreur lors de la suppression de la machine" . $query . "<br>" . $conexion_2->error; 
   }

?>

<!DOCTYPE html>
<html lang="en">


<body>
<a href="panneau-control.php" target=_blank >Retourner au centre de Machines virtuelles</a>
</body>
</html>
