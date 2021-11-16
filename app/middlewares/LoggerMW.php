<?php
require_once './models/Login.php';

class LoggerMW
{
    public static function LogOperacion($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['username'];
        $usuario = Usuario::obtenerUsuarioByUsername($usuario);
        
        if($usuario)
        {
            Login::Log($usuario->usuario);

            $response = $handler->handle($request);
                
            return $response;
        }

        $response = $handler->handle($request);
        return $response;
    }
}