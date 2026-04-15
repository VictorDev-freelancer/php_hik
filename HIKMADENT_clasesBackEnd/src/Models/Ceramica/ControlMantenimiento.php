<?php

declare(strict_types=1);

namespace HikmaDent\Models\Ceramica;

/**
 * ControlMantenimiento - Monitorea el estado de la impresora/horno cerámico.
 * Notifica cuando se requiere mantenimiento preventivo.
 * Diagrama: OrdenImpresion "*" -- "1" ControlMantenimiento : afecta a
 */
class ControlMantenimiento
{
    private static int $LIMITE_IMPRESIONES_SIN_LIMPIEZA = 50;

    public function __construct(
        private \DateTime $ultimaLimpieza,
        private int       $totalImpresiones
    ) {}

    public function getUltimaLimpieza(): \DateTime { return $this->ultimaLimpieza; }
    public function getTotalImpresiones(): int     { return $this->totalImpresiones; }

    public function registrarImpresion(): void
    {
        $this->totalImpresiones++;
    }

    /**
     * Envía una alerta si se superó el límite de usos sin limpieza.
     * El Observer NotificacionTecnico puede suscribirse a este evento.
     */
    public function notificarMantenimiento(): bool
    {
        if ($this->totalImpresiones >= self::$LIMITE_IMPRESIONES_SIN_LIMPIEZA) {
            echo "[ControlMantenimiento] ⚠️  ALERTA: Se requiere mantenimiento. Total impresiones: {$this->totalImpresiones}. Última limpieza: " . $this->ultimaLimpieza->format('d/m/Y') . "\n";
            return true;
        }
        echo "[ControlMantenimiento] ✅ Equipo en buen estado ({$this->totalImpresiones} usos desde última limpieza).\n";
        return false;
    }

    public function registrarLimpieza(): void
    {
        $this->ultimaLimpieza   = new \DateTime();
        $this->totalImpresiones = 0;
        echo "[ControlMantenimiento] ✅ Limpieza registrada en: " . $this->ultimaLimpieza->format('d/m/Y H:i') . "\n";
    }
}
