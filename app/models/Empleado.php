<?php

class Empleado extends Usuario{

    public $loginDateTime; // no va aca , no tiene sentido
    public $apellido;
    public $nombre;
    public $operaciones; //lo tomo de las tablas
    public $funcion; //bartender cervecero cocinero mozo  - socio ?
    public $sector;
    public $estado;
    public $activo;
    public $suspendido;
    public $deleted;

}
?>