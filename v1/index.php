<?php

require_once "vistas/VistaJson.php";
require_once "utilidades/ExcepcionApi.php";
require_once "modelos/Usuario.php";
require_once "modelos/Personaje.php";

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
            break;
        default:
            die("entrando");
    }

function enrutador($metodo,$objeto,$peticion,$vista)    {
        switch ($metodo) {
        case 'get':
            $vista->imprimir($objeto::get($peticion));
            break;

        case 'post':
            $resultado=$objeto::post($peticion);
            $vista->imprimir($resultado);
            // Procesar método post
            break;
        case 'put':
            // Procesar método put
            break;

        case 'delete':
            // Procesar método delete
            break;
        default:
            // Método no aceptado
        }

}

$vista = new VistaJson();

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