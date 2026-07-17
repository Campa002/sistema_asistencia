
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
const coursesData = [
  {
    id: 1,
    name: '4to 1ra',
    spec: 'Electrónica',
    turno: 'Turno Mañana',
    alumnos: 32,
    status: 'completa',
    updated: '08:45 AM'
  },
  {
    id: 2,
    name: '5to 2da',
    spec: 'Electrónica',
    turno: 'Turno Mañana',
    alumnos: 28,
    status: 'pendiente',
    updated: null
  },
  {
    id: 3,
    name: '6to 1ra',
    spec: 'Electricidad',
    turno: 'Turno Tarde',
    alumnos: 25,
    status: 'pendiente',
    updated: null,
    ingresa: '13:30 PM'
  },
  {
    id: 4,
    name: '4to 4ta',
    spec: 'Programación',
    turno: 'Turno Tarde',
    alumnos: 30,
    status: 'completa',
    updated: '14:45 PM'
  },
  {
    id: 5,
    name: '7mo 1ra',
    spec: 'Programación',
    turno: 'Turno Mañana',
    alumnos: 18,
    status: 'pendiente',
    updated: null
  },
  {
    id: 6,
    name: '5to 3ra',
    spec: 'Construcciones',
    turno: 'Turno Mañana',
    alumnos: 22,
    status: 'pendiente',
    updated: null
  }
];

const studentsData = [
  {
    last: 'ALONSO',
    first: 'Martin Ignacio',
    dni: '45.765.912',
    course: '5to 2da',
    estado: 'p'
  },
  {
    last: 'BENITEZ',
    first: 'Lucía Belén',
    dni: '45.090.203',
    course: '5to 2da',
    estado: 'a'
  },
  {
    last: 'CACERES',
    first: 'Julian',
    dni: '46.123.112',
    course: '5to 2da',
    estado: 't'
  },
  {
    last: 'DIAZ',
    first: 'Facundo Ezequiel',
    dni: '45.243.556',
    course: '5to 2da',
    estado: 'ra'
  },
  {
    last: 'FERNÁNDEZ',
    first: 'Lucía',
    dni: '49800700',
    course: '7mo 1ra',
    estado: 'p'
  },
  {
    last: 'GARCÍA',
    first: 'Alejandro',
    dni: '48555444',
    course: '7mo 1ra',
    estado: 'p'
  },
  {
    last: 'LÓPEZ',
    first: 'Facundo',
    dni: '50393939',
    course: '7mo 2da',
    estado: 'p'
  },
  {
    last: 'RODRÍGUEZ',
    first: 'Martina',
    dni: '46900800',
    course: '6to 1ra',
    estado: 'p'
  },
  {
    last: 'MARTINEZ',
    first: 'Santiago',
    dni: '47112233',
    course: '4to 1ra',
    estado: 'a'
  },
  {
    last: 'GOMEZ',
    first: 'Lautaro',
    dni: '46888777',
    course: '4to 1ra',
    estado: 'p'
  }
];

const histData = [
  {
    course: '7mo 1ra – Programación',
    date: '24 May, 2026',
    turno: 'Mañana (09:45 – 11:55)',
    presentes: 22,
    ausentes: 3,
    status: 'completa',
    modified: true
  },
  {
    course: '6to 2da – Electrónica',
    date: '24 May, 2026',
    turno: 'Mañana (09:45 – 11:55)',
    presentes: 18,
    ausentes: 7,
    status: 'completa',
    modified: false
  },
  {
    course: '5to 1ra – Programación',
    date: '24 May, 2026',
    turno: 'Mañana (07:35 – 09:35)',
    presentes: 25,
    ausentes: 1,
    status: 'completa',
    modified: false
  }
];

const msgsData = [
  {
    id: 1,
    from: 'Gomez, Ricardo (Padre)',
    alumno: 'Lautaro Gomez - 4to 1ra',
    preview: 'Buen día, quería justificar la inasistencia...',
    time: '10:45 AM',
    unread: 2,
    conversation: [
      {
        dir: 'in',
        text:
          'Buen día, quería justificar la inasistencia de Lautaro del día de hoy.',
        time: '10:45 AM'
      },
      {
        dir: 'out',
        text:
          'Buen día, Ricardo. Sí, tomamos nota. ¿Me podría enviar el certificado médico para adjuntarlo?',
        time: '10:52 AM'
      }
    ]
  },
  {
    id: 2,
    from: 'Perez, Maria (Madre)',
    alumno: 'Sofía Perez - 5to 2da',
    preview: 'Gracias por el aviso del examen.',
    time: 'Ayer',
    unread: 0,
    conversation: [
      {
        dir: 'in',
        text:
          'Gracias por el aviso del examen, muy amable.',
        time: 'Ayer'
      }
    ]
  },
  {
    id: 3,
    from: 'Lopez, Claudio (Tutor)',
    alumno: 'Kevin Lopez - 3ro 1ra',
    preview: '¿Podría confirmarme el horario?',
    time: 'Ayer',
    unread: 1,
    conversation: [
      {
        dir: 'in',
        text:
          '¿Podría confirmarme el horario de entrada de 3ro 1ra?',
        time: 'Ayer'
      }
    ]
  },
  {
    id: 4,
    from: 'Retegui Mateo (Alumno)',
    alumno: 'Mateo R. - 6to 1ra',
    preview: 'Perfecto, nos vemos en la reunión.',
    time: 'Ayer',
    unread: 0,
    conversation: [
      {
        dir: 'in',
        text:
          'Perfecto, nos vemos en la reunión. Muchas gracias.',
        time: 'Ayer'
      }
    ]
  }
];

let activeMsg = null;
let alumnosPage_ = 0;

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
            onclick="openTomarAsistencia('${course.name} – ${course.spec}')"
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
function openTomarAsistencia(courseName) {
  const courseElement = document.getElementById(
    'tomar-course'
  );

  if (courseElement) {
    courseElement.textContent =
      courseName;
  }

  attendData = studentsData.map(
    function (student) {
      return { ...student };
    }
  );

  renderAttendTable();
  showView('tomar');

  const nav = document.getElementById(
    'nav-asistencia'
  );

  if (nav) {
    nav.classList.add('active');
  }
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
  alert(
    '✅ Asistencia guardada correctamente.'
  );

  showView('asistencia');
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
              onclick="openTomarAsistencia('${history.course}')"
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
      const email =
        student.last.toLowerCase() +
        '.' +
        student.first
          .split(' ')[0]
          .toLowerCase() +
        '@gmail.com';

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

  message.conversation.push({
    dir: 'out',
    text: text,
    time: 'Ahora'
  });

  input.value = '';

  const body = document.getElementById(
    'chat-body'
  );

  if (!body) {
    return;
  }

  const div = document.createElement(
    'div'
  );

  const bubble =
    document.createElement('div');

  bubble.className =
    'bubble bubble-out';

  bubble.textContent = text;

  const time =
    document.createElement('div');

  time.className =
    'bubble-time';

  time.style.textAlign = 'right';
  time.textContent = 'Ahora';

  div.appendChild(bubble);
  div.appendChild(time);
  body.appendChild(div);

  body.scrollTop =
    body.scrollHeight;
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

