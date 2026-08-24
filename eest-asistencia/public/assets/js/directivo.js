
// ─── SISTEMA DE NOTIFICACIONES (TOASTS) ───
// Reemplaza alert()/confirm() del navegador ("localhost dice") por un
// sistema visual propio, mismo criterio que ya usa el portal de Preceptor
// (ver #toast-container en directivo.php y los estilos .toast en
// directivo.css) — no se comparte el archivo, pero sí el mismo patrón.
function showToast(message, type) {
  type = type || 'info';
  var container = document.getElementById('toast-container');
  if (!container) {
    console.warn('Toast:', type, message);
    return;
  }

  var iconos = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };

  var toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  toast.innerHTML =
    '<span class="toast-icon">' + (iconos[type] || iconos.info) + '</span>' +
    '<span class="toast-msg"></span>' +
    '<button type="button" class="toast-close" aria-label="Cerrar">&times;</button>';
  toast.querySelector('.toast-msg').textContent = message;

  function cerrar() {
    toast.classList.remove('toast-in');
    toast.classList.add('toast-out');
    setTimeout(function () { toast.remove(); }, 200);
  }

  toast.querySelector('.toast-close').addEventListener('click', cerrar);
  container.appendChild(toast);
  requestAnimationFrame(function () { toast.classList.add('toast-in'); });
  setTimeout(cerrar, 4500);
}

// Modal de confirmación propio (reemplaza window.confirm() del navegador),
// reutilizando las mismas clases .modal-overlay-directivo/.modal-directivo
// que ya usan los modales de Solicitudes/Reemplazos/Perfil — no se agrega
// CSS nuevo.
function mostrarConfirmacionDirectivo(titulo, texto, alConfirmar) {
  var existente = document.getElementById('confirm-overlay-directivo');
  if (existente) existente.remove();

  var overlay = document.createElement('div');
  overlay.className = 'modal-overlay-directivo';
  overlay.id = 'confirm-overlay-directivo';
  overlay.style.display = 'flex';
  overlay.innerHTML =
    '<div class="modal-directivo" style="max-width:360px;text-align:center;">' +
    '<div class="modal-directivo__titulo"></div>' +
    '<p style="color:var(--gris-texto);font-size:13.5px;margin:0 0 18px;"></p>' +
    '<div class="modal-directivo__acciones" style="justify-content:center;">' +
    '<button type="button" class="boton-secundario">Cancelar</button>' +
    '<button type="button" class="boton-primario" style="background:var(--rojo)">Confirmar</button>' +
    '</div></div>';
  overlay.querySelector('.modal-directivo__titulo').textContent = titulo;
  overlay.querySelector('p').textContent = texto;

  function cerrar() { overlay.remove(); }
  overlay.querySelector('.boton-secundario').addEventListener('click', cerrar);
  overlay.addEventListener('click', function (e) { if (e.target === overlay) cerrar(); });
  overlay.querySelector('.boton-primario').addEventListener('click', function () {
    cerrar();
    alConfirmar();
  });

  document.body.appendChild(overlay);
}

// Gráfico de asistencia
var ctxGrafico = document
  .getElementById('grafico-asistencia')
  .getContext('2d');

// Presentes/ausentes reales por curso, inyectados por directivo.php en
// window.SERVER_DATA (antes eran arrays de ejemplo hardcodeados).
var SD = window.SERVER_DATA || { etiquetas: [], presentesManana: [], ausentesManana: [], presentesTarde: [], ausentesTarde: [] };
var etiquetas = SD.etiquetas;
var datosManana = SD.presentesManana;
var datosTarde = SD.presentesTarde;
var ausentesManana = SD.ausentesManana;
var ausentesTarde = SD.ausentesTarde;

var grafico = new Chart(ctxGrafico, {
  type: 'bar',

  data: {
    labels: etiquetas,

    datasets: [
      {
        label: 'Presentes',
        data: datosManana,
        backgroundColor: '#006397',
        borderRadius: 4
      },
      {
        label: 'Ausentes',
        data: ausentesManana,
        backgroundColor: '#c5dce8',
        borderRadius: 4
      }
    ]
  },

  options: {
    responsive: true,

    plugins: {
      legend: {
        display: false
      }
    },

    scales: {
      x: {
        stacked: true,

        grid: {
          display: false
        },

        ticks: {
          font: {
            size: 11
          }
        }
      },

      y: {
        stacked: true,

        grid: {
          color: '#f0f0f0'
        },

        ticks: {
          font: {
            size: 11
          }
        }
      }
    }
  }
});

