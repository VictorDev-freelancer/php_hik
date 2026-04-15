<?php

declare(strict_types=1);

namespace HikmaDent\Models\Fresado;

use HikmaDent\Enums\MaterialType;

/**
 * Maquina - Representa una fresadora del área de Fresado.
 *
 * Regla de negocio: Antes de iniciar un TrabajoFresado, el WorkflowManager
 * debe verificar la disponibilidad de la máquina y que acepta el material.
 */
class Maquina
{
    private bool $enUso = false;

    public function __construct(
        private string $nombre,
        private bool   $aceptaZirconio,
        private bool   $aceptaPMMA
    ) {}

    public function getNombre(): string      { return $this->nombre; }
    public function isAceptaZirconio(): bool { return $this->aceptaZirconio; }
    public function isAceptaPMMA(): bool     { return $this->aceptaPMMA; }
    public function isEnUso(): bool          { return $this->enUso; }

    /**
     * Verifica si la máquina está disponible para un material específico.
     *
     * @return bool true solo si está libre Y acepta el material solicitado.
     */
    public function verificarDisponibilidad(MaterialType $material): bool
    {
        if ($this->enUso) {
            echo "[Maquina '{$this->nombre}'] ❌ No disponible — actualmente en uso.\n";
            return false;
        }

        $acepta = match($material) {
            MaterialType::ZIRCONIO, MaterialType::CERAMICA => $this->aceptaZirconio,
            MaterialType::PMMA, MaterialType::RESINA       => $this->aceptaPMMA,
            default => false,
        };

        if (!$acepta) {
            echo "[Maquina '{$this->nombre}'] ❌ No acepta el material '{$material->value}'.\n";
            return false;
        }

        echo "[Maquina '{$this->nombre}'] ✅ Disponible para material '{$material->value}'.\n";
        return true;
    }

    public function ocupar(): void   { $this->enUso = true;  echo "[Maquina '{$this->nombre}'] Ocupada.\n"; }
    public function liberar(): void  { $this->enUso = false; echo "[Maquina '{$this->nombre}'] Liberada.\n"; }
}
