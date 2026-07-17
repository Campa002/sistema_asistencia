# aclaraciones.md

# Aclaraciones para continuar el desarrollo del Sistema de Gestión de Asistencia Escolar

Este archivo contiene aclaraciones importantes para continuar el desarrollo del sistema. El proyecto actual ya tiene una carpeta con backend y base de datos creados anteriormente, pero esa versión fue realizada con un informe anterior. Por eso, antes de seguir desarrollando, se debe revisar el código actual y compararlo con el informe actualizado y las reglas nuevas del sistema.

## 1. Prioridad de documentos

Para tomar decisiones, usar este orden de prioridad:

1. `reglas_del_sistema.md`
2. `informe_actualizado.md`
3. `aclaraciones.md`
4. bocetos e imágenes de interfaces
5. código actual del proyecto
6. actividad original

La actividad original sirve solo como contexto general, pero no debe ser la base principal, porque algunas cosas cambiaron. Si hay contradicciones entre la actividad y el informe actualizado, siempre se debe priorizar el informe actualizado y las reglas del sistema.

Si hay contradicciones entre los bocetos y el informe, siempre se debe priorizar el informe. Los bocetos deben usarse principalmente para respetar colores, formas, estilo visual, distribución general y diseño de interfaz.

## 2. Objetivo del trabajo

Necesito que desarrolles un sistema completo de gestión de asistencia escolar con frontend, backend, base de datos y configuración PWA.

El sistema debe estar hecho con PHP nativo, MySQL, HTML dentro de archivos PHP, CSS, JavaScript y estructura MVC simple.

No quiero archivos `.html` sueltos. Las interfaces deben estar en archivos `.php` con HTML integrado, porque el proyecto combina PHP y HTML.

El proyecto se va a ejecutar en XAMPP, usando MySQL.

## 3. Orden de trabajo obligatorio

No empieces directamente haciendo pantallas sin revisar el proyecto.

Primero hacé una auditoría del proyecto actual.

El orden recomendado es:

1. Leer `reglas_del_sistema.md`.
2. Leer `informe_actualizado.md`.
3. Leer este archivo `aclaraciones.md`.
4. Revisar la estructura actual del proyecto.
5. Revisar la base de datos existente.
6. Revisar backend, controladores, modelos, vistas, sesiones y permisos.
7. Comparar todo con las reglas nuevas.
8. Decir qué cosas sirven y qué cosas están desactualizadas.
9. Proponer los cambios necesarios.
10. Corregir primero la base de datos.
11. Corregir después el backend.
12. Crear o ajustar el frontend.
13. Implementar o ajustar la PWA.
14. Probar los flujos principales.

Si podés hacer todo en una sola tanda, hacelo. Si el proyecto es demasiado grande, avanzá por etapas, pero siempre dejando clara la estructura completa de archivos.

## 4. Estructura de archivos

Antes de crear o modificar muchos archivos, primero mostrale al usuario una estructura completa del proyecto con carpetas y archivos.

La estructura debe incluir también archivos que quizás se creen más adelante, para que el proyecto quede ordenado desde el inicio.

Usar una estructura MVC simple, no una carpeta plana.

Estructura sugerida:

