# reglas_del_sistema.md

# Reglas del Sistema de Gestión de Asistencia Escolar

Este archivo contiene las reglas principales que debe respetar el sistema. Estas reglas tienen prioridad sobre versiones anteriores del informe o del backend.

## 1. Tecnología general

El sistema será una PWA escolar desarrollada en PHP nativo, MySQL y estructura MVC simple.

Debe funcionar en el servidor institucional GEdeCon.

Se utilizarán sesiones PHP nativas para login y control de roles.

No usar JWT en esta primera versión.

Las notificaciones, mensajes y alertas deben actualizarse mediante polling simple cada cierto intervalo de tiempo.

No usar WebSockets.

El sistema debe tener diseño responsive para funcionar en computadora y celular.

## 2. Roles del sistema

Los roles principales son:

Administrador.

Directivo.

Preceptor.

Alumno.

Padre/Tutor.

Cada rol debe tener permisos diferentes.

Ningún usuario debe poder acceder a funciones que no correspondan a su rol.

## 3. Permisos por rol

## Administrador

El Administrador tiene permisos completos sobre el sistema.

Puede crear, editar, borrar, auditar, corregir y configurar.

Puede gestionar usuarios, cursos, materias, asistencias, reportes, parámetros técnicos, comunicados globales y mantenimiento.

Puede realizar correcciones excepcionales sobre asistencias de meses cerrados.

## Directivo

El Directivo supervisa la información institucional.

Puede aprobar o rechazar solicitudes de alumnos.

Puede aprobar o rechazar vinculaciones de padres/tutores.

Puede asignar y gestionar reemplazos de preceptores.

Puede consultar asistencia institucional.

Puede ver alertas, telecomunicados y notificaciones.

No debe tener funciones técnicas como backups, configuración avanzada o mantenimiento interno.

No debe editar asistencias como tarea normal.

## Preceptor

El Preceptor toma asistencia.

Puede registrar asistencia por materia.

Puede modificar asistencias solo dentro del mes activo.

Cuando el mes se cierra, el Preceptor ya no puede modificar esas asistencias.

Puede registrar retiros anticipados.

Puede aprobar o rechazar justificaciones.

Puede consultar historial de asistencias de sus cursos y materias asignadas.

Puede comunicarse por mensaje privado con padres/tutores.

## Alumno

El Alumno solo consulta su información.

Puede ver historial de asistencia, porcentaje de asistencia, llegadas tarde, retiros, avisos y notificaciones.

No puede modificar asistencias.

No puede chatear directamente con preceptores.

Puede iniciar la vinculación de un Padre/Tutor desde su perfil.

## Padre/Tutor

El Padre/Tutor solo puede ver información del alumno vinculado.

Puede consultar asistencia, faltas, llegadas tarde, retiros anticipados, justificaciones, alertas y notificaciones.

Puede enviar justificaciones.

Puede comunicarse por mensaje privado con el Preceptor.

No puede ver información de alumnos no vinculados.

## 4. Registro de usuarios

## Alumno

El Alumno se registra solo desde la pantalla inicial.

El alumno debe cargar:

Nombre.

Apellido.

Correo electrónico.

Contraseña.

Curso.

División.

Turno.

Especialidad si corresponde.

No se debe pedir DNI al alumno.

La cuenta del alumno queda en estado pendiente.

El Directivo aprueba o rechaza la solicitud.

Cuando el Directivo aprueba, el alumno puede ingresar al sistema.

## Padre/Tutor

El Padre/Tutor no se registra libremente desde cero.

Primero debe existir un alumno aprobado.

Desde la cuenta del alumno debe existir una sección llamada “Asignar Padre/Tutor” o “Vincular Padre/Tutor”.

El alumno carga los datos del padre/tutor:

Nombre.

Apellido.

DNI.

Correo electrónico.

Teléfono.

Relación con el alumno: padre, madre o tutor.

Esa carga genera una solicitud de vinculación para el Directivo.

El Directivo aprueba o rechaza la vinculación.

Si es posible implementar link por email, usar esa opción.

Si el link por email es demasiado complejo, usar la versión simple: el Directivo aprueba la vinculación y el Padre/Tutor crea su contraseña en una pantalla de “Primer acceso”.

## Preceptores, Directivos y Administradores

Estos usuarios no se registran desde formulario público.

Los crea el Administrador.

A estos usuarios sí se les pide DNI.

Se debe pedir:

Nombre.

Apellido.

DNI.

Correo institucional.

Teléfono.

Rol.

Cargo o turno según corresponda.

Cursos o materias asignadas si corresponde.

Las contraseñas no deben ser visibles para otros usuarios.

## 5. Asistencia por materia y bloques horarios

La asistencia se toma por materia, al inicio de la misma o del cambio de materia.

Si una materia dura varias horas seguidas, se toma una sola asistencia al inicio.

La asistencia debe guardar:

Fecha.

Curso.

División.

Turno.

Materia.

Bloque horario (primera_hora o segunda_hora).

Horario o módulo.

Preceptor que registra.

Lista de alumnos.

Estado de cada alumno.

### Bloques horarios oficiales:
- **Turno mañana**:
  - 1ra hora: 07:35 a 09:35
  - Recreo: 09:35 a 09:45
  - 2da hora: 09:45 a 11:55
- **Almuerzo**: 11:55 a 12:55
- **Turno tarde**:
  - 1ra hora: 12:55 a 14:55
  - Recreo: 14:55 a 15:15
  - 2da hora: 15:15 a 17:15
- **Turno vespertino**:
  - 1ra hora: 17:35 a 19:35
  - Recreo: 19:35 a 19:55
  - 2da hora: 19:55 a 21:45

## 6. Estados de asistencia (alumno)

Los estados posibles son:
- Presente
- Ausente
- Llegada tarde
- Ausente con presente
- Justificado
- Retirado anticipado

## 7. Regla de 15 minutos

Existe un margen de 15 minutos desde el inicio de la materia.
- Si el alumno llega dentro de los 15 minutos: "Llegada tarde"
- Si llega después de los 15 minutos:
  - Curso de turno único: "Ausente con presente"
  - Curso de doble turno: 0.5 falta por ese turno

## 8. Cálculo de inasistencias

### Regla fundamental
Máximo 1 falta por alumno y día. Nunca más de 1.

### Cálculo por curso

#### Turno único:
- Presente → 0
- Llegada tarde → 0.25
- Retirado anticipado → 0.5
- Ausente con presente → 1
- Ausente → 1
- Justificado → 1 (queda marcado como justificado pero suma falta)

#### Doble turno:
- Presente en ambos → 0
- Llegada tarde en un turno → 0.25
- Retirado anticipado en un turno → 0.5
- Ausente en un turno → 0.5
- Ausente en ambos → 1
- Justificado → suma como falta normal

### Casos especiales dentro de un mismo turno:
- 1ra hora ausente, 2da hora presente → 1 falta para ese turno
- 1ra hora presente, 2da hora ausente → 0.5 faltas (retiro anticipado)

## 9. Estados del registro general de asistencia
- Abierto: preceptor inició pero no finalizó
- Cerrado/finalizado: asistencia completada
- Modificado/corregido: editado por administrador
- Anulada: dado de baja (no suma faltas, visible para auditoría)

## 10. Edición y anulación de asistencias
- El Administrador puede editar TODO el registro (curso, materia, turno, bloque, fecha, preceptor, estado general, estados de alumnos, observaciones)
- El Administrador puede anular asistencias (baja lógica, no física)
- Toda edición o anulación queda registrada en auditoría
- Al editar, se recalculan automáticamente las faltas del alumno en ese día

## 11. Auditoría
Se registra:
- Usuario admin que realizó la acción
- Fecha y hora
- Registro de asistencia afectado
- Alumno afectado (si aplica)
- Campo modificado
- Valor anterior
- Valor nuevo
- Observaciones

## 12. Resumen de asistencia diaria
Se guarda un resumen diario por alumno, curso y turno con el total de faltas calculadas y el detalle del cálculo.

## 13. Retiros anticipados
El sistema debe permitir registrar retiros anticipados. Datos obligatorios:
- Alumno
- Fecha
- Horario del retiro
- Motivo
- Si el alumno es mayor o menor de edad
- Si es menor, nombre y apellido del adulto que lo retira (autorizado en el legajo)
- El retiro anticipado impacta en la asistencia según las reglas

## 14. Justificaciones
El Padre/Tutor puede enviar justificaciones. El Preceptor aprueba/rechaza. El justificado suma falta pero queda marcado.

## 15. Reemplazos de preceptores
Los reemplazos los asigna el Directivo. Registra quién cubrió a quién.

## 16. Alertas y telecomunicados
Matriz de alertas:
- Cada llegada tarde: aviso al Padre/Tutor
- 10 faltas: aviso de inasistencias
- 20 faltas: alerta para acta formal
- 28 faltas: notificación de máximas permitidas

## 17. Mensajería
- **Mensajería privada familiar**: Solo entre Padre/Tutor y Preceptor. No canal general.
- **Mensajería institucional**: 
  - Administrador ↔ Directivo
  - Administrador ↔ Preceptor
  - Administrador ↔ Administrador
- Cada usuario solo ve las conversaciones en las que participa.
- El Administrador no puede ver las conversaciones privadas entre Padre/Tutor y Preceptor.

## 18. Comunicado Global
Aviso interno del Administrador, visible al ingresar o en panel de notificaciones.
