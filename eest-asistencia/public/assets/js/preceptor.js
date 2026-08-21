
// ─── SIDEBAR RESPONSIVE ───
function setPreceptorSidebarState(open) {
  const sidebar = document.getElementById('preceptor-sidebar');
  const overlay = document.getElementById('preceptor-sidebar-overlay');
  const toggle = document.getElementById('preceptor-menu-toggle');

  if (!sidebar || !overlay || !toggle) {
    return;
  }

  sidebar.classList.toggle('open', open);
  overlay.classList.toggle('open', open);

  toggle.setAttribute(
    'aria-expanded',
    open ? 'true' : 'false'
  );

  toggle.setAttribute(
    'aria-label',
    open ? 'Cerrar menú' : 'Abrir menú'
  );

  document.body.classList.toggle(
    'preceptor-menu-open',
    open
  );
}

function abrirSidebarPreceptor() {
  setPreceptorSidebarState(true);
}

function cerrarSidebarPreceptor() {
  setPreceptorSidebarState(false);
}

function toggleSidebarPreceptor() {
  const sidebar = document.getElementById(
    'preceptor-sidebar'
  );

  if (!sidebar) {
    return;
  }

  setPreceptorSidebarState(
    !sidebar.classList.contains('open')
  );
}

document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    cerrarSidebarPreceptor();
  }
});

window.addEventListener('resize', function () {
  if (window.innerWidth > 900) {
    cerrarSidebarPreceptor();
  }
});

// ─── DATOS ───
// Provistos por el servidor (ver preceptor.php -> window.SERVER_DATA),
// generados por PreceptorController::portalData() a partir de la BD real
// (preceptor_cursos, alumno_cursos, registros_asistencia, mensajes, etc).
const SD = window.SERVER_DATA || { cursos: [], alumnos: [], historial: [], msgs: [], bloques: {}, horariosPorCurso: {}, csrfToken: '' };

const coursesData = SD.cursos;
const studentsData = SD.alumnos;
const histData = SD.historial;
const msgsData = SD.msgs;
const bloquesInstitucionales = SD.bloques;
const horariosPorCurso = SD.horariosPorCurso || {};

let activeMsg = null;
let alumnosPage_ = 0;

// Estado de la pantalla "Tomar Asistencia": curso actualmente seleccionado y
// la opción de materia/horario real elegida en el select (ver
// renderModuloSelect/onTomarModuloChange). null mientras no hay nada elegido
// todavía o el día no tiene clases para ese curso.
let tomarCursoId = null;
let tomarOpciones = [];
let tomarOpcionElegida = null;

const PER_PAGE = 10;

let attendData = [];
let filteredAlumnos = [...studentsData];

// ─── NAVEGACIÓN ───
function showView(name) {
  cerrarSidebarPreceptor();

  document
    .querySelectorAll('.view')
    .forEach(function (view) {
      view.classList.remove('active');
    });

  document
    .querySelectorAll('.nav-item')
    .forEach(function (nav) {
      nav.classList.remove('active');
    });

  const view = document.getElementById(
    'view-' + name
  );

  if (!view) {
    console.error(
      'No existe la vista:',
      name
    );
    return;
  }

  view.classList.add('active');

  const navEl = document.getElementById(
    'nav-' + name
  );

  if (navEl) {
    navEl.classList.add('active');
  }

  if (name === 'asistencia') {
    renderCourses();
  }

  if (name === 'alumnos') {
    renderAlumnos();
  }

  if (name === 'historial') {
    renderHistorial();
  }

  if (name === 'mensajes') {
    renderMsgs();
  }

  const main = document.querySelector(
    '#preceptor-portal-root .main'
  );

  if (main) {
    main.scrollTo({
      top: 0,
      behavior: 'auto'
    });
  }
}

