<?php
require_role('preceptor');
require_once __DIR__ . '/../../controllers/PreceptorController.php';

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';
$nombre_completo = trim($nombre . ' ' . $apellido);

$preceptorId = (int) $_SESSION['usuario_id'];
$portal = PreceptorController::portalData($preceptorId);
$usuario = $portal['usuario'];
$turno = $portal['turnoResumen'];
?>
<link rel="stylesheet" href="../public/assets/css/preceptor.css">
<link rel="shortcut icon" href="../../public/assets/img/logo.webp" type="image/x-icon">
<div id="preceptor-portal-root">


<!-- TOPBAR -->
<header class="topbar">
  <div class="topbar-brand">
    <img id="nav-dashboard" onclick="showView('dashboard')" class="b" src="../public/assets/img/logo.webp" alt="">
    EEST N°1 Eduardo Ader
  </div>
  <div class="topbar-icons">
    <?php $totalNoLeidos = array_sum(array_column($portal['msgs'], 'unread')); ?>
    <button class="notif-wrap" onclick="showView('mensajes')" aria-label="Notificaciones">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <?php if ($totalNoLeidos > 0): ?><span class="notif-badge"><?= e($totalNoLeidos) ?></span><?php endif; ?>
    </button>

    <button onclick="showView('perfil')" aria-label="Abrir perfil">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
    </button>

    <button
      type="button"
      class="preceptor-menu-toggle"
      id="preceptor-menu-toggle"
      onclick="toggleSidebarPreceptor()"
      aria-label="Abrir menú"
      aria-controls="preceptor-sidebar"
      aria-expanded="false"
    >
      <svg class="preceptor-icon-menu" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
      <svg class="preceptor-icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="6" y1="6" x2="18" y2="18"/>
        <line x1="18" y1="6" x2="6" y2="18"/>
      </svg>
    </button>
  </div>
</header>

