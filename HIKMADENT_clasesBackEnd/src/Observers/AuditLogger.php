<?php

declare(strict_types=1);

namespace HikmaDent\Observers;

/**
 * AuditLogger - Observador de Trazabilidad para Business Intelligence.
 *
 * Se suscribe a TODOS los eventos del WorkflowManager.
 * Cada cambio de área queda registrado con Timestamp y OperadorId
 * en un log que simula la tabla de auditoría de la base de datos BI.
 *
 * En producción, este método haría un INSERT en la tabla `auditoria_workflow`.
 */
class AuditLogger
{
    /** @var array<int, array{evento, ordenId, operadorId, timestamp, datos}> */
    private array $registros = [];

    /**
     * Manejador del evento. Se ejecuta automáticamente cuando WorkflowManager
     * dispara un evento mediante IObservable::disparar().
     *
     * @param array $datos Payload del evento: {evento, ordenId, operadorId, areaAnterior, areaNueva, timestamp}
     */
    public function manejarEvento(array $datos): void
    {
        $registro = [
            'evento'       => $datos['evento']      ?? 'desconocido',
            'ordenId'      => $datos['ordenId']     ?? 0,
            'operadorId'   => $datos['operadorId']  ?? 0,
            'areaAnterior' => $datos['areaAnterior'] ?? '-',
            'areaNueva'    => $datos['areaNueva']   ?? '-',
            'timestamp'    => $datos['timestamp']   ?? (new \DateTime())->format('Y-m-d H:i:s'),
            'detalles'     => $datos['detalles']    ?? [],
        ];

        $this->registros[] = $registro;

        // Simulación de INSERT en BD — En producción reemplazar con PDO/Eloquent
        echo sprintf(
            "[AuditLogger] 📋 [%s] Orden #%d | Operador #%d | %s → %s\n",
            $registro['timestamp'],
            $registro['ordenId'],
            $registro['operadorId'],
            $registro['areaAnterior'],
            $registro['areaNueva']
        );
    }

    /**
     * Retorna todos los registros de auditoría (para el EfficiencyReportService).
     */
    public function getRegistros(): array
    {
        return $this->registros;
    }

    /**
     * Exporta los registros como JSON para almacenamiento o análisis BI externo.
     */
    public function exportarComoJson(): string
    {
        return json_encode($this->registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Filtra los registros por orden específica.
     */
    public function filtrarPorOrden(int $ordenId): array
    {
        return array_filter($this->registros, fn($r) => $r['ordenId'] === $ordenId);
    }
}
