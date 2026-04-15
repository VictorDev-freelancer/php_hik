<?php

declare(strict_types=1);

namespace HikmaDent\Processes;

use HikmaDent\Contracts\IProcesable;
use HikmaDent\Models\Dental\PiezaDental;
use HikmaDent\Models\Dental\Maquillaje;
use HikmaDent\Models\Dental\Ajuste;

/**
 * ProcesoDental - Implementación del área Dental/Cerámica artesanal.
 * Cubre el flujo: PiezaDental → Maquillaje → Ajuste → (RegistroIncidencia si falla)
 */
class ProcesoDental implements IProcesable
{
    public function iniciarProceso(int $ordenId, int $operadorId): bool
    {
        echo "\n[ProcesoDental] === Iniciando para Orden #{$ordenId} (Operador #{$operadorId}) ===\n";

        $pieza = new PiezaDental($ordenId, 'Corona', new \DateTime());
        $pieza->iniciarProcesoCeramico();

        $maquillaje = new Maquillaje('Estratificado', 45);
        $maquillaje->completarMaquillaje();

        echo "[ProcesoDental] ✅ PiezaDental iniciada y maquillaje programado.\n";
        return true;
    }

    public function finalizarProceso(int $ordenId, int $operadorId): bool
    {
        echo "[ProcesoDental] Ejecutando validación de Ajuste para Orden #{$ordenId}...\n";
        $ajuste = new Ajuste(formaAdaptacion: true, texturaAltura: true);
        $calidad = $ajuste->validarCalidad();
        if ($calidad) {
            echo "[ProcesoDental] ✅ Ajuste aprobado. Orden #{$ordenId} lista para siguiente área.\n";
        }
        return $calidad;
    }

    public function validarCalidad(int $ordenId): bool
    {
        $ajuste = new Ajuste(formaAdaptacion: true, texturaAltura: true);
        return $ajuste->validarCalidad();
    }

    public function getNombreArea(): string { return 'Dental'; }
}
