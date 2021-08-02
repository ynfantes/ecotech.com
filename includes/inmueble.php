<?php

/**
 * Clase que mantiene la tabla inmueble
 *
 * @autor   Edgar Messia
 * @static  
 * @package     Valoriza2.Framework
 * @subpackage	FileSystem
 * @since	1.0
 */

class inmueble extends db implements crud {

    const tabla = "inmueble";

    public function actualizar($id, $data){
        return db::update(self::tabla, $data, array("id" => $id));
    }

    public function borrar($id){
        return db::delete(self::tabla, array("id" => $id));
    }

    /**
     * Inserta el contenido en la tabla propietarios
     *
     * @param	Array	$data	Arreglo con la data
     * 
     * @return	Array	Retorna arreglo con parámetos del resultado
     * @since	1.0
     */
    public function insertar($data){
        return db::insert(self::tabla, $data);
    }

    public function listar(){
       return db::select("*", self::tabla);
    }
    
    public function ver($id){
        return db::select("*",self::tabla,array("id"=>$id));
    }

    public function borrarTodo() {
        return db::delete(self::tabla);
    }
    
    public function estadoDeCuenta($id) {
        return db::select("*","inmueble_deuda_confidencial",Array("id_inmueble"=>$id));
    }
    
    public function listarInmueblesPorPropietario($cedula) {
        $consulta = "select * from inmueble i join propiedades p on i.id = p.id_inmueble where p.cedula=".$cedula;
        return db::query($consulta);
        
    }
    
    public function movimientoFacturacionMensual($inmueble,$total=null) {
        $query = "select facturacion_mensual.*, inmueble.nombre_inmueble from facturacion_mensual join inmueble ON facturacion_mensual.id_inmueble = inmueble.id where id_inmueble='$inmueble' and periodo >= date_add((select max(periodo) from facturacion_mensual where id_inmueble='$inmueble'), INTERVAL -5 MONTH) order by periodo ASC";
        return db::query($query);
//        $consulta = "select * from facturacion_mensual ";
//        if ($total > 6) {
//            $inicio = $total - 6;
//            $consulta.= "LIMIT $inicio ,$total";
//        }
//        return $this->query($consulta);
//        //return db::select("*","facturacion_mensual",array("id_inmueble"=>$inmueble),"",Array("periodo" =>"asc"));
    }
    public function totalPeriodosFacturados($inmueble) {
        $consulta = "select count(*) as total from facturacion_mensual";
        $r = $this->query($consulta);
        
        return $r['data'][0]['total'];
    }
    public function movimientoCobranzaMensual($inmueble) {
        //return db::select("*","cobranza_mensual",array("id_inmueble"=>$inmueble),"",Array("periodo" =>"asc"));
        $query = "select cobranza_mensual.*,inmueble.nombre_inmueble from cobranza_mensual join inmueble on cobranza_mensual.id_inmueble = inmueble.id where id_inmueble='$inmueble' and periodo >= date_add((select max(periodo) from facturacion_mensual where id_inmueble='$inmueble'), INTERVAL -5 MONTH) order by periodo ASC";
        return db::query($query);
    }
    
    public function insertarEstadoDeCuentaInmueble($data) {
        return db::insert("inmueble_deuda_confidencial", $data);
    }
    
    public function insertarFacturacionMensual($data) {
        
        $query = "insert into facturacion_mensual(id_inmueble,periodo,facturado) "
                . "VALUES('".$data['id_inmueble']."','".$data['periodo']."','".$data['facturado']."') ON DUPLICATE KEY "
                . "UPDATE facturado='".$data['facturado']."'";
        
        return db::exec_query($query);
    }
    
    public function insertarCobranzaMensual($data) {

        $query = "insert into cobranza_mensual(id_inmueble,periodo,monto) "
                . "VALUES('".$data['id_inmueble']."','".$data['periodo']."','".$data['monto']."') ON DUPLICATE KEY "
                . "UPDATE monto='".$data['monto']."'";
        
        return db::exec_query($query);
    }
    
    public function insertarGrupo($data) {
        return db::insert("grupo", $data,"IGNORE");
    }
    
    public function insertarGrupoPropietario($data) {
        return db::insert("grupo_propietario", $data,"IGNORE");
    }
    
    public function obtenerSemaforo($id_grupo) {
        $consulta = "SELECT g.descripcion,d.* FROM `grupo` g join `grupo_propietario` p on g.id_inmueble = p.id_inmueble and g.id = p.id_grupo 
        join  `inmueble_deuda_confidencial` d on d.id_inmueble = p.id_inmueble and d.apto = p.apto  where g.id=".$id_grupo." order by d.id_inmueble, d.apto";
        
        return db::query($consulta);
    }
    
    public function obtenerIdGrupo($id_inmueble,$apto) {
        $consulta = "select * from grupo_propietario where id_inmueble='$id_inmueble' and apto='$apto'";
        
        $r = $this->query($consulta);
        
        if ($r['suceed'] && count($r['data'])>0) {
            return $r['data'][0]['id_grupo'];
        } else {
            return 0;
        }
    }
    public function agregarCuentaInmueble($data) {
        return db::insert("inmueble_cuenta", $data,"IGNORE");
    }
    public function obtenerCuentasBancariasPorInmueble($inmueble) {
        return db::select("*","inmueble_cuenta",Array("id_inmueble"=>$inmueble));
    }
    
    public function listarCuentasBancariasPorInmuebleBanco($inmueble,$banco){
        return db::select("*","inmueble_cuenta",Array("id_inmueble"=>$inmueble,"banco"=>$banco));
    }
    
    public function obtenerNombreBancoPorNumeroCuenta($inmueble,$numero_cuenta){
        return db::select("*","inmueble_cuenta",Array("id_inmueble"=>$inmueble,"numero_cuenta"=>$numero_cuenta));
    }
    
    public function obtenerNumeroCuentaPorInmuebleBanco($inmueble,$banco) {
        return db::select("*","inmueble_cuenta",Array("id_inmueble"=>$inmueble,"banco"=>$banco));
    }
    
    public function listarBancosActivos(){
        return db::select("*","bancos",Array("inactivo"=>0));
    }
            
}
