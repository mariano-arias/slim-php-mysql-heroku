<?php
class Login{

    public static function Log($user){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO logs (fecha, hora, username) 
            VALUES (:fecha, :hora, :username)");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d'), PDO::PARAM_STR);
        $consulta->bindValue(':hora', date('H:i:s'), PDO::PARAM_STR);
        $consulta->bindValue(':username', $user, PDO::PARAM_STR);
        $consulta->execute();
    
        return $objAccesoDatos->obtenerUltimoId();
    }
}
?>