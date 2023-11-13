<?php
    // Requiere el archivo de utilidades para la base de datos y las configuraciones generales
    require_once "utilidades/db.php"; 
    require_once "configuraciones/general.php";

    // Clase para manejar sesiones de usuario
    class Sesion {
        // Constante que representa el nombre de la sesión
        const SESION = "auth";
        // Constante que representa el tiempo de expiración predeterminado para una sesión
        const SESION_TIEMPO_EXPIRACION = "+15 days";

        // Constructor privado para evitar la instanciación de la clase
        private function __construct() {
            
        }
        
        // Método estático para crear una nueva sesión
        public static function crear($idUsuario, $userAgent) {
            // Calcular la fecha de expiración
            $fechaExpiracion = strtotime(self::SESION_TIEMPO_EXPIRACION, time());

            // Insertar la nueva sesión en la base de datos
            $nuevaSesion = Db::insert("insert into sesiones (id_usuario, fecha_expiracion, user_agent) values ($idUsuario, FROM_UNIXTIME($fechaExpiracion), '$userAgent')");
            
            // Retornar el token y la fecha de expiración de la nueva sesión
            return ["token" => $nuevaSesion[0]["token"], "fecha_expiracion" => $fechaExpiracion];
        }

        // Método estático para obtener información de una sesión a través de su token
        public static function obtener($token) {
            // Validar el token antes de intentar obtener la sesión
            if (!self::validarToken($token)) {
                return [];
            }

            // Seleccionar la sesión de la base de datos
            $sesion = Db::select("select *, fecha_expiracion <= now() as expirado from sesiones where token = '$token'");
            
            // Verificar si se encontró una sesión
            if (empty($sesion)) {
                return [];
            }

            // Retornar la sesión obtenida
            return $sesion[0];
        }

        // Método estático para validar una sesión a través de su token
        public static function validar($token) {
            // Obtener la sesión asociada al token
            $sesion = self::obtener($token);

            // Verificar si la sesión está expirada
            if (empty($sesion) || $sesion["expirado"]) {
                return false;
            }

            // La sesión es válida
            return true;
        }

        // Método estático para identificar la información del navegador asociado a una sesión
        public static function identificar($token) {
            // Obtener la sesión asociada al token
            $sesion = self::obtener($token);

            // Verificar si la sesión existe y tiene información del agente de usuario (user agent)
            if (empty($sesion) || empty($sesion["user_agent"])) {
                return [];
            }

            // Obtener y retornar la información del navegador
            $userAgent = $sesion["user_agent"];
            $infoNavegador = self::obtenerInfoNavegador($userAgent);

            return $infoNavegador ?: [];
        }

        // Método estático para eliminar una sesión a través de su token
        public static function eliminar($token) {
            // Validar el token antes de intentar eliminar la sesión
            if (!self::validarToken($token)) {
                return false;
            }

            // Eliminar la sesión de la base de datos
            return Db::delete("delete from sesiones where token = '$token'") ? true : false;
        }

        // Método privado estático para validar un token según un patrón específico
        private static function validarToken($token) {
            return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $token);
        }
        
        // Método privado estático para obtener información del navegador
        private static function obtenerInfoNavegador($userAgent) {
            // Utilizar la función get_browser si está disponible, de lo contrario, retornar un arreglo vacío
            return function_exists('get_browser') ? get_browser($userAgent, true) : [];
        }
    }

    // Clase para manejar la sesión del usuario utilizando cookies
    class SesionUsuario {        
        // Constructor privado para evitar la instanciación de la clase
        private function __construct() {
            
        }

        // Método estático para crear una nueva sesión de usuario
        public static function crear($idUsuario) {
            // Obtener el agente de usuario (user agent) desde la solicitud HTTP, si está disponible
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

            // Crear una nueva sesión y establecer una cookie para almacenar el token
            $nuevaSesion = Sesion::crear($idUsuario, $userAgent);
            return setcookie(Sesion::SESION, $nuevaSesion["token"], $nuevaSesion["fecha_expiracion"], "/", BASE_URL, false, true);
        }

        // Método estático para obtener información de la sesión del usuario
        public static function obtener() {
            // Obtener el token almacenado en la cookie
            $token = self::obtenerCookie();

            // Si no hay token, retornar un arreglo vacío
            if (is_null($token)) {
                return [];
            }

            // Obtener la información de la sesión a través del token
            return Sesion::obtener($token);
        }

        // Método estático para validar la sesión del usuario
        public static function validar() {
            // Obtener el token almacenado en la cookie
            $token = self::obtenerCookie();

            // Si no hay token, la sesión no es válida
            if (is_null($token)) {
                return false;
            }
            
            // Validar la sesión a través del token
            return Sesion::validar($token);
        }

        // Método estático para eliminar la sesión del usuario
        public static function eliminar() {
            // Obtener el token almacenado en la cookie
            $token = self::obtenerCookie();

            // Si no hay token, no se puede eliminar la sesión
            if (is_null($token)) {
                return false;
            }

            // Eliminar la sesión a través del token y eliminar la cookie
            Sesion::eliminar($token);
            return setcookie(Sesion::SESION, "", 0, "/", BASE_URL, false, true);
        }

        // Método privado estático para obtener el valor de la cookie de sesión
        private static function obtenerCookie() {
            return $_COOKIE[Sesion::SESION] ?? null;
        }
    }
?>
