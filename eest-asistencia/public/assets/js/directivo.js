
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

  cerrarSidebar();
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
        var confirmar = window.confirm(
          '¿Querés cerrar la sesión actual?'
        );

        if (!confirmar) {
          event.preventDefault();
          return;
        }

        var botones =
          formulario.querySelectorAll(
            'button[type="submit"]'
          );

        botones.forEach(
          function (boton) {
            boton.disabled = true;

            boton.setAttribute(
              'aria-busy',
              'true'
            );
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