<div class="layout">
  <!-- SIDEBAR -->
  <nav class="sidebar" id="preceptor-sidebar" aria-label="Menú principal del preceptor">
    <div class="sidebar-user">
      <div class="sidebar-avatar" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="8" r="4"></circle>
          <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"></path>
        </svg>
      </div>

      <div class="sidebar-name">
        <?php echo e($apellido . ', ' . $nombre); ?>
      </div>

      <div class="sidebar-role">
        Preceptor<?php echo $turno !== '' ? ' — Turno ' . e(ucfirst($turno)) : ''; ?>
      </div>
    </div>

    <div class="sidebar-nav">
      <div class="nav-item active" id="nav-dashboard" onclick="showView('dashboard')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        <span class="nav-label">Dashboard</span>
      </div>
      <div class="nav-item" id="nav-asistencia" onclick="showView('asistencia')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span class="nav-label">Asistencia</span>
      </div>
      <div class="nav-item" id="nav-historial" onclick="showView('historial')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span class="nav-label">Historial</span>
      </div>
      <div class="nav-item" id="nav-alumnos" onclick="showView('alumnos')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span class="nav-label">Alumnos</span>
      </div>
      <div class="nav-item" id="nav-mensajes" onclick="showView('mensajes')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="nav-label">Mensajes</span>
      </div>
      <div class="nav-item" id="nav-perfil" onclick="showView('perfil')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        <span class="nav-label">Mi Perfil</span>
      </div>
    </div>
  </nav>

  <button
    type="button"
    class="preceptor-sidebar-overlay"
    id="preceptor-sidebar-overlay"
    onclick="cerrarSidebarPreceptor()"
    aria-label="Cerrar menú"
    tabindex="-1"
  ></button>

  <!-- MAIN -->
  <main class="main">

    <!-- ══════════ DASHBOARD ══════════ -->
    <section class="view active" id="view-dashboard">
      <div style="margin-bottom:20px">
        <div class="dash-welcome">Buen día, <?= e($nombre) ?></div>
        <div class="dash-sub">Resumen de actividad para hoy, <?= e($portal['fechaHoyLarga']) ?>.</div>
      </div>
      <div class="grid-2" style="margin-bottom:16px">
        <div class="stat-card-light">
          <div class="stat-label">Asistencia hoy</div>
          <div class="stat-num"><?= $portal['asistenciaHoyPct'] !== null ? e($portal['asistenciaHoyPct']) . '%' : '—' ?></div>
        </div>
        <div class="stat-card-light">
          <div class="stat-label">Cursos a cargo</div>
          <div class="stat-num" style="color:var(--gray-800)"><?= e(count($portal['cursos'])) ?></div>
        </div>
      </div>


      <div class="card">
        <div class="section-title">
          Cursos a Cargo
          <span class="turn-label"><?= e($turno ?: '—') ?></span>
        </div>
        <table class="course-table">
          <thead>
            <tr>
              <th>CURSO</th>
              <th>ESTADO</th>
              <th>ACCIÓN</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($portal['cursos'])): ?>
              <tr><td colspan="3" style="text-align:center;color:var(--gray-400)">No tenés cursos asignados actualmente.</td></tr>
            <?php else: foreach ($portal['cursos'] as $c): ?>
              <tr>
                <td><?= e($c['name']) ?> — <?= e($c['spec']) ?></td>
                <td>
                  <?php if ($c['status'] === 'completa'): ?>
                    <span class="badge badge-green">Completo</span>
                  <?php else: ?>
                    <span class="badge badge-yellow">Pendiente</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($c['status'] === 'completa'): ?>
                    <span class="action-link" onclick="showView('historial')">Ver lista</span>
                  <?php else: ?>
                    <span class="action-link" onclick="openTomarAsistencia(<?= (int) $c['id'] ?>)">Tomar</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <div class="card" style="margin-top:16px">
        <div class="section-title">
          <span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
            Avisos de los directivos
          </span>
        </div>
        <?php if (empty($portal['avisos'])): ?>
          <div style="padding:12px 0;color:var(--gray-400);font-size:13px">Sin avisos por el momento.</div>
        <?php else: foreach ($portal['avisos'] as $aviso): ?>
          <div class="aviso <?= $aviso['prioridad'] === 'urgente' || $aviso['prioridad'] === 'alta' ? 'aviso-red' : 'aviso-gray' ?>">
            <div class="aviso-title" style="<?= $aviso['prioridad'] === 'normal' ? 'color:var(--gray-700)' : '' ?>"><?= e($aviso['titulo']) ?></div>
            <div class="aviso-body"><?= e($aviso['contenido']) ?></div>
            <div class="aviso-tag">PRIORIDAD <?= e(mb_strtoupper($aviso['prioridad'])) ?></div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </section>

    <!-- ══════════ ASISTENCIA – selección ══════════ -->
    <section class="view" id="view-asistencia">
      <div class="eyebrow">Gestión Escolar</div>
      <div class="page-title">Selección de Cursos</div>
      <div class="page-subtitle">Gestión de asistencia diaria por división.</div>

      <div class="search-row">
        <input class="search-input" type="text" placeholder="🔍  Buscar por año o división..." oninput="filterCourses(this.value)">
        <button class="btn btn-dark">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
          Filtrar
        </button>
      </div>

      <div class="courses-grid" id="courses-grid">
        <!-- Tarjetas generadas por JS -->
      </div>
    </section>

    <!-- ══════════ TOMAR ASISTENCIA ══════════ -->
    <section class="view" id="view-tomar">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
        <button class="btn btn-ghost btn-sm" onclick="showView('asistencia')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          Volver
        </button>
        <div>
          <div class="eyebrow">Gestión Escolar</div>
          <div class="page-title" id="tomar-title">Registro de Asistencia</div>
          <div style="font-size:13px;color:var(--gray-500)" id="tomar-course">5to 2da – Electrónica</div>
        </div>
      </div>

      <div class="card" style="margin-bottom:16px">
        <div class="attend-selectors">
          <div>
            <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px">MÓDULO / HORA</div>
            <select class="attend-select" id="tomar-modulo" onchange="onTomarModuloChange()"></select>
          </div>
          <div>
            <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px">FECHA</div>
            <input type="date" class="attend-select" id="tomar-fecha" value="<?= e(date('Y-m-d')) ?>" onchange="onTomarFechaChange()">
          </div>
        </div>
      </div>

      <div class="stat-row">
        <div class="mini-stat">
          <div class="mini-stat-icon green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div><div class="mini-stat-num" id="cnt-p">22</div><div class="mini-stat-lbl">Presentes</div></div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-icon red">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          </div>
          <div><div class="mini-stat-num" id="cnt-a">3</div><div class="mini-stat-lbl">Ausentes</div></div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-icon yellow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div><div class="mini-stat-num" id="cnt-t">2</div><div class="mini-stat-lbl">Tardes</div></div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-icon blue">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </div>
          <div><div class="mini-stat-num" id="cnt-ra">1</div><div class="mini-stat-lbl">Retiros</div></div>
        </div>
      </div>

      <div class="card">
        <table class="attend-table" id="attend-table">
          <thead>
            <tr>
              <th>ALUMNO</th>
              <th>REGISTRO DE ASISTENCIA</th>
            </tr>
          </thead>
          <tbody id="attend-tbody"></tbody>
        </table>
        <div class="attend-footer">
          <span class="attend-count" id="attend-count">Mostrando 28 de 28 alumnos matriculados</span>
          <button class="btn btn-dark" id="btn-guardar-asistencia" onclick="guardarAsistencia()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Guardar Asistencia
          </button>
        </div>
      </div>
    </section>

    <!-- ══════════ HISTORIAL ══════════ -->
    <section class="view" id="view-historial">
      <div class="page-title">Historial de Asistencias</div>
      <div class="page-subtitle">Consulta de registros finalizados y modificados.</div>

      <div class="hist-stat-row">
        <div class="hist-stat-dark">
          <div class="hist-lbl white">Total finalizadas</div>
          <div class="hist-num" style="color:#fff">142</div>
        </div>
        <div class="hist-stat-light">
          <div>
            <div class="hist-lbl gray">Modificadas</div>
            <div class="hist-num blue">12</div>
          </div>
          <button style="background:none;border:none;cursor:pointer;color:var(--blue-accent)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </button>
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-bottom:16px">
        <input class="search-input" type="text" placeholder="🔍  Buscar curso ...">
      </div>

      <div class="filter-bar">
        <button class="filter-chip active" onclick="filterChip(this)">Curso/División</button>
        <button class="filter-chip" onclick="filterChip(this)">Horario</button>
        <button class="filter-chip" onclick="filterChip(this)">Turno</button>
        <button class="filter-chip" onclick="filterChip(this)">Fecha</button>
      </div>

      <div id="hist-list">
        <!-- JS generado -->
      </div>
    </section>

    <!-- ══════════ ALUMNOS ══════════ -->
    <section class="view" id="view-alumnos">
      <div class="eyebrow">Panel de Control</div>
      <div class="page-title">Directorio de Alumnos</div>

      <div style="background:var(--gray-100);border-radius:8px;padding:8px 14px;font-size:13px;color:var(--gray-600);display:inline-flex;align-items:center;gap:6px;margin-bottom:16px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Ciclo Lectivo 2026
      </div>

      <div class="search-row">
        <input class="search-input" type="text" placeholder="🔍  Buscar por nombre, apellido o DNI..." oninput="filterAlumnos(this.value)">
      </div>

      <div class="alumnos-filters" id="alumnos-filter-bar">
        <button class="chip-btn active" onclick="filterByDivision('todos', this)">Todos</button>
        <?php foreach ($portal['cursos'] as $c): ?>
          <button class="chip-btn" onclick="filterByDivision('<?= e($c['name']) ?>', this)"><?= e($c['name']) ?></button>
        <?php endforeach; ?>
      </div>

      <div class="card" style="padding:0;overflow:hidden">
        <table class="alumnos-table" id="alumnos-table">
          <thead>
            <tr>
              <th>ALUMNO</th>
              <th>DNI</th>
              <th>CURSO</th>
              <th>ESTADO</th>
            </tr>
          </thead>
          <tbody id="alumnos-tbody"></tbody>
        </table>
        <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--gray-100)">
          <span class="page-info" id="alumnos-count">Mostrando <?= e(min(10, count($portal['alumnos']))) ?> de <?= e(count($portal['alumnos'])) ?> alumnos</span>
          <div class="pagination">
            <button class="page-btn" onclick="alumnosPage(-1)">&#8592;</button>
            <button class="page-btn" onclick="alumnosPage(1)">&#8594;</button>
          </div>
        </div>
      </div>
    </section>

    <!-- ══════════ MENSAJES ══════════ -->
    <section class="view" id="view-mensajes">
      <div class="eyebrow">Centro de Mensajería</div>
      <div class="page-title" style="margin-bottom:16px">Gestión institucional de comunicaciones con familias.</div>

      <div class="msg-layout">
        <div class="msg-sidebar">
          <div class="msg-sidebar-header">
            <div class="msg-sidebar-title">Bandeja de Entrada</div>
            <div style="display:flex;gap:8px">
              <input class="search-input" style="padding:7px 12px;font-size:12px" placeholder="🔍  Buscar tutor o alumno...">
              <button class="btn btn-dark btn-sm">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                Filtrar
              </button>
            </div>
          </div>
          <div id="msg-list"></div>
        </div>
        <div class="msg-panel" id="msg-panel">
          <div class="msg-no-selection" id="msg-no-sel">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Seleccioná una conversación
          </div>
          <div id="msg-chat" style="display:none;flex:1;flex-direction:column">
            <div class="msg-panel-header">
              <div>
                <div class="msg-panel-from" id="chat-from"></div>
                <div class="msg-panel-sub" id="chat-sub"></div>
              </div>
              <button class="btn btn-ghost btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
              </button>
            </div>
            <div class="msg-panel-body" id="chat-body"></div>
            <div class="msg-input-row">
              <textarea class="msg-input" rows="2" placeholder="Escribí un mensaje..." id="chat-input"></textarea>
              <button class="btn btn-primary" onclick="sendMsg()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      <button class="fab" onclick="newMsg()" title="Nuevo mensaje">+</button>
    </section>

    <!-- ══════════ PERFIL ══════════ -->
    <section class="view" id="view-perfil">
      <div class="card">
        <div class="profile-top">
          <div class="profile-avatar">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <div class="profile-edit-btn">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
          </div>
          <div>
            <div class="profile-name"><?php echo e($nombre_completo); ?></div>
            <div style="font-size:13px;color:var(--gray-500);margin-bottom:8px">DNI: <?= e($usuario['dni'] ?? '—') ?></div>
            <div class="profile-tags">
              <span class="badge badge-gray">Preceptor</span>
              <?php if ($turno): ?><span class="badge badge-gray"><?= e($turno) ?></span><?php endif; ?>
            </div>
          </div>
        </div>
        <div class="profile-actions">
          <form method="POST" action="index.php" style="display:inline;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn btn-danger">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Cerrar Sesión
            </button>
          </form>
          <button class="btn btn-danger" onclick="alert('Función: Indicar ausencia')">Indicar ausencia</button>
        </div>
        <hr class="div">
        <div class="profile-data-row">
          <div class="profile-data-label">Correo Institucional</div>
          <div class="profile-data-val"><?= e($usuario['email'] ?? '—') ?></div>
        </div>
        <div class="profile-data-row">
          <div class="profile-data-label">Fecha de Registro</div>
          <div class="profile-data-val"><?= e($usuario['created_at'] ? format_date_long_argentina($usuario['created_at']) : '—') ?></div>
        </div>
        <div class="profile-data-row" style="border-bottom:none">
          <div class="profile-data-label">Estado Administrativo</div>
          <div class="profile-data-val" style="display:flex;align-items:center;gap:6px">
            <span style="width:8px;height:8px;border-radius:50%;background:var(--green);display:inline-block"></span>
            <?= $usuario['estado'] === 'activo' ? 'Cuenta activa' : e(ucfirst($usuario['estado'])) ?>
          </div>
        </div>
        <hr class="div">
        <button class="btn btn-ghost" style="width:100%;justify-content:center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          Configuración de Cuenta
        </button>
      </div>

      <div class="card" style="margin-top:16px">
        <div class="section-title">
          <span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Cursos Asignados
          </span>
          <a href="#" class="action-link" onclick="showView('asistencia');return false">Ver todos los cursos →</a>
        </div>
        <?php if (empty($portal['cursos'])): ?>
          <div style="padding:8px 0;color:var(--gray-400);font-size:13px">Sin cursos asignados.</div>
        <?php else: foreach ($portal['cursos'] as $c): ?>
          <div class="course-list-item"><span class="course-list-name"><?= e($c['name']) ?> – <?= e($c['spec']) ?></span><span class="badge badge-blue">Activo</span></div>
        <?php endforeach; endif; ?>
      </div>
    </section>

  </main>
