<?php

declare(strict_types=1);

namespace HikmaDent\Models\Dental;

/**
 * PiezaDental - Entidad principal del Área Dental.
 * Representa la pieza física que pasa por el proceso cerámico.
 * Diagrama: PiezaDental --> Maquillaje --> Ajuste --> RegistroIncidencia
 */
class PiezaDental
{
    private bool $procesoCeramicoIniciado = false;

    public function __construct(
        private int       $id,
        private string    $tipoDiseño,        // 'Corona' | 'Póntico' | 'Incrustación'
        private \DateTime $fechaIngreso
    ) {}

    public function getId(): int            { return $this->id; }
    public function getTipoDiseño(): string { return $this->tipoDiseño; }
    public function getFechaIngreso(): \DateTime { return $this->fechaIngreso; }

    /**
     * Inicia el proceso cerámico de la pieza dental.
     * Precondición: La pieza debe provenir del área de Inyectado.
     */
    public function iniciarProcesoCeramico(): void
    {
        $this->procesoCeramicoIniciado = true;
        echo "[PiezaDental #{$this->id}] Proceso cerámico iniciado para '{$this->tipoDiseño}'.\n";
    }

    public function esProcesoCeramicoIniciado(): bool
    {
        return $this->procesoCeramicoIniciado;
    }

    public function __toString(): string
    {
        return "PiezaDental #{$this->id} ({$this->tipoDiseño}) - Ingreso: " . $this->fechaIngreso->format('d/m/Y');
    }
}
