<?php
include './includes/constants.php';
include './includes/mailto.php';
$mail = new mailto(SMTP);
echo("enviando......<br>");
$r = $mail->enviar_email("Prueba", "Este es un mensaje de prueba", '', "ynfantes@gmail.com", "Edgar Messia");
echo("enviado");                    
if ($r=='') {
    echo(".- Mensaje enviado a Ok!\n");
} else {
    echo(".- Mensaje enviado a Falló\n");
}
