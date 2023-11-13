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
    // Obtiene el valor del campo 'id_libro' del formulario, elimina caracteres no numéricos y asigna a $idLibro
    $idLibro = isset($_POST["id_libro"]) ? preg_replace('/[^0-9]/', '', $_POST["id_libro"]) : null;

    // Busca el libro en la base de datos
    $existeLibro = Db::select("select * from libros where id = '$idLibro'"); 

    // Verifica si el libro no existe y redirecciona con un mensaje de error
    if (empty($existeLibro)) {
        Flash::crearMensaje("error", "No existe el libro.", "/administracion/libros");
    }

    // Busca si algún ejemplar de este libro está actualmente prestado a un lector/a
    $existeEjemplarPrestado = Db::select("select l.id as 'id_libro' from ejemplares e left join prestamos p on e.id = p.id_ejemplar and p.fecha_entrega is null inner join libros l on e.id_libro = l.id where p.id is not null and l.id = '$idLibro'"); 
    
    // Verifica si hay ejemplares prestados y redirecciona con un mensaje de error
    if (!empty($existeEjemplarPrestado)) {
        Flash::crearMensaje("error", "Actualmente un ejemplar de este libro está siendo prestado a un lector.", "/administracion/libros");
    }

    // Elimina el libro de la base de datos
    Db::delete("delete from libros where id = '$idLibro'");
    
    // Redirecciona con un mensaje de éxito
    Flash::crearMensaje("exito", "El libro ha sido eliminado correctamente.", "/administracion/libros");
  }
?>
