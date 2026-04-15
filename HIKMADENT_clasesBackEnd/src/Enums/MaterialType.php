<?php

declare(strict_types=1);

namespace HikmaDent\Enums;

/**
 * MaterialType - Enum de Materiales del Laboratorio
 *
 * Define los materiales posibles para los procesos de Fresado e Inyectado.
 * La propiedad `requiereSintetizado` indica si el material pasa por
 * el proceso de sintetizado antes de ser enviado a la siguiente área.
 */
enum MaterialType: string
{
    case ZIRCONIO = 'Zirconio';
    case PMMA     = 'PMMA';
    case RESINA   = 'Resina';
    case CERAMICA = 'Ceramica';
    case YESO     = 'Yeso';

    /** Indica si este material requiere el proceso de sintetizado en Fresado. */
    public function requiereSintetizado(): bool
    {
        return match($this) {
            MaterialType::ZIRCONIO => true,
            MaterialType::PMMA     => false,
            MaterialType::RESINA   => false,
            MaterialType::CERAMICA => true,
            MaterialType::YESO     => false,
        };
    }

    /**
     * Verifica si el material es compatible con una máquina de fresado PMMA.
     */
    public function esFresablePorPMMA(): bool
    {
        return match($this) {
            MaterialType::PMMA, MaterialType::RESINA => true,
            default => false,
        };
    }
}
