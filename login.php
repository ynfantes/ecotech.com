<?php
include_once 'includes/constants.php';
$propietario = new propietario();
$result = array();
if (!MANTENIMIENTO) {
    if (isset($_POST['login'])) {
        $result = $propietario->login($_POST['cedula'], $_POST['password'], 0);   
    } else {
        if (isset($_POST['cedula'])) {
            $result = $propietario->recuperarContraSena($_POST['cedula']);
        }
    }
    echo $twig->render('login.html.twig', array("mensaje" => $result));
   // echo $twig->render('suspendido.html.twig');
} else {
    $min = date("i");
    $avance = $min * 100 / 60;
    echo $twig->render('mantenimiento.html.twig',array("avance" => $avance));
}