function cambiarTurnoGrafico(turno, boton) {
  document
    .querySelectorAll('.selector-turno__opcion')
    .forEach(function (item) {
      item.classList.remove(
        'selector-turno__opcion--activo'
      );
    });

  boton.classList.add(
    'selector-turno__opcion--activo'
  );

  var esManana = turno === 'manana';

  grafico.data.datasets[0].data =
    esManana ? datosManana : datosTarde;
  grafico.data.datasets[1].data =
    esManana ? ausentesManana : ausentesTarde;

  grafico.update();
}

// Navegación entre secciones
function mostrarSeccion(idSeccion, botonNav) {
  document
    .querySelectorAll('.seccion')
    .forEach(function (seccion) {
      seccion.classList.remove(
        'seccion--activa'
      );
    });

  var seccionActiva = document.getElementById(
    'seccion-' + idSeccion
  );

  if (seccionActiva) {
    seccionActiva.classList.add(
      'seccion--activa'
    );
  }

  document
    .querySelectorAll('.nav__item')
    .forEach(function (boton) {
      boton.classList.remove(
        'nav__item--activo'
      );
    });

  if (botonNav) {
    botonNav.classList.add(
      'nav__item--activo'
    );
  }

  var titulos = {
    dashboard: 'Sistema de Asistencia',
    solicitudes: 'Solicitudes de Acceso',
    reemplazos: 'Sistema de Asistencia',
    asistencia: 'Asistencia Institucional',
    notificaciones: 'Notificaciones',
    perfil: 'Sistema de Asistencia'
  };

  var tituloBarra = document.getElementById(
    'titulo-barra-superior'
  );

  if (tituloBarra) {
    tituloBarra.textContent =
      titulos[idSeccion] ||
      'Sistema de Asistencia';
  }

  // Recordar la sección activa: si alguna acción necesita recargar la
  // página completa, se restaura acá mismo en vez de volver siempre al
  // Dashboard (ver bloque de restauración al final del archivo).
  try { sessionStorage.setItem('directivoSeccionActiva', idSeccion); } catch (e) {}

  cerrarSidebar();
}

// Orden real de las secciones en el sidebar (mismo orden que .nav__item en
// el HTML) — se usa solo para restaurar la sección activa tras un reload.
var ORDEN_SECCIONES = ['dashboard', 'solicitudes', 'reemplazos', 'asistencia', 'notificaciones', 'perfil'];

function restaurarSeccionActiva() {
  var guardada = null;
  try { guardada = sessionStorage.getItem('directivoSeccionActiva'); } catch (e) {}
  if (!guardada || guardada === 'dashboard') return;
  var idx = ORDEN_SECCIONES.indexOf(guardada);
  var botones = document.querySelectorAll('.nav__item');
  mostrarSeccion(guardada, idx >= 0 ? botones[idx] : null);
}

// Filtros de solicitudes
function filtrarSolicitudes(boton) {
  document
    .querySelectorAll('.tab-btn')
    .forEach(function (item) {
      item.classList.remove(
        'tab-btn--activo'
      );
    });

  boton.classList.add(
    'tab-btn--activo'
  );
}

// Filtros de notificaciones
function filtrarNotificaciones(boton) {
  document
    .querySelectorAll('.tab-notif')
    .forEach(function (item) {
      item.classList.remove(
        'tab-notif--activo'
      );
    });

  boton.classList.add(
    'tab-notif--activo'
  );
}

// Sidebar responsive
function abrirSidebar() {
  var sidebar = document.getElementById(
    'barra-lateral'
  );

  var overlay = document.getElementById(
    'overlay-sidebar'
  );

  if (sidebar) {
    sidebar.classList.add(
      'barra-lateral--visible'
    );
  }

  if (overlay) {
    overlay.classList.add(
      'overlay-sidebar--visible'
    );
  }
}

