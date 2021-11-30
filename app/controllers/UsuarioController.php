<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  

  public function VerificarLogin($request, $response, $args)
  {

      $parametros = $request->getParsedBody();

      $usuario = $parametros['username'];
      $clave = $parametros['password'];

      $userIn = new Usuario();
      $userIn->usuario = $usuario;
      $userIn->clave = $clave;

      $usuario = Usuario::obtenerUsuarioByUsername($userIn->usuario);
// var_dump($usuario);
      if($usuario)
      {
        if(password_verify($userIn->clave, $usuario->clave))
        {
          if($usuario->estado==1){

            $response->getBody()->write(AuthJWT::CrearToken($usuario));
            return $response
            ->withHeader('Content-Type', 'application/json');
            //$payload = json_encode(array("mensaje" => "Usuario logueado con exito"));
          }else{
            $payload = json_encode(array("mensaje" => "Usuario dado de baja o suspendido"));
          }
        }else{
          $payload = json_encode(array("mensaje" => "Usuario correcto, pass incorrecto"));
        }

      }else{
        $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
      }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
  }
  
  public function CargarUno($request, $response, $args)
  {
        $sectores = ["barra", "cerveceria", "cocina", "mozo", "socio"];
        $flag = false;

        $parametros = $request->getParsedBody();
//var_dump($parametros);
        if(isset($parametros['username']) && isset($parametros['clave']) 
            && isset($parametros['apellido']) && isset($parametros['nombre']) && isset($parametros['sector']) 
            && !empty($parametros['username']) && !empty($parametros['clave'])
            && !empty($parametros['apellido']) && !empty($parametros['nombre']) && !empty($parametros['sector']))
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
            if(!Usuario::obtenerUsuarioByUsername(trim($parametros['username'])))
            {
              $usr = new Usuario();
              // Creamos el usuario
              $usr->clave = trim($parametros['clave']);
              $usr->usuario = trim($parametros['username']);
              $usr->nombre =trim($parametros['nombre']);
              $usr->apellido =trim($parametros['apellido']);
              $usr->operaciones = 0;
              $usr->sector =trim($parametros['sector']);
              $usr->estado = 1;
              $id = $usr->crearUsuario();
              $payload = json_encode(array("mensaje" => "Usuario creado con exitosssss! Su numero de identificacion es: ".$id));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "UserName ya existe, elija otro o ingrese con su pass"));
            }
          }
          else
          {
            $validos ="";
            for($i = 0; $i <count($sectores); $i++){
              $validos = $validos . $sectores[$i]. " - ";
        }
            $payload = json_encode(array("mensaje" => "Sector solo puede ser uno habilitado: ".$validos));
                       // $sectores[0]. " - ".$sectores[1]. " - ".$sectores[2]. " - ".$sectores[3]." - ".$sectores[4]));
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
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuarioByUsername($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUnoById($request, $response, $args)
    {
        $usr = $args['id'];
        $usuario = Usuario::obtenerUsuarioById($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $userId = $parametros['usuarioId'];
        $estado=$parametros['estado'];
        Usuario::modificarUsuario($userId, $estado);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $args['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPorSector($request, $response, $args){

      $sector = $args['sector'];
var_dump($sector);
      $lista= Usuario::Listar($sector);
      $payload = json_encode(array("listaUsuario" => $lista));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');

    }

    
  public function SaveCSV($request, $response){

    $lista = Usuario::obtenerTodos();

    if(Usuario::SaveDataCSV($lista)){

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
