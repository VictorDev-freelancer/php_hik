<?php

declare(strict_types=1);

namespace HikmaDent\Observers;

/**
 * NotificacionTecnico - Observador de Notificaciones entre Áreas.
 *
 * Implementa el patrón Observer para notificar al técnico de la siguiente
 * estación cuando una pieza finaliza en la estación actual.
 *
 * Caso de uso: Al finalizar Fresado, el técnico de Inyectado recibe una
 * notificación automática sin que Fresado e Inyectado estén directamente acoplados.
 *
 * En producción: Enviaría un email, SMS, push notification o WebSocket event.
 */
class NotificacionTecnico
{
    /** @var array<int, array{evento, mensaje, destinatario, timestamp}> */
    private array $notificacionesEnviadas = [];

    /**
     * Mapa de eventos a mensajes y destinatarios.
     * Configurable sin modificar el código (Open/Closed Principle).
     */
    private array $mapaEventos = [
        'fresado.finalizado'   => ['area' => 'Inyectado',  'emoji' => '⚙️'],
        'inyectado.finalizado' => ['area' => 'Ceramica',   'emoji' => '🔥'],
        'ceramica.finalizado'  => ['area' => 'Impresion',  'emoji' => '🖨️'],
        'impresion.finalizado' => ['area' => 'Calidad',    'emoji' => '✅'],
        'yeso.finalizado'      => ['area' => 'Inyectado',  'emoji' => '🧪'],
        'area.transferencia'   => ['area' => 'Siguiente',  'emoji' => '📨'],
    ];

    /**
     * Maneja el evento y envía la notificación al técnico correspondiente.
     *
     * @param array $datos Payload del evento del WorkflowManager.
     */
    public function manejarEvento(array $datos): void
    {
        $tipoEvento  = $datos['evento']  ?? 'desconocido';
        $ordenId     = $datos['ordenId'] ?? 0;
        $areaDestino = $datos['areaNueva'] ?? ($this->mapaEventos[$tipoEvento]['area'] ?? 'Desconocida');
        $emoji       = $this->mapaEventos[$tipoEvento]['emoji'] ?? '🔔';

        $mensaje = sprintf(
            "%s [HIKMADENT] Orden #%d lista para el área de %s. Favor proceder.",
            $emoji,
            $ordenId,
            $areaDestino
        );

        $notificacion = [
            'evento'      => $tipoEvento,
            'ordenId'     => $ordenId,
            'destinatario'=> "Técnico de {$areaDestino}",
            'mensaje'     => $mensaje,
            'timestamp'   => (new \DateTime())->format('Y-m-d H:i:s'),
            'canal'       => 'sistema',  // En producción: 'email' | 'sms' | 'push'
        ];

        $this->notificacionesEnviadas[] = $notificacion;

        echo "[NotificacionTecnico] 📨 → {$notificacion['destinatario']}: {$mensaje}\n";
    }

    public function getNotificacionesEnviadas(): array
    {
        return $this->notificacionesEnviadas;
    }
}
