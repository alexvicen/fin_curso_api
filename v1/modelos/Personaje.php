<?php

require_once 'utilidades/ConexionBD.php';

class Personaje{
    //Datos de la tabla Campaña
    const NOMBRE_TABLA = "mos_personaje";
    const ID_PERSONAJE = "id_personaje";
    const NOMBRE_PERSONAJE = "nombre_personaje";
    const FK_USUARIO = "fk_usuario";
    const NIVEL = "nivel";
    const NIVEL_CASCO = "nivCasco";
    const NIVEL_ARCO = "nivArco";
    const NIVEL_ESCUDO = "nivEscudo";
    const NIVEL_GUANTES = "nivGuantes";
    const NIVEL_BOTAS = "nivBotas";
    const NIVEL_FLECHA = "nivFlecha";




    public static function get($peticion)
    {
        $cabeceras = apache_request_headers();
        if (isset($cabeceras["Authorization"])) {
            $idUsuario = $cabeceras["Authorization"];
        }
        return self::obtenerPersonajePorIdUsuario($idUsuario);

    }
    public static  function obtenerPersonajePorIdUsuario($idUsuario)
    {

        $comando = "SELECT " .
            self::NOMBRE_PERSONAJE . "," .
            self::NIVEL . "," .
            self::NIVEL_CASCO . "," .
            self::NIVEL_ARCO . "," .
            self::NIVEL_ESCUDO . "," .
            self::NIVEL_GUANTES . "," .
            self::NIVEL_BOTAS . "," .
            self::NIVEL_FLECHA .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::FK_USUARIO . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $idUsuario);

        if ($sentencia->execute())
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        else
            return null;
    }

}