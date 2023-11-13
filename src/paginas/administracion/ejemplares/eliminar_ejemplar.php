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
    // Obtiene el valor del campo 'id_ejemplar' del formulario, elimina caracteres no numéricos y asigna a $idEjemplar
    $idEjemplar = isset($_POST["id_ejemplar"]) ? preg_replace('/[^0-9]/', '', $_POST["id_ejemplar"]) : null;

    // Busca el ejemplar en la base de datos
    $existeEjemplar = Db::select("select * from ejemplares where id = '$idEjemplar'"); 

    // Verifica si el ejemplar no existe y redirecciona con un mensaje de error
    if (empty($existeEjemplar)) {
        Flash::crearMensaje("error", "No existe el ejemplar.", "/administracion/ejemplares");
    }

    // Busca si el ejemplar está actualmente prestado a un lector/a
    $existeEjemplarPrestado = Db::select("select e.id as 'id_ejemplar' from ejemplares e left join prestamos p on e.id = p.id_ejemplar and p.fecha_entrega is null where p.id is not null and e.id = '$idEjemplar'"); 
    
    // Verifica si el ejemplar está prestado y redirecciona con un mensaje de error
    if (!empty($existeEjemplarPrestado)) {
        Flash::crearMensaje("error", "Actualmente el ejemplar está siendo prestado a un lector/a.", "/administracion/ejemplares");
    }

    // Elimina el ejemplar de la base de datos
    Db::delete("delete from ejemplares where id = '$idEjemplar'");
    
    // Redirecciona con un mensaje de éxito
    Flash::crearMensaje("exito", "El ejemplar ha sido eliminado correctamente.", "/administracion/ejemplares");
  }
?>
