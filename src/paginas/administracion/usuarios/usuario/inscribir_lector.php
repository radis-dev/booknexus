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

    // Se obtienen y filtran los datos del formulario relacionados con la inscripción de un lector.
    $nif = $_POST["nif"] ?? null;
    $nombre = $_POST["nombre"] ?? null;
    $apellidos = $_POST["apellidos"] ?? null;
    $fechaNacimiento = strtotime($_POST["fecha_nacimiento"]) ?? null;

    // Se verifica si el usuario con el ID proporcionado existe en la base de datos.
    $existeUsuario = Db::select("select * from usuarios where id = '$idUsuario'"); 
    if (empty($existeUsuario)) {
      // Si no existe el usuario, se crea un mensaje de error y se redirige a la página de administración de usuarios.
      Flash::crearMensaje("error", "No existe el usuario.", "/administracion/usuarios");
    } 

    // Se verifica si el usuario ya está registrado como lector.
    $existeLector = Db::select("select * from lectores where id_usuario = '$idUsuario'"); 
    if (!empty($existeLector)) {
      // Si el usuario ya es lector, se crea un mensaje de error y se redirige a la página específica del usuario.
      Flash::crearMensaje("error", "Este usuario ya está inscrito como lector", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se realizan verificaciones y validaciones adicionales para cada campo del formulario.
    if (empty($nif)) {
      Flash::crearMensaje("error", "El NIF es obligatorio.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    } elseif (!preg_match('/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $nif)) {
      Flash::crearMensaje("error", "El NIF no es válido.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    $existeNif = Db::select("select * from lectores where nif = '$nif'"); 
    if (!empty($existeNif)) {
      Flash::crearMensaje("error", "El NIF ya está registrado", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    if (empty($nombre)) {
      Flash::crearMensaje("error", "El nombre es obligatorio.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    } elseif (!preg_match('/^[a-zA-Z\' ]{1,50}$/', $nombre)) {
      Flash::crearMensaje("error", "El nombre no es válido.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    if (empty($apellidos)) {
      Flash::crearMensaje("error", "Los apellidos son obligatorios.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    } elseif (!preg_match('/^[a-zA-Z\' ]{1,100}$/', $apellidos)) {
      Flash::crearMensaje("error", "Los apellidos no son válidos.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    $existeNombreCompleto = Db::select("select * from lectores where nombre = '$nombre' and apellidos = '$apellidos'"); 
    if (!empty($existeNombreCompleto)) {
      Flash::crearMensaje("error", "El nombre completo ya está registrado", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    if (empty($fechaNacimiento)) {
      Flash::crearMensaje("error", "La fecha de nacimiento es obligatoria.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    } elseif ($fechaNacimiento == false) {
      Flash::crearMensaje("error", "La fecha de nacimiento no es válida.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    } elseif (strtotime(date('Y-m-d')) < $fechaNacimiento) {
      Flash::crearMensaje("error", "La fecha de nacimiento es superior a la fecha actual.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
    }

    // Se inserta un nuevo lector en la base de datos.
    $nuevoLector = Db::insert("insert into lectores (id_usuario, nif, nombre, apellidos, fecha_nacimiento) values ('$idUsuario', '$nif', '$nombre', '$apellidos', FROM_UNIXTIME($fechaNacimiento))");
    
    // Se crea un mensaje de éxito y se redirige a la página específica del usuario.
    Flash::crearMensaje("exito", "¡Felicidades! Tu cuenta se ha creado con éxito.", "/administracion/usuarios/usuario/?id_usuario=$idUsuario");
  }
?>
