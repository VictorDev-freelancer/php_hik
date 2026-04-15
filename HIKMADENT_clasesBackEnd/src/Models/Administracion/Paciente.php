<?php

declare(strict_types=1);

namespace HikmaDent\Models\Administracion;

/**
 * Paciente - Representa al paciente para el que se elabora la pieza dental.
 * Relación: Un Paciente puede tener muchas OrdenTrabajo.
 */
class Paciente
{
    public function __construct(
        private int    $idPaciente,
        private string $nombre,
        private int    $edad,
        private string $observaciones = ''
    ) {}

    public function getIdPaciente(): int       { return $this->idPaciente; }
    public function getNombre(): string        { return $this->nombre; }
    public function getEdad(): int             { return $this->edad; }
    public function getObservaciones(): string { return $this->observaciones; }

    public function __toString(): string
    {
        return "{$this->nombre} ({$this->edad} años)";
    }
}