```text
sistema-asistencia/
│
├── config/
│   ├── database.php
│   ├── config.php
│   └── constants.php
│
├── controllers/
│   ├── AuthController.php
│   ├── AdminController.php
│   ├── DirectivoController.php
│   ├── PreceptorController.php
│   ├── AlumnoController.php
│   ├── TutorController.php
│   ├── AsistenciaController.php
│   ├── JustificacionController.php
│   ├── ReemplazoController.php
│   ├── NotificacionController.php
│   ├── MensajeController.php
│   ├── ReporteController.php
│   └── ComunicadoController.php
│
├── models/
│   ├── Usuario.php
│   ├── Alumno.php
│   ├── Tutor.php
│   ├── Curso.php
│   ├── Division.php
│   ├── Materia.php
│   ├── Asistencia.php
│   ├── Justificacion.php
│   ├── Reemplazo.php
│   ├── Notificacion.php
│   ├── Mensaje.php
│   ├── Reporte.php
│   ├── Comunicado.php
│   └── Auditoria.php
│
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   ├── registro_alumno.php
│   │   └── primer_acceso_tutor.php
│   │
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── usuarios.php
│   │   ├── cursos.php
│   │   ├── materias.php
│   │   ├── historial_asistencias.php
│   │   ├── reportes.php
│   │   ├── comunicados_globales.php
│   │   ├── gestion_tecnica.php
│   │   └── perfil.php
│   │
│   ├── directivo/
│   │   ├── dashboard.php
│   │   ├── solicitudes_acceso.php
│   │   ├── vinculaciones_tutores.php
│   │   ├── reemplazos.php
│   │   ├── asistencia_institucional.php
│   │   ├── alertas.php
│   │   ├── notificaciones.php
│   │   └── perfil.php
│   │
│   ├── preceptor/
│   │   ├── dashboard.php
│   │   ├── tomar_asistencia.php
│   │   ├── historial_asistencias.php
│   │   ├── justificaciones.php
│   │   ├── retiros_anticipados.php
│   │   ├── mensajes_privados.php
│   │   ├── notificaciones.php
│   │   └── perfil.php
│   │
│   ├── alumno/
│   │   ├── dashboard.php
│   │   ├── historial_asistencia.php
│   │   ├── porcentaje_asistencia.php
│   │   ├── asignar_tutor.php
│   │   ├── notificaciones.php
│   │   └── perfil.php
│   │
│   ├── tutor/
│   │   ├── resumen.php
│   │   ├── historial_asistencias.php
│   │   ├── justificaciones.php
│   │   ├── mensajes_privados.php
│   │   ├── notificaciones.php
│   │   └── perfil.php
│   │
│   └── layouts/
│       ├── header.php
│       ├── sidebar_admin.php
│       ├── sidebar_directivo.php
│       ├── sidebar_preceptor.php
│       ├── sidebar_alumno.php
│       ├── sidebar_tutor.php
│       ├── footer.php
│       └── modal_base.php
│
├── public/
│   ├── index.php
│   ├── manifest.json
│   ├── service-worker.js
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   ├── styles.css
│   │   │   ├── responsive.css
│   │   │   └── auth.css
│   │   │
│   │   ├── js/
│   │   │   ├── app.js
│   │   │   ├── polling.js
│   │   │   ├── charts.js
│   │   │   ├── modals.js
│   │   │   ├── asistencia.js
│   │   │   ├── notificaciones.js
│   │   │   └── pwa.js
│   │   │
│   │   ├── img/
│   │   │   ├── logo.png
│   │   │   └── icons/
│   │   │
│   │   └── vendors/
│   │
│   └── uploads/
│       ├── justificaciones/
│       └── reportes/
│
├── includes/
│   ├── auth.php
│   ├── roles.php
│   ├── helpers.php
│   ├── csrf.php
│   └── flash.php
│
├── api/
│   ├── notificaciones.php
│   ├── mensajes.php
│   ├── asistencia.php
│   ├── alertas.php
│   └── reportes.php
│
├── sql/
│   ├── database.sql
│   ├── seed.sql
│   └── updates.sql
│
├── docs/
│   ├── informe_actualizado.md
│   ├── reglas_del_sistema.md
│   ├── aclaraciones.md
│   └── bocetos/
│
└── README.md
```

## 5. Stack técnico

Usar:

PHP nativo.

MySQL.

PDO para conexión a base de datos.

HTML dentro de archivos PHP.

CSS propio.

Bootstrap si ayuda al diseño y rapidez.

JavaScript.

Chart.js para gráficos si se necesitan.

JSON para respuestas simples de endpoints internos o API REST.

PWA con `manifest.json` y `service-worker.js`.

No usar Laravel, React, Vue, Angular o Node.js salvo que sea estrictamente necesario. Como el proyecto es escolar y se ejecutará en XAMPP, priorizar tecnologías simples y fáciles de defender.

## 6. Servidor

El proyecto se va a correr en XAMPP.

No sabemos todavía si el servidor GEdeCon tendrá Apache, Nginx o soporte para `.htaccess`.

Por ahora, el sistema debe funcionar bien en XAMPP y no depender obligatoriamente de `.htaccess`.

Si se usan rutas amigables, dejar una alternativa funcional con rutas tradicionales por query string o archivos PHP.

## 7. Autenticación

Usar sesiones PHP nativas.

No usar JWT.

Debe existir un único login.

Después del login, el sistema debe redirigir según el rol del usuario:

Administrador → panel de Administrador.

Directivo → panel de Directivo.

Preceptor → panel de Preceptor.

Alumno → panel de Alumno.

Padre/Tutor → panel de Padre/Tutor.

Cada vista o controlador debe validar el rol antes de mostrar contenido.

## 8. Base de datos

El SQL debe estar en un archivo separado dentro de la carpeta `sql/`.

El archivo principal debe llamarse `database.sql`.

También se puede crear `seed.sql` para datos de prueba.

La base de datos debe respetar el informe actualizado y las reglas nuevas.

Recordar especialmente:

Asistencia por materia.

Horario o módulo.

Cierre mensual.

Cálculo de inasistencias.

Vinculación Padre/Tutor desde Alumno.

DNI solo para adultos y personal institucional.

Justificaciones aprobadas por Preceptor.

Reemplazos asignados por Directivo.

Mensajería privada.

Alertas y telecomunicados.

