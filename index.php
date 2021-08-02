<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include_once 'includes/constants.php';
if (MANTENIMIENTO) {
    $min = date("i");
    $avance = $min * 100 / 60;
    echo $twig->render('mantenimiento.html.twig',array("avance" => $avance));
} else {
    echo $twig->render('index.html.twig');
    //echo $twig->render('suspendido.html.twig');
}