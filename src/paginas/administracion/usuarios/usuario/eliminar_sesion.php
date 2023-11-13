<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(true);
?>
<?php
  // Se requiere el archivo "roles.php" que contiene la lógica para autenticar los roles de los usuarios.
  require_once "middleware/roles.php";
  
  // Se utiliza el middleware de roles para permitir el acceso solo a usuarios con el rol "ADMINISTRADOR"
  RolesMiddleware::permitir(["ADMINISTRADOR"]);
?>
<?php
  // Se requieren los archivos "db.php", "flash.php" y "sesion.php", que contienen utilidades relacionadas con la base de datos, mensajes flash y gestión de sesiones, respectivamente.
  require_once "utilidades/db.php";
  require_once "utilidades/flash.php";
  require_once "utilidades/sesion.php";

  // Se verifica si la solicitud HTTP es de tipo POST.
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se obtienen y filtran los IDs de sesión y usuario de la entrada del formulario, asegurándose de que solo contengan caracteres numéricos.
    $idSesion = isset($_POST["id_sesion"]) ? preg_replace('/[^0-9]/', '', $_POST["id_sesion"]) : null;
    $idUsuario = isset($_POST["id_usuario"]) ? preg_replace('/[^0-9]/', '', $_POST["id_usuario"]) : null;

    // Se verifica si el usuario con el ID proporcionado existe en la base de datos.
    $existeUsuario = Db::select("select * from usuarios where id = '$idUsuario'"); 
    if (empty($existeUsuario)) {
        // Si no existe el usuario, se crea un mensaje de error y se redirige a la página de administración de usuarios.
        Flash::crearMensaje("error", "No existe el usuario.", "/administracion/usuarios");
    }
    
    // Se verifica si la sesión con el ID proporcionado existe en la base de datos.
    $existeSesion = Db::select("select * from sesiones where id = '$idSesion'"); 
    if (empty($existeSesion)) {
        // Si no existe la sesión, se crea un mensaje de error y se redirige a la página de administración de usuarios específica del usuario.
        Flash::crearMensaje("error", "La sesión que intentas eliminar no existe.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se obtiene la sesión actual del usuario.
    $sesionActual = SesionUsuario::obtener();
    
    // Se verifica si la sesión que se intenta eliminar es la sesión actualmente utilizada por el usuario.
    if ($existeSesion[0]["token"] === $sesionActual["token"]) {
        // Si se intenta eliminar la sesión actual, se crea un mensaje de error y se redirige a la página de administración de usuarios específica del usuario.
        Flash::crearMensaje("error", "No puedes eliminar la sesión que estás utilizando actualmente.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }
    
    // Se elimina la sesión de la base de datos.
    Db::delete("delete from sesiones where id = '$idSesion'");
    
    // Se crea un mensaje de éxito y se redirige a la página de administración de usuarios específica del usuario.
    Flash::crearMensaje("exito", "La sesión ha sido eliminada correctamente.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
  }
?>
