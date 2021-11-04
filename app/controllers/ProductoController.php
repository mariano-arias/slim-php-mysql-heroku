<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable{
    
    public function CargarUno($request, $response, $args)
    {
      $sectores = ["barra", "cerveceria", "cocina", "candyBar"];
      $flag = false;

      $parametros = $request->getParsedBody();
//var_dump($parametros);
      if(isset($parametros['producto']) && isset($parametros['precio']) && isset($parametros['sector']) 
          && !empty($parametros['producto']) && !empty($parametros['precio']) && !empty($parametros['sector']))
      {
        foreach ($sectores as $sec)
        {
          if($parametros['sector']===$sec)
          {
            $flag = true;
          }
        }

        if($flag)
        {
          //if(!Usuario::obtenerUsuarioByUsername(trim($parametros['username'])))
          //{
            $usr = new Producto();

            $usr->producto = trim($parametros['producto']);
            $usr->precio = trim($parametros['precio']);
            $usr->sector =trim($parametros['sector']);
            $id = $usr->crearProducto();
            $payload = json_encode(array("mensaje" => "Producto incorporado al menu! Su numero de identificacion es: ".$id));
          // }
          // else
          // {
          //   $payload = json_encode(array("mensaje" => "UserName ya existe, elija otro o ingrese con su pass"));
          // }
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Sector solo puede ser uno habilitado"));
        }
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Debe completar todos los datos"));
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
    
        // Buscamos producto por id
        $prod = $args['idProducto'];
        
        $producto = Producto::obtenerProductoById($prod);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
  
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaMenú" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!empty($parametros['id']) && !empty($parametros['producto']) && !empty($parametros['precio']
        && $parametros['sector'])){
          
          $obj = new Producto();
          $obj->id = $parametros['id'];
          $obj->producto = trim($parametros['producto']);
          $obj->precio = trim($parametros['precio']);
          $obj->sector =trim($parametros['sector']);
          
          Producto::ModificarUnoById($obj);
          
          $payload = json_encode(array("mensaje" => "Producto actualizado"));
        }else{
          $payload = json_encode(array("mensaje" => "No puede haber campos vacios"));
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