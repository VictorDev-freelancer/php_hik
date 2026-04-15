<?php

declare(strict_types=1);

namespace HikmaDent\Models\Inyectado;

use HikmaDent\Enums\MaterialType;

/**
 * ProcesoInyectado - Orquesta las fases del proceso de inyectado.
 * Contiene: FaseTermica (curva de horno) y AjusteAdaptacion (control final).
 * Diagrama: ProcesoInyectado *-- FaseTermica | ProcesoInyectado *-- AjusteAdaptacion
 */
class ProcesoInyectado
{
    private array $fasesRegistradas = [];

    public function __construct(
        private int          $ordenId,
        private MaterialType $material,
        private FaseTermica  $faseTermica,
        private AjusteAdaptacion $ajusteAdaptacion
    ) {}

    public function getOrdenId(): int                      { return $this->ordenId; }
    public function getMaterial(): MaterialType            { return $this->material; }
    public function getFaseTermica(): FaseTermica          { return $this->faseTermica; }
    public function getAjusteAdaptacion(): AjusteAdaptacion { return $this->ajusteAdaptacion; }
    public function getFasesRegistradas(): array           { return $this->fasesRegistradas; }

    /**
     * Registra el inicio de una fase del proceso (para trazabilidad BI).
     */
    public function registrarFase(string $nombreFase): void
    {
        $this->fasesRegistradas[] = [
            'fase'      => $nombreFase,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        echo "[ProcesoInyectado #{$this->ordenId}] Fase registrada: '{$nombreFase}'\n";
    }

    /**
     * Verifica la calidad del proceso completo (fases térmica + ajuste).
     *
     * @return bool true si el proceso es aprobado para avanzar.
     */
    public function verificarCalidad(): bool
    {
        $curvaOk  = $this->faseTermica->validarCurvaCalor();
        $ajusteOk = $this->ajusteAdaptacion->finalizarOrden();
        $resultado = $curvaOk && $ajusteOk;
        echo "[ProcesoInyectado #{$this->ordenId}] Calidad total: " . ($resultado ? '✅ APROBADO' : '❌ RECHAZADO') . "\n";
        return $resultado;
    }

    /** Devuelve todos los datos técnicos para almacenamiento JSON en OrdenTrabajo. */
    public function comoDetalleTecnico(): array
    {
        return [
            'material'   => $this->material->value,
            'fases'      => $this->fasesRegistradas,
            'faseTermica'=> $this->faseTermica->comoArray(),
        ];
    }
}
