<?php

declare(strict_types=1);

namespace HikmaDent\Models\Dental;

/**
 * Ajuste - Control de calidad de forma y textura de la pieza dental.
 * Si falla la validación, el flujo genera un RegistroIncidencia.
 * Diagrama: Maquillaje --> Ajuste --> RegistroIncidencia (si falla calidad)
 */
class Ajuste
{
    public function __construct(
        private bool $formaAdaptacion = false,
        private bool $texturaAltura   = false
    ) {}

    public function isFormaAdaptacionOk(): bool { return $this->formaAdaptacion; }
    public function isTexturaAlturaOk(): bool   { return $this->texturaAltura; }

    public function setFormaAdaptacion(bool $valor): void { $this->formaAdaptacion = $valor; }
    public function setTexturaAltura(bool $valor): void   { $this->texturaAltura   = $valor; }

    /**
     * Valida que la pieza cumpla los criterios de forma y textura.
     *
     * @return bool true si ambos criterios son aprobados (pasa a siguiente área),
     *              false si falla (genera RegistroIncidencia).
     */
    public function validarCalidad(): bool
    {
        $aprobado = $this->formaAdaptacion && $this->texturaAltura;
        $estado   = $aprobado ? '✅ APROBADO' : '❌ RECHAZADO → Genera RegistroIncidencia';
        echo "[Ajuste] Validación de calidad: {$estado}\n";
        return $aprobado;
    }
}
