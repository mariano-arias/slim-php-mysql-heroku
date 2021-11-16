<?php

//require_once './AuthJWT.php';

use Slim\Psr7\Message;
use Slim\Psr7\Response;

class ValidacionMW{
    
    
    public static function ValidarToken($request, $handler) {
        
        $aux = $request->getHeaderLine('Authorization');
        if ($aux){
            
            $token = trim(explode('Bearer', $aux)[1]);
            
            if($token){
                
                AuthJWT::VerificarToken($token);
                
                $response = $handler->handle($request);
                //echo "pasé Validar token";
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

           //echo "dale gas";

           $response = $handler->handle($request);

           return $response;
        }

        $response = new Response();
        $payload = json_encode(array("mensaje" => "Usuario no autorizado para esta funcion"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
       // return $response;
    }

    public static function ValidarMozo($request, $handler){
    
        //echo "entro mozo validar";

        $aux = $request->getHeaderLine('Authorization');
    
        $token = trim(explode('Bearer', $aux)[1]);
        
        $user= AuthJWT::ObtenerData($token);

       if($user === 'mozo'){

           echo "dale gas";

           $response = $handler->handle($request);

           return $response;
        }

        $response = new Response();
        $payload = json_encode(array("mensaje" => "Usuario no autorizado para esta funcion"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
       // return $response;
    }

    public static function ValidarSector($request, $handler){

        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $pedido = Pedido::obtenerPedidoById($id);
        //var_dump($pedido);

        $aux = $request->getHeaderLine('Authorization');
    
        $token = trim(explode('Bearer', $aux)[1]);
        
        $sector= AuthJWT::ObtenerData($token);

       if($sector === $pedido->sector){

           $response = $handler->handle($request);

           return $response;
        }

        $response = new Response();
        $payload = json_encode(array("mensaje" => "Pedido no corresponde al sector del empleado"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
       // return $response;
    }

    public static function ValidarMesa($request, $handler){
    
       // echo "entro mesa validar";
        $parametros = $request->getParsedBody();
        $id = $parametros['idMesa'];

        var_dump($id);
        $mesa = new Mesa();
        $mesa= $mesa->obtenerMesaById($id);

       // var_dump($aux);

        if($mesa->id != null){
            if($mesa->estado == 'cerrada'){
                $response = $handler->handle($request);
                return $response;
            }else{
                $payload = json_encode(array("mensaje" => "La mesa ya está ocupada"));
            }
        }else{
            $payload = json_encode(array("mensaje" => "Codigo mesa no encontrado"));

        }

        $response = new Response();

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');

    }
    public static function ValidarListarSector($request, $handler){

        // $parametros = $request->getParsedBody();
        // $id = $parametros['id'];
        // $pedido = Pedido::obtenerPedidoById($id);
        // //var_dump($pedido);

        //     $aux = $request->getHeaderLine('Authorization');

        //     $token = trim(explode('Bearer', $aux)[1]);
        
        //     $sector= AuthJWT::ObtenerData($token);

        //     PedidoController::TraerTodos();

        //     $response = $handler->handle($request);

        //    return $response;

        // $response = new Response();
        // $payload = json_encode(array("mensaje" => "Usuario no autorizado para esta funcion"));
        // $response->getBody()->write($payload);
        // return $response->withHeader('Content-Type', 'application/json');
       // return $response;
    }
}

?>