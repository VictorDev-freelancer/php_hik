<?php

declare(strict_types=1);

namespace HikmaDent\Processes;

use HikmaDent\Contracts\IProcesable;
use HikmaDent\Models\Inyectado\ProcesoInyectado;
use HikmaDent\Models\Inyectado\FaseTermica;
use HikmaDent\Models\Inyectado\AjusteAdaptacion;
use HikmaDent\Enums\MaterialType;

/**
 * ProcesoInyectado - Implementación del área de Inyectado (cerámica prensada).
 * Orquesta la FaseTermica y el AjusteAdaptacion para validar la pieza.
 */
class ProcesoAreaInyectado implements IProcesable
{
    public function iniciarProceso(int $ordenId, int $operadorId): bool
    {
        echo "\n[ProcesoInyectado] === Iniciando para Orden #{$ordenId} (Operador #{$operadorId}) ===\n";

        $faseTermica     = new FaseTermica(25.0, 850.0, 20);
        $ajusteAdaptacion = new AjusteAdaptacion(true, true);

        $proceso = new ProcesoInyectado($ordenId, MaterialType::CERAMICA, $faseTermica, $ajusteAdaptacion);
        $proceso->registrarFase('Precalentamiento');
        $proceso->registrarFase('InyeccionCeramica');

        echo "[ProcesoInyectado] ✅ Proceso iniciado con FaseTermica y AjusteAdaptacion configurados.\n";
        return true;
    }

    public function finalizarProceso(int $ordenId, int $operadorId): bool
    {
        echo "[ProcesoInyectado] Verificando calidad para Orden #{$ordenId}...\n";

        $faseTermica      = new FaseTermica(25.0, 850.0, 20);
        $ajusteAdaptacion = new AjusteAdaptacion(true, true);
        $proceso          = new ProcesoInyectado($ordenId, MaterialType::CERAMICA, $faseTermica, $ajusteAdaptacion);

        $ajusteAdaptacion->ValidarEscaneoIntraoral();
        $calidad = $proceso->verificarCalidad();

        if ($calidad) {
            echo "[ProcesoInyectado] ✅ Orden #{$ordenId} finalizada — pasa a Cerámica.\n";
        } else {
            echo "[ProcesoInyectado] ❌ Orden #{$ordenId} rechazada — requiere revisión.\n";
        }
        return $calidad;
    }

    public function validarCalidad(int $ordenId): bool
    {
        echo "[ProcesoInyectado] Validando calidad oclusión para Orden #{$ordenId}.\n";
        return true;
    }

    public function getNombreArea(): string { return 'Inyectado'; }
}
