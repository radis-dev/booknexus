<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(true);
?>
<?php
  // Se requiere el archivo "roles.php" que contiene la lógica para autenticar los roles de los usuarios.
  require_once "middleware/roles.php";
  
  // Se utiliza el middleware de roles para permitir el acceso solo a usuarios con el rol "ADMINISTRADOR" y "PERSONAL"
  RolesMiddleware::permitir(["ADMINISTRADOR", "PERSONAL"]);
?>
<?php
  // Incluye los archivos necesarios
  require_once "utilidades/db.php";
  require_once "utilidades/flash.php";

  // Verifica si la solicitud es de tipo POST
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtiene el valor del campo 'id_prestamo' del formulario, elimina caracteres no numéricos y asigna a $idPrestamo
    $idPrestamo = isset($_POST["id_prestamo"]) ? preg_replace('/[^0-9]/', '', $_POST["id_prestamo"]) : null;

    // Busca el préstamo en la base de datos
    $prestamo = Db::select("select * from prestamos where id = '$idPrestamo'"); 

    // Verifica si el préstamo no existe y redirecciona con un mensaje de error
    if (empty($prestamo)) {
      Flash::crearMensaje("error", "No existe el préstamo.", "/administracion/prestamos");
    } elseif ($prestamo[0]["fecha_entrega"] !== null) {
      Flash::crearMensaje("error", "El préstamo ya ha sido finalizado.", "/administracion/prestamos");
    }

    // Obtiene la fecha actual y la asigna a $fechaEntrega
    $fechaEntrega = time();

    // Actualiza la fecha de entrega del préstamo en la base de datos
    Db::update("update prestamos set fecha_entrega = FROM_UNIXTIME($fechaEntrega) where id = '$idPrestamo'");
    
    // Redirecciona con un mensaje de éxito
    Flash::crearMensaje("exito", "El préstamo del ejemplar ha sido finalizado.", "/administracion/prestamos");
  }
?>
