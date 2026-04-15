<?php

declare(strict_types=1);

namespace HikmaDent\Enums;

/**
 * PriorityLevel - Enum de Niveles de Prioridad de la Orden
 *
 * Usado por el módulo GestionPrioridad para determinar
 * el orden de procesamiento en la cola de trabajo.
 */
enum PriorityLevel: string
{
    case URGENTE   = 'Urgente';
    case NORMAL    = 'Normal';
    case RETRASADO = 'Retrasado';

    /** Retorna el peso numérico para el cálculo de orden en cola. */
    public function peso(): int
    {
        return match($this) {
            PriorityLevel::URGENTE   => 1,
            PriorityLevel::NORMAL    => 2,
            PriorityLevel::RETRASADO => 3,
        };
    }
}
