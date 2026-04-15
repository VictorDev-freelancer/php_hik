<?php

declare(strict_types=1);

namespace HikmaDent\Contracts;

/**
 * Interfaz IObservable
 *
 * Contrato para el patrón Observer (Observador).
 * Permite que los observadores (AuditLogger, NotificacionTecnico) se suscriban
 * a los eventos del WorkflowManager sin que las áreas estén acopladas entre sí.
 */
interface IObservable
{
    /**
     * Suscribe un observador al sistema de eventos.
     *
     * @param string   $evento     Nombre del evento a escuchar (ej. 'fresado.finalizado').
     * @param callable $observador Función o método que se ejecuta cuando el evento ocurre.
     */
    public function suscribir(string $evento, callable $observador): void;

    /**
     * Dispara un evento y notifica a todos los observadores suscritos.
     *
     * @param string $evento Nombre del evento disparado.
     * @param array  $datos  Datos asociados al evento (ordenId, operadorId, timestamp, etc.).
     */
    public function disparar(string $evento, array $datos): void;
}
