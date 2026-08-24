<?php
require_role('directivo');
require_once __DIR__ . '/../../controllers/DirectivoController.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

$directivoId = (int) $_SESSION['usuario_id'];
$portal = DirectivoController::portalData($directivoId);
$usuario = $portal['usuario'];

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';
$nombre_completo = trim($nombre . ' ' . $apellido);
$email = $usuario['email'] ?? ($_SESSION['email'] ?? '');
$dni = $usuario['dni'] ?? '';

$fecha_larga = $portal['fechaHoyLarga'];

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<link rel="stylesheet" href="../public/assets/css/directivo.css">
<link rel="shortcut icon" href="../public/assets/img/logo.webp" type="image/x-icon">
<div id="directivo-portal-root">

<!-- Notificaciones propias (reemplaza alert()/confirm() del navegador) -->
<div id="toast-container" class="toast-container"></div>

<!-- Overlay para mobile -->
<div class="overlay-sidebar" id="overlay-sidebar" onclick="cerrarSidebar()"></div>

<!-- Modal: motivo de rechazo de una solicitud -->
<div class="modal-overlay-directivo" id="modal-rechazo-overlay" style="display:none;">
  <div class="modal-directivo">
    <div class="modal-directivo__titulo">Rechazar solicitud</div>
    <div class="modal-directivo__campo">
      <label for="rechazo-motivo">Motivo del rechazo</label>
      <textarea id="rechazo-motivo" rows="3" placeholder="Explicá brevemente por qué se rechaza..."></textarea>
    </div>
    <div class="modal-directivo__acciones">
      <button class="boton-secundario" onclick="cerrarModalRechazo()">Cancelar</button>
      <button class="boton-primario" style="background:var(--rojo)" onclick="confirmarRechazoSolicitud()">Rechazar</button>
    </div>
  </div>
</div>

<!-- Modal: asignar preceptor a un reemplazo -->
<div class="modal-overlay-directivo" id="modal-asignar-overlay" style="display:none;">
  <div class="modal-directivo">
    <div class="modal-directivo__titulo">Asignar reemplazo</div>
    <div class="modal-directivo__campo">
      <label for="asignar-preceptor-select">Preceptor disponible</label>
      <select id="asignar-preceptor-select"><option value="">Cargando...</option></select>
    </div>
    <div class="modal-directivo__acciones">
      <button class="boton-secundario" onclick="cerrarModalAsignar()">Cancelar</button>
      <button class="boton-primario" id="btn-confirmar-asignar" onclick="confirmarAsignarReemplazo()">Asignar</button>
    </div>
  </div>
</div>

<!-- Modal: editar perfil -->
<div class="modal-overlay-directivo" id="modal-perfil-overlay" style="display:none;">
  <div class="modal-directivo">
    <div class="modal-directivo__titulo">Editar perfil</div>
    <div class="modal-directivo__campo">
      <label for="perfil-nombre">Nombre</label>
      <input type="text" id="perfil-nombre">
    </div>
    <div class="modal-directivo__campo">
      <label for="perfil-apellido">Apellido</label>
      <input type="text" id="perfil-apellido">
    </div>
    <div class="modal-directivo__campo">
      <label for="perfil-email">Correo electrónico</label>
      <input type="email" id="perfil-email">
    </div>
    <div class="modal-directivo__campo">
      <label for="perfil-telefono">Teléfono de contacto</label>
      <input type="text" id="perfil-telefono">
    </div>
    <div class="modal-directivo__acciones">
      <button class="boton-secundario" onclick="cerrarModalPerfil()">Cancelar</button>
      <button class="boton-primario" id="btn-guardar-perfil" onclick="guardarPerfilDirectivo()">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal: nuevo reemplazo -->
