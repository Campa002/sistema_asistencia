<?php
require_role('alumno');
require_once __DIR__ . '/../../controllers/AlumnoController.php';

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';
$nombre_completo = trim($nombre . ' ' . $apellido);

$alumnoId = (int) $_SESSION['usuario_id'];
$portal = AlumnoController::portalData($alumnoId);
$usuario = $portal['usuario'];
$curso = $portal['curso'];
$cursoLabel = $curso ? ($curso['anio'] . '° ' . $curso['division'] . '°') : '';
$cursoLabelLarga = $curso ? ($curso['anio'] . '° Año - ' . ($curso['especialidad_nombre'] ?? 'General')) : 'Sin curso asignado';

$curso_sidebar = $cursoLabel;
?>
<link rel="stylesheet" href="../public/assets/css/alumnos.css">

<div id="alumno-portal-root">

 
  <!-- ENCABEZADO -->
  <header class="encabezado">
    <div class="encabezado-logo">
      <img class="nav-item activo b" id="nav-dashboard" onclick="mostrarPantalla('pantalla-dashboard')" src="../public/assets/img/logo.webp" alt="">
      EEST N°1
    </div>
    <div class="encabezado-acciones">
      <button
        type="button"
        class="encabezado-perfil"
        onclick="mostrarPantalla('pantalla-perfil')"
        aria-label="Abrir perfil"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="8" r="4"/>
          <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
        </svg>
      </button>

      <button
        type="button"
        class="alumno-menu-toggle"
        id="alumno-menu-toggle"
        onclick="toggleSidebarAlumno()"
        aria-label="Abrir menú"
        aria-controls="alumno-sidebar"
        aria-expanded="false"
      >
        <svg class="icono-menu" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
        <svg class="icono-cerrar" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="6" y1="6" x2="18" y2="18"/>
          <line x1="18" y1="6" x2="6" y2="18"/>
        </svg>
      </button>
    </div>
  </header>
 
  <div class="contenedor-principal">
 
    <!-- BARRA LATERAL -->
    <nav class="barra-lateral" id="alumno-sidebar" aria-label="Menú principal del alumno">
      <div class="alumno-sidebar-user">
        <div class="alumno-sidebar-avatar" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"></path>
          </svg>
        </div>
        <div class="alumno-sidebar-name">
          <?php echo e($apellido . ', ' . $nombre); ?>
        </div>
        <div class="alumno-sidebar-role">
          Alumno<?php echo $curso_sidebar !== '' ? ' — ' . e($curso_sidebar) : ''; ?>
        </div>
      </div>

      <button class="nav-item activo" id="nav-dashboard" onclick="mostrarPantalla('pantalla-dashboard')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span>Dashboard</span>
      </button>
      <button class="nav-item" id="nav-asistencia" onclick="mostrarPantalla('pantalla-asistencia')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/></svg>
        <span>Asistencia</span>
      </button>
      <button class="nav-item" id="nav-alumnos" onclick="mostrarPantalla('pantalla-alumnos')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
        <span>Alumnos</span>
      </button>
      <button class="nav-item" id="nav-perfil" onclick="mostrarPantalla('pantalla-perfil')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        <span>Perfil</span>
      </button>
    
      <div class="alumno-sidebar-footer">
        <form method="POST" action="index.php">
          <input type="hidden" name="action" value="logout">
          <button type="submit" class="alumno-logout-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Cerrar sesión
          </button>
        </form>
      </div>
