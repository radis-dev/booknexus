<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(true);
?>
<?php
  // Incluye los archivos necesarios
  require_once "utilidades/db.php";
  require_once "utilidades/sesion.php";

  // Verifica si la solicitud es de tipo GET
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Obtiene la información de la sesión actual del usuario
    $sesion = SesionUsuario::obtener();

    // Obtiene la información del usuario actual
    $usuario = Db::select("select u.nombre_usuario as 'nombre_usuario', u.correo_electronico as 'correo_electronico', u.fecha_creacion as 'fecha_creacion', r.nombre as 'rol_actual' from usuarios u left join roles r on u.id_rol = r.id where u.id = '{$sesion['id_usuario']}'");

    // Obtiene la lista de sesiones del usuario, ordenadas por sesión actual y fecha de creación descendente
    $sesiones = Db::select("select * from sesiones where id_usuario = '{$sesion['id_usuario']}' order by case when token = '{$sesion['token']}' then 0 else 1 end, fecha_creacion desc");

    // Obtiene la información del lector asociado al usuario actual
    $lector = Db::select("select * from lectores where id_usuario = '{$sesion['id_usuario']}'");
  }
?>

<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Perfil");
  ?>
  <body class="bg-neutral-700">
    <?php 
      include_once "componentes/ui/headers/headerInicio.php";
      renderizarHeaderInicio();
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
                </div>
              <?php endif; ?>
          </div>
        </div>
      </section>
      <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <h2 class="text-xl text-white font-medium mb-4">Sesiones</h2>
          <ul class="flex flex-col space-y-2">
            <?php foreach($sesiones as $indice => $sesion): ?>
              <li class="flex flex-row justify-between items-center bg-neutral-700 rounded-md py-4 px-6">
                <div class="flex-1 flex-row space-y-1">
                  <?php
                    // Obtiene información sobre el navegador y sistema operativo de la sesión actual
                    $infoNavegador = Sesion::identificar($sesion["token"]);
                  ?>
                  <p class="font-medium text-white text-sm"><?= $infoNavegador['browser'] ?? 'Navegador Desconocido' ?> - <?= $infoNavegador['platform'] ?? 'Plataforma Desconocida' ?></p>
                  <p class="text-white text-sm"><?= $sesion["fecha_creacion"] ?></p>
                </div>
                <?php if($indice != 0): ?>
                  <form action="/perfil/eliminar_sesion" method="post">
                    <button type="submit" class="flex justify-center items-center rounded-md bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">Eliminar</button>
                    <input type="hidden" name="id_sesion" value="<?= $sesion["id"] ?>">
                  </form>
                <?php endif; ?>  
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
    </main>
  </body>
</html>
