<?php

declare(strict_types=1);

namespace HikmaDent\Processes;

use HikmaDent\Contracts\IProcesable;
use HikmaDent\Models\Impresion\ModuloImpresion;
use HikmaDent\Models\Impresion\GestionPrioridad;
use HikmaDent\Models\Impresion\TransferenciaArea;
use HikmaDent\Enums\PriorityLevel;

/**
 * ProcesoImpresion - Implementación del área de Impresión.
 * Valida la impresión recibida, gestiona prioridades y transfiere a Calidad.
 */
class ProcesoImpresion implements IProcesable
{
    public function iniciarProceso(int $ordenId, int $operadorId): bool
    {
        echo "\n[ProcesoImpresion] === Iniciando para Orden #{$ordenId} (Operador #{$operadorId}) ===\n";

        $gestion      = new GestionPrioridad(PriorityLevel::NORMAL);
        $transferencia = new TransferenciaArea('Calidad');
        $modulo       = new ModuloImpresion(
            $ordenId, true, 'Yeso Tipo III',
            new \DateTime(), $gestion, $transferencia
        );

        $cola = $modulo->consultarPrioridadEnCola();
        echo "[ProcesoImpresion] ✅ Orden #{$ordenId} en cola. Posición: " . count($cola) . "\n";
        return true;
    }

    public function finalizarProceso(int $ordenId, int $operadorId): bool
    {
        echo "[ProcesoImpresion] Finalizando y transfiriendo Orden #{$ordenId} a Calidad...\n";
        $gestion      = new GestionPrioridad(PriorityLevel::NORMAL);
        $transferencia = new TransferenciaArea('Calidad');
        $modulo       = new ModuloImpresion(
            $ordenId, true, 'Yeso Tipo III',
            new \DateTime('-10 minutes'), $gestion, $transferencia
        );
        $evento = $modulo->finalizarYTransferir();
        echo "[ProcesoImpresion] ✅ Transferencia completada: " . json_encode($evento) . "\n";
        return true;
    }

    public function validarCalidad(int $ordenId): bool
    {
        echo "[ProcesoImpresion] Validando impresión para Orden #{$ordenId}.\n";
        return true;
    }

    public function getNombreArea(): string { return 'Impresion'; }
}
