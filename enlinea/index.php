<?php
include_once '../includes/constants.php';
include_once '../includes/propietario.php';
include_once '../includes/file.php';

propietario::esPropietarioLogueado();


$archivo = '../'.ACTUALIZ . ARCHIVO_ACTUALIZACION;
$fecha_actualizacion = JFile::read($archivo);

$session = $_SESSION;
$propiedad = new propiedades();
$cartelera = new cartelera();
//$semafono = Array();
$propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
//
if ($propiedades['suceed'] == true) {
    $cartelera_inmueble = Array();
    $cartelera->tabla="cartelera_inmueble";
    foreach ($propiedades['data'] as $propiedad) {
        $resultado = $cartelera->listarCarteleraInmueble($propiedad['id_inmueble']);
        array_push($cartelera_inmueble, $resultado['data']);
    }
}

switch ($accion) {
    default :
        echo $twig->render('enlinea/index.html.twig', array(
            "session" => $session,
            "fecha_actualizacion" => $fecha_actualizacion,
            "publicaciones"=> $cartelera_inmueble
            ));
        break;
}