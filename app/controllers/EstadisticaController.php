<?php
require_once './models/Estadistica.php';
require_once './interfaces/IApiUsable.php';

class EstadisticaController extends Estadistica implements IApiUsable{

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['mesa']) && $parametros['pedido']){

            if( ctype_digit($parametros['puntuacionMesa']) &&
                ctype_digit($parametros['puntuacionMozo']) &&
                ctype_digit($parametros['puntuacionRestaurante']) &&
                ctype_digit($parametros['puntuacionCocinero']) ){

                    if(strlen($parametros['texto'])<66){
                        $aux= Pedido::obtenerPedidoById($parametros['pedido']);
                        if($aux){
                            if($aux->idMesa == $parametros['mesa']){

                                $encuesta = new Encuesta();
                                $encuesta->idMesa = $parametros['mesa'];
                                $encuesta->idPedido = $parametros['pedido'];
                                $encuesta->puntuacionMesa =$parametros['puntuacionMesa'];
                                $encuesta->puntuacionMozo= $parametros['puntuacionMozo'];
                                $encuesta->puntuacionRestaurante = $parametros['puntuacionRestaurante'];
                                $encuesta->puntuacionCocinero = $parametros['puntuacionCocinero'];
                                $encuesta->texto = $parametros['texto'];
                                
                                //var_dump($encuesta);
                                $encuesta->Crear();

                                $payload = json_encode(array("mensaje" => "Encuesta grabada con exito"));
                            }else{
                                $payload = json_encode(array("mensaje" => "No coincide mesa con pedido."));
                            }
                        }else{
                            $payload = json_encode(array("mensaje" => "No se encontro pedido"));
                        }
                    }else{$payload = json_encode(array("mensaje" => "Texto ingresado excede los caracteres permitidos"));}
                }else{
                    $payload = json_encode(array("mensaje" => "Puntuacion debe ser con numeros del 1 al 10"));
                }
        }else{
            $payload = json_encode(array("mensaje" => "Mesa y Pedido son datos obligatorios"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // // Buscamos usuario por nombre
        // $usr = $args['usuario'];
        // $usuario = Usuario::obtenerUsuario($usr);
        // $payload = json_encode($usuario);

        // $response->getBody()->write($payload);
        // return $response
        //   ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        //$path = $_SERVER['PATH_INFO']??"";
        $parametros = $request->getParsedBody();
       
        $usr = $args['empleado'];


        $path = $_SERVER['REQUEST_URI']??"";

        //var_dump($request['REQUEST_URI']);

        //$path = $request->REQUEST_URI;
        $path =explode('/',$path);
       // echo $path[3];

       switch($path[3]){
           case 'mesas':
            $lista = Estadistica::obtenerMesaMasUsada();
            $payload = json_encode(array("Mesas más usadas: " => $lista));
            break;
            case 'pedidos':
                $lista = Estadistica::obtenerPedidoNoEntregado();
                $payload = json_encode(array("Pedidos no entregados en tiempo estimado: " => $lista));
            break;
            case 'productos':
                $lista = Estadistica::obtenerProductoMasVendido();
                $payload = json_encode(array("Pedidos mas vendido: " => $lista));
            break;
            case 'empleados':
                $lista = Estadistica::GetEmpleadoLogIn($usr);
                $payload = json_encode(array("Logins empleado: " => $lista));
            break;
            case 'operaciones':
                $lista = Estadistica::obteneroperacionesPorSector();
                $payload = json_encode(array("Cantidad de operaciones por sector: " => $lista));
            break;

       }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();

        // $nombre = $parametros['nombre'];
        // Usuario::modificarUsuario($nombre);

        // $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        // $response->getBody()->write($payload);
        // return $response
        //   ->withHeader('Content-Type', 'application/json');
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
}
?>