<div class="modal-overlay-directivo" id="modal-nuevo-reemplazo-overlay" style="display:none;">
  <div class="modal-directivo">
    <div class="modal-directivo__titulo">Nuevo reemplazo</div>
    <div class="modal-directivo__campo">
      <label for="nr-fecha">Fecha</label>
      <input type="date" id="nr-fecha" value="<?= e(date('Y-m-d')) ?>" onchange="onCambioFechaOCursoNuevoReemplazo()">
    </div>
    <div class="modal-directivo__campo">
      <label for="nr-curso">Curso</label>
      <select id="nr-curso" onchange="onCambioFechaOCursoNuevoReemplazo()">
        <option value="">Elegí un curso...</option>
        <?php foreach ($portal['cursosTodos'] as $c): ?>
          <option value="<?= (int) $c['id'] ?>"><?= e($c['anio']) ?>° <?= e($c['division']) ?>°</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="modal-directivo__campo">
      <label for="nr-materia">Materia / horario agendado ese día</label>
      <select id="nr-materia"><option value="">Elegí primero fecha y curso...</option></select>
    </div>
    <div class="modal-directivo__campo">
      <label for="nr-preceptor">Preceptor titular (ausente)</label>
      <select id="nr-preceptor"><option value="">Elegí primero fecha y curso...</option></select>
    </div>
    <div class="modal-directivo__campo">
      <label for="nr-prioridad">Prioridad</label>
      <select id="nr-prioridad">
        <option value="normal">Normal</option>
        <option value="alta">Alta</option>
        <option value="urgente">Urgente</option>
      </select>
    </div>
    <div class="modal-directivo__campo">
      <label for="nr-motivo">Motivo</label>
      <textarea id="nr-motivo" rows="2" placeholder="Ej: licencia médica, capacitación..."></textarea>
    </div>
    <div class="modal-directivo__acciones">
      <button class="boton-secundario" onclick="cerrarModalNuevoReemplazo()">Cancelar</button>
      <button class="boton-primario" id="btn-crear-reemplazo" onclick="confirmarNuevoReemplazo()">Crear</button>
    </div>
  </div>
