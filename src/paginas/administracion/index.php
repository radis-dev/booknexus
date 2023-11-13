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
<!doctype html>
<html lang="es">
  <?php 
    require_once "componentes/ui/head.php"; 
    renderizarHead("Administracion");
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
  </body>
</html>
