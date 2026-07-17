<?php
require_role('padre_tutor');
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';
$nombre_completo = trim($nombre . ' ' . $apellido);
?>
<link rel="stylesheet" href="../public/assets/css/padretutor.css">
<div id="pt-portal-root">


<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- NOTIF PANEL -->
<div class="notif-panel" id="notif-panel">
  <div class="notif-panel-header">Notificaciones</div>
  <div class="notif-p-item">
    <div class="notif-p-title">⚠️ Llegada tarde registrada</div>
    <div class="notif-p-body">Lucas llegó 15 minutos tarde hoy a las 07:45 hs.</div>
    <div class="notif-p-time">Hace 2 horas</div>
  </div>
  <div class="notif-p-item">
    <div class="notif-p-title">📅 Reunión de Padres – 15 Oct</div>
    <div class="notif-p-body">Sala de Profesores, 18:00 hs. Asistencia obligatoria.</div>
    <div class="notif-p-time">Ayer</div>
  </div>
  <div class="notif-p-item">
    <div class="notif-p-title">✅ Justificación aprobada</div>
    <div class="notif-p-body">La inasistencia del 02/10 fue aprobada por el preceptor.</div>
    <div class="notif-p-time">Hace 3 días</div>
  </div>
</div>

<!-- TOPBAR -->
<header class="topbar">
  <div class="topbar-left">
    <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Menú">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="topbar-brand">
      <img class="b" id="nav-resumen" onclick="showView('resumen')" src="../public/assets/img/logo.webp" alt="">
      EEST N°1 Ader
    </div>
  </div>
  <div class="topbar-icons">
    <button class="notif-wrap" onclick="toggleNotifPanel()">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <span class="notif-badge">3</span>
    </button>
  </div>
</header>

