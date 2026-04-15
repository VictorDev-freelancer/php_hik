<?php

declare(strict_types=1);

namespace HikmaDent\Models\Inyectado;

/**
 * AjusteAdaptacion - Verifica el ajuste final de la pieza inyectada.
 * Valida el escaneo intraoral y el ajuste oclusal antes de finalizar.
 * Diagrama: ProcesoInyectado *-- AjusteAdaptacion
 */
class AjusteAdaptacion
{
    public function __construct(
        private bool $escaneoIntraoralOk = false,
        private bool $ajusteOclusalOk    = false
    ) {}

    public function setEscaneoIntraoralOk(bool $valor): void { $this->escaneoIntraoralOk = $valor; }
    public function setAjusteOclusalOk(bool $valor): void    { $this->ajusteOclusalOk    = $valor; }
    public function isEscaneoIntraoralOk(): bool             { return $this->escaneoIntraoralOk; }
    public function isAjusteOclusalOk(): bool                { return $this->ajusteOclusalOk; }

    /**
     * Valida el escaneo intraoral de la pieza.
     * Método con nombre descriptivo según Clean Code.
     */
    public function ValidarEscaneoIntraoral(): bool
    {
        if ($this->escaneoIntraoralOk) {
            echo "[AjusteAdaptacion] ✅ Escaneo intraoral validado correctamente.\n";
        } else {
            echo "[AjusteAdaptacion] ❌ Escaneo intraoral con discrepancias.\n";
        }
        return $this->escaneoIntraoralOk;
    }

    /**
     * Finaliza la orden de inyectado si todos los ajustes están aprobados.
     *
     * @return bool true si la orden puede avanzar a Cerámica.
     */
    public function finalizarOrden(): bool
    {
        $aprobado = $this->escaneoIntraoralOk && $this->ajusteOclusalOk;
        $estado   = $aprobado ? '✅ APROBADO → pasa a Cerámica' : '❌ RECHAZADO → revisión requerida';
        echo "[AjusteAdaptacion] Finalización de orden: {$estado}\n";
        return $aprobado;
    }
}
