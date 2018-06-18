<?php
session_start();

$host_db = "localhost";// 
 $user_db = "root";
 $pass_db = "root";
 $db_name = "vm_database_v2"; //basedatosmaster
 $tbl_name = "installed_vm"; //Usuarios

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
} else {
   echo "Cette page est seulement pour les utilisateurs.<br>";
   echo "<br><a href='login.html'>Login</a>";
   echo "<br><br><a href='index.html'>S'enregistrer</a>";

exit;
}

 $conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);
 if ($conexion->connect_error) {
 die("La conexion a échoué: " . $conexion->connect_error);
}

//**********Installation des machines virtuels************//
//a) On verifie que le nom choisi pour la machine n'existe pas dans la table  
$phrase = $_POST['nom_de_machines'].'_1';
$Operating_System=$_POST['Operating_System'];
$taille_disque=$_POST['taille_disque'];
$ram=$_POST['ram'];
$processeur=$_POST['nombre_processeurs'];

$cherchernommachine = "SELECT * FROM $tbl_name
 WHERE name = '$phrase' ";

 $result = $conexion->query($cherchernommachine);

 $count = mysqli_num_rows($result);

 if ($count == 1) {
 echo "<br />". "Le nom de la machine a été pris par un autre utilisateur." . "<br />";

 echo "<a href='panneau-control.php'>Veuillez choisir un autre nom de machine</a>";
 exit;
 }
//b) ON installe la quantité de machines virtuels que l'utilisateur a demandé(bouble for)
for($i=1;$i<(int)$_POST['quantite']+1;$i++){
	
// fin *****prendre une @ip disponible de la table "vm_ip_adress"***/


//C) Insertion des informations dans la table concernant à la machine à installer 
$phrase = $_POST['nom_de_machines'].'_'.$i;
$query = "INSERT INTO installed_vm (id_user, name, systeme_de_exploitation, nombre_de_processeurs, quantite_de_memoire,taille_du_disque_dur,date_de_creation,quantite_de_machines,description,ip_address)
           VALUES((SELECT id_user from users_vm where alias='$_SESSION[alias]'),'$phrase','$_POST[Operating_System]','$_POST[nombre_processeurs]' ,'$_POST[ram]','$_POST[taille_disque]',NOW(),'$_POST[quantite]','$_POST[description]',(SELECT ip_adress FROM vm_ip_adress where available='yes' LIMIT 1))";
//Prise de l'adresse IP comme variable STRING
$conexion_2=mysqli_connect("localhost","root","root","vm_database_v2");
$ip="SELECT ip_adress FROM vm_ip_adress WHERE available='yes' LIMIT 1";
$ip_adress_vm=mysqli_query($conexion_2,$ip);
$row=mysqli_fetch_assoc($ip_adress_vm);

if ($conexion->query($query) === TRUE) {
//echo 'machine installée';
echo "La machine $phrase a été installée et son adress IP est $row[ip_adress]";
echo "<br>";
 }else {
 echo "Erreur lors de l'installation de la machine." . $query . "<br>" . $conexion->error; 
   }

//D) Bloquer l'adresse ip déjà prise par la machine créée 
$register_id_vm = "SELECT id_vm from installed_vm order by id_vm desc LIMIT 1";
$block_ip = "UPDATE vm_ip_adress SET available='no',id_vm=($register_id_vm) WHERE available='yes' LIMIT 1";
if ($conexion->query($block_ip) === TRUE){}else{echo "No blocked_ip_address";}
//echo "Son adresse IP est : $ip_adress_row[ip_adress]";


//E) ######################CODE ANSIBLE QUI INSTALLE LES MACHINES###############
$myfile = fopen("testfile.yml", "w");
$txt = "---
# Création d'une nouvelle VM dans un server esx
# Return changed = false quand la machine est déjà installée

- hosts: esxi
 # gather_facts: false
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
        guest: $phrase
        from_template: yes
        template_src: $Operating_System
        power_on_after_clone: yes

#    - name: Configuration de la VM
        vm_extra_config:
          folder: InstalledVM
          vcpu.hotadd: yes
          mem.hotadd: yes
          notes: Test VM machine
#        template: $Operating_System

#    - name: Stockage configuration 
        vm_disk:
          disk1:
            size_gb: $taille_disque
            type: thin
            datastore: datastore-VM

#    - name: Network configuration 
        vm_nic:
          nic1:
            type: vmxnet3
            network: VM Network
            network_type: standard
          nic2:
            type: vmxnet3
            network: dvSwitch Network
            network_type: dvs

#    - name: Hardware config
        vm_hardware:
          memory_mb: $ram
          num_cpus: $procresseur
          osid: centos64Guest
          scsi: paravirtual

#    - name: esxi location
        esxi:
          datacenter: Datacenter
          hostname: 10.104.10.100
...";
fwrite($myfile, $txt);
fclose($myfile);
exec('ansible-playbook -i host testfile.yml');
unlink('testfile.yml');
//-----------------------------------------------------------------------//
}

echo "<br>";
echo "Le login pour acceder a vos machine est par deffaut:utilisateur";
echo "<br>";
echo "et le mot de pass par deffaut est:password";
echo "<br>";
echo "nous vous conseillons de personaliser votre mot de passe dans vos machines installées";
 
?>
<!DOCTYPE html>
<html lang="en">

<body>
<a href="panneau-control.php" target=_blank >Retourner au centre de Machines virtuelles</a>
</body>
</html>
