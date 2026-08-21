# Informe actualizado del Sistema de Gestión de Asistencia Escolar

## 1. Introducción

El Sistema de Gestión de Asistencia Escolar es una plataforma web institucional diseñada para digitalizar, organizar y automatizar el registro de asistencia de los estudiantes.

El sistema reemplaza los registros manuales realizados en hojas, planillas o cuadernos por una herramienta centralizada que permite consultar información de manera rápida, segura y ordenada.

La plataforma está pensada para administradores, directivos, preceptores, alumnos y padres o tutores. Cada usuario accede únicamente a las funciones correspondientes a su rol.

El proyecto se desarrolla como una aplicación web progresiva (PWA), utilizando PHP nativo, MySQL, HTML integrado en archivos PHP, CSS, JavaScript y una estructura MVC simple. Su entorno principal de ejecución es XAMPP.

## 2. Problemática

En muchas instituciones educativas, la asistencia todavía se registra manualmente. Esto puede provocar pérdida de tiempo, errores, dificultad para consultar información histórica, demoras en reportes, problemas para controlar llegadas tarde y retiros anticipados, dificultades para justificar inasistencias, poca comunicación con las familias y falta de trazabilidad cuando se modifica un registro.

El sistema busca resolver estos problemas mediante una plataforma centralizada y accesible.

## 3. Objetivo general

Desarrollar un sistema que permita gestionar la asistencia escolar de forma digital, organizada, segura y accesible.

El sistema debe facilitar el trabajo diario de los preceptores, mejorar el control institucional de los directivos, permitir al administrador mantener la plataforma y ofrecer a alumnos y familias acceso a su información.

## 4. Objetivos específicos

- Registrar asistencia por materia.
- Guardar curso, división, turno, bloque horario, materia, fecha y preceptor.
- Registrar el estado individual de cada alumno.
- Calcular faltas según las reglas institucionales.
- Mantener un máximo de una falta por alumno y por día.
- Gestionar llegadas tarde y retiros anticipados.
- Permitir el envío y revisión de justificaciones.
- Generar reportes institucionales.
- Registrar auditoría sobre cambios importantes.
- Facilitar la vinculación entre alumnos y padres o tutores.
- Gestionar reemplazos de preceptores.
- Mostrar alertas, notificaciones y comunicados institucionales.
- Ofrecer una interfaz adaptable a computadoras y teléfonos.
- Permitir la instalación del sistema como PWA.

## 5. Tecnologías utilizadas

El sistema utiliza PHP nativo, MySQL o MariaDB, PDO, HTML integrado en archivos PHP, CSS propio, JavaScript, Chart.js, JSON, sesiones PHP nativas, polling simple, manifest y Service Worker.

No se utiliza JWT, WebSockets, Laravel, React, Vue, Angular ni Node.js en esta versión.

## 6. Arquitectura del sistema

El proyecto utiliza una estructura MVC simple:

- **Modelos:** realizan consultas y operaciones con la base de datos.
- **Controladores:** procesan acciones, validan datos y coordinan el flujo.
- **Vistas:** muestran las interfaces según el rol.
- **Configuración:** conexión a la base, constantes y parámetros.
- **Includes:** autenticación, permisos, helpers y mensajes temporales.
- **Public:** punto de entrada, CSS, JavaScript, imágenes, manifest y Service Worker.
- **SQL:** estructura de la base de datos y datos de prueba.
- **Docs:** documentación funcional y técnica.

El sistema debe funcionar con rutas por query string, por ejemplo `index.php?page=admin/asistencias`.

## 7. Roles y permisos

### 7.1 Administrador

Puede gestionar usuarios, cursos, materias, asistencias, reportes, parámetros técnicos, comunicados globales, mantenimiento y auditoría. Puede corregir asistencias cerradas y anular registros mediante baja lógica. También puede comunicarse internamente con directivos, preceptores y otros administradores.

No debe leer libremente conversaciones privadas entre padres o tutores y preceptores.

### 7.2 Directivo

Puede aprobar o rechazar solicitudes de alumnos, aprobar o rechazar vinculaciones de padres o tutores, asignar reemplazos, consultar asistencia institucional, visualizar estadísticas, gestionar alertas y consultar notificaciones.

