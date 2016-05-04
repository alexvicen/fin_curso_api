<?php
require_once 'utilidades/ConexionBD.php';
require_once 'utilidades/ExcepcionApi.php';
require_once 'Personaje.php';

class Usuario
{
    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "mos_usuarios";
    const ID_USUARIO = "id_usuario";
    const NOMBRE_USUARIO = "nombre_usuario";
    const LOGIN_USUARIO = "login_usuario";
    const PASS_USUARIO = "pass_usuario";
    const EMAIL = "email";

    const ESTADO_PARAMETROS_INCORRECTOS = 400;
    const ESTADO_URL_INCORRECTA = 454;
    const ESTADO_CREACION_EXITOSA = 201;
    const ESTADO_CREACION_FALLIDA = 102;
    const ESTADO_FALLA_DESCONOCIDA = 418;
    const ESTADO_CLAVE_NO_AUTORIZADA = 401;

    var $usuarioDB=NULL;

    public static function post($peticion)
    {


        if ($peticion == 'registro') {
            return self::registrar();
        } else if ($peticion== 'login') {
            return self::loguear();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }

    }


    private function registrar()
    {
        $cuerpo = file_get_contents('php://input');

        $usuario = json_decode($cuerpo);

        // Validar campos

        $resultado = self::crear($usuario);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
                http_response_code(201);
                return
                    [
                        "estado" => self::ESTADO_CREACION_EXITOSA,
                        "mensaje" => utf8_encode("¡Registro con éxito!")
                    ];
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Soy una tetera", 418);
        }
    }


    private function crear($datosUsuario)
    {

        //inicializamos valores del usuario
        $nombre = $datosUsuario->nombre_usuario;
        $login = $datosUsuario->login_usuario;
        $pass = $datosUsuario->pass_usuario;
        $passCiph = self::encriptarContrasena($pass);
        $email = $datosUsuario->email;

        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE_USUARIO . "," .
                self::LOGIN_USUARIO . "," .
                self::PASS_USUARIO . "," .
                self::EMAIL  . ")" .
                " VALUES(?,?,?,?)";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $login);
            $sentencia->bindParam(3, $passCiph);
            $sentencia->bindParam(4, $email);

            $resultado = $sentencia->execute();

            if ($resultado) {
                return self::ESTADO_CREACION_EXITOSA;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

    }


    private function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana) {
            //die($contrasenaPlana);

            return sha1($contrasenaPlana);  //password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        }else return null;

    }


    private function loguear()
    {

        $body = file_get_contents('php://input');
        $usuario = json_decode($body);

        $login = $usuario->login_usuario;
        $contrasena = $usuario->pass_usuario;

        //comprobar si el json contiene el recurso correo o login para saber como loguear al usuario.

        if (self::autenticarPorCorreo($login, $contrasena)) {
            if ($usuarioBD = self::obtenerUsuarioPorCorreo($login)) {
                http_response_code(200);
                return ["estado" => 1, Personaje::obtenerPersonajePorIdUsuario($usuarioBD["id_usuario"])];
            } elseif ($usuarioBD = self::obtenerUsuarioPorLogin($login)) {
                http_response_code(200);
                return ["estado" => 1, Personaje::obtenerPersonajePorIdUsuario($usuarioBD["id_usuario"])];
            } else {
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA,"Ha ocurrido un error");
            }
        }else {
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS,utf8_encode("Correo/Login o contraseña inválidos"));

        }

    }

    //comprobamos contraseña solo si existe usuario.
    private function autenticarPorCorreo($login, $contrasena)
    {
        $comando = "SELECT pass_usuario FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::EMAIL . "=?";

        try {

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $login);

            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetch();

                if (self::validarContrasena($contrasena, $resultado[0])) {
                    return true;
                } else{

                    return self::autenticarPorLogin($login, $contrasena);
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    private function autenticarPorLogin($login, $contrasena)
    {
        $comando = "SELECT pass_usuario FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::LOGIN_USUARIO . "=?";

        try {

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $login);

            $sentencia->execute();

            if ($sentencia) {

                $resultado = $sentencia->fetch();
                $a=$resultado[0];
                if (self::validarContrasena($contrasena,$a)) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    private function validarContrasena($contrasenaPlana, $contrasenaHash)
    {
        return self::comprobarContrasenas($contrasenaPlana, $contrasenaHash);
    }


    private function obtenerUsuarioPorCorreo($login)
    {

        $comando = "SELECT " .
            self::ID_USUARIO . "," .
            self::NOMBRE_USUARIO . "," .
            self::LOGIN_USUARIO . "," .
            self::PASS_USUARIO . "," .
            self::EMAIL .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::EMAIL . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $login);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }


    private function obtenerUsuarioPorLogin($login)
    {

        $comando = "SELECT " .
            self::ID_USUARIO . "," .
            self::NOMBRE_USUARIO . "," .
            self::LOGIN_USUARIO . "," .
            self::PASS_USUARIO . "," .
            self::EMAIL .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::LOGIN_USUARIO . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $login);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }

    private function comprobarContrasenas($contrasenaPlana, $contrasenaHash){
        if($contrasenaHash==sha1($contrasenaPlana)){
            return true;
        }else return false;
    }

    private function actualizarUsuario($usuario){

    }
    private function obtenerIdUsuario($claveApi)
    {
        $comando = "SELECT " . self::ID_USUARIO.
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::API_KEY . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $claveApi);

        if ($sentencia->execute()) {
            $resultado = $sentencia->fetch();
            return $resultado['idUsuario'];
        } else
            return null;
    }


}


