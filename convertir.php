<?php
include_once 'convertidorImagen.php';

$image = $_FILES['image'];

$converter = new imageConverter();
$resultado = $converter->convertir($image, "./", "imagenSalida-", true, T_JPG);

echo "<a href='$resultado'>Ver resultado</a>";