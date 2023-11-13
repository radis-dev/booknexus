<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(false);
?>

<?php
  // Se requieren los archivos "sesion.php", "flash.php", y "db.php", que contienen utilidades relacionadas con sesiones, mensajes flash y acceso a la base de datos, respectivamente.
  require_once "utilidades/sesion.php";
  require_once "utilidades/flash.php";
  require_once "utilidades/db.php";

  // Se verifica si la solicitud HTTP es de tipo POST.
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se obtienen los datos del formulario de inicio de sesión.
    $correoElectronico = $_POST["correo_electronico"] ?? null;
    $contrasena = $_POST["contrasena"] ?? null;
    
    // Se validan los campos del formulario.
    if (empty($correoElectronico)) {
      Flash::crearMensaje("error", "El correo electrónico es obligatorio.", "/auth/iniciar_sesion");
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $correoElectronico)) {
      Flash::crearMensaje("error", "El correo electrónico no es válido.", "/auth/iniciar_sesion");
    }

    if (empty($contrasena)) {
      Flash::crearMensaje("error", "La contraseña es obligatoria.", "/auth/iniciar_sesion");
    }

    // Se realiza una consulta a la base de datos para obtener al usuario con el correo electrónico proporcionado.
    $usuario = Db::select("select * from usuarios where correo_electronico = '$correoElectronico'"); 
    
    // Se verifica la existencia del usuario y se compara la contraseña proporcionada con la almacenada en la base de datos.
    if (empty($usuario) || !password_verify($contrasena, $usuario[0]["contrasena"])) {
      Flash::crearMensaje("error", "El correo electrónico o la contraseña son inválidos.", "/auth/iniciar_sesion");
    } else {
      // Si la autenticación es exitosa, se crea una sesión para el usuario y se muestra un mensaje de bienvenida.
      SesionUsuario::crear($usuario[0]["id"]);
      Flash::crearMensaje("exito", "¡Bienvenido a BookNexus, {$usuario[0]['nombre_usuario']}!", "/");
    }
  }
?>
<!DOCTYPE html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Iniciar sesion");
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
              Iniciar sesion en Book<span class="text-blue-500">Nexus</span>
            </h1>
          </div>
          <form action="/auth/iniciar_sesion" method="post" class="flex flex-col space-y-5">
            <div class="flex flex-col space-y-2">
              <label class="text-sm font-semibold text-white campo-requerido">Correo electronico</label>
              <input
                type="email"
                name="correo_electronico"
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
            <button class="rounded-md bg-blue-500 p-1.5 text-sm font-semibold text-white hover:bg-blue-600">
              Iniciar sesion
            </button>
          </form>
        <a href="/auth/registrarse" class="rounded-md p-1.5 text-center text-sm font-semibold text-white hover:bg-neutral-600">
          ¿No tienes una cuenta? Registrate
        </a>
      </section>
      </div>
    </main>
  </body>
</html>
