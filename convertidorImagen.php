<?php

define("T_WEBP", 1);
define("T_JPG", 2);
define("T_PNG", 3);
define("T_GIF", 4);
/*
 * Creado por Luis Alejandro LEMOS CAMACHO
 * Desde Montevide, URUGUAY al mundo
 */

class imageConverter {

    public $lastImg = "";
    public $error = "";
    
    public function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }
    
    public function convertir($images, $dirSalida, $nombreArchivo, $autoGen = false, $formatoSalida = 1, $maxWidth = 600, $maxHeight = 600) {
        /*
         * Parámetros:
         * $images -> En este parámetro va el $_POST['imagen'] que traigas de tu formulario.
         * $dirSalida -> Directorio destino donde se guardará la imagen convertida.
         * $nombreArchivo -> Nombre del archivo de salida.
         * $autoGen -> Decide si el nombre aportado es el nombre completo de la imagen (false) o si es un prefijo al que hay que añadirle un string autogenerado para evitar duplicados.
         * $formatoSalida -> Contendra un numero del al 4 para definir el formato de salida. 1 = WEBP, 2 = JPG, 3 = PNG y 4 = GIF
         * $maxWidth -> establece el ancho máximo permitido de la imagen, en caso de ser superado se reescalará la imagen para cumplir la condición.
         * $maxHeight -> establece el alto máximo permitido de la imagen. Funciona igual que el maxWidth.
         */
        if ($images['type'] == 'image/png' || $images['type'] == 'image/jpeg' || $images['type'] == 'image/jpg' || $images['type'] == 'image/gif' || $images['type'] == 'image/webp') {

            if ($autoGen) {
                $nombreArchivo .= generate_string("abcdefghijklmnopqrstuvxyz0123456789");
            }

            $medidasimagen = getimagesize($images['tmp_name']);

            $rtOriginal = $images['tmp_name'];

            switch ($images['type']) {
                case 'image/jpeg':
                    $original = imagecreatefromjpeg($rtOriginal);
                    break;

                case 'image/png':
                    $original = imagecreatefrompng($rtOriginal);
                    break;
                case 'image/webp':
                    $original = imagecreatefromwebp($rtOriginal);
                    break;
                case 'image/gif':
                    $original = imagecreatefromgif($rtOriginal);
                    break;
            }
            if ($medidasimagen[0] <= $maxWidth && $medidasimagen[1] <= $maxHeight) {
                $ancho_final = $medidasimagen[0];
                $alto_final = $medidasimagen[1];
            } else {
                $x_ratio = $maxWidth / $medidasimagen[0];
                $y_ratio = $maxHeight / $medidasimagen[1];

                if ($medidasimagen[0] > $maxWidth) {

                    $ancho_final = ceil($x_ratio * $medidasimagen[0]);
                    $ratio = $medidasimagen[1] / $medidasimagen[0];
                    $alto_final = ceil($ancho_final * $ratio);
                } else {

                    $alto_final = ceil($y_ratio * $medidasimagen[1]);
                    $ratio = $medidasimagen[0] / $medidasimagen[1];
                    $ancho_final = ceil($alto_final * $ratio);
                }
            }

            $lienzo = imagecreatetruecolor($ancho_final, $alto_final);

            imagecopyresampled($lienzo, $original, 0, 0, 0, 0, $ancho_final, $alto_final, $medidasimagen[0], $medidasimagen[1]);
            switch ($formatoSalida) {
                case 1:
                    $return = imagewebp($lienzo, $dirSalida . $nombreArchivo. ".webp");
                    break;
                case 2:
                    $return = imagejpeg($lienzo, $dirSalida . $nombreArchivo. ".jpg");
                    break;
                case 3:
                    $return = imagepng($lienzo, $dirSalida . $nombreArchivo. ".png");
                    break;
                case 4:
                    $return = imagegif($lienzo, $dirSalida . $nombreArchivo. ".gif");
                    break;
            }

            $this->lastImg = $nombreArchivo;
            if ($return) {
                return $nombreArchivo;
            } else {
                $this->error = "Hubo un problema al crear la imagen";
                return false;
            }
        } else {
            $this->error = "La imagen no tiene un formato soportado";
            return false;
        }
    }
}
