<?php
$fichero = 'config.json';
$actual = $_POST['json'];
// Escribe el contenido al fichero
file_put_contents($fichero, $actual);

var_dump( $_POST);
return;