<?php
/**
 * Partial reutilizable: filas de la tabla "Asistencia Institucional".
 * Espera $registrosAsistencia (misma forma que arma DirectivoController::portalData()
 * y DirectivoController::filtrarAsistenciaAjax()). Incluido tanto en el render
 * inicial de directivo.php como en la respuesta AJAX del filtro, para no
 * duplicar el markup.
 */
if (empty($registrosAsistencia)): ?>
  <tr><td colspan="6" style="text-align:center;color:var(--gris-texto)">No hay registros de asistencia.</td></tr>
<?php else: foreach ($registrosAsistencia as $item):
    $reg = $item['reg']; $conteo = $item['conteo'];
    $iniciales = mb_strtoupper(mb_substr($reg['preceptor_nombre'], 0, 1) . mb_substr($reg['preceptor_apellido'], 0, 1));
    $estadoClaseMap = ['cerrada' => 'completo', 'modificada' => 'en-proceso', 'abierta' => 'sin-asignar', 'anulada' => 'sin-asignar'];
    $estadoClase = $estadoClaseMap[$reg['estado_calculado']] ?? 'sin-asignar';
?>
  <tr>
    <td>
      <div><strong><?= e($reg['anio']) ?> <?= e($reg['division']) ?>°</strong></div>
      <div style="font-size:12px; color:var(--gris-texto);"><?= e($reg['materia_nombre']) ?></div>
    </td>
    <td>
      <div><?= e(format_date_short_argentina($reg['fecha'])) ?></div>
      <div style="font-size:12px; color:var(--gris-texto);"><?= $reg['hora_inicio'] ? e(substr($reg['hora_inicio'], 0, 5)) : '' ?></div>
    </td>
    <td>
      <div style="display:flex; align-items:center; gap:8px;">
        <div class="avatar-iniciales"><?= e($iniciales) ?></div>
        <?= e($reg['preceptor_nombre'] . ' ' . $reg['preceptor_apellido']) ?>
      </div>
    </td>
    <td>
      <div class="pataj">
        <span><?= e($conteo['presente']) ?></span><span> / </span><span><?= e($conteo['ausente']) ?></span><span> / </span><span><?= e($conteo['llegada_tarde']) ?></span><span style="color:var(--gris-texto);"> / </span><span><?= e($conteo['justificado']) ?></span>
      </div>
    </td>
    <td><span class="indicador-estado indicador-estado--<?= e($estadoClase) ?>"><?= e(ucfirst($reg['estado_calculado'])) ?></span></td>
    <td>
      <button class="btn-accion btn-accion--ver"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
    </td>
  </tr>
<?php endforeach; endif; ?>
