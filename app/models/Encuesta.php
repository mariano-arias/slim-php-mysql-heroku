<?php

class Encuesta
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

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM encuestas where puntuacionRestaurante > 6 order by puntuacionRestaurante desc");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}
?>