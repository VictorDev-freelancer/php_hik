<?php

declare(strict_types=1);

namespace HikmaDent\Models\Administracion;

/**
 * ClienteDoctor - Representa al doctor o clínica que solicita el trabajo.
 * Relación: Un ClienteDoctor puede tener muchas OrdenTrabajo.
 */
class ClienteDoctor
{
    public function __construct(
        private int    $idDoctor,
        private string $nombre,
        private string $clinica,
        private string $telefono = '',
        private string $email    = ''
    ) {}

    public function getIdDoctor(): int    { return $this->idDoctor; }
    public function getNombre(): string   { return $this->nombre; }
    public function getClinica(): string  { return $this->clinica; }
    public function getTelefono(): string { return $this->telefono; }
    public function getEmail(): string    { return $this->email; }

    public function __toString(): string
    {
        return "Dr. {$this->nombre} ({$this->clinica})";
    }
}
