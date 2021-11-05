<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable{
    
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pedido->id =  substr(str_shuffle($permitted_chars), 0, 5);

        $pedido->idMesa = $parametros['idMesa'];
        $pedido->idMozo= $parametros['idMozo'];
        $pedido->clienteNombre= $parametros['clienteNombre'];
        $pedido->idProducto= $parametros['idProducto'];
        $pedido->cantidad= $parametros['cantidad'];

        $pedido->precio =Producto::obtenerProductoById($pedido->idProducto)->precio * $pedido->cantidad;
        $pedido->sector =Producto::obtenerProductoById($pedido->idProducto)->sector;

        $pedido->estado = 'En espera';
        
        $pedido->crearPedido();

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
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("Pedidos: " => $lista));

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
        $tiempoEstimado = $parametros['tiempoEstimado'];

        $aux = Pedido::obtenerPedidoById($id);
      
        if($aux){
          $aux->idEmpleadoPreparacion = $idEmpleado;
          $aux->estado = $estado;
          $aux->tiempoEstimado = $tiempoEstimado;
          Pedido::ModificarUnoById($aux);

          $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
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
}
?>