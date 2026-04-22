# 📋 TaskFlow — Gestor de Tareas Kanban

Proyecto académico de gestión de tareas con tablero Kanban implementado en HTML, CSS y JavaScript puro.

## 🚀 Características

- Formulario para agregar tareas con título, descripción, prioridad y fecha límite
- Tablero Kanban con 3 columnas: **Pendientes**, **En Progreso**, **Completadas**
- Cambio de estado mediante botones en cada tarjeta
- Eliminación de tareas con animación
- Contadores automáticos por columna
- Validación de campos del formulario
- Diseño responsive (desktop, tablet, móvil)

## 📁 Estructura del Proyecto

```
proyecto_tareas_gestor/
├── index.html
├── README.md
├── assets/
│   ├── css/
│   │   ├── style.css         # Estilos principales y variables
│   │   └── responsive.css    # Media queries (3 breakpoints)
│   ├── js/
│   │   └── script.js         # Lógica de la aplicación
│   ├── img/
│   │   ├── logo.png
│   │   └── favicon.ico
│   └── fonts/
└── docs/
    └── uso_ia.md
```

## 🛠️ Tecnologías

- **HTML5** — Estructura semántica
- **CSS3** — Variables CSS, Flexbox, Grid, animaciones
- **JavaScript ES6+** — DOM manipulation, eventos, estado reactivo

## ▶️ Cómo usar

1. Clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/proyecto-tareas-gestor.git
   ```
2. Abre `index.html` en tu navegador.
3. No requiere dependencias ni servidor.

## 📌 Flujo de una tarea

```
[Nueva tarea] → Pendiente → En Progreso → Completada
                    ↑____________↑
                    (se puede regresar)
```

## 📄 Licencia

Proyecto académico — uso libre con atribución.
