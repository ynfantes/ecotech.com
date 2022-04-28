<?php
include_once '../includes/constants.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : "";
$id = isset($_GET['id']) ? $_GET['id'] : -1;

switch ($accion) {

    case "listarPropietariosPorInmueble" :
        if (isset($_POST['id_inmueble'])) {
            $pro = new propietario();


            $r = $pro->listarPropietariosPorInmueble($_POST['id_inmueble']);


            if ($r['suceed'] && count($r['data']) > 0) {
                $jsonTable = json_encode($r['data']);
                echo $jsonTable;
            }
        
          }


        break; 
          
    case "salir":
        
        $user_logout = new usuario();
        $user_logout->logout();
        break; 
        
    case "guardar":
        $pago = new pago();
        $data = $_POST;
        if (count($data) > 0) {
            unset($data['procesar']);
            $exito = $pago->registrarPago($data);
            $exito['facturas'] = $_POST['facturas'];
            
        } else {
            header("location:".URL_INTRANET."/caja");
            return;
        }
        
        echo $twig->render('intranet/caja/index.html.twig', array("session" => $session,
            "resultado" => $exito,
            "accion" => "registrar"
        ));
        break;
    
    case "guardar-cartelera":
        $result = Array();
        $data = Array();
        $data = $_POST;
        $cartelera = new cartelera();
        $cartelera->tabla = "cartelera_inmueble";
        if (isset($_FILES['archivo'])) {
            $file = explode(".", $_FILES['archivo']['name']);
            $extension = strtolower(end($file));
            $data['archivo'] = strtolower($_FILES['archivo']['name']);
            $tempFile = $_FILES['archivo']['tmp_name'];
            $mainFile = $data['archivo'];
            move_uploaded_file($tempFile, "archivos/" . $mainFile);
        }
        $inmuebles = $data['inmueble'];
        foreach ($inmuebles as $inmueble) {
            $data['inmueble'] = $inmueble;
            $result = $cartelera->insertar($data);
        }
        if ($result['suceed']) {
            $result['mensaje'] = '<button type="button" class="close" data-dismiss="alert">';
            $result['mensaje'] .= '<i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-check"></i>';
            $result['mensaje'] .= 'Muy Bien! </strong> Publicación Registrada con éxito.';
        } else {
            $result['mensaje'] = '<button type="button" class="close" data-dismiss="alert">
            <i class="ace-icon fa fa-times"></i>
            </button><strong><i class="ace-icon fa fa-times"></i>
            Ups! Ha ocurrido un error</strong> No se pudo registrar esta publicación.';
        }
        echo json_encode($result);
        break; 

    case "cartelera-general":
        $cartelera = new cartelera();
        $publicaciones = $cartelera->listar();


        echo $twig->render('intranet/caja/cartelera.general.html.twig', array(
            "session" => $session,
            "publicaciones" => $publicaciones['data'],
            "id" => $id
        ));
        break; 
        
    case "eliminar-cartelera-general":
        $data = Array();
        $cartelera = new cartelera();
        $cartelera->tabla="cartelera_inmueble";
        if (is_numeric($id)) {
            $data['eliminar'] = 1;
            $cartelera->actualizar($id, $data);
        }
        $publicaciones = $cartelera->listar();
        
        echo $twig->render('intranet/cartelera/publicaciones.html.twig', Array("publicaciones" => $publicaciones['data']));
        break; 

    case "eliminar-cartelera-condominio":
        $data = Array();
        $cartelera = new cartelera();
        $cartelera->tabla = "cartelera_inmueble";
        if (is_numeric($id)) {
            $data['eliminar'] = 1;
            $cartelera->actualizar($id, $data);
        }
        $publicaciones = $cartelera->listar();
        echo $twig->render('intranet/cartelera/publicaciones.html.twig', Array("publicaciones" => $publicaciones['data']));
        break; 
            
    case "listar-cartelera-general":
        $cartelera = new cartelera();
        $cartelera->tabla = "cartelera_inmueble";
        $publicaciones = $cartelera->listar();

        echo $twig->render('intranet/cartelera/publicaciones.html.twig', Array("publicaciones" => $publicaciones['data']));
        break; 

    case "cartelera-condominio":
        $cartelera = new cartelera();
        $inmueble = new inmueble();


        $cartelera->tabla = "cartelera_inmueble";
        $publicaciones = $cartelera->listar();


        $lista_inm = $inmueble->listarInmueblesAutorizados($session['usuario']['id']);


        echo $twig->render('intranet/caja/cartelera.condominio.html.twig', array(
            "session" => $session,
            "publicaciones" => $publicaciones['data'],
            "id" => $id,
            "inmuebles" => $lista_inm['data']
        ));
        break; 
        
    case "consultar":
        
        if (isset($_POST['inmueble']) && isset($_POST['apto'])) {
            $facturas = new factura();
            $inmueble = new inmueble();
            $propietario = new propietario();
            
            $inm = $inmueble->ver($_POST['inmueble']);
            
            $prop = $propietario->obtenerPropietario($_POST['inmueble'], $_POST['apto']);
            
            $factura = $facturas->estadoDeCuenta($_POST['inmueble'], $_POST['apto']);

            if ($factura['suceed'] == true) {

                for ($index = 0; $index < count($factura['data']); $index++) {

                    $filename = "../avisos/" . $factura['data'][$index]['numero_factura'] . ".pdf";
                    $factura['data'][$index]['aviso'] = file_exists($filename);
                }

                $cuenta = Array(
                    "inmueble" => $_POST['inmueble'],
                    "apto" => $_POST['apto'],
                    "inmueble"=>$inm['data'],
                    "propietario"=>$prop,
                    "recibos" => $factura['data']);
            }
        }
        $lista_inm = $inmueble->listar();
        echo $twig->render('intranet/caja/index.html.twig', array(
            "session" => $session,
            "cuentas" => $cuenta,
            "inmuebles"=> $lista_inm['data']
        ));
        break; 

    default :
        
        $inmueble = new inmueble();
        $lista_inm = $inmueble->listar();
        
        echo $twig->render(
            'intranet.html.twig',
            Array('condominios' => $lista_inm['data'])
        );
        break; 

}