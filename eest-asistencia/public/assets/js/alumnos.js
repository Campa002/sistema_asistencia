
(function () {
  'use strict';

  const SCREEN_TO_NAV = {
    'pantalla-dashboard': 'nav-dashboard',
    'pantalla-asistencia': 'nav-asistencia',
    'pantalla-alumnos': 'nav-alumnos',
    'pantalla-mensajes': 'nav-mensajes',
    'pantalla-perfil': 'nav-perfil'
  };

  // Datos reales inyectados por el servidor (ver alumnos.php -> window.SERVER_DATA),
  // generados por AlumnoController::portalData() a partir de la BD real.
  const SD = window.SERVER_DATA || { calendario: {}, msgs: [], csrfToken: '' };

  // Arranca en el mes más reciente que tenga registros reales (si no hay
  // ninguno, en el mes actual) — antes arrancaba siempre en Mayo 2026 fijo.
  let calendarioFecha = (function () {
    const claves = Object.keys(SD.calendario).sort();
    if (claves.length > 0) {
      const partes = claves[claves.length - 1].split('-').map(Number);
      return new Date(partes[0], partes[1] - 1, 1);
    }
    const hoy = new Date();
    return new Date(hoy.getFullYear(), hoy.getMonth(), 1);
  })();

  function byId(id) {
    return document.getElementById(id);
  }

  function actualizarEstadoMenu(abierto) {
    const sidebar = byId('alumno-sidebar');
    const overlay = byId('alumno-sidebar-overlay');
    const toggle = byId('alumno-menu-toggle');

    if (!sidebar || !overlay || !toggle) {
      return;
    }

    sidebar.classList.toggle('abierta', abierto);
    overlay.classList.toggle('abierto', abierto);

    toggle.setAttribute(
      'aria-expanded',
      abierto ? 'true' : 'false'
    );

    toggle.setAttribute(
      'aria-label',
      abierto ? 'Cerrar menú' : 'Abrir menú'
    );

    document.body.classList.toggle(
      'alumno-menu-abierto',
      abierto
    );
  }

  window.abrirSidebarAlumno = function () {
    actualizarEstadoMenu(true);
  };

  window.cerrarSidebarAlumno = function () {
    actualizarEstadoMenu(false);
  };

  window.toggleSidebarAlumno = function () {
    const sidebar = byId('alumno-sidebar');

    if (!sidebar) {
      return;
    }

    actualizarEstadoMenu(
      !sidebar.classList.contains('abierta')
    );
  };

  window.mostrarPantalla = function (id) {
    const destino = byId(id);

    if (!destino) {
      console.error('No existe la pantalla:', id);
      return;
    }

    document
      .querySelectorAll('#alumno-portal-root .pantalla')
      .forEach(function (pantalla) {
        pantalla.classList.remove('activa');
      });

    document
      .querySelectorAll('#alumno-portal-root .nav-item')
      .forEach(function (item) {
        item.classList.remove('activo');
      });

    destino.classList.add('activa');

    if (id === 'pantalla-mensajes' && typeof renderConversaciones === 'function') {
      renderConversaciones();
    }

    const navId = SCREEN_TO_NAV[id];
    const nav = navId ? byId(navId) : null;

    if (nav) {
      nav.classList.add('activo');
    }

    window.cerrarSidebarAlumno();

    const contenido = document.querySelector(
      '#alumno-portal-root .contenido'
    );

    if (contenido) {
      contenido.scrollTo({
        top: 0,
        behavior: 'auto'
      });
    } else {
      window.scrollTo({
        top: 0,
        behavior: 'auto'
      });
    }
  };

  window.filtrarAlumnos = function () {
    const buscador = byId('buscador-alumnos');
    const lista = byId('lista-alumnos');

    if (!buscador || !lista) {
      return;
    }

    const termino = buscador.value
      .trim()
      .toLocaleLowerCase('es');

    lista
      .querySelectorAll('.alumno-item')
      .forEach(function (item) {
        const nombre = item.querySelector('.alumno-nombre');
        const curso = item.querySelector('.alumno-curso');

        const texto = [
          nombre ? nombre.textContent : '',
          curso ? curso.textContent : ''
        ]
          .join(' ')
          .toLocaleLowerCase('es');

        item.style.display = texto.includes(termino)
          ? ''
          : 'none';
      });
  };

  // ─── MENSAJES (conversaciones reales, ver Mensaje.php) ───
  let activeConv = null;

  function iniciales(nombre) {
    return (nombre || '?')
      .split(' ')
      .filter(Boolean)
      .slice(0, 2)
      .map(function (p) { return p[0].toUpperCase(); })
      .join('');
  }

  function renderConversaciones() {
    const lista = byId('conversaciones-lista');
    const vacio = byId('mensajes-vacio');
    if (!lista) return;

    if (!SD.msgs.length) {
      lista.innerHTML = '';
      if (vacio) vacio.style.display = 'block';
      return;
    }
    if (vacio) vacio.style.display = 'none';

    lista.innerHTML = SD.msgs.map(function (m) {
      return '<div class="conversacion-item' + (activeConv === m.id ? ' activa' : '') + '" onclick="abrirChat(' + m.id + ')">' +
        '<div class="conv-avatar">' + iniciales(m.nombre) + '</div>' +
        '<div style="flex:1; min-width:0;">' +
          '<div class="conv-nombre">' + m.nombre + '</div>' +
          '<div class="conv-rol">' + m.rol + '</div>' +
          '<div class="conv-preview">' + m.preview + '</div>' +
        '</div>' +
        '<div class="conv-hora">' + m.hora + '</div>' +
      '</div>';
    }).join('');
  }

  function renderChatMensajes(conv) {
    const body = byId('chat-mensajes');
    if (!body) return;
    if (!conv.conversation.length) {
      body.innerHTML = '<div style="padding:24px;text-align:center;color:#6C757D;font-size:14px;">Sin mensajes todavía. Escribí el primero.</div>';
      return;
    }
    body.innerHTML = conv.conversation.map(function (msg) {
      const claseHora = msg.dir === 'enviado' ? 'mensaje-hora der' : 'mensaje-hora';
      return '<div class="mensaje-burbuja ' + msg.dir + '">' + msg.text + '</div>' +
        '<div class="' + claseHora + '">' + msg.time + '</div>';
    }).join('');
    body.scrollTop = body.scrollHeight;
  }

  window.abrirChat = function (id) {
    activeConv = id;
    const conv = SD.msgs.find(function (m) { return m.id === id; });
    if (!conv) return;

    renderConversaciones();

    const cabecera = byId('chat-cabecera');
    const vacioMsg = byId('chat-mensajes-vacio');
    if (cabecera) cabecera.style.display = 'flex';
    if (vacioMsg) vacioMsg.style.display = 'none';

    const avatar = byId('chat-avatar');
    if (avatar) avatar.textContent = iniciales(conv.nombre);
    const nombreEl = byId('chat-nombre');
    if (nombreEl) nombreEl.textContent = conv.nombre;
    const rolEl = byId('chat-rol');
    if (rolEl) rolEl.textContent = conv.rol;

    renderChatMensajes(conv);

    const panel = byId('panel-chat');
    if (panel && window.innerWidth <= 640) {
      panel.classList.add('activo');
    }
  };

  window.volverConversaciones = function () {
    const panel = byId('panel-chat');

    if (panel) {
      panel.classList.remove('activo');
    }
  };

  window.enviarMensaje = function (evento) {
    if (
      evento.key === 'Enter' &&
      !evento.shiftKey
    ) {
      evento.preventDefault();
      window.enviarMensajeClick();
    }
  };

  window.enviarMensajeClick = function () {
    const campo = byId('campo-mensaje');
    if (!campo || activeConv === null) return;

    const texto = campo.value.trim();
    if (texto === '') return;

    const conv = SD.msgs.find(function (m) { return m.id === activeConv; });
    if (!conv) return;

    campo.value = '';
    campo.disabled = true;

    fetch('index.php?page=alumno/enviar_mensaje_ajax', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ conversacion_id: activeConv, contenido: texto, csrf_token: SD.csrfToken })
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        campo.disabled = false;
        campo.focus();
        if (!data.ok) {
          alert(data.error || 'No se pudo enviar el mensaje.');
          campo.value = texto;
          return;
        }
        conv.conversation.push({ dir: 'enviado', text: texto, time: data.hora || '' });
        conv.preview = texto;
        renderChatMensajes(conv);
        renderConversaciones();
      })
      .catch(function () {
        campo.disabled = false;
        alert('No se pudo enviar el mensaje. Verificá tu conexión.');
        campo.value = texto;
      });
  };

  function estadoCalendario(anio, mes, dia) {
    const hoy = new Date();
    const esHoy = anio === hoy.getFullYear() &&
      mes === hoy.getMonth() &&
      dia === hoy.getDate();

    if (esHoy) {
      return 'hoy';
    }

    const clave = anio + '-' +
      String(mes + 1).padStart(2, '0') + '-' +
      String(dia).padStart(2, '0');

    return SD.calendario[clave] || '';
  }

  function renderizarCalendarioAlumno() {
    const titulo = byId('calendario-mes-titulo');
    const body = byId('calendario-alumno-body');

    if (!titulo || !body) {
      return;
    }

    const anio = calendarioFecha.getFullYear();
    const mes = calendarioFecha.getMonth();

    titulo.childNodes.forEach(function (nodo) {
      if (nodo.nodeType === Node.TEXT_NODE) {
        nodo.remove();
      }
    });

    const nombreMes = calendarioFecha
      .toLocaleDateString('es-AR', {
        month: 'long',
        year: 'numeric'
      })
      .replace(/^./, function (letra) {
        return letra.toUpperCase();
      });

    titulo.appendChild(
      document.createTextNode(' ' + nombreMes)
    );

    const primerDia = new Date(
      anio,
      mes,
      1
    );

    const ultimoDia = new Date(
      anio,
      mes + 1,
      0
    );

    const diasMes = ultimoDia.getDate();

    const desplazamiento =
      (primerDia.getDay() + 6) % 7;

    const diasMesAnterior = new Date(
      anio,
      mes,
      0
    ).getDate();

    let html = '';
    let contador = 1;
    let siguiente = 1;

    for (let fila = 0; fila < 6; fila += 1) {
      html += '<tr>';

      for (
        let columna = 0;
        columna < 7;
        columna += 1
      ) {
        const posicion =
          fila * 7 + columna;

        if (posicion < desplazamiento) {
          const diaAnterior =
            diasMesAnterior -
            desplazamiento +
            posicion +
            1;

          html +=
            '<td>' +
              '<div class="dia-celda vacio">' +
                diaAnterior +
              '</div>' +
            '</td>';
        } else if (contador <= diasMes) {
          const estado = estadoCalendario(
            anio,
            mes,
            contador
          );

          html +=
            '<td>' +
              '<div class="dia-celda ' +
                estado +
              '">' +
                contador +
              '</div>' +
            '</td>';

          contador += 1;
        } else {
          html +=
            '<td>' +
              '<div class="dia-celda vacio">' +
                siguiente +
              '</div>' +
            '</td>';

          siguiente += 1;
        }
      }

      html += '</tr>';

      if (
        contador > diasMes &&
        siguiente > 7
      ) {
        break;
      }
    }

    body.innerHTML = html;
  }

  window.cambiarMesAlumno = function (delta) {
    calendarioFecha = new Date(
      calendarioFecha.getFullYear(),
      calendarioFecha.getMonth() + delta,
      1
    );

    renderizarCalendarioAlumno();
  };

  function iniciarFiltrosMensajes() {
    document
      .querySelectorAll(
        '#alumno-portal-root .filtro-pill'
      )
      .forEach(function (pill) {
        pill.addEventListener(
          'click',
          function () {
            document
              .querySelectorAll(
                '#alumno-portal-root .filtro-pill'
              )
              .forEach(function (item) {
                item.classList.remove('activo');
              });

            pill.classList.add('activo');
          }
        );
      });
  }

  function manejarTeclado(evento) {
    if (evento.key === 'Escape') {
      window.cerrarSidebarAlumno();
    }
  }

  function manejarResize() {
    if (window.innerWidth > 850) {
      window.cerrarSidebarAlumno();
    }
  }

  function iniciarPanelAlumno() {
    iniciarFiltrosMensajes();
    renderizarCalendarioAlumno();

    document.addEventListener(
      'keydown',
      manejarTeclado
    );

    window.addEventListener(
      'resize',
      manejarResize
    );

    console.info(
      'Panel del alumno inicializado correctamente.'
    );
  }

  if (document.readyState === 'loading') {
    document.addEventListener(
      'DOMContentLoaded',
      iniciarPanelAlumno
    );
  } else {
    iniciarPanelAlumno();
  }
})();

