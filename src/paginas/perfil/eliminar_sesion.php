<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(true);
?>
<?php
  // Se requieren los archivos "db.php", "flash.php", y "sesion.php", que contienen utilidades relacionadas con la base de datos, mensajes flash y gestión de sesiones, respectivamente.
  require_once "utilidades/db.php";
  require_once "utilidades/flash.php";
  require_once "utilidades/sesion.php";

  // Se verifica si la solicitud HTTP es de tipo POST.
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se obtiene y filtra el ID de sesión de la entrada del formulario, asegurándose de que solo contenga caracteres numéricos.
    $idSesion = isset($_POST["id_sesion"]) ? preg_replace('/[^0-9]/', '', $_POST["id_sesion"]) : null;
    
    // Se realiza una consulta a la base de datos para obtener la sesión con el ID proporcionado.
    $sesion = Db::select("select * from sesiones where id = '$idSesion'"); 
    if (empty($sesion)) {
        // Si la sesión no existe, se crea un mensaje de error y se redirige al usuario a la página de perfil.
        Flash::crearMensaje("error", "La sesión que intentas eliminar no existe.", "/perfil");
    }

    // Se obtiene la sesión actual del usuario.
    $sesionActual = SesionUsuario::obtener();
    
    // Se verifica si la sesión que se intenta eliminar es la sesión actualmente utilizada por el usuario.
    if ($sesion[0]["token"] === $sesionActual["token"]) {
        // Si se intenta eliminar la sesión actual, se crea un mensaje de error y se redirige al usuario a la página de perfil.
        Flash::crearMensaje("error", "No puedes eliminar la sesión que estás utilizando actualmente.", "/perfil");
    }
    
    // Se elimina la sesión de la base de datos.
    Db::delete("delete from sesiones where id = '$idSesion'");
    
    // Se crea un mensaje de éxito y se redirige al usuario a la página de perfil.
    Flash::crearMensaje("exito", "La sesión ha sido eliminada correctamente.", "/perfil");
  }
?>