function cerrarSidebar() {
  var sidebar = document.getElementById(
    'barra-lateral'
  );

  var overlay = document.getElementById(
    'overlay-sidebar'
  );

  if (sidebar) {
    sidebar.classList.remove(
      'barra-lateral--visible'
    );
  }

  if (overlay) {
    overlay.classList.remove(
      'overlay-sidebar--visible'
    );
  }
}

// Cerrar filtros activos
document
  .querySelectorAll('.filtro-activo__cerrar')
  .forEach(function (boton) {
    boton.addEventListener(
      'click',
      function () {
        var filtro = this.closest(
          '.filtro-activo'
        );

        if (filtro) {
          filtro.style.display = 'none';
        }
      }
    );
  });

// Cierre de sesión real por POST
document
  .querySelectorAll('.js-logout-form')
  .forEach(function (formulario) {
    formulario.addEventListener(
      'submit',
      function (event) {
        // Reemplaza window.confirm() ("localhost dice") por un modal propio,
        // reutilizando las mismas clases .modal-overlay-directivo/.modal-directivo
        // que ya usan los otros modales de este módulo.
        event.preventDefault();
        mostrarConfirmacionDirectivo(
          'Cerrar sesión',
          '¿Querés cerrar la sesión actual?',
          function () {
            var botones = formulario.querySelectorAll('button[type="submit"]');
            botones.forEach(function (boton) {
              boton.disabled = true;
              boton.setAttribute('aria-busy', 'true');
            });
            formulario.submit();
          }
        );
      }
    );
  });

// Cerrar sidebar con Escape
document.addEventListener(
  'keydown',
  function (event) {
    if (
      event.key === 'Escape' &&
      typeof cerrarSidebar === 'function'
    ) {
      cerrarSidebar();
    }
  }
);

// ─── SOLICITUDES DE ACCESO (aprobar / rechazar reales) ───
function aprobarSolicitud(solicitudId) {
  var body = new URLSearchParams({ solicitud_id: solicitudId, csrf_token: SD.csrfToken });
  fetch('index.php?page=directivo/aprobar_solicitud_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) {
        showToast(data.error || 'No se pudo aprobar la solicitud.', 'error');
        return;
      }
      aplicarResolucionSolicitudEnDOM(solicitudId, 'Aprobada', 'completo');
      showToast('Solicitud aprobada correctamente.', 'success');
    })
    .catch(function () {
      showToast('No se pudo aprobar la solicitud. Verificá tu conexión.', 'error');
    });
}

// Actualiza la fila de una solicitud sin recargar la página: se queda en
// Solicitudes, la fila pasa a mostrar el estado resuelto real.
function aplicarResolucionSolicitudEnDOM(solicitudId, etiqueta, claseIndicador) {
  var celdaEstado = document.getElementById('solicitud-estado-' + solicitudId);
  if (celdaEstado) {
    celdaEstado.innerHTML = '<span class="indicador-estado indicador-estado--' + claseIndicador + '">' + etiqueta + '</span>';
  }
  var celdaAcciones = document.getElementById('solicitud-acciones-' + solicitudId);
  if (celdaAcciones) {
    celdaAcciones.innerHTML = '<span style="font-size:12px;color:var(--gris-texto)">' + etiqueta + ' — recién ahora</span>';
  }
}

var solicitudRechazoActual = null;

function abrirModalRechazo(solicitudId) {
  solicitudRechazoActual = solicitudId;
  var textarea = document.getElementById('rechazo-motivo');
  if (textarea) textarea.value = '';
  var overlay = document.getElementById('modal-rechazo-overlay');
  if (overlay) overlay.style.display = 'flex';
}

function cerrarModalRechazo() {
  solicitudRechazoActual = null;
  var overlay = document.getElementById('modal-rechazo-overlay');
  if (overlay) overlay.style.display = 'none';
}

