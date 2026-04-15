<?php

declare(strict_types=1);

namespace HikmaDent\Services;

use HikmaDent\Observers\AuditLogger;

/**
 * EfficiencyReportService - Servicio de Análisis de Eficiencia (BI).
 *
 * Implementa GetEfficiencyReport() para calcular el tiempo promedio
 * que cada OrdenTrabajo pasa en cada una de las interfaces IProcesable.
 *
 * Fuente de datos: Los registros del AuditLogger (tabla de auditoría).
 * Salida: Reporte estructurado listo para dashboard BI.
 */
class EfficiencyReportService
{
    public function __construct(
        private AuditLogger $auditLogger
    ) {}

    /**
     * Genera el reporte de eficiencia global del laboratorio HIKMADENT.
     *
     * Calcula:
     * - Tiempo promedio por área (en minutos)
     * - Número de transiciones por área
     * - Áreas con mayor tiempo de retención (cuellos de botella)
     * - Órdenes retrocedidas (indicador de calidad)
     *
     * @return array Reporte estructurado para consumo del dashboard.
     */
    public function GetEfficiencyReport(): array
    {
        $registros = $this->auditLogger->getRegistros();

        if (empty($registros)) {
            return ['mensaje' => 'Sin datos de auditoría disponibles.', 'areas' => []];
        }

        // Agrupar registros por área (areaAnterior = área que procesó)
        $tiemposPorArea  = [];
        $conteoTransiciones = [];

        foreach ($registros as $i => $registro) {
            $area = $registro['areaAnterior'];

            // Calcular tiempo entre esta transición y la anterior para la misma orden
            if ($i > 0) {
                $anterior  = $registros[$i - 1];
                $tsActual  = new \DateTime($registro['timestamp']);
                $tsAnterior= new \DateTime($anterior['timestamp']);
                $diffSeg   = $tsActual->getTimestamp() - $tsAnterior->getTimestamp();
                $diffMin   = max(0, round($diffSeg / 60, 2));

                $tiemposPorArea[$area][]    = $diffMin;
            }

            $conteoTransiciones[$area] = ($conteoTransiciones[$area] ?? 0) + 1;
        }

        // Calcular promedios
        $reporteAreas = [];
        foreach ($tiemposPorArea as $area => $tiempos) {
            $promedio = round(array_sum($tiempos) / count($tiempos), 2);
            $reporteAreas[$area] = [
                'area'              => $area,
                'tiempo_promedio_min' => $promedio,
                'total_transiciones'  => $conteoTransiciones[$area] ?? 0,
                'min_tiempo'          => min($tiempos),
                'max_tiempo'          => max($tiempos),
            ];
        }

        // Detectar cuellos de botella (área con mayor tiempo promedio)
        $cuellosBotella = [];
        if (!empty($reporteAreas)) {
            usort($reporteAreas, fn($a, $b) => $b['tiempo_promedio_min'] <=> $a['tiempo_promedio_min']);
            $cuellosBotella = array_slice($reporteAreas, 0, 2);
        }

        // Contar retrocesos (indicador de calidad)
        $retrocesos = array_filter($registros, fn($r) => ($r['tipoTransicion'] ?? '') === 'retroceso');

        $reporte = [
            'generado_en'        => (new \DateTime())->format('Y-m-d H:i:s'),
            'total_transiciones' => count($registros),
            'total_retrocesos'   => count($retrocesos),
            'indice_calidad'     => count($registros) > 0
                ? round((1 - count($retrocesos) / count($registros)) * 100, 1) . '%'
                : '100%',
            'areas'              => array_values($reporteAreas),
            'cuellos_de_botella' => $cuellosBotella,
        ];

        $this->imprimirReporte($reporte);
        return $reporte;
    }

    private function imprimirReporte(array $reporte): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════╗\n";
        echo "║       REPORTE DE EFICIENCIA — HIKMADENT LAB              ║\n";
        echo "╠══════════════════════════════════════════════════════════╣\n";
        echo "║ Generado : {$reporte['generado_en']}                  \n";
        echo "║ Transiciones totales : {$reporte['total_transiciones']}\n";
        echo "║ Retrocesos (errores) : {$reporte['total_retrocesos']}\n";
        echo "║ Índice de calidad    : {$reporte['indice_calidad']}\n";
        echo "╠══════════════════════════════════════════════════════════╣\n";
        echo "║ TIEMPOS POR ÁREA (promedio en minutos):\n";
        foreach ($reporte['areas'] as $area) {
            echo sprintf("║   %-15s → Prom: %5.2f min | Trx: %d\n",
                $area['area'],
                $area['tiempo_promedio_min'],
                $area['total_transiciones']
            );
        }
        echo "╚══════════════════════════════════════════════════════════╝\n\n";
    }
}
