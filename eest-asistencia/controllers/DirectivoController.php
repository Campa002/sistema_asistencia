<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * Datos reales para el portal (SPA server-rendered) de Directivo. Reutiliza
 * Reporte.php (mismas consultas que ya usa Admin → Reportes/Dashboard) y
 * Asistencia.php — no duplica lógica de cálculo de faltas.
 */
class DirectivoController {

    public static function portalData(int $directivoId): array {
        $db = Database::getConnection();
        $usuario = Usuario::getById($directivoId);

        $stats = Reporte::getReportStats([]);

        $stmtP = $db->query("SELECT estado, COUNT(*) n FROM detalles_asistencia GROUP BY estado");
        $porEstado = [];
        foreach ($stmtP->fetchAll(PDO::FETCH_ASSOC) as $r) $porEstado[$r['estado']] = (int) $r['n'];
        $presentes = ($porEstado['presente'] ?? 0) + ($porEstado['llegada_tarde'] ?? 0) + ($porEstado['ausente_con_presente'] ?? 0);
        $ausentes = $porEstado['ausente'] ?? 0;

        $solicitudesPendientes = (int) $db->query("SELECT COUNT(*) FROM solicitudes_acceso WHERE estado = 'pendiente'")->fetchColumn();
        $solicitudesTotal = (int) $db->query("SELECT COUNT(*) FROM solicitudes_acceso")->fetchColumn();
        $reemplazosSinAsignar = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores WHERE estado = 'sin_asignar'")->fetchColumn();
        $reemplazosAsignadosHoy = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores WHERE estado IN ('asignado','realizado') AND fecha = CURDATE()")->fetchColumn();
        $reemplazosTotal = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores")->fetchColumn();
        $reemplazosRealizados = (int) $db->query("SELECT COUNT(*) FROM reemplazos_preceptores WHERE estado = 'realizado'")->fetchColumn();
        $efectividadCobertura = $reemplazosTotal > 0 ? round($reemplazosRealizados / $reemplazosTotal * 100, 1) : 0;

        $preceptoresActivos = count(Usuario::getAll(['rol' => 'preceptor', 'estado' => 'activo']));
        $preceptoresTotal = count(Usuario::getAll(['rol' => 'preceptor']));
        $preceptoresDisponibles = max(0, $preceptoresActivos - self::preceptoresConReemplazoHoy($db));

        $ultimaCarga = $db->query("SELECT MAX(created_at) FROM registros_asistencia")->fetchColumn();

        // Gráfico: presentes/ausentes reales por curso, mañana vs. tarde
        $graficoManana = self::presentesAusentesPorCurso($db, 'mañana');
        $graficoTarde = self::presentesAusentesPorCurso($db, 'tarde');

        // Avisos recientes (los más nuevos, dirigidos a directivo o a todos)
        $avisos = self::notificacionesPara($db, $directivoId, ['aviso', 'alerta', 'recordatorio'], 2);

        // Solicitudes de acceso reales
        $stmtS = $db->query("SELECT sa.*, u.nombre, u.apellido, u.dni, u.email, u.telefono
                              FROM solicitudes_acceso sa
                              JOIN usuarios u ON u.id = sa.usuario_id
                              ORDER BY sa.created_at DESC LIMIT 30");
        $solicitudes = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Reemplazos reales
        $stmtR = $db->query("SELECT rp.*, c.anio, c.division, esp.nombre AS especialidad_nombre,
                                     ut.nombre AS titular_nombre, ut.apellido AS titular_apellido,
                                     ur.nombre AS reemplazante_nombre, ur.apellido AS reemplazante_apellido
                              FROM reemplazos_preceptores rp
                              JOIN cursos c ON c.id = rp.curso_id
                              LEFT JOIN especialidades esp ON esp.id = c.especialidad_id
                              JOIN usuarios ut ON ut.id = rp.preceptor_titular_id
                              LEFT JOIN usuarios ur ON ur.id = rp.preceptor_reemplazante_id
                              ORDER BY rp.fecha DESC LIMIT 30");
        $reemplazos = $stmtR->fetchAll(PDO::FETCH_ASSOC);

        // Asistencia institucional: registros recientes de todos los cursos
        $resAsist = Asistencia::getAll([], 1, 20);
        $registrosAsistencia = [];
        foreach ($resAsist['registros'] as $reg) {
            $detalles = Asistencia::getDetallesByRegistroId($reg['id']);
            $conteo = ['presente' => 0, 'ausente' => 0, 'llegada_tarde' => 0, 'justificado' => 0];
            foreach ($detalles as $d) {
                if (isset($conteo[$d['estado']])) $conteo[$d['estado']]++;
            }
            $registrosAsistencia[] = ['reg' => $reg, 'conteo' => $conteo];
        }

        // Notificaciones (todo tipo salvo comunicado, ya cubierto en Admin)
        $notificaciones = self::notificacionesPara($db, $directivoId, ['aviso', 'alerta', 'recordatorio'], 30);

        return [
            'usuario' => $usuario,
            'statsGenerales' => $stats,
            'presentes' => $presentes,
            'ausentes' => $ausentes,
            'llegadasTarde' => (int) $stats['llegadas_tarde'],
            'justificaciones' => (int) $stats['justificaciones_total'],
            'solicitudesPendientes' => $solicitudesPendientes,
            'solicitudesTotal' => $solicitudesTotal,
            'reemplazosSinAsignar' => $reemplazosSinAsignar,
            'reemplazosAsignadosHoy' => $reemplazosAsignadosHoy,
            'reemplazosTotal' => $reemplazosTotal,
            'efectividadCobertura' => $efectividadCobertura,
            'preceptoresActivos' => $preceptoresActivos,
            'preceptoresTotal' => $preceptoresTotal,
            'preceptoresDisponibles' => $preceptoresDisponibles,
            'ultimaCarga' => $ultimaCarga,
            'graficoManana' => $graficoManana,
            'graficoTarde' => $graficoTarde,
            'avisos' => $avisos,
            'solicitudes' => $solicitudes,
            'reemplazos' => $reemplazos,
            'registrosAsistencia' => $registrosAsistencia,
            'notificaciones' => $notificaciones,
            'fechaHoyLarga' => format_date_long_argentina(),
        ];
    }

    /**
     * Presentes/ausentes reales por curso para un turno, usados en el
     * gráfico de barras apiladas del dashboard (antes con datos inventados).
     */
    private static function presentesAusentesPorCurso(PDO $db, string $turno): array {
        $stmt = $db->prepare("SELECT CONCAT(cur.anio, '° ', cur.division) AS division,
                    SUM(CASE WHEN det.estado != 'ausente' THEN 1 ELSE 0 END) AS presentes,
                    SUM(CASE WHEN det.estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes
                FROM detalles_asistencia det
                JOIN registros_asistencia reg ON det.registro_id = reg.id
                JOIN cursos cur ON reg.curso_id = cur.id
                WHERE reg.turno = ? AND reg.estado != 'anulada'
                GROUP BY cur.id, cur.anio, cur.division
                ORDER BY cur.anio, cur.division");
        $stmt->execute([$turno]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function preceptoresConReemplazoHoy(PDO $db): int {
        $stmt = $db->query("SELECT COUNT(DISTINCT preceptor_titular_id) FROM reemplazos_preceptores WHERE fecha = CURDATE() AND estado != 'cancelado'");
        return (int) $stmt->fetchColumn();
    }

    private static function notificacionesPara(PDO $db, int $usuarioId, array $tipos, int $limite): array {
        $in = implode(',', array_fill(0, count($tipos), '?'));
        $stmt = $db->prepare("SELECT n.*, (nl.id IS NOT NULL) AS leida
                               FROM notificaciones n
                               LEFT JOIN notificaciones_leidas nl ON nl.notificacion_id = n.id AND nl.usuario_id = ?
                               WHERE n.tipo IN ($in) AND n.activo = 1
                                 AND (FIND_IN_SET('directivo', n.rol_destino) > 0 OR FIND_IN_SET('todos', n.rol_destino) > 0)
                               ORDER BY n.created_at DESC LIMIT " . (int) $limite);
        $stmt->execute([$usuarioId, ...$tipos]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