</nav>

    <button
      type="button"
      class="alumno-sidebar-overlay"
      id="alumno-sidebar-overlay"
      onclick="cerrarSidebarAlumno()"
      aria-label="Cerrar menú"
      tabindex="-1"
    ></button>

    <!-- CONTENIDO -->
    <main class="contenido">
 
      <!-- PANTALLA: DASHBOARD  -->
      <div class="pantalla activa" id="pantalla-dashboard">
        <h1 class="saludo-titulo">Hola, <?php echo e($nombre); ?></h1>
        <p class="saludo-subtitulo"><?= e($cursoLabelLarga) ?>. Revisa tu progreso académico hoy.</p>

        <div class="fecha-badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <?= e($portal['fechaHoyLarga']) ?>
        </div>
 
        <div class="cuadricula-dashboard">
 
          <!-- Asistencia total -->
          <div class="tarjeta tarjeta-asistencia">
            <div class="tarjeta-titulo">Asistencia total</div>
            <?php
              $pctAsist = $portal['porcentajeAsistencia'] ?? 0;
              $circunferencia = 2 * M_PI * 60;
              $dashoffset = round($circunferencia * (1 - min(100, max(0, $pctAsist)) / 100), 1);
            ?>
            <div class="circulo-contenedor">
              <div class="circulo-wrap">
                <svg class="circulo-svg" width="150" height="150" viewBox="0 0 150 150">
                  <circle cx="75" cy="75" r="60" fill="none" stroke="#DEE2E6" stroke-width="12"/>
                  <circle cx="75" cy="75" r="60" fill="none" stroke="#3498DB" stroke-width="12"
                    stroke-dasharray="<?= e(round($circunferencia, 1)) ?>"
                    stroke-dashoffset="<?= e($dashoffset) ?>"
                    stroke-linecap="round"/>
                </svg>
                <div style="position:absolute; display:flex; flex-direction:column; align-items:center;">
                  <span class="circulo-porcentaje"><?= $portal['porcentajeAsistencia'] !== null ? e($portal['porcentajeAsistencia']) . '%' : '—' ?></span>
                  <span class="circulo-label">Presente</span>
                </div>
              </div>
            </div>
            <div class="stats-fila">
              <div class="stat-item">
                <span class="stat-label">Faltas</span>
                <span class="stat-valor rojo"><?= e($portal['ausentes']) ?></span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Tardes</span>
                <span class="stat-valor amarillo"><?= e($portal['llegadasTarde']) ?></span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Justificadas</span>
                <span class="stat-valor azul"><?= e($portal['justificados']) ?></span>
              </div>
            </div>
          </div>
 
          <!-- Avisos recientes -->
          <div class="tarjeta tarjeta-avisos">
            <div class="avisos-cabecera">
              <span class="tarjeta-titulo" style="margin-bottom:0">Avisos recientes</span>
              <button class="ver-todos" onclick="mostrarPantalla('pantalla-asistencia')">Ver todos</button>
            </div>
            <?php if (empty($portal['avisos'])): ?>
              <div style="padding:14px 0;color:#6C757D;font-size:13px;">Sin avisos recientes.</div>
            <?php else: foreach (array_slice($portal['avisos'], 0, 2) as $i => $aviso): ?>
              <div class="aviso-item" <?= $i === 1 ? 'style="border-left-color: #28A745;"' : '' ?>>
                <div class="aviso-icono" <?= $i === 1 ? 'style="background: rgba(40,167,69,0.1); color: var(--verde);"' : '' ?>>
                  <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                  <div class="aviso-titulo"><?= e($aviso['titulo']) ?></div>
                  <div class="aviso-descripcion"><?= e(mb_substr($aviso['contenido'], 0, 60)) ?><?= mb_strlen($aviso['contenido']) > 60 ? '...' : '' ?></div>
                  <div class="aviso-tiempo"><?= e(format_date_short_argentina($aviso['created_at'])) ?></div>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </div>

          <!-- Notificaciones -->
          <div class="tarjeta tarjeta-notificaciones">
            <div class="avisos-cabecera">
              <span class="tarjeta-titulo" style="margin-bottom:0">Notificaciones</span>
            </div>
            <?php if (empty($portal['notificaciones'])): ?>
              <div style="padding:14px 0;color:#6C757D;font-size:13px;">Sin notificaciones.</div>
            <?php else: foreach ($portal['notificaciones'] as $n):
                $color = ['alerta' => 'rojo', 'aviso' => 'verde', 'recordatorio' => 'amarillo'][$n['tipo']] ?? 'verde';
            ?>
              <div class="notificacion-item">
                <div class="notificacion-dot <?= e($color) ?>"></div>
                <div>
                  <div class="notificacion-texto"><?= e($n['titulo']) ?></div>
                  <div class="notificacion-tiempo"><?= e(format_date_short_argentina($n['created_at'])) ?></div>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </div>
 
          <!-- Panel calificaciones -->
          <div class="tarjeta tarjeta-calificaciones">
            <div class="calificaciones-cabecera">
              <span class="tarjeta-titulo" style="margin-bottom:0">Panel de calificaciones</span>
              <button class="boton-pdf">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Rite PDF
              </button>
            </div>
            <table class="tabla-calificaciones">
              <thead>
                <tr>
                  <th>Materia</th>
                  <th>Docente</th>
                  <th>Nota</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Programacion II</td>
                  <td>Ganduglia</td>
                  <td class="nota-valor">8</td>
                </tr>
                <tr>
                  <td>Base de Datos</td>
                  <td>Paula Balda</td>
                  <td class="nota-valor">7</td>
                </tr>
                <tr>
                  <td>Sistemas Digitales</td>
                  <td>Salimbeni</td>
                  <td class="nota-valor" style="color: var(--rojo);">4</td>
                </tr>
                <tr>
                  <td>Redes y Comunicaciones</td>
                  <td>Torres</td>
                  <td class="nota-valor">6</td>
                </tr>
                <tr>
                  <td>Laboratorio de Hardware</td>
                  <td>Pereyra</td>
                  <td class="nota-valor">9</td>
                </tr>
              </tbody>
            </table>
          </div>
 
        </div>
      </div>
 
 
      <!--  PANTALLA: HISTORIAL DE ASISTENCIA  -->
      <div class="pantalla" id="pantalla-asistencia">
        <h1 class="pagina-titulo">Historial de Asistencia</h1>
        <p class="pagina-subtitulo"><?= e($cursoLabelLarga) ?></p>

        <div class="cuadricula-stats">
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta">Asistencia Total</div>
            <div class="tarjeta-stat-valor" style="color: var(--verde);"><?= $portal['porcentajeAsistencia'] !== null ? e($portal['porcentajeAsistencia']) . '%' : '—' ?></div>
          </div>
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta">Inasistencias</div>
            <div class="tarjeta-stat-valor" style="color: var(--rojo);"><?= e($portal['faltasTotales']) ?></div>
          </div>
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta-icono">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 17l5-5-5-5M6 17l5-5-5-5"/></svg>
              Retiro anticipado
            </div>
            <div class="tarjeta-stat-valor"><?= e($portal['retiros']) ?></div>
          </div>
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta-icono" style="color: #E67E22;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E67E22" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
              Llegadas tarde
            </div>
            <div class="tarjeta-stat-valor"><?= e($portal['llegadasTarde']) ?></div>
          </div>
        </div>
 
        <div class="fila-historial">
          <!-- Calendario y registros -->
          <div>
            <div class="tarjeta" style="margin-bottom: 22px;">
              <div class="calendario-cabecera">
                <div class="calendario-mes" id="calendario-mes-titulo">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  Mayo 2026
                </div>
                <div class="calendario-nav">
                  <button type="button" onclick="cambiarMesAlumno(-1)" aria-label="Mes anterior"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg></button>
                  <button type="button" onclick="cambiarMesAlumno(1)" aria-label="Mes siguiente"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg></button>
                </div>
              </div>
              <table class="calendario-grilla">
                <thead>
                  <tr>
                    <th>LU</th><th>MA</th><th>MI</th><th>JU</th><th>VI</th><th>SA</th><th>DO</th>
                  </tr>
                </thead>
                <tbody id="calendario-alumno-body">
                  <tr>
                    <td><div class="dia-celda vacio">29</div></td>
                    <td><div class="dia-celda vacio">30</div></td>
                    <td><div class="dia-celda presente">1</div></td>
                    <td><div class="dia-celda presente">2</div></td>
                    <td><div class="dia-celda presente">3</div></td>
                    <td><div class="dia-celda vacio">4</div></td>
                    <td><div class="dia-celda vacio">5</div></td>
                  </tr>
                  <tr>
                    <td><div class="dia-celda presente">6</div></td>
                    <td><div class="dia-celda tarde">7</div></td>
                    <td><div class="dia-celda presente">8</div></td>
                    <td><div class="dia-celda presente">9</div></td>
                    <td><div class="dia-celda ausente">10</div></td>
                    <td><div class="dia-celda vacio">11</div></td>
                    <td><div class="dia-celda vacio">12</div></td>
                  </tr>
                  <tr>
                    <td><div class="dia-celda presente">13</div></td>
                    <td><div class="dia-celda ausente">14</div></td>
                    <td><div class="dia-celda ausente">15</div></td>
                    <td><div class="dia-celda tarde">16</div></td>
                    <td><div class="dia-celda presente">17</div></td>
                    <td><div class="dia-celda vacio">18</div></td>
                    <td><div class="dia-celda vacio">19</div></td>
                  </tr>
                  <tr>
                    <td><div class="dia-celda hoy">20</div></td>
                    <td><div class="dia-celda">21</div></td>
                    <td><div class="dia-celda">22</div></td>
                    <td><div class="dia-celda">23</div></td>
                    <td><div class="dia-celda">24</div></td>
                    <td><div class="dia-celda vacio">25</div></td>
                    <td><div class="dia-celda vacio">26</div></td>
                  </tr>
                </tbody>
              </table>
              <div class="calendario-leyenda">
                <div class="leyenda-item"><div class="leyenda-circulo verde"></div> Presente</div>
                <div class="leyenda-item"><div class="leyenda-circulo amarillo"></div> Tarde</div>
                <div class="leyenda-item"><div class="leyenda-circulo rojo"></div> Ausente</div>
              </div>
            </div>
 
            <div class="tarjeta">
              <div class="registros-cabecera">
                <span style="font-size:17px; font-weight:700;">Registros recientes</span>
                <button class="boton-pdf">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Exportar PDF
                </button>
              </div>
 
              <?php if (empty($portal['historialReciente'])): ?>
                <div style="padding:20px 0;color:#6C757D;text-align:center;font-size:14px;">Todavía no hay registros de asistencia.</div>
              <?php else: foreach ($portal['historialReciente'] as $h):
                  $fechaObj = new DateTimeImmutable($h['fecha']);
                  $diasEsp = ['Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'];
                  $labelEstado = ['presente' => 'Presente', 'tarde' => 'Tarde', 'ausente' => 'Ausente', 'justificado' => 'Justificado'][$h['estado']];
                  $mesesEsp = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
              ?>
                <div class="registro-fila">
                  <div>
                    <div class="registro-fecha-titulo"><?= e($fechaObj->format('j')) ?> <?= e($mesesEsp[(int) $fechaObj->format('n')]) ?></div>
                    <div class="registro-fecha-dia"><?= e($diasEsp[$fechaObj->format('l')] ?? '') ?></div>
                  </div>
                  <div style="text-align:center;">
                    <div class="registro-entrada-label">E/S</div>
                    <div class="registro-entrada"><?= $h['hora_llegada'] ? e(substr($h['hora_llegada'], 0, 5)) : '--:--' ?><br><?= $h['hora_retiro'] ? e(substr($h['hora_retiro'], 0, 5)) : '--:--' ?></div>
                  </div>
                  <div class="registro-estado">
                    <span class="badge-estado <?= e($h['estado']) ?>"><?= e($labelEstado) ?></span>
                    <span class="registro-falta"><?= e(number_format($h['falta'], 2)) ?> Falta</span>
                  </div>
                </div>
              <?php endforeach; endif; ?>
            </div>
          </div>
 
          <!-- Columna lateral: resumen mensual -->
          <div>
            <div class="tarjeta" style="margin-bottom: 16px;">
              <div class="tarjeta-titulo">Resumen mensual</div>
              <div style="display:flex; flex-direction:column; gap:10px;">
                <?php
                  $diasTotal = (int) $portal['diasTotal'];
                  $presentes = (int) $portal['presentes'];
                  $ausentes = (int) $portal['ausentes'];
                  $tarde = (int) $portal['llegadasTarde'];
                  $justificados = (int) $portal['justificados'];
                ?>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Dias con registro</span>
                  <span style="font-weight:700;"><?= e($diasTotal) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Presentes</span>
                  <span style="font-weight:700; color:var(--verde);"><?= e($presentes) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Ausentes</span>
                  <span style="font-weight:700; color:var(--rojo);"><?= e($ausentes) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Tarde</span>
                  <span style="font-weight:700; color:#E67E22;"><?= e($tarde) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0;">
                  <span style="font-size:14px; color:var(--gris);">Justificados</span>
                  <span style="font-weight:700; color:var(--azul-claro);"><?= e($justificados) ?></span>
                </div>
              </div>
            </div>
            <div class="tarjeta">
              <div class="tarjeta-titulo">Porcentaje acumulado</div>
              <div style="margin: 16px 0;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;">
                  <span>Asistencia</span>
                  <span style="font-weight:700; color:var(--verde);"><?= $portal['porcentajeAsistencia'] !== null ? e($portal['porcentajeAsistencia']) . '%' : '—' ?></span>
                </div>
                <div style="background:var(--fondo-claro); border-radius:8px; height:10px; overflow:hidden;">
                  <div style="background:var(--verde); width:<?= e($portal['porcentajeAsistencia'] ?? 0) ?>%; height:100%; border-radius:8px;"></div>
                </div>
              </div>
              <?php $pctFaltas = $portal['maximoFaltas'] > 0 ? min(100, round($portal['faltasTotales'] / $portal['maximoFaltas'] * 100, 1)) : 0; ?>
              <div style="margin: 16px 0;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;">
                  <span>Maximo permitido</span>
                  <span style="font-weight:700; color:var(--rojo);"><?= e($portal['maximoFaltas']) ?> faltas</span>
                </div>
                <div style="background:var(--fondo-claro); border-radius:8px; height:10px; overflow:hidden;">
                  <div style="background:var(--rojo); width:<?= e($pctFaltas) ?>%; height:100%; border-radius:8px;"></div>
                </div>
                <div style="font-size:12px; color:var(--gris); margin-top:6px;"><?= e($portal['faltasTotales']) ?> / <?= e($portal['maximoFaltas']) ?> faltas acumuladas</div>
              </div>
            </div>
          </div>
        </div>
      </div>
 
 
      <!--  PANTALLA: ALUMNOS -->
      <div class="pantalla" id="pantalla-alumnos">
        <h1 class="pagina-titulo">Alumnos</h1>
        <p class="pagina-subtitulo">Compañeros del establecimiento</p>
 
        <div class="busqueda-barra">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" placeholder="Buscar por nombre" id="buscador-alumnos" oninput="filtrarAlumnos()" />
        </div>
 
        <div class="filtros-fila">
          <select class="select-filtro">
            <option>Año: Todos</option>
            <option>4to Año</option>
            <option>5to Año</option>
            <option>6to Año</option>
            <option>7mo Año</option>
          </select>
          <select class="select-filtro">
            <option>Division: Todas</option>
            <option>1ra</option>
            <option>2da</option>
            <option>3ra</option>
          </select>
          <button class="boton-filtrar">Filtrar</button>
        </div>
 
        <div class="lista-alumnos" id="lista-alumnos">
          <?php if (empty($portal['companeros'])): ?>
            <div style="padding:20px;color:#6C757D;text-align:center;">No hay más alumnos registrados.</div>
          <?php else: foreach ($portal['companeros'] as $c): ?>
            <div class="alumno-item">
              <div class="alumno-avatar">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
              </div>
              <div>
                <div class="alumno-nombre"><?= e($c['nombre'] . ' ' . $c['apellido']) ?></div>
                <div class="alumno-curso"><?= e($c['anio']) ?>° Año - <?= e($c['division']) ?>°<?= $c['especialidad_nombre'] ? ' - ' . e($c['especialidad_nombre']) : '' ?></div>
              </div>
              <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </button>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
 
 
      <!--  PANTALLA: MENSAJES  -->
      <div class="pantalla" id="pantalla-mensajes">
        <div class="contenedor-mensajes">
 
          <!-- Panel izquierdo -->
          <div class="mensajes-panel-izq" id="panel-conversaciones">
            <div class="mensajes-busqueda">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
              <input type="text" placeholder="Buscar mensajes..." />
            </div>
            <div class="conversaciones-lista" id="conversaciones-lista"></div>
            <div id="mensajes-vacio" style="display:none;padding:24px;text-align:center;color:#6C757D;font-size:14px;">Todavía no tenés conversaciones.</div>
          </div>

          <!-- Panel derecho - Chat -->
          <div class="mensajes-panel-der" id="panel-chat">
            <div class="chat-cabecera" id="chat-cabecera" style="display:none;">
              <button class="chat-volver" onclick="volverConversaciones()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg>
              </button>
              <div class="conv-avatar" id="chat-avatar" style="width:36px;height:36px;font-size:13px;"></div>
              <div>
                <div class="chat-nombre" id="chat-nombre"></div>
                <div class="chat-rol" id="chat-rol"></div>
              </div>
            </div>

            <div class="chat-mensajes" id="chat-mensajes">
              <div id="chat-mensajes-vacio" style="padding:24px;text-align:center;color:#6C757D;font-size:14px;">Seleccioná una conversación para ver los mensajes.</div>
            </div>
 
            <div class="chat-input-area">
              <div class="chat-input-fila">
                <button class="chat-emoji-btn">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </button>
                <textarea class="chat-input" placeholder="Escribe un mensaje..." rows="1" id="campo-mensaje" onkeydown="enviarMensaje(event)"></textarea>
                <button class="chat-adjunto-btn">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                </button>
                <button class="chat-enviar" onclick="enviarMensajeClick()">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/></svg>
                </button>
              </div>
              <div class="chat-hint">Presiona Enter para enviar, Shift + Enter para nueva linea.</div>
            </div>
          </div>
        </div>
      </div>
 
 
      <!-- PANTALLA: PERFIL-->
      <div class="pantalla" id="pantalla-perfil">
        <div style="max-width: 820px; margin: 0 auto;">
 
          <div class="perfil-tarjeta-superior">
            <div class="perfil-avatar-wrap">
              <div class="perfil-avatar">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="5"/><path d="M3 21c0-5 4-8 9-8s9 3 9 8"/></svg>
              </div>
              <div class="perfil-avatar-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
              </div>
            </div>
            <div class="perfil-nombre"><?php echo e($nombre_completo); ?></div>
            <div class="perfil-badges">
              <span class="perfil-badge"><?= e($cursoLabel ?: 'Sin curso') ?><?= $curso && $curso['especialidad_nombre'] ? ' ' . e($curso['especialidad_nombre']) : '' ?></span>
              <?php if ($curso): ?><span class="perfil-badge">Turno <?= e($curso['turno']) ?></span><?php endif; ?>
            </div>
            <button class="boton-cerrar-sesion">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Cerrar Sesion
            </button>
          </div>
 
          <div class="perfil-cuadricula">
            <div class="tarjeta">
              <div class="tarjeta-titulo" style="margin-bottom:14px;">Datos del alumno</div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">DNI</div>
                <div class="datos-campo-valor"><?= e($usuario['dni'] ?? 'No informado') ?></div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Fecha de nacimiento</div>
                <div class="datos-campo-valor"><?= !empty($usuario['fecha_nacimiento']) ? e(date('d/m/Y', strtotime($usuario['fecha_nacimiento']))) : 'No informado' ?></div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Curso y division</div>
                <div class="datos-campo-valor"><?= $curso ? e($curso['anio']) . '° Año - ' . e($curso['division']) . '° Div.' : 'Sin curso asignado' ?></div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Turno asignado</div>
                <div class="datos-campo-valor"><?= $curso ? e(ucfirst($curso['turno'])) : 'No informado' ?></div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Orientacion academica</div>
                <div class="datos-campo-valor"><?= $curso && $curso['especialidad_nombre'] ? e($curso['especialidad_nombre']) : 'No informado' ?></div>
              </div>
            </div>

            <div class="tarjeta">
              <div class="tarjeta-titulo" style="margin-bottom:14px;">Datos del tutor</div>
              <?php if ($portal['tutor']): $tutor = $portal['tutor']; ?>
              <div class="tutor-item">
                <div class="tutor-icono">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div>
                  <div class="tutor-etiqueta">Responsable legal (<?= e(ucfirst($tutor['relacion'])) ?>)</div>
                  <div class="tutor-valor"><?= e($tutor['nombre'] . ' ' . $tutor['apellido']) ?></div>
                </div>
              </div>
              <div class="tutor-item">
                <div class="tutor-icono">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 11.9 19.79 19.79 0 0 1 1.57 3.28 2 2 0 0 1 3.53 1h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 8a16 16 0 0 0 5.91 5.91l.72-.72a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div>
                  <div class="tutor-etiqueta">Telefono de contacto</div>
                  <div class="tutor-valor"><?= e($tutor['telefono'] ?? 'No informado') ?></div>
                </div>
              </div>
              <div class="tutor-item">
                <div class="tutor-icono">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                  <div class="tutor-etiqueta">Correo electronico</div>
                  <div class="tutor-valor"><?= e($tutor['email']) ?></div>
                </div>
              </div>
              <?php else: ?>
              <div style="padding:8px 0;color:#6C757D;font-size:14px;">Sin tutor vinculado todavía.</div>
              <?php endif; ?>
            </div>
          </div>
 
          <div class="pie-perfil">EEST N°1 "Eduardo Ader" - Sistema de Gestion Academica</div>
 
        </div>
      </div>
 
    </main>
  </div>
 
    
 
 

</div>

<script>
  // Datos reales inyectados por el servidor (ver AlumnoController::portalData()),
  // reemplazan los mocks hardcodeados que tenía alumnos.js.
  window.SERVER_DATA = <?php
    echo json_encode([
      'calendario' => $portal['calendario'],
      'msgs' => $portal['msgs'],
      'csrfToken' => csrf_token(),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  ?>;
</script>
<script src="../public/assets/js/alumnos.js?v=4"></script>
