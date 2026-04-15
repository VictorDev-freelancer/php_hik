<?php

declare(strict_types=1);

namespace HikmaDent\Models\Fresado;

use HikmaDent\Enums\MaterialType;

/**
 * Inventario - Controla el stock de materiales para el área de Fresado.
 *
 * Regla de negocio: Antes de iniciar un trabajo en Fresado,
 * se debe verificar que hay stock suficiente del material requerido.
 */
class Inventario
{
    /** @var array<string, int> Material => Cantidad disponible en unidades */
    private array $stock = [];

    public function __construct(array $stockInicial = [])
    {
        foreach ($stockInicial as $material => $cantidad) {
            $this->stock[$material] = $cantidad;
        }
    }

    public function getCantidadDisponible(MaterialType $material): int
    {
        return $this->stock[$material->value] ?? 0;
    }

    /**
     * Verifica si hay stock suficiente para el material solicitado.
     */
    public function verificarStock(MaterialType $material, int $cantidad): bool
    {
        $disponible = $this->getCantidadDisponible($material);
        if ($disponible >= $cantidad) {
            echo "[Inventario] ✅ Stock suficiente de '{$material->value}': {$disponible} unidades disponibles (se requieren {$cantidad}).\n";
            return true;
        }
        echo "[Inventario] ❌ Stock insuficiente de '{$material->value}': solo {$disponible} disponibles (se requieren {$cantidad}).\n";
        return false;
    }

    /**
     * Descuenta la cantidad usada del stock.
     * Debe llamarse solo después de verificarStock() con resultado positivo.
     */
    public function descontarStock(MaterialType $material, int $cantidad): void
    {
        if (!$this->verificarStock($material, $cantidad)) {
            throw new \RuntimeException("No se puede descontar: stock insuficiente de '{$material->value}'.");
        }
        $this->stock[$material->value] -= $cantidad;
        echo "[Inventario] Stock de '{$material->value}' actualizado: {$this->stock[$material->value]} restantes.\n";
    }
}
