<?php

use Illuminate\Support\Arr;

require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './managers/FileManager.php';

class PedidoController extends Pedido implements IApiUsable{
    
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pedido->id =  substr(str_shuffle($permitted_chars), 0, 5);
        $pedido->idMesa = $parametros['idMesa'];
        $pedido->idMozo= PedidoController::GetMozoIdByToken($request);
        $pedido->clienteNombre= $parametros['clienteNombre'];
        $pedido->idProducto= $parametros['idProducto'];
        $pedido->cantidad= $parametros['cantidad'];
        
        $pedido->precio =Producto::obtenerProductoById($pedido->idProducto)->precio * $pedido->cantidad;
        $pedido->sector =Producto::obtenerProductoById($pedido->idProducto)->sector;

        $pedido->estado = 'En espera';

        $pedido->crearPedido();

        $mesa = new Mesa();
        $mesa = $mesa->modificarMesaEstado('esperando', $pedido->idMesa);

        if (isset($_FILES["photo"])){
          
          $pedido->photoPath=FileManager::SaveFile($request, $pedido->id);
          $pedido->SetPhotoPath();
        }

        $payload = json_encode(array("mensaje" => "Pedido creado con exito. Su Codigo es de pedido es: ".$pedido->id));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
    
        // Buscamos producto por id
        $id = $args['pedido'];
        
        $pedido = Pedido::obtenerPedidoById($id);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $aux = $request->getHeaderLine('Authorization');

        $token = trim(explode('Bearer', $aux)[1]);

        $sector= AuthJWT::ObtenerData($token);

        //var_dump($sector);
        switch($sector)
        {

          case 'socio':
            $lista = Pedido::obtenerTodos();
          $payload = json_encode(array("Pedidos: " => $lista));
          break;
          case 'barra':
          case 'cerveceria':
          case 'cocina':
            $lista = Pedido::obtenerPedidoBySector($sector);
            $payload = json_encode(array("Pedidos sector: " => $lista));
          break;
          case 'mozo':
            $payload = json_encode("Funcion no habilitada para mozo");
            break;
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $idEmpleado = $parametros['idEmpleadoPreparacion'];
        $estado = $parametros['estado'];
        if(isset( $parametros['tiempoEstimado'])){
          $tiempoEstimado = $parametros['tiempoEstimado'];
        }

       $aux = Pedido::obtenerPedidoById($id);
       //var_dump($aux);
      
        if($aux){

          if ($aux->estado != $estado){

            switch ($estado){
              case 'en preparacion':
                $aux->idEmpleadoPreparacion = $idEmpleado;
                $aux->estado = $estado;
                $aux->tiempoEstimado = $tiempoEstimado;
                Pedido::ModificarEnPreparacion($aux);
                break;
              case 'listo':
                $aux->idEmpleadoPreparacion = $idEmpleado;
                $aux->estado = $estado;
                Pedido::ModificarTerminar($aux);
                break;
            }

            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
          }else{
            $payload = json_encode(array("mensaje" => "Pedido ya está en estado: ".$estado));
          }
        }else{
          $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }


    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function GetMozoIdByToken($request){
      
      $aux = $request->getHeaderLine('Authorization');
    
      $token = trim(explode('Bearer', $aux)[1]);
      
      $id= AuthJWT::ObtenerId($token);
      return $id;
    }
}
?>