</div>

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
          <div class="tarjeta-estadistica__etiqueta">ASISTENCIA</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['statsGenerales']['asistencia_general']) ?>%</div>
          <div class="barra-progreso"><div class="barra-progreso__relleno" style="width:<?= e($portal['statsGenerales']['asistencia_general']) ?>%; background-color: var(--azul-primario);"></div></div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">PRESENTES</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['presentes']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Registros históricos</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">AUSENTES</div>
          <div class="tarjeta-estadistica__valor tarjeta-estadistica__valor--rojo"><?= e($portal['ausentes']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Total inasistencias</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">LLEGADAS TARDE</div>
          <div class="tarjeta-estadistica__valor tarjeta-estadistica__valor--naranja"><?= e($portal['llegadasTarde']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Registradas</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">SOLICITUDES</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['solicitudesPendientes']) ?></div>
          <div class="tarjeta-estadistica__descripcion" style="color: var(--naranja); font-weight: 600;">Pendientes</div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">REEMPLAZOS</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['reemplazosSinAsignar']) ?></div>
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
                <div class="acceso-rapido" style="cursor:pointer" onclick="mostrarSeccion('reemplazos', document.querySelectorAll('.nav__item')[2]); abrirModalNuevoReemplazo();">
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
              <span><?= $portal['ultimaCarga'] ? e(date('d/m H:i', strtotime($portal['ultimaCarga']))) : '—' ?></span>
            </div>
            <div class="tarjeta-sistema__fila">
              <span>Preceptores Activos</span>
              <span><?= e($portal['preceptoresActivos']) ?> / <?= e($portal['preceptoresTotal']) ?></span>
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
          <?php if (empty($portal['avisos'])): ?>
            <div style="padding:16px 0;color:var(--gris-texto);font-size:13px">Sin avisos recientes.</div>
          <?php else: foreach ($portal['avisos'] as $aviso):
              $esUrgente = in_array($aviso['prioridad'], ['urgente', 'alta'], true);
          ?>
            <div class="aviso">
              <div class="aviso__linea <?= $esUrgente ? 'aviso__linea--roja' : 'aviso__linea--azul' ?>"></div>
              <?php if ($esUrgente): ?>
                <svg class="aviso__icono" viewBox="0 0 24 24" fill="#DC3545"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
              <?php else: ?>
                <svg class="aviso__icono" viewBox="0 0 24 24" fill="#3498DB"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              <?php endif; ?>
              <div class="aviso__contenido">
                <div class="aviso__titulo"><?= e($aviso['titulo']) ?></div>
                <div class="aviso__descripcion"><?= e($aviso['contenido']) ?></div>
                <div class="aviso__tiempo"><?= e(format_date_short_argentina($aviso['created_at'])) ?></div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </section>

    <!-- ===== SOLICITUDES DE ACCESO ===== -->
    <section class="seccion" id="seccion-solicitudes">
      <div style="font-size: 13px; color: var(--gris-texto); margin-bottom: 16px;">Solicitudes de Acceso</div>

      <div class="cuadricula-solicitudes">
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">PENDIENTES</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['solicitudesPendientes']) ?></div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">TOTAL</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['solicitudesTotal']) ?></div>
        </div>
      </div>

      <div class="tarjeta-contenedor">
        <div style="padding: 0 0 0 0;">
          <div style="display:flex; align-items:center; justify-content:space-between; padding: 14px 20px; border-bottom: 1px solid #f0f0f0;">
            <div class="tabs-filtro" style="padding:0; margin:0; gap:8px; display:flex; flex-wrap:wrap;">
              <button class="tab-btn tab-btn--activo">Todos <span class="tab-btn__conteo"><?= e($portal['solicitudesTotal']) ?></span></button>
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
                <?php if (empty($portal['solicitudes'])): ?>
                  <tr><td colspan="7" style="text-align:center;color:var(--gris-texto)">No hay solicitudes registradas.</td></tr>
                <?php else: foreach ($portal['solicitudes'] as $s):
                  $fechaSol = new DateTimeImmutable($s['created_at']);
                  $estadoClase = ['pendiente' => 'pendiente', 'aprobado' => 'completo', 'rechazado' => 'sin-asignar'][$s['estado']] ?? 'pendiente';
                  $estadoLabel = ucfirst($s['estado']);
                ?>
                  <tr id="solicitud-row-<?= (int) $s['id'] ?>">
                    <td><strong><?= e(trim($s['nombre'] . ' ' . $s['apellido'])) ?></strong></td>
                    <td><?= e($s['dni'] ?? '—') ?></td>
                    <td><div><?= e($s['email']) ?></div><div style="font-size:12px; color:var(--gris-texto);"><?= e($s['telefono'] ?? '—') ?></div></td>
                    <td><?php if ($s['tipo'] === 'alumno'): ?><span class="insignia insignia--azul">Estudiante</span><?php else: ?><span class="insignia insignia--gris">Padre/Tutor</span><?php endif; ?></td>
                    <td><div><?= e($fechaSol->format('d')) ?></div><div><?= e(format_date_short_argentina($s['created_at'])) ?></div></td>
                    <td id="solicitud-estado-<?= (int) $s['id'] ?>"><span class="indicador-estado indicador-estado--<?= e($estadoClase) ?>"><?= e($estadoLabel) ?></span></td>
                    <td id="solicitud-acciones-<?= (int) $s['id'] ?>">
                      <?php if ($s['estado'] === 'pendiente'): ?>
                        <button class="btn-accion btn-accion--aprobar" title="Aprobar" onclick="aprobarSolicitud(<?= (int) $s['id'] ?>)"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></button>
                        <button class="btn-accion btn-accion--rechazar" title="Rechazar" onclick="abrirModalRechazo(<?= (int) $s['id'] ?>)"><svg viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></button>
                      <?php else: ?>
                        <span style="font-size:12px;color:var(--gris-texto)">
                          <?= $s['estado'] === 'aprobado' ? 'Aprobada' : 'Rechazada' ?>
                          <?php if (!empty($s['fecha_revision'])): ?>
                            — <?= e(format_date_short_argentina($s['fecha_revision'])) ?>
                          <?php endif; ?>
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>

          <div class="paginacion">
            <span class="paginacion__texto">Mostrando <?= e(count($portal['solicitudes'])) ?> de <?= e($portal['solicitudesTotal']) ?> solicitudes</span>
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
        <button class="boton-primario" onclick="abrirModalNuevoReemplazo()">
          <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
          Nuevo Reemplazo
        </button>
      </div>

      <div class="cuadricula-estadisticas cuadricula-estadisticas--4col">
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">Total Sin Asignar</div>
          <div style="display:flex; align-items:baseline; gap:10px;">
            <div class="tarjeta-estadistica__valor"><?= e($portal['reemplazosSinAsignar']) ?></div>
          </div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">Asignados hoy</div>
          <div style="display:flex; align-items:baseline; gap:10px;">
            <div class="tarjeta-estadistica__valor"><?= e(str_pad((string) $portal['reemplazosAsignadosHoy'], 2, '0', STR_PAD_LEFT)) ?></div>
          </div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-gris">
          <div class="tarjeta-estadistica__etiqueta">Efectividad Cobertura</div>
          <div style="display:flex; align-items:baseline; gap:10px;">
            <div class="tarjeta-estadistica__valor"><?= e($portal['efectividadCobertura']) ?>%</div>
          </div>
        </div>
        <div class="tarjeta-estadistica">
          <div class="tarjeta-estadistica__etiqueta">Preceptores Disponibles</div>
          <div style="display:flex; align-items:center; justify-content:space-between;">
            <div class="tarjeta-estadistica__valor"><?= e(str_pad((string) $portal['preceptoresDisponibles'], 2, '0', STR_PAD_LEFT)) ?></div>
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
          <div class="barra-filtros__acciones-derecha">
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
                <th>TURNO</th>
                <th>FECHA</th>
                <th>MOTIVO</th>
                <th>PRIORIDAD</th>
                <th>ESTADO</th>
                <th>ACCIONES</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($portal['reemplazos'])): ?>
                <tr><td colspan="8" style="text-align:center;color:var(--gris-texto)">No hay reemplazos registrados.</td></tr>
              <?php else: foreach ($portal['reemplazos'] as $r):
                  $iniciales = mb_strtoupper(mb_substr($r['titular_nombre'], 0, 1) . mb_substr($r['titular_apellido'], 0, 1));
                  $prioridadClase = ['normal' => 'baja', 'alta' => 'normal', 'urgente' => 'critica'][$r['prioridad']] ?? 'normal';
                  $estadoClase = ['sin_asignar' => 'sin-asignar', 'asignado' => 'asignado', 'realizado' => 'completo', 'cancelado' => 'sin-asignar'][$r['estado']] ?? 'sin-asignar';
                  $estadoLabel = ['sin_asignar' => 'Sin Asignar', 'asignado' => 'Asignado', 'realizado' => 'Realizado', 'cancelado' => 'Cancelado'][$r['estado']] ?? ucfirst($r['estado']);
              ?>
                <tr id="reemplazo-row-<?= (int) $r['id'] ?>">
                  <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                      <div class="avatar-iniciales"><?= e($iniciales) ?></div>
                      <span><?= e($r['titular_apellido'] . ', ' . $r['titular_nombre']) ?></span>
                    </div>
                  </td>
                  <td><?= e($r['anio']) ?>° <?= e($r['division']) ?>° <?= $r['especialidad_nombre'] ? '- ' . e($r['especialidad_nombre']) : '' ?></td>
                  <td>
                    <?= e(ucfirst($r['turno'])) ?>
                    <div style="font-size:11px; color:var(--gris-texto);"><?= e($r['materia_nombre'] ?: 'Sin materia puntual') ?></div>
                  </td>
                  <td>
                    <strong><?= e(format_date_short_argentina($r['fecha'])) ?></strong>
                    <?php if (in_array($r['estado'], ['sin_asignar', 'asignado'], true)): ?>
                      <div style="font-size:11px; color:<?= $r['urgenciaMinutos'] < 0 ? 'var(--rojo)' : 'var(--gris-texto)' ?>; font-weight:<?= $r['urgenciaMinutos'] < 0 ? '700' : '400' ?>">
                        <?= e($r['horaClase']) ?> hs — <?= e($r['urgenciaTexto']) ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><?= e($r['motivo'] ?: '—') ?></td>
                  <td><span class="prioridad prioridad--<?= e($prioridadClase) ?>"><?= e(ucfirst($r['prioridad'])) ?></span></td>
                  <td id="reemplazo-estado-<?= (int) $r['id'] ?>"><span class="indicador-estado indicador-estado--<?= e($estadoClase) ?>"><?= e($estadoLabel) ?></span></td>
                  <td id="reemplazo-acciones-<?= (int) $r['id'] ?>">
                    <?php if ($r['estado'] === 'sin_asignar'): ?>
                      <button class="boton-secundario" style="padding:6px 12px;font-size:12px"
                        onclick="abrirModalAsignar(<?= (int) $r['id'] ?>)">
                        Asignar
                      </button>
                      <button class="btn-accion btn-accion--rechazar" title="Cancelar reemplazo" onclick="cancelarReemplazo(<?= (int) $r['id'] ?>)">
                        <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                      </button>
                    <?php elseif ($r['estado'] === 'asignado'): ?>
                      <div style="font-size:12px;color:var(--gris-texto); margin-bottom:6px;">
                        Cubre: <?= e($r['reemplazante_apellido'] . ', ' . $r['reemplazante_nombre']) ?>
                      </div>
                      <button class="boton-secundario" style="padding:6px 12px;font-size:12px;margin-right:6px;" title="Marcar como realizado" onclick="marcarRealizadoReemplazo(<?= (int) $r['id'] ?>)">
                        Realizado
                      </button>
                      <button class="btn-accion btn-accion--rechazar" title="Cancelar reemplazo" onclick="cancelarReemplazo(<?= (int) $r['id'] ?>)">
                        <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                      </button>
                    <?php else: ?>
                      <span style="font-size:12px;color:var(--gris-texto)">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>

        <div class="paginacion">
          <span class="paginacion__texto">Mostrando <?= e(count($portal['reemplazos'])) ?> de <?= e($portal['reemplazosTotal']) ?> reemplazos</span>
        </div>
      </div>
    </section>

    <!-- ===== ASISTENCIA INSTITUCIONAL ===== -->
    <section class="seccion" id="seccion-asistencia">
      <div style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Asistencia Institucional</div>

      <div class="cuadricula-5col">
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-azul">
          <div class="tarjeta-estadistica__etiqueta">PRESENTES</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['presentes']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Total histórico</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-rojo">
          <div class="tarjeta-estadistica__etiqueta">AUSENTES</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['ausentes']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Sin justificar</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-gris">
          <div class="tarjeta-estadistica__etiqueta">TARDE</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['llegadasTarde']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Ingresos fuera de hora</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--borde-gris">
          <div class="tarjeta-estadistica__etiqueta">JUSTIFICADOS</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['justificaciones']) ?></div>
          <div class="tarjeta-estadistica__descripcion">Con certificacion</div>
        </div>
        <div class="tarjeta-estadistica tarjeta-estadistica--fondo-oscuro">
          <div class="tarjeta-estadistica__etiqueta">ASISTENCIA GRAL.</div>
          <div class="tarjeta-estadistica__valor"><?= e($portal['statsGenerales']['asistencia_general']) ?>%</div>
          <div class="barra-progreso"><div class="barra-progreso__relleno" style="width:<?= e($portal['statsGenerales']['asistencia_general']) ?>%;"></div></div>
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
              <?php if (empty($portal['registrosAsistencia'])): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--gris-texto)">No hay registros de asistencia.</td></tr>
              <?php else: foreach ($portal['registrosAsistencia'] as $item):
                  $reg = $item['reg']; $conteo = $item['conteo'];
                  $iniciales = mb_strtoupper(mb_substr($reg['preceptor_nombre'], 0, 1) . mb_substr($reg['preceptor_apellido'], 0, 1));
                  $estadoClaseMap = ['cerrada' => 'completo', 'modificada' => 'en-proceso', 'abierta' => 'sin-asignar', 'anulada' => 'sin-asignar'];
                  $estadoClase = $estadoClaseMap[$reg['estado_calculado']] ?? 'sin-asignar';
              ?>
                <tr>
                  <td>
                    <div><strong><?= e($reg['anio']) ?> <?= e($reg['division']) ?>°</strong></div>
                    <div style="font-size:12px; color:var(--gris-texto);"><?= e($reg['materia_nombre']) ?></div>
                  </td>
                  <td>
                    <div><?= e(format_date_short_argentina($reg['fecha'])) ?></div>
                    <div style="font-size:12px; color:var(--gris-texto);"><?= $reg['hora_inicio'] ? e(substr($reg['hora_inicio'], 0, 5)) : '' ?></div>
                  </td>
                  <td>
                    <div style="display:flex; align-items:center; gap:8px;">
                      <div class="avatar-iniciales"><?= e($iniciales) ?></div>
                      <?= e($reg['preceptor_nombre'] . ' ' . $reg['preceptor_apellido']) ?>
                    </div>
                  </td>
                  <td>
                    <div class="pataj">
                      <span><?= e($conteo['presente']) ?></span><span> / </span><span><?= e($conteo['ausente']) ?></span><span> / </span><span><?= e($conteo['llegada_tarde']) ?></span><span style="color:var(--gris-texto);"> / </span><span><?= e($conteo['justificado']) ?></span>
                    </div>
                  </td>
                  <td><span class="indicador-estado indicador-estado--<?= e($estadoClase) ?>"><?= e(ucfirst($reg['estado_calculado'])) ?></span></td>
                  <td>
                    <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>

        <div class="paginacion">
          <span class="paginacion__texto">Mostrando <?= e(count($portal['registrosAsistencia'])) ?> registros recientes</span>
        </div>
      </div>
    </section>

    <!-- ===== NOTIFICACIONES ===== -->
    <section class="seccion" id="seccion-notificaciones">
      <div style="font-size: 22px; font-weight: 700; margin-bottom: 20px;">Notificaciones</div>

      <div class="tarjeta-contenedor">
        <div class="tabs-notificaciones">
          <button class="tab-notif tab-notif--activo">Todas <span class="tab-notif__conteo"><?= e(count($portal['notificaciones'])) ?></span></button>
        </div>

        <div class="lista-notificaciones">
          <?php if (empty($portal['notificaciones'])): ?>
            <div style="padding:20px;color:var(--gris-texto);text-align:center;">Sin notificaciones.</div>
          <?php else: foreach ($portal['notificaciones'] as $n):
              $leida = (bool) $n['leida'];
              $tipoInfo = [
                'alerta' => ['clase' => 'sistema', 'label' => 'Alerta', 'icono' => 'rojo'],
                'aviso' => ['clase' => 'asistencia', 'label' => 'Aviso', 'icono' => 'gris'],
                'recordatorio' => ['clase' => 'reemplazo', 'label' => 'Recordatorio', 'icono' => 'gris'],
              ][$n['tipo']] ?? ['clase' => 'sistema', 'label' => ucfirst($n['tipo']), 'icono' => 'gris'];
          ?>
            <div class="item-notificacion" <?= $leida ? 'style="background-color: #fafafa;"' : 'style="cursor:pointer"' ?> <?= $leida ? '' : 'onclick="marcarNotificacionLeida(' . (int) $n['id'] . ', this)"' ?>>
              <div class="item-notificacion__punto-lectura <?= $leida ? 'item-notificacion__punto-lectura--leida' : 'item-notificacion__punto-lectura--no-leida' ?>"></div>
              <div class="item-notificacion__icono-contenedor item-notificacion__icono-contenedor--<?= $tipoInfo['icono'] === 'rojo' ? 'rojo-claro' : 'gris' ?>">
                <svg viewBox="0 0 24 24" fill="<?= $tipoInfo['icono'] === 'rojo' ? 'var(--rojo)' : 'var(--gris-texto)' ?>"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
              </div>
              <div class="item-notificacion__cuerpo">
                <div class="item-notificacion__tipo item-notificacion__tipo--<?= e($tipoInfo['clase']) ?>"><?= e($tipoInfo['label']) ?></div>
                <div class="item-notificacion__titulo"><?= e($n['titulo']) ?></div>
                <div class="item-notificacion__descripcion"><?= e($n['contenido']) ?></div>
              </div>
              <div class="item-notificacion__tiempo"><?= e(format_date_short_argentina($n['created_at'])) ?></div>
            </div>
          <?php endforeach; endif; ?>
        </div>
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
          <div class="perfil-nombre"><?= e($nombre_completo) ?></div>
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
                <div class="dato-perfil__valor"><?= e($email ?: '—') ?></div>
              </div>
              <div>
                <div class="dato-perfil__etiqueta">Telefono de Contacto</div>
                <div class="dato-perfil__valor"><?= e($usuario['telefono'] ?? '—') ?></div>
              </div>
            </div>
            <div class="botones-perfil">
              <button class="btn-editar-perfil" onclick="abrirModalPerfil()">
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
                <div class="item-seguridad__valor"><?= !empty($usuario['ultimo_acceso']) ? e(format_date_short_argentina($usuario['ultimo_acceso']) . ', ' . date('H:i', strtotime($usuario['ultimo_acceso']))) : '—' ?></div>
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

