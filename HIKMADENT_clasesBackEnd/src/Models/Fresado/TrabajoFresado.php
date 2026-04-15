<?php

declare(strict_types=1);

namespace HikmaDent\Models\Fresado;

use HikmaDent\Enums\MaterialType;

/**
 * TrabajoFresado - Representa el trabajo de fresado CNC para una orden.
 * Diagrama: TrabajoFresado --> Maquina (asignado a) | TrabajoFresado --> Inventario (consume)
 */
class TrabajoFresado
{
    private bool      $cargaIniciada    = false;
    private bool      $sintetizadoFinal = false;
    private ?Maquina  $maquinaAsignada  = null;

    public function __construct(
        private int          $idTrabajo,
        private string       $archivoDiseno,
        private MaterialType $materialSeleccionado
    ) {}

    public function getIdTrabajo(): int              { return $this->idTrabajo; }
    public function getArchivoDiseno(): string       { return $this->archivoDiseno; }
    public function getMaterialSeleccionado(): MaterialType { return $this->materialSeleccionado; }
    public function getMaquinaAsignada(): ?Maquina   { return $this->maquinaAsignada; }
    public function isCargaIniciada(): bool          { return $this->cargaIniciada; }

    public function asignarMaquina(Maquina $maquina): void
    {
        $this->maquinaAsignada = $maquina;
        echo "[TrabajoFresado #{$this->idTrabajo}] Máquina '{$maquina->getNombre()}' asignada.\n";
    }

    /**
     * Inicia la carga del archivo de diseño en la fresadora.
     * Precondición: Máquina disponible + Stock verificado.
     */
    public function iniciarCarga(): void
    {
        if ($this->maquinaAsignada === null) {
            throw new \RuntimeException("No se puede iniciar la carga sin una máquina asignada.");
        }
        $this->cargaIniciada = true;
        $this->maquinaAsignada->ocupar();
        echo "[TrabajoFresado #{$this->idTrabajo}] Carga del archivo '{$this->archivoDiseno}' iniciada.\n";
    }

    /**
     * Finaliza el proceso de sintetizado (solo para materiales que lo requieren).
     */
    public function finalizarSintetizado(): void
    {
        if ($this->materialSeleccionado->requiereSintetizado()) {
            echo "[TrabajoFresado #{$this->idTrabajo}] Proceso de sintetizado completado para '{$this->materialSeleccionado->value}'.\n";
        }
        $this->sintetizadoFinal = true;
        $this->maquinaAsignada?->liberar();
        echo "[TrabajoFresado #{$this->idTrabajo}] ✅ Fresado finalizado. Pieza lista para siguiente área.\n";
    }
}
