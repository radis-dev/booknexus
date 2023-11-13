<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(false);
?>

<?php
  // Se requieren los archivos "flash.php" y "db.php", que contienen utilidades relacionadas con mensajes flash y acceso a la base de datos, respectivamente.
  require_once "utilidades/flash.php";
  require_once "utilidades/db.php";

  // Se verifica si la solicitud HTTP es de tipo POST.
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se obtienen los datos del formulario de registro.
    $correoElectronico = $_POST["correo_electronico"] ?? null;
    $nombreUsuario = $_POST["nombre_usuario"] ?? null;
    $contrasena = $_POST["contrasena"] ?? null;

    // Se validan los campos del formulario.
    if (empty($correoElectronico)) {
      Flash::crearMensaje("error", "El correo electrónico es obligatorio.", "/auth/registrarse");
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $correoElectronico)) {
      Flash::crearMensaje("error", "El correo electrónico no es válido.", "/auth/registrarse");
    }

    if (empty($nombreUsuario)) {
      Flash::crearMensaje("error", "El nombre de usuario es obligatorio.", "/auth/registrarse");
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,15}$/', $nombreUsuario)) {
      Flash::crearMensaje("error", "El nombre de usuario debe tener entre 4 y 15 caracteres y solo puede contener letras, números y guiones bajos (sin espacios).", "/auth/registrarse");
    }

    if (empty($contrasena)) {
      Flash::crearMensaje("error", "La contraseña es obligatoria.", "/auth/registrarse");
    } elseif (!preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/', $contrasena)) {
      Flash::crearMensaje("error", "La contraseña debe tener al menos 8 caracteres alfanuméricos, incluyendo al menos una mayúscula, una minúscula y un número.", "/auth/registrarse");
    }

    // Se verifica si el correo electrónico ya está registrado.
    $existeCorreoElectronico = Db::select("select * from usuarios where correo_electronico = '$correoElectronico'"); 
    if (!empty($existeCorreoElectronico)) {
      Flash::crearMensaje("error", "El correo electrónico ya está registrado. Por favor, elige otro correo electrónico.", "/auth/registrarse");
    }

    // Se verifica si el nombre de usuario ya está registrado.
    $existeNombreUsuario = Db::select("select * from usuarios where nombre_usuario = '$nombreUsuario'"); 
    if (!empty($existeNombreUsuario)) {
      Flash::crearMensaje("error", "El nombre de usuario ya está registrado. Por favor, elige otro nombre de usuario.", "/auth/registrarse");
    }

    // Se hashea la contraseña antes de almacenarla en la base de datos.
    $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

    // Se inserta el nuevo usuario en la base de datos.
    $nuevoUsuario = Db::insert("insert into usuarios (id_rol, nombre_usuario, correo_electronico, contrasena) values ('4', '$nombreUsuario', '$correoElectronico', '$contrasenaHash')");
  
    // Se crea un mensaje flash de éxito y se redirige al usuario a la página de inicio de sesión.
    Flash::crearMensaje("exito", "¡Felicidades! Tu cuenta se ha creado con éxito.", "/auth/iniciar_sesion");
  }
?>
<!DOCTYPE html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Registrarse");
  ?>
  <body class="bg-neutral-700">
    <main class="mx-auto px-16">
      <div class="flex flex-col space-y-2 h-screen items-center justify-center">
        <?php 
          include_once "componentes/ui/alerta.php"; 
          renderizarAlerta();
        ?>
        <section class="flex w-[30rem] flex-col space-y-5 rounded-md bg-neutral-800 p-10">
          <div class="flex flex-col space-y-2 text-center">
            <h1 class="text-2xl font-semibold text-white">
              Unete a Book<span class="text-blue-500">Nexus</span> hoy
            </h1>
            <p class="text-justify text-xs font-semibold leading-relaxed text-white">
              Crear una cuenta te permite acceder miles de libros, seguir las
              novedades de tus autores favoritos y publicar tus propios libros
            </p>
          </div>
          <form action="/auth/registrarse" method="POST" class="flex flex-col space-y-5">
            <div class="flex flex-col space-y-2">
              <label class="text-sm font-semibold text-white campo-requerido">
                Correo electronico
              </label>
              <input
                type="email"
                name="correo_electronico"
                class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                required
              />
            </div>
            <div class="flex flex-col space-y-2">
              <label class="text-sm font-semibold text-white campo-requerido"
              >Nombre de usuario</label
            >
            <input
              type="text"
              name="nombre_usuario"
              class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
              required
            />
          </div>
          <div class="flex flex-col space-y-2">
            <label class="text-sm font-semibold text-white campo-requerido">Contraseña</label>
            <input
              type="password"
              name="contrasena"
              class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
              required
            />
          </div>
          <button
            class="rounded-md bg-blue-500 p-1.5 text-sm font-semibold text-white hover:bg-blue-600"
          >
            Registrarse
          </button>
        </form>
        <a
          href="/auth/iniciar_sesion"
          class="rounded-md p-1.5 text-center text-sm font-semibold text-white hover:bg-neutral-700"
            >¿Ya tienes una cuenta de Booknexus? Inicia sesion</a>
        </section>
      </div>
    </main>
  </body>
</html>
