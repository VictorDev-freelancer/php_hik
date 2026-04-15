<?php

declare(strict_types=1);

namespace HikmaDent\Models\Inyectado;

/**
 * FaseTermica - Representa la curva de calor en el proceso de inyectado.
 * Almacena los parámetros del horno para trazabilidad y BI.
 */
class FaseTermica
{
    public function __construct(
        private float $temperaturaInicial,  // En grados Celsius
        private float $temperaturaMaxima,
        private int   $tiempoMinutos
    ) {}

    public function getTemperaturaInicial(): float { return $this->temperaturaInicial; }
    public function getTemperaturaMaxima(): float  { return $this->temperaturaMaxima; }
    public function getTiempoMinutos(): int        { return $this->tiempoMinutos; }

    /**
     * Valida que la curva de calor sea correcta para el material inyectado.
     * Los valores óptimos para cerámica prensada: 700-900°C, 15-30 min.
     *
     * @return bool true si los parámetros son válidos para el proceso.
     */
    public function validarCurvaCalor(): bool
    {
        $temperaturaValida = $this->temperaturaMaxima >= 700 && $this->temperaturaMaxima <= 950;
        $tiempoValido = $this->tiempoMinutos >= 10 && $this->tiempoMinutos <= 45;
        $valida = $temperaturaValida && $tiempoValido;

        $estado = $valida ? '✅ Curva válida' : '❌ Curva fuera de parámetros';
        echo "[FaseTermica] {$estado}: {$this->temperaturaMaxima}°C / {$this->tiempoMinutos} min.\n";
        return $valida;
    }

    public function comoArray(): array
    {
        return [
            'temperatura_inicial' => $this->temperaturaInicial,
            'temperatura_maxima'  => $this->temperaturaMaxima,
            'tiempo_minutos'      => $this->tiempoMinutos,
        ];
    }
}
