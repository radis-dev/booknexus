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
  // Se requieren los archivos "db.php", "sesion.php" y "flash.php", que contienen utilidades relacionadas con la base de datos, gestión de sesiones y mensajes flash, respectivamente.
  require_once "utilidades/db.php";
  require_once "utilidades/sesion.php";
  require_once "utilidades/flash.php";

  // Se verifica si la solicitud HTTP es de tipo GET.
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Se obtiene y filtra el ID del usuario de la entrada de la URL, asegurándose de que solo contenga caracteres numéricos.
    $idUsuario = isset($_GET["id_usuario"]) ? preg_replace('/[^0-9]/', '', $_GET["id_usuario"]) : null;

    // Se realiza una consulta para obtener información sobre el usuario con el ID proporcionado.
    $usuario = Db::select("select u.nombre_usuario as 'nombre_usuario', u.correo_electronico as 'correo_electronico', u.fecha_creacion as 'fecha_creacion', r.nombre as 'rol_actual' from usuarios u left join roles r on u.id_rol = r.id where u.id = '$idUsuario'");
    if (empty($usuario)) {
      // Si no existe el usuario, se crea un mensaje de error y se redirige a la página de administración de usuarios.
      Flash::crearMensaje("error", "No existe el usuario.", "/administracion/usuarios");
    } 

    // Se realizan consultas adicionales para obtener información sobre las sesiones y el lector asociado al usuario.
    $sesiones = Db::select("select * from sesiones where id_usuario = '$idUsuario' order by fecha_creacion desc");
    $lector = Db::select("select * from lectores where id_usuario = '$idUsuario'");
    $roles = Db::select("select r.id AS 'id', r.nombre AS 'nombre' from roles r where r.id not in (select id_rol from usuarios where id = '$idUsuario') or r.id is null");
  }
?>

<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Usuario");
  ?>
  <body class="bg-neutral-700">
    <?php 
      include_once "componentes/ui/headers/headerAdmin.php";
      renderizarHeaderAdmin();
    ?>
    <?php 
      include_once "componentes/ui/alerta.php"; 
      renderizarAlerta();
    ?>
    <main class="flex flex-col space-y-2">
      <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <h2 class="text-xl text-white font-medium mb-4">Perfil</h2>
          <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4">
              <div class="w-full bg-neutral-700 rounded-md py-4 px-6 md:max-w-full">
                <h2 class="text-md text-white font-medium mb-2">Informacion de la cuenta</h2>
                <ul class="flex flex-col space-y-2">
                  <li class="text-white text-sm">Nombre de usuario: <?= $usuario[0]["nombre_usuario"] ?></li>
                  <li class="text-white text-sm">Correo electronico: <?= $usuario[0]["correo_electronico"] ?></li>
                  <li class="text-white text-sm">Fecha de creacion: <?= $usuario[0]["fecha_creacion"] ?></li>
                  <li class="text-white text-sm">Rol actual: <?= $usuario[0]["rol_actual"] ?? "Desconocido" ?></li>
                </ul>
              </div>
              <?php if(!empty($lector)): ?>
                <div class="w-full bg-neutral-700 rounded-md py-4 px-6 md:max-w-full">
                  <h2 class="text-md text-white font-medium mb-2">Informacion del lector</h2>
                    <ul class="flex flex-col space-y-2">
                      <li class="text-white text-sm">NIF: <?= $lector[0]["nif"] ?></li>
                      <li class="text-white text-sm">Nombre: <?= $lector[0]["nombre"] ?></li>
                      <li class="text-white text-sm">Apellidos: <?= $lector[0]["apellidos"] ?></li>
                      <li class="text-white text-sm">Fecha de nacimiento: <?= $lector[0]["fecha_nacimiento"] ?></li>
                      <li class="text-white text-sm">Fecha de registro: <?= $lector[0]["fecha_creacion"] ?></li>
                    </ul>
                    <form action="/administracion/usuarios/usuario/desinscribir_lector" method="post">
                      <input type="hidden" name="id_usuario" value="<?= $idUsuario ?>">
                      <button class="rounded-md bg-red-500 mt-4 p-1.5 text-sm font-semibold text-white hover:bg-red-600 w-full">
                        Desinscribir
                      </button>
                    </form>
                </div>
              <?php endif; ?>
          </div>
        </div>
      </section>
      <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <h2 class="text-xl text-white font-medium mb-4">Cambiar rol</h2>
          <form action="/administracion/usuarios/usuario/cambiar_rol" method="post" class="flex flex-col space-y-5">
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  Roles
                </label>
                <select name="id_rol" size="2" class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none" required>
                  <?php foreach($roles as $rol): ?>
                    <option value="<?= $rol["id"] ?>"><?= $rol["nombre"] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button class="rounded-md bg-blue-500 p-1.5 text-sm font-semibold text-white hover:bg-blue-600">
                Cambiar
              </button>
              <input type="hidden" name="id_usuario" value="<?= $idUsuario ?>">
            </form>
        </div>
      </section>
      <?php if(empty($lector)): ?>
        <section class="mx-auto px-16 w-full">
          <div class="bg-neutral-800 rounded-md py-4 px-6">
            <h2 class="text-xl text-white font-medium mb-4">Inscribir lector</h2>
            <form action="/administracion/usuarios/usuario/inscribir_lector" method="post" class="flex flex-col space-y-5">
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  NIF
                </label>
                <input
                  type="text"
                  name="nif"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  Nombre
                </label>
                <input
                  type="text"
                  name="nombre"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">Apellidos</label>
                <input
                  type="text"
                  name="apellidos"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">Fecha de nacimiento</label>
                <input
                  type="date"
                  name="fecha_nacimiento"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <input type="hidden" name="id_usuario" value="<?= $idUsuario ?>">
              <button class="rounded-md bg-blue-500 p-1.5 text-sm font-semibold text-white hover:bg-blue-600">
                Inscribir
              </button>
            </form>
          </div>
        </section>
      <?php endif; ?>
      <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <h2 class="text-xl text-white font-medium mb-4">Sesiones</h2>
          <ul class="flex flex-col space-y-2">
            <?php foreach($sesiones as $indice => $sesion): ?>
              <li class="flex flex-row justify-between items-center bg-neutral-700 rounded-md py-4 px-6">
                <div class="flex-1 flex-row space-y-1">
                  <?php
                    $infoNavegador = Sesion::identificar($sesion["token"]);
                  ?>
                  <p class="font-medium text-white text-sm"><?= $infoNavegador['browser'] ?? 'Navegador Desconocido' ?> - <?= $infoNavegador['platform'] ?? 'Plataforma Desconocida' ?></p>
                  <p class="text-white text-sm"><?= $sesion["fecha_creacion"] ?></p>
                </div>
                <form action="/administracion/usuarios/usuario/eliminar_sesion" method="post">
                  <button type="submit" class="flex justify-center items-center rounded-md bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">Eliminar</button>
                  <input type="hidden" name="id_sesion" value="<?= $sesion["id"] ?>">
                  <input type="hidden" name="id_usuario" value="<?= $idUsuario ?>">
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
    </main>
  </body>
</html>
