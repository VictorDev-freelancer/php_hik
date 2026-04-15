<?php

declare(strict_types=1);

namespace HikmaDent\Services;

use HikmaDent\Contracts\IObservable;
use HikmaDent\Enums\AreaType;
use HikmaDent\Enums\OrderStatus;
use HikmaDent\Models\OrdenTrabajo;
use HikmaDent\Models\Dental\RegistroIncidencia;

/**
 * WorkflowManager - Servicio Central de Flujo de Trabajo.
 *
 * Es el ÚNICO responsable de mover una OrdenTrabajo de una área a otra.
 * Implementa el patrón Mediador + Observer para:
 *   1. Validar transiciones de estado (Máquina de Estados).
 *   2. Ejecutar reglas de negocio antes de cada transición.
 *   3. Disparar eventos para AuditLogger y NotificacionTecnico.
 *
 * Principio SRP (SOLID): Esta clase es la única que "mueve" órdenes.
 * Bajo acoplamiento: Las áreas no se conocen entre sí, solo conocen al WorkflowManager.
 */
class WorkflowManager implements IObservable
{
    /** @var array<string, callable[]> Mapa de eventos → observadores suscritos */
    private array $observadores = [];

    /** @var OrdenTrabajo[] Registro en memoria de órdenes activas */
    private array $ordenesActivas = [];

    // ─── IObservable ────────────────────────────────────────────────────────────

    public function suscribir(string $evento, callable $observador): void
    {
        $this->observadores[$evento][] = $observador;
        echo "[WorkflowManager] Observador suscrito al evento '{$evento}'.\n";
    }

    public function disparar(string $evento, array $datos): void
    {
        $datos['evento'] = $evento;
        foreach ($this->observadores[$evento] ?? [] as $observador) {
            ($observador)($datos);
        }
        // También dispara a suscriptores del evento comodín '*'
        foreach ($this->observadores['*'] ?? [] as $observador) {
            ($observador)($datos);
        }
    }

    // ─── Gestión de Órdenes ─────────────────────────────────────────────────────

    public function registrarOrden(OrdenTrabajo $orden): void
    {
        $this->ordenesActivas[$orden->getIdOrden()] = $orden;
        echo "[WorkflowManager] Orden #{$orden->getIdOrden()} registrada en el sistema.\n";
    }

    public function getOrden(int $ordenId): ?OrdenTrabajo
    {
        return $this->ordenesActivas[$ordenId] ?? null;
    }

    // ─── Transiciones de Área ───────────────────────────────────────────────────

    /**
     * Mueve una orden al área siguiente, validando la transición.
     * Es el método central del sistema — toda transición pasa por aquí.
     *
     * @throws \RuntimeException Si la transición no está permitida.
     */
    public function avanzarAreaSiguiente(
        int     $ordenId,
        AreaType $areaDestino,
        int     $operadorId
    ): bool {
        $orden = $this->getOrdenOFallar($ordenId);

        $areaActual         = $orden->getAreaActual();
        $transicionesValidas = $areaActual->transicionesPermitidas();

        if (!in_array($areaDestino, $transicionesValidas)) {
            $validas = implode(', ', array_map(fn($a) => $a->value, $transicionesValidas));
            throw new \RuntimeException(
                "Transición inválida: '{$areaActual->value}' → '{$areaDestino->value}'. Válidas: [{$validas}]"
            );
        }

        $this->ejecutarTransicion($orden, $areaDestino, $operadorId);
        return true;
    }

    /**
     * Permite el retroceso de una orden desde Calidad a un área específica.
     * Regla de negocio: Solo el área de Calidad puede retroceder una orden.
     *
     * @throws \RuntimeException Si el retroceso no está permitido.
     */
    public function retrocederAArea(
        int     $ordenId,
        AreaType $areaRetroceso,
        int     $operadorId,
        string  $motivoIncidencia
    ): bool {
        $orden      = $this->getOrdenOFallar($ordenId);
        $areaActual = $orden->getAreaActual();

        if (!in_array($areaRetroceso, $areaActual->retrocesosPermitidos())) {
            throw new \RuntimeException(
                "Retroceso no permitido desde '{$areaActual->value}' → '{$areaRetroceso->value}'."
            );
        }

        // Crear el registro de incidencia
        $incidencia = new RegistroIncidencia($ordenId, $motivoIncidencia, $areaRetroceso, $operadorId);
        $incidencia->retornarAAdaptacion();

        $orden->actualizarEstado(OrderStatus::RETROCEDIDO);
        $this->ejecutarTransicion($orden, $areaRetroceso, $operadorId, 'retroceso');
        return true;
    }

    // ─── Lógica Interna ─────────────────────────────────────────────────────────

    private function ejecutarTransicion(
        OrdenTrabajo $orden,
        AreaType     $areaDestino,
        int          $operadorId,
        string       $tipoTransicion = 'avance'
    ): void {
        $areaAnterior = $orden->getAreaActual()->value;
        $orden->cambiarArea($areaDestino, $operadorId);
        $orden->actualizarEstado(OrderStatus::EN_PROCESO);

        $datosPorEvento = [
            'ordenId'      => $orden->getIdOrden(),
            'operadorId'   => $operadorId,
            'areaAnterior' => $areaAnterior,
            'areaNueva'    => $areaDestino->value,
            'tipoTransicion'=> $tipoTransicion,
            'timestamp'    => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        // Disparar eventos al AuditLogger y NotificacionTecnico
        $this->disparar('orden.area.cambiada', $datosPorEvento);
        $this->disparar(strtolower($areaAnterior) . '.finalizado', $datosPorEvento);

        echo sprintf(
            "[WorkflowManager] ✅ Orden #%d: '%s' → '%s' (Operador #%d) [%s]\n",
            $orden->getIdOrden(), $areaAnterior, $areaDestino->value, $operadorId, $tipoTransicion
        );
    }

    private function getOrdenOFallar(int $ordenId): OrdenTrabajo
    {
        $orden = $this->getOrden($ordenId);
        if ($orden === null) {
            throw new \RuntimeException("Orden #{$ordenId} no encontrada en el WorkflowManager.");
        }
        return $orden;
    }
}
