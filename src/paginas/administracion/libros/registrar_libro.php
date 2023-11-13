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
    // Obtiene los valores del formulario o asigna null si no existen
    $titulo = $_POST["titulo"] ?? null;
    $isbn = $_POST["isbn"] ?? null;
    $fechaPublicacion = $_POST["fecha_publicacion"] ?? null;

    // Validaciones para el campo 'titulo'
    if (empty($titulo)) {
      Flash::crearMensaje("error", "El titulo es obligatorio.", "/administracion/libros");
    } elseif (!preg_match('/^[a-zA-Z\' ]{1,50}$/', $titulo)) {
      Flash::crearMensaje("error", "El titulo no es válido.", "/administracion/libros");
    }

    // Verifica si el titulo ya está registrado y redirecciona con un mensaje de error
    $existeTitulo = Db::select("select * from libros where titulo = '$titulo'"); 
    if (!empty($existeTitulo)) {
      Flash::crearMensaje("error", "El titulo ya está registrado.", "/administracion/libros");
    }

    // Validaciones para el campo 'isbn'
    if (empty($isbn)) {
      Flash::crearMensaje("error", "El ISBN es obligatorio.", "/administracion/libros");
    } elseif (!preg_match('/^(978|979)\d{10}$/', $isbn)) {
      Flash::crearMensaje("error", "El ISBN no es válido.", "/administracion/libros");
    }

    // Verifica si el ISBN ya está registrado y redirecciona con un mensaje de error
    $existeISBN = Db::select("select * from libros where isbn = '$isbn'"); 
    if (!empty($existeISBN)) {
      Flash::crearMensaje("error", "El ISBN ya está registrado.", "/administracion/libros");
    }

    // Validaciones para el campo 'fecha_publicacion'
    if (empty($fechaPublicacion)) {
      Flash::crearMensaje("error", "La fecha de publicación es obligatoria.", "/administracion/libros");
    } elseif (!date_create_from_format('Y-m-d', $fechaPublicacion)) {
      Flash::crearMensaje("error", "La fecha de publicación no es válida.", "/administracion/libros");
    } elseif (strtotime(date('Y-m-d')) < strtotime($fechaPublicacion)) {
      Flash::crearMensaje("error", "La fecha de publicacion es superior a la fecha actual.", "/administracion/libros");
    }

    // Inserta un nuevo libro en la base de datos
    $nuevoLibro = Db::insert("insert into libros (isbn, titulo, fecha_publicacion) values ('$isbn', '$titulo', '$fechaPublicacion')");
    
    // Redirecciona con un mensaje de éxito
    Flash::crearMensaje("exito", "Acabas de añadir un nuevo libro correctamente.", "/administracion/libros");
  }
?>
