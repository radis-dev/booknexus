<?php 
    require_once "configuraciones/general.php";

    final class Prestamos {

        public static function obtenerEstado($fechaEntrega, $fechaCreacion, $fechaDevolucion) {
            // Obtener el timestamp actual
            $timestampActual = time();

            // Convertir las fechas a timestamps Unix solo considerando el día
            $timestampCreacion = strtotime(date('Y-m-d', strtotime($fechaCreacion)));
            $timestampDevolucion = strtotime(date('Y-m-d', strtotime($fechaDevolucion)));

            // Verificar si los libros han sido entregados
            if ($fechaEntrega !== null) {
                // Los libros han sido entregados
                $timestampEntrega = strtotime(date('Y-m-d', strtotime($fechaEntrega)));

                if ($timestampEntrega <= $timestampDevolucion) {
                    // Los libros fueron entregados a tiempo
                    return 2; // Estado: Entregado
                } else {
                    // Los libros fueron entregados con retraso
                    return 3; // Estado: Entregado con retraso
                }
            } else {
                // Los libros no han sido entregados

                // Verificar si hay retraso en la devolución (excepto si la devolución es hoy)
                if ($timestampActual > $timestampDevolucion && date('Y-m-d', $timestampActual) !== date('Y-m-d', $timestampDevolucion)) {
                    // Hay retraso en la devolución
                    return 0; // Estado: Retraso
                } else {
                    // Los libros están prestados y dentro del plazo de devolución
                    return 1; // Estado: Prestado
                }
            }
        }
    }
?>