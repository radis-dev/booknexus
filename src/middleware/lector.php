<?php
    // Se incluyen los archivos necesarios
    require_once "utilidades/sesion.php";
    require_once "utilidades/flash.php";

    // Se define una clase final llamada LectorMiddleware
    final class LectorMiddleware {
        
        // Constructor privado para evitar instancias de la clase
        private function __construct() {

        }

        // Método estático permitir(), utilizado para verificar si el usuario es un lector
        public static function permitir() {
            // Se obtiene la información de la sesión del usuario
            $sesion = SesionUsuario::obtener();
            
            // Se realiza una consulta a la base de datos para verificar si el usuario es un lector
            $esLector = Db::select("select * from lectores where id_usuario = '{$sesion['id_usuario']}'");
            
            // Si no se encuentra información en la consulta, se redirige y se muestra un mensaje de error
            if (empty($esLector)) {
                Flash::crearMensaje("error", "Para acceder a esta sección, por favor regístrese como lector en la biblioteca.", "/");
            }
        }
    }    
?>
