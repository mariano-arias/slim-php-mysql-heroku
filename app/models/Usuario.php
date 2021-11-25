<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $apellido;
    public $nombre;
    public $sector;
    public $operaciones; //lo tomo de las tablas
    public $estado; //1 activo, 0 borrado, 2 suspendido

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO usuarios (usuario, clave, apellido, nombre, sector, operaciones, estado ) 
                VALUES (:usuario, :clave, :apellido, :nombre, :sector, :operaciones, :estado)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':operaciones', $this->operaciones, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        try{

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT id, usuario, clave, apellido, nombre, sector, operaciones, estado FROM usuarios");
                $consulta->execute();
                
                return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
            }catch(Exception $ex){
                echo "Se ha producido un error.".$ex->getMessage();
            }
    }

    public static function obtenerUsuarioByUsername($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT id, usuario, clave, apellido, nombre, sector, operaciones, estado FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioById($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, apellido, nombre, sector, operaciones, estado FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($userId, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        if($estado == 2){
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE id = :id");
            $consulta->bindValue(':id', $userId, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        }
        if ($estado==0){
            $fecha = new DateTime(date("d-m-Y"));
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado, fechaBaja = :fechaBaja WHERE id = :id");
            $consulta->bindValue(':id', $userId, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
            $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        }
        //$consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        //$consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja, estado = 0 WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function Listar($sector){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, apellido, nombre, sector, operaciones, estado FROM usuarios WHERE sector LIKE :sector");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function GetAllForCSV()
    {
        try{

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT id, usuario, apellido, nombre, sector, estado FROM usuarios");
                $consulta->execute();
                
                return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
            }catch(Exception $ex){
                echo "Se ha producido un error.".$ex->getMessage();
            }
    }
    public static function SaveDataCSV($lista){

        $lista = Usuario::obtenerTodos();

        //set column headers
        $fields = array('ID', 'Usuario', 'Clave', 'Apellido', 'Nombre', 'Sector', 'Operaciones', 'Estado');

        return FileManager::SaveToCSV($lista, $fields);
    }

}