Comunicado Global interno.

Auditoría.

## 9. Diseño y bocetos

Voy a pasar bocetos en formato PNG.

Algunos bocetos son de escritorio y otros de celular.

El Preceptor y el Alumno están pensados principalmente para celular, pero todas las interfaces deben ser responsive: escritorio y móvil.

El Administrador tiene bocetos viejos donde aparece aceptando solicitudes. No usar esa lógica, porque ahora las solicitudes las aprueba el Directivo. En los bocetos del Admin usar principalmente colores, formas, estilo, sidebar, cards, tablas, botones y distribución visual.

El Directivo no tiene boceto propio. Debe diseñarse siguiendo la misma línea visual del Administrador, pero con secciones y funciones propias.

Si hay algo en el boceto que no aparece en el informe, como descargar PDF u otras funciones extra, se puede dejar si no contradice la lógica principal.

Si hay contradicción entre boceto e informe, priorizar el informe.

## 10. Logo y colores

El logo del sitio será el archivo:

`i1780957417498_image.png`

La paleta de colores está en el archivo:

`1780957611529_image.png`

Usar esa paleta como base, pero se pueden usar colores adicionales si ayudan al diseño.

Mantener una estética moderna, clara, institucional y coherente entre todos los roles.

## 11. Directivo

El Directivo debe mantener una estética visual similar al Administrador.

Debe usar una barra lateral parecida, mismos colores, mismo estilo de cards, tablas, filtros y modales.

La diferencia está en las secciones y permisos.

Secciones del Directivo:

Dashboard.

Solicitudes de Acceso.

Vinculaciones de Padres/Tutores.

Reemplazos de Preceptores.

Asistencia Institucional.

Alertas y Telecomunicados.

Notificaciones.

Perfil / Cerrar sesión.

El Directivo no debe tener Gestión Técnica, backups, mantenimiento ni configuración avanzada.

## 12. Administrador

El Administrador tiene permisos completos.

Secciones del Administrador:

Dashboard.

Gestión de Usuarios.

Gestión de Cursos.

Gestión de Materias.

Historial de Asistencias.

Reportes.

Gestión Técnica.

Comunicados Globales.

Perfil / Cerrar sesión.

La sección Gestión Técnica no aparece en los bocetos, pero se abriría desde la tuerquita superior. Debe seguir la misma línea visual de los bocetos.

## 13. Comunicado Global

El Comunicado Global es un aviso interno del sistema.

No es una notificación push real del navegador.

El Administrador podrá crear avisos institucionales importantes.

Los usuarios podrán verlos al ingresar, en el dashboard o desde el panel de notificaciones.

Puede tener:

Título.

Mensaje.

Fecha de publicación.

Fecha de vencimiento.

Destinatarios.

Estado.

## 14. Notificaciones y mensajes

Usar polling simple.

No usar WebSockets.

Mensajes privados: consultar cada 10 o 15 segundos.

Alertas generales: consultar cada 30 o 60 segundos.

Dashboard: actualizar al cargar y, si hace falta, cada 60 segundos.

La mensajería dentro del sistema será solo privada entre Padre/Tutor y Preceptor.

No crear canal general grupal.

El Alumno no chatea con el Preceptor.

## 15. Reportes y PDF

Si se implementa exportación a PDF, usar la librería más conveniente y estable.

Puede usarse mPDF, TCPDF, Dompdf u otra similar.

Elegir la que tenga mayor probabilidad de funcionar bien en XAMPP con PHP.

Si la exportación a PDF complica demasiado el avance, dejarla como módulo preparado o mejora futura, pero no romper el resto del sistema.

## 16. Gráficos

Chart.js está bien para gráficos del dashboard.

Se puede usar otra opción si es más simple, pero no complicar el proyecto.

## 17. PWA

Implementar PWA de forma simple.

Debe incluir:

`manifest.json`.

`service-worker.js`.

Íconos.

Nombre de la app.

Color principal.

Pantalla adaptable.

No hace falta hacer una PWA demasiado compleja.

La prioridad es que el sistema funcione correctamente.

## 18. Preguntas antes de empezar

Antes de implementar cambios grandes, hacé las preguntas necesarias.

Si detectás contradicciones entre backend, base de datos, informe o bocetos, avisá antes de decidir.

No inventes lógica nueva si ya está definida en `reglas_del_sistema.md`.

## 19. Importante

No borrar código existente sin explicar por qué.

No romper funciones que ya estén funcionando.

Si se modifica una tabla, explicar la modificación.

Si se crea una tabla nueva, explicar para qué sirve.

Si se usa un dato de prueba, aclarar que es temporal.

Priorizar que el sistema funcione antes que hacerlo demasiado complejo.

La interfaz debe verse moderna y profesional, pero también debe ser clara y fácil de usar.
