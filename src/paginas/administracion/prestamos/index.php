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
  // Incluye los archivos necesarios
  require_once "utilidades/db.php";
  require_once "utilidades/prestamos.php";

  // Verifica si la solicitud es de tipo GET
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Obtiene la lista de préstamos con información adicional sobre libros, lectores y ejemplares, ordenados por fecha de entrega descendente
    $prestamos = Db::select("select P.id as 'id_prestamo', LI.titulo as 'titulo', P.fecha_devolucion as 'fecha_devolucion', P.fecha_creacion as 'fecha_creacion', P.fecha_entrega as 'fecha_entrega', LE.id_usuario as 'id_usuario', concat(LE.nombre, ' ', LE.apellidos) as 'nombre_completo'
    from prestamos P
    join lectores LE on P.id_usuario = LE.id_usuario
    left join ejemplares E on P.id_ejemplar = E.id
    left join libros LI on E.id_libro = LI.id
    order by P.fecha_entrega desc");

    // Obtiene la lista de ejemplares que no están actualmente prestados
    $ejemplares = Db::select("select e.id as 'id_ejemplar', l.titulo as 'titulo', l.isbn as 'isbn' from ejemplares e left join prestamos p on e.id = p.id_ejemplar and p.fecha_entrega is null inner join libros l on e.id_libro = l.id where p.id is null order by l.id");

    // Obtiene la lista de lectores con información sobre su ID, nombre completo y NIF
    $lectores = Db::select("select l.id_usuario, concat(l.nombre, ' ', l.apellidos) as 'nombre_completo', l.nif from lectores l");
  }
?>

<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Prestamos");
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
            <h2 class="text-xl text-white font-medium mb-4">Registrar prestamo</h2>
            <form action="/administracion/prestamos/registrar_prestamo" method="post" class="flex flex-col space-y-5">
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  Lector/a
                </label>
                <select name="id_usuario" size="4" class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none" required>
                  <?php foreach($lectores as $lector): ?>
                    <option value="<?= $lector["id_usuario"] ?>">(<?= $lector["nif"] ?>) <?= $lector["nombre_completo"] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="flex flex-col space-y-2">
                <label class="text-sm font-semibold text-white campo-requerido">
                  Ejemplar
                </label>
                <select name="id_ejemplar" size="4" class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none" required>
                  <?php foreach($ejemplares as $ejemplar): ?>
                    <option value="<?= $ejemplar["id_ejemplar"] ?>">(<?= $ejemplar["id_ejemplar"] ?>) Titulo: <?= $ejemplar["titulo"] ?> - ISBN: <?= $ejemplar["isbn"] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button class="rounded-md bg-blue-500 p-1.5 text-sm font-semibold text-white hover:bg-blue-600">
                Registrar
              </button>
            </form>
          </div>
        </section>
      <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <h2 class="text-xl text-white font-medium mb-4">Prestamos</h2>
          <table class="overflow-hidden rounded-md min-w-full divide-y-2 divide-neutral-600">
              <thead class="text-center">
                <tr>
                    <th class="px-4 py-2 font-medium text-white">#</th>
                    <th class="px-4 py-2 font-medium text-white">Lector/a</th>
                    <th class="px-4 py-2 font-medium text-white">Ejemplar</th>
                    <th class="px-4 py-2 font-medium text-white">Estado</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de creacion</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de devolucion</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de entrega</th>
                    <th class="px-4 py-2"></th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php foreach($prestamos as $prestamo): ?>
                    <tr>
                        <td class="px-4 py-2 text-white"><?= $prestamo["id_prestamo"] ?></td>
                        <td class="px-4 py-2 text-white underline hover:decoration-blue-500">
                          <a href="/administracion/usuarios/usuario/?id_usuario=<?= $prestamo["id_usuario"] ?>"><?= $prestamo["nombre_completo"] ?></a>
                        </td>
                        <td class="px-4 py-2 text-white"><?= $prestamo["titulo"] ?? "Desconocido" ?></td>
                        <td class="px-4 py-2 text-white">
                            <?php
                            $estado = Prestamos::obtenerEstado($prestamo["fecha_entrega"], $prestamo["fecha_creacion"], $prestamo["fecha_devolucion"]);
                            if ($estado == 1): ?>
                                <p class="text-yellow-500 font-medium">Prestado</p>
                            <?php elseif ($estado == 2): ?>
                                <p class="text-green-500 font-medium">Entregado</p>
                            <?php elseif ($estado == 0): ?>
                                <p class="text-red-500 font-medium">Retraso</p>
                            <?php elseif ($estado == 3): ?>
                                <p class="text-orange-500 font-medium">Entregado con retraso</p>
                            <?php else: ?>
                                <p class="text-white font-medium">Desconocido</p>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-white"><?= $prestamo["fecha_creacion"] ?></td>
                        <td class="px-4 py-2 text-white"><?= $prestamo["fecha_devolucion"] ?></td>
                        <td class="px-4 py-2 text-white"><?= $prestamo["fecha_entrega"] ?? "Desconocido" ?></td>
                        <?php if($estado == 1 || $estado == 0): ?>
                          <td class="flex flex-row space-x-2 px-4 py-2 text-white">
                            <form action="/administracion/prestamos/finalizar_prestamo" method="post">
                              <button type="submit" class="flex justify-center rounded-md p-2 w-28 text-sm font-semibold text-white bg-green-600 hover:bg-green-700">Finalizar</button>
                              <input type="hidden" name="id_prestamo" value="<?= $prestamo["id_prestamo"] ?>">
                            </form>
                          </td>
                        <?php else: ?>
                        <td class="px-4 py-2"></td>
                        <?php endif; ?> 
                    </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
        </div>
      </section>
    </main>
  </body>
</html>