</div>

<script>
  // Datos reales inyectados por el servidor (reemplazan los mocks
  // hardcodeados que tenía preceptor.js). Ver PreceptorController::portalData().
  window.SERVER_DATA = <?php
    $bloques = [
      'mañana' => [Asistencia::getBloqueHorarioInfo('mañana', 'primera_hora'), Asistencia::getBloqueHorarioInfo('mañana', 'segunda_hora')],
      'tarde' => [Asistencia::getBloqueHorarioInfo('tarde', 'primera_hora'), Asistencia::getBloqueHorarioInfo('tarde', 'segunda_hora')],
      'vespertino' => [Asistencia::getBloqueHorarioInfo('vespertino', 'primera_hora'), Asistencia::getBloqueHorarioInfo('vespertino', 'segunda_hora')],
    ];
    // Horario real por curso (asignaciones_materias), recortado a lo que
    // necesita el selector de "Tomar Asistencia": qué materia corresponde a
    // cada día/horario — nunca se inventa ni se deja elegir un materia_id
    // que no esté realmente agendado (el backend vuelve a validar esto).
    $horariosPorCurso = [];
    foreach ($portal['horariosPorCurso'] as $cid => $asignaciones) {
      $horariosPorCurso[$cid] = array_map(fn($a) => [
        'materiaId' => (int) $a['materia_id'],
        'materiaNombre' => $a['materia_nombre'],
        'diaSemana' => (int) $a['dia_semana'],
        'horaInicio' => substr($a['hora_inicio'], 0, 5),
        'horaFin' => substr($a['hora_fin'], 0, 5),
      ], $asignaciones);
    }

    echo json_encode([
      'cursos' => $portal['cursos'],
      'alumnos' => $portal['alumnos'],
      'historial' => $portal['historial'],
      'msgs' => $portal['msgs'],
      'bloques' => $bloques,
      'horariosPorCurso' => $horariosPorCurso,
      'csrfToken' => csrf_token(),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  ?>;
</script>
<script src="../public/assets/js/preceptor.js?v=3"></script>

</div>
