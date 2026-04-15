<?php

declare(strict_types=1);

namespace HikmaDent\Models\Administracion;

/**
 * DisenoExocad - Representa el diseño digital vinculado a una OrdenTrabajo.
 * Almacena la metadata del archivo CAD generado con el software Exocad.
 */
class DisenoExocad
{
    public function __construct(
        private string  $nombreArchivo,
        private float   $tiempoDiseno,       // En horas
        private string  $versionSoftware,
        private string  $rutaArchivo = ''
    ) {}

    public function getNombreArchivo(): string  { return $this->nombreArchivo; }
    public function getTiempoDiseno(): float    { return $this->tiempoDiseno; }
    public function getVersionSoftware(): string { return $this->versionSoftware; }
    public function getRutaArchivo(): string    { return $this->rutaArchivo; }

    /**
     * Vincula el archivo .stl o .exocad a la ruta indicada.
     */
    public function vincularArchivo(string $ruta): void
    {
        $this->rutaArchivo = $ruta;
        echo "[DisenoExocad] Archivo '{$this->nombreArchivo}' vinculado en: {$ruta}\n";
    }

    public function __toString(): string
    {
        return "Diseño: {$this->nombreArchivo} | v{$this->versionSoftware} | Tiempo: {$this->tiempoDiseno}h";
    }
}
