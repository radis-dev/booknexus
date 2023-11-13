<?php
  // Función para renderizar la sección head del documento HTML
  function renderizarHead($titulo) {
?>
  <head>
    <!-- Configuración del conjunto de caracteres y escala inicial -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Título de la página, con el valor proporcionado como parámetro -->
    <title>Booknexus - <?php echo $titulo ?></title>

    <!-- Inclusión de la biblioteca Tailwind CSS desde un CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Enlace al archivo de estilos general.css en la carpeta public/css -->
    <link rel="stylesheet" href="/public/css/general.css">
  </head>
<?php
  }
?>