function confirmarRechazoSolicitud() {
  if (!solicitudRechazoActual) return;
  var textarea = document.getElementById('rechazo-motivo');
  var motivo = textarea ? textarea.value.trim() : '';
  if (motivo === '') {
    showToast('Ingresá un motivo de rechazo.', 'warning');
    return;
  }

  var idResuelto = solicitudRechazoActual;
  var body = new URLSearchParams({ solicitud_id: idResuelto, motivo: motivo, csrf_token: SD.csrfToken });
  fetch('index.php?page=directivo/rechazar_solicitud_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) {
        showToast(data.error || 'No se pudo rechazar la solicitud.', 'error');
        return;
      }
      cerrarModalRechazo();
      aplicarResolucionSolicitudEnDOM(idResuelto, 'Rechazada', 'sin-asignar');
      showToast('Solicitud rechazada.', 'success');
    })
    .catch(function () {
      showToast('No se pudo rechazar la solicitud. Verificá tu conexión.', 'error');
    });
}

// ─── REEMPLAZOS ───
var ETIQUETAS_ESTADO_REEMPLAZO = { sin_asignar: 'Sin Asignar', asignado: 'Asignado', realizado: 'Realizado', cancelado: 'Cancelado' };
var CLASES_ESTADO_REEMPLAZO = { sin_asignar: 'sin-asignar', asignado: 'asignado', realizado: 'completo', cancelado: 'sin-asignar' };

function actualizarFilaReemplazoEnDOM(reemplazoId, estado, textoAcciones) {
  var celdaEstado = document.getElementById('reemplazo-estado-' + reemplazoId);
  if (celdaEstado) {
    celdaEstado.innerHTML = '<span class="indicador-estado indicador-estado--' + CLASES_ESTADO_REEMPLAZO[estado] + '">' + ETIQUETAS_ESTADO_REEMPLAZO[estado] + '</span>';
  }
  var celdaAcciones = document.getElementById('reemplazo-acciones-' + reemplazoId);
  if (celdaAcciones) celdaAcciones.innerHTML = textoAcciones;
}

var reemplazoAsignarActual = null;

function abrirModalAsignar(reemplazoId) {
  reemplazoAsignarActual = reemplazoId;
  var select = document.getElementById('asignar-preceptor-select');
  if (select) select.innerHTML = '<option value="">Cargando...</option>';
  var overlay = document.getElementById('modal-asignar-overlay');
  if (overlay) overlay.style.display = 'flex';

  fetch('index.php?page=directivo/preceptores_disponibles_ajax&reemplazo_id=' + encodeURIComponent(reemplazoId))
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!select) return;
      if (!data.ok) {
        select.innerHTML = '<option value="">No se pudieron cargar los preceptores</option>';
        return;
      }
      if (data.preceptores.length === 0) {
        select.innerHTML = '<option value="">No hay preceptores disponibles para ese horario</option>';
        return;
      }
      select.innerHTML = data.preceptores
        .map(function (p) { return '<option value="' + p.id + '">' + p.apellido + ', ' + p.nombre + '</option>'; })
        .join('');
    })
    .catch(function () {
      if (select) select.innerHTML = '<option value="">Error de conexión</option>';
    });
}

function cerrarModalAsignar() {
  reemplazoAsignarActual = null;
  var overlay = document.getElementById('modal-asignar-overlay');
  if (overlay) overlay.style.display = 'none';
}

function confirmarAsignarReemplazo() {
  if (!reemplazoAsignarActual) return;
  var select = document.getElementById('asignar-preceptor-select');
  var preceptorId = select ? select.value : '';
  if (!preceptorId) {
    showToast('Elegí un preceptor.', 'warning');
    return;
  }
  var nombrePreceptor = select.options[select.selectedIndex].textContent;

  var btn = document.getElementById('btn-confirmar-asignar');
  if (btn) btn.disabled = true;
  var idReemplazo = reemplazoAsignarActual;

  var body = new URLSearchParams({ reemplazo_id: idReemplazo, preceptor_id: preceptorId, csrf_token: SD.csrfToken });
  fetch('index.php?page=directivo/asignar_reemplazo_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (btn) btn.disabled = false;
      if (!data.ok) {
        showToast(data.error || 'No se pudo asignar el reemplazo.', 'error');
        return;
      }
      cerrarModalAsignar();
      var acciones = '<div style="font-size:12px;color:var(--gris-texto); margin-bottom:6px;">Cubre: ' + nombrePreceptor + '</div>'
        + '<button class="btn-accion btn-accion--rechazar" title="Cancelar reemplazo" onclick="cancelarReemplazo(' + idReemplazo + ')">'
        + '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></button>';
      actualizarFilaReemplazoEnDOM(idReemplazo, 'asignado', acciones);
      showToast('Reemplazo asignado correctamente.', 'success');
    })
    .catch(function () {
      if (btn) btn.disabled = false;
      showToast('No se pudo asignar el reemplazo. Verificá tu conexión.', 'error');
    });
}

