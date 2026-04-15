<?php

declare(strict_types=1);

namespace HikmaDent\Enums;

/**
 * OrderStatus - Enum de Estados de la OrdenTrabajo
 *
 * Representa el ciclo de vida de una orden dentro del sistema HIKMADENT.
 * Garantiza que ningún proceso pueda asignar un estado no definido.
 */
enum OrderStatus: string
{
    case PENDIENTE    = 'Pendiente';
    case EN_PROCESO   = 'EnProceso';
    case EN_ESPERA    = 'EnEspera';
    case COMPLETADO   = 'Completado';
    case CON_ERROR    = 'ConError';
    case RETROCEDIDO  = 'Retrocedido';
    case FINALIZADO   = 'Finalizado';

    /** Retorna el label legible para el dashboard BI. */
    public function label(): string
    {
        return match($this) {
            OrderStatus::PENDIENTE   => '⏳ Pendiente',
            OrderStatus::EN_PROCESO  => '⚙️ En Proceso',
            OrderStatus::EN_ESPERA   => '🕐 En Espera',
            OrderStatus::COMPLETADO  => '✅ Completado',
            OrderStatus::CON_ERROR   => '❌ Con Error',
            OrderStatus::RETROCEDIDO => '↩️ Retrocedido',
            OrderStatus::FINALIZADO  => '🏁 Finalizado',
        };
    }
}
