-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2026 a las 23:22:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `eest_asistencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_cursos`
--

CREATE TABLE `alumno_cursos` (
  `id` int(10) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `ciclo_lectivo` year(4) NOT NULL,
  `estado` enum('activo','inactivo','egresado','trasladado') NOT NULL DEFAULT 'activo',
  `fecha_matricula` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumno_cursos`
--

INSERT INTO `alumno_cursos` (`id`, `alumno_id`, `curso_id`, `ciclo_lectivo`, `estado`, `fecha_matricula`, `created_at`) VALUES
(1, 4, 1, '2026', 'activo', '2026-03-01', '2026-06-09 03:57:23'),
(100, 100, 1, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(101, 101, 10, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(102, 102, 11, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(103, 103, 12, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(104, 104, 13, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(105, 105, 14, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(106, 106, 15, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(107, 107, 16, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(108, 108, 17, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(109, 109, 18, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(110, 110, 19, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(111, 111, 20, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(112, 112, 21, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(113, 113, 22, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(114, 114, 1, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(115, 115, 10, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(116, 116, 11, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(117, 117, 12, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(118, 118, 13, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(119, 119, 14, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(120, 120, 15, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(121, 121, 16, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(122, 122, 17, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(123, 123, 18, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(124, 124, 19, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(125, 125, 20, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(126, 126, 21, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(127, 127, 22, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(128, 128, 1, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(129, 129, 10, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(130, 130, 11, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(131, 131, 12, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(132, 132, 13, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(133, 133, 14, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(134, 134, 15, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(135, 135, 16, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(136, 136, 17, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(137, 137, 18, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(138, 138, 19, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00'),
(139, 139, 20, '2026', 'activo', '2026-03-01', '2026-03-05 11:10:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_materias`
--

CREATE TABLE `asignaciones_materias` (
  `id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `materia_id` int(10) UNSIGNED NOT NULL,
  `preceptor_id` int(10) UNSIGNED NOT NULL,
  `modulo_horario` varchar(50) NOT NULL COMMENT 'Ej: 1ra Hora, 2da Hora',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `dia_semana` tinyint(3) UNSIGNED NOT NULL COMMENT '1=Lunes, 2=Martes, ..., 7=Domingo',
  `ciclo_lectivo` year(4) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignaciones_materias`
--

INSERT INTO `asignaciones_materias` (`id`, `curso_id`, `materia_id`, `preceptor_id`, `modulo_horario`, `hora_inicio`, `hora_fin`, `dia_semana`, `ciclo_lectivo`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, '1ra Hora', '08:00:00', '09:20:00', 1, '2026', 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(2, 1, 3, 3, '2da Hora', '09:30:00', '10:50:00', 1, '2026', 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(100, 1, 20, 3, '2da Hora', '09:30:00', '10:50:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(101, 1, 23, 3, '3ra Hora', '11:00:00', '12:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(102, 1, 24, 3, '4ta Hora', '13:00:00', '14:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(103, 1, 35, 3, '5ta Hora', '14:30:00', '15:50:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(104, 1, 25, 3, '1ra Hora', '08:00:00', '09:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(105, 10, 23, 10, '3ra Hora', '11:00:00', '12:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(106, 10, 24, 10, '4ta Hora', '13:00:00', '14:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(107, 10, 35, 10, '5ta Hora', '14:30:00', '15:50:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(108, 10, 30, 10, '1ra Hora', '08:00:00', '09:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(109, 10, 31, 10, '2da Hora', '09:30:00', '10:50:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(110, 11, 24, 11, '4ta Hora', '13:00:00', '14:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(111, 11, 35, 11, '5ta Hora', '14:30:00', '15:50:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(112, 11, 30, 11, '1ra Hora', '08:00:00', '09:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(113, 11, 31, 11, '2da Hora', '09:30:00', '10:50:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(114, 11, 32, 11, '3ra Hora', '11:00:00', '12:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(115, 12, 35, 12, '5ta Hora', '14:30:00', '15:50:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(116, 12, 30, 12, '1ra Hora', '08:00:00', '09:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(117, 12, 31, 12, '2da Hora', '09:30:00', '10:50:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(118, 12, 32, 12, '3ra Hora', '11:00:00', '12:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(119, 12, 33, 12, '4ta Hora', '13:00:00', '14:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(120, 13, 30, 13, '1ra Hora', '08:00:00', '09:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(121, 13, 31, 13, '2da Hora', '09:30:00', '10:50:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(122, 13, 32, 13, '3ra Hora', '11:00:00', '12:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(123, 13, 33, 13, '4ta Hora', '13:00:00', '14:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(124, 13, 6, 13, '5ta Hora', '14:30:00', '15:50:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(125, 14, 31, 14, '2da Hora', '09:30:00', '10:50:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(126, 14, 32, 14, '3ra Hora', '11:00:00', '12:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(127, 14, 33, 14, '4ta Hora', '13:00:00', '14:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(128, 14, 6, 14, '5ta Hora', '14:30:00', '15:50:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(129, 14, 1, 14, '1ra Hora', '08:00:00', '09:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(130, 15, 27, 3, '3ra Hora', '11:00:00', '12:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(131, 15, 28, 3, '4ta Hora', '13:00:00', '14:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(132, 15, 29, 3, '5ta Hora', '14:30:00', '15:50:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(133, 15, 1, 3, '1ra Hora', '08:00:00', '09:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(134, 15, 20, 3, '2da Hora', '09:30:00', '10:50:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(135, 16, 33, 10, '4ta Hora', '13:00:00', '14:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(136, 16, 6, 10, '5ta Hora', '14:30:00', '15:50:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(137, 16, 1, 10, '1ra Hora', '08:00:00', '09:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(138, 16, 20, 10, '2da Hora', '09:30:00', '10:50:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(139, 16, 23, 10, '3ra Hora', '11:00:00', '12:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(140, 17, 29, 11, '5ta Hora', '14:30:00', '15:50:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(141, 17, 1, 11, '1ra Hora', '08:00:00', '09:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(142, 17, 20, 11, '2da Hora', '09:30:00', '10:50:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(143, 17, 23, 11, '3ra Hora', '11:00:00', '12:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(144, 17, 24, 11, '4ta Hora', '13:00:00', '14:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(145, 18, 1, 12, '1ra Hora', '08:00:00', '09:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(146, 18, 20, 12, '2da Hora', '09:30:00', '10:50:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(147, 18, 23, 12, '3ra Hora', '11:00:00', '12:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(148, 18, 24, 12, '4ta Hora', '13:00:00', '14:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(149, 18, 35, 12, '5ta Hora', '14:30:00', '15:50:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(150, 19, 20, 13, '2da Hora', '09:30:00', '10:50:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(151, 19, 23, 13, '3ra Hora', '11:00:00', '12:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(152, 19, 24, 13, '4ta Hora', '13:00:00', '14:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(153, 19, 35, 13, '5ta Hora', '14:30:00', '15:50:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(154, 19, 25, 13, '1ra Hora', '08:00:00', '09:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(155, 20, 23, 14, '3ra Hora', '11:00:00', '12:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(156, 20, 24, 14, '4ta Hora', '13:00:00', '14:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(157, 20, 35, 14, '5ta Hora', '14:30:00', '15:50:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(158, 20, 30, 14, '1ra Hora', '08:00:00', '09:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(159, 20, 31, 14, '2da Hora', '09:30:00', '10:50:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(160, 21, 24, 3, '4ta Hora', '13:00:00', '14:20:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(161, 21, 35, 3, '5ta Hora', '14:30:00', '15:50:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(162, 21, 25, 3, '1ra Hora', '08:00:00', '09:20:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(163, 21, 26, 3, '2da Hora', '09:30:00', '10:50:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(164, 21, 27, 3, '3ra Hora', '11:00:00', '12:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(165, 22, 35, 10, '5ta Hora', '14:30:00', '15:50:00', 1, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(166, 22, 25, 10, '1ra Hora', '08:00:00', '09:20:00', 2, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(167, 22, 26, 10, '2da Hora', '09:30:00', '10:50:00', 3, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(168, 22, 27, 10, '3ra Hora', '11:00:00', '12:20:00', 4, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(169, 22, 28, 10, '4ta Hora', '13:00:00', '14:20:00', 5, '2026', 1, '2026-03-10 11:00:00', '2026-03-10 11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_asistencias`
--

CREATE TABLE `auditoria_asistencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `registro_id` int(10) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `accion` varchar(100) NOT NULL,
  `campo_modificado` varchar(100) DEFAULT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria_asistencias`
--

INSERT INTO `auditoria_asistencias` (`id`, `registro_id`, `alumno_id`, `usuario_id`, `accion`, `campo_modificado`, `valor_anterior`, `valor_nuevo`, `observaciones`, `created_at`) VALUES
(1, 1153, 120, 1, 'editar', 'estado', 'presente', 'ausente', '', '2026-06-11 22:42:58'),
(2, 1153, 106, 1, 'editar', 'estado', 'presente', 'ausente', 'porque si', '2026-06-11 22:43:20'),
(3, 1153, NULL, 1, 'editar', 'preceptor_id', '13', '11', '', '2026-06-11 22:44:35'),
(4, 1153, 120, 1, 'editar', 'estado', 'ausente', 'llegada_tarde', '', '2026-06-12 01:23:29'),
(5, 1153, NULL, 1, 'editar', 'estado', 'abierta', 'modificada', '', '2026-06-12 01:23:29'),
(6, 1010, NULL, 1, 'anular', 'estado', 'abierta', 'anulada', 'mala toma de asistencia\r\n', '2026-06-12 01:41:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backups_registro`
--

CREATE TABLE `backups_registro` (
  `id` int(10) UNSIGNED NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `tamano` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'en bytes',
  `tipo` enum('manual','automatico') NOT NULL DEFAULT 'manual',
  `creado_por` int(10) UNSIGNED DEFAULT NULL,
  `estado` enum('completado','fallido','en_proceso') NOT NULL DEFAULT 'completado',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `backups_registro`
--

INSERT INTO `backups_registro` (`id`, `archivo`, `tamano`, `tipo`, `creado_por`, `estado`, `notas`, `created_at`) VALUES
(100, 'backup_eest_asistencia_2026_06_01.sql', 2048576, 'automatico', 1, 'completado', 'Backup automático de prueba', '2026-06-02 02:00:00'),
(101, 'backup_eest_asistencia_2026_06_08.sql', 2560000, 'manual', 1, 'completado', 'Backup manual previo a entrega', '2026-06-08 21:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloques_horarios`
--

CREATE TABLE `bloques_horarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `turno` enum('mañana','tarde','vespertino') NOT NULL,
  `bloque_horario` enum('primera_hora','segunda_hora') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bloques_horarios`
--

INSERT INTO `bloques_horarios` (`id`, `turno`, `bloque_horario`, `hora_inicio`, `hora_fin`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'mañana', 'primera_hora', '07:35:00', '09:35:00', '1ra hora turno mañana', 'activo', '2026-06-11 22:13:05', '2026-06-11 22:13:05'),
(2, 'mañana', 'segunda_hora', '09:45:00', '11:55:00', '2da hora turno mañana', 'activo', '2026-06-11 22:13:05', '2026-06-11 22:13:05'),
(3, 'tarde', 'primera_hora', '12:55:00', '14:55:00', '1ra hora turno tarde', 'activo', '2026-06-11 22:13:05', '2026-06-11 22:13:05'),
(4, 'tarde', 'segunda_hora', '15:15:00', '17:15:00', '2da hora turno tarde', 'activo', '2026-06-11 22:13:05', '2026-06-11 22:13:05'),
(5, 'vespertino', 'primera_hora', '17:35:00', '19:35:00', '1ra hora turno vespertino', 'activo', '2026-06-11 22:13:05', '2026-06-11 22:13:05'),
(6, 'vespertino', 'segunda_hora', '19:55:00', '21:45:00', '2da hora turno vespertino', 'activo', '2026-06-11 22:13:05', '2026-06-11 22:13:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierres_mensuales`
--

CREATE TABLE `cierres_mensuales` (
  `id` int(10) UNSIGNED NOT NULL,
  `institucion_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `anio` year(4) NOT NULL,
  `mes` tinyint(3) UNSIGNED NOT NULL COMMENT '1=Enero, ..., 12=Diciembre',
  `cerrado_por` int(10) UNSIGNED NOT NULL,
  `fecha_cierre` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cierres_mensuales`
--

INSERT INTO `cierres_mensuales` (`id`, `institucion_id`, `anio`, `mes`, `cerrado_por`, `fecha_cierre`, `observaciones`, `created_at`) VALUES
(100, 1, '2026', 5, 1, '2026-06-01 15:00:00', 'Cierre mensual de mayo generado para pruebas', '2026-06-01 15:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id` int(10) UNSIGNED NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` enum('texto','numero','booleano','json') NOT NULL DEFAULT 'texto',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `updated_at`) VALUES
(1, 'ciclo_lectivo_actual', '2026', 'Año lectivo en curso', 'numero', '2026-06-09 03:57:23'),
(2, 'porcentaje_minimo_asistencia', '75', 'Porcentaje mínimo requerido', 'numero', '2026-06-09 03:57:23'),
(3, 'maximo_tardanzas', '3', 'Tardanzas antes de equivaler falta', 'numero', '2026-06-09 03:57:23'),
(4, 'email_smtp_host', '', 'Host SMTP para envío de emails', 'texto', '2026-06-09 03:57:23'),
(5, 'email_smtp_port', '587', 'Puerto SMTP', 'numero', '2026-06-09 03:57:23'),
(6, 'email_smtp_user', '', 'Usuario SMTP', 'texto', '2026-06-09 03:57:23'),
(7, 'email_smtp_pass', '', 'Contraseña SMTP (encriptada)', 'texto', '2026-06-09 03:57:23'),
(8, 'email_from', 'no-reply@eest1.edu.ar', 'Email remitente', 'texto', '2026-06-09 03:57:23'),
(9, 'version_sistema', '3.0.0', 'Versión actual del sistema', 'texto', '2026-06-09 03:57:23'),
(10, 'estado_sistema', 'operativo', 'Estado del sistema', 'texto', '2026-06-09 03:57:23'),
(11, 'mantenimiento_modo', '0', 'Modo mantenimiento activo', 'booleano', '2026-06-09 03:57:23'),
(12, 'regla_15_minutos', '1', 'Aplicar regla de 15 minutos para llegadas tarde', 'booleano', '2026-06-09 03:57:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversaciones`
--

CREATE TABLE `conversaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `tipo` enum('directo','grupal') NOT NULL DEFAULT 'directo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conversaciones`
--

INSERT INTO `conversaciones` (`id`, `titulo`, `tipo`, `created_at`, `updated_at`) VALUES
(100, 'Consulta sobre asistencia - Alumno 100', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(101, 'Consulta sobre asistencia - Alumno 101', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(102, 'Consulta sobre asistencia - Alumno 102', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(103, 'Consulta sobre asistencia - Alumno 103', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(104, 'Consulta sobre asistencia - Alumno 104', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(105, 'Consulta sobre asistencia - Alumno 105', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(106, 'Consulta sobre asistencia - Alumno 106', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(107, 'Consulta sobre asistencia - Alumno 107', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(108, 'Consulta sobre asistencia - Alumno 108', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(109, 'Consulta sobre asistencia - Alumno 109', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(110, 'Consulta sobre asistencia - Alumno 110', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(111, 'Consulta sobre asistencia - Alumno 111', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(112, 'Consulta sobre asistencia - Alumno 112', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(113, 'Consulta sobre asistencia - Alumno 113', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(114, 'Consulta sobre asistencia - Alumno 114', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(115, 'Consulta sobre asistencia - Alumno 115', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(116, 'Consulta sobre asistencia - Alumno 116', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(117, 'Consulta sobre asistencia - Alumno 117', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(118, 'Consulta sobre asistencia - Alumno 118', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(119, 'Consulta sobre asistencia - Alumno 119', 'directo', '2026-06-01 12:00:00', '2026-06-01 12:15:00'),
(120, NULL, 'directo', '2026-06-12 04:31:39', '2026-06-12 04:31:39'),
(121, NULL, 'directo', '2026-06-12 06:05:14', '2026-06-12 06:05:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversacion_participantes`
--

CREATE TABLE `conversacion_participantes` (
  `id` int(10) UNSIGNED NOT NULL,
  `conversacion_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `ultimo_leido` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conversacion_participantes`
--

INSERT INTO `conversacion_participantes` (`id`, `conversacion_id`, `usuario_id`, `ultimo_leido`, `activo`, `created_at`) VALUES
(100, 100, 200, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(101, 100, 3, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(102, 101, 201, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(103, 101, 10, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(104, 102, 202, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(105, 102, 11, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(106, 103, 203, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(107, 103, 12, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(108, 104, 204, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(109, 104, 13, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(110, 105, 205, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(111, 105, 14, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(112, 106, 206, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(113, 106, 3, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(114, 107, 207, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(115, 107, 10, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(116, 108, 208, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(117, 108, 11, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(118, 109, 209, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(119, 109, 12, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(120, 110, 210, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(121, 110, 13, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(122, 111, 211, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(123, 111, 14, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(124, 112, 212, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(125, 112, 3, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(126, 113, 213, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(127, 113, 10, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(128, 114, 214, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(129, 114, 3, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(130, 115, 215, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(131, 115, 10, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(132, 116, 216, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(133, 116, 11, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(134, 117, 217, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(135, 117, 12, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(136, 118, 218, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(137, 118, 13, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(138, 119, 219, '2026-06-01 09:15:00', 1, '2026-06-01 12:00:00'),
(139, 119, 14, '2026-06-01 09:10:00', 1, '2026-06-01 12:00:00'),
(140, 120, 1, '2026-06-12 15:54:44', 1, '2026-06-12 04:31:39'),
(141, 120, 240, NULL, 1, '2026-06-12 04:31:39'),
(142, 121, 1, '2026-06-12 15:55:31', 1, '2026-06-12 06:05:14'),
(143, 121, 13, NULL, 1, '2026-06-12 06:05:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(10) UNSIGNED NOT NULL,
  `institucion_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `anio` tinyint(3) UNSIGNED NOT NULL COMMENT '1 al 7',
  `division` tinyint(3) UNSIGNED NOT NULL COMMENT '1ra=1, 2da=2, etc.',
  `turno` enum('mañana','tarde','vespertino') NOT NULL DEFAULT 'mañana',
  `especialidad_id` int(10) UNSIGNED NOT NULL,
  `aula` varchar(50) DEFAULT NULL,
  `ciclo_lectivo` year(4) NOT NULL,
  `estado` enum('activo','inactivo','archivado') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `institucion_id`, `anio`, `division`, `turno`, `especialidad_id`, `aula`, `ciclo_lectivo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 'mañana', 3, '401', '2026', 'activo', '2026-06-09 03:57:23', '2026-06-13 21:13:08'),
(3, 1, 1, 1, 'mañana', 1, '101', '2026', 'activo', '2026-06-09 03:57:23', '2026-06-09 06:15:23'),
(10, 1, 1, 2, 'mañana', 1, '102', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-09 06:15:23'),
(11, 1, 2, 1, 'mañana', 1, '201', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-09 06:15:23'),
(12, 1, 2, 2, 'mañana', 1, '202', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(13, 1, 3, 1, 'mañana', 1, '301', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-09 06:15:23'),
(14, 1, 3, 2, 'mañana', 1, '302', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(15, 1, 4, 2, 'mañana', 3, '402', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:13:08'),
(16, 1, 4, 3, 'tarde', 3, 'Lab E1', '2026', 'activo', '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(17, 1, 5, 1, 'tarde', 3, '501', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(18, 1, 5, 2, 'tarde', 3, 'Lab E2', '2026', 'activo', '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(19, 1, 6, 1, 'vespertino', 3, '601', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(20, 1, 6, 2, 'vespertino', 3, 'Lab E3', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(21, 1, 7, 1, 'vespertino', 3, '701', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(22, 1, 7, 2, 'vespertino', 2, '702', '2026', 'activo', '2026-03-01 11:00:00', '2026-06-13 21:22:39'),
(23, 1, 1, 3, 'mañana', 1, '103', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-10 05:42:31'),
(24, 1, 1, 4, 'tarde', 1, '104', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-13 21:22:39'),
(25, 1, 1, 5, 'tarde', 1, '105', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-13 21:22:39'),
(26, 1, 2, 3, 'mañana', 1, '203', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-10 05:42:31'),
(27, 1, 2, 4, 'tarde', 1, '204', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-10 05:42:31'),
(28, 1, 2, 5, 'tarde', 1, '205', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-10 05:42:31'),
(29, 1, 3, 3, 'tarde', 1, '303', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-13 21:22:39'),
(30, 1, 3, 4, 'tarde', 1, '304', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-10 05:42:31'),
(32, 1, 4, 4, 'vespertino', 2, 'Lab E4', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-13 21:22:39'),
(33, 1, 5, 3, 'vespertino', 2, '503', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-13 21:22:39'),
(34, 1, 6, 3, 'vespertino', 2, 'Lab E5', '2026', 'activo', '2026-06-10 05:42:31', '2026-06-13 21:22:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_turnos`
--

CREATE TABLE `curso_turnos` (
  `id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `turno` enum('mañana','tarde','vespertino') NOT NULL,
  `es_turno_principal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `curso_turnos`
--

INSERT INTO `curso_turnos` (`id`, `curso_id`, `turno`, `es_turno_principal`, `created_at`) VALUES
(27, 1, 'mañana', 1, '2026-06-13 21:22:39'),
(28, 3, 'mañana', 1, '2026-06-13 21:22:39'),
(29, 10, 'mañana', 1, '2026-06-13 21:22:39'),
(30, 11, 'mañana', 1, '2026-06-13 21:22:39'),
(31, 12, 'mañana', 1, '2026-06-13 21:22:39'),
(32, 13, 'mañana', 1, '2026-06-13 21:22:39'),
(33, 14, 'mañana', 1, '2026-06-13 21:22:39'),
(34, 15, 'mañana', 1, '2026-06-13 21:22:39'),
(35, 16, 'tarde', 1, '2026-06-13 21:22:39'),
(36, 17, 'tarde', 1, '2026-06-13 21:22:39'),
(37, 18, 'tarde', 1, '2026-06-13 21:22:39'),
(38, 19, 'vespertino', 1, '2026-06-13 21:22:39'),
(39, 20, 'vespertino', 1, '2026-06-13 21:22:39'),
(40, 21, 'vespertino', 1, '2026-06-13 21:22:39'),
(41, 22, 'vespertino', 1, '2026-06-13 21:22:39'),
(42, 23, 'mañana', 1, '2026-06-13 21:22:39'),
(43, 24, 'tarde', 1, '2026-06-13 21:22:39'),
(44, 25, 'tarde', 1, '2026-06-13 21:22:39'),
(45, 26, 'mañana', 1, '2026-06-13 21:22:39'),
(46, 27, 'tarde', 1, '2026-06-13 21:22:39'),
(47, 28, 'tarde', 1, '2026-06-13 21:22:39'),
(48, 29, 'tarde', 1, '2026-06-13 21:22:39'),
(49, 30, 'tarde', 1, '2026-06-13 21:22:39'),
(50, 32, 'vespertino', 1, '2026-06-13 21:22:39'),
(51, 33, 'vespertino', 1, '2026-06-13 21:22:39'),
(52, 34, 'vespertino', 1, '2026-06-13 21:22:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_asistencia`
--

CREATE TABLE `detalles_asistencia` (
  `id` int(10) UNSIGNED NOT NULL,
  `registro_id` int(10) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('presente','ausente','llegada_tarde','ausente_con_presente','justificado','retirado_anticipado') NOT NULL DEFAULT 'ausente',
  `hora_llegada` time DEFAULT NULL,
  `minutos_tarde` smallint(5) UNSIGNED DEFAULT NULL,
  `hora_retiro` time DEFAULT NULL,
  `observaciones` varchar(500) DEFAULT NULL,
  `modificado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalles_asistencia`
--

INSERT INTO `detalles_asistencia` (`id`, `registro_id`, `alumno_id`, `estado`, `hora_llegada`, `minutos_tarde`, `hora_retiro`, `observaciones`, `modificado`, `created_at`, `updated_at`) VALUES
(1000, 1000, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1001, 1000, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1002, 1000, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1003, 1000, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1004, 1001, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1005, 1001, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1006, 1001, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1007, 1001, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1008, 1002, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1009, 1002, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1010, 1002, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1011, 1003, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1012, 1003, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1013, 1003, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1014, 1004, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1015, 1004, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1016, 1004, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1017, 1005, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1018, 1005, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1019, 1005, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1020, 1006, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1021, 1006, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1022, 1006, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1023, 1007, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1024, 1007, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1025, 1007, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1026, 1008, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1027, 1008, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1028, 1008, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1029, 1009, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1030, 1009, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1031, 1009, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1032, 1010, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1033, 1010, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1034, 1010, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1035, 1011, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1036, 1011, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1037, 1011, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1038, 1012, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1039, 1012, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1040, 1012, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1041, 1013, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1042, 1013, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1043, 1013, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-25 11:05:00', '2026-05-25 11:05:00'),
(1044, 1014, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1045, 1014, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1046, 1014, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1047, 1014, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1048, 1015, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1049, 1015, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1050, 1015, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1051, 1015, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1052, 1016, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1053, 1016, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1054, 1016, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1055, 1017, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1056, 1017, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1057, 1017, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1058, 1018, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1059, 1018, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1060, 1018, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1061, 1019, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1062, 1019, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1063, 1019, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1064, 1020, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1065, 1020, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1066, 1020, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1067, 1021, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1068, 1021, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1069, 1021, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1070, 1022, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1071, 1022, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1072, 1022, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1073, 1023, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1074, 1023, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1075, 1023, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1076, 1024, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1077, 1024, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1078, 1024, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1079, 1025, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1080, 1025, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1081, 1025, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1082, 1026, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1083, 1026, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1084, 1026, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1085, 1027, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1086, 1027, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1087, 1027, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-26 11:05:00', '2026-05-26 11:05:00'),
(1088, 1028, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1089, 1028, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1090, 1028, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1091, 1028, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1092, 1029, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1093, 1029, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1094, 1029, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1095, 1029, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1096, 1030, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1097, 1030, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1098, 1030, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1099, 1031, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1100, 1031, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1101, 1031, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1102, 1032, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1103, 1032, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1104, 1032, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1105, 1033, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1106, 1033, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1107, 1033, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1108, 1034, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1109, 1034, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1110, 1034, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1111, 1035, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1112, 1035, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1113, 1035, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1114, 1036, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1115, 1036, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1116, 1036, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1117, 1037, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1118, 1037, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1119, 1037, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1120, 1038, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1121, 1038, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1122, 1038, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1123, 1039, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1124, 1039, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1125, 1039, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1126, 1040, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1127, 1040, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1128, 1040, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1129, 1041, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1130, 1041, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1131, 1041, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-27 11:05:00', '2026-05-27 11:05:00'),
(1132, 1042, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1133, 1042, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1134, 1042, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1135, 1042, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1136, 1043, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1137, 1043, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1138, 1043, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1139, 1043, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1140, 1044, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1141, 1044, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1142, 1044, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1143, 1045, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1144, 1045, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1145, 1045, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1146, 1046, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1147, 1046, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1148, 1046, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1149, 1047, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1150, 1047, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1151, 1047, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1152, 1048, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1153, 1048, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1154, 1048, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1155, 1049, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1156, 1049, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1157, 1049, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1158, 1050, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1159, 1050, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1160, 1050, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1161, 1051, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1162, 1051, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1163, 1051, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1164, 1052, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1165, 1052, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1166, 1052, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1167, 1053, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1168, 1053, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1169, 1053, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1170, 1054, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1171, 1054, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1172, 1054, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1173, 1055, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1174, 1055, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1175, 1055, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-28 11:05:00', '2026-05-28 11:05:00'),
(1176, 1056, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1177, 1056, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1178, 1056, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1179, 1056, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1180, 1057, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1181, 1057, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1182, 1057, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1183, 1057, 4, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1184, 1058, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1185, 1058, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1186, 1058, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1187, 1059, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1188, 1059, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1189, 1059, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1190, 1060, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1191, 1060, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1192, 1060, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1193, 1061, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1194, 1061, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1195, 1061, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1196, 1062, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1197, 1062, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1198, 1062, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1199, 1063, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1200, 1063, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1201, 1063, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1202, 1064, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1203, 1064, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1204, 1064, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1205, 1065, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1206, 1065, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1207, 1065, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1208, 1066, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1209, 1066, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1210, 1066, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1211, 1067, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1212, 1067, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1213, 1067, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1214, 1068, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1215, 1068, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1216, 1068, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1217, 1069, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1218, 1069, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1219, 1069, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-05-29 11:05:00', '2026-05-29 11:05:00'),
(1220, 1070, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1221, 1070, 114, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1222, 1070, 128, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1223, 1070, 4, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1224, 1071, 100, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1225, 1071, 114, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1226, 1071, 128, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1227, 1071, 4, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1228, 1072, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1229, 1072, 115, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1230, 1072, 129, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1231, 1073, 101, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1232, 1073, 115, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1233, 1073, 129, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1234, 1074, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1235, 1074, 116, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1236, 1074, 130, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1237, 1075, 102, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1238, 1075, 116, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1239, 1075, 130, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1240, 1076, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1241, 1076, 117, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1242, 1076, 131, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1243, 1077, 103, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1244, 1077, 117, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1245, 1077, 131, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1246, 1078, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1247, 1078, 118, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1248, 1078, 132, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1249, 1079, 104, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1250, 1079, 118, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1251, 1079, 132, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1252, 1080, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1253, 1080, 119, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1254, 1080, 133, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1255, 1081, 105, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1256, 1081, 119, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1257, 1081, 133, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1258, 1082, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1259, 1082, 120, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1260, 1082, 134, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1261, 1083, 106, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1262, 1083, 120, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1263, 1083, 134, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-01 11:05:00', '2026-06-01 11:05:00'),
(1264, 1084, 100, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1265, 1084, 114, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1266, 1084, 128, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1267, 1084, 4, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1268, 1085, 100, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1269, 1085, 114, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1270, 1085, 128, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1271, 1085, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1272, 1086, 101, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1273, 1086, 115, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1274, 1086, 129, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1275, 1087, 101, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1276, 1087, 115, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1277, 1087, 129, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1278, 1088, 102, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1279, 1088, 116, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1280, 1088, 130, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1281, 1089, 102, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1282, 1089, 116, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1283, 1089, 130, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1284, 1090, 103, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1285, 1090, 117, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1286, 1090, 131, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1287, 1091, 103, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1288, 1091, 117, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1289, 1091, 131, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1290, 1092, 104, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1291, 1092, 118, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1292, 1092, 132, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1293, 1093, 104, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1294, 1093, 118, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1295, 1093, 132, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1296, 1094, 105, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1297, 1094, 119, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1298, 1094, 133, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1299, 1095, 105, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1300, 1095, 119, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1301, 1095, 133, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1302, 1096, 106, 'llegada_tarde', '08:12:00', 12, NULL, 'Llegó dentro del margen de 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1303, 1096, 120, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1304, 1096, 134, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1305, 1097, 106, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1306, 1097, 120, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1307, 1097, 134, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-02 11:05:00', '2026-06-02 11:05:00'),
(1308, 1098, 100, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1309, 1098, 114, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1310, 1098, 128, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1311, 1098, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1312, 1099, 100, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1313, 1099, 114, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1314, 1099, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1315, 1099, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1316, 1100, 101, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1317, 1100, 115, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1318, 1100, 129, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1319, 1101, 101, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1320, 1101, 115, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1321, 1101, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1322, 1102, 102, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1323, 1102, 116, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1324, 1102, 130, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1325, 1103, 102, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1326, 1103, 116, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1327, 1103, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1328, 1104, 103, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1329, 1104, 117, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1330, 1104, 131, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1331, 1105, 103, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1332, 1105, 117, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1333, 1105, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1334, 1106, 104, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1335, 1106, 118, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1336, 1106, 132, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1337, 1107, 104, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1338, 1107, 118, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1339, 1107, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1340, 1108, 105, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1341, 1108, 119, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1342, 1108, 133, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1343, 1109, 105, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1344, 1109, 119, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1345, 1109, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1346, 1110, 106, 'ausente_con_presente', '08:25:00', 25, NULL, 'Llegó luego de los 15 minutos', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1347, 1110, 120, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1348, 1110, 134, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1349, 1111, 106, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1350, 1111, 120, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1351, 1111, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-03 11:05:00', '2026-06-03 11:05:00'),
(1352, 1112, 100, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1353, 1112, 114, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1354, 1112, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1355, 1112, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1356, 1113, 100, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1357, 1113, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1358, 1113, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1359, 1113, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1360, 1114, 101, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1361, 1114, 115, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1362, 1114, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1363, 1115, 101, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1364, 1115, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1365, 1115, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1366, 1116, 102, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1367, 1116, 116, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1368, 1116, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1369, 1117, 102, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1370, 1117, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1371, 1117, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1372, 1118, 103, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1373, 1118, 117, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1374, 1118, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1375, 1119, 103, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1376, 1119, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1377, 1119, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1378, 1120, 104, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1379, 1120, 118, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1380, 1120, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1381, 1121, 104, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1382, 1121, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1383, 1121, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1384, 1122, 105, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1385, 1122, 119, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1386, 1122, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1387, 1123, 105, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1388, 1123, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1389, 1123, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1390, 1124, 106, 'ausente', NULL, NULL, NULL, 'Ausente sin justificar', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1391, 1124, 120, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1392, 1124, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1393, 1125, 106, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1394, 1125, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1395, 1125, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-04 11:05:00', '2026-06-04 11:05:00'),
(1396, 1126, 100, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1397, 1126, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1398, 1126, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1399, 1126, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1400, 1127, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1401, 1127, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1402, 1127, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1403, 1127, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1404, 1128, 101, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1405, 1128, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1406, 1128, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1407, 1129, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1408, 1129, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1409, 1129, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1410, 1130, 102, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1411, 1130, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1412, 1130, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1413, 1131, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1414, 1131, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1415, 1131, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1416, 1132, 103, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1417, 1132, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1418, 1132, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1419, 1133, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1420, 1133, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1421, 1133, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1422, 1134, 104, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1423, 1134, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1424, 1134, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1425, 1135, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1426, 1135, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1427, 1135, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1428, 1136, 105, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1429, 1136, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1430, 1136, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1431, 1137, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1432, 1137, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1433, 1137, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1434, 1138, 106, 'retirado_anticipado', NULL, NULL, '11:20:00', 'Retiro anticipado informado', 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1435, 1138, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1436, 1138, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1437, 1139, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00');
INSERT INTO `detalles_asistencia` (`id`, `registro_id`, `alumno_id`, `estado`, `hora_llegada`, `minutos_tarde`, `hora_retiro`, `observaciones`, `modificado`, `created_at`, `updated_at`) VALUES
(1438, 1139, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1439, 1139, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-05 11:05:00', '2026-06-05 11:05:00'),
(1440, 1140, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1441, 1140, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1442, 1140, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1443, 1140, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1444, 1141, 100, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1445, 1141, 114, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1446, 1141, 128, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1447, 1141, 4, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1448, 1142, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1449, 1142, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1450, 1142, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1451, 1143, 101, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1452, 1143, 115, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1453, 1143, 129, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1454, 1144, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1455, 1144, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1456, 1144, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1457, 1145, 102, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1458, 1145, 116, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1459, 1145, 130, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1460, 1146, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1461, 1146, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1462, 1146, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1463, 1147, 103, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1464, 1147, 117, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1465, 1147, 131, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1466, 1148, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1467, 1148, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1468, 1148, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1469, 1149, 104, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1470, 1149, 118, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1471, 1149, 132, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1472, 1150, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1473, 1150, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1474, 1150, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1475, 1151, 105, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1476, 1151, 119, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1477, 1151, 133, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1478, 1152, 106, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1479, 1152, 120, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1480, 1152, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00'),
(1481, 1153, 106, 'ausente', NULL, NULL, NULL, NULL, 1, '2026-06-08 11:05:00', '2026-06-11 22:43:20'),
(1482, 1153, 120, 'llegada_tarde', NULL, NULL, NULL, NULL, 1, '2026-06-08 11:05:00', '2026-06-12 01:23:29'),
(1483, 1153, 134, 'presente', NULL, NULL, NULL, NULL, 0, '2026-06-08 11:05:00', '2026-06-08 11:05:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `ciclo` enum('basico','superior') NOT NULL DEFAULT 'superior',
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id`, `nombre`, `codigo`, `ciclo`, `activo`) VALUES
(1, 'Ciclo Básico', 'CB', 'basico', 1),
(2, 'Programación', 'PRG', 'superior', 1),
(3, 'Electrónica', 'ELE', 'superior', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_cambios_asistencia`
--

CREATE TABLE `historial_cambios_asistencia` (
  `id` int(10) UNSIGNED NOT NULL,
  `detalle_id` int(10) UNSIGNED NOT NULL,
  `registro_id` int(10) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `estado_anterior` enum('presente','ausente','llegada_tarde','ausente_con_presente','justificado','retirado_anticipado') DEFAULT NULL,
  `estado_nuevo` enum('presente','ausente','llegada_tarde','ausente_con_presente','justificado','retirado_anticipado') NOT NULL,
  `modificado_por` int(10) UNSIGNED NOT NULL,
  `motivo` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `nombre_corto` varchar(50) NOT NULL,
  `cue` varchar(20) DEFAULT NULL COMMENT 'Código Único de Establecimiento',
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `email_institucional` varchar(150) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instituciones`
--

INSERT INTO `instituciones` (`id`, `nombre`, `nombre_corto`, `cue`, `direccion`, `telefono`, `email_institucional`, `logo_path`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Escuela de Educación Secundaria Técnica N°1 Eduardo Ader', 'EEST N°1', '060000-00', 'Provincia de Buenos Aires', NULL, NULL, NULL, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justificaciones`
--

CREATE TABLE `justificaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `detalle_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Asistencia específica que se justifica',
  `enviado_por` int(10) UNSIGNED NOT NULL COMMENT 'padre_tutor_id o alumno_id',
  `tipo` enum('medica','personal','academica','otro') NOT NULL DEFAULT 'personal',
  `motivo` text NOT NULL,
  `archivo_path` varchar(500) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_tipo` varchar(50) DEFAULT NULL COMMENT 'pdf, png, jpeg',
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `revisado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL,
  `comentario_revisor` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `justificaciones`
--

INSERT INTO `justificaciones` (`id`, `alumno_id`, `detalle_id`, `enviado_por`, `tipo`, `motivo`, `archivo_path`, `archivo_nombre`, `archivo_tipo`, `estado`, `revisado_por`, `fecha_revision`, `comentario_revisor`, `created_at`, `updated_at`) VALUES
(100, 128, 1222, 228, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(101, 4, 1223, 5, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(102, 114, 1225, 214, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(103, 128, 1226, 228, 'otro', 'Situación personal', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(104, 4, 1227, 5, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(105, 129, 1230, 229, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(106, 115, 1232, 215, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(107, 129, 1233, 229, 'otro', 'Situación personal', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(108, 130, 1236, 230, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(109, 116, 1238, 216, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(110, 130, 1239, 230, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(111, 131, 1242, 231, 'otro', 'Situación personal', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(112, 117, 1244, 217, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(113, 131, 1245, 231, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(114, 132, 1248, 232, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(115, 118, 1250, 218, 'otro', 'Situación personal', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(116, 132, 1251, 232, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(117, 133, 1254, 233, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(118, 119, 1256, 219, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(119, 133, 1257, 233, 'otro', 'Situación personal', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(120, 134, 1260, 234, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(121, 120, 1262, 220, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(122, 134, 1263, 234, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(123, 114, 1265, 214, 'otro', 'Situación personal', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(124, 128, 1266, 228, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(125, 4, 1267, 5, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(126, 100, 1268, 200, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(127, 114, 1269, 214, 'otro', 'Situación personal', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(128, 128, 1270, 228, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(129, 115, 1273, 215, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(130, 129, 1274, 229, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(131, 101, 1275, 201, 'otro', 'Situación personal', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(132, 115, 1276, 215, 'medica', 'Certificado médico adjunto', NULL, NULL, NULL, 'rechazada', 3, '2026-06-03 10:00:00', 'Motivo insuficiente', '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(133, 129, 1277, 229, 'personal', 'Trámite familiar', NULL, NULL, NULL, 'pendiente', NULL, NULL, NULL, '2026-06-02 12:00:00', '2026-06-03 13:00:00'),
(134, 116, 1279, 216, 'academica', 'Participación en actividad escolar', NULL, NULL, NULL, 'aprobada', 3, '2026-06-03 10:00:00', 'Justificación aceptada', '2026-06-02 12:00:00', '2026-06-03 13:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_actividad`
--

CREATE TABLE `log_actividad` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `registro_id` int(10) UNSIGNED DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `log_actividad`
--

INSERT INTO `log_actividad` (`id`, `usuario_id`, `accion`, `descripcion`, `tabla_afectada`, `registro_id`, `datos_anteriores`, `datos_nuevos`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN', 'Inicio de sesión exitoso', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 04:02:53'),
(100, 1, 'LOGIN', 'Actividad de prueba #1', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 11:00:00'),
(101, 2, 'CREAR_USUARIO', 'Actividad de prueba #2', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 12:00:00'),
(102, 3, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #3', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 13:00:00'),
(103, 10, 'CREAR_COMUNICADO', 'Actividad de prueba #4', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 14:00:00'),
(104, 11, 'APROBAR_SOLICITUD', 'Actividad de prueba #5', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 15:00:00'),
(105, 12, 'GENERAR_REPORTE', 'Actividad de prueba #6', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 16:00:00'),
(106, 13, 'LOGIN', 'Actividad de prueba #7', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-07 17:00:00'),
(107, 14, 'CREAR_USUARIO', 'Actividad de prueba #8', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-08 18:00:00'),
(108, 1, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #9', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-09 19:00:00'),
(109, 2, 'CREAR_COMUNICADO', 'Actividad de prueba #10', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 20:00:00'),
(110, 3, 'APROBAR_SOLICITUD', 'Actividad de prueba #11', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 11:00:00'),
(111, 10, 'GENERAR_REPORTE', 'Actividad de prueba #12', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 12:00:00'),
(112, 11, 'LOGIN', 'Actividad de prueba #13', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 13:00:00'),
(113, 12, 'CREAR_USUARIO', 'Actividad de prueba #14', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 14:00:00'),
(114, 13, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #15', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 15:00:00'),
(115, 14, 'CREAR_COMUNICADO', 'Actividad de prueba #16', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-07 16:00:00'),
(116, 1, 'APROBAR_SOLICITUD', 'Actividad de prueba #17', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-08 17:00:00'),
(117, 2, 'GENERAR_REPORTE', 'Actividad de prueba #18', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-09 18:00:00'),
(118, 3, 'LOGIN', 'Actividad de prueba #19', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 19:00:00'),
(119, 10, 'CREAR_USUARIO', 'Actividad de prueba #20', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 20:00:00'),
(120, 11, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #21', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 11:00:00'),
(121, 12, 'CREAR_COMUNICADO', 'Actividad de prueba #22', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 12:00:00'),
(122, 13, 'APROBAR_SOLICITUD', 'Actividad de prueba #23', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 13:00:00'),
(123, 14, 'GENERAR_REPORTE', 'Actividad de prueba #24', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 14:00:00'),
(124, 1, 'LOGIN', 'Actividad de prueba #25', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-07 15:00:00'),
(125, 2, 'CREAR_USUARIO', 'Actividad de prueba #26', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-08 16:00:00'),
(126, 3, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #27', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-09 17:00:00'),
(127, 10, 'CREAR_COMUNICADO', 'Actividad de prueba #28', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 18:00:00'),
(128, 11, 'APROBAR_SOLICITUD', 'Actividad de prueba #29', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 19:00:00'),
(129, 12, 'GENERAR_REPORTE', 'Actividad de prueba #30', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 20:00:00'),
(130, 13, 'LOGIN', 'Actividad de prueba #31', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 11:00:00'),
(131, 14, 'CREAR_USUARIO', 'Actividad de prueba #32', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 12:00:00'),
(132, 1, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #33', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 13:00:00'),
(133, 2, 'CREAR_COMUNICADO', 'Actividad de prueba #34', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-07 14:00:00'),
(134, 3, 'APROBAR_SOLICITUD', 'Actividad de prueba #35', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-08 15:00:00'),
(135, 10, 'GENERAR_REPORTE', 'Actividad de prueba #36', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-09 16:00:00'),
(136, 11, 'LOGIN', 'Actividad de prueba #37', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 17:00:00'),
(137, 12, 'CREAR_USUARIO', 'Actividad de prueba #38', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 18:00:00'),
(138, 13, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #39', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 19:00:00'),
(139, 14, 'CREAR_COMUNICADO', 'Actividad de prueba #40', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 20:00:00'),
(140, 1, 'APROBAR_SOLICITUD', 'Actividad de prueba #41', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 11:00:00'),
(141, 2, 'GENERAR_REPORTE', 'Actividad de prueba #42', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 12:00:00'),
(142, 3, 'LOGIN', 'Actividad de prueba #43', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-07 13:00:00'),
(143, 10, 'CREAR_USUARIO', 'Actividad de prueba #44', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-08 14:00:00'),
(144, 11, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #45', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-09 15:00:00'),
(145, 12, 'CREAR_COMUNICADO', 'Actividad de prueba #46', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 16:00:00'),
(146, 13, 'APROBAR_SOLICITUD', 'Actividad de prueba #47', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 17:00:00'),
(147, 14, 'GENERAR_REPORTE', 'Actividad de prueba #48', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 18:00:00'),
(148, 1, 'LOGIN', 'Actividad de prueba #49', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 19:00:00'),
(149, 2, 'CREAR_USUARIO', 'Actividad de prueba #50', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 20:00:00'),
(150, 3, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #51', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 11:00:00'),
(151, 10, 'CREAR_COMUNICADO', 'Actividad de prueba #52', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-07 12:00:00'),
(152, 11, 'APROBAR_SOLICITUD', 'Actividad de prueba #53', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-08 13:00:00'),
(153, 12, 'GENERAR_REPORTE', 'Actividad de prueba #54', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-09 14:00:00'),
(154, 13, 'LOGIN', 'Actividad de prueba #55', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-01 15:00:00'),
(155, 14, 'CREAR_USUARIO', 'Actividad de prueba #56', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-02 16:00:00'),
(156, 1, 'ACTUALIZAR_ASISTENCIA', 'Actividad de prueba #57', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-03 17:00:00'),
(157, 2, 'CREAR_COMUNICADO', 'Actividad de prueba #58', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-04 18:00:00'),
(158, 3, 'APROBAR_SOLICITUD', 'Actividad de prueba #59', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-05 19:00:00'),
(159, 10, 'GENERAR_REPORTE', 'Actividad de prueba #60', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-06-06 20:00:00'),
(160, 1, 'LOGOUT', 'Cierre de sesión', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 05:28:45'),
(161, 1, 'LOGIN', 'Inicio de sesión exitoso', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 06:21:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(10) UNSIGNED NOT NULL,
  `institucion_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `nombre` varchar(150) NOT NULL COMMENT 'Matemática, Programación, etc.',
  `codigo` varchar(20) DEFAULT NULL,
  `especialidad_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = para todas las especialidades',
  `anio_correspondiente` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '1-7, NULL = para varios años',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `institucion_id`, `nombre`, `codigo`, `especialidad_id`, `anio_correspondiente`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Matemática', 'MAT', NULL, NULL, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(2, 1, 'Lengua y Literatura', 'LENG', NULL, NULL, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(3, 1, 'Programación I', 'PRG1', 2, 4, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(4, 1, 'Programación II', 'PRG2', 2, 5, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(5, 1, 'Electrónica I', 'ELE1', 3, 4, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(6, 1, 'Física', 'FIS', NULL, NULL, 1, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(20, 1, 'Lengua y Literatura', 'LEN', NULL, NULL, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(21, 1, 'Historia', 'HIS', NULL, NULL, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(22, 1, 'Geografía', 'GEO', NULL, NULL, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(23, 1, 'Inglés', 'ING', NULL, NULL, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(24, 1, 'Educación Física', 'EDF', NULL, NULL, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(25, 1, 'Sistemas Operativos', 'SO', 2, 5, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(26, 1, 'Base de Datos', 'BD', 2, 5, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(27, 1, 'Desarrollo Web', 'WEB', 2, 6, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(28, 1, 'Programación Orientada a Objetos', 'POO', 2, 4, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(29, 1, 'Redes', 'RED', 2, 6, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(30, 1, 'Laboratorio de Hardware', 'LH', 3, 4, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(31, 1, 'Electrónica Digital', 'ED', 3, 5, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(32, 1, 'Microcontroladores', 'MIC', 3, 6, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(33, 1, 'Instalaciones Electrónicas', 'IE', 3, 7, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(34, 1, 'Proyecto Final', 'PF', NULL, 7, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(35, 1, 'Seguridad e Higiene', 'SH', NULL, 7, 1, '2026-03-01 11:00:00', '2026-03-01 11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(10) UNSIGNED NOT NULL,
  `conversacion_id` int(10) UNSIGNED NOT NULL,
  `remitente_id` int(10) UNSIGNED NOT NULL,
  `contenido` text NOT NULL,
  `tipo` enum('texto','archivo','imagen') NOT NULL DEFAULT 'texto',
  `archivo_path` varchar(500) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_tipo` varchar(50) DEFAULT NULL,
  `archivo_tamano` int(10) UNSIGNED DEFAULT NULL COMMENT 'en bytes',
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `conversacion_id`, `remitente_id`, `contenido`, `tipo`, `archivo_path`, `archivo_nombre`, `archivo_tipo`, `archivo_tamano`, `leido`, `eliminado`, `created_at`) VALUES
(100, 100, 200, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(101, 100, 3, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(102, 101, 201, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(103, 101, 10, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(104, 102, 202, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(105, 102, 11, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(106, 103, 203, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(107, 103, 12, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(108, 104, 204, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(109, 104, 13, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(110, 105, 205, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(111, 105, 14, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(112, 106, 206, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(113, 106, 3, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(114, 107, 207, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(115, 107, 10, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(116, 108, 208, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(117, 108, 11, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(118, 109, 209, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(119, 109, 12, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(120, 110, 210, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(121, 110, 13, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(122, 111, 211, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(123, 111, 14, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(124, 112, 212, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(125, 112, 3, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(126, 113, 213, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(127, 113, 10, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(128, 114, 214, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(129, 114, 3, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(130, 115, 215, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(131, 115, 10, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(132, 116, 216, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(133, 116, 11, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(134, 117, 217, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(135, 117, 12, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(136, 118, 218, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(137, 118, 13, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-01 12:10:00'),
(138, 119, 219, 'Hola, quería consultar por la inasistencia registrada esta semana.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:02:00'),
(139, 119, 14, 'Buen día, ya revisé el registro. Puede enviar la justificación desde el panel.', 'texto', NULL, NULL, NULL, NULL, 1, 0, '2026-06-01 12:10:00'),
(140, 120, 1, 'hola', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-12 04:31:39'),
(141, 121, 1, 'a', 'texto', NULL, NULL, NULL, NULL, 0, 0, '2026-06-12 06:05:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `institucion_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `creado_por` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `tipo` enum('aviso','comunicado','alerta','recordatorio') NOT NULL DEFAULT 'aviso',
  `prioridad` enum('normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `rol_destino` set('admin','directivo','preceptor','alumno','padre_tutor','todos') NOT NULL DEFAULT 'todos',
  `curso_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = para toda la institución',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_expiracion` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `institucion_id`, `creado_por`, `titulo`, `contenido`, `tipo`, `prioridad`, `rol_destino`, `curso_id`, `activo`, `fecha_expiracion`, `created_at`, `updated_at`) VALUES
(100, 1, 1, 'Inscripción a Pasantías 2026', 'Recordamos a los alumnos de 7mo año que continúa abierta la inscripción.', 'aviso', 'normal', 'alumno,padre_tutor', 1, 1, '2026-12-31', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(101, 1, 2, 'Reunión de preceptores', 'Se informa reunión de coordinación institucional el viernes.', 'recordatorio', 'normal', 'directivo,preceptor', NULL, 1, '2026-12-31', '2026-06-02 11:00:00', '2026-06-02 11:00:00'),
(102, 1, 2, 'Ausentismo crítico detectado', 'Se detectaron cursos con porcentajes altos de inasistencias.', 'alerta', 'alta', 'admin,directivo', NULL, 1, '2026-12-31', '2026-06-03 11:00:00', '2026-06-03 11:00:00'),
(103, 1, 2, 'Comunicado global', 'Actualización importante sobre horarios y organización escolar.', 'comunicado', 'normal', 'todos', NULL, 1, '2026-12-31', '2026-06-04 11:00:00', '2026-06-04 11:00:00'),
(104, 1, 2, 'Carga de justificaciones', 'Recordar revisar las justificaciones pendientes.', 'recordatorio', 'normal', 'preceptor', 13, 1, '2026-12-31', '2026-06-05 11:00:00', '2026-06-05 11:00:00'),
(105, 1, 1, 'Mantenimiento del sistema', 'El sistema podrá presentar interrupciones fuera del horario escolar.', 'aviso', 'normal', 'admin', NULL, 1, '2026-12-31', '2026-06-06 11:00:00', '2026-06-06 11:00:00'),
(106, 1, 2, 'Inscripción a Pasantías 2026', 'Recordamos a los alumnos de 7mo año que continúa abierta la inscripción.', 'aviso', 'normal', 'alumno,padre_tutor', NULL, 1, '2026-12-31', '2026-06-07 11:00:00', '2026-06-07 11:00:00'),
(107, 1, 2, 'Reunión de preceptores', 'Se informa reunión de coordinación institucional el viernes.', 'recordatorio', 'normal', 'directivo,preceptor', NULL, 1, '2026-12-31', '2026-06-08 11:00:00', '2026-06-08 11:00:00'),
(108, 1, 2, 'Ausentismo crítico detectado', 'Se detectaron cursos con porcentajes altos de inasistencias.', 'alerta', 'alta', 'admin,directivo', 17, 1, '2026-12-31', '2026-06-09 11:00:00', '2026-06-09 11:00:00'),
(109, 1, 2, 'Comunicado global', 'Actualización importante sobre horarios y organización escolar.', 'comunicado', 'normal', 'todos', NULL, 1, '2026-12-31', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(110, 1, 1, 'Carga de justificaciones', 'Recordar revisar las justificaciones pendientes.', 'recordatorio', 'normal', 'preceptor', NULL, 1, '2026-12-31', '2026-06-02 11:00:00', '2026-06-02 11:00:00'),
(111, 1, 2, 'Mantenimiento del sistema', 'El sistema podrá presentar interrupciones fuera del horario escolar.', 'aviso', 'normal', 'admin', NULL, 1, '2026-12-31', '2026-06-03 11:00:00', '2026-06-03 11:00:00'),
(112, 1, 2, 'Inscripción a Pasantías 2026', 'Recordamos a los alumnos de 7mo año que continúa abierta la inscripción.', 'aviso', 'normal', 'alumno,padre_tutor', 21, 1, '2026-12-31', '2026-06-04 11:00:00', '2026-06-04 11:00:00'),
(113, 1, 2, 'Reunión de preceptores', 'Se informa reunión de coordinación institucional el viernes.', 'recordatorio', 'normal', 'directivo,preceptor', NULL, 1, '2026-12-31', '2026-06-05 11:00:00', '2026-06-05 11:00:00'),
(114, 1, 2, 'Ausentismo crítico detectado', 'Se detectaron cursos con porcentajes altos de inasistencias.', 'alerta', 'alta', 'admin,directivo', NULL, 1, '2026-12-31', '2026-06-06 11:00:00', '2026-06-06 11:00:00'),
(115, 1, 1, 'Comunicado global', 'Actualización importante sobre horarios y organización escolar.', 'comunicado', 'normal', 'todos', NULL, 1, '2026-12-31', '2026-06-07 11:00:00', '2026-06-07 11:00:00'),
(116, 1, 2, 'Carga de justificaciones', 'Recordar revisar las justificaciones pendientes.', 'recordatorio', 'normal', 'preceptor', 11, 1, '2026-12-31', '2026-06-08 11:00:00', '2026-06-08 11:00:00'),
(117, 1, 2, 'Mantenimiento del sistema', 'El sistema podrá presentar interrupciones fuera del horario escolar.', 'aviso', 'normal', 'admin', NULL, 1, '2026-12-31', '2026-06-09 11:00:00', '2026-06-09 11:00:00'),
(118, 1, 2, 'Inscripción a Pasantías 2026', 'Recordamos a los alumnos de 7mo año que continúa abierta la inscripción.', 'aviso', 'normal', 'alumno,padre_tutor', NULL, 1, '2026-12-31', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(119, 1, 2, 'Reunión de preceptores', 'Se informa reunión de coordinación institucional el viernes.', 'recordatorio', 'normal', 'directivo,preceptor', NULL, 1, '2026-12-31', '2026-06-02 11:00:00', '2026-06-02 11:00:00'),
(120, 1, 1, 'Ausentismo crítico detectado', 'Se detectaron cursos con porcentajes altos de inasistencias.', 'alerta', 'alta', 'admin,directivo', 15, 1, '2026-12-31', '2026-06-03 11:00:00', '2026-06-03 11:00:00'),
(121, 1, 2, 'Comunicado global', 'Actualización importante sobre horarios y organización escolar.', 'comunicado', 'normal', 'todos', NULL, 1, '2026-12-31', '2026-06-04 11:00:00', '2026-06-04 11:00:00'),
(122, 1, 2, 'Carga de justificaciones', 'Recordar revisar las justificaciones pendientes.', 'recordatorio', 'normal', 'preceptor', NULL, 1, '2026-12-31', '2026-06-05 11:00:00', '2026-06-05 11:00:00'),
(123, 1, 2, 'Mantenimiento del sistema', 'El sistema podrá presentar interrupciones fuera del horario escolar.', 'aviso', 'normal', 'admin', NULL, 1, '2026-12-31', '2026-06-06 11:00:00', '2026-06-06 11:00:00'),
(124, 1, 2, 'Inscripción a Pasantías 2026', 'Recordamos a los alumnos de 7mo año que continúa abierta la inscripción.', 'aviso', 'normal', 'alumno,padre_tutor', 19, 1, '2026-12-31', '2026-06-07 11:00:00', '2026-06-07 11:00:00'),
(125, 1, 1, 'Reunión de preceptores', 'Se informa reunión de coordinación institucional el viernes.', 'recordatorio', 'normal', 'directivo,preceptor', NULL, 1, '2026-12-31', '2026-06-08 11:00:00', '2026-06-08 11:00:00'),
(126, 1, 2, 'Ausentismo crítico detectado', 'Se detectaron cursos con porcentajes altos de inasistencias.', 'alerta', 'alta', 'admin,directivo', NULL, 1, '2026-12-31', '2026-06-09 11:00:00', '2026-06-09 11:00:00'),
(127, 1, 2, 'Comunicado global', 'Actualización importante sobre horarios y organización escolar.', 'comunicado', 'normal', 'todos', NULL, 1, '2026-12-31', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(128, 1, 2, 'Carga de justificaciones', 'Recordar revisar las justificaciones pendientes.', 'recordatorio', 'normal', 'preceptor', 1, 1, '2026-12-31', '2026-06-02 11:00:00', '2026-06-02 11:00:00'),
(129, 1, 2, 'Mantenimiento del sistema', 'El sistema podrá presentar interrupciones fuera del horario escolar.', 'aviso', 'normal', 'admin', NULL, 1, '2026-12-31', '2026-06-03 11:00:00', '2026-06-03 11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_leidas`
--

CREATE TABLE `notificaciones_leidas` (
  `id` int(10) UNSIGNED NOT NULL,
  `notificacion_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `leida_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones_leidas`
--

INSERT INTO `notificaciones_leidas` (`id`, `notificacion_id`, `usuario_id`, `leida_en`) VALUES
(100, 100, 1, '2026-06-09 11:00:00'),
(102, 102, 1, '2026-06-09 11:00:00'),
(104, 104, 1, '2026-06-09 11:00:00'),
(106, 106, 1, '2026-06-09 11:00:00'),
(108, 108, 1, '2026-06-09 11:00:00'),
(110, 110, 1, '2026-06-09 11:00:00'),
(112, 112, 1, '2026-06-09 11:00:00'),
(114, 114, 1, '2026-06-09 11:00:00'),
(116, 116, 1, '2026-06-09 11:00:00'),
(118, 118, 1, '2026-06-09 11:00:00'),
(120, 120, 1, '2026-06-09 11:00:00'),
(122, 122, 1, '2026-06-09 11:00:00'),
(124, 124, 1, '2026-06-09 11:00:00'),
(126, 126, 1, '2026-06-09 11:00:00'),
(128, 128, 1, '2026-06-09 11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preceptor_cursos`
--

CREATE TABLE `preceptor_cursos` (
  `id` int(10) UNSIGNED NOT NULL,
  `preceptor_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `es_titular` tinyint(1) NOT NULL DEFAULT 1,
  `ciclo_lectivo` year(4) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `preceptor_cursos`
--

INSERT INTO `preceptor_cursos` (`id`, `preceptor_id`, `curso_id`, `es_titular`, `ciclo_lectivo`, `activo`, `created_at`) VALUES
(1, 3, 1, 1, '2026', 1, '2026-06-09 03:57:23'),
(101, 10, 1, 0, '2026', 1, '2026-03-01 11:00:00'),
(102, 10, 10, 1, '2026', 1, '2026-03-01 11:00:00'),
(103, 11, 11, 1, '2026', 1, '2026-03-01 11:00:00'),
(104, 12, 12, 1, '2026', 1, '2026-03-01 11:00:00'),
(105, 13, 13, 1, '2026', 1, '2026-03-01 11:00:00'),
(106, 14, 13, 0, '2026', 1, '2026-03-01 11:00:00'),
(107, 14, 14, 1, '2026', 1, '2026-03-01 11:00:00'),
(108, 3, 15, 1, '2026', 1, '2026-03-01 11:00:00'),
(109, 10, 16, 1, '2026', 1, '2026-03-01 11:00:00'),
(110, 11, 17, 1, '2026', 1, '2026-03-01 11:00:00'),
(111, 12, 17, 0, '2026', 1, '2026-03-01 11:00:00'),
(112, 12, 18, 1, '2026', 1, '2026-03-01 11:00:00'),
(113, 13, 19, 1, '2026', 1, '2026-03-01 11:00:00'),
(114, 14, 20, 1, '2026', 1, '2026-03-01 11:00:00'),
(115, 3, 21, 1, '2026', 1, '2026-03-01 11:00:00'),
(116, 10, 21, 0, '2026', 1, '2026-03-01 11:00:00'),
(117, 10, 22, 1, '2026', 1, '2026-03-01 11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reemplazos_preceptores`
--

CREATE TABLE `reemplazos_preceptores` (
  `id` int(10) UNSIGNED NOT NULL,
  `preceptor_titular_id` int(10) UNSIGNED NOT NULL,
  `preceptor_reemplazante_id` int(10) UNSIGNED DEFAULT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `materia_id` int(10) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `turno` enum('mañana','tarde','vespertino') NOT NULL,
  `motivo` text DEFAULT NULL,
  `prioridad` enum('normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `estado` enum('sin_asignar','asignado','realizado','cancelado') NOT NULL DEFAULT 'sin_asignar',
  `asignado_por` int(10) UNSIGNED DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reemplazos_preceptores`
--

INSERT INTO `reemplazos_preceptores` (`id`, `preceptor_titular_id`, `preceptor_reemplazante_id`, `curso_id`, `materia_id`, `fecha`, `turno`, `motivo`, `prioridad`, `estado`, `asignado_por`, `notas`, `created_at`, `updated_at`) VALUES
(100, 3, NULL, 1, 1, '2026-06-10', 'mañana', 'Licencia médica', 'normal', 'sin_asignar', NULL, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(101, 10, 12, 10, 3, '2026-06-11', 'tarde', 'Capacitación institucional', 'alta', 'asignado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(102, 11, 13, 11, 4, '2026-06-12', 'mañana', 'Reunión directiva', 'urgente', 'realizado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(103, 12, 14, 12, 5, '2026-06-13', 'tarde', 'Ausencia informada', 'normal', 'cancelado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(104, 13, NULL, 13, 6, '2026-06-14', 'mañana', 'Licencia médica', 'alta', 'sin_asignar', NULL, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(105, 14, 10, 14, 20, '2026-06-15', 'tarde', 'Capacitación institucional', 'urgente', 'asignado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(106, 3, 11, 15, 21, '2026-06-16', 'mañana', 'Reunión directiva', 'normal', 'realizado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(107, 10, 12, 16, 22, '2026-06-17', 'tarde', 'Ausencia informada', 'alta', 'cancelado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(108, 11, NULL, 17, 23, '2026-06-18', 'mañana', 'Licencia médica', 'urgente', 'sin_asignar', NULL, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(109, 12, 14, 18, 24, '2026-06-19', 'tarde', 'Capacitación institucional', 'normal', 'asignado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(110, 13, 3, 19, 25, '2026-06-20', 'mañana', 'Reunión directiva', 'alta', 'realizado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(111, 14, 10, 20, 26, '2026-06-21', 'tarde', 'Ausencia informada', 'urgente', 'cancelado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(112, 3, NULL, 21, 27, '2026-06-22', 'mañana', 'Licencia médica', 'normal', 'sin_asignar', NULL, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(113, 10, 12, 22, 28, '2026-06-23', 'tarde', 'Capacitación institucional', 'alta', 'asignado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(114, 11, 13, 1, 29, '2026-06-24', 'mañana', 'Reunión directiva', 'urgente', 'realizado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(115, 12, 14, 10, 30, '2026-06-25', 'tarde', 'Ausencia informada', 'normal', 'cancelado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(116, 13, NULL, 11, 31, '2026-06-26', 'mañana', 'Licencia médica', 'alta', 'sin_asignar', NULL, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00'),
(117, 14, 10, 12, 32, '2026-06-27', 'tarde', 'Capacitación institucional', 'urgente', 'asignado', 2, 'Reemplazo generado como dato de prueba', '2026-06-01 11:00:00', '2026-06-01 11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_asistencia`
--

CREATE TABLE `registros_asistencia` (
  `id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `materia_id` int(10) UNSIGNED NOT NULL,
  `preceptor_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `modulo_horario` varchar(50) NOT NULL COMMENT 'Ej: 1ra Hora, 2da Hora',
  `bloque_horario` enum('primera_hora','segunda_hora') NOT NULL DEFAULT 'primera_hora',
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `turno` enum('mañana','tarde','vespertino') NOT NULL,
  `cierre_mensual_id` int(10) UNSIGNED DEFAULT NULL,
  `estado` enum('abierta','cerrada','modificada','anulada') NOT NULL DEFAULT 'abierta',
  `observaciones` text DEFAULT NULL,
  `cerrado_en` datetime DEFAULT NULL,
  `ciclo_lectivo` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registros_asistencia`
--

INSERT INTO `registros_asistencia` (`id`, `curso_id`, `materia_id`, `preceptor_id`, `fecha`, `modulo_horario`, `bloque_horario`, `hora_inicio`, `hora_fin`, `turno`, `cierre_mensual_id`, `estado`, `observaciones`, `cerrado_en`, `ciclo_lectivo`, `created_at`, `updated_at`) VALUES
(1000, 1, 20, 10, '2026-04-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-04-25 12:30:00', '2026', '2026-04-25 11:00:00', '2026-06-12 01:43:13'),
(1001, 1, 23, 11, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1002, 10, 1, 13, '2026-05-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1003, 10, 20, 14, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1004, 11, 20, 14, '2026-05-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1005, 11, 23, 3, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1006, 12, 23, 3, '2026-05-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1007, 12, 24, 10, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1008, 13, 24, 10, '2026-05-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1009, 13, 25, 11, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1010, 14, 25, 11, '2026-05-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'anulada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-06-12 01:41:24'),
(1011, 14, 26, 12, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1012, 15, 26, 12, '2026-05-25', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1013, 15, 27, 13, '2026-05-25', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-25 12:30:00', '2026', '2026-05-25 11:00:00', '2026-05-25 15:30:00'),
(1014, 1, 23, 10, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1015, 1, 24, 11, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1016, 10, 20, 13, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1017, 10, 23, 14, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1018, 11, 23, 14, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1019, 11, 24, 3, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1020, 12, 24, 3, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1021, 12, 25, 10, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1022, 13, 25, 10, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1023, 13, 26, 11, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1024, 14, 26, 11, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1025, 14, 27, 12, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1026, 15, 27, 12, '2026-05-26', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1027, 15, 30, 13, '2026-05-26', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-26 12:30:00', '2026', '2026-05-26 11:00:00', '2026-05-26 15:30:00'),
(1028, 1, 24, 10, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1029, 1, 25, 11, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1030, 10, 23, 13, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1031, 10, 24, 14, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1032, 11, 24, 14, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1033, 11, 25, 3, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1034, 12, 25, 3, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1035, 12, 26, 10, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1036, 13, 26, 10, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1037, 13, 27, 11, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1038, 14, 27, 11, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1039, 14, 30, 12, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1040, 15, 30, 12, '2026-05-27', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1041, 15, 31, 13, '2026-05-27', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-27 12:30:00', '2026', '2026-05-27 11:00:00', '2026-05-27 15:30:00'),
(1042, 1, 25, 10, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1043, 1, 26, 11, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1044, 10, 24, 13, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1045, 10, 25, 14, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1046, 11, 25, 14, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1047, 11, 26, 3, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1048, 12, 26, 3, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1049, 12, 27, 10, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1050, 13, 27, 10, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1051, 13, 30, 11, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1052, 14, 30, 11, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1053, 14, 31, 12, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1054, 15, 31, 12, '2026-05-28', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1055, 15, 35, 13, '2026-05-28', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-28 12:30:00', '2026', '2026-05-28 11:00:00', '2026-05-28 15:30:00'),
(1056, 1, 26, 10, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1057, 1, 27, 11, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1058, 10, 25, 13, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1059, 10, 26, 14, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1060, 11, 26, 14, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1061, 11, 27, 3, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1062, 12, 27, 3, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1063, 12, 30, 10, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1064, 13, 30, 10, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1065, 13, 31, 11, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1066, 14, 31, 11, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1067, 14, 35, 12, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1068, 15, 35, 12, '2026-05-29', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1069, 15, 1, 13, '2026-05-29', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'cerrada', 'Registro de prueba generado', '2026-05-29 12:30:00', '2026', '2026-05-29 11:00:00', '2026-05-29 15:30:00'),
(1070, 1, 31, 10, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1071, 1, 35, 11, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1072, 10, 30, 13, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1073, 10, 31, 14, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1074, 11, 31, 14, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1075, 11, 35, 3, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1076, 12, 35, 3, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1077, 12, 1, 10, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1078, 13, 1, 10, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1079, 13, 20, 11, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1080, 14, 20, 11, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1081, 14, 23, 12, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1082, 15, 23, 12, '2026-06-01', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1083, 15, 24, 13, '2026-06-01', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-01 11:00:00', '2026-06-01 15:30:00'),
(1084, 1, 35, 10, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1085, 1, 1, 11, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1086, 10, 31, 13, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1087, 10, 35, 14, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1088, 11, 35, 14, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1089, 11, 1, 3, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1090, 12, 1, 3, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1091, 12, 20, 10, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1092, 13, 20, 10, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1093, 13, 23, 11, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1094, 14, 23, 11, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1095, 14, 24, 12, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1096, 15, 24, 12, '2026-06-02', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1097, 15, 25, 13, '2026-06-02', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-02 11:00:00', '2026-06-02 15:30:00'),
(1098, 1, 1, 10, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1099, 1, 20, 11, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1100, 10, 35, 13, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1101, 10, 1, 14, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1102, 11, 1, 14, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1103, 11, 20, 3, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1104, 12, 20, 3, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1105, 12, 23, 10, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1106, 13, 23, 10, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1107, 13, 24, 11, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1108, 14, 24, 11, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1109, 14, 25, 12, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1110, 15, 25, 12, '2026-06-03', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1111, 15, 26, 13, '2026-06-03', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-03 11:00:00', '2026-06-03 15:30:00'),
(1112, 1, 20, 10, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1113, 1, 23, 11, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1114, 10, 1, 13, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1115, 10, 20, 14, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1116, 11, 20, 14, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1117, 11, 23, 3, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1118, 12, 23, 3, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1119, 12, 24, 10, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1120, 13, 24, 10, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1121, 13, 25, 11, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1122, 14, 25, 11, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1123, 14, 26, 12, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1124, 15, 26, 12, '2026-06-04', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1125, 15, 27, 13, '2026-06-04', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-04 11:00:00', '2026-06-04 15:30:00'),
(1126, 1, 23, 10, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1127, 1, 24, 11, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1128, 10, 20, 13, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1129, 10, 23, 14, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1130, 11, 23, 14, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1131, 11, 24, 3, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1132, 12, 24, 3, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1133, 12, 25, 10, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1134, 13, 25, 10, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1135, 13, 26, 11, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1136, 14, 26, 11, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1137, 14, 27, 12, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1138, 15, 27, 12, '2026-06-05', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1139, 15, 30, 13, '2026-06-05', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-05 11:00:00', '2026-06-05 15:30:00'),
(1140, 1, 26, 10, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1141, 1, 27, 11, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1142, 10, 25, 13, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1143, 10, 26, 14, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1144, 11, 26, 14, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1145, 11, 27, 3, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1146, 12, 27, 3, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1147, 12, 30, 10, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1148, 13, 30, 10, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1149, 13, 31, 11, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1150, 14, 31, 11, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1151, 14, 35, 12, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'tarde', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1152, 15, 35, 12, '2026-06-08', '1ra Hora', 'primera_hora', '08:00:00', '09:20:00', 'mañana', NULL, 'abierta', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-08 15:30:00'),
(1153, 15, 1, 11, '2026-06-08', '2da Hora', 'segunda_hora', '09:30:00', '10:50:00', 'mañana', NULL, 'modificada', 'Registro de prueba generado', NULL, '2026', '2026-06-08 11:00:00', '2026-06-12 01:23:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resumen_asistencia_diaria`
--

CREATE TABLE `resumen_asistencia_diaria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `turno` enum('mañana','tarde','vespertino') NOT NULL,
  `faltas_total` decimal(3,2) NOT NULL DEFAULT 0.00,
  `tiene_justificacion` tinyint(1) NOT NULL DEFAULT 0,
  `detalle_calculo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `resumen_asistencia_diaria`
--

INSERT INTO `resumen_asistencia_diaria` (`id`, `alumno_id`, `curso_id`, `fecha`, `turno`, `faltas_total`, `tiene_justificacion`, `detalle_calculo`, `created_at`, `updated_at`) VALUES
(1, 134, 15, '2026-06-08', 'mañana', 0.00, 0, 'Bloque primera_hora: presente | Bloque segunda_hora: presente', '2026-06-11 22:42:46', '2026-06-12 01:23:29'),
(2, 120, 15, '2026-06-08', 'mañana', 0.25, 0, 'Bloque primera_hora: presente | Bloque segunda_hora: llegada_tarde', '2026-06-11 22:42:46', '2026-06-12 01:23:29'),
(3, 106, 15, '2026-06-08', 'mañana', 1.00, 0, 'Bloque primera_hora: presente | Bloque segunda_hora: ausente', '2026-06-11 22:42:46', '2026-06-12 01:23:29'),
(4, 119, 14, '2026-05-25', 'tarde', 0.00, 0, 'Bloque segunda_hora: presente', '2026-06-12 01:41:24', '2026-06-12 01:41:24'),
(5, 105, 14, '2026-05-25', 'tarde', 0.00, 0, 'Bloque segunda_hora: presente', '2026-06-12 01:41:24', '2026-06-12 01:41:24'),
(6, 133, 14, '2026-05-25', 'tarde', 0.00, 0, 'Bloque segunda_hora: presente', '2026-06-12 01:41:24', '2026-06-12 01:41:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_acceso`
--

CREATE TABLE `solicitudes_acceso` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('alumno','vinculacion_padre') NOT NULL DEFAULT 'alumno',
  `datos_extra` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Info adicional de la solicitud' CHECK (json_valid(`datos_extra`)),
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `revisado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_acceso`
--

INSERT INTO `solicitudes_acceso` (`id`, `usuario_id`, `tipo`, `datos_extra`, `estado`, `revisado_por`, `fecha_revision`, `motivo_rechazo`, `created_at`, `updated_at`) VALUES
(100, 100, 'alumno', '{\"curso_id\": 1}', 'aprobado', 2, '2026-03-07 09:00:00', NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(101, 101, 'alumno', '{\"curso_id\": 10}', 'aprobado', 2, '2026-03-07 09:00:00', NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(102, 102, 'alumno', '{\"curso_id\": 11}', 'pendiente', NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(103, 103, 'alumno', '{\"curso_id\": 12}', 'rechazado', 2, '2026-03-07 09:00:00', 'Datos incompletos', '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(104, 104, 'alumno', '{\"curso_id\": 13}', 'aprobado', 2, '2026-03-07 09:00:00', NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(105, 105, 'alumno', '{\"curso_id\": 14}', 'aprobado', 2, '2026-03-07 09:00:00', NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(106, 106, 'alumno', '{\"curso_id\": 15}', 'pendiente', NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(107, 107, 'alumno', '{\"curso_id\": 16}', 'rechazado', 2, '2026-03-07 09:00:00', 'Datos incompletos', '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(108, 108, 'alumno', '{\"curso_id\": 17}', 'aprobado', 2, '2026-03-07 09:00:00', NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(109, 109, 'alumno', '{\"curso_id\": 18}', 'aprobado', 2, '2026-03-07 09:00:00', NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(110, 110, 'alumno', '{\"curso_id\": 19}', 'pendiente', NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(111, 111, 'alumno', '{\"curso_id\": 20}', 'rechazado', 2, '2026-03-07 09:00:00', 'Datos incompletos', '2026-03-05 12:00:00', '2026-03-07 12:00:00'),
(112, 200, 'vinculacion_padre', '{\"alumno_id\": 100, \"relacion\": \"padre\"}', 'aprobado', 2, '2026-03-08 09:00:00', NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(113, 201, 'vinculacion_padre', '{\"alumno_id\": 101, \"relacion\": \"padre\"}', 'pendiente', NULL, NULL, NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(114, 202, 'vinculacion_padre', '{\"alumno_id\": 102, \"relacion\": \"padre\"}', 'aprobado', 2, '2026-03-08 09:00:00', NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(115, 203, 'vinculacion_padre', '{\"alumno_id\": 103, \"relacion\": \"padre\"}', 'rechazado', 2, '2026-03-08 09:00:00', 'No coincide la documentación', '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(116, 204, 'vinculacion_padre', '{\"alumno_id\": 104, \"relacion\": \"padre\"}', 'aprobado', 2, '2026-03-08 09:00:00', NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(117, 205, 'vinculacion_padre', '{\"alumno_id\": 105, \"relacion\": \"padre\"}', 'pendiente', NULL, NULL, NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(118, 206, 'vinculacion_padre', '{\"alumno_id\": 106, \"relacion\": \"padre\"}', 'aprobado', 2, '2026-03-08 09:00:00', NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(119, 207, 'vinculacion_padre', '{\"alumno_id\": 107, \"relacion\": \"padre\"}', 'rechazado', 2, '2026-03-08 09:00:00', 'No coincide la documentación', '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(120, 208, 'vinculacion_padre', '{\"alumno_id\": 108, \"relacion\": \"padre\"}', 'aprobado', 2, '2026-03-08 09:00:00', NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(121, 209, 'vinculacion_padre', '{\"alumno_id\": 109, \"relacion\": \"padre\"}', 'pendiente', NULL, NULL, NULL, '2026-03-06 12:00:00', '2026-03-08 12:00:00'),
(122, 245, 'alumno', '{\"curso_id\":22,\"nombre_padre\":\"Carlos Perez\"}', 'pendiente', NULL, NULL, NULL, '2026-06-13 21:08:56', '2026-06-13 21:08:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `institucion_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `dni` varchar(15) DEFAULT NULL COMMENT 'Solo para adultos: NULL para alumnos',
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','directivo','preceptor','alumno','padre_tutor') NOT NULL,
  `estado` enum('activo','inactivo','pendiente','rechazado') NOT NULL DEFAULT 'pendiente',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `primer_acceso` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=debe cambiar contraseña',
  `token_recuperacion` varchar(255) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `institucion_id`, `nombre`, `apellido`, `dni`, `email`, `password_hash`, `rol`, `estado`, `foto_perfil`, `telefono`, `fecha_nacimiento`, `domicilio`, `primer_acceso`, `token_recuperacion`, `token_expiracion`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', 'Sistema', '00000000', 'admin@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'admin', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-06-13 17:50:20', '2026-06-09 03:57:23', '2026-06-13 20:50:20'),
(2, 1, 'Juan', 'Directivo', '11111111', 'directivo@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'directivo', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(3, 1, 'María', 'Preceptor', '22222222', 'preceptor@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, '01162165019', NULL, NULL, 0, NULL, NULL, NULL, '2026-06-09 03:57:23', '2026-06-10 04:54:33'),
(4, 1, 'Lucas', 'Alumno', NULL, 'lucas.alumno@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(5, 1, 'Carlos', 'Padre', '33333333', 'carlos.padre@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(10, 1, 'Sofía', 'Pereyra', '30111222', 'sofia.pereyra@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, '1122334455', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(11, 1, 'Martín', 'Gómez', '30222333', 'martin.gomez@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, '1122334456', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(12, 1, 'Paula', 'Rivas', '30333444', 'paula.rivas@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, '1122334457', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(13, 1, 'Diego', 'Fernández', '30444555', 'diego.fernandez@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, '1122334458', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(14, 1, 'Valeria', 'Suárez', '30555666', 'valeria.suarez@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, '1122334459', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(15, 1, 'Laura', 'Directora', '30666777', 'laura.directora@eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'directivo', 'activo', NULL, '1122334460', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-01 11:00:00', '2026-03-01 11:00:00'),
(100, 1, 'Agustín', 'Acuña', NULL, 'agustin.acuña1@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(101, 1, 'Benjamín', 'Díaz', NULL, 'benjamin.diaz2@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(102, 1, 'Camila', 'García', NULL, 'camila.garcia3@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(103, 1, 'Delfina', 'Juárez', NULL, 'delfina.juarez4@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(104, 1, 'Emilia', 'Molina', NULL, 'emilia.molina5@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(105, 1, 'Facundo', 'Paz', NULL, 'facundo.paz6@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(106, 1, 'Giuliana', 'Silva', NULL, 'giuliana.silva7@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(107, 1, 'Ignacio', 'Vega', NULL, 'ignacio.vega8@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(108, 1, 'Jazmín', 'Zapata', NULL, 'jazmin.zapata9@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(109, 1, 'Lautaro', 'Cabrera', NULL, 'lautaro.cabrera10@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(110, 1, 'Martina', 'Ferreyra', NULL, 'martina.ferreyra11@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(111, 1, 'Nicolás', 'Ibarra', NULL, 'nicolas.ibarra12@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(112, 1, 'Olivia', 'López', NULL, 'olivia.lopez13@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(113, 1, 'Pedro', 'Ojeda', NULL, 'pedro.ojeda14@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(114, 1, 'Rocío', 'Romero', NULL, 'rocio.romero15@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(115, 1, 'Santiago', 'Urquiza', NULL, 'santiago.urquiza16@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(116, 1, 'Tiziana', 'Yáñez', NULL, 'tiziana.yañez17@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(117, 1, 'Valentino', 'Benítez', NULL, 'valentino.benitez18@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(118, 1, 'Abril', 'Escobar', NULL, 'abril.escobar19@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(119, 1, 'Bruno', 'Herrera', NULL, 'bruno.herrera20@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(120, 1, 'Catalina', 'Klein', NULL, 'catalina.klein21@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(121, 1, 'Dante', 'Núñez', NULL, 'dante.nuñez22@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(122, 1, 'Elena', 'Quiroga', NULL, 'elena.quiroga23@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(123, 1, 'Franco', 'Torres', NULL, 'franco.torres24@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(124, 1, 'Guadalupe', 'Wagner', NULL, 'guadalupe.wagner25@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(125, 1, 'Iván', 'Acuña', NULL, 'ivan.acuña26@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(126, 1, 'Josefina', 'Díaz', NULL, 'josefina.diaz27@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(127, 1, 'Kevin', 'García', NULL, 'kevin.garcia28@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(128, 1, 'Lucía', 'Juárez', NULL, 'lucia.juarez29@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(129, 1, 'Mateo', 'Molina', NULL, 'mateo.molina30@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(130, 1, 'Noelia', 'Paz', NULL, 'noelia.paz31@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(131, 1, 'Octavio', 'Silva', NULL, 'octavio.silva32@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(132, 1, 'Pilar', 'Vega', NULL, 'pilar.vega33@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(133, 1, 'Ramiro', 'Zapata', NULL, 'ramiro.zapata34@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(134, 1, 'Sofía', 'Cabrera', NULL, 'sofia.cabrera35@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(135, 1, 'Tomás', 'Ferreyra', NULL, 'tomas.ferreyra36@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(136, 1, 'Uma', 'Ibarra', NULL, 'uma.ibarra37@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(137, 1, 'Victoria', 'López', NULL, 'victoria.lopez38@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(138, 1, 'Walter', 'Ojeda', NULL, 'walter.ojeda39@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(139, 1, 'Zoe', 'Romero', NULL, 'zoe.romero40@alumnos.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(200, 1, 'Carlos', 'Acuña', '35000000', 'tutor.acuña1@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000000', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(201, 1, 'María', 'Díaz', '35000001', 'tutor.diaz2@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000001', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(202, 1, 'Ana', 'García', '35000002', 'tutor.garcia3@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000002', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(203, 1, 'Jorge', 'Juárez', '35000003', 'tutor.juarez4@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000003', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(204, 1, 'Claudia', 'Molina', '35000004', 'tutor.molina5@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000004', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(205, 1, 'Roberto', 'Paz', '35000005', 'tutor.paz6@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000005', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(206, 1, 'Patricia', 'Silva', '35000006', 'tutor.silva7@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000006', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(207, 1, 'Miguel', 'Vega', '35000007', 'tutor.vega8@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000007', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(208, 1, 'Carlos', 'Zapata', '35000008', 'tutor.zapata9@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000008', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(209, 1, 'María', 'Cabrera', '35000009', 'tutor.cabrera10@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000009', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(210, 1, 'Ana', 'Ferreyra', '35000010', 'tutor.ferreyra11@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000010', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(211, 1, 'Jorge', 'Ibarra', '35000011', 'tutor.ibarra12@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000011', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(212, 1, 'Claudia', 'López', '35000012', 'tutor.lopez13@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000012', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(213, 1, 'Roberto', 'Ojeda', '35000013', 'tutor.ojeda14@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000013', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(214, 1, 'Patricia', 'Romero', '35000014', 'tutor.romero15@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000014', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(215, 1, 'Miguel', 'Urquiza', '35000015', 'tutor.urquiza16@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000015', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(216, 1, 'Carlos', 'Yáñez', '35000016', 'tutor.yañez17@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000016', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(217, 1, 'María', 'Benítez', '35000017', 'tutor.benitez18@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000017', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(218, 1, 'Ana', 'Escobar', '35000018', 'tutor.escobar19@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000018', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(219, 1, 'Jorge', 'Herrera', '35000019', 'tutor.herrera20@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000019', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(220, 1, 'Claudia', 'Klein', '35000020', 'tutor.klein21@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000020', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(221, 1, 'Roberto', 'Núñez', '35000021', 'tutor.nuñez22@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000021', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(222, 1, 'Patricia', 'Quiroga', '35000022', 'tutor.quiroga23@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000022', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(223, 1, 'Miguel', 'Torres', '35000023', 'tutor.torres24@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000023', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(224, 1, 'Carlos', 'Wagner', '35000024', 'tutor.wagner25@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000024', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(225, 1, 'María', 'Acuña', '35000025', 'tutor.acuña26@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000025', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(226, 1, 'Ana', 'Díaz', '35000026', 'tutor.diaz27@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000026', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(227, 1, 'Jorge', 'García', '35000027', 'tutor.garcia28@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000027', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(228, 1, 'Claudia', 'Juárez', '35000028', 'tutor.juarez29@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000028', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(229, 1, 'Roberto', 'Molina', '35000029', 'tutor.molina30@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000029', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(230, 1, 'Patricia', 'Paz', '35000030', 'tutor.paz31@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000030', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(231, 1, 'Miguel', 'Silva', '35000031', 'tutor.silva32@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000031', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(232, 1, 'Carlos', 'Vega', '35000032', 'tutor.vega33@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000032', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(233, 1, 'María', 'Zapata', '35000033', 'tutor.zapata34@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000033', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(234, 1, 'Ana', 'Cabrera', '35000034', 'tutor.cabrera35@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000034', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(235, 1, 'Jorge', 'Ferreyra', '35000035', 'tutor.ferreyra36@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000035', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(236, 1, 'Claudia', 'Ibarra', '35000036', 'tutor.ibarra37@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000036', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(237, 1, 'Roberto', 'López', '35000037', 'tutor.lopez38@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000037', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(238, 1, 'Patricia', 'Ojeda', '35000038', 'tutor.ojeda39@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000038', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(239, 1, 'Miguel', 'Romero', '35000039', 'tutor.romero40@familias.eest1.edu.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, '1140000039', NULL, NULL, 0, NULL, NULL, NULL, '2026-03-05 11:00:00', '2026-03-05 11:00:00'),
(240, 1, 'LUCIANO', 'CAMPANELLI', '48648179', 'luchocampanelli1@gmail.com', '$2y$10$UgUhhFH5QLyKzd8KdIZndupPwHjnxYiJXumRPZl3WMMl./CooHXdi', 'directivo', 'activo', NULL, '01162165019', NULL, NULL, 1, NULL, NULL, NULL, '2026-06-10 04:59:04', '2026-06-10 05:24:06'),
(241, 1, 'Usuario', 'Prueba', NULL, 'usuario.t1vl@gmail.com', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'alumno', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-06-12 19:53:52', '2026-06-12 19:53:52'),
(242, 1, 'Preceptor', 'Prueba', '40111222', 'preceptor@abc.gob.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'preceptor', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-06-13 17:03:34', '2026-06-12 19:53:52', '2026-06-13 20:03:34'),
(243, 1, 'Directivo', 'Prueba', '40222333', 'directivo@abc.gob.ar', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'directivo', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-06-13 17:34:49', '2026-06-12 19:53:52', '2026-06-13 20:34:49'),
(244, 1, 'Padre Tutor', 'Prueba', '40333444', 'padretutor@gmail.com', '$2y$10$Z.E6KprO578jArO26Y4G5uQlmb8AXa9jeH6I4gQCy/S.nMm6gXgqe', 'padre_tutor', 'activo', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-06-12 19:53:52', '2026-06-12 19:53:52'),
(245, 1, 'LUCIANO', '', NULL, 'luchocampanelli@gmail.com', '$2y$10$Qiy/05IFv1UK6agx9OBBx.5Tc09PAgJbIQLicPUGptnqSIfxq7cYi', 'alumno', 'pendiente', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-06-13 21:08:56', '2026-06-13 21:08:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vinculaciones`
--

CREATE TABLE `vinculaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `padre_tutor_id` int(10) UNSIGNED NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `relacion` enum('padre','madre','tutor','otro') NOT NULL DEFAULT 'tutor',
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `aprobado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `token_vinculacion` varchar(255) DEFAULT NULL COMMENT 'Para opción 1: link por email',
  `token_expiracion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vinculaciones`
--

INSERT INTO `vinculaciones` (`id`, `padre_tutor_id`, `alumno_id`, `relacion`, `estado`, `aprobado_por`, `fecha_aprobacion`, `token_vinculacion`, `token_expiracion`, `created_at`, `updated_at`) VALUES
(1, 5, 4, 'padre', 'aprobado', 2, '2026-06-09 00:57:23', NULL, NULL, '2026-06-09 03:57:23', '2026-06-09 03:57:23'),
(100, 200, 100, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(101, 201, 101, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(102, 202, 102, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(103, 203, 103, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(104, 204, 104, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(105, 205, 105, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(106, 206, 106, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(107, 207, 107, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(108, 208, 108, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(109, 209, 109, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(110, 210, 110, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(111, 211, 111, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(112, 212, 112, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(113, 213, 113, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(114, 214, 114, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(115, 215, 115, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(116, 216, 116, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(117, 217, 117, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(118, 218, 118, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(119, 219, 119, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(120, 220, 120, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(121, 221, 121, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(122, 222, 122, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(123, 223, 123, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(124, 224, 124, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(125, 225, 125, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(126, 226, 126, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(127, 227, 127, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(128, 228, 128, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(129, 229, 129, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(130, 230, 130, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(131, 231, 131, 'madre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(132, 232, 132, 'tutor', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(133, 233, 133, 'padre', 'aprobado', 2, '2026-03-06 10:00:00', NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(134, 234, 134, 'madre', 'pendiente', NULL, NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(135, 235, 135, 'tutor', 'pendiente', NULL, NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(136, 236, 136, 'padre', 'pendiente', NULL, NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(137, 237, 137, 'madre', 'pendiente', NULL, NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(138, 238, 138, 'tutor', 'pendiente', NULL, NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00'),
(139, 239, 139, 'padre', 'pendiente', NULL, NULL, NULL, NULL, '2026-03-05 12:00:00', '2026-03-05 12:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno_cursos`
--
ALTER TABLE `alumno_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_alumno_curso_ciclo` (`alumno_id`,`ciclo_lectivo`),
  ADD KEY `idx_curso` (`curso_id`);

--
-- Indices de la tabla `asignaciones_materias`
--
ALTER TABLE `asignaciones_materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_materia` (`materia_id`),
  ADD KEY `idx_preceptor` (`preceptor_id`);

--
-- Indices de la tabla `auditoria_asistencias`
--
ALTER TABLE `auditoria_asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auditoria_registro` (`registro_id`),
  ADD KEY `idx_auditoria_alumno` (`alumno_id`),
  ADD KEY `idx_auditoria_usuario` (`usuario_id`);

--
-- Indices de la tabla `backups_registro`
--
ALTER TABLE `backups_registro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bk_usuario` (`creado_por`);

--
-- Indices de la tabla `bloques_horarios`
--
ALTER TABLE `bloques_horarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bloques_turno_bloque` (`turno`,`bloque_horario`);

--
-- Indices de la tabla `cierres_mensuales`
--
ALTER TABLE `cierres_mensuales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_anio_mes_institucion` (`anio`,`mes`,`institucion_id`),
  ADD KEY `idx_cerrador` (`cerrado_por`),
  ADD KEY `fk_cm_institucion` (`institucion_id`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_clave` (`clave`);

--
-- Indices de la tabla `conversaciones`
--
ALTER TABLE `conversaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `conversacion_participantes`
--
ALTER TABLE `conversacion_participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_conv_user` (`conversacion_id`,`usuario_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_anio_division` (`anio`,`division`),
  ADD KEY `idx_turno` (`turno`),
  ADD KEY `idx_ciclo` (`ciclo_lectivo`),
  ADD KEY `fk_cursos_especialidad` (`especialidad_id`),
  ADD KEY `fk_cursos_institucion` (`institucion_id`);

--
-- Indices de la tabla `curso_turnos`
--
ALTER TABLE `curso_turnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_curso_turno` (`curso_id`,`turno`),
  ADD KEY `idx_curso_turnos_curso` (`curso_id`);

--
-- Indices de la tabla `detalles_asistencia`
--
ALTER TABLE `detalles_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_registro_alumno` (`registro_id`,`alumno_id`),
  ADD KEY `idx_alumno` (`alumno_id`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_codigo` (`codigo`);

--
-- Indices de la tabla `historial_cambios_asistencia`
--
ALTER TABLE `historial_cambios_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_detalle` (`detalle_id`),
  ADD KEY `idx_registro` (`registro_id`),
  ADD KEY `idx_alumno` (`alumno_id`),
  ADD KEY `idx_modificador` (`modificado_por`);

--
-- Indices de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alumno` (`alumno_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_enviado_por` (`enviado_por`),
  ADD KEY `fk_jus_detalle` (`detalle_id`),
  ADD KEY `fk_jus_revisor` (`revisado_por`);

--
-- Indices de la tabla `log_actividad`
--
ALTER TABLE `log_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_accion` (`accion`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_materias_especialidad` (`especialidad_id`),
  ADD KEY `fk_materias_institucion` (`institucion_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversacion` (`conversacion_id`),
  ADD KEY `idx_remitente` (`remitente_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_creador` (`creado_por`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `fk_not_curso` (`curso_id`);

--
-- Indices de la tabla `notificaciones_leidas`
--
ALTER TABLE `notificaciones_leidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_notif_user` (`notificacion_id`,`usuario_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `preceptor_cursos`
--
ALTER TABLE `preceptor_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_preceptor_curso_ciclo` (`preceptor_id`,`curso_id`,`ciclo_lectivo`),
  ADD KEY `idx_curso` (`curso_id`);

--
-- Indices de la tabla `reemplazos_preceptores`
--
ALTER TABLE `reemplazos_preceptores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_titular` (`preceptor_titular_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_materia` (`materia_id`),
  ADD KEY `fk_rp_reemplazante` (`preceptor_reemplazante_id`),
  ADD KEY `fk_rp_curso` (`curso_id`),
  ADD KEY `fk_rp_asignador` (`asignado_por`);

--
-- Indices de la tabla `registros_asistencia`
--
ALTER TABLE `registros_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_curso_materia_fecha_modulo` (`curso_id`,`materia_id`,`fecha`,`modulo_horario`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_preceptor` (`preceptor_id`),
  ADD KEY `idx_materia` (`materia_id`),
  ADD KEY `idx_cierre_mensual` (`cierre_mensual_id`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `resumen_asistencia_diaria`
--
ALTER TABLE `resumen_asistencia_diaria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_resumen_asistencia` (`alumno_id`,`curso_id`,`fecha`,`turno`),
  ADD KEY `idx_resumen_curso_fecha` (`curso_id`,`fecha`);

--
-- Indices de la tabla `solicitudes_acceso`
--
ALTER TABLE `solicitudes_acceso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_tipo_estado` (`tipo`,`estado`),
  ADD KEY `fk_sa_revisor` (`revisado_por`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email` (`email`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `fk_usuarios_institucion` (`institucion_id`);

--
-- Indices de la tabla `vinculaciones`
--
ALTER TABLE `vinculaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_padre_alumno` (`padre_tutor_id`,`alumno_id`),
  ADD KEY `idx_alumno` (`alumno_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `fk_vin_aprobador` (`aprobado_por`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumno_cursos`
--
ALTER TABLE `alumno_cursos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT de la tabla `asignaciones_materias`
--
ALTER TABLE `asignaciones_materias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT de la tabla `auditoria_asistencias`
--
ALTER TABLE `auditoria_asistencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `backups_registro`
--
ALTER TABLE `backups_registro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `bloques_horarios`
--
ALTER TABLE `bloques_horarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cierres_mensuales`
--
ALTER TABLE `cierres_mensuales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `conversaciones`
--
ALTER TABLE `conversaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT de la tabla `conversacion_participantes`
--
ALTER TABLE `conversacion_participantes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `curso_turnos`
--
ALTER TABLE `curso_turnos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `detalles_asistencia`
--
ALTER TABLE `detalles_asistencia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1484;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_cambios_asistencia`
--
ALTER TABLE `historial_cambios_asistencia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT de la tabla `log_actividad`
--
ALTER TABLE `log_actividad`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT de la tabla `notificaciones_leidas`
--
ALTER TABLE `notificaciones_leidas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `preceptor_cursos`
--
ALTER TABLE `preceptor_cursos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `reemplazos_preceptores`
--
ALTER TABLE `reemplazos_preceptores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `registros_asistencia`
--
ALTER TABLE `registros_asistencia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1154;

--
-- AUTO_INCREMENT de la tabla `resumen_asistencia_diaria`
--
ALTER TABLE `resumen_asistencia_diaria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitudes_acceso`
--
ALTER TABLE `solicitudes_acceso`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT de la tabla `vinculaciones`
--
ALTER TABLE `vinculaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno_cursos`
--
ALTER TABLE `alumno_cursos`
  ADD CONSTRAINT `fk_ac_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ac_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asignaciones_materias`
--
ALTER TABLE `asignaciones_materias`
  ADD CONSTRAINT `fk_am_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_am_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_am_preceptor` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria_asistencias`
--
ALTER TABLE `auditoria_asistencias`
  ADD CONSTRAINT `fk_aa_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_aa_registro` FOREIGN KEY (`registro_id`) REFERENCES `registros_asistencia` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `backups_registro`
--
ALTER TABLE `backups_registro`
  ADD CONSTRAINT `fk_bk_usuario` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cierres_mensuales`
--
ALTER TABLE `cierres_mensuales`
  ADD CONSTRAINT `fk_cm_cerrador` FOREIGN KEY (`cerrado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cm_institucion` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `conversacion_participantes`
--
ALTER TABLE `conversacion_participantes`
  ADD CONSTRAINT `fk_cp_conv` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cp_user` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_cursos_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`),
  ADD CONSTRAINT `fk_cursos_institucion` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `curso_turnos`
--
ALTER TABLE `curso_turnos`
  ADD CONSTRAINT `fk_ct_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalles_asistencia`
--
ALTER TABLE `detalles_asistencia`
  ADD CONSTRAINT `fk_da_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_da_registro` FOREIGN KEY (`registro_id`) REFERENCES `registros_asistencia` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_cambios_asistencia`
--
ALTER TABLE `historial_cambios_asistencia`
  ADD CONSTRAINT `fk_hca_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hca_detalle` FOREIGN KEY (`detalle_id`) REFERENCES `detalles_asistencia` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hca_modificador` FOREIGN KEY (`modificado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_hca_registro` FOREIGN KEY (`registro_id`) REFERENCES `registros_asistencia` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD CONSTRAINT `fk_jus_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_jus_detalle` FOREIGN KEY (`detalle_id`) REFERENCES `detalles_asistencia` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_jus_enviado` FOREIGN KEY (`enviado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_jus_revisor` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `log_actividad`
--
ALTER TABLE `log_actividad`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `fk_materias_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_materias_institucion` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_msg_conv` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_remitente` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_not_creador` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_not_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `notificaciones_leidas`
--
ALTER TABLE `notificaciones_leidas`
  ADD CONSTRAINT `fk_nl_notif` FOREIGN KEY (`notificacion_id`) REFERENCES `notificaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nl_user` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `preceptor_cursos`
--
ALTER TABLE `preceptor_cursos`
  ADD CONSTRAINT `fk_pc_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pc_preceptor` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reemplazos_preceptores`
--
ALTER TABLE `reemplazos_preceptores`
  ADD CONSTRAINT `fk_rp_asignador` FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rp_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `fk_rp_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rp_reemplazante` FOREIGN KEY (`preceptor_reemplazante_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rp_titular` FOREIGN KEY (`preceptor_titular_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `registros_asistencia`
--
ALTER TABLE `registros_asistencia`
  ADD CONSTRAINT `fk_ra_cierre` FOREIGN KEY (`cierre_mensual_id`) REFERENCES `cierres_mensuales` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ra_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `fk_ra_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`),
  ADD CONSTRAINT `fk_ra_preceptor` FOREIGN KEY (`preceptor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `resumen_asistencia_diaria`
--
ALTER TABLE `resumen_asistencia_diaria`
  ADD CONSTRAINT `fk_rad_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rad_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_acceso`
--
ALTER TABLE `solicitudes_acceso`
  ADD CONSTRAINT `fk_sa_revisor` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_institucion` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `vinculaciones`
--
ALTER TABLE `vinculaciones`
  ADD CONSTRAINT `fk_vin_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vin_aprobador` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_vin_padre` FOREIGN KEY (`padre_tutor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
