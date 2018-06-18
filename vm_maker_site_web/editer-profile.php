<?php
session_start();
?>

 <!DOCTYPE html>
<html lang="en">

<head>
<title>Edition du profil</title>
</head>

<body>
<h1>Panneau de configuration</h1>

<link rel="stylesheet" type="text/css" href="/code_examples/tutorial.css">
<script type="text/javascript" src="passtest.js"></script>
<div class="tutorialWrapper">
    <form action="appliquer-editer-profile.php" method="post">
        <div class="fieldWrapper">
            <label for="pass1">Password:</label>
            <input type="password" name="pass1" id="pass1">
        </div>
        <div class="fieldWrapper">
            <label for="pass2">Confirm Password:</label>
            <input type="password" name="pass2" id="pass2" onkeyup="checkPass(); return false;">
            <span id="confirmMessage" class="confirmMessage"></span>
        </div>
        <input type="submit" name="submit" value="Editer le mot de pass">
    </form>
</div>
<form>
    <button type="submit" formaction="effacer-utilisateur.php">Supprimer votre compte</button>
</form>

<br><br>
<a href=logout.php>Fermer la session X </a>
</body>
</html>