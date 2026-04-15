<?php

declare(strict_types=1);

namespace HikmaDent\Models\Yeso;

use HikmaDent\Enums\MaterialType;

/**
 * MaterialYeso - Representa el material utilizado en el área de Yeso.
 * Contiene propiedades sobre cantidad y si requiere sintetizado posterior.
 * Diagrama: AreaYeso "1" -- "1" MaterialYeso : utiliza
 */
class MaterialYeso
{
    public function __construct(
        private MaterialType $nombre,           // Zirconio | PMMA | Resina
        private float        $cantidadGramosMl,
        private bool         $requiereSintetizado
    ) {}

    public function getNombre(): MaterialType      { return $this->nombre; }
    public function getCantidadGramosMl(): float   { return $this->cantidadGramosMl; }
    public function isRequiereSintetizado(): bool  { return $this->requiereSintetizado; }

    public function __toString(): string
    {
        $sint = $this->requiereSintetizado ? 'Sí' : 'No';
        return "Material: {$this->nombre->value} | {$this->cantidadGramosMl}g/ml | Sintetizado: {$sint}";
    }
}
