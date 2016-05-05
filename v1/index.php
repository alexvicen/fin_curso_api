<?php
error_reporting (0);
require_once "vistas/VistaJson.php";
require_once "utilidades/ExcepcionApi.php";
require_once "modelos/Usuario.php";
require_once "modelos/Personaje.php";

const ESTADO_METODO_NO_PERMITIDO=403;
$objeto = NULL;
$vista = new VistaJson();
//die($_REQUEST["PATH_INFO"]);

$peticion = $_REQUEST["PATH_INFO"];


$array = explode("/", $peticion);
//die($array[1]);

$recurso = $array[0];
$peticion = $array[1];

//$recurso = array_shift($peticion);

$recursos_existentes = array('personajes', 'usuarios');

// Comprobar si existe el recurso
if (!in_array($recurso, $recursos_existentes)) {
	die("no existe el recurso solicitado");
}

$metodo = strtolower($_SERVER['REQUEST_METHOD']);

    switch ($recurso){
        case 'usuarios':
            $objeto=new Usuario();
            enrutador($metodo,$objeto,$peticion,$vista);
            break;
        case 'personajes':
            $objeto=new Personaje();
            enrutador($metodo,$objeto,$peticion,$vista);
            break;
        default:
            die("algo falla");
    }

function enrutador($metodo,$objeto,$peticion,$vista)    {
        switch ($metodo) {
        case 'get':
            $vista->imprimir($objeto::get($peticion));
            break;

        case 'post':
            $vista->imprimir($objeto::post($peticion));
            // Procesar método post
            break;
        case 'put':
            $vista->imprimir($objeto::put($peticion));
            break;

        case 'delete':
            // Procesar método delete
            break;
        default:
            $vista->estado = 405;
            $cuerpo = [
                "estado" => ESTADO_METODO_NO_PERMITIDO,
                "mensaje" => utf8_encode("Esa solicitud es ilegal")
            ];
            $vista->imprimir($cuerpo);
        }

}

set_exception_handler(function ($exception) use ($vista) {
		$cuerpo = array(
			"estado" => $exception->estado,
			"mensaje" => $exception->getMessage()
		);
		if ($exception->getCode()) {
			$vista->estado = $exception->getCode();
		} else {
			$vista->estado = 500;
		}

		$vista->imprimir($cuerpo);
	}
);

throw new ExcepcionApi(2, "Error con estado 2", 404);

?>