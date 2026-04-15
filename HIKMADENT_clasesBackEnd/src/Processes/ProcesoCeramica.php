<?php

declare(strict_types=1);

namespace HikmaDent\Processes;

use HikmaDent\Contracts\IProcesable;
use HikmaDent\Models\Ceramica\OrdenImpresion;
use HikmaDent\Models\Ceramica\PostProcesado;
use HikmaDent\Models\Ceramica\ControlMantenimiento;

/**
 * ProcesoCeramica - Implementación del área Cerámica/Impresión 3D.
 * Gestiona los estados: Lavado → Fotocurado → Terminado.
 */
class ProcesoCeramica implements IProcesable
{
    public function iniciarProceso(int $ordenId, int $operadorId): bool
    {
        echo "\n[ProcesoCeramica] === Iniciando para Orden #{$ordenId} (Operador #{$operadorId}) ===\n";

        $postProcesado       = new PostProcesado(10, 15, 25.0);
        $controlMant         = new ControlMantenimiento(new \DateTime('-3 days'), 20);
        $ordenImpresion      = new OrdenImpresion($ordenId, new \DateTime(), $postProcesado, $controlMant);

        $valido = $postProcesado->validarProceso();
        if ($valido) {
            $ordenImpresion->actualizarEstado(); // Lavado → Fotocurado
            echo "[ProcesoCeramica] ✅ Proceso cerámico iniciado. Estado: {$ordenImpresion->getEstado()->value}\n";
        }
        return $valido;
    }

    public function finalizarProceso(int $ordenId, int $operadorId): bool
    {
        echo "[ProcesoCeramica] Avanzando a estado Terminado para Orden #{$ordenId}...\n";
        $postProcesado  = new PostProcesado(10, 15, 25.0);
        $controlMant    = new ControlMantenimiento(new \DateTime('-3 days'), 21);
        $ordenImpresion = new OrdenImpresion($ordenId, new \DateTime(), $postProcesado, $controlMant);

        $ordenImpresion->actualizarEstado(); // → Fotocurado
        $ordenImpresion->actualizarEstado(); // → Terminado
        echo "[ProcesoCeramica] ✅ Orden #{$ordenId} en estado: {$ordenImpresion->getEstado()->value}\n";
        return $ordenImpresion->estaTerminado();
    }

    public function validarCalidad(int $ordenId): bool
    {
        $postProcesado = new PostProcesado(10, 15, 25.0);
        return $postProcesado->validarProceso();
    }

    public function getNombreArea(): string { return 'Ceramica'; }
}
