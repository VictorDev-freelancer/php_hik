<?php

declare(strict_types=1);

namespace HikmaDent\Contracts;

/**
 * Interfaz IProcesable
 *
 * Contrato base que TODA área del laboratorio HIKMADENT debe implementar.
 * Siguiendo el principio Open/Closed (SOLID), agregar un área nueva (ej. Ortodoncia)
 * solo requiere implementar esta interfaz sin modificar el código existente.
 *
 * Flujo de trabajo:
 * Digital -> (Fresado | Yeso) -> Inyectado -> Cerámica -> Calidad
 */
interface IProcesable
{
    /**
     * Inicia el proceso del área con la orden dada.
     * Debe validar las precondiciones del área antes de comenzar.
     *
     * @param int $ordenId   Identificador único de la OrdenTrabajo.
     * @param int $operadorId ID del técnico responsable de esta operación.
     * @return bool          true si el proceso inició correctamente.
     */
    public function iniciarProceso(int $ordenId, int $operadorId): bool;

    /**
     * Finaliza el proceso del área y prepara la orden para la siguiente estación.
     *
     * @param int $ordenId   Identificador único de la OrdenTrabajo.
     * @param int $operadorId ID del técnico responsable de esta operación.
     * @return bool          true si el proceso finalizó correctamente.
     */
    public function finalizarProceso(int $ordenId, int $operadorId): bool;

    /**
     * Valida la calidad del trabajo realizado en esta área.
     *
     * @param int $ordenId Identificador único de la OrdenTrabajo.
     * @return bool        true si la calidad es aprobada.
     */
    public function validarCalidad(int $ordenId): bool;

    /**
     * Retorna el nombre del área para BI y reportes de eficiencia.
     */
    public function getNombreArea(): string;
}
