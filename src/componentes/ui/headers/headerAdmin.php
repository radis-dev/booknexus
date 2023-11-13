<?php
  // Función para renderizar el encabezado de la interfaz de administración
  function renderizarHeaderAdmin() {
?>
<header class="mx-auto px-16 py-2">
  <div class="flex flex-col justify-between items-center bg-neutral-800 rounded-md space-y-4 py-4 px-6 md:flex-row md:space-x-6 md:space-y-0">

    <!-- Enlace y logo del sistema -->
    <a href="/administracion" class="flex items-center">
      <h1 class="font-semibold text-white text-xl">
        Book<span class="text-blue-500">Nexus</span>
      </h1>
    </a>

    <!-- Navegación con enlaces a las secciones de administración -->
    <nav class="flex-1">
      <ul class="flex flex-row space-x-6">
        <li class="text-white font-semibold border-b-2 border-transparent hover:border-blue-500">
          <a href="/administracion">
            Inicio
          </a>
        </li>
        <li class="text-white font-semibold border-b-2 border-transparent hover:border-blue-500">
          <a href="/administracion/usuarios">
            Usuarios
          </a>
        </li>
        <li class="text-white font-semibold border-b-2 border-transparent hover:border-blue-500">
          <a href="/administracion/prestamos">
            Prestamos
          </a>
        </li>
        <li class="text-white font-semibold border-b-2 border-transparent hover:border-blue-500">
          <a href="/administracion/ejemplares">
            Ejemplares
          </a>
        </li>
        <li class="text-white font-semibold border-b-2 border-transparent hover:border-blue-500">
          <a href="/administracion/libros">
            Libros
          </a>
        </li>
      </ul>
    </nav>

    <!-- Enlaces adicionales y opciones de administración -->
    <div class="flex flex-row space-x-2">
      <a href="/" class="flex justify-center rounded-md bg-blue-500 p-2 w-28 text-sm font-semibold text-white hover:bg-blue-600">
        Volver al inicio
      </a>
      <a href="/auth/cerrar_sesion" class="flex justify-center rounded-md p-2 w-28 text-sm font-semibold text-white bg-neutral-600 hover:bg-neutral-700">
        Cerrar sesion
      </a>
    </div>
  </div>
</header>
<?php
  }
?>

