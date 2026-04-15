<?php

declare(strict_types=1);

namespace HikmaDent\Models\Yeso;

/**
 * AreaYeso - Proceso de preparación de modelos en yeso.
 * Registra el tiempo de secado y el material base utilizado.
 * Diagrama: AreaYeso "1" -- "1" MaterialYeso : utiliza
 */
class AreaYeso
{
    private ?string    $horaSalida = null;
    private bool       $salidaRegistrada = false;

    public function __construct(
        private int           $idProceso,
        private string        $materialBase,
        private \DateTime     $horaSecado,
        private MaterialYeso  $material
    ) {}

    public function getIdProceso(): int        { return $this->idProceso; }
    public function getMaterialBase(): string  { return $this->materialBase; }
    public function getHoraSecado(): \DateTime { return $this->horaSecado; }
    public function getMaterial(): MaterialYeso { return $this->material; }
    public function isSalidaRegistrada(): bool { return $this->salidaRegistrada; }

    /**
     * Registra la salida del proceso de yeso hacia la siguiente área.
     * Calcula el tiempo real de secado para trazabilidad BI.
     */
    public function registrarSalida(): void
    {
        $ahora             = new \DateTime();
        $this->horaSalida  = $ahora->format('Y-m-d H:i:s');
        $this->salidaRegistrada = true;

        $diff    = $ahora->diff($this->horaSecado);
        $minutos = ($diff->h * 60) + $diff->i;
        echo "[AreaYeso #{$this->idProceso}] Salida registrada a las {$this->horaSalida}. Tiempo real de secado: {$minutos} min.\n";
        echo "[AreaYeso #{$this->idProceso}] Material: {$this->material}\n";
    }

    /** Devuelve el detalle técnico para almacenamiento en OrdenTrabajo JSON. */
    public function comoDetalleTecnico(): array
    {
        return [
            'material_base'   => $this->materialBase,
            'hora_secado'     => $this->horaSecado->format('Y-m-d H:i:s'),
            'hora_salida'     => $this->horaSalida,
            'material_detalle'=> (string) $this->material,
        ];
    }
}
