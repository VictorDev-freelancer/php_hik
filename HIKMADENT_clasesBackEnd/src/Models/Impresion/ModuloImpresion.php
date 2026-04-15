<?php

declare(strict_types=1);

namespace HikmaDent\Models\Impresion;

use HikmaDent\Enums\PriorityLevel;

/**
 * ModuloImpresion - Punto de entrada del área de Impresión.
 * Valida la impresión recibida, gestiona prioridades y transfiere a la
 * siguiente estación al finalizar.
 * Diagrama: ModuloImpresion --> GestionPrioridad | --> TransferenciaArea
 */
class ModuloImpresion
{
    private array $excepciones = [];

    public function __construct(
        private int              $idRecepcion,
        private bool             $impresionValida,
        private string           $tipoYeso,
        private \DateTime        $inicioFraguado,
        private GestionPrioridad $gestionPrioridad,
        private TransferenciaArea $transferenciaArea
    ) {}

    public function getIdRecepcion(): int           { return $this->idRecepcion; }
    public function isImpresionValida(): bool       { return $this->impresionValida; }
    public function getTipoYeso(): string           { return $this->tipoYeso; }
    public function getInicioFraguado(): \DateTime  { return $this->inicioFraguado; }
    public function getExcepciones(): array         { return $this->excepciones; }

    /**
     * Registra una excepción o incidencia durante el proceso de impresión.
     */
    public function registrarExcepcion(string $descripcion): void
    {
        $this->excepciones[] = [
            'descripcion' => $descripcion,
            'timestamp'   => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        echo "[ModuloImpresion #{$this->idRecepcion}] ⚠️  Excepción registrada: '{$descripcion}'\n";
    }

    /**
     * Consulta la cola de prioridades y retorna la posición de esta recepción.
     */
    public function consultarPrioridadEnCola(): array
    {
        $this->gestionPrioridad->agregarOrdenACola($this->idRecepcion);
        return $this->gestionPrioridad->calcularOrdenCola();
    }

    /**
     * Finaliza el proceso de impresión y notifica a la siguiente estación.
     * Solo se puede finalizar si la impresión es válida.
     *
     * @return array Datos del evento de transferencia.
     * @throws \RuntimeException Si la impresión no es válida.
     */
    public function finalizarYTransferir(): array
    {
        if (!$this->impresionValida) {
            throw new \RuntimeException(
                "ModuloImpresion #{$this->idRecepcion}: No se puede transferir — impresión inválida."
            );
        }

        $tiempoFraguado = (new \DateTime())->diff($this->inicioFraguado);
        $minutos        = ($tiempoFraguado->h * 60) + $tiempoFraguado->i;
        echo "[ModuloImpresion #{$this->idRecepcion}] ✅ Fraguado completado en {$minutos} min. Tipo de yeso: {$this->tipoYeso}\n";

        return $this->transferenciaArea->notificarSiguienteEstacion($this->idRecepcion);
    }
}
