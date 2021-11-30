<?php

class Mesa
{
    public $id; //Las mesas tienen un código de identificación único (de 5 caracteres)
    //public $fecha;
    //public $horaIn;
    //public $horaOut;
    public $estado; // esperando, comiento, pagando, cerrada
    public $operaciones;
    public $facturacion;

    
    
    public function __construct()
    {

    }

    public function set($obj, $value){
        $obj->id = $value;
    }

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO mesas ( id, estado ) 
                VALUES ( :id, :estado)");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return true;
    }
    public function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT id, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public function obtenerMesaById($mesa){
       // var_dump($mesa);
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT id, estado FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function GetMesaById($mesa){
        // var_dump($mesa);
         
         $objAccesoDatos = AccesoDatos::obtenerInstancia();
         $consulta = $objAccesoDatos->prepararConsulta(
             "SELECT id, estado FROM mesas WHERE id = :id");
         $consulta->bindValue(':id', $mesa, PDO::PARAM_STR);
         $consulta->execute();
 
         return $consulta->fetchObject('Mesa');
     }

    public static function obtenerMesaLibre(){
        // var_dump($mesa);
         
         $objAccesoDatos = AccesoDatos::obtenerInstancia();
         $consulta = $objAccesoDatos->prepararConsulta(
             "SELECT * FROM mesas WHERE estado like 'cerrada'");
         
         $consulta->execute();
 
         return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
     }

    public function modificarMesaEstado($estado, $mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE mesas SET estado =:estado WHERE id =:id");
        $consulta->bindValue(':id', $mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        if($consulta->execute()){
            return true;
        }
        return false;
    }
}
?>