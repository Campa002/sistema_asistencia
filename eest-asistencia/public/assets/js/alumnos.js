
(function () {
  'use strict';

  const SCREEN_TO_NAV = {
    'pantalla-dashboard': 'nav-dashboard',
    'pantalla-asistencia': 'nav-asistencia',
    'pantalla-alumnos': 'nav-alumnos',
    'pantalla-mensajes': 'nav-mensajes',
    'pantalla-perfil': 'nav-perfil'
  };

  let calendarioFecha = new Date(2026, 4, 1);

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

  window.abrirChat = function (evento) {
    document
      .querySelectorAll(
        '#alumno-portal-root .conversacion-item'
      )
      .forEach(function (conversacion) {
        conversacion.classList.remove('activa');
      });

    const actual =
      evento && evento.currentTarget
        ? evento.currentTarget
        : null;

    if (actual) {
      actual.classList.add('activa');
    }

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

    const chat = document.querySelector(
      '#alumno-portal-root .chat-mensajes'
    );

    if (!campo || !chat) {
      return;
    }

    const texto = campo.value.trim();

    if (texto === '') {
      return;
    }

    const burbuja = document.createElement('div');
    burbuja.className = 'mensaje-burbuja enviado';
    burbuja.textContent = texto;

    const hora = document.createElement('div');
    hora.className = 'mensaje-hora der';

    const ahora = new Date();

    hora.textContent = ahora.toLocaleTimeString(
      'es-AR',
      {
        hour: '2-digit',
        minute: '2-digit'
      }
    );

    chat.appendChild(burbuja);
    chat.appendChild(hora);

    chat.scrollTop = chat.scrollHeight;

    campo.value = '';
    campo.focus();
  };

  function estadoCalendario(anio, mes, dia) {
    if (anio !== 2026 || mes !== 4) {
      return '';
    }

    const presentes = [
      1,
      2,
      3,
      6,
      8,
      9,
      13,
      17
    ];

    const tardes = [
      7,
      16
    ];

    const ausentes = [
      10,
      14,
      15
    ];

    if (presentes.includes(dia)) {
      return 'presente';
    }

    if (tardes.includes(dia)) {
      return 'tarde';
    }

    if (ausentes.includes(dia)) {
      return 'ausente';
    }

    if (dia === 20) {
      return 'hoy';
    }

    return '';
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

