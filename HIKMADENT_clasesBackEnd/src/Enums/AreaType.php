<?php

declare(strict_types=1);

namespace HikmaDent\Enums;

/**
 * AreaType - Enum de Áreas del Laboratorio HIKMADENT
 *
 * Define todas las estaciones posibles por las que una OrdenTrabajo puede pasar.
 * El uso de Enums garantiza tipado fuerte y evita valores inválidos en el flujo.
 *
 * Flujo principal:
 * DIGITAL -> FRESADO o YESO -> INYECTADO -> CERAMICA -> CALIDAD
 */
enum AreaType: string
{
    case DIGITAL   = 'Digital';
    case FRESADO   = 'Fresado';
    case YESO      = 'Yeso';
    case INYECTADO = 'Inyectado';
    case CERAMICA  = 'Ceramica';
    case IMPRESION = 'Impresion';
    case CALIDAD   = 'Calidad';

    /**
     * Retorna las áreas a las que se puede transicionar desde esta área.
     * Implementa la lógica de la Máquina de Estados.
     */
    public function transicionesPermitidas(): array
    {
        return match($this) {
            AreaType::DIGITAL   => [AreaType::FRESADO, AreaType::YESO],
            AreaType::FRESADO   => [AreaType::INYECTADO],
            AreaType::YESO      => [AreaType::INYECTADO],
            AreaType::INYECTADO => [AreaType::CERAMICA],
            AreaType::CERAMICA  => [AreaType::IMPRESION],
            AreaType::IMPRESION => [AreaType::CALIDAD],
            AreaType::CALIDAD   => [],
        };
    }

    /**
     * Permite el retroceso desde Calidad a un área específica (RegistroIncidencia).
     * Regla de negocio: solo el área de Calidad puede retroceder una orden.
     */
    public function retrocesosPermitidos(): array
    {
        return match($this) {
            AreaType::CALIDAD   => [AreaType::FRESADO, AreaType::INYECTADO, AreaType::CERAMICA],
            default             => [],
        };
    }
}