// ─── CURSOS ───
function renderCourses(filter = '') {
  const grid = document.getElementById(
    'courses-grid'
  );

  if (!grid) {
    return;
  }

  const normalizedFilter = filter
    .trim()
    .toLowerCase();

  const filtered = coursesData.filter(
    function (course) {
      const content = [
        course.name,
        course.spec,
        course.turno
      ]
        .join(' ')
        .toLowerCase();

      return content.includes(
        normalizedFilter
      );
    }
  );

  grid.innerHTML = filtered
    .map(function (course) {
      const isCompleta =
        course.status === 'completa';

      const badge = isCompleta
        ? '<span class="badge badge-green">● Completa</span>'
        : '<span class="badge badge-red">● Pendiente</span>';

      const actions = isCompleta
        ? `
          <span
            class="action-link"
            onclick="showView('historial')"
          >
            Editar Asistencia →
          </span>

          <span class="action-link">
            Ver Detalles →
          </span>
        `
        : `
          <button
            class="btn btn-dark btn-sm"
            onclick="openTomarAsistencia(${course.id})"
          >
            Tomar Asistencia
          </button>
        `;

      const footerLeft = isCompleta
        ? `
          <span class="course-updated">
            Actualizado: ${course.updated}
          </span>
        `
        : course.ingresa
          ? `
            <span class="course-updated">
              Ingresa ${course.ingresa}
            </span>
          `
          : `
            <span class="course-updated">
              Sin registro hoy
            </span>
          `;

      return `
        <div class="course-card">
          <div class="course-card-header">
            <div
              class="course-card-icon ${
                isCompleta ? '' : 'dark'
              }"
            >
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                />
                <circle cx="9" cy="7" r="4"/>
                <path
                  d="M23 21v-2a4 4 0 0 0-3-3.87"
                />
                <path
                  d="M16 3.13a4 4 0 0 1 0 7.75"
                />
              </svg>
            </div>

            ${badge}
          </div>

          <div class="course-name">
            ${course.name}
          </div>

          <div class="course-meta">
            ${course.turno} •
            ${course.alumnos} ALUMNOS
          </div>

          <div class="course-footer">
            ${footerLeft}

            <div class="course-actions">
              ${actions}
            </div>
          </div>
        </div>
      `;
    })
    .join('');
}

function filterCourses(value) {
  renderCourses(value);
}

// ─── TOMAR ASISTENCIA ───
function openTomarAsistencia(courseId) {
  const course = coursesData.find(
    function (c) { return c.id === courseId; }
  );

  const courseElement = document.getElementById(
    'tomar-course'
  );

  if (courseElement) {
    courseElement.textContent = course
      ? course.name + ' – ' + course.spec
      : '';
  }

  tomarCursoId = courseId;

  // Roster real del curso seleccionado (antes usaba siempre la misma
  // lista de ejemplo sin importar el curso).
  attendData = studentsData
    .filter(function (student) {
      return student.courseId === courseId;
    })
    .map(function (student) {
      return { ...student, estado: null };
    });

  const fechaInput = document.getElementById('tomar-fecha');
  if (fechaInput) fechaInput.value = fechaDeHoyArgentina();

  renderModuloSelect();
  renderAttendTable();
  showView('tomar');

  const nav = document.getElementById(
    'nav-asistencia'
  );

  if (nav) {
    nav.classList.add('active');
  }
}

// Fecha de hoy en formato YYYY-MM-DD. El guardado real solo acepta la fecha
// de hoy (ver PreceptorController::guardarAsistenciaAjax) — corregir fechas
// pasadas sigue siendo tarea de Admin → Historial.
function fechaDeHoyArgentina() {
  const hoy = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Argentina/Buenos_Aires' });
  return hoy;
}

function diaSemanaDeFecha(fechaStr) {
  // 1=Lunes ... 7=Domingo (igual convención que asignaciones_materias.dia_semana)
  const partes = fechaStr.split('-').map(Number);
  const fecha = new Date(partes[0], partes[1] - 1, partes[2]);
  const dow = fecha.getDay(); // 0=Domingo..6=Sábado
  return dow === 0 ? 7 : dow;
}

// Opciones reales de materia/horario para el curso y la fecha elegidos,
// tomadas de asignaciones_materias (vía SD.horariosPorCurso) — ya no se
// arma a partir de los bloques institucionales genéricos, para que la toma
// quede asociada a la materia realmente agendada ese día.
function renderModuloSelect() {
  const select = document.getElementById('tomar-modulo');
  if (!select) return;

  const fechaInput = document.getElementById('tomar-fecha');
  const fecha = fechaInput ? fechaInput.value : fechaDeHoyArgentina();
  const diaSemana = diaSemanaDeFecha(fecha);

  const horario = horariosPorCurso[tomarCursoId] || [];
  tomarOpciones = horario
    .filter(function (h) { return h.diaSemana === diaSemana; })
    .sort(function (a, b) { return a.horaInicio.localeCompare(b.horaInicio); });

  const guardarBtn = document.getElementById('btn-guardar-asistencia');

  if (tomarOpciones.length === 0) {
    select.innerHTML = '<option>Sin clases programadas para esta fecha</option>';
    tomarOpcionElegida = null;
    if (guardarBtn) guardarBtn.disabled = true;
    return;
  }

  select.innerHTML = tomarOpciones
    .map(function (h, i) {
      return `<option value="${i}">${h.materiaNombre} (${h.horaInicio} – ${h.horaFin})</option>`;
    })
    .join('');

  select.selectedIndex = 0;
  tomarOpcionElegida = tomarOpciones[0];
  if (guardarBtn) guardarBtn.disabled = false;
}

