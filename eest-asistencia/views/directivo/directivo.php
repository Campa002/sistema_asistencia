<?php
require_role('directivo');

date_default_timezone_set('America/Argentina/Buenos_Aires');

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';
$nombre_completo = trim($nombre . ' ' . $apellido);
$email = $_SESSION['email'] ?? '';
$dni = $_SESSION['dni'] ?? '';

$dias = [
    'Domingo', 'Lunes', 'Martes', 'Miércoles',
    'Jueves', 'Viernes', 'Sábado'
];

$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo',
    4 => 'abril', 5 => 'mayo', 6 => 'junio',
    7 => 'julio', 8 => 'agosto', 9 => 'septiembre',
    10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];

$ahora = time();
$fecha_larga =
    $dias[(int) date('w', $ahora)] . ', ' .
    date('j', $ahora) . ' de ' .
    $meses[(int) date('n', $ahora)] . ' de ' .
    date('Y', $ahora);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<link rel="stylesheet" href="../public/assets/css/directivo.css">
<link rel="shortcut icon" href="../public/assets/img/logo.webp" type="image/x-icon">
<div id="directivo-portal-root">


<!-- Overlay para mobile -->
<div class="overlay-sidebar" id="overlay-sidebar" onclick="cerrarSidebar()"></div>

<!-- Barra lateral -->
<aside class="barra-lateral" id="barra-lateral">
  <div class="barra-lateral__encabezado">
    <img id="nav-dashboard" onclick="showView('dashboard')" class="b" src="../public/assets/img/logo.webp" alt="">
    <div>
      <div class="barra-lateral__titulo">EEST N°1</div>
      <div class="barra-lateral__subtitulo">Eduardo Ader</div>
    </div>
  </div>

  <nav class="barra-lateral__nav">
    <button class="nav__item nav__item--activo" onclick="mostrarSeccion('dashboard', this)">
      <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
      Dashboard
    </button>
    <button class="nav__item" onclick="mostrarSeccion('solicitudes', this)">
      <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      Solicitudes de Acceso
    </button>
    <button class="nav__item" onclick="mostrarSeccion('reemplazos', this)">
      <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      Reemplazos
    </button>
    <button class="nav__item" onclick="mostrarSeccion('asistencia', this)">
      <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
      Asistencia Institucional
    </button>
    <button class="nav__item" onclick="mostrarSeccion('notificaciones', this)">
      <svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
      Notificaciones
    </button>
    <button class="nav__item" onclick="mostrarSeccion('perfil', this)">
      <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      Perfil
    </button>
  </nav>

  <div class="barra-lateral__cerrar-sesion">
    <form method="POST" action="index.php" class="js-logout-form">
      <input type="hidden" name="action" value="logout">
      <button type="submit" class="boton-cerrar-sesion">Cerrar sesión</button>
    </form>
  </div>
</aside>

