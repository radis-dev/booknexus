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
  // Se requiere el archivo "db.php", que contiene utilidades relacionadas con la base de datos.
  require_once "utilidades/db.php";

  // Se verifica si la solicitud HTTP es de tipo GET.
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Se ejecuta una consulta SQL para obtener información sobre usuarios y si son lectores.
    // La consulta utiliza un LEFT JOIN con la tabla "lectores" para incluir información adicional si un usuario es lector.
    $usuarios = Db::select("select u.id, r.nombre as 'rol_actual', u.correo_electronico, u.nombre_usuario, 
    case when l.id_usuario is not null then 1 else 0 end as 'es_lector', 
    concat(l.nombre, ' ', l.apellidos) as 'nombre_completo', l.nif from usuarios u 
    left join lectores l on u.id = l.id_usuario left join roles r on u.id_rol = r.id");
  }
?>

<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Usuarios");
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
          <h2 class="text-xl text-white font-medium mb-4">Usuarios</h2>
          <table class="overflow-hidden rounded-md min-w-full divide-y-2 divide-neutral-600">
              <thead class="text-center">
                <tr>
                  <th class="px-4 py-2 font-medium text-white">Nombre de usuario</th>
                  <th class="px-4 py-2 font-medium text-white">Correo electronico</th>
                  <th class="px-4 py-2 font-medium text-white">Rol actual</th>
                  <th class="px-4 py-2 font-medium text-white">¿Es lector?</th>
                  <th class="px-4 py-2 font-medium text-white">NIF</th>
                  <th class="px-4 py-2 font-medium text-white">Nombre completo</th>
                  <th class="px-4 py-2"></th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php foreach($usuarios as $usuario): ?>
                  <tr class="text-center">
                    <td class="px-4 py-2 text-white"><?= $usuario["nombre_usuario"] ?></td>
                    <td class="px-4 py-2 text-white"><?= $usuario["correo_electronico"] ?></td>
                    <td class="px-4 py-2 text-white"><?= $usuario["rol_actual"] ?? "Desconocido" ?></td>
                    <td class="px-4 py-2">
                      <?php if($usuario["es_lector"]): ?>
                        <p class="text-green-500 font-medium">Si</p>
                      <?php else: ?>
                        <p class="text-red-500 font-medium">No</p>
                      <?php endif; ?> 
                    </td>
                    <td class="px-4 py-2 text-white"><?= $usuario["nif"] ?? "Desconocido" ?></td>
                    <td class="px-4 py-2 text-white"><?= $usuario["nombre_completo"] ?? "Desconocido" ?></td>
                    <td class="px-4 py-2">
                      <a href="/administracion/usuarios/usuario?id_usuario=<?= $usuario["id"] ?>" class="flex justify-center rounded-md p-2 w-28 text-sm font-semibold text-white bg-neutral-600 hover:bg-neutral-700">
                        Detalles
                      </a>
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
