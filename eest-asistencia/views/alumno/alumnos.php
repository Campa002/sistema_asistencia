<?php
require_role('alumno');

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';
$nombre_completo = trim($nombre . ' ' . $apellido);

/*
 * El curso puede incorporarse a la sesión más adelante.
 * Mientras no exista, la sidebar muestra únicamente "Alumno".
 */
$curso_sidebar = $_SESSION['curso'] ?? '';
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
        <p class="saludo-subtitulo">7° Año - Informática. Revisa tu progreso académico hoy.</p>
 
        <div class="fecha-badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          15 de Mayo, 2026
        </div>
 
        <div class="cuadricula-dashboard">
 
          <!-- Asistencia total -->
          <div class="tarjeta tarjeta-asistencia">
            <div class="tarjeta-titulo">Asistencia total</div>
            <div class="circulo-contenedor">
              <div class="circulo-wrap">
                <svg class="circulo-svg" width="150" height="150" viewBox="0 0 150 150">
                  <circle cx="75" cy="75" r="60" fill="none" stroke="#DEE2E6" stroke-width="12"/>
                  <circle cx="75" cy="75" r="60" fill="none" stroke="#3498DB" stroke-width="12"
                    stroke-dasharray="339.3"
                    stroke-dashoffset="33.9"
                    stroke-linecap="round"/>
                </svg>
                <div style="position:absolute; display:flex; flex-direction:column; align-items:center;">
                  <span class="circulo-porcentaje">90%</span>
                  <span class="circulo-label">Presente</span>
                </div>
              </div>
            </div>
            <div class="stats-fila">
              <div class="stat-item">
                <span class="stat-label">Faltas</span>
                <span class="stat-valor rojo">3</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Tardes</span>
                <span class="stat-valor amarillo">2</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Justificadas</span>
                <span class="stat-valor azul">1</span>
              </div>
            </div>
          </div>
 
          <!-- Avisos recientes -->
          <div class="tarjeta tarjeta-avisos">
            <div class="avisos-cabecera">
              <span class="tarjeta-titulo" style="margin-bottom:0">Avisos recientes</span>
              <button class="ver-todos">Ver todos</button>
            </div>
            <div class="aviso-item">
              <div class="aviso-icono">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              </div>
              <div>
                <div class="aviso-titulo">Mantenimiento de Servidores</div>
                <div class="aviso-descripcion">El servidor de la escuela...</div>
                <div class="aviso-tiempo">Hace 2 horas</div>
              </div>
            </div>
            <div class="aviso-item" style="border-left-color: #28A745;">
              <div class="aviso-icono" style="background: rgba(40,167,69,0.1); color: var(--verde);">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <div class="aviso-titulo">Inscripción a Pasantias 2026</div>
                <div class="aviso-descripcion">Recordamos a los alumnos de...</div>
                <div class="aviso-tiempo">Ayer</div>
              </div>
            </div>
          </div>
 
          <!-- Notificaciones -->
          <div class="tarjeta tarjeta-notificaciones">
            <div class="avisos-cabecera">
              <span class="tarjeta-titulo" style="margin-bottom:0">Notificaciones</span>
              <button class="ver-todos">Ver todas</button>
            </div>
            <div class="notificacion-item">
              <div class="notificacion-dot verde"></div>
              <div>
                <div class="notificacion-texto">Asistencia registrada para Programación II</div>
                <div class="notificacion-tiempo">Hoy, 08:10</div>
              </div>
            </div>
            <div class="notificacion-item">
              <div class="notificacion-dot amarillo"></div>
              <div>
                <div class="notificacion-texto">Llegada tarde registrada el 16 de Mayo</div>
                <div class="notificacion-tiempo">Ayer</div>
              </div>
            </div>
            <div class="notificacion-item">
              <div class="notificacion-dot rojo"></div>
              <div>
                <div class="notificacion-texto">Inasistencia registrada el 15 de Mayo</div>
                <div class="notificacion-tiempo">15 Mayo</div>
              </div>
            </div>
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
        <p class="pagina-subtitulo">7mo 2da - Ciclo Superior</p>
 
        <div class="cuadricula-stats">
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta">Asistencia Total</div>
            <div class="tarjeta-stat-valor" style="color: var(--verde);">92%</div>
          </div>
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta">Inasistencias</div>
            <div class="tarjeta-stat-valor" style="color: var(--rojo);">4.5 <span>/ 28</span></div>
          </div>
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta-icono">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 17l5-5-5-5M6 17l5-5-5-5"/></svg>
              Retiro anticipado
            </div>
            <div class="tarjeta-stat-valor">1</div>
          </div>
          <div class="tarjeta-stat">
            <div class="tarjeta-stat-etiqueta-icono" style="color: #E67E22;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E67E22" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
              Llegadas tarde
            </div>
            <div class="tarjeta-stat-valor">3</div>
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
 
              <div class="registro-fila">
                <div>
                  <div class="registro-fecha-titulo">17 Mayo</div>
                  <div class="registro-fecha-dia">Viernes</div>
                </div>
                <div style="text-align:center;">
                  <div class="registro-entrada-label">E/S</div>
                  <div class="registro-entrada">07:45<br>11:55</div>
                </div>
                <div class="registro-estado">
                  <span class="badge-estado presente">Presente</span>
                </div>
              </div>
 
              <div class="registro-fila">
                <div>
                  <div class="registro-fecha-titulo">16 Mayo</div>
                  <div class="registro-fecha-dia">Jueves</div>
                </div>
                <div style="text-align:center;">
                  <div class="registro-entrada-label">E/S</div>
                  <div class="registro-entrada">07:58<br>12:15</div>
                </div>
                <div class="registro-estado">
                  <span class="badge-estado tarde">Tarde</span>
                  <span class="registro-falta">0.25 Falta</span>
                </div>
              </div>
 
              <div class="registro-fila">
                <div>
                  <div class="registro-fecha-titulo">15 Mayo</div>
                  <div class="registro-fecha-dia">Miercoles</div>
                </div>
                <div style="text-align:center;">
                  <div class="registro-entrada-label">E/S</div>
                  <div class="registro-entrada">--:--<br>--:--</div>
                </div>
                <div class="registro-estado">
                  <span class="badge-estado ausente">Ausente</span>
                  <span class="registro-falta">1.0 Falta</span>
                </div>
              </div>
 
              <div class="registro-fila">
                <div>
                  <div class="registro-fecha-titulo">14 Mayo</div>
                  <div class="registro-fecha-dia">Martes</div>
                </div>
                <div style="text-align:center;">
                  <div class="registro-entrada-label">E/S</div>
                  <div class="registro-entrada">--:--<br>--:--</div>
                </div>
                <div class="registro-estado">
                  <span class="badge-estado justificado">Justificado</span>
                  <span class="registro-falta">0.0 Falta</span>
                </div>
              </div>
            </div>
          </div>
 
          <!-- Columna lateral: resumen mensual -->
          <div>
            <div class="tarjeta" style="margin-bottom: 16px;">
              <div class="tarjeta-titulo">Resumen mensual</div>
              <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Dias habiles</span>
                  <span style="font-weight:700;">20</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Presentes</span>
                  <span style="font-weight:700; color:var(--verde);">15</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Ausentes</span>
                  <span style="font-weight:700; color:var(--rojo);">3</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--borde);">
                  <span style="font-size:14px; color:var(--gris);">Tarde</span>
                  <span style="font-weight:700; color:#E67E22;">2</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:8px 0;">
                  <span style="font-size:14px; color:var(--gris);">Justificados</span>
                  <span style="font-weight:700; color:var(--azul-claro);">1</span>
                </div>
              </div>
            </div>
            <div class="tarjeta">
              <div class="tarjeta-titulo">Porcentaje acumulado</div>
              <div style="margin: 16px 0;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;">
                  <span>Asistencia</span>
                  <span style="font-weight:700; color:var(--verde);">92%</span>
                </div>
                <div style="background:var(--fondo-claro); border-radius:8px; height:10px; overflow:hidden;">
                  <div style="background:var(--verde); width:92%; height:100%; border-radius:8px;"></div>
                </div>
              </div>
              <div style="margin: 16px 0;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px;">
                  <span>Maximo permitido</span>
                  <span style="font-weight:700; color:var(--rojo);">28 faltas</span>
                </div>
                <div style="background:var(--fondo-claro); border-radius:8px; height:10px; overflow:hidden;">
                  <div style="background:var(--rojo); width:16%; height:100%; border-radius:8px;"></div>
                </div>
                <div style="font-size:12px; color:var(--gris); margin-top:6px;">4.5 / 28 faltas acumuladas</div>
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
          <div class="alumno-item">
            <div class="alumno-avatar">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
              <div class="alumno-nombre">Martina Garcia</div>
              <div class="alumno-curso">4to Año - 1ra</div>
            </div>
            <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>
          </div>
          <div class="alumno-item">
            <div class="alumno-avatar">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
              <div class="alumno-nombre">Julian Rodriguez</div>
              <div class="alumno-curso">4to Año - 1ra</div>
            </div>
            <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>
          </div>
          <div class="alumno-item">
            <div class="alumno-avatar">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
              <div class="alumno-nombre">Sofia Lopez</div>
              <div class="alumno-curso">5to Año - 3ra</div>
            </div>
            <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>
          </div>
          <div class="alumno-item">
            <div class="alumno-avatar">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
              <div class="alumno-nombre">Lucas Martinez</div>
              <div class="alumno-curso">4to Año - 1ra</div>
            </div>
            <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>
          </div>
          <div class="alumno-item">
            <div class="alumno-avatar">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
              <div class="alumno-nombre">Ramiro Caballo</div>
              <div class="alumno-curso">7mo Año - 2da</div>
            </div>
            <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>
          </div>
          <div class="alumno-item">
            <div class="alumno-avatar">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
              <div class="alumno-nombre">Mateo Bianchi</div>
              <div class="alumno-curso">4to Año - 1ra</div>
            </div>
            <button class="alumno-chat-btn" onclick="mostrarPantalla('pantalla-mensajes')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </button>
          </div>
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
            <div class="mensajes-filtros">
              <button class="filtro-pill activo">Todos</button>
              <button class="filtro-pill">Profesores</button>
              <button class="filtro-pill">Preceptores</button>
            </div>
            <div class="conversaciones-lista">
              <div class="conversacion-item activa" onclick="abrirChat(event)">
                <div class="conv-avatar">RM</div>
                <div style="flex:1; min-width:0;">
                  <div class="conv-nombre">Roberto Martinez</div>
                  <div class="conv-rol">Preceptor - 5to 2da</div>
                  <div class="conv-preview">Pudiste revisar el formulario de la salida?</div>
                </div>
                <div class="conv-hora">14:20</div>
              </div>
              <div class="conversacion-item" onclick="abrirChat(event)">
                <div class="conv-avatar" style="background: var(--azul-medio);">PB</div>
                <div style="flex:1; min-width:0;">
                  <div class="conv-nombre">Paula Balda</div>
                  <div class="conv-rol">Profesora - Base de Datos</div>
                  <div class="conv-preview">El trabajo practico es para el viernes</div>
                </div>
                <div class="conv-hora">Ayer</div>
              </div>
            </div>
          </div>
 
          <!-- Panel derecho - Chat -->
          <div class="mensajes-panel-der" id="panel-chat">
            <div class="chat-cabecera">
              <button class="chat-volver" onclick="volverConversaciones()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg>
              </button>
              <div class="conv-avatar" style="width:36px;height:36px;font-size:13px;">RM</div>
              <div>
                <div class="chat-nombre">Roberto Martinez</div>
                <div class="chat-rol">Preceptor 5° 2° turno mañana</div>
              </div>
              <button class="chat-opciones">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
              </button>
            </div>
 
            <div class="chat-mensajes">
              <div class="chat-fecha-separador">HOY</div>
 
              <div class="mensaje-burbuja recibido">
                Hola Juan, te escribo para recordarte que debes traer la autorización firmada por tus padres para la visita técnica del próximo viernes.
              </div>
              <div class="mensaje-hora">09:15 AM</div>
 
              <div class="mensaje-burbuja enviado">
                Buenos días Roberto. Ya la tengo firmada, se la puedo alcanzar al preceptoria durante el primer recreo?
              </div>
              <div class="mensaje-hora der">09:42 AM</div>
 
              <div class="mensaje-burbuja recibido">
                Si, perfecto. Estaré recibiendo las de todo el curso. Pudiste revisar el formulario de la salida que envié al correo?
              </div>
              <div class="mensaje-hora">14:20 PM</div>
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
            <div class="perfil-nombre">Santiago Garcia</div>
            <div class="perfil-badges">
              <span class="perfil-badge">6° 1° Electronica</span>
              <span class="perfil-badge">Turno tarde</span>
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
                <div class="datos-campo-valor">45.228.391</div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Fecha de nacimiento</div>
                <div class="datos-campo-valor">14 Oct 2008</div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Curso y division</div>
                <div class="datos-campo-valor">6to Año - 1ra Div.</div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Turno asignado</div>
                <div class="datos-campo-valor">Tarde (12:55 a 17:35)</div>
              </div>
              <div class="datos-campo">
                <div class="datos-campo-etiqueta">Orientacion academica</div>
                <div class="datos-campo-valor">Tecnicatura en Informatica Personal y Profesional</div>
              </div>
            </div>
 
            <div class="tarjeta">
              <div class="tarjeta-titulo" style="margin-bottom:14px;">Datos del tutor</div>
              <div class="tutor-item">
                <div class="tutor-icono">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div>
                  <div class="tutor-etiqueta">Responsable legal</div>
                  <div class="tutor-valor">Mariana Solis</div>
                </div>
              </div>
              <div class="tutor-item">
                <div class="tutor-icono">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 11.9 19.79 19.79 0 0 1 1.57 3.28 2 2 0 0 1 3.53 1h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 8a16 16 0 0 0 5.91 5.91l.72-.72a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div>
                  <div class="tutor-etiqueta">Telefono de contacto</div>
                  <div class="tutor-valor">+54 9 11 5839-2011</div>
                </div>
              </div>
              <div class="tutor-item">
                <div class="tutor-icono">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                  <div class="tutor-etiqueta">Correo electronico</div>
                  <div class="tutor-valor">m.solis.tutor@abc.gob.ar</div>
                </div>
              </div>
              <button class="boton-solicitar">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Solicitar Actualizacion
              </button>
            </div>
          </div>
 
          <div class="pie-perfil">EEST N°1 "Eduardo Ader" - Sistema de Gestion Academica</div>
 
        </div>
      </div>
 
    </main>
  </div>
 
    
 
 

</div>

<script src="../public/assets/js/alumnos.js?v=3"></script>
