<?php

/**
 * Clase que mantiene la tabla propietario
 *
 * @autor   Edgar Messia
 * @static  
 * @package     Valoriza2.Framework
 * @subpackage	FileSystem
 * @since	1.0
 */

class propietario extends db implements crud  {

    const tabla = "propietarios";

    public function actualizar($id, $data) {
        
        return db::update(self::tabla, $data, Array("id"=>$id));
    }

    public function borrar($id) {
        return db::delete(self::tabla, Array("id"=>$id));
    }

    public function borrarTodo() {
        return db::delete(self::tabla);
    }

    public function insertar($data) {
        return db::insert(self::tabla,$data);
    }

    public function listar() {
        return db::select("*", self::tabla);
    }

    public function ver($id) {
        return db::select("*",self::tabla,Array("id"=>$id));
    }
    
    public function cambioDeClave($id,$clave) {
        return db::update(self::tabla, Array("clave"=>$clave,"id"=>$id,"cambio_clave"=>1));
    }
    
    public function login($cedula,$password) {
        $r = array();
        if ($cedula!="" && $password!="") {
        
            $r = db::select("*",self::tabla,Array("cedula"=>$cedula));
            
            if ($r['suceed'] == 'true' && count($r['data']) > 0) {
                $res = db::select("*","junta_condominio",Array("cedula"=>$cedula));
                $junta_condominio = '';
                if ($res['suceed'] && count($res['data'])> 0) {
                    $junta_condominio = $res['data'][0]['id_inmueble'];
                }
                if ($r['data'][0]['clave']==$password) {
                    // registramos la sesion del usuario
                    $sesion = $this->generarIdInicioSesion($r['data'][0]['cedula']);
                    
                    session_start();
                    if ($sesion['suceed']) {
                        $_SESSION['id_sesion'] = $sesion['insert_id'];
                    }
                    $_SESSION['usuario'] = $r['data'][0];
                    $_SESSION['junta'] = $junta_condominio;
                    $_SESSION['status'] = 'logueado';
                    $_SESSION['portal_mercantil'] = FALSE;
                    $propiedades = new propiedades();
                    $p = $propiedades->inmueblePorPropietario($r['data'][0]['cedula']);
                    if ($p['suceed'] && count($p['data'])> 0) {
                        foreach ($p['data'] as $propiedad) {
                            //echo $propiedad['id_inmueble']=='1001';
                            if ($propiedad['id_inmueble']=='1001') {
                                $_SESSION['portal_mercantil'] = TRUE;
                                break;
                            }
                        }
                    }
                    $r['status']  = "success";
                    $r['message'] = "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n"
                        . "<i class=\"zmdi zmdi-thumb-up mr-3\"></i>\n<strong>Usuario registrado!</strong> "
                        . "Lo estamos redireccionando....\n<button type=\"button\" class=\"close\" "
                        . "data-dismiss=\"alert\" aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n"
                        . "</button>\n</div>";
                    $r['url'] = URL_SISTEMA;
                    unset($r['data'],$r['query'],$r['stats'],$r['suceed']);
                } else {
                    $r['status']  = "fail";
                    $r['message'] = "Contraseña inválida.";
                }
            } else {
                $r['status']  = "fail";
                $r['message'] = "Código de cliente no registrado.";
            }
        } else {
            $r['status']  = "fail";
            $r['message'] = "Por favor complete los campos requeridos.";
        }
        if ($r['status']=='fail') {
            $r['message'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n"
            . "<i class=\"zmdi zmdi-close-circle-o mr-3\"></i>\n<strong>Ups!</strong> "
            . $r['message']."\n<button type=\"button\" class=\"close\" data-dismiss=\"alert\" "
            . "aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n</button>\n</div>";
        }
        return $r;
    }
    
    public function generarIdInicioSesion($cedula) {
        $sql = "insert into sesion(cedula,inicio,fin) values(".$cedula.",now(),now())";
        return db::exec_query($sql);
    }
    
    public function recuperarContraSena($cedula) {
        if ($cedula!="") {
            $result = db::select("*",self::tabla,Array("cedula"=>$cedula));
            if ($result['suceed'] == 'true' && count($result['data']) > 0) {
                if ($result['data'][0]['email']!='') {
                    
                    // se envia el email de confirmación
                    $ini = parse_ini_file('emails.ini');
                    $mail = new mailto(SMTP);
                    
                    $mensaje = sprintf($ini['CUERPO_RECUPERAR_CONTRASENA'],
                            $result['data'][0]['clave']);

                    $r = $mail->enviar_email("Recuperar Contraseña", $mensaje, "", 
                            $result['data'][0]['email'],
                            $result['data'][0]['nombre']);
                    if ($r=="") {
                        $result['status']  = 'success';
                        $result['message'] = "Clave enviada al email: ".$result['data'][0]['email'];
                        $result['message'] = "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n"
                        . "<i class=\"zmdi zmdi-thumb-up mr-3\"></i>\n<strong>Usuario registrado!</strong> "
                        . $result['message']."\n<button type=\"button\" class=\"close\" "
                        . "data-dismiss=\"alert\" aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n"
                        . "</button>\n</div>";
                    } else {
                        $result['status']='fail';
                        $result['message']="No se puedo enviar el correo electrónico.
                            Póngase en contacto con ".TITULO.".";
                    }
                    
                } else {
                    $result['status']='fail';
                    $result['message']="No tenemos registrado un email a donde enviarle su contraseña.
                        Por favor póngase en contacto con nosotros para actualizar su información de
                        contacto.";
                }
            } else {
                $result['status']='fail';
                $result['message']="Código de cliente no registrado. Si considera
                    que es un error, póngase en contacto con ".TITULO.".";
            }
        } else {
            $result['status']='fail';
            $result['message'] = "Introduzca su codigo de cliente.";
            
        }
        if ($result['status']=='fail') {
            $result['message'] = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n"
            . "<i class=\"zmdi zmdi-close-circle-o mr-3\"></i>\n<strong>Ups!</strong> "
            . $result['message']."\n<button type=\"button\" class=\"close\" data-dismiss=\"alert\" "
            . "aria-label=\"Close\">\n<i class=\"zmdi zmdi-close\"></i>\n</button>\n</div>";
        }
        return $result;
    }
   
    public static function esPropietarioLogueado() {
        session_start();
        if (!isset($_SESSION['status']) || $_SESSION['status'] != 'logueado' || !isset($_SESSION['usuario'])) {
            header("location:".ROOT);
            die();
        }
    }
    
    /**
     * Cierra la sesión 
     */
    public function logout() {
        session_start();
        
        if (isset($_SESSION['id_sesion'])) {
            
            $this->exec_query("update sesion set fin=now() where id=".$_SESSION['id_sesion']);
        }
        
        if (isset($_SESSION['status'])) {
            unset($_SESSION['status']);
            unset($_SESSION['usuario']);
            session_unset();
            session_destroy();

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 1000);
            }
            header("location:" . ROOT );
        }
    }
    
    public static function listarPropietariosClavesActualizadas() {
        $query = "select propiedades.id_inmueble, propiedades.apto , propietarios.id, propietarios.clave 
            from propietarios join propiedades
            on propietarios.cedula = propiedades.cedula
            where propietarios.cambio_clave=1";
        return db::query($query);
    }
    
     public function obtenerPropietariosActualizados() {
        $query = "SELECT p . * , pr.id_inmueble, pr.apto
            FROM propietarios p
            JOIN propiedades pr ON p.cedula = pr.cedula
            WHERE p.modificado = 1 Order By pr.id_inmueble ASC";
        
        return $this->dame_query($query);
    }
    
    public function listarPropietariosConEmail($id = null) {
       $query = "SELECT p.*,pro.apto, pro.id_inmueble FROM propietarios p join propiedades pro on p.cedula = pro.cedula where p.email !=''";
        if($id != null) {
            $query.= " and pro.id_inmueble='".$id."'";
        }
        $query.=" order by pro.apto";
        //$query.= " limit 0,10";
        
        return $this->dame_query($query);
    }
    
    public function envioMasivoEmail($asunto,$template, $id = null) {
        $propieatarios = $this->listarPropietariosConEmail($id);
        if ($propieatarios['suceed'] && count($propieatarios['data'])>0) {
            // cargamos el template
            if (file_exists($template)) {
                $contenido_original = file_get_contents($template);
                if ($contenido_original=='') {
                    echo "No se puedo cargar el contenido de ".$template;
                    die();
                }
                // enviamos el email a los destinatarios
                $resultado='';
                $n=1;
                $mail = new mailto(mailPHP);
                foreach ($propieatarios['data'] as $propietario) {
                    
                    $contenido = $contenido_original;
                    // hacemos la personalizacion del contenido
                    foreach ($propietario as $key => $value) {
                        $contenido = str_replace("[".$key."]", $value, $contenido);
                    }
                    //echo $contenido;
                    // aquí enviamos el email
                    $destinatario = $propietario['email'];
                    //$destinatario = 'jesusvelasquez757@gmail.com ';
                    $r = $mail->enviar_email($asunto, $contenido, '', $destinatario, $propietario['nombre']);
                    
                    if ($r=='') {
                        $resultado.= $n.".- Mensaje enviado a ".$destinatario." Ok!\n";
                    } else {
                        $resultado.= $n.".- Mensaje enviado a ".$destinatario." Falló\n";
                    }
                    $n++;
                }
                echo nl2br($resultado);
            } else {
                echo $template." no existe";
            }
        } else {
            die('No hay propietarios con email registrado'.$id);
        }
    }
}