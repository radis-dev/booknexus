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
  // Se requieren los archivos "db.php", "flash.php" y "sesion.php", que contienen utilidades relacionadas con la base de datos, mensajes flash y gestión de sesiones, respectivamente.
  require_once "utilidades/db.php";
  require_once "utilidades/flash.php";
  require_once "utilidades/sesion.php";

  // Se verifica si la solicitud HTTP es de tipo POST.
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se obtiene y filtra el ID del usuario de la entrada del formulario, asegurándose de que solo contenga caracteres numéricos.
    $idUsuario = isset($_POST["id_usuario"]) ? preg_replace('/[^0-9]/', '', $_POST["id_usuario"]) : null;

    // Se verifica si el usuario con el ID proporcionado existe en la base de datos.
    $existeUsuario = Db::select("select * from usuarios where id = '$idUsuario'"); 
    if (empty($existeUsuario)) {
      // Si no existe el usuario, se crea un mensaje de error y se redirige a la página de administración de usuarios.
      Flash::crearMensaje("error", "No existe el usuario.", "/administracion/usuarios");
    }

    // Se verifica si el usuario está inscrito como lector.
    $existeLector = Db::select("select * from lectores where id_usuario = '$idUsuario'"); 
    if (empty($existeLector)) {
      // Si el usuario no está inscrito como lector, se crea un mensaje de error y se redirige a la página específica del usuario.
      Flash::crearMensaje("error", "Este usuario no está inscrito como lector.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se verifica si el usuario tiene ejemplares prestados que aún no ha devuelto.
    $existeEjemplaresPrestados = Db::select("select * from prestamos where id_usuario = '$idUsuario' and fecha_entrega is null"); 
    if (!empty($existeEjemplaresPrestados)) {
      // Si el usuario tiene ejemplares prestados sin devolver, se crea un mensaje de error y se redirige a la página específica del usuario.
      Flash::crearMensaje("error", "Antes de desinscribirle como lector debe devolver todos los préstamos.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se elimina al usuario de la tabla de lectores.
    Db::delete("delete from lectores where id_usuario = '$idUsuario'");
    
    // Se crea un mensaje de éxito y se redirige a la página específica del usuario.
    Flash::crearMensaje("exito", "El usuario ya no es un lector registrado.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
  }
?>
