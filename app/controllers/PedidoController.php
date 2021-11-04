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

        $nombre = $parametros['nombre'];
 

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

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