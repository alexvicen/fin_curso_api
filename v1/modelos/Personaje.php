<?php

require_once 'utilidades/ConexionBD.php';

class Personaje{

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

    const ESTADO_PARAMETROS_INCORRECTOS = 400;
    const ESTADO_URL_INCORRECTA = 454;
    const ESTADO_CREACION_EXITOSA = 201;
    const ESTADO_CREACION_FALLIDA = 102;
    const ESTADO_FALLA_DESCONOCIDA = 418;
    const ESTADO_CLAVE_NO_AUTORIZADA = 401;
    const ESTADO_AUSENCIA_CLAVE_API=403;
    const ESTADO_ACTUALIZA_EXISTOSO = 201;
    const ESTADO_ERROR_BD=101;


    public static function put($peticion)
    {
        if ($peticion == "actualizar") {
            return self::actualiza();
        }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);	}

    }

    private function actualiza(){

        $resultadoActualiza=self::actualizaPersonaje();

        switch ($resultadoActualiza) {
            case self::ESTADO_CREACION_EXITOSA:
                http_response_code(201);
                return
                    [
                        "estado" => self::ESTADO_ACTUALIZA_EXISTOSO,
                        "mensaje" => utf8_encode("Actualizado con exito")
                    ];
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Soy una tetera", 418);
        }







    }
    private function actualizaPersonaje(){

        $body = file_get_contents('php://input');
        $personaje = json_decode($body);

        $nombrePersonaje=$personaje->nombre_personaje;
        $fkUsuario=$personaje->fk_usuario;
        $nivel=$personaje->nivel;
        $nivCasco=$personaje->nivel_casco;
        $nivArco=$personaje->nivel_arco;
        $nivEscudo=$personaje->nivel_escudo;
        $nivGuantes=$personaje->nivel_guantes;
        $nivBotas=$personaje->nivel_botas;
        $nivFlecha=$personaje->nivel_flecha;
        $pepita=$personaje->pepita;
        $roca=$personaje->roca;
        $tronco=$personaje->tronco;
        $hierro=$personaje->hierro;
        $gemaBruto=$personaje->gema_bruto;
        $lingoteOro=$personaje->lingote_oro;
        $lingoteHierro=$personaje->lingote_hierro;
        $gema=$personaje->gema;
        $piedra=$personaje->piedra;
        $tablaMadera=$personaje->tabla_madera;
        try{
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando =	"UPDATE ". self::NOMBRE_TABLA ." SET ".
                self::NOMBRE_PERSONAJE. " = ?".", ".
                self::NIVEL. " = ?".", ".
                self::NIVEL_CASCO . " = ?".", ".
                self::NIVEL_ARCO. " = ?".", ".
                self::NIVEL_ESCUDO . " = ?".", ".
                self::NIVEL_GUANTES . " = ?".", ".
                self::NIVEL_BOTAS . " = ?".", ".
                self::NIVEL_FLECHA . " = ?".", ".
                self::PEPITA . " = ?".", ".
                self::ROCA . " = ?".", ".
                self::TRONCO . " = ?".", ".
                self::HIERRO . " = ?".", ".
                self::GEMA_BRUTO . " = ?".", ".
                self::LINGOTE_ORO . " = ?".", ".
                self::LINGOTE_HIERRO . " = ?".", ".
                self::GEMA . " = ?".", ".
                self::PIEDRA . " = ?".", ".
                self::TABLA_MADERA . " = ?". " WHERE ". self::FK_USUARIO. " = ?";
            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nombrePersonaje);
            $sentencia->bindParam(2, $nivel);
            $sentencia->bindParam(3, $nivCasco);
            $sentencia->bindParam(4, $nivArco);
            $sentencia->bindParam(5, $nivEscudo);
            $sentencia->bindParam(6, $nivGuantes);
            $sentencia->bindParam(7, $nivBotas);
            $sentencia->bindParam(8, $nivFlecha);
            $sentencia->bindParam(9, $pepita);
            $sentencia->bindParam(10, $roca);
            $sentencia->bindParam(11, $tronco);
            $sentencia->bindParam(12, $hierro);
            $sentencia->bindParam(13, $gemaBruto);
            $sentencia->bindParam(14, $lingoteOro);
            $sentencia->bindParam(15, $lingoteHierro);
            $sentencia->bindParam(16, $gema);
            $sentencia->bindParam(17, $piedra);
            $sentencia->bindParam(18, $tablaMadera);
            $sentencia->bindParam(19, $fkUsuario);

            $resultado = $sentencia->execute();
            if ($resultado) {
                return self::ESTADO_ACTUALIZA_EXISTOSO;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

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