No realiza mantenimiento técnico ni edita asistencias como tarea normal.

### 7.3 Preceptor

Puede tomar asistencia por materia, consultar cursos y materias asignadas, editar asistencias dentro del período permitido, registrar llegadas tarde, retiros anticipados, revisar justificaciones, consultar historial, recibir notificaciones y comunicarse con padres o tutores vinculados.

### 7.4 Alumno

Puede consultar su historial, porcentaje de asistencia, faltas, llegadas tarde, retiros, notificaciones y vincular a un padre o tutor. No puede editar asistencias ni chatear directamente con preceptores.

### 7.5 Padre o Tutor

Puede consultar la información del alumno vinculado, enviar justificaciones, ver alertas y comunicarse de forma privada con el preceptor. No puede acceder a estudiantes no vinculados.

## 8. Autenticación y acceso

El sistema tiene un único inicio de sesión. Después de validar correo y contraseña, redirige al panel correspondiente según el rol.

Cada vista y controlador debe validar que exista una sesión activa, que el usuario esté habilitado y que el rol tenga permiso. Las contraseñas se guardan con hash y nunca deben mostrarse.

## 9. Registro de usuarios

### 9.1 Alumno

El alumno se registra desde la pantalla inicial con nombre, apellido, correo, contraseña, curso, división, turno y especialidad cuando corresponda. No se solicita DNI. La cuenta queda pendiente hasta la aprobación del Directivo.

### 9.2 Padre/Tutor

No se registra libremente. El alumno aprobado inicia la vinculación y carga los datos del adulto. El Directivo aprueba o rechaza la solicitud. Luego el adulto configura su contraseña mediante primer acceso o enlace de invitación.

### 9.3 Usuarios institucionales

Administradores, directivos y preceptores son creados por el Administrador. Se solicita nombre, apellido, DNI, correo institucional, teléfono, rol, turno o cargo y asignaciones correspondientes.

## 10. Gestión de cursos y materias

El Administrador gestiona años, divisiones, turnos, ciclo lectivo, especialidades, aulas, materias y asignaciones.

Especialidades principales:

- Ciclo Básico.
- Programación.
- Electrónica.

Un curso puede tener uno o dos turnos. Se permiten mañana+tarde o tarde+vespertino. No se permite mañana+vespertino ni tres turnos.

## 11. Bloques horarios

(Corregidos según los Excel institucionales "Horarios ciclo básico" y "horarios ciclo superior", agosto 2026)

### Turno mañana

- 1ra hora: 07:35 a 09:35.
- Recreo: 09:35 a 09:55.
- 2da hora: 09:55 a 11:55.

### Almuerzo

- 11:55 a 12:55.

### Turno tarde

- 1ra hora: 12:55 a 14:55.
- Recreo: 14:55 a 15:15.
- 2da hora: 15:15 a 17:15.

### Turno vespertino

- 1ra hora: 17:35 a 19:35.
- Recreo: 19:35 a 19:45.
- 2da hora: 19:45 a 21:45.

En base se guardan como `primera_hora` y `segunda_hora`; en pantalla se muestran como “1ra hora” y “2da hora”.

## 12. Registro de asistencia

La asistencia se toma por materia o cambio de materia. Si una materia dura varias horas seguidas, se registra una sola asistencia al inicio.

Cada registro general contiene fecha, curso, división, turno, materia, bloque horario, preceptor, observaciones y estado general.

Cada detalle por alumno contiene alumno, estado, hora de llegada cuando corresponde, observación e indicador de modificación.

## 13. Estados de asistencia por alumno

- Presente.
- Ausente.
- Llegada tarde.
- Ausente con presente.
- Justificado.
- Retirado anticipado.

Existe un margen de 15 minutos. Dentro del margen se registra llegada tarde; después del margen se registra ausente con presente o media falta según el tipo de jornada.

## 14. Cálculo de faltas

### Regla principal

Un alumno no puede sumar más de una falta por día. La falta se calcula por turno y día, no por cantidad de materias.

