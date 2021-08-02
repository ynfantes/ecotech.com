<?php
if ($_GET('token')=="") { 
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include_once 'includes/constants.php';
//$min = date("i");
//$avance = $min * 100 / 60;
//echo $twig->render('mantenimiento.html.twig',array("avance" => $avance));
$inmueble = new inmueble();
$lista_inmuebles = $inmueble->listar();

echo $twig->render('intranet.html.twig',Array("condominios"=>$lista_inmuebles['data']));
} else {
    die("Aceso Restringido");
}