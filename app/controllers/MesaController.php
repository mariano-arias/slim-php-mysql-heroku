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
        $mesa->estado = 'cerrada';
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
        $estados = ['esperando', 'comiendo', 'pagando', 'cerrada'];
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
            $aux=Mesa::ObtenerMesaById($mesa);

            if($aux){
                  if($aux->estado!=$estado)
                  {
                        $aux = $request->getHeaderLine('Authorization');
    
                        $token = trim(explode('Bearer', $aux)[1]);
                        
                        $sectorUser= AuthJWT::ObtenerData($token);

                        switch($estado){

                              case 'cerrada':
                                    if($sectorUser== 'socio'){
                                          if(Mesa::modificarMesaEstado($estado, $mesa)){
                                                $payload = json_encode(array("mensaje" => "Mesa estado modificado con exito"));
                                          }else{
                                                $payload = json_encode(array("mensaje" => "Ha habido un error"));
                                          }
                                    }else{
                                          $payload = json_encode(array("mensaje" => "Solo usuario Socio puede cerrar una mesa"));
                                    }
                              break;
                              case 'esperando':
                              case 'comiendo':
                              case 'pagando':
                                          if ($sectorUser == 'mozo') {
                                                if (Mesa::modificarMesaEstado($estado, $mesa)) {
                                                      $payload = json_encode(array("mensaje" => "Mesa estado modificado con exito"));
                                                } else {
                                                      $payload = json_encode(array("mensaje" => "Ha habido un error"));
                                                }
                                          } else {
                                                $payload = json_encode(array("mensaje" => "Solo usuario Mozo puede realizar esta modificacion"));
                                          }
                              break;
                        }
                  }else{
                        $payload = json_encode(array("mensaje" => "Error - La mesa ".$mesa." ya está en estado: ".$estado));
                  }
            }
            else
            {
                  $payload = json_encode(array("mensaje" => "Codigo mesa no encontrado"));
            }
      }else{
            $validos = "";

            for($i = 0; $i <count($estados); $i++){
                  $validos = $validos . $estados[$i]. " - ";
            }
            $payload = json_encode(array("mensaje" => "Estado no valido. Estados validos: ". $validos));
                              // $estados[0]." - ".$estados[1]." - ".$estados[2]." - ".$estados[3]));
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