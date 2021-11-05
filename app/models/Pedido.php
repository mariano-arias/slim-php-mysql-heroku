<?php

class Pedido
{
    public $id; //El mozo le da un código único alfanumérico (de 5 caracteres) al cliente que le permite identificar su pedido
    public $fecha;
    public $idMesa;
    public $idMozo;
    public $clienteNombre;
    public $idProducto; //un array de productos?
    public $cantidad;
    public $precio;
    public $sector;
    public $idEmpleadoPreparacion;
    public $estado;
    public $tiempoEstimado;
    public $tiempoRealizado;
    public $foto;

    public function crearPedido(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO pedidos (id, fecha, idMesa, idMozo, clienteNombre, idProducto, cantidad, precio, sector, estado ) 
                VALUES (:id, :fecha, :idMesa, :idMozo, :clienteNombre, :idProducto, :cantidad, :precio, :sector, :estado)");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':clienteNombre', $this->clienteNombre, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

        public static function obtenerPedidoById($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function ModificarUnoById($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidos SET idEmpleadoPreparacion = :idEmpleadoPreparacion, estado = :estado, tiempoEstimado = :tiempoEstimado WHERE id = :id");
            $consulta->bindValue(':id', $pedido->id, PDO::PARAM_STR);
            $consulta->bindValue(':idEmpleadoPreparacion', $pedido->idEmpleadoPreparacion, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoEstimado', $pedido->tiempoEstimado, PDO::PARAM_INT);

        $consulta->execute();
    }
}
?>