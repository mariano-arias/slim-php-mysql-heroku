<?php

//require_once './AuthJWT.php';

use Slim\Psr7\Message;
use Slim\Psr7\Response;

class MWValidacion{
    
    
    public static function ValidarToken($request, $handler) {
        
        $aux = $request->getHeaderLine('Authorization');
        if ($aux){
            
            $token = trim(explode('Bearer', $aux)[1]);
            
            if($token){
                
                AuthJWT::VerificarToken($token);
                
                $response = $handler->handle($request);
                
                return $response;
            }
        }
        $response = new Response();
        $payload = json_encode(array("mensaje" => "Usuario no autenticado"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function ValidarSocio($request, $handler){
    
        $aux = $request->getHeaderLine('Authorization');
    
        $token = trim(explode('Bearer', $aux)[1]);
        
        $user= AuthJWT::ObtenerData($token);

       if($user === 'socio'){

           echo "dale gas";

           $response = $handler->handle($request);
        }
    
       // return $response;
    }
}

?>