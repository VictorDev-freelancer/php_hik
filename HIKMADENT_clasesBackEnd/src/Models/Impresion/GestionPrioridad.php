<?php

declare(strict_types=1);

namespace HikmaDent\Models\Impresion;

use HikmaDent\Enums\PriorityLevel;

/**
 * GestionPrioridad - Calcula el orden de procesamiento en la cola de trabajo.
 * Determina qué órdenes se atienden primero según su nivel de urgencia.
 * Diagrama: ModuloImpresion --> GestionPrioridad : consulta
 */
class GestionPrioridad
{
    /** @var array<int, array{ordenId: int, prioridad: PriorityLevel, timestamp: string}> */
    private array $cola = [];

    public function __construct(
        private PriorityLevel $nivel
    ) {}

    public function getNivel(): PriorityLevel { return $this->nivel; }

    /**
     * Agrega una orden a la cola de trabajo con su prioridad.
     */
    public function agregarOrdenACola(int $ordenId): void
    {
        $this->cola[] = [
            'ordenId'   => $ordenId,
            'prioridad' => $this->nivel,
            'peso'      => $this->nivel->peso(),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        echo "[GestionPrioridad] Orden #{$ordenId} agregada a la cola con prioridad '{$this->nivel->value}'.\n";
    }

    /**
     * Calcula el orden de la cola, ordenando por peso de prioridad.
     *
     * @return array Cola ordenada de menor a mayor peso (Urgente primero).
     */
    public function calcularOrdenCola(): array
    {
        usort($this->cola, fn($a, $b) => $a['peso'] <=> $b['peso']);
        echo "[GestionPrioridad] Cola calculada: " . count($this->cola) . " órdenes pendientes.\n";
        return $this->cola;
    }

    public function getCola(): array { return $this->cola; }
}