<script>
  // Datos reales del gráfico de presentismo (Reporte::getPresentismoPorDivision),
  // reemplazan los arrays hardcodeados que tenía directivo.js.
  window.SERVER_DATA = <?php
    $etiquetas = array_map(fn($d) => $d['division'], $portal['graficoManana']);
    $presentesManana = array_map(fn($d) => (int) $d['presentes'], $portal['graficoManana']);
    $ausentesManana = array_map(fn($d) => (int) $d['ausentes'], $portal['graficoManana']);
    $porTarde = [];
    foreach ($portal['graficoTarde'] as $d) $porTarde[$d['division']] = $d;
    $presentesTarde = array_map(fn($div) => (int) ($porTarde[$div]['presentes'] ?? 0), $etiquetas);
    $ausentesTarde = array_map(fn($div) => (int) ($porTarde[$div]['ausentes'] ?? 0), $etiquetas);
    echo json_encode([
      'etiquetas' => $etiquetas,
      'presentesManana' => $presentesManana,
      'ausentesManana' => $ausentesManana,
      'presentesTarde' => $presentesTarde,
      'ausentesTarde' => $ausentesTarde,
      'csrfToken' => csrf_token(),
      'usuario' => [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'email' => $email,
        'telefono' => $usuario['telefono'] ?? '',
      ],
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  ?>;
</script>
<script src="../public/assets/js/directivo.js?v=3"></script>
