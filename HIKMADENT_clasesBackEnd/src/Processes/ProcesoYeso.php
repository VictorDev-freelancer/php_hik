<?php

declare(strict_types=1);

namespace HikmaDent\Processes;

use HikmaDent\Contracts\IProcesable;
use HikmaDent\Models\Yeso\AreaYeso;
use HikmaDent\Models\Yeso\MaterialYeso;
use HikmaDent\Enums\MaterialType;

/**
 * ProcesoYeso - Implementación alternativa del área de Yeso.
 * Ruta alternativa al Fresado en el flujo: Digital → Yeso → Inyectado.
 */
class ProcesoYeso implements IProcesable
{
    public function iniciarProceso(int $ordenId, int $operadorId): bool
    {
        echo "\n[ProcesoYeso] === Iniciando para Orden #{$ordenId} (Operador #{$operadorId}) ===\n";

        $material = new MaterialYeso(MaterialType::YESO, 150.5, false);
        $area     = new AreaYeso($ordenId, 'Yeso Tipo III', new \DateTime(), $material);

        echo "[ProcesoYeso] Material: {$material}\n";
        echo "[ProcesoYeso] ✅ Proceso de yeso iniciado. Tiempo de secado en curso...\n";
        return true;
    }

    public function finalizarProceso(int $ordenId, int $operadorId): bool
    {
        echo "[ProcesoYeso] Registrando salida para Orden #{$ordenId}...\n";
        $material = new MaterialYeso(MaterialType::YESO, 150.5, false);
        $area     = new AreaYeso($ordenId, 'Yeso Tipo III', new \DateTime('-30 minutes'), $material);
        $area->registrarSalida();
        echo "[ProcesoYeso] ✅ Orden #{$ordenId} lista para Inyectado.\n";
        return true;
    }

    public function validarCalidad(int $ordenId): bool
    {
        echo "[ProcesoYeso] Validando consistencia del modelo de yeso para Orden #{$ordenId}.\n";
        // Verificación visual del modelo — aprobado por técnico
        echo "[ProcesoYeso] ✅ Modelo en buen estado.\n";
        return true;
    }

    public function getNombreArea(): string { return 'Yeso'; }
}
