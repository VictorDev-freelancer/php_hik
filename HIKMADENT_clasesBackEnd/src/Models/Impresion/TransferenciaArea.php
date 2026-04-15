<?php

declare(strict_types=1);

namespace HikmaDent\Models\Impresion;

/**
 * TransferenciaArea - Gestiona la notificación y transferencia entre estaciones.
 * Cuando una pieza finaliza en un área, notifica a la siguiente estación.
 * Diagrama: ModuloImpresion --> TransferenciaArea : finaliza con
 */
class TransferenciaArea
{
    private \DateTime $horaTransferencia;
    private bool      $notificacionEnviada = false;

    public function __construct(
        private string $areaDestino
    ) {}

    public function getAreaDestino(): string               { return $this->areaDestino; }
    public function getHoraTransferencia(): ?\DateTime     { return $this->horaTransferencia ?? null; }
    public function isNotificacionEnviada(): bool          { return $this->notificacionEnviada; }

    /**
     * Notifica al técnico del área siguiente que la pieza está lista.
     * Implementa el disparo del evento que el Observer escucha.
     *
     * @param int $ordenId ID de la orden transferida.
     * @return array Datos del evento para el WorkflowManager.
     */
    public function notificarSiguienteEstacion(int $ordenId): array
    {
        $this->horaTransferencia   = new \DateTime();
        $this->notificacionEnviada = true;

        $evento = [
            'evento'           => 'area.transferencia',
            'ordenId'          => $ordenId,
            'areaDestino'      => $this->areaDestino,
            'horaTransferencia'=> $this->horaTransferencia->format('Y-m-d H:i:s'),
        ];

        echo "[TransferenciaArea] 📨 Notificación enviada a '{$this->areaDestino}' para Orden #{$ordenId} a las " . $this->horaTransferencia->format('H:i:s') . "\n";
        return $evento;
    }
}
