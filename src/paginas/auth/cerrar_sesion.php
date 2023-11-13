<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(true);
?>
<?php
    // Se requieren los archivos "sesion.php" y "flash.php", que probablemente contienen utilidades relacionadas con sesiones y mensajes flash.
    require_once "utilidades/sesion.php";
    require_once "utilidades/flash.php";

    // Se verifica si la solicitud HTTP es de tipo GET.
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        // Se utiliza la clase SesionUsuario para eliminar la sesión del usuario.
        SesionUsuario::eliminar();
        
        // Se crea un mensaje flash de éxito con un mensaje específico y se redirige al usuario a la raíz ("/").
        Flash::crearMensaje("exito", "Has cerrado sesión con éxito. ¡Hasta pronto!", "/");
    }
?>