function onTomarModuloChange() {
  const select = document.getElementById('tomar-modulo');
  if (!select) return;
  const idx = parseInt(select.value, 10);
  tomarOpcionElegida = Number.isInteger(idx) ? tomarOpciones[idx] : null;
}

function onTomarFechaChange() {
  renderModuloSelect();
}

function renderAttendTable() {
  const tbody = document.getElementById(
    'attend-tbody'
  );

  if (!tbody) {
    return;
  }

  tbody.innerHTML = attendData
    .map(function (student, index) {
      return `
        <tr>
          <td>
            <div class="student-name">
              ${student.last},
              ${student.first}
            </div>

            <div class="student-dni">
              DNI: ${student.dni}
            </div>
          </td>

          <td>
            <div class="attend-btns">
              <button
                class="attend-btn ${
                  student.estado === 'p'
                    ? 'active-p'
                    : ''
                }"
                onclick="setEstado(${index}, 'p')"
              >
                P
              </button>

              <button
                class="attend-btn ${
                  student.estado === 'a'
                    ? 'active-a'
                    : ''
                }"
                onclick="setEstado(${index}, 'a')"
              >
                A
              </button>

              <button
                class="attend-btn ${
                  student.estado === 't'
                    ? 'active-t'
                    : ''
                }"
                onclick="setEstado(${index}, 't')"
              >
                T
              </button>

              <button
                class="attend-btn ${
                  student.estado === 'ra'
                    ? 'active-ra'
                    : ''
                }"
                onclick="setEstado(${index}, 'ra')"
              >
                RA
              </button>
            </div>
          </td>
        </tr>
      `;
    })
    .join('');

  updateCounts();
}

function setEstado(index, estado) {
  attendData[index].estado =
    attendData[index].estado === estado
      ? null
      : estado;

  renderAttendTable();
}

function updateCounts() {
  const presentCount = attendData.filter(
    function (student) {
      return student.estado === 'p';
    }
  ).length;

  const absentCount = attendData.filter(
    function (student) {
      return student.estado === 'a';
    }
  ).length;

  const lateCount = attendData.filter(
    function (student) {
      return student.estado === 't';
    }
  ).length;

  const earlyCount = attendData.filter(
    function (student) {
      return student.estado === 'ra';
    }
  ).length;

  const presentElement =
    document.getElementById('cnt-p');

  const absentElement =
    document.getElementById('cnt-a');

  const lateElement =
    document.getElementById('cnt-t');

  const earlyElement =
    document.getElementById('cnt-ra');

  if (presentElement) {
    presentElement.textContent =
      presentCount;
  }

  if (absentElement) {
    absentElement.textContent =
      absentCount;
  }

  if (lateElement) {
    lateElement.textContent =
      lateCount;
  }

  if (earlyElement) {
    earlyElement.textContent =
      earlyCount;
  }
}

