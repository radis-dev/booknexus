<?php 
    // Requiere el archivo de configuraciones generales
    require_once "configuraciones/general.php";

    // Clase final para interactuar con la base de datos
    final class Db {
        // Propiedad estática para almacenar la conexión a la base de datos
        private static $conexion = null;

        // Constructor privado para evitar la instanciación de la clase
        private function __construct() {
            
        }

        // Método privado estático para establecer la conexión a la base de datos
        private static function conectar() {
            // Si no hay una conexión existente, crear una nueva
            if (self::$conexion === null) {
                self::$conexion = mysqli_connect(DB_HOST, DB_USUARIO, DB_CONTRASENA, DB_BASEDATOS, DB_PUERTO);
            }
        }

        // Método privado estático para realizar una consulta a la base de datos
        private static function consulta($sql) {
            // Establecer la conexión antes de ejecutar la consulta
            self::conectar();
            // Ejecutar la consulta y retornar el resultado
            return mysqli_query(self::$conexion, $sql);
        }

        // Método estático para realizar una consulta SELECT y retornar el resultado como un arreglo asociativo
        public static function select($sql) {
            // Realizar la consulta
            $resultado = self::consulta($sql);
            // Si la consulta es exitosa, retornar el resultado como un arreglo asociativo, de lo contrario, retornar un arreglo vacío
            return ($resultado) ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
        }

        // Método estático para realizar una consulta INSERT y retornar la fila insertada
        public static function insert($sql) {
            // Establecer la conexión
            self::conectar();

            // Obtener el nombre de la tabla desde la consulta INSERT
            $nombreTabla = preg_match("/INSERT\s+INTO\s+([a-zA-Z_]+)/i", $sql, $matches) ? $matches[1] : false;

            // Ejecutar la consulta INSERT
            if (!self::consulta($sql)) { 
                return []; 
            }

            // Obtener el ID de la última fila insertada
            $ultimaId = mysqli_insert_id(self::$conexion);
            
            // Si el ID es inválido, retornar un arreglo vacío
            if ($ultimaId <= 0) { 
                return []; 
            }

            // Obtener la fila insertada usando el ID
            $resultado = self::consulta("select * from $nombreTabla where id = $ultimaId");
            
            // Si la consulta SELECT es exitosa, retornar la fila insertada como un arreglo asociativo, de lo contrario, retornar un arreglo vacío
            if (!$resultado) {
                return [];
            }

            return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
        }

        // Método estático para realizar una consulta UPDATE y retornar true si la actualización es exitosa, de lo contrario, false
        public static function update($sql) {
            // Ejecutar la consulta UPDATE
            $resultado = self::consulta($sql);
            // Retornar true si la actualización afecta a alguna fila, de lo contrario, false
            return ($resultado !== false) && mysqli_affected_rows(self::$conexion);
        }

        // Método estático para realizar una consulta DELETE y retornar true si la eliminación es exitosa, de lo contrario, false
        public static function delete($sql) {
            // Ejecutar la consulta DELETE
            $resultado = self::consulta($sql);
            // Retornar true si la eliminación afecta a alguna fila, de lo contrario, false
            return ($resultado !== false) && mysqli_affected_rows(self::$conexion);
        }
    }
?>
