<?php
    // Se incluyen los archivos necesarios para el manejo de sesiones y mensajes flash
    require_once "utilidades/sesion.php";
    require_once "utilidades/flash.php";

    // Se define una clase llamada AuthMiddleware
    final class AuthMiddleware {
        
        // El constructor es privado para evitar instancias de la clase
        private function __construct() {
            // El constructor está vacío, ya que no se espera que se creen instancias de esta clase
        }

        // Método estático para verificar el acceso permitido
        public static function permitir($permitirAcceso) {
            // Se verifica si no hay una sesión de usuario válida y se permite el acceso
            if (!SesionUsuario::validar() && $permitirAcceso) {
                // Si la sesión no es válida y se debe permitir el acceso, se redirige a la página principal
                Flash::crearMensaje("error", "Sesión no iniciada o expirada. Por favor, inicie sesión nuevamente para continuar", "/");
            }
        }
    }    
?>
