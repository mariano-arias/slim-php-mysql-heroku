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
          $aux = Producto::obtenerProductoByName($parametros['producto']);
          if(!$aux || $aux == null){

            $p = new Producto();
            
            $p->producto = trim($parametros['producto']);
            $p->precio = trim($parametros['precio']);
            $p->sector =trim($parametros['sector']);
            $id = $p->crearProducto();
            $payload = json_encode(array("mensaje" => "Producto incorporado al menu! Su numero de identificacion es: ".$id));
          }else{
            $payload = json_encode(array("mensaje" => "Producto ya existe"));
          }

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

        if(isset($parametros['id']) && !empty($parametros['id'])){

          $producto = Producto::obtenerProductoById($parametros['id']);

          if( isset($parametros['producto']) && !empty($parametros['producto'])){
            $producto->producto=trim($parametros['producto']);
          }
          if( isset($parametros['precio']) && !empty($parametros['precio'])){
            $producto->precio=trim($parametros['precio']);
          }
          if( isset($parametros['sector']) && !empty($parametros['sector'])){
            $producto->sector=trim($parametros['sector']);
          }

          Producto::ModificarUnoById($producto);
          
          $payload = json_encode(array("mensaje" => "Producto actualizado"));
        }else{
          $payload = json_encode(array("mensaje" => "No puede haber campos vacios"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarTodos($array){



    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();

        // $usuarioId = $parametros['usuarioId'];
        // Usuario::borrarUsuario($usuarioId);

        // $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        // $response->getBody()->write($payload);
        // return $response
        //   ->withHeader('Content-Type', 'application/json');
    }

  public function GetCSV($request, $response, $args)
  {

    if ($_FILES["file"]["error"] > 0) {
      echo "Error: " . $_FILES["file"]["error"] . "<br />";
      $payload = json_encode(array("mensaje" => "Error con el archivo"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    } else {
      $aux = $_FILES['file'];

    //  var_dump($aux);
      $aux = $aux['tmp_name'];
      // var_dump($aux);
      $array = Producto::GetDataCSV($aux);


      if( $array != null){

        $p = "Producto Id N° ";
        $i = 0;
        
        foreach ($array as $aux) {

          if($aux != null){
            
            $obj = new Producto();
            
            $obj->producto = $aux[0];
            $obj->precio = $aux[1];
            $obj->sector = $aux[2];
            
            $id = $obj->crearProducto();
            
            $p .= $id . ", ";
            $i++;
          }
        }
        
        $payload = json_encode(array("mensaje" => "Productos incorporados al menu! Sus numeros de identificacion son: " . $p));
      }else{
        $payload = json_encode(array("mensaje" => "No se han incorporado productos"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
  }

  public function SaveCSV($request, $response){

    $lista = Producto::obtenerTodos();

    if(Producto::SaveDataCSV($lista)){

     // $payload = json_encode("Archivo grabado con exito.");
      
    }
    else{
      $payload = json_encode("Error. No se ha grabado el archivo.");
    }
    //$response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/json');

  }
}
?>