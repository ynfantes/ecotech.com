<?php
header( 'Content-type: text/html; charset=utf-8' );
include_once '../../includes/constants.php';
$path= getcwd();
$dir = opendir($path);
$factura = new factura();
$e = 0;
$n = 0;
// Leo todos los ficheros de la carpeta
while ($elemento = readdir($dir)){
    // Tratamos los elementos . y .. que tienen todas las carpetas
    if( $elemento != "." && $elemento != ".." && $elemento != "mantenimiento.php" && $elemento != "index.php"){
        // Si es una carpeta
        if(!is_dir($path.'/'.$elemento) ){
            
            $r = $factura->avisoExisteEnBaseDeDatos($elemento);
            if ($r==0) {
                unlink($path."/".$elemento);
                $n++;
            } else {
                $e++;
            }
        }
    }
}
echo "<br>";
echo "$n archivos NO están en la base de datos.<br>";
echo "$e archivos SI están en la base de datos";