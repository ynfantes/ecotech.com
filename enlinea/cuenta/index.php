<?php
include_once '../../includes/constants.php';
include_once '../../includes/propietario.php';

propietario::esPropietarioLogueado();
        
$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";
$session = $_SESSION;

$propiedad = new propiedades();
$facturas = new factura();
$inmuebles = new inmueble();
$bitacora = new bitacora();

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="listar">
    case "listar":
    default :
        
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);


        $cuenta = Array();


        if ($propiedades['suceed'] == true) {


            foreach ($propiedades['data'] as $propiedad) {

                $bitacora->insertar(Array(
                    "id_sesion"=>$session['id_sesion'],
                    "id_accion"=>1,
                    "descripcion"=>$propiedad['id_inmueble']." - ".$propiedad['apto'],
                ));
                
                $inmueble = $inmuebles->ver($propiedad['id_inmueble']);
                $factura = $facturas->estadoDeCuenta($propiedad['id_inmueble'], $propiedad['apto']);


                if ($factura['suceed'] == true) {


                    for ($index = 0; $index < count($factura['data']); $index++) {
                        $filename = "../avisos/" . $factura['data'][$index]['numero_factura'] . ".pdf";
                        $factura['data'][$index]['aviso'] = file_exists($filename);
                    }


                    $cuenta[] = Array("inmueble" => $inmueble['data'][0],
                        "propiedades" => $propiedad,
                        "cuentas" => $factura['data']);
                }
            }
        }
        echo $twig->render('enlinea/cuenta/formulario.html.twig', array("session" => $session,
            "cuentas" => $cuenta
        ));


        break; // </editor-fold>

        
}