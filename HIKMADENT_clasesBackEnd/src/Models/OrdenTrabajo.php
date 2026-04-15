<?php

declare(strict_types=1);

namespace HikmaDent\Models;

use HikmaDent\Enums\AreaType;
use HikmaDent\Enums\OrderStatus;

/**
 * OrdenTrabajo - Entidad principal del sistema HIKMADENT
 *
 * Implementa una Máquina de Estados que controla el avance de la orden
 * a través de las diferentes áreas del laboratorio dental.
 * El historial de transiciones se almacena como un array de registros
 * (equivalente a un campo JSON en base de datos).
 */
class OrdenTrabajo
{
    private int         $idOrden;
    private \DateTime   $fechaIngreso;
    private string      $tipoEntrada;    // 'Escaneo' | 'ModeloFisico'
    private OrderStatus $estado;
    private AreaType    $areaActual;

    /** @var array<int, array{area: string, estado: string, timestamp: string, operadorId: int}> */
    private array $historialTransiciones = [];

    /** Detalles técnicos por área (temperaturas, tiempos, etc.) almacenados como JSON */
    private array $detallesTecnicos = [];

    public function __construct(
        int    $idOrden,
        string $tipoEntrada = 'Escaneo'
    ) {
        $this->idOrden      = $idOrden;
        $this->tipoEntrada  = $tipoEntrada;
        $this->fechaIngreso = new \DateTime();
        $this->estado       = OrderStatus::PENDIENTE;
        $this->areaActual   = AreaType::DIGITAL;
    }

    // ─── Getters ────────────────────────────────────────────────────────────────

    public function getIdOrden(): int        { return $this->idOrden; }
    public function getFechaIngreso(): \DateTime { return $this->fechaIngreso; }
    public function getTipoEntrada(): string { return $this->tipoEntrada; }
    public function getEstado(): OrderStatus { return $this->estado; }
    public function getAreaActual(): AreaType { return $this->areaActual; }
    public function getHistorial(): array    { return $this->historialTransiciones; }
    public function getDetallesTecnicos(): array { return $this->detallesTecnicos; }

    // ─── Setters controlados ────────────────────────────────────────────────────

    /**
     * Cambia el área actual y registra la transición en el historial.
     * Solo el WorkflowManager debe invocar este método.
     */
    public function cambiarArea(AreaType $nuevaArea, int $operadorId): void
    {
        $this->historialTransiciones[] = [
            'area'        => $this->areaActual->value,
            'nuevaArea'   => $nuevaArea->value,
            'estado'      => $this->estado->value,
            'timestamp'   => (new \DateTime())->format('Y-m-d H:i:s'),
            'operadorId'  => $operadorId,
        ];
        $this->areaActual = $nuevaArea;
    }

    public function actualizarEstado(OrderStatus $nuevoEstado): void
    {
        $this->estado = $nuevoEstado;
    }

    /**
     * Agrega detalles técnicos para un área específica.
     * Ejemplo: temperatura de curva de calor en Inyectado.
     */
    public function agregarDetalleTecnico(string $area, array $detalle): void
    {
        $this->detallesTecnicos[$area] = array_merge(
            $this->detallesTecnicos[$area] ?? [],
            $detalle
        );
    }

    /** Serializa los detalles técnicos como JSON (para base de datos). */
    public function detallesTecnicosComoJson(): string
    {
        return json_encode($this->detallesTecnicos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /** Muestra un resumen de la orden para el dashboard BI. */
    public function resumen(): string
    {
        return sprintf(
            "[OrdenTrabajo #%d] Área: %s | Estado: %s | Entrada: %s | Ingreso: %s",
            $this->idOrden,
            $this->areaActual->value,
            $this->estado->label(),
            $this->tipoEntrada,
            $this->fechaIngreso->format('d/m/Y H:i')
        );
    }
}
