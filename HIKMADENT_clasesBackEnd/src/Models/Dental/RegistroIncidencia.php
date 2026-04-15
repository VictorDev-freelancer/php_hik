<?php

declare(strict_types=1);

namespace HikmaDent\Models\Dental;

use HikmaDent\Enums\AreaType;

/**
 * RegistroIncidencia - Registra fallos de calidad y define el área de retroceso.
 *
 * Regla de negocio clave: Si el AreaCalidad detecta un error, este registro
 * almacena el problema y el área destino del retroceso para que el
 * WorkflowManager lo ejecute.
 *
 * Diagrama: Ajuste --> RegistroIncidencia (si falla calidad)
 */
class RegistroIncidencia
{
    private \DateTime $fechaRegistro;

    public function __construct(
        private int      $idOrden,
        private string   $problema,
        private AreaType $areaRetroceso,
        private int      $operadorId
    ) {
        $this->fechaRegistro = new \DateTime();
    }

    public function getIdOrden(): int           { return $this->idOrden; }
    public function getProblema(): string       { return $this->problema; }
    public function getAreaRetroceso(): AreaType { return $this->areaRetroceso; }
    public function getOperadorId(): int        { return $this->operadorId; }
    public function getFechaRegistro(): \DateTime { return $this->fechaRegistro; }

    /**
     * Solicita el retroceso de la orden al área indicada.
     * El WorkflowManager recibirá esta incidencia y ejecutará la transición.
     *
     * @return array Datos del evento de retroceso para el WorkflowManager.
     */
    public function retornarAAdaptacion(): array
    {
        $datos = [
            'idOrden'      => $this->idOrden,
            'problema'     => $this->problema,
            'areaRetroceso'=> $this->areaRetroceso->value,
            'operadorId'   => $this->operadorId,
            'timestamp'    => $this->fechaRegistro->format('Y-m-d H:i:s'),
        ];
        echo "[RegistroIncidencia] Orden #{$this->idOrden} → Retroceso a '{$this->areaRetroceso->value}'. Motivo: {$this->problema}\n";
        return $datos;
    }

    public function __toString(): string
    {
        return "[Incidencia] Orden #{$this->idOrden} | Problema: {$this->problema} | Retroceso a: {$this->areaRetroceso->value}";
    }
}