<div class="layout">

  <!-- SIDEBAR -->
  <nav class="sidebar" id="sidebar">
    <div class="sidebar-user">
      <div class="sidebar-avatar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      </div>
      <div class="sidebar-name"><?php echo e($apellido . ', ' . $nombre); ?></div>
      <div class="sidebar-role">Tutora – Lucas García</div>
    </div>
    <div class="sidebar-nav">
      <div class="nav-item active" id="nav-resumen" onclick="showView('resumen')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Resumen
      </div>
      <div class="nav-item" id="nav-registro" onclick="showView('registro')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Registro
      </div>
      <div class="nav-item" id="nav-mensajes" onclick="showView('mensajes')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Mensajes
        <span class="badge-notif">2</span>
      </div>
      <div class="nav-item" id="nav-perfil" onclick="showView('perfil')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Mi Perfil
      </div>
    </div>
    <div class="sidebar-footer">
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="btn-logout">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Cerrar Sesión
        </button>
      </form>
    </div>
  </nav>

  <!-- MAIN -->
  <main class="main">

    <!-- ══════════ RESUMEN ══════════ -->
    <section class="view active" id="view-resumen">
      <div class="dash-greeting">Buenos días, Familia Rossi</div>
      <div class="dash-sub">Resumen de asistencia de su hijo</div>

      <!-- Alumno selector -->
      <div class="alumno-selector" onclick="alert('Seleccionar otro alumno vinculado')">
        <div class="alumno-avatar">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <div class="dot"></div>
        </div>
        <div class="alumno-info">
          <div class="alumno-name">Mateo García</div>
          <div class="alumno-course">4to Año – Electronica</div>
        </div>
        <div class="alumno-chevron">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>

      <!-- Asistencia card -->
      <div class="asist-card">
        <div class="asist-label">
          Promedio de asistencias
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--blue-btn)" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div class="asist-pct">94% <span>EXCELENTE</span></div>
        <div class="progress-bar"><div class="progress-fill" style="width:94%"></div></div>
      </div>

      <!-- Mini stats -->
      <div class="grid-2" style="margin-bottom:18px;">
        <div class="mini-stat-card">
          <div class="mini-stat-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
            Inasistencias
          </div>
          <div class="mini-stat-num">02</div>
          <div class="mini-stat-sub">Ciclo lectivo</div>
        </div>
        <div class="mini-stat-card">
          <div class="mini-stat-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--yellow)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Llegadas tarde
          </div>
          <div class="mini-stat-num">03</div>
          <div class="mini-stat-sub">Acumuladas</div>
        </div>
      </div>

      <!-- Aviso importante -->
      <div class="aviso-imp">
        <div class="aviso-imp-eyebrow">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Aviso Importante
        </div>
        <div class="aviso-imp-title">Reunión de Padres</div>
        <div class="aviso-imp-body">Se convoca a una reunión presencial para tratar el rendimiento académico del segundo trimestre.</div>
        <div class="aviso-imp-footer">
          <span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            15 OCT
          </span>
          <span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            18:00 HS
          </span>
        </div>
        <div class="aviso-imp-icon">
          <svg width="80" height="80" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-7-1a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
        </div>
      </div>

      <!-- Registros recientes -->
      <div class="card">
        <div class="section-title">
          Registros Recientes
          <span class="action-link" onclick="showView('registro')">Ver todo</span>
        </div>
        <div class="registro-item">
          <div class="registro-icon reg-icon-green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div class="registro-info">
            <div class="registro-title">Presente</div>
            <div class="registro-time">Hoy, 07:30 hs</div>
          </div>
          <span class="badge badge-green">A TIEMPO</span>
        </div>
        <div class="registro-item">
          <div class="registro-icon reg-icon-yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--yellow)" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          </div>
          <div class="registro-info">
            <div class="registro-title">Llegada Tarde</div>
            <div class="registro-time">Ayer, 07:45 hs</div>
          </div>
          <span class="badge badge-yellow">15 MIN</span>
        </div>
        <div class="registro-item">
          <div class="registro-icon reg-icon-green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div class="registro-info">
            <div class="registro-title">Presente</div>
            <div class="registro-time">02 Oct, 07:28 hs</div>
          </div>
          <span class="badge badge-green">A TIEMPO</span>
        </div>
      </div>
    </section>

    <!-- ══════════ REGISTRO ══════════ -->
    <section class="view" id="view-registro">
      <div class="eyebrow">Portal Familiar</div>
      <div class="page-title">Registro de Asistencia</div>
      <div class="page-subtitle">Historial detallado por día.</div>

      <!-- Alumno -->
      <div class="alumno-selector" style="margin-bottom:18px;" onclick="alert('Cambiar alumno')">
        <div class="alumno-avatar">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <div class="dot"></div>
        </div>
        <div class="alumno-info">
          <div class="alumno-name">Mateo García</div>
          <div class="alumno-course">4to Año – Electronica</div>
        </div>
        <div class="alumno-chevron">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>

      <!-- Stats bar -->
      <div class="grid-3" style="margin-bottom:18px;">
        <div class="card" style="padding:14px;text-align:center;">
          <div style="font-size:11px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Asistencia</div>
          <div style="font-size:22px;font-weight:800;color:var(--gray-800);">85%</div>
        </div>
        <div class="card" style="padding:14px;text-align:center;">
          <div style="font-size:11px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Faltas</div>
          <div style="font-size:22px;font-weight:800;color:var(--red);">4</div>
        </div>
        <div class="card" style="padding:14px;text-align:center;">
          <div style="font-size:11px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Tardes</div>
          <div style="font-size:22px;font-weight:800;color:var(--blue-btn);">2</div>
        </div>
      </div>

      <!-- Month nav -->
      <div class="month-nav">
        <button onclick="changeMonth(-1)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <span class="month-label" id="month-label">Junio 2026</span>
        <button onclick="changeMonth(1)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>

      <!-- Day rows -->
      <div id="day-rows"></div>

    </section>

    <!-- ══════════ MENSAJES ══════════ -->
    <section class="view" id="view-mensajes">
      <div class="eyebrow" style="margin-bottom:14px;">Centro de Comunicaciones</div>

      <div class="msg-layout">
        <!-- Lista -->
        <div class="msg-sidebar" id="msg-list-panel">
          <div class="msg-sidebar-header">
            <div class="msg-sidebar-title">Mensajes</div>
            <div class="msg-search-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" placeholder="Buscar preceptor.." oninput="filterMsgs(this.value)">
            </div>
          </div>
          <div id="msg-list"></div>
          <div class="msg-empty" id="msg-empty-list" style="display:none;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p>Centro de Comunicaciones</p>
          </div>
        </div>

        <!-- Chat -->
        <div class="msg-panel" id="msg-panel">
          <div class="msg-empty" id="chat-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p>Seleccioná una conversación</p>
          </div>
          <div id="chat-panel" style="display:none;flex:1;flex-direction:column;overflow:hidden;">
            <div class="chat-header">
              <div class="chat-header-left">
                <button class="chat-back" onclick="closeChatMobile()">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="chat-avatar">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                </div>
                <div>
                  <div class="chat-name" id="chat-name-head">—</div>
                  <div class="chat-status" id="chat-status-head">En línea ahora</div>
                </div>
              </div>
              <button class="chat-dots">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
              </button>
            </div>
            <div class="chat-body" id="chat-body"></div>
            <div class="chat-input-row">
              <button class="chat-attach" onclick="document.getElementById('chat-file').click()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
              </button>
              <input type="file" id="chat-file" style="display:none" onchange="attachFile(this)">
              <textarea class="chat-input" id="chat-input" rows="1" placeholder="Escribir mensaje..."
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg()}"
                oninput="autoGrow(this)"></textarea>
              <button class="chat-send" onclick="sendMsg()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ══════════ PERFIL ══════════ -->
    <section class="view" id="view-perfil">
      <div class="page-title">Mi Perfil</div>
      <div class="page-subtitle">Información del Portal Familiar</div>

      <div class="card" style="margin-bottom:16px;">
        <div class="profile-card-header">
          <div class="profile-avatar-wrap">
            <div class="profile-avatar">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div class="profile-edit-dot">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
          </div>
          <div>
            <div class="profile-badge-label">Tutor Responsable</div>
            <div class="profile-name"><?php echo e($apellido . ', ' . $nombre); ?></div>
            <div class="profile-dni">DNI: 28.455.912</div>
          </div>
        </div>
        <hr class="div">
        <div class="profile-row">
          <div class="profile-row-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div>
            <div class="profile-row-label">Correo Electrónico</div>
            <div class="profile-row-val">r.garcia@gmail.com</div>
          </div>
        </div>
        <div class="profile-row">
          <div class="profile-row-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.62 3.45 2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.98-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div>
            <div class="profile-row-label">Teléfono de Contacto</div>
            <div class="profile-row-val">+54 9 11 4455-8899</div>
          </div>
        </div>
        <div class="profile-row">
          <div class="profile-row-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div>
            <div class="profile-row-label">Domicilio Declarado</div>
            <div class="profile-row-val">Av. San Martín 450, Florida Este</div>
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom:16px;">
        <div class="section-title" style="margin-bottom:14px;">Alumno Asociado</div>
        <div class="alumno-assoc-card">
          <div class="alumno-assoc-avatar">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </div>
          <div style="flex:1;">
            <div class="alumno-assoc-name">García, Lucas Matías</div>
            <div class="alumno-assoc-course">6to 2da – Electronica</div>
          </div>
          <span class="badge badge-navy">REGULAR</span>
        </div>
        <div class="alumno-stats-row">
          <div class="alumno-stat-item">
            <div class="alumno-stat-icon icon-green">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
              <div class="alumno-stat-label">Asistencia</div>
              <div class="alumno-stat-val">94%</div>
            </div>
          </div>
          <div class="alumno-stat-item">
            <div class="alumno-stat-icon icon-blue">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div>
              <div class="alumno-stat-label">Faltas Totales</div>
              <div class="alumno-stat-val">8.50</div>
            </div>
          </div>
        </div>
      </div>

      <form method="POST" action="index.php" style="width:100%;">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="btn btn-danger btn-full">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Cerrar Sesión
        </button>
      </form>
    </section>

  </main>
</div>

<!-- BOTTOM NAV -->
<nav class="bottom-nav" id="bottom-nav">
  <button class="bottom-nav-item active" id="bnav-resumen" onclick="showView('resumen');syncBNav('resumen')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
    Resumen
  </button>
  <button class="bottom-nav-item" id="bnav-registro" onclick="showView('registro');syncBNav('registro')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    Registro
  </button>
  <button class="bottom-nav-item" id="bnav-mensajes" onclick="showView('mensajes');syncBNav('mensajes')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    Mensajes
  </button>
  <button class="bottom-nav-item" id="bnav-perfil" onclick="showView('perfil');syncBNav('perfil')">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
    Perfil
  </button>
</nav>

<script src="../public/assets/js/padretutor.js"></script>

</div>
