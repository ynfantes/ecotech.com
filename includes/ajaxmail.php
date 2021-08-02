<?php
$r = array();
if ($_POST['username']=='' | $_POST['email']=='' | $_POST['msg']=='' | $_POST['subject']==''){
    $r['message'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n"
            . "<i class=\"zmdi zmdi-close-circle-o mr-3\"></i>\n<strong>Ups!</strong> "
            . "Por favor complete los campos requeridos.\n<button type=\"button\" class=\"close\" data-dismiss=\"alert\" "
            . "aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n</button>\n</div>";
    $r['status']  = "fail";
} else {
    include './constants.php';
    include './mailto.php';
    $mail = new mailto(SMTP);
    $mensaje = $_POST['username'].' ha escrito desde '.NOMBRE_APLICACION.'<br><br>';
    $mensaje.= $_POST['msg'].'<br>';
    $mensaje.= 'Ha dejado este correo electrónico: '.$_POST['email'].' en caso de requerir respuesta.';
    $e = $mail->enviar_email($_POST['subject'],
            $mensaje, '', 
            "tucondominioeco@gmail.com");
    if ($e=='') {
        $r['status']  = "success";
        $r['message'] = "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n"
            . "<i class=\"zmdi zmdi-thumb-up mr-3\"></i>\n<strong>Bien hecho!</strong> "
            . "Nos pondremos en contacto a la brevedad.\n<button type=\"button\" class=\"close\" "
            . "data-dismiss=\"alert\" aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n"
            . "</button>\n</div>";
    } else {
        $r['message'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n"
            . "<i class=\"zmdi zmdi-close-circle-o mr-3\"></i>\n<strong>Ups!</strong> "
            . "No se puedo enviar el correo de contacto.\n<button type=\"button\" class=\"close\" data-dismiss=\"alert\" "
            . "aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n</button>\n</div>";
        $r['status']  = "fail";
    }
    
}
echo json_encode($r);