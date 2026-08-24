// Sistema de notificaciones visuales compartido — reemplaza alert()/confirm()
// del navegador ("localhost dice") en los portales que no tenían su propio
// sistema (Estudiante, Padre/Tutor). Requiere <div id="toast-container">
// en la página (ver toast.css). Un módulo, reutilizable desde cualquier
// vista con un solo <script src="toast.js"> — no se duplica por portal.
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

// Modal de confirmación propio (reemplaza confirm() del navegador) para
// acciones que sí necesitan confirmación explícita — ej. cerrar sesión.
// Uso: mostrarConfirmacion('¿Cerrar sesión?', 'Vas a salir del portal.', function(){ ... });
function mostrarConfirmacion(titulo, texto, alConfirmar) {
  var existente = document.getElementById('confirm-overlay-generico');
  if (existente) existente.remove();

  var overlay = document.createElement('div');
  overlay.className = 'confirm-overlay';
  overlay.id = 'confirm-overlay-generico';
  overlay.innerHTML =
    '<div class="confirm-modal">' +
    '<div class="confirm-modal__titulo"></div>' +
    '<div class="confirm-modal__texto"></div>' +
    '<div class="confirm-modal__acciones">' +
    '<button type="button" class="confirm-modal__cancelar">Cancelar</button>' +
    '<button type="button" class="confirm-modal__aceptar">Confirmar</button>' +
    '</div></div>';
  overlay.querySelector('.confirm-modal__titulo').textContent = titulo;
  overlay.querySelector('.confirm-modal__texto').textContent = texto;

  function cerrar() { overlay.remove(); }
  overlay.querySelector('.confirm-modal__cancelar').addEventListener('click', cerrar);
  overlay.addEventListener('click', function (e) { if (e.target === overlay) cerrar(); });
  overlay.querySelector('.confirm-modal__aceptar').addEventListener('click', function () {
    cerrar();
    alConfirmar();
  });

  document.body.appendChild(overlay);
}