function marcarRealizadoReemplazo(reemplazoId) {
  var body = new URLSearchParams({ reemplazo_id: reemplazoId, csrf_token: SD.csrfToken });
  fetch('index.php?page=directivo/marcar_realizado_reemplazo_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) {
        showToast(data.error || 'No se pudo marcar como realizado.', 'error');
        return;
      }
      actualizarFilaReemplazoEnDOM(reemplazoId, 'realizado', '<span style="font-size:12px;color:var(--gris-texto)">—</span>');
      showToast('Reemplazo marcado como realizado.', 'success');
    })
    .catch(function () {
      showToast('No se pudo marcar como realizado. Verificá tu conexión.', 'error');
    });
}

function cancelarReemplazo(reemplazoId) {
  var body = new URLSearchParams({ reemplazo_id: reemplazoId, csrf_token: SD.csrfToken });
  fetch('index.php?page=directivo/cancelar_reemplazo_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) {
        showToast(data.error || 'No se pudo cancelar el reemplazo.', 'error');
        return;
      }
      actualizarFilaReemplazoEnDOM(reemplazoId, 'cancelado', '<span style="font-size:12px;color:var(--gris-texto)">—</span>');
      showToast('Reemplazo cancelado.', 'success');
    })
    .catch(function () {
      showToast('No se pudo cancelar el reemplazo. Verificá tu conexión.', 'error');
    });
}

// ─── FILTRO DE REEMPLAZOS POR TURNO (cliente, sobre filas ya renderizadas) ───
function filtrarReemplazosPorTurno(selectEl) {
  var turno = (selectEl.value || '').toLowerCase();
  var filas = document.querySelectorAll('#seccion-reemplazos tbody tr[data-turno]');
  filas.forEach(function (fila) {
    var mostrar = !turno || (fila.getAttribute('data-turno') || '').toLowerCase() === turno;
    fila.style.display = mostrar ? '' : 'none';
  });
}

// ─── FILTRO DE ASISTENCIA INSTITUCIONAL (real, vía AJAX) ───
function actualizarAsistenciaInstitucional() {
  var anio = document.getElementById('filtro-asistencia-anio').value;
  var division = document.getElementById('filtro-asistencia-division').value;
  var fecha = document.getElementById('filtro-asistencia-fecha').value;
  var body = new URLSearchParams({ anio: anio, division: division, fecha: fecha, csrf_token: SD.csrfToken });

  fetch('index.php?page=directivo/filtrar_asistencia_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) {
        showToast(data.error || 'No se pudo actualizar la asistencia institucional.', 'error');
        return;
      }
      document.getElementById('asistencia-institucional-tbody').innerHTML = data.html;
      showToast('Asistencia institucional actualizada (' + data.total + ' registros).', 'success');
    })
    .catch(function () {
      showToast('No se pudo actualizar. Verificá tu conexión.', 'error');
    });
}

// ─── NUEVO REEMPLAZO (crear real) ───
function abrirModalNuevoReemplazo() {
  var fecha = document.getElementById('nr-fecha');
  var curso = document.getElementById('nr-curso');
  var motivo = document.getElementById('nr-motivo');
  if (fecha) fecha.value = fechaDeHoyDirectivo();
  if (curso) curso.value = '';
  if (motivo) motivo.value = '';
  resetearSelectsNuevoReemplazo('Elegí primero fecha y curso...');
  var overlay = document.getElementById('modal-nuevo-reemplazo-overlay');
  if (overlay) overlay.style.display = 'flex';
}

function cerrarModalNuevoReemplazo() {
  var overlay = document.getElementById('modal-nuevo-reemplazo-overlay');
  if (overlay) overlay.style.display = 'none';
}

