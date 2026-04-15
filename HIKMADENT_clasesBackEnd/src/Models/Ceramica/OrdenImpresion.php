<?php

declare(strict_types=1);

namespace HikmaDent\Models\Ceramica;

/**
 * EstadoCeramica - Enum interno para el estado del proceso cerámico.
 */
enum EstadoCeramica: string
{
    case LAVADO     = 'Lavado';
    case FOTOCURADO = 'Fotocurado';
    case TERMINADO  = 'Terminado';
    case CON_ERROR  = 'ConError';
}

/**
 * OrdenImpresion - Gestiona la orden dentro del área cerámica/impresión.
 * Implementa la máquina de estados interna del área (Lavado→Fotocurado→Terminado).
 * Diagrama: OrdenImpresion "1"--"1" PostProcesado | "*"--"1" ControlMantenimiento
 */
class OrdenImpresion
{
    private EstadoCeramica $estado;
    private array          $erroresRegistrados = [];

    public function __construct(
        private int                 $idOrden,
        private \DateTime           $fechaRecepcion,
        private PostProcesado       $postProcesado,
        private ControlMantenimiento $controlMantenimiento
    ) {
        $this->estado = EstadoCeramica::LAVADO;
    }

    public function getIdOrden(): int                     { return $this->idOrden; }
    public function getFechaRecepcion(): \DateTime        { return $this->fechaRecepcion; }
    public function getEstado(): EstadoCeramica           { return $this->estado; }
    public function getPostProcesado(): PostProcesado     { return $this->postProcesado; }
    public function getErrores(): array                   { return $this->erroresRegistrados; }

    /**
     * Registra un error técnico en la orden y cambia su estado.
     * El WorkflowManager procesará este error para generar un RegistroIncidencia.
     */
    public function registrarError(string $tipoError): void
    {
        $this->erroresRegistrados[] = [
            'tipo'      => $tipoError,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        $this->estado = EstadoCeramica::CON_ERROR;
        echo "[OrdenImpresion #{$this->idOrden}] ❌ Error registrado: '{$tipoError}'. Estado → ConError.\n";
    }

    /**
     * Avanza al siguiente estado en el flujo cerámico.
     * Lavado -> Fotocurado -> Terminado
     */
    public function actualizarEstado(): void
    {
        $this->estado = match($this->estado) {
            EstadoCeramica::LAVADO     => EstadoCeramica::FOTOCURADO,
            EstadoCeramica::FOTOCURADO => EstadoCeramica::TERMINADO,
            EstadoCeramica::TERMINADO  => EstadoCeramica::TERMINADO,
            EstadoCeramica::CON_ERROR  => EstadoCeramica::LAVADO,   // Reinicia si hay error
        };
        echo "[OrdenImpresion #{$this->idOrden}] Estado actualizado → {$this->estado->value}\n";
        $this->controlMantenimiento->registrarImpresion();
        $this->controlMantenimiento->notificarMantenimiento();
    }

    public function estaTerminado(): bool
    {
        return $this->estado === EstadoCeramica::TERMINADO;
    }
}
