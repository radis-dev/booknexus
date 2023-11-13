<?php
  // Se requiere el archivo "auth.php" que contiene la lógica para autenticar a los usuarios.
  require_once "middleware/auth.php";
  
  // Se utiliza el middleware de autenticación para permitir el acceso solo si el parámetro pasado es verdadero.
  AuthMiddleware::permitir(true);
?>
<?php
  // Se requiere el archivo "lector.php" que contiene la lógica para el middleware de Lector.
  require_once "middleware/lector.php";
  
  // Se utiliza el middleware de Lector para permitir el acceso según las reglas del middleware.
  LectorMiddleware::permitir();
?>
<?php
  // Se requiere el archivo "db.php", que contiene utilidades relacionadas con la base de datos.
  require_once "utilidades/db.php";

  // Se verifica si la solicitud HTTP es de tipo GET.
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Se filtra y obtiene la consulta de búsqueda de la variable $_GET, asegurándose de que solo contenga caracteres alfanuméricos y espacios.
    $consulta = isset($_GET["consulta"]) ? preg_replace('/[^A-Za-z0-9 ]/', '', $_GET["consulta"]) : null;

    // Consulta SQL base para obtener información sobre libros y su disponibilidad.
    $sql = "select l.id as 'id', l.isbn as 'isbn', l.titulo as 'titulo', l.fecha_publicacion as 'fecha_publicacion', 
    sum(case when p.id_ejemplar is not null then 1 else 0 end) as 'cantidad_prestados', count(e.id) - sum(case when p.id_ejemplar is not null then 1 else 0 end) as 'cantidad_disponibles' 
    from ejemplares e 
    inner join libros l on e.id_libro = l.id 
    left join prestamos p on e.id = p.id_ejemplar and p.fecha_entrega is null";

    // Se añade condición WHERE a la consulta SQL si se proporciona una consulta de búsqueda.
    if (!empty($consulta)) {
      $sql .= " where l.titulo like '%{$consulta}%' or l.isbn like '%{$consulta}%'";
    }

    // Se completa la consulta SQL con la agrupación y ordenamiento adecuados.
    $sql .= " group by l.id order by cantidad_disponibles desc";

    // Se ejecuta la consulta SQL y se almacenan los resultados en la variable $libros.
    $libros = Db::select($sql);
  }
?>
<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Buscador");
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
          <h2 class="text-xl text-white font-medium mb-4">Buscador</h2>
          <form action="/buscador" method="get">
            <input
              type="search"
              name="consulta"
              class="w-full rounded-md border-[2px] border-neutral-700 bg-transparent p-1.5 text-sm text-white focus:border-blue-500 focus:outline-none"
            />
          </form>
        </div>
      </section>
      <section class="mx-auto px-16 w-full">
        <div class="bg-neutral-800 rounded-md py-4 px-6">
          <!-- Encabezado que muestra la cantidad de libros encontrados en la búsqueda -->
          <h2 class="text-xl text-white font-medium mb-4">Se ha encontrado <?= count($libros) ?> libro/s</h2>
          <table class="overflow-hidden rounded-md min-w-full divide-y-2 divide-neutral-600">
              <thead class="text-center">
                <tr>
                    <th class="px-4 py-2 font-medium text-white">Titulo</th>
                    <th class="px-4 py-2 font-medium text-white">ISBN</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha publicacion</th>
                    <th class="px-4 py-2 font-medium text-white">Cantidad disponibles</th>
                    <th class="px-4 py-2 font-medium text-white">Cantidad prestados</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php foreach($libros as $libro): ?>
                  <tr>
                      <!-- Columna para mostrar el título del libro -->
                      <td class="px-4 py-2 text-white"><?= $libro["titulo"] ?></td>
                      
                      <!-- Columna para mostrar el ISBN del libro -->
                      <td class="px-4 py-2 text-white"><?= $libro["isbn"] ?></td>
                      
                      <!-- Columna para mostrar la fecha de publicación del libro -->
                      <td class="px-4 py-2 text-white"><?= $libro["fecha_publicacion"] ?></td>
                      
                      <!-- Columna para mostrar la cantidad de ejemplares disponibles, con color de texto verde si hay disponibles, y rojo si no hay disponibles -->
                      <td class="px-4 py-2 text-green-500">
                          <?php if($libro["cantidad_disponibles"] == 0): ?>
                              <p class="text-red-500"><?= $libro["cantidad_disponibles"] ?></p>
                          <?php else: ?>
                              <p class="text-green-500"><?= $libro["cantidad_disponibles"] ?></p>
                          <?php endif; ?>
                      </td>
                      
                      <!-- Columna para mostrar la cantidad de ejemplares prestados, con color de texto verde si no hay ejemplares prestados, y rojo si hay ejemplares prestados -->
                      <td class="px-4 py-2 text-red-500">
                          <?php if($libro["cantidad_prestados"] == 0): ?>
                              <p class="text-green-500"><?= $libro["cantidad_prestados"] ?></p>
                          <?php else: ?>
                              <p class="text-red-500"><?= $libro["cantidad_prestados"] ?></p>
                          <?php endif; ?>
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
