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
  // Incluye el archivo necesario
  require_once "utilidades/db.php";

  // Verifica si la solicitud es de tipo GET
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Obtiene todos los libros de la base de datos
    $libros = Db::select("select * from libros");
  }
?>

<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Libros");
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
            <h2 class="text-xl text-white font-medium mb-4">Registrar libro</h2>
            <form action="/administracion/libros/registrar_libro" method="post" class="flex flex-col space-y-5">
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  Titulo
                </label>
                <input
                  type="text"
                  name="titulo"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  ISBN
                </label>
                <input
                  type="text"
                  name="isbn"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  Fecha de publicacion
                </label>
                <input
                  type="date"
                  name="fecha_publicacion"
                  class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
                  required
                  />
              </div>
              <button class="rounded-md bg-blue-500 p-1.5 text-sm font-semibold text-white hover:bg-blue-600">
                Registrar
              </button>
            </form>
          </div>
        </section>
        <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <h2 class="text-xl text-white font-medium mb-4">Libros</h2>
          <table class="overflow-hidden rounded-md min-w-full divide-y-2 divide-neutral-600">
              <thead class="text-center">
                <tr>
                    <th class="px-4 py-2 font-medium text-white">Titulo</th>
                    <th class="px-4 py-2 font-medium text-white">ISBN</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de publicacion</th>
                    <th class="px-4 py-2"></th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php foreach($libros as $libro): ?>
                    <tr>
                        <td class="px-4 py-2 text-white"><?= $libro["titulo"] ?></td>
                        <td class="px-4 py-2 text-white"><?= $libro["isbn"] ?></td>
                        <td class="px-4 py-2 text-white"><?= $libro["fecha_publicacion"] ?></td>
                        <td class="flex flex-row space-x-2 px-4 py-2 text-white">
                          <form class="w-full" action="/administracion/libros/eliminar_libro" method="post">
                            <button type="submit" class="flex justify-center rounded-md p-2 w-full text-sm font-semibold text-white bg-red-600 hover:bg-red-700">Eliminar</button>
                            <input type="hidden" name="id_libro" value="<?= $libro["id"] ?>">
                          </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
        </div>
      </section>
    </main>
  </body>
</html>
