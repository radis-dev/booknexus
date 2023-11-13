<?php
    // Se incluyen los archivos necesarios
    require_once "utilidades/sesion.php";
    require_once "utilidades/flash.php";

    // Se define una clase final llamada RolesMiddleware
    final class RolesMiddleware {
        
        // Constructor privado para evitar instancias de la clase
        private function __construct() {

        }

        // Método estático permitir(), utilizado para verificar los roles permitidos
        public static function permitir(array $rolesPermitidos) {
            // Se obtiene la información de la sesión del usuario
            $sesion = SesionUsuario::obtener();
            
            $rolesPermitidosString = implode("','", array_map('strtoupper', $rolesPermitidos));

            // Se realiza una consulta a la base de datos para verificar los roles del usuario
            $tieneRol = Db::select("
                SELECT r.nombre AS 'nombre_rol'
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id
                WHERE u.id = '{$sesion['id_usuario']}' AND r.nombre IN ('$rolesPermitidosString')
            ");

            // Si el usuario no tiene ninguno de los roles permitidos, se muestra un mensaje de error y se redirige
            if (empty($tieneRol)) {
                Flash::crearMensaje("error", "Acceso denegado. Debe tener uno de los siguientes roles permitidos: " . $rolesPermitidosString, "/");
            }
        }
    }    
?>
