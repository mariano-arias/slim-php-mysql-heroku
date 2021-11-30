<?php

//require_once './AuthJWT.php';

use Firebase\JWT\ExpiredException;
use Slim\Psr7\Message;
use Slim\Psr7\Response;

class ValidacionMW{
    
    
    public static function ValidarToken($request, $handler) 
    {
        try{

            $aux = $request->getHeaderLine('Authorization');
            if ($aux){
                
                $token = trim(explode('Bearer', $aux)[1]);
                
                if($token){
                    
                    AuthJWT::VerificarToken($token);
                    
                    $response = $handler->handle($request);
                    //echo "Valido token";
                    return $response;
                }
            }
        }catch(ExpiredException $ex){
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Token vencido"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }catch(DomainException ){
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Token no valido"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }catch(Exception){

        }
        $response = new Response();
        $payload = json_encode(array("mensaje" => "Usuario no autenticado"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function ValidarSocio($request, $handler){
    
        try{

            $aux = $request->getHeaderLine('Authorization');
            
            $token = trim(explode('Bearer', $aux)[1]);
            
            $user= AuthJWT::ObtenerData($token);
            
            if($user === 'socio'){
                
                $response = $handler->handle($request);
                
                return $response;
            }
        }catch(ExpiredException $ex){
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Token vencido"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }catch(DomainException ){
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Token no valido"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }catch(Exception){

        }
            
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Usuario no autorizado para esta funcion"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');

    }

    public static function ValidarMozo($request, $handler)
    {

        try {

            $aux = $request->getHeaderLine('Authorization');

            $token = trim(explode('Bearer', $aux)[1]);

            $user = AuthJWT::ObtenerData($token);

            if ($user === 'mozo') {

                $response = $handler->handle($request);

                return $response;
            }
        } catch (ExpiredException $ex) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Token vencido"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (DomainException) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Token no valido"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception) {
        }

        $response = new Response();
        $payload = json_encode(array("mensaje" => "Usuario no autorizado para esta funcion"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }

    public static function ValidarSector($request, $handler){

        try{
            
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
        }catch(ExpiredException $ex){
                $response = new Response();
                $payload = json_encode(array("mensaje" => "Token vencido"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
        }catch(DomainException ){
                $response = new Response();
                $payload = json_encode(array("mensaje" => "Token no valido"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
        }catch(Exception){
        }
            
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Pedido no corresponde al sector del empleado"));
            $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }

    public static function ValidarMesa($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['idMesa'];

        //var_dump($id);
        $mesa = new Mesa();
        $mesa = $mesa->obtenerMesaById($id);

        // var_dump($aux);

        if ($mesa->id != null) {
            if ($mesa->estado == 'cerrada') {
                $response = $handler->handle($request);
                return $response;
            } else {
                $mesas = Mesa::obtenerMesaLibre();
                
                $payload = json_encode(array("La mesa ya está ocupada. Mesas libres: " => $mesas));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Codigo mesa no encontrado"));
        }


        $response = new Response();

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarPhoto($request, $handler){

        if (isset($_FILES["photo"])){

            $fileType =$_FILES['photo']['type'];
            //var_dump($fileType);
            
            if(($fileType=='image/jpg') || ($fileType=='image/jpeg') || ($fileType=='image/png') ){
                
                $response = $handler->handle($request);
                
                return $response;
            }
            
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Imagen solo puede ser jpg o png"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response = $handler->handle($request);
                
        return $response;
    }
}
