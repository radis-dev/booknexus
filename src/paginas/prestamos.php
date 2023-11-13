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
  // Se requieren los archivos "db.php", "sesion.php", y "prestamos.php", que contienen utilidades relacionadas con la base de datos, gestión de sesiones y manejo de préstamos, respectivamente.
  require_once "utilidades/db.php";
  require_once "utilidades/sesion.php";
  require_once "utilidades/prestamos.php";

  // Se verifica si la solicitud HTTP es de tipo GET.
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Se obtiene la sesión actual del usuario.
    $sesion = SesionUsuario::obtener();

    // Se realiza una consulta a la base de datos para obtener los préstamos del usuario actual.
    // La consulta se une con las tablas "prestamos", "ejemplares", y "libros" para obtener información detallada sobre los préstamos.
    $prestamos = Db::select("select P.id as 'id_prestamo', LI.titulo as 'titulo', P.fecha_devolucion as 'fecha_devolucion', P.fecha_creacion as 'fecha_creacion', P.fecha_entrega as 'fecha_entrega'  
    from prestamos P left join ejemplares E on P.id_ejemplar = E.id left join libros LI on E.id_libro = LI.id 
    where P.id_usuario = {$sesion["id_usuario"]} 
    order by P.fecha_entrega desc");
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
          <h2 class="text-xl text-white font-medium mb-4">Prestamos</h2>
          <table class="overflow-hidden rounded-md min-w-full divide-y-2 divide-neutral-600">
              <thead class="text-center">
                <tr>
                    <th class="px-4 py-2 font-medium text-white">#</th>
                    <th class="px-4 py-2 font-medium text-white">Titulo</th>
                    <th class="px-4 py-2 font-medium text-white">Estado</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de creacion</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de devolucion</th>
                    <th class="px-4 py-2 font-medium text-white">Fecha de entrega</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php foreach($prestamos as $prestamo): ?>
                  <tr>
                      <!-- Columna para mostrar el ID del préstamo -->
                      <td class="px-4 py-2 text-white"><?= $prestamo["id_prestamo"] ?></td>
                      
                      <!-- Columna para mostrar el título del libro prestado -->
                      <td class="px-4 py-2 text-white"><?= $prestamo["titulo"] ?? "Desconocido" ?></td>
                      
                      <!-- Columna para mostrar el estado del préstamo -->
                      <td class="px-4 py-2 text-white">
                          <?php
                          // Se determina el estado del préstamo utilizando la función obtenerEstado de la clase Prestamos.
                          $estado = Prestamos::obtenerEstado($prestamo["fecha_entrega"], $prestamo["fecha_creacion"], $prestamo["fecha_devolucion"]);
                          
                          // Se utiliza una estructura condicional para mostrar un mensaje según el estado del préstamo.
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
                      
                      <!-- Columnas para mostrar fechas relacionadas con el préstamo -->
                      <td class="px-4 py-2 text-white"><?= $prestamo["fecha_creacion"] ?></td>
                      <td class="px-4 py-2 text-white"><?= $prestamo["fecha_devolucion"] ?></td>
                      <td class="px-4 py-2 text-white"><?= $prestamo["fecha_entrega"] ?? "Desconocido" ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
        </div>
      </section>
    </main>
  </body>
</html>
