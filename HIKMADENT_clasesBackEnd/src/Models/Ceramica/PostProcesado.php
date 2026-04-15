<?php

declare(strict_types=1);

namespace HikmaDent\Models\Ceramica;

/**
 * PostProcesado - Detalles técnicos del post-procesado cerámico.
 * Almacena tiempos de lavado, fotocurado y temperatura de horno.
 * Diagrama: OrdenImpresion "1" -- "1" PostProcesado
 */
class PostProcesado
{
    public function __construct(
        private int   $tiempoLavado,      // En minutos
        private int   $tiempoFotocurado,  // En minutos
        private float $temperatura        // En grados Celsius
    ) {}

    public function getTiempoLavado(): int     { return $this->tiempoLavado; }
    public function getTiempoFotocurado(): int { return $this->tiempoFotocurado; }
    public function getTemperatura(): float    { return $this->temperatura; }

    /**
     * Valida que los parámetros de post-procesado estén dentro del rango técnico.
     * Valores estándar: Lavado 5-20min, Fotocurado 10-30min, Temp 20-35°C.
     *
     * @return bool true si el proceso es válido para continuar.
     */
    public function validarProceso(): bool
    {
        $lavadoOk     = $this->tiempoLavado >= 5     && $this->tiempoLavado <= 20;
        $fotocuradoOk = $this->tiempoFotocurado >= 10 && $this->tiempoFotocurado <= 30;
        $tempOk       = $this->temperatura >= 20     && $this->temperatura <= 35;

        $valido = $lavadoOk && $fotocuradoOk && $tempOk;
        echo "[PostProcesado] Validación: " . ($valido ? '✅ APROBADO' : '❌ PARÁMETROS INCORRECTOS') . "\n";
        echo "  → Lavado: {$this->tiempoLavado}min | Fotocurado: {$this->tiempoFotocurado}min | Temp: {$this->temperatura}°C\n";
        return $valido;
    }

    public function comoArray(): array
    {
        return [
            'tiempo_lavado'     => $this->tiempoLavado,
            'tiempo_fotocurado' => $this->tiempoFotocurado,
            'temperatura'       => $this->temperatura,
        ];
    }
}
