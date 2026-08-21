
// ═══════════════════════════════════════
//  DATA
// ═══════════════════════════════════════
// Provistos por el servidor (ver padre-tutor.php -> window.SERVER_DATA),
// generados por PadreTutorController::portalData() a partir de la BD real
// (vinculaciones, detalles_asistencia, mensajes).
const SD = window.SERVER_DATA || { porMes: {}, anioInicial: new Date().getFullYear(), mesInicial: new Date().getMonth() + 1, msgs: [], csrfToken: '' };

const MONTHS = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
let currentMonth = SD.mesInicial;
let currentYear  = SD.anioInicial;

const DIAS_DATA = SD.porMes;
const msgsData = SD.msgs.map(function (m) {
  return {
    id: m.id, from: m.from, role: m.role, time: m.time, unread: m.unread,
    preview: m.preview, preview2: '', conversation: m.conversation
  };
});

let activeConv = null;
let filteredMsgs = [...msgsData];

// ═══════════════════════════════════════
//  NAV
// ═══════════════════════════════════════
function showView(name){
  document.querySelectorAll('.view').forEach(v=>v.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('view-'+name).classList.add('active');
  const nav = document.getElementById('nav-'+name);
  if(nav) nav.classList.add('active');
  if(name==='mensajes') renderMsgList();
  if(name==='registro') renderDayRows();
  closeSidebar();
  syncBNav(name);
  window.scrollTo(0,0);
  document.getElementById('notif-panel').classList.remove('open');
}

function syncBNav(name){
  document.querySelectorAll('.bottom-nav-item').forEach(b=>b.classList.remove('active'));
  const el = document.getElementById('bnav-'+name);
  if(el) el.classList.add('active');
}

// ═══════════════════════════════════════
//  SIDEBAR MOBILE
// ═══════════════════════════════════════
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('open');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('open');
}

// ═══════════════════════════════════════
//  NOTIF PANEL
// ═══════════════════════════════════════
function toggleNotifPanel(){
  document.getElementById('notif-panel').classList.toggle('open');
}
document.addEventListener('click',function(e){
  const panel = document.getElementById('notif-panel');
  if(!panel.contains(e.target) && !e.target.closest('.notif-wrap')){
    panel.classList.remove('open');
  }
});

// ═══════════════════════════════════════
//  REGISTRO – DAY ROWS
// ═══════════════════════════════════════
function renderDayRows(){
  const key = currentYear+'-'+currentMonth;
  const dias = DIAS_DATA[key] || [];
  document.getElementById('month-label').textContent = MONTHS[currentMonth-1]+' '+currentYear;
  const container = document.getElementById('day-rows');
  if(dias.length===0){
    container.innerHTML='<div class="card" style="text-align:center;padding:30px;color:var(--gray-400);">Sin registros para este mes.</div>';
    return;
  }
  container.innerHTML = dias.map(d=>{
    let borderClass, badgeHtml, horaText;
    if(d.estado==='presente'){
      borderClass='border-green';
      badgeHtml='<span class="badge badge-green">PRESENTE</span>';
      horaText='Ingreso: '+d.ingreso;
    } else if(d.estado==='tarde'){
      borderClass='border-yellow';
      badgeHtml='<span class="badge badge-yellow">TARDE</span>';
      horaText='Ingreso: '+d.ingreso;
    } else {
      borderClass='border-red';
      badgeHtml='<span class="badge badge-red">AUSENTE</span>';
      horaText='Sin registro de ingreso';
    }
    return `
    <div class="day-row">
      <div class="day-col">
        <div class="day-name">${d.dia}</div>
        <div class="day-num">${d.num}</div>
      </div>
      <div class="day-content day-left-border ${borderClass}">
        <div>
          <div class="day-type">${d.tipo}</div>
          <div class="day-hour">${horaText}</div>
        </div>
        ${badgeHtml}
      </div>
    </div>`;
  }).join('');
}

function changeMonth(dir){
  currentMonth += dir;
  if(currentMonth<1){ currentMonth=12; currentYear--; }
  if(currentMonth>12){ currentMonth=1; currentYear++; }
  renderDayRows();
}

function attachFile(input){
  if(!input.files.length) return;
  const file = input.files[0];
  if(activeConv===null) return;
  const conv = msgsData.find(m=>m.id===activeConv);
  if(!conv) return;
  conv.conversation.push({ dir:'out', text:null, time:nowTime(), hasFile:true, fileName:file.name, fileSize:(file.size/1024/1024).toFixed(1)+' MB' });
  renderChat(conv);
  input.value='';
}

function nowTime(){
  const d=new Date();
  return d.getHours().toString().padStart(2,'0')+':'+d.getMinutes().toString().padStart(2,'0');
}

