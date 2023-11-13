<?php
    // Definición de una clase Flash que maneja mensajes flash en una aplicación web
    final class Flash {
        // Constante que representa la clave para almacenar mensajes flash en la sesión
        private const FLASH = "mensajes_flash";

        // Constantes que representan los tipos posibles de mensajes flash
        const FLASH_ADVERTENCIA = "advertencia";
        const FLASH_ERROR = "error";
        const FLASH_EXITO = "exito";

        // Método para crear un mensaje flash
        public static function crearMensaje($tipo, $contenido, $url = null) {
            // Verificar si ya existe un mensaje del mismo tipo y eliminarlo
            if (isset($_SESSION[self::FLASH][$tipo])) {
                unset($_SESSION[self::FLASH][$tipo]);
            }

            // Almacenar el nuevo mensaje en la sesión
            $_SESSION[self::FLASH][$tipo] = ["contenido" => $contenido, "tipo" => $tipo];
            
            // Redirigir a la URL proporcionada, si se especifica
            if (!is_null($url)) {
                header("Location: $url");
                exit;
            }
        }

        // Método para obtener y eliminar los mensajes flash de la sesión
        public static function obtenerMensajes() {
            // Verificar si hay mensajes en la sesión
            if (!isset($_SESSION[self::FLASH])) return;

            // Obtener los mensajes almacenados en la sesión
            $mensajes = $_SESSION[self::FLASH];
            
            // Eliminar los mensajes de la sesión
            unset($_SESSION[self::FLASH]);

            // Retornar los mensajes obtenidos
            return $mensajes;
        }
    }

    // Iniciar la sesión, necesario para utilizar variables de sesión en PHP
    session_start();
?>
