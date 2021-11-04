<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable{
    
   public $estados = ['esperando', 'comiento', 'pagando', 'cerrada'];
  
  public function CargarUno($request, $response, $args)
  {

        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $mesa->id =  substr(str_shuffle($permitted_chars), 0, 5);
        $mesa->estado = 'esperando';
        $mesa->crearMesa();
        $payload = json_encode(array("mensaje" => "Mesa creada con exito. Mesa Codigo: ". $mesa->id. " - Estado: ".$mesa->estado));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['mesa'];
        $mesa = Mesa::obtenerMesaById($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $estados = ['esperando', 'comiento', 'pagando', 'cerrada'];
        $flag = false;
        $parametros = $request->getParsedBody();
       
        $estado = $parametros['estado'];
        $mesa = $parametros['id'];
      
        foreach ($estados as $est){
            if($est === $estado){
                  $flag = true;
            }
        }

        if($flag){
            if(Mesa::modificarMesaEstado($estado, $mesa)){
                  $payload = json_encode(array("mensaje" => "Mesa estado modificado con exito"));
            }else{

                  $payload = json_encode(array("mensaje" => "Codigo mesa no encontrado"));
            }
      }else{
            $payload = json_encode(array("mensaje" => "Estado no valido"));
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