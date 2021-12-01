<?php

class Estadistica
{
    public $id;
    public $idMesa;
    public $idPedido;
    public $puntuacionMesa;
    public $puntuacionMozo;
    public $puntuacionRestaurante;
    public $puntuacionCocinero;
    public $texto; //hasta 66 caracteres

    public function crear(){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO encuestas (idMesa, idPedido, puntuacionMesa, puntuacionMozo, puntuacionRestaurante, puntuacionCocinero, texto) 
                VALUES (:idMesa, :idPedido, :puntuacionMesa, :puntuacionMozo, :puntuacionRestaurante, :puntuacionCocinero, :texto)");
        
        // $fecha = new DateTime(date("d-m-Y"));
        // $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionRestaurante', $this->puntuacionRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':texto', $this->texto, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT idMesa, COUNT(idMesa) as registros FROM `pedidos` GROUP by idMesa ORDER by registros desc");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerPedidoNoEntregado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT id as pedido , fecha, horaIn, horaOut, tiempoEstimado as estimado, TIMESTAMPDIFF(minute, horaIn, horaOut) as transcurrido FROM pedidos WHERE TIMESTAMPDIFF(minute, horaIn, horaOut) > tiempoEstimado");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerProductoMasVendido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT a.idProducto as productoNumero, b.producto as Nombre, count(a.idProducto) as Ventas 
                FROM pedidos a left join productos b on a.idProducto=b.id 
                GROUP by a.idProducto
                order by ventas desc");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obteneroperacionesPorSector()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT sector, count(sector) as operaciones FROM `pedidos` GROUP BY sector");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
    
    public static function GetEmpleadoLogIn($user)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM logs WHERE username like  :user");
            $consulta->bindValue(':user', $user, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
}
//SELECT idMesa, COUNT(idMesa) as registros FROM `pedidos` GROUP by idMesa ORDER by registros desc
?>