<?php
    // Se incluye el archivo necesario para el manejo de mensajes flash
    require_once "utilidades/flash.php";

    // Función para renderizar alertas en la interfaz de usuario
    function renderizarAlerta() {
        // Definición de colores asociados a los tipos de mensajes flash
        $colores = [
            Flash::FLASH_ADVERTENCIA => "yellow",
            Flash::FLASH_ERROR => "red",
            Flash::FLASH_EXITO => "green"
        ];

        // Se obtienen los mensajes flash almacenados
        $mensajes = Flash::obtenerMensajes();
        
        // Si no hay mensajes, se sale de la función
        if (!isset($mensajes)) return;
        
        // Iteración sobre los mensajes para renderizar las alertas
        foreach ($mensajes as $mensaje) {
            // Se genera el código HTML para la alerta con el color y contenido correspondiente
            echo sprintf('<div class="mx-auto px-16 py-2"><div class="py-4 px-6 rounded-md text-%1$s-700 bg-%1$s-100 border border-%1$s-700 break-words">%2$s</div></div>',
                $colores[$mensaje["tipo"]],
                $mensaje["contenido"]
            );
        } 
    }
?>