function fechaDeHoyDirectivo() {
  return new Date().toLocaleDateString('en-CA', { timeZone: 'America/Argentina/Buenos_Aires' });
}

function resetearSelectsNuevoReemplazo(mensaje) {
  var materia = document.getElementById('nr-materia');
  var preceptor = document.getElementById('nr-preceptor');
  if (materia) materia.innerHTML = '<option value="">' + mensaje + '</option>';
  if (preceptor) preceptor.innerHTML = '<option value="">' + mensaje + '</option>';
}

// Se llama cuando cambian fecha o curso: recarga materias/preceptores REALES
// de ese curso para ese día (asignaciones_materias / preceptor_cursos) —
// nunca se deja tipear un horario o preceptor a mano.
function onCambioFechaOCursoNuevoReemplazo() {
  var fecha = document.getElementById('nr-fecha').value;
  var cursoId = document.getElementById('nr-curso').value;
  if (!fecha || !cursoId) {
    resetearSelectsNuevoReemplazo('Elegí primero fecha y curso...');
    return;
  }
  resetearSelectsNuevoReemplazo('Cargando...');

  fetch('index.php?page=directivo/materias_de_curso_fecha_ajax&curso_id=' + encodeURIComponent(cursoId) + '&fecha=' + encodeURIComponent(fecha))
    .then(function (r) { return r.json(); })
    .then(function (data) {
      var materia = document.getElementById('nr-materia');
      var preceptor = document.getElementById('nr-preceptor');
      if (!data.ok) {
        if (materia) materia.innerHTML = '<option value="">' + (data.error || 'Error') + '</option>';
        return;
      }
      if (materia) {
        materia.innerHTML = data.materias.length
          ? data.materias.map(function (m) { return '<option value="' + m.materiaId + '">' + m.materiaNombre + ' (' + m.horaInicio + ' – ' + m.horaFin + ')</option>'; }).join('')
          : '<option value="">Ese curso no tiene clases agendadas ese día</option>';
      }
      if (preceptor) {
        preceptor.innerHTML = data.preceptores.length
          ? data.preceptores.map(function (p) { return '<option value="' + p.id + '">' + p.apellido + ', ' + p.nombre + (p.esTitular ? ' (titular)' : ' (taller)') + '</option>'; }).join('')
          : '<option value="">Ese curso no tiene preceptor asignado</option>';
      }
    })
    .catch(function () {
      resetearSelectsNuevoReemplazo('Error de conexión');
    });
}

function confirmarNuevoReemplazo() {
  var fecha = document.getElementById('nr-fecha').value;
  var cursoId = document.getElementById('nr-curso').value;
  var materiaId = document.getElementById('nr-materia').value;
  var preceptorId = document.getElementById('nr-preceptor').value;
  var prioridad = document.getElementById('nr-prioridad').value;
  var motivo = document.getElementById('nr-motivo').value.trim();

  if (!fecha || !cursoId) { showToast('Elegí fecha y curso.', 'warning'); return; }
  if (!materiaId) { showToast('Elegí una materia agendada para ese curso ese día.', 'warning'); return; }
  if (!preceptorId) { showToast('Elegí el preceptor titular.', 'warning'); return; }
  if (!motivo) { showToast('Ingresá un motivo.', 'warning'); return; }

  var btn = document.getElementById('btn-crear-reemplazo');
  if (btn) btn.disabled = true;

  var body = new URLSearchParams({
    fecha: fecha, curso_id: cursoId, materia_id: materiaId,
    preceptor_titular_id: preceptorId, prioridad: prioridad, motivo: motivo,
    csrf_token: SD.csrfToken
  });

  fetch('index.php?page=directivo/crear_reemplazo_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (btn) btn.disabled = false;
      if (!data.ok) {
        showToast(data.error || 'No se pudo crear el reemplazo.', 'error');
        return;
      }
      cerrarModalNuevoReemplazo();
      showToast('Reemplazo creado correctamente.', 'success');
      // A diferencia de aprobar/asignar/cancelar (que actualizan una fila que
      // ya existe en la tabla), un reemplazo nuevo no tiene fila en el DOM
      // todavía — acá sí hace falta traer la tabla actualizada del servidor.
      // Se guarda la sección activa (mostrarSeccion ya la persiste) y se
      // restaura sola al volver a cargar.
      setTimeout(function () { location.reload(); }, 900);
    })
    .catch(function () {
      if (btn) btn.disabled = false;
      showToast('No se pudo crear el reemplazo. Verificá tu conexión.', 'error');
    });
}

