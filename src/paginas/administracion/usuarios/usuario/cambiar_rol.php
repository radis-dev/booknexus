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
  // Se incluyen los archivos necesarios
  require_once "utilidades/db.php";
  require_once "utilidades/sesion.php";
  require_once "utilidades/flash.php";

  // Verificación de la solicitud POST
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se obtienen y sanitizan los datos del formulario
    $idUsuario = isset($_POST["id_usuario"]) ? preg_replace('/[^0-9]/', '', $_POST["id_usuario"]) : null;
    $idRol = isset($_POST["id_rol"]) ? preg_replace('/[^0-9]/', '', $_POST["id_rol"]) : null;

    // Se verifica la existencia del usuario en la base de datos
    $usuario = Db::select("select * from usuarios where id = '$idUsuario'"); 
    if (empty($usuario)) {
      // Si no existe, se crea un mensaje de error y se redirige
      Flash::crearMensaje("error", "No existe el usuario.", "/administracion/usuarios");
    }

    // Se verifica la existencia del rol en la base de datos
    $existeRol = Db::select("select * from roles where id = '$idRol'"); 
    if (empty($existeRol)) {
      // Si no existe, se crea un mensaje de error y se redirige
      Flash::crearMensaje("error", "No existe el rol.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se verifica que el nuevo rol no sea el mismo que el actual del usuario
    if ($usuario[0]["id_rol"] == $idRol) {
        // Si es el mismo, se crea un mensaje de error y se redirige
        Flash::crearMensaje("error", "No es posible asignar el mismo rol al usuario.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se obtiene la información de la sesión del usuario
    $sesion = SesionUsuario::obtener();
    
    // Se verifica que el usuario no esté intentando modificar su propio rol
    if ($usuario[0]["id"] == $sesion['id_usuario']) {
        // Si está intentando hacerlo, se crea un mensaje de error y se redirige
        Flash::crearMensaje("error", "No es posible modificarte el rol a ti mismo", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se actualiza el rol del usuario en la base de datos
    Db::update("update usuarios set id_rol = '$idRol' where id = '$idUsuario'");
    
    // Se crea un mensaje de éxito y se redirige
    Flash::crearMensaje("exito", "Acabas de modificar el rol del usuario.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
  }
?>