<!-- Contenido principal -->
<div class="contenido-principal">

  <!-- Barra superior -->
  <header class="barra-superior">
    <div style="display:flex; align-items:center; gap:12px;">
      <button class="btn-menu-hamburguesa" onclick="abrirSidebar()">
        <svg viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
      </button>
      <span class="barra-superior__titulo" id="titulo-barra-superior">Sistema de Asistencia</span>
    </div>
    <div class="barra-superior__acciones">
      <button class="barra-superior__icono-btn">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/></svg>
      </button>
      <button class="barra-superior__icono-btn" onclick="mostrarSeccion('notificaciones', document.querySelectorAll('.nav__item')[4])">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
        <span class="insignia-notificacion"></span>
      </button>
      <div class="barra-superior__separador"></div>
      <div class="barra-superior__avatar" onclick="mostrarSeccion('perfil', document.querySelectorAll('.nav__item')[5])">
        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
    </div>
  </header>

  <!-- Area de contenido -->
  <main class="area-contenido">

    <!-- ===== DASHBOARD ===== -->
    <section class="seccion seccion--activa" id="seccion-dashboard">
      <div style="margin-bottom: 6px;">
        <div style="font-size: 26px; font-weight: 700;">Buenos días, <?php echo e($nombre); ?></div>
        <div style="font-size: 14px; color: var(--gris-texto);">Resumen institucional para hoy, <?php echo e($fecha_larga); ?>.</div>
      </div>

      <div style="height: 20px;"></div>

      <div class="cuadricula-estadisticas cuadricula-estadisticas--6col">
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">
            ASISTENCIA
            <span class="etiqueta-cambio etiqueta-cambio--verde">+2.4%</span>
          </div>
          <div class="tarjeta-estadistica__valor">94%</div>
          <div class="barra-progreso"><div class="barra-progreso__relleno" style="width:94%; background-color: var(--azul-primario);"></div></div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">PRESENTES</div>
          <div class="tarjeta-estadistica__valor">820</div>
          <div class="tarjeta-estadistica__descripcion">Alumnos en aula</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">AUSENTES</div>
          <div class="tarjeta-estadistica__valor tarjeta-estadistica__valor--rojo">120</div>
          <div class="tarjeta-estadistica__descripcion">Total inasistencias</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">LLEGADAS TARDE</div>
          <div class="tarjeta-estadistica__valor tarjeta-estadistica__valor--naranja">42</div>
          <div class="tarjeta-estadistica__descripcion">Registradas hoy</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">SOLICITUDES</div>
          <div class="tarjeta-estadistica__valor">8</div>
          <div class="tarjeta-estadistica__descripcion" style="color: var(--naranja); font-weight: 600;">Pendientes</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">REEMPLAZOS</div>
          <div class="tarjeta-estadistica__valor">12</div>
          <div class="tarjeta-estadistica__descripcion">Por asignar</div>
        </div>
      </div>

      <div class="cuadricula-dos-columnas">
        <!-- Grafico -->
        <div class="tarjeta-contenedor">
          <div class="tarjeta-contenedor__encabezado">
            <div class="tarjeta-contenedor__titulo">Asistencia por Curso</div>
            <div class="selector-turno">
              <button class="selector-turno__opcion selector-turno__opcion--activo" onclick="cambiarTurnoGrafico('manana', this)">Manana</button>
              <button class="selector-turno__opcion" onclick="cambiarTurnoGrafico('tarde', this)">Tarde</button>
            </div>
          </div>
          <div class="tarjeta-contenedor__cuerpo">
            <canvas id="grafico-asistencia" height="200"></canvas>
          </div>
        </div>

        <!-- Columna derecha -->
        <div style="display: flex; flex-direction: column; gap: 14px;">
          <!-- Accesos rapidos -->
          <div class="tarjeta-contenedor">
            <div class="tarjeta-contenedor__encabezado">
              <div class="tarjeta-contenedor__titulo">Accesos Rapidos</div>
            </div>
            <div class="tarjeta-contenedor__cuerpo">
              <div class="cuadricula-accesos">
                <div class="acceso-rapido">
                  <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  <span class="acceso-rapido__texto">Cerrar Planilla</span>
                </div>
                <div class="acceso-rapido">
                  <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                  <span class="acceso-rapido__texto">Buscar Alumno</span>
                </div>
                <div class="acceso-rapido">
                  <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                  <span class="acceso-rapido__texto">Nuevo Reemplazo</span>
                </div>
                <div class="acceso-rapido">
                  <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                  <span class="acceso-rapido__texto">Imprimir Reporte</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Estado del sistema -->
          <div class="tarjeta-sistema">
            <div class="tarjeta-sistema__titulo">Estado del Sistema</div>
            <div class="tarjeta-sistema__fila">
              <span>Sincronizacion Cloud</span>
              <span class="tarjeta-sistema__punto"></span>
            </div>
            <div class="tarjeta-sistema__fila">
              <span>Ultima Carga</span>
              <span>10:42 AM</span>
            </div>
            <div class="tarjeta-sistema__fila">
              <span>Preceptores Activos</span>
              <span>24 / 26</span>
            </div>
            <div class="tarjeta-sistema__cita">"La educacion tecnica es el pilar de la innovacion nacional."</div>
          </div>
        </div>
      </div>

      <div style="height: 20px;"></div>

      <!-- Avisos recientes -->
      <div class="tarjeta-contenedor">
        <div class="tarjeta-contenedor__encabezado">
          <div class="tarjeta-contenedor__titulo">Avisos Recientes</div>
          <button class="enlace-ver-todos" onclick="mostrarSeccion('notificaciones', document.querySelectorAll('.nav__item')[4])">Ver todos</button>
        </div>
        <div class="tarjeta-contenedor__cuerpo" style="padding: 4px 20px 8px;">
          <div class="aviso">
            <div class="aviso__linea aviso__linea--roja"></div>
            <svg class="aviso__icono" viewBox="0 0 24 24" fill="#DC3545"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
            <div class="aviso__contenido">
              <div class="aviso__titulo">Ausencia Imprevista - Preceptor 4 Anio</div>
              <div class="aviso__descripcion">Se requiere reemplazo para el turno tarde de hoy en la sede central.</div>
              <div class="aviso__tiempo">Hace 15 minutos</div>
            </div>
          </div>
          <div class="aviso">
            <div class="aviso__linea aviso__linea--azul"></div>
            <svg class="aviso__icono" viewBox="0 0 24 24" fill="#3498DB"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            <div class="aviso__contenido">
              <div class="aviso__titulo">Actualizacion de Protocolo de Asistencia</div>
              <div class="aviso__descripcion">Se ha actualizado la normativa para el registro de llegadas tarde.</div>
              <div class="aviso__tiempo">Hace 2 horas</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== SOLICITUDES DE ACCESO ===== -->
    <section class="seccion" id="seccion-solicitudes">
      <div style="font-size: 13px; color: var(--gris-texto); margin-bottom: 16px;">Solicitudes de Acceso</div>

      <div class="cuadricula-solicitudes">
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">PENDIENTES</div>
          <div class="tarjeta-estadistica__valor">24</div>
          <div style="font-size: 13px; color: var(--verde); display:flex; align-items:center; gap:4px; margin-top:4px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
            +12% vs ayer
          </div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">TOTAL HOY</div>
          <div class="tarjeta-estadistica__valor">48</div>
          <div style="font-size: 12px; color: var(--gris-texto); display:flex; align-items:center; gap:4px; margin-top:4px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            Actualizado hace 5m
          </div>
        </div>
      </div>

      <div class="tarjeta-contenedor">
        <div style="padding: 0 0 0 0;">
          <div style="display:flex; align-items:center; justify-content:space-between; padding: 14px 20px; border-bottom: 1px solid #f0f0f0;">
            <div class="tabs-filtro" style="padding:0; margin:0; gap:8px; display:flex; flex-wrap:wrap;">
              <button class="tab-btn tab-btn--activo">Todos <span class="tab-btn__conteo">72</span></button>
              <button class="tab-btn" onclick="filtrarSolicitudes(this)">Estudiantes</button>
              <button class="tab-btn" onclick="filtrarSolicitudes(this)">Padres</button>
              <button class="tab-btn" onclick="filtrarSolicitudes(this)">Preceptores</button>
            </div>
            <div style="display:flex; gap:10px;">
              <button class="boton-secundario">
                <svg viewBox="0 0 24 24"><path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39C20.25 4.95 19.78 4 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/></svg>
                Filtros
              </button>
              <button class="boton-secundario">
                <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z"/></svg>
                Exportar
              </button>
            </div>
          </div>

          <div class="tabla-contenedor">
            <table class="tabla">
              <thead>
                <tr>
                  <th>NOMBRE</th>
                  <th>DNI</th>
                  <th>CONTACTO</th>
                  <th>TIPO</th>
                  <th>FECHA</th>
                  <th>ESTADO</th>
                  <th>ACCIONES</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Mateo Berardi</strong></td>
                  <td>45.289.102</td>
                  <td><div>mateo.b@gmail.com</div><div style="font-size:12px; color:var(--gris-texto);">11 2345-6789</div></td>
                  <td><span class="insignia insignia--azul">Estudiante</span></td>
                  <td><div>14</div><div>May</div><div>2024</div></td>
                  <td><span class="indicador-estado indicador-estado--pendiente">Pendiente</span></td>
                  <td>
                    <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                    <button class="btn-accion btn-accion--aprobar"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></button>
                    <button class="btn-accion btn-accion--rechazar"><svg viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></button>
                  </td>
                </tr>
                <tr>
                  <td><strong>Laura Rodriguez</strong></td>
                  <td>28.910.453</td>
                  <td><div>l.rod@outlook.com</div><div style="font-size:12px; color:var(--gris-texto);">11 9876-5432</div></td>
                  <td><span class="insignia insignia--gris">Padre</span></td>
                  <td><div>13</div><div>May</div><div>2024</div></td>
                  <td><span class="indicador-estado indicador-estado--pendiente">Pendiente</span></td>
                  <td>
                    <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                    <button class="btn-accion btn-accion--aprobar"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></button>
                    <button class="btn-accion btn-accion--rechazar"><svg viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></button>
                  </td>
                </tr>
                <tr>
                  <td><strong>Mateo Berardi</strong></td>
                  <td>45.289.102</td>
                  <td><div>mateo.b@gmail.com</div><div style="font-size:12px; color:var(--gris-texto);">11 2345-6789</div></td>
                  <td><span class="insignia insignia--azul">Estudiante</span></td>
                  <td><div>14</div><div>May</div><div>2024</div></td>
                  <td><span class="indicador-estado indicador-estado--pendiente">Pendiente</span></td>
                  <td>
                    <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                    <button class="btn-accion btn-accion--aprobar"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></button>
                    <button class="btn-accion btn-accion--rechazar"><svg viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></button>
                  </td>
                </tr>
                <tr>
                  <td><strong>Laura Rodriguez</strong></td>
                  <td>28.910.453</td>
                  <td><div>l.rod@outlook.com</div><div style="font-size:12px; color:var(--gris-texto);">11 9876-5432</div></td>
                  <td><span class="insignia insignia--gris">Padre</span></td>
                  <td><div>13</div><div>May</div><div>2024</div></td>
                  <td><span class="indicador-estado indicador-estado--pendiente">Pendiente</span></td>
                  <td>
                    <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                    <button class="btn-accion btn-accion--aprobar"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></button>
                    <button class="btn-accion btn-accion--rechazar"><svg viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="paginacion">
            <span class="paginacion__texto">Mostrando 1 a 10 de 24 solicitudes</span>
            <div class="paginacion__botones">
              <button class="paginacion__btn">&lt;</button>
              <button class="paginacion__btn paginacion__btn--activo">1</button>
              <button class="paginacion__btn">2</button>
              <button class="paginacion__btn">3</button>
              <button class="paginacion__btn">&gt;</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== REEMPLAZOS ===== -->
    <section class="seccion" id="seccion-reemplazos">
      <div class="migaja-pan">Panel / <span>Reemplazos de Preceptores</span></div>

      <div class="encabezado-seccion">
        <div>
          <div class="seccion__titulo">Gestion de Reemplazos</div>
          <div class="seccion__subtitulo">Supervise y asigne coberturas para las ausencias del personal.</div>
        </div>
        <button class="boton-primario">
          <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
          Nuevo Reemplazo
        </button>
      </div>

      <div class="cuadricula-estadisticas cuadricula-estadisticas--4col">
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">Total Pendientes</div>
          <div style="display:flex; align-items:baseline; gap:10px;">
            <div class="tarjeta-estadistica__valor">12</div>
            <span class="etiqueta-cambio etiqueta-cambio--rojo">+3 hoy</span>
          </div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">Asignados hoy</div>
          <div style="display:flex; align-items:baseline; gap:10px;">
            <div class="tarjeta-estadistica__valor">08</div>
            <span class="etiqueta-cambio" style="background:#e8f4fd; color:#084298;">Normal</span>
          </div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-gris">
          <div class="tarjeta-estadistica__etiqueta">Efectividad Cobertura</div>
          <div style="display:flex; align-items:baseline; gap:10px;">
            <div class="tarjeta-estadistica__valor">94%</div>
            <span class="etiqueta-cambio insignia--gris" style="background:#f0f0f0; color:#555; border-radius:4px; padding:2px 8px; font-size:11px; font-weight:600;">Semanal</span>
          </div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">Preceptores Disponibles</div>
          <div style="display:flex; align-items:center; justify-content:space-between;">
            <div class="tarjeta-estadistica__valor">05</div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="var(--gris-texto)"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
          </div>
        </div>
      </div>

      <div class="tarjeta-contenedor">
        <div class="barra-filtros">
          <select class="filtro-select">
            <option>Turno: Todos</option>
            <option>Manana</option>
            <option>Tarde</option>
            <option>Vespertino</option>
          </select>
          <div class="filtro-activo">
            Prioridad: Alta
            <button class="filtro-activo__cerrar">x</button>
          </div>
          <div class="filtro-activo">
            Fecha: 24/05/2024
            <button class="filtro-activo__cerrar">x</button>
          </div>
          <div class="barra-filtros__acciones-derecha">
            <button class="enlace-limpiar">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39C20.25 4.95 19.78 4 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/></svg>
              Limpiar filtros
            </button>
            <button class="btn-accion" style="background:none; border:none; cursor:pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--gris-texto)"><path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z"/></svg>
            </button>
          </div>
        </div>

        <div class="tabla-contenedor">
          <table class="tabla">
            <thead>
              <tr>
                <th>PRECEPTOR TITULAR</th>
                <th>CURSO / DIV</th>
                <th>TURNO / HORARIO</th>
                <th>FECHA</th>
                <th>MOTIVO</th>
                <th>PRIORIDAD</th>
                <th>ESTADO</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar-iniciales avatar-iniciales--rm">RM</div>
                    <span>Rodriguez, Marta</span>
                  </div>
                </td>
                <td>4to 1ra - Informatica</td>
                <td><div>Manana</div><div style="font-size:12px; color:var(--gris-texto);">07:30 - 12:00</div></td>
                <td><strong>24 Mayo 2024</strong></td>
                <td>L. Medica - Art 70</td>
                <td><span class="prioridad prioridad--critica">Critica</span></td>
                <td><span class="indicador-estado indicador-estado--sin-asignar">Sin Asignar</span></td>
              </tr>
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar-iniciales avatar-iniciales--jl">JL</div>
                    <span>Lopez, Jorge</span>
                  </div>
                </td>
                <td>6to 2da - Electromec.</td>
                <td><div>Tarde</div><div style="font-size:12px; color:var(--gris-texto);">13:30 - 18:00</div></td>
                <td><strong>24 Mayo 2024</strong></td>
                <td>Capacitacion Docente</td>
                <td><span class="prioridad prioridad--normal">Normal</span></td>
                <td><span class="indicador-estado indicador-estado--asignado">Asignado</span></td>
              </tr>
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar-iniciales avatar-iniciales--ga">GA</div>
                    <span>Garcia, Ana</span>
                  </div>
                </td>
                <td>2do 3ra - Ciclo Basico</td>
                <td><div>Manana</div><div style="font-size:12px; color:var(--gris-texto);">07:30 - 12:00</div></td>
                <td><strong>25 Mayo 2024</strong></td>
                <td>Personal</td>
                <td><span class="prioridad prioridad--baja">Baja</span></td>
                <td><span class="indicador-estado indicador-estado--sin-asignar">Sin Asignar</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="paginacion">
          <span class="paginacion__texto">Mostrando 1-10 de 48 reemplazos</span>
          <div class="paginacion__botones">
            <button class="paginacion__btn">&lt;</button>
            <button class="paginacion__btn">&gt;</button>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== ASISTENCIA INSTITUCIONAL ===== -->
    <section class="seccion" id="seccion-asistencia">
      <div style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Asistencia Institucional</div>

      <div class="cuadricula-5col">
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">PRESENTES <span class="etiqueta-cambio etiqueta-cambio--verde">+2.4%</span></div>
          <div class="tarjeta-estadistica__valor">842</div>
          <div class="tarjeta-estadistica__descripcion">Alumnos hoy</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-rojo">
          <div class="tarjeta-estadistica__etiqueta">AUSENTES <span class="etiqueta-cambio etiqueta-cambio--rojo">-0.8%</span></div>
          <div class="tarjeta-estadistica__valor">56</div>
          <div class="tarjeta-estadistica__descripcion">Sin justificar</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-gris">
          <div class="tarjeta-estadistica__etiqueta">TARDE</div>
          <div class="tarjeta-estadistica__valor">18</div>
          <div class="tarjeta-estadistica__descripcion">Ingresos fuera de hora</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-gris">
          <div class="tarjeta-estadistica__etiqueta">JUSTIFICADOS</div>
          <div class="tarjeta-estadistica__valor">24</div>
          <div class="tarjeta-estadistica__descripcion">Con certificacion</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--fondo-oscuro">
          <div class="tarjeta-estadistica__etiqueta">ASISTENCIA GRAL.</div>
          <div class="tarjeta-estadistica__valor">91.4%</div>
          <div class="barra-progreso"><div class="barra-progreso__relleno" style="width:91.4%;"></div></div>
        </div>
      </div>

      <div class="tarjeta-contenedor">
        <div class="barra-filtros">
          <select class="filtro-select">
            <option>Todos los Cursos</option>
          </select>
          <select class="filtro-select">
            <option>Todas las Divisiones</option>
          </select>
          <input type="date" class="filtro-select" placeholder="mm/dd/yyyy" style="padding-right: 12px; appearance: auto;">
          <button class="boton-secundario" style="border-radius: 20px;">
            <svg viewBox="0 0 24 24"><path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39C20.25 4.95 19.78 4 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/></svg>
            Mas Filtros
          </button>
          <div class="barra-filtros__acciones-derecha">
            <button class="boton-secundario">
              <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z"/></svg>
              EXPORTAR
            </button>
            <button class="boton-primario">
              <svg viewBox="0 0 24 24"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
              ACTUALIZAR
            </button>
          </div>
        </div>

        <div class="tabla-contenedor">
          <table class="tabla">
            <thead>
              <tr>
                <th>CURSO / DIV</th>
                <th>FECHA &amp; HORA</th>
                <th>PRECEPTOR</th>
                <th>P / A / T / J</th>
                <th>ESTADO</th>
                <th>ACCIONES</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div><strong>4 1ra</strong></div>
                  <div style="font-size:12px; color:var(--gris-texto);">Ciclo Superior</div>
                </td>
                <td>
                  <div>24 Oct, 2023</div>
                  <div style="font-size:12px; color:var(--gris-texto);">07:45 AM</div>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <div class="avatar-iniciales avatar-iniciales--rg">RG</div>
                    Ricardo Gomez
                  </div>
                </td>
                <td>
                  <div class="pataj">
                    <span>28</span><span> / </span><span>4</span><span> / </span><span>1</span><span style="color:var(--gris-texto);"> / </span><span>0</span>
                  </div>
                </td>
                <td><span class="indicador-estado indicador-estado--completo">Completo</span></td>
                <td>
                  <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                  <button class="btn-accion btn-accion--historial"><svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg></button>
                </td>
              </tr>
              <tr>
                <td>
                  <div><strong>5 3ra</strong></div>
                  <div style="font-size:12px; color:var(--gris-texto);">Ciclo Superior</div>
                </td>
                <td>
                  <div>24 Oct, 2023</div>
                  <div style="font-size:12px; color:var(--gris-texto);">08:02 AM</div>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <div class="avatar-iniciales avatar-iniciales--ms">MS</div>
                    Marta Sanchez
                  </div>
                </td>
                <td>
                  <div class="pataj">
                    <span>22</span><span> / </span><span>8</span><span> / </span><span>2</span><span style="color:var(--gris-texto);"> / </span><span>3</span>
                  </div>
                </td>
                <td><span class="indicador-estado indicador-estado--en-proceso">En Proceso</span></td>
                <td>
                  <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                  <button class="btn-accion btn-accion--historial"><svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="paginacion">
          <span class="paginacion__texto">Mostrando 1 a 10 de 45 registros</span>
          <div class="paginacion__botones">
            <button class="paginacion__btn">&lt;</button>
            <button class="paginacion__btn paginacion__btn--activo">1</button>
            <button class="paginacion__btn">2</button>
            <button class="paginacion__btn">3</button>
            <button class="paginacion__btn">&gt;</button>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== NOTIFICACIONES ===== -->
    <section class="seccion" id="seccion-notificaciones">
      <div style="font-size: 22px; font-weight: 700; margin-bottom: 20px;">Notificaciones</div>

      <div class="tarjeta-contenedor">
        <div class="tabs-notificaciones">
          <button class="tab-notif tab-notif--activo" onclick="filtrarNotificaciones(this)">Todas <span class="tab-notif__conteo">24</span></button>
          <button class="tab-notif" onclick="filtrarNotificaciones(this)">Solicitudes <span class="tab-notif__conteo tab-notif__conteo--gris">4</span></button>
          <button class="tab-notif" onclick="filtrarNotificaciones(this)">Reemplazos <span class="tab-notif__conteo tab-notif__conteo--gris">12</span></button>
          <button class="tab-notif" onclick="filtrarNotificaciones(this)">Asistencia <span class="tab-notif__conteo tab-notif__conteo--gris">6</span></button>
          <button class="tab-notif" onclick="filtrarNotificaciones(this)">Sistema <span class="tab-notif__conteo tab-notif__conteo--gris">2</span></button>
          <div class="acciones-notificaciones">
            <button class="btn-notif-accion btn-notif-accion--azul">
              <svg viewBox="0 0 24 24"><path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/></svg>
              Marcar todas como leidas
            </button>
            <button class="btn-notif-accion btn-notif-accion--rojo">
              <svg viewBox="0 0 24 24"><path d="M15 16h4v2h-4zm0-8h7v2h-7zm0 4h6v2h-6zM3 18c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V8H3v10zM14 5h-3l-1-1H6L5 5H2v2h12z"/></svg>
              Limpiar historial
            </button>
          </div>
        </div>

        <div class="lista-notificaciones">
          <!-- Notif 1 - No leida, solicitud -->
          <div class="item-notificacion">
            <div class="item-notificacion__punto-lectura item-notificacion__punto-lectura--no-leida"></div>
            <div class="item-notificacion__icono-contenedor item-notificacion__icono-contenedor--azul-oscuro">
              <svg viewBox="0 0 24 24" fill="white"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <div class="item-notificacion__cuerpo">
              <div class="item-notificacion__tipo item-notificacion__tipo--solicitud">Solicitud de Acceso</div>
              <div class="item-notificacion__titulo">Nueva solicitud: Marcos Perez</div>
              <div class="item-notificacion__descripcion">El preceptor Marcos Perez solicita acceso al sistema de cargas de asistencia para el turno tarde.</div>
              <div class="item-notificacion__botones">
                <button class="btn-notif-ver">Ver detalle</button>
                <button class="btn-notif-secundario">Marcar como leida</button>
              </div>
            </div>
            <div class="item-notificacion__tiempo">Hace 15 min</div>
          </div>

          <!-- Notif 2 - Leida, reemplazo -->
          <div class="item-notificacion" style="background-color: #fafafa;">
            <div class="item-notificacion__punto-lectura item-notificacion__punto-lectura--leida"></div>
            <div class="item-notificacion__icono-contenedor item-notificacion__icono-contenedor--gris">
              <svg viewBox="0 0 24 24" fill="var(--gris-texto)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div class="item-notificacion__cuerpo">
              <div class="item-notificacion__tipo item-notificacion__tipo--reemplazo">Reemplazo de Preceptor</div>
              <div class="item-notificacion__titulo">Asignacion completada: Reemplazo Aula 4</div>
              <div class="item-notificacion__descripcion">La docente Laura Gomez ha sido asignada para cubrir el turno del Preceptor Rodriguez.</div>
              <div class="item-notificacion__botones">
                <button class="btn-notif-secundario">Ver detalle</button>
              </div>
            </div>
            <div class="item-notificacion__tiempo">2h ago</div>
          </div>

          <!-- Notif 3 - No leida, sistema -->
          <div class="item-notificacion">
            <div class="item-notificacion__punto-lectura item-notificacion__punto-lectura--roja"></div>
            <div class="item-notificacion__icono-contenedor item-notificacion__icono-contenedor--rojo-claro">
              <svg viewBox="0 0 24 24" fill="var(--rojo)"><path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/></svg>
            </div>
            <div class="item-notificacion__cuerpo">
              <div class="item-notificacion__tipo item-notificacion__tipo--sistema">Sistema</div>
              <div class="item-notificacion__titulo">Mantenimiento Programado</div>
              <div class="item-notificacion__descripcion">El sistema estara fuera de servicio por mantenimiento el dia Sabado 24 de 02:00 a 04:00 AM.</div>
              <div class="item-notificacion__botones">
                <button class="btn-notif-ver">Ver detalle</button>
                <button class="btn-notif-secundario">Entendido</button>
              </div>
            </div>
            <div class="item-notificacion__tiempo">Hoy, 08:30 AM</div>
          </div>

          <!-- Notif 4 - Leida, asistencia -->
          <div class="item-notificacion" style="background-color: #fafafa;">
            <div class="item-notificacion__punto-lectura item-notificacion__punto-lectura--leida"></div>
            <div class="item-notificacion__icono-contenedor item-notificacion__icono-contenedor--gris">
              <svg viewBox="0 0 24 24" fill="var(--gris-texto)"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
            </div>
            <div class="item-notificacion__cuerpo">
              <div class="item-notificacion__tipo item-notificacion__tipo--asistencia">Asistencia Institucional</div>
              <div class="item-notificacion__titulo">Reporte semanal consolidado disponible</div>
              <div class="item-notificacion__descripcion">Se ha generado el reporte de asistencia correspondiente a la semana del 12 al 18 de este mes.</div>
              <div class="item-notificacion__botones">
                <button class="btn-notif-secundario">Ver detalle</button>
              </div>
            </div>
            <div class="item-notificacion__tiempo">Ayer, 04:45 PM</div>
          </div>
        </div>

        <button class="btn-cargar-mas">Cargar notificaciones anteriores</button>
      </div>
    </section>

    <!-- ===== PERFIL ===== -->
    <section class="seccion" id="seccion-perfil">
      <div class="encabezado-perfil">
        <div class="perfil-foto">
          <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
          <button class="perfil-foto__editar">
            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
          </button>
        </div>
        <div>
          <div class="perfil-nombre">Dr. Alejandro Rodriguez</div>
          <div class="perfil-cargo">
            <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4c1.4 0 2.8 1.1 2.8 2.5V9c.6 0 1.2.6 1.2 1.2v3.5c0 .7-.6 1.3-1.2 1.3H9.2c-.7 0-1.2-.6-1.2-1.2v-3.5C8 9.6 8.6 9 9.2 9V7.5C9.2 6.1 10.6 5 12 5z"/></svg>
            Cargo: Directivo Institucional
          </div>
        </div>
      </div>

      <div class="cuadricula-perfil">
        <div class="tarjeta-perfil-info">
          <div class="tarjeta-perfil-info__encabezado">
            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            <div class="tarjeta-perfil-info__titulo">Informacion Personal</div>
          </div>
          <div class="tarjeta-perfil-info__cuerpo">
            <div class="cuadricula-datos-perfil">
              <div>
                <div class="dato-perfil__etiqueta">Nombre Completo</div>
                <div class="dato-perfil__valor"><?php echo e($nombre_completo); ?></div>
              </div>
              <div>
                <div class="dato-perfil__etiqueta">Documento (DNI)</div>
                <div class="dato-perfil__valor"><?php echo e($dni !== "" ? $dni : "No informado"); ?></div>
              </div>
              <div>
                <div class="dato-perfil__etiqueta">Correo Electronico</div>
                <div class="dato-perfil__valor">a.rodriguez@eest1ader.edu.ar</div>
              </div>
              <div>
                <div class="dato-perfil__etiqueta">Telefono de Contacto</div>
                <div class="dato-perfil__valor">+54 11 4765-8822</div>
              </div>
            </div>
            <div class="botones-perfil">
              <button class="btn-editar-perfil">
                <svg viewBox="0 0 24 24"><path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/></svg>
                Editar Perfil
              </button>
              <form method="POST" action="index.php" class="js-logout-form" style="display:inline;">
              <input type="hidden" name="action" value="logout">
              <button type="submit" class="btn-cerrar-sesion-rojo">
                <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                Cerrar sesión
              </button>
            </form>
            </div>
          </div>
        </div>

        <div>
          <div class="tarjeta-seguridad">
            <div class="tarjeta-seguridad__titulo">
              <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
              Seguridad
            </div>
            <div class="item-seguridad">
              <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
              <div>
                <div class="item-seguridad__etiqueta">Ultimo acceso</div>
                <div class="item-seguridad__valor">Hoy, 08:42 AM</div>
              </div>
            </div>
            <div class="item-seguridad">
              <svg viewBox="0 0 24 24"><path d="M9.5 4C8.7 4 8 4.7 8 5.5V6H4v2h1v11h14V8h1V6h-4v-.5C16 4.7 15.3 4 14.5 4h-5zM10 6h4v.5c0-.28-.22-.5-.5-.5H10.5c-.28 0-.5.22-.5.5V6zM6 8h12v9H6V8zm2 3v2h3v-2H8zm5 0v2h3v-2h-3z"/></svg>
              <div>
                <div class="item-seguridad__etiqueta">IP de Sesion</div>
                <div class="item-seguridad__valor">192.168.1.104</div>
              </div>
            </div>
          </div>

          <div class="tarjeta-soporte">
            <div class="tarjeta-soporte__texto">Necesitas ayuda con los permisos de gestion?</div>
            <button class="btn-soporte">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
              Soporte Tecnico
            </button>
          </div>
        </div>
      </div>
    </section>

  </main>
</div>



</div>

<script src="../public/assets/js/directivo.js?v=1"></script>