// ═══════════════════════════════════════
//  MENSAJES
// ═══════════════════════════════════════
function renderMsgList(){
  const list = document.getElementById('msg-list');
  list.innerHTML = filteredMsgs.map(m=>`
    <div class="msg-item ${activeConv===m.id?'active':''}" onclick="openConv(${m.id})">
      <div class="msg-avatar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        ${m.unread?'<div class="msg-dot"></div>':''}
      </div>
      <div class="msg-info">
        <div class="msg-from">${m.from}</div>
        <div class="${m.unread?'msg-preview-bold':'msg-preview'}">${m.preview}</div>
      </div>
      <div class="msg-meta">
        <span class="${m.unread?'msg-time':'msg-time-gray'}">${m.time}</span>
      </div>
    </div>
  `).join('');
}

function filterMsgs(q){
  const lower = q.toLowerCase();
  filteredMsgs = msgsData.filter(m=>
    m.from.toLowerCase().includes(lower) || m.preview.toLowerCase().includes(lower)
  );
  renderMsgList();
}

function openConv(id){
  activeConv = id;
  const conv = msgsData.find(m=>m.id===id);
  conv.unread = false;
  renderMsgList();

  const chatPanel = document.getElementById('chat-panel');
  const chatEmpty = document.getElementById('chat-empty');
  chatPanel.style.display='flex';
  chatPanel.style.flexDirection='column';
  chatPanel.style.flex='1';
  chatPanel.style.overflow='hidden';
  chatEmpty.style.display='none';

  document.getElementById('chat-name-head').textContent = conv.from;
  document.getElementById('chat-status-head').textContent = 'En línea ahora';

  renderChat(conv);

  // Mobile: show panel, hide list
  if(window.innerWidth<=768){
    document.getElementById('msg-panel').classList.add('mobile-open');
    document.getElementById('msg-list-panel').style.display='none';
  }
}

function renderChat(conv){
  const body = document.getElementById('chat-body');
  body.innerHTML = conv.conversation.map(msg=>{
    if(msg.hasFile){
      return `
      <div class="bubble-wrap ${msg.dir}">
        <div class="file-bubble">
          <div class="file-bubble-header">
            <div class="file-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="file-info">
              <div class="file-name">${msg.fileName}</div>
              <div class="file-size">${msg.fileSize}</div>
            </div>
          </div>
          <div class="file-preview-placeholder">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          </div>
          <a class="file-download" href="#" onclick="return false">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Descargar
          </a>
        </div>
        <div class="bubble-time">${msg.time}</div>
      </div>`;
    }
    if(msg.dir==='out'){
      return `
      <div class="bubble-wrap out">
        <div class="bubble bubble-out">${msg.text}</div>
        <div class="bubble-check">${msg.time} <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/><polyline points="20 6 9 17 4 12" transform="translate(4,0)"/></svg></div>
      </div>`;
    }
    return `
    <div class="bubble-wrap in">
      <div class="bubble bubble-in">${msg.text}</div>
      <div class="bubble-time">${msg.time}</div>
    </div>`;
  }).join('');
  body.scrollTop = body.scrollHeight;
}

function sendMsg(){
  if(activeConv===null) return;
  const input = document.getElementById('chat-input');
  const text = input.value.trim();
  if(!text) return;
  const conv = msgsData.find(m=>m.id===activeConv);

  input.value='';
  input.style.height='';
  input.disabled = true;

  fetch('index.php?page=padre_tutor/enviar_mensaje_ajax', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ conversacion_id: activeConv, contenido: text, csrf_token: SD.csrfToken })
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      input.disabled = false;
      if (!data.ok) {
        alert(data.error || 'No se pudo enviar el mensaje.');
        input.value = text;
        return;
      }
      conv.conversation.push({ dir:'out', text, time: data.hora || nowTime(), hasFile:false });
      conv.preview = text;
      renderChat(conv);
    })
    .catch(function () {
      input.disabled = false;
      alert('No se pudo enviar el mensaje. Verificá tu conexión.');
      input.value = text;
    });
}

function closeChatMobile(){
  document.getElementById('msg-panel').classList.remove('mobile-open');
  document.getElementById('msg-list-panel').style.display='flex';
  document.getElementById('msg-list-panel').style.flexDirection='column';
  activeConv = null;
  renderMsgList();
}

function autoGrow(el){
  el.style.height='';
  el.style.height = Math.min(el.scrollHeight, 100)+'px';
}

// ═══════════════════════════════════════
//  INIT
// ═══════════════════════════════════════
showView('resumen');
renderDayRows();
renderMsgList();