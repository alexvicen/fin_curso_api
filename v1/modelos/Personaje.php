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
    const PEPITA = "pepita";
    const HIERRO = "hierro";
    const GEMA_BRUTO = "gema_bruto";
    const ROCA = "roca";
    const TRONCO = "tronco";
    const LINGOTE_ORO = "lingote_oro";
    const LINGOTE_HIERRO = "lingote_hierro";
    const GEMA = "gema";
    const PIEDRA = "piedra";
    const TABLA_MADERA = "tabla_madera";

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
            self::NIVEL_FLECHA . "," .
            self::PEPITA . "," .
            self::HIERRO . "," .
            self::GEMA_BRUTO . "," .
            self::ROCA . "," .
            self::TRONCO . "," .
            self::LINGOTE_ORO . "," .
            self::LINGOTE_HIERRO . "," .
            self::GEMA . "," .
            self::PIEDRA . "," .
            self::TABLA_MADERA .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::FK_USUARIO . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $idUsuario);

        if ($sentencia->execute())
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        else
            throw new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
    }

}