// ─── NOTIFICACIONES (marcar como leída, real) ───
function marcarNotificacionLeida(notificacionId, elemento) {
  var body = new URLSearchParams({ notificacion_id: notificacionId, csrf_token: SD.csrfToken });
  fetch('index.php?page=directivo/marcar_notificacion_leida_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.ok) {
        showToast(data.error || 'No se pudo marcar como leída.', 'error');
        return;
      }
      if (elemento) {
        elemento.style.cursor = 'default';
        elemento.style.backgroundColor = '#fafafa';
        elemento.removeAttribute('onclick');
        var punto = elemento.querySelector('.item-notificacion__punto-lectura');
        if (punto) {
          punto.classList.remove('item-notificacion__punto-lectura--no-leida');
          punto.classList.add('item-notificacion__punto-lectura--leida');
        }
      }
    })
    .catch(function () {
      showToast('No se pudo marcar como leída. Verificá tu conexión.', 'error');
    });
}

// ─── PERFIL (editar datos reales) ───
function abrirModalPerfil() {
  var u = SD.usuario || {};
  var campos = { 'perfil-nombre': u.nombre, 'perfil-apellido': u.apellido, 'perfil-email': u.email, 'perfil-telefono': u.telefono };
  Object.keys(campos).forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.value = campos[id] || '';
  });
  var overlay = document.getElementById('modal-perfil-overlay');
  if (overlay) overlay.style.display = 'flex';
}

function cerrarModalPerfil() {
  var overlay = document.getElementById('modal-perfil-overlay');
  if (overlay) overlay.style.display = 'none';
}

function guardarPerfilDirectivo() {
  var nombre = (document.getElementById('perfil-nombre') || {}).value || '';
  var apellido = (document.getElementById('perfil-apellido') || {}).value || '';
  var email = (document.getElementById('perfil-email') || {}).value || '';
  var telefono = (document.getElementById('perfil-telefono') || {}).value || '';

  var btn = document.getElementById('btn-guardar-perfil');
  if (btn) btn.disabled = true;

  var body = new URLSearchParams({
    nombre: nombre.trim(),
    apellido: apellido.trim(),
    email: email.trim(),
    telefono: telefono.trim(),
    csrf_token: SD.csrfToken
  });

  fetch('index.php?page=directivo/actualizar_perfil_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (btn) btn.disabled = false;
      if (!data.ok) {
        showToast(data.error || 'No se pudo guardar el perfil.', 'error');
        return;
      }
      cerrarModalPerfil();
      // Actualiza en el DOM (encabezado del panel + tarjeta de perfil) sin
      // recargar: SD.usuario también se refresca para que reabrir el modal
      // muestre los valores nuevos.
      SD.usuario = { nombre: nombre.trim(), apellido: apellido.trim(), email: email.trim(), telefono: telefono.trim() };
      var nombreCompleto = SD.usuario.apellido ? (SD.usuario.nombre + ' ' + SD.usuario.apellido) : SD.usuario.nombre;
      document.querySelectorAll('.perfil-nombre').forEach(function (el) { el.textContent = nombreCompleto; });
      var valores = document.querySelectorAll('.cuadricula-datos-perfil .dato-perfil__valor');
      if (valores[0]) valores[0].textContent = nombreCompleto;
      if (valores[2]) valores[2].textContent = SD.usuario.email || '—';
      if (valores[3]) valores[3].textContent = SD.usuario.telefono || '—';
      showToast('Perfil actualizado correctamente.', 'success');
    })
    .catch(function () {
      if (btn) btn.disabled = false;
      showToast('No se pudo guardar el perfil. Verificá tu conexión.', 'error');
    });
}

// Arranca la app en la sección que estaba activa antes de un reload (si lo
// hubo) en vez de volver siempre a Dashboard.
restaurarSeccionActiva();

