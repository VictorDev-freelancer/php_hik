<?php

declare(strict_types=1);

namespace HikmaDent\Models\Dental;

/**
 * Maquillaje - Proceso estético aplicado a la pieza dental.
 * Tipos: 'Corona' (recubrimiento total) | 'Estratificado' (capas de porcelana).
 * Diagrama: PiezaDental --> Maquillaje --> Ajuste
 */
class Maquillaje
{
    private bool $completado = false;

    public function __construct(
        private string $tipo,           // 'Corona' | 'Estratificado'
        private int    $tiempoEstimado  // En minutos
    ) {}

    public function getTipo(): string          { return $this->tipo; }
    public function getTiempoEstimado(): int   { return $this->tiempoEstimado; }
    public function estaCompletado(): bool     { return $this->completado; }

    /**
     * Marca el proceso de maquillaje como completado.
     * Precondición: El proceso cerámico de la pieza debe estar iniciado.
     */
    public function completarMaquillaje(): void
    {
        $this->completado = true;
        echo "[Maquillaje] Tipo '{$this->tipo}' completado en {$this->tiempoEstimado} min. → Pasa a Ajuste.\n";
    }
}
