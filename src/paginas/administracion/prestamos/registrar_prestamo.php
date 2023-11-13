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
    $idUsuario = isset($_POST["id_usuario"]) ? preg_replace('/[^0-9]/', '', $_POST["id_usuario"]) : null;
    $idEjemplar = isset($_POST["id_ejemplar"]) ? preg_replace('/[^0-9]/', '', $_POST["id_ejemplar"]) : null;

    // Busca el lector/a en la base de datos
    $existeLector = Db::select("select * from lectores where id_usuario = '$idUsuario'"); 

    // Verifica si el lector/a no existe y redirecciona con un mensaje de error
    if (empty($existeLector)) {
        Flash::crearMensaje("error", "No existe el lector/a.", "/administracion/prestamos");
    }

    // Busca el ejemplar en la base de datos
    $existeEjemplar = Db::select("select * from ejemplares where id = '$idEjemplar'"); 

    // Verifica si el ejemplar no existe y redirecciona con un mensaje de error
    if (empty($existeEjemplar)) {
        Flash::crearMensaje("error", "No existe el ejemplar.", "/administracion/prestamos");
    }

    // Busca si el ejemplar está actualmente prestado a un lector/a
    $existeEjemplarPrestado = Db::select("select e.id as 'id_ejemplar' from ejemplares e left join prestamos p on e.id = p.id_ejemplar and p.fecha_entrega is null where p.id is not null and e.id = '$idEjemplar'"); 

    // Verifica si el ejemplar está prestado y redirecciona con un mensaje de error
    if (!empty($existeEjemplarPrestado)) {
        Flash::crearMensaje("error", "Actualmente el ejemplar está siendo prestado a un lector/a.", "/administracion/prestamos");
    }

    // Obtiene la cantidad de préstamos sin entregar del lector/a
    $prestamosSinEntregar = Db::select("select count(*) as 'cantidad_prestamos' from prestamos where id_usuario = '$idUsuario' and fecha_entrega is null"); 
    $cantidadPrestamos = $prestamosSinEntregar[0]["cantidad_prestamos"];

    // Verifica si el lector/a ya tiene 3 préstamos sin devolver y redirecciona con un mensaje de error
    if ($cantidadPrestamos >= 3) {
        Flash::crearMensaje("error", "Actualmente el lector tiene {$cantidadPrestamos} préstamos sin devolver, el máximo de préstamos permitidos es 3.", "/administracion/prestamos");
    }

    // Calcula la fecha de devolución sumando 7 días a la fecha actual
    $fechaDevolucion = strtotime("+7 days", time());

    // Inserta un nuevo préstamo en la base de datos
    $nuevoPrestamo = Db::insert("insert into prestamos (id_ejemplar, id_usuario, fecha_devolucion) values ('$idEjemplar', '$idUsuario', FROM_UNIXTIME($fechaDevolucion))");
    
    // Redirecciona con un mensaje de éxito
    Flash::crearMensaje("exito", "El ejemplar ha sido prestado correctamente durante 7 días.", "/administracion/prestamos");
  }
?>