### Turno único

- Presente: 0.
- Llegada tarde: 0,25.
- Retirado anticipado: 0,5.
- Ausente con presente: 1.
- Ausente: 1.
- Justificado: 1, identificado como justificado.

### Doble turno

- Presente en ambos: 0.
- Llegada tarde en un turno: 0,25.
- Retirado anticipado en un turno: 0,5.
- Ausente en un turno: 0,5.
- Ausente en ambos: 1.
- Retiro anticipado en el primer turno y ausencia en el segundo: 1.
- Justificado: suma como ausencia normal.

Casos especiales:

- 1ra hora ausente y 2da presente: 1 falta para ese turno.
- 1ra hora presente y 2da ausente: 0,5 por retiro anticipado.

## 15. Estado general del registro

- **Abierta:** registro dentro del período activo.
- **Cerrada:** registro fuera del período editable normal.
- **Modificada:** corregida por un Administrador.
- **Anulada:** dada de baja de forma lógica.

Prioridad visual: anulada, modificada, cerrada, abierta.

Una asistencia anulada permanece visible para auditoría, pero no suma faltas ni afecta porcentajes.

## 16. Edición y cierre

El Preceptor puede editar asistencias dentro del mes activo. Una vez cerrado el período, solo el Administrador puede realizar correcciones excepcionales.

El Administrador puede modificar curso, materia, turno, bloque, fecha, preceptor, observaciones y estados de alumnos. Toda modificación cambia el registro a modificada, registra auditoría y recalcula el resumen diario.

## 17. Auditoría

Registra usuario, fecha y hora, registro afectado, alumno afectado cuando corresponde, acción, campo modificado, valor anterior, valor nuevo y observaciones.

## 18. Resumen diario

Se calcula por alumno, curso, fecha y turno. Incluye faltas totales, justificación, detalle del cálculo y fecha de actualización. Debe respetar el máximo de una falta diaria.

## 19. Retiros anticipados

Debe guardar alumno, fecha, hora, motivo, mayoría o minoría de edad, adulto autorizado cuando corresponda, usuario que registra y observaciones.

El retiro anticipado suma 0,5 faltas.

## 20. Justificaciones

El Padre/Tutor envía la justificación y el Preceptor la aprueba o rechaza. Una falta justificada continúa sumando, pero queda identificada para evaluación institucional.

## 21. Reemplazos

Los asigna el Directivo. Se registra preceptor ausente, reemplazante, curso, fecha, bloque horario, motivo, estado y usuario que asignó.

## 22. Alertas y notificaciones

Matriz prevista:

- cada llegada tarde: aviso al Padre/Tutor;
- 10 faltas: advertencia;
- 20 faltas: alerta para acta formal;
- 28 faltas: notificación de máximo permitido.

Se actualizan mediante polling simple.

## 23. Comunicados globales

El Administrador puede crear avisos internos con título, mensaje, fecha de publicación, vencimiento, destinatarios, prioridad y estado.

## 24. Mensajería

### Familias y preceptores

Mensajería privada entre Padre/Tutor y Preceptor. El alumno no participa y el Administrador no debe leer estas conversaciones libremente.

### Comunicación institucional

El Administrador puede comunicarse con Directivos, Preceptores y otros Administradores. Cada conversación queda restringida a sus participantes.

La mensajería usa polling cada 10 o 15 segundos. Los comunicados globales permanecen separados.

## 25. Reportes

Filtros principales:

- curso;
- división;
- turno;
- especialidad;
- materia;
- preceptor;
- fecha desde;
- fecha hasta;
- tipo de registro;
- estado de asistencia.

Tipos: presentes, ausentes, llegadas tarde, ausentes con presente, justificados y retiros anticipados.

## 26. Panel del Administrador

Secciones:

- Panel.
- Gestión de Cursos.
- Usuarios.
- Asistencias.
- Mensajes institucionales.
- Reportes.
- Comunicados Globales.
- Gestión Técnica.
- Perfil.
- Cerrar sesión.

## 27. Panel del Directivo

Secciones:

- Dashboard.
- Solicitudes de acceso.
- Vinculaciones.
- Reemplazos.
- Asistencia institucional.
- Alertas y telecomunicados.
- Notificaciones.
- Mensajes institucionales.
- Perfil.

## 28. Panel del Preceptor

Secciones:

- Dashboard.
- Tomar asistencia.
- Historial.
- Justificaciones.
- Retiros anticipados.
- Mensajes privados.
- Notificaciones.
- Perfil.

## 29. Panel del Alumno

Secciones:

- Dashboard.
- Historial.
- Porcentaje de asistencia.
- Asignar Padre/Tutor.
- Notificaciones.
- Perfil.

## 30. Panel del Padre/Tutor

Secciones:

- Resumen del alumno.
- Historial.
- Justificaciones.
- Mensajes privados.
- Notificaciones.
- Perfil.

## 31. Reportes, gráficos y exportación

Los gráficos pueden generarse con Chart.js. La exportación a PDF puede implementarse con Dompdf, mPDF o TCPDF. Si se complica, puede quedar como mejora futura.

## 32. PWA

Debe incluir `manifest.json`, `service-worker.js`, iconos, nombre de la aplicación, colores institucionales, diseño responsive y opción de instalación.

La PWA no garantiza funcionamiento completo sin conexión; el acceso a datos actualizados requiere conexión con el servidor.

## 33. Diseño visual

La interfaz debe ser moderna, institucional, clara, sencilla, responsive y accesible.

Paleta principal:

- `#1A2B4C`
- `#071D3A`
- `#006397`
- `#3498DB`
- `#6C757D`
- `#F8F9FA`
- `#FFFFFF`
- `#28A745`
- `#DC3545`

Los bocetos son referencia visual; las reglas funcionales tienen prioridad.

## 34. Seguridad

Debe aplicar sesiones PHP, validación por rol, PDO, hash de contraseñas, escape de salida, validación de archivos, control por propiedad, baja lógica, auditoría, prevención de acceso por cambio manual de ID y protección CSRF cuando se implemente completamente.

## 35. Tablas principales

- instituciones;
- usuarios;
- especialidades;
- cursos;
- curso_turnos;
- materias;
- asignaciones_materias;
- alumno_cursos;
- preceptor_cursos;
- solicitudes_registro;
- vinculaciones;
- registros_asistencia;
- detalles_asistencia;
- resumen_asistencia_diaria;
- auditoria_asistencias;
- retiros_anticipados;
- justificaciones;
- reemplazos;
- conversaciones;
- conversacion_participantes;
- mensajes;
- notificaciones;
- comunicados;
- configuracion_sistema;
- backups_registro;
- log_actividad.

Los nombres exactos deben coincidir con el SQL real.

## 36. Pruebas necesarias

- login y logout;
- redirección por rol;
- usuarios;
- aprobación de alumnos;
- vinculación de tutores;
- cursos;
- toma de asistencia;
- edición y anulación;
- cálculo de faltas;
- cierre mensual;
- retiros;
- justificaciones;
- reemplazos;
- mensajes;
- comunicados;
- reportes;
- permisos;
- responsive;
- instalación PWA.

## 37. Estado actual y próximas etapas

Orden recomendado:

1. Autenticación y sesiones.
2. Dashboard del Administrador.
3. Gestión de Usuarios.
4. Gestión de Cursos.
5. Historial de Asistencias.
6. Mensajería institucional.
7. Reportes.
8. Comunicados Globales.
9. Gestión Técnica.
10. Módulo Directivo.
11. Módulo Preceptor.
12. Módulo Alumno.
13. Módulo Padre/Tutor.
14. PWA y pruebas finales.

## 38. Conclusión

El Sistema de Gestión de Asistencia Escolar permitirá mejorar el control de asistencia, reducir errores, agilizar consultas y fortalecer la comunicación institucional.

La plataforma centraliza usuarios, cursos, materias, asistencias, faltas, justificaciones, retiros, mensajes, alertas y reportes.

El uso de roles, auditoría, baja lógica y consultas seguras permite mantener la información organizada y protegida. La implementación como PWA facilita el acceso desde computadoras y celulares.
