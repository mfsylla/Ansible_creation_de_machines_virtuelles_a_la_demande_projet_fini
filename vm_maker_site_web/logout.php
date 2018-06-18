<?php

session_start();
unset ($SESSION['alias']);
session_destroy();

header('Location: http://localhost/vm_maker_beta4/index.html');
?>