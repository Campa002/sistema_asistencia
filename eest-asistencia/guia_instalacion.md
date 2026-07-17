Guía de instalación del sistema

Esta guía está pensada para una persona que nunca instaló un proyecto PHP con XAMPP.

1. Instalar XAMPP

Descargar e instalar XAMPP para Windows. Durante la instalación, asegurarse de incluir:

Apache
MySQL
PHP
phpMyAdmin

La ubicación habitual es:

C:\xampp
2. Copiar la carpeta del proyecto

Buscar la carpeta completa del sistema, llamada:

eest-asistencia

Copiarla dentro de:

C:\xampp\htdocs

La ruta final debe quedar exactamente así:

C:\xampp\htdocs\eest-asistencia

No colocarla dentro de otra carpeta duplicada. Esto estaría mal:

C:\xampp\htdocs\eest-asistencia\eest-asistencia
3. Iniciar XAMPP

Abrir:

XAMPP Control Panel

Presionar Start en:

Apache
MySQL

Ambos deberían quedar en color verde.

Si Apache o MySQL no arrancan, probablemente otro programa esté usando sus puertos.

4. Crear la base de datos

Abrir el navegador e ingresar:

http://localhost/phpmyadmin

Luego:

Presionar Nueva.
En nombre de la base escribir:
eest_asistencia
Seleccionar una cotejación como:
utf8mb4_unicode_ci
Presionar Crear.

El nombre debe ser exactamente:

eest_asistencia
5. Importar la base de datos

Dentro de phpMyAdmin:

Seleccionar la base eest_asistencia.
Presionar la pestaña Importar.
Presionar Seleccionar archivo.
Buscar la carpeta:
C:\xampp\htdocs\eest-asistencia\sql
Seleccionar el archivo SQL principal y más actualizado del sistema.

Debería ser un archivo parecido a:

eest_asistencia_completa_final_corregida.sql

o el archivo completo que se encuentre actualmente dentro de sql.

Presionar Importar o Continuar.

Cuando termine, deberían aparecer tablas como:

usuarios
cursos
registros_asistencia
detalles_asistencia
resumen_asistencia_diaria
mensajes
conversaciones

No importar varios archivos completos uno encima de otro, porque pueden aparecer tablas duplicadas.

6. Revisar la conexión

Abrir el archivo de conexión del proyecto. Puede encontrarse en:

C:\xampp\htdocs\eest-asistencia\config\database.php

o en:

C:\xampp\htdocs\eest-asistencia\database.php

Para una instalación normal de XAMPP debería usar valores similares a estos:

$host = 'localhost';
$dbname = 'eest_asistencia';
$username = 'root';
$password = '';

En XAMPP, MySQL normalmente utiliza:

Usuario: root
Contraseña: vacía

No cambiar estos datos si la conexión ya funciona.

7. Abrir el sistema

Con Apache y MySQL encendidos, abrir:

http://localhost/eest-asistencia/public/

También puede abrirse directamente:

http://localhost/eest-asistencia/public/index.php

Nunca abrir los archivos haciendo doble clic desde Windows, porque PHP necesita ejecutarse mediante Apache.

Incorrecto:

C:\xampp\htdocs\eest-asistencia\public\index.php

Correcto:

http://localhost/eest-asistencia/public/index.php
Usuarios de prueba

Todos estos usuarios utilizan la contraseña:

password
Rol	Correo	Contraseña
Administrador	admin@eest.edu.ar	password
Alumno	usuario.t1vl@gmail.com	password
Preceptor	preceptor@abc.gob.ar	password
Directivo	directivo@abc.gob.ar	password
Padre/Tutor	padretutor@gmail.com	password

Después de iniciar sesión, cada usuario debería ser enviado automáticamente al panel de su rol.

Rutas esperadas

Administrador:

index.php?page=admin/dashboard

Alumno:

index.php?page=alumnos/alumnos_owen

Preceptor:

index.php?page=preceptor/preceptor

Directivo:

index.php?page=directivo/directivo

Padre/Tutor:

index.php?page=padre_tutor/padre-tutor
8. Probar el cierre de sesión

En cada panel:

Presionar Cerrar sesión.
Confirmar que vuelve al login.
Presionar Atrás en el navegador.
Confirmar que el panel no vuelve a quedar accesible.
Problemas comunes

Apache no inicia: cerrar programas como IIS, Skype u otros servidores locales.

MySQL no inicia: puede estar ocupado el puerto 3306.

Página en blanco: activar errores PHP temporalmente o revisar el registro de Apache.

Base de datos no encontrada: confirmar que se llama exactamente:

eest_asistencia

CSS o JS no cargan: recargar con:

Ctrl + F5

Error de usuario o contraseña: comprobar que el usuario esté activo y que el hash haya sido generado con password_hash().