function guardarAsistencia() {
  if (!tomarOpcionElegida) {
    alert('No hay ninguna materia agendada para este curso en la fecha elegida.');
    return;
  }

  if (attendData.length === 0) {
    alert('Este curso no tiene alumnos matriculados.');
    return;
  }

  const sinMarcar = attendData.filter(function (s) { return !s.estado; });
  if (sinMarcar.length > 0) {
    alert('Falta marcar el estado de ' + sinMarcar.length + ' alumno(s) antes de guardar.');
    return;
  }

  const fechaInput = document.getElementById('tomar-fecha');
  const fecha = fechaInput ? fechaInput.value : fechaDeHoyArgentina();

  const estados = {};
  attendData.forEach(function (s) { estados[s.id] = s.estado; });

  const btn = document.getElementById('btn-guardar-asistencia');
  if (btn) btn.disabled = true;

  const body = new URLSearchParams({
    curso_id: tomarCursoId,
    materia_id: tomarOpcionElegida.materiaId,
    dia_semana: diaSemanaDeFecha(fecha),
    fecha: fecha,
    csrf_token: SD.csrfToken
  });
  Object.keys(estados).forEach(function (alumnoId) {
    body.append('estados[' + alumnoId + ']', estados[alumnoId]);
  });

  fetch('index.php?page=preceptor/guardar_asistencia_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json().then(function (data) { return { status: r.status, data: data }; }); })
    .then(function (res) {
      if (btn) btn.disabled = false;
      if (!res.data.ok) {
        alert(res.data.error || 'No se pudo guardar la asistencia.');
        return;
      }
      const accionTexto = res.data.accion === 'creado' ? 'registrada' : 'actualizada';
      alert(
        'Asistencia ' + accionTexto + ' correctamente: ' +
        res.data.presentes + ' de ' + res.data.total + ' alumnos presentes.'
      );
      showView('asistencia');
    })
    .catch(function () {
      if (btn) btn.disabled = false;
      alert('No se pudo guardar la asistencia. Verificá tu conexión.');
    });
}

// ─── HISTORIAL ───
function renderHistorial() {
  const list = document.getElementById(
    'hist-list'
  );

  if (!list) {
    return;
  }

  list.innerHTML = histData
    .map(function (history) {
      return `
        <div class="hist-card">
          <div class="hist-card-header">
            <div>
              <div class="hist-course">
                ${history.course}
              </div>

              <div class="hist-date">
                📅 ${history.date}
                | Turno:
                ${history.turno}
              </div>
            </div>

            <div class="hist-badges">
              <span class="badge badge-green">
                Completa
              </span>

              ${
                history.modified
                  ? `
                    <span class="badge badge-blue">
                      Modificada
                    </span>
                  `
                  : ''
              }
            </div>
          </div>

          <div class="hist-stats-row">
            <div class="hist-stat-item">
              <div class="lbl">
                Presentes
              </div>

              <div class="val green">
                ${history.presentes}
              </div>
            </div>

            <div class="hist-stat-item">
              <div class="lbl">
                Ausentes
              </div>

              <div class="val red">
                ${history.ausentes}
              </div>
            </div>
          </div>

          <div class="hist-card-footer">
            <span class="action-link">
              Ver Detalle
            </span>

            <span
              class="action-link"
              onclick="openTomarAsistencia(${history.cursoId})"
            >
              Editar
            </span>

            <span class="action-link">
              Detalles
            </span>
          </div>
        </div>
      `;
    })
    .join('');
}

function filterChip(element) {
  document
    .querySelectorAll('.filter-chip')
    .forEach(function (chip) {
      chip.classList.remove('active');
    });

  element.classList.add('active');
}

// ─── ALUMNOS ───
function renderAlumnos() {
  const tbody = document.getElementById(
    'alumnos-tbody'
  );

  if (!tbody) {
    return;
  }

  const start =
    alumnosPage_ * PER_PAGE;

  const currentPage = filteredAlumnos.slice(
    start,
    start + PER_PAGE
  );

  tbody.innerHTML = currentPage
    .map(function (student) {
      const email = student.email || '—';

      return `
        <tr>
          <td>
            <div class="student-row">
              <div class="avatar-circle">
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <circle cx="12" cy="8" r="4"/>
                  <path
                    d="M4 20c0-4 3.6-7 8-7s8 3 8 7"
                  />
                </svg>
              </div>

              <div>
                <div
                  style="
                    font-weight: 700;
                    color: var(--gray-800);
                  "
                >
                  ${student.last},
                  ${student.first}
                </div>

                <div
                  style="
                    font-size: 11px;
                    color: var(--gray-400);
                  "
                >
                  ${email}
                </div>
              </div>
            </div>
          </td>

          <td>${student.dni}</td>
          <td>${student.course}</td>

          <td>
            <span class="badge badge-blue">
              Activo
            </span>
          </td>
        </tr>
      `;
    })
    .join('');

  const count = document.getElementById(
    'alumnos-count'
  );

  if (count) {
    count.textContent =
      `Mostrando ${currentPage.length} ` +
      `de ${filteredAlumnos.length} alumnos`;
  }
}

function filterAlumnos(query) {
  const normalized =
    query.toLowerCase();

  filteredAlumnos = studentsData.filter(
    function (student) {
      return (
        student.last +
        ' ' +
        student.first +
        ' ' +
        student.dni
      )
        .toLowerCase()
        .includes(normalized);
    }
  );

  alumnosPage_ = 0;
  renderAlumnos();
}

function filterByDivision(
  division,
  element
) {
  document
    .querySelectorAll('.chip-btn')
    .forEach(function (button) {
      button.classList.remove('active');
    });

  element.classList.add('active');

  filteredAlumnos =
    division === 'todos'
      ? [...studentsData]
      : studentsData.filter(
          function (student) {
            return (
              student.course === division
            );
          }
        );

  alumnosPage_ = 0;
  renderAlumnos();
}

function alumnosPage(direction) {
  const maxPage = Math.max(
    0,
    Math.ceil(
      filteredAlumnos.length / PER_PAGE
    ) - 1
  );

  alumnosPage_ = Math.max(
    0,
    Math.min(
      maxPage,
      alumnosPage_ + direction
    )
  );

  renderAlumnos();
}

// ─── MENSAJES ───
function renderMsgs() {
  const list = document.getElementById(
    'msg-list'
  );

  if (!list) {
    return;
  }

  list.innerHTML = msgsData
    .map(function (message) {
      return `
        <div
          class="msg-item ${
            activeMsg === message.id
              ? 'active'
              : ''
          }"
          onclick="openMsg(${message.id})"
        >
          <div class="msg-avatar">
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="12" cy="8" r="4"/>
              <path
                d="M4 20c0-4 3.6-7 8-7s8 3 8 7"
              />
            </svg>
          </div>

          <div class="msg-info">
            <div class="msg-from">
              ${message.from}
            </div>

            <div class="msg-alumno">
              Alumno:
              ${message.alumno}
            </div>

            <div class="msg-preview">
              ${message.preview}
            </div>
          </div>

          <div class="msg-meta">
            <span class="msg-time">
              ${message.time}
            </span>

            ${
              message.unread
                ? `
                  <span class="msg-unread">
                    ${message.unread}
                  </span>
                `
                : ''
            }
          </div>
        </div>
      `;
    })
    .join('');
}

function openMsg(id) {
  activeMsg = id;

  const message = msgsData.find(
    function (item) {
      return item.id === id;
    }
  );

  if (!message) {
    return;
  }

  message.unread = 0;

  const noSelection =
    document.getElementById(
      'msg-no-sel'
    );

  if (noSelection) {
    noSelection.style.display = 'none';
  }

  const chat = document.getElementById(
    'msg-chat'
  );

  if (!chat) {
    return;
  }

  chat.style.display = 'flex';

  const from = document.getElementById(
    'chat-from'
  );

  const sub = document.getElementById(
    'chat-sub'
  );

  if (from) {
    from.textContent =
      message.from;
  }

  if (sub) {
    sub.textContent =
      'Alumno: ' + message.alumno;
  }

  const body = document.getElementById(
    'chat-body'
  );

  if (body) {
    body.innerHTML = message.conversation
      .map(function (conversation) {
        return `
          <div>
            <div
              class="
                bubble
                bubble-${conversation.dir}
              "
            >
              ${conversation.text}
            </div>

            <div
              class="bubble-time"
              style="
                text-align:
                ${
                  conversation.dir === 'out'
                    ? 'right'
                    : 'left'
                };
              "
            >
              ${conversation.time}
            </div>
          </div>
        `;
      })
      .join('');

    body.scrollTop =
      body.scrollHeight;
  }

  renderMsgs();
}

function sendMsg() {
  if (!activeMsg) {
    return;
  }

  const input = document.getElementById(
    'chat-input'
  );

  if (!input) {
    return;
  }

  const text = input.value.trim();

  if (!text) {
    return;
  }

  const message = msgsData.find(
    function (item) {
      return item.id === activeMsg;
    }
  );

  if (!message) {
    return;
  }

  input.value = '';
  input.disabled = true;

  const endpoint = (window.SERVER_DATA && window.SERVER_DATA.enviarMensajeEndpoint) ||
    'index.php?page=preceptor/enviar_mensaje_ajax';

  fetch(endpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      conversacion_id: activeMsg,
      contenido: text,
      csrf_token: SD.csrfToken
    })
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      input.disabled = false;
      if (!data.ok) {
        alert(data.error || 'No se pudo enviar el mensaje.');
        input.value = text;
        return;
      }

      message.conversation.push({
        dir: 'out',
        text: text,
        time: data.hora || 'Ahora'
      });
      message.preview = text;
      message.time = data.hora || 'Ahora';

      const body = document.getElementById('chat-body');
      if (body) {
        const div = document.createElement('div');
        const bubble = document.createElement('div');
        bubble.className = 'bubble bubble-out';
        bubble.textContent = text;
        const time = document.createElement('div');
        time.className = 'bubble-time';
        time.style.textAlign = 'right';
        time.textContent = data.hora || 'Ahora';
        div.appendChild(bubble);
        div.appendChild(time);
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
      }
    })
    .catch(function () {
      input.disabled = false;
      alert('No se pudo enviar el mensaje. Verificá tu conexión.');
      input.value = text;
    });
}

function newMsg() {
  const to = prompt(
    'Destinatario ' +
    '(nombre del padre, tutor o alumno):'
  );

  if (to) {
    alert(
      'Función: redactar mensaje para ' +
      to
    );
  }
}

// ─── INICIALIZACIÓN ───
document.addEventListener(
  'DOMContentLoaded',
  function () {
    showView('dashboard');
  }
);

