/* ============================================
   TASKFLOW — script.js
   Gestor de tareas Kanban
   ============================================ */

// ── ESTADO GLOBAL ──────────────────────────
var estado = { tareas: [] };
var idCounter = 0;

// ── REFERENCIAS DOM ────────────────────────
var form           = document.getElementById('formulario-tarea');
var inputTitulo    = document.getElementById('titulo');
var inputDesc      = document.getElementById('descripcion');
var inputPrioridad = document.getElementById('prioridad');
var inputFecha     = document.getElementById('fecha');
var mensajeError   = document.getElementById('mensaje-error');

var listaPendientes = document.getElementById('lista-pendientes');
var listaProgreso   = document.getElementById('lista-progreso');
var listaCompletado = document.getElementById('lista-completado');

var contPendiente  = document.getElementById('contador-pendiente');
var contProgreso   = document.getElementById('contador-progreso');
var contCompletado = document.getElementById('contador-completado');
var totalTareas    = document.getElementById('total-tareas');

var columnas = {
  pendiente:  listaPendientes,
  progreso:   listaProgreso,
  completado: listaCompletado
};

var contadores = {
  pendiente:  contPendiente,
  progreso:   contProgreso,
  completado: contCompletado
};

// ── SUBMIT FORMULARIO ──────────────────────
form.addEventListener('submit', function(e) {
  e.preventDefault();
  limpiarErrores();

  var titulo    = inputTitulo.value.trim();
  var desc      = inputDesc.value.trim();
  var prioridad = inputPrioridad.value;
  var fecha     = inputFecha.value;

  var hayError = false;

  if (!titulo)    { marcarError(inputTitulo,    'El título no puede estar vacío.');        hayError = true; }
  if (!desc)      { marcarError(inputDesc,       'La descripción no puede estar vacía.');  hayError = true; }
  if (!prioridad) { marcarError(inputPrioridad,  'Debes seleccionar una prioridad.');      hayError = true; }
  if (!fecha)     { marcarError(inputFecha,      'Debes indicar una fecha límite.');       hayError = true; }

  if (hayError) {
    mostrarError('⚠️ Completa todos los campos antes de agregar la tarea.');
    return;
  }

  idCounter++;
  var tarea = {
    id:        idCounter,
    titulo:    titulo,
    desc:      desc,
    prioridad: prioridad,
    fecha:     fecha,
    estado:    'pendiente'
  };

  estado.tareas.push(tarea);
  renderizarTarjeta(tarea);
  actualizarContadores();
  form.reset();
  limpiarErrores();

  document.getElementById('tablero').scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// Limpiar error al escribir
[inputTitulo, inputDesc, inputPrioridad, inputFecha].forEach(function(campo) {
  ['input', 'change'].forEach(function(ev) {
    campo.addEventListener(ev, function() {
      campo.classList.remove('campo-invalido');
      if (!form.querySelector('.campo-invalido')) limpiarErrores();
    });
  });
});

// ── RENDERIZAR TARJETA ──────────────────────
function renderizarTarjeta(tarea) {
  var container = columnas[tarea.estado];
  var vacio = container.querySelector('.estado-vacio');
  if (vacio) vacio.remove();

  var card = document.createElement('div');
  card.className = 'tarjeta prioridad-' + tarea.prioridad;
  card.setAttribute('data-id', tarea.id);

  card.innerHTML =
    '<div class="tarjeta-titulo">' + escapar(tarea.titulo) + '</div>' +
    '<div class="tarjeta-descripcion">' + escapar(tarea.desc) + '</div>' +
    '<div class="tarjeta-meta">' +
      '<span class="badge-prioridad ' + tarea.prioridad + '">' + etiquetaPrioridad(tarea.prioridad) + '</span>' +
      '<span class="badge-fecha">📅 ' + formatearFecha(tarea.fecha) + '</span>' +
    '</div>' +
    '<div class="tarjeta-acciones" id="acciones-' + tarea.id + '"></div>';

  container.appendChild(card);
  renderizarBotones(tarea.id, tarea.estado);
  actualizarVacios();
}

// ── BOTONES CON addEventListener ───────────
function renderizarBotones(id, estadoActual) {
  var div = document.getElementById('acciones-' + id);
  if (!div) return;
  div.innerHTML = '';

  if (estadoActual === 'pendiente') {
    div.appendChild(crearBoton('btn-accion btn-iniciar',   '▶ Iniciar',   id, 'progreso'));
    div.appendChild(crearBoton('btn-accion btn-completar', '✓ Completar', id, 'completado'));
  } else if (estadoActual === 'progreso') {
    div.appendChild(crearBoton('btn-accion btn-regresar',  '← Pendiente', id, 'pendiente'));
    div.appendChild(crearBoton('btn-accion btn-completar', '✓ Completar', id, 'completado'));
  } else if (estadoActual === 'completado') {
    div.appendChild(crearBoton('btn-accion btn-regresar',  '↩ Reabrir',   id, 'pendiente'));
  }

  var btnEliminar = document.createElement('button');
  btnEliminar.className   = 'btn-eliminar';
  btnEliminar.textContent = '✕ Eliminar';
  btnEliminar.addEventListener('click', (function(tid) {
    return function() { eliminarTarea(tid); };
  })(id));
  div.appendChild(btnEliminar);
}

function crearBoton(clases, texto, id, nuevoEstado) {
  var btn = document.createElement('button');
  btn.className   = clases;
  btn.textContent = texto;
  btn.addEventListener('click', (function(tid, ns) {
    return function() { cambiarEstado(tid, ns); };
  })(id, nuevoEstado));
  return btn;
}

// ── CAMBIAR ESTADO ─────────────────────────
function cambiarEstado(id, nuevoEstado) {
  var tarea = null;
  for (var i = 0; i < estado.tareas.length; i++) {
    if (estado.tareas[i].id === id) { tarea = estado.tareas[i]; break; }
  }
  if (!tarea) return;

  var card = document.querySelector('.tarjeta[data-id="' + id + '"]');
  if (!card) return;

  tarea.estado = nuevoEstado;
  card.remove();

  var nuevoContainer = columnas[nuevoEstado];
  var vacio = nuevoContainer.querySelector('.estado-vacio');
  if (vacio) vacio.remove();

  nuevoContainer.appendChild(card);

  // Restaurar id en el div de acciones
  var accionesDiv = card.querySelector('.tarjeta-acciones');
  accionesDiv.id  = 'acciones-' + id;

  renderizarBotones(id, nuevoEstado);
  actualizarVacios();
  actualizarContadores();
}

// ── ELIMINAR TAREA ─────────────────────────
function eliminarTarea(id) {
  var idx = -1;
  for (var i = 0; i < estado.tareas.length; i++) {
    if (estado.tareas[i].id === id) { idx = i; break; }
  }
  if (idx === -1) return;
  estado.tareas.splice(idx, 1);

  var card = document.querySelector('.tarjeta[data-id="' + id + '"]');
  if (card) {
    card.style.transition = 'opacity .2s ease, transform .2s ease';
    card.style.opacity    = '0';
    card.style.transform  = 'scale(0.95)';
    setTimeout(function() {
      card.remove();
      actualizarVacios();
      actualizarContadores();
    }, 220);
  }
}

// ── CONTADORES ─────────────────────────────
function actualizarContadores() {
  var grupos = { pendiente: 0, progreso: 0, completado: 0 };
  estado.tareas.forEach(function(t) { grupos[t.estado]++; });
  contadores.pendiente.textContent  = grupos.pendiente;
  contadores.progreso.textContent   = grupos.progreso;
  contadores.completado.textContent = grupos.completado;
  totalTareas.textContent = grupos.pendiente + grupos.progreso;
}

// ── ESTADO VACÍO ───────────────────────────
function actualizarVacios() {
  Object.keys(columnas).forEach(function(key) {
    var container   = columnas[key];
    var tieneTareas = container.querySelector('.tarjeta');
    var tieneVacio  = container.querySelector('.estado-vacio');
    if (!tieneTareas && !tieneVacio) {
      var p = document.createElement('p');
      p.className   = 'estado-vacio';
      p.textContent = 'Sin tareas aquí';
      container.appendChild(p);
    } else if (tieneTareas && tieneVacio) {
      tieneVacio.remove();
    }
  });
}

// ── VALIDACIÓN VISUAL ──────────────────────
function marcarError(campo, msg) {
  campo.classList.add('campo-invalido');
  campo.setAttribute('title', msg);
}

function mostrarError(msg) {
  mensajeError.textContent = msg;
  mensajeError.style.display = 'block';
}

function limpiarErrores() {
  mensajeError.textContent   = '';
  mensajeError.style.display = 'none';
  [inputTitulo, inputDesc, inputPrioridad, inputFecha].forEach(function(c) {
    c.classList.remove('campo-invalido');
    c.removeAttribute('title');
  });
}

// ── UTILS ──────────────────────────────────
function escapar(str) {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function formatearFecha(iso) {
  if (!iso) return '—';
  var p = iso.split('-');
  return p[2] + '/' + p[1] + '/' + p[0];
}

function etiquetaPrioridad(p) {
  var map = { alta: '🔴 Alta', media: '🟡 Media', baja: '🟢 Baja' };
  return map[p] || p;
}

// ── INIT ───────────────────────────────────
actualizarVacios();
