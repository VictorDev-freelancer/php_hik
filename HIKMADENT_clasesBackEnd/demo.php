<?php

declare(strict_types=1);

/**
 * ══════════════════════════════════════════════════════════════════
 *  DEMO.PHP — HIKMADENT Sistema de Laboratorio Dental
 *  Recorre el flujo completo de una OrdenTrabajo de inicio a fin.
 *
 *  Flujo demostrado:
 *  Digital → Fresado → Inyectado → Cerámica → Impresión → Calidad
 *  + Retroceso desde Calidad (RegistroIncidencia)
 *  + Reporte de Eficiencia BI (GetEfficiencyReport)
 * ══════════════════════════════════════════════════════════════════
 */

// ─── Autoload Manual (sin Composer instalado) ────────────────────────────────
spl_autoload_register(function (string $clase): void {
    $base      = __DIR__ . '/src/';
    $namespace = 'HikmaDent\\';

    if (str_starts_with($clase, $namespace)) {
        $relativo = str_replace($namespace, '', $clase);
        $archivo  = $base . str_replace('\\', DIRECTORY_SEPARATOR, $relativo) . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
        }
    }
});

use HikmaDent\Enums\AreaType;
use HikmaDent\Enums\MaterialType;
use HikmaDent\Models\OrdenTrabajo;
use HikmaDent\Models\Fresado\Maquina;
use HikmaDent\Models\Fresado\Inventario;
use HikmaDent\Observers\AuditLogger;
use HikmaDent\Observers\NotificacionTecnico;
use HikmaDent\Processes\ProcesoFresado;
use HikmaDent\Processes\ProcesoAreaInyectado;
use HikmaDent\Processes\ProcesoCeramica;
use HikmaDent\Processes\ProcesoImpresion;
use HikmaDent\Services\WorkflowManager;
use HikmaDent\Services\EfficiencyReportService;

// ═══════════════════════════════════════════════════════════════════
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║         HIKMADENT — Sistema de Laboratorio Dental             ║\n";
echo "║         Demo de Flujo Completo de OrdenTrabajo                ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// ─── 1. Configurar el WorkflowManager con Observadores ──────────────────────
echo "━━━ [1/7] Configurando WorkflowManager y Observadores ━━━━━━━━━━\n";

$workflowManager   = new WorkflowManager();
$auditLogger       = new AuditLogger();
$notificacionTecnico = new NotificacionTecnico();

// Suscribir observadores — AuditLogger escucha todo, Notificacion solo transiciones
$workflowManager->suscribir('orden.area.cambiada', [$auditLogger,         'manejarEvento']);
$workflowManager->suscribir('orden.area.cambiada', [$notificacionTecnico, 'manejarEvento']);

// ─── 2. Crear y Registrar la OrdenTrabajo ───────────────────────────────────
echo "\n━━━ [2/7] Creando Orden de Trabajo #1001 ━━━━━━━━━━━━━━━━━━━━━━\n";

$orden = new OrdenTrabajo(1001, 'Escaneo');
$workflowManager->registrarOrden($orden);
echo $orden->resumen() . "\n";

// ─── 3. Digital → Fresado (con validaciones de máquina + stock) ─────────────
echo "\n━━━ [3/7] ÁREA DIGITAL → FRESADO ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$maquina   = new Maquina('Fresadora Zirconio Z1', aceptaZirconio: true, aceptaPMMA: false);
$inventario = new Inventario([
    MaterialType::ZIRCONIO->value => 10,
    MaterialType::PMMA->value     => 5,
]);

$procesoFresado = new ProcesoFresado($maquina, $inventario, cantidadMaterialRequerida: 2);
$procesoFresado->iniciarProceso(1001, operadorId: 101);

$workflowManager->avanzarAreaSiguiente(1001, AreaType::FRESADO, operadorId: 101);
$orden->agregarDetalleTecnico('Fresado', ['material' => 'Zirconio', 'archivo' => 'diseno_1001.stl']);

sleep(1); // Simular tiempo de proceso para métricas BI

// ─── 4. Fresado → Inyectado ────────────────────────────────────────────────
echo "\n━━━ [4/7] ÁREA FRESADO → INYECTADO ━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$procesoFresado->finalizarProceso(1001, operadorId: 101);

$procesoInyectado = new ProcesoAreaInyectado();
$procesoInyectado->iniciarProceso(1001, operadorId: 102);

$workflowManager->avanzarAreaSiguiente(1001, AreaType::INYECTADO, operadorId: 102);
$orden->agregarDetalleTecnico('Inyectado', [
    'temperatura'  => 850,
    'tiempo_min'   => 20,
    'material'     => 'Ceramica prensada'
]);

sleep(1);

// ─── 5. Inyectado → Cerámica ───────────────────────────────────────────────
echo "\n━━━ [5/7] ÁREA INYECTADO → CERÁMICA ━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$procesoInyectado->finalizarProceso(1001, operadorId: 102);

$procesoCeramica = new ProcesoCeramica();
$procesoCeramica->iniciarProceso(1001, operadorId: 103);

$workflowManager->avanzarAreaSiguiente(1001, AreaType::CERAMICA, operadorId: 103);
$orden->agregarDetalleTecnico('Ceramica', [
    'tiempo_lavado'     => 10,
    'tiempo_fotocurado' => 15,
    'temperatura'       => 25.0,
]);

sleep(1);

// ─── 6. Cerámica → Impresión → Calidad ────────────────────────────────────
echo "\n━━━ [6/7] ÁREA CERÁMICA → IMPRESIÓN → CALIDAD ━━━━━━━━━━━━━━━━\n";

$procesoCeramica->finalizarProceso(1001, operadorId: 103);

$procesoImpresion = new ProcesoImpresion();
$procesoImpresion->iniciarProceso(1001, operadorId: 104);
$workflowManager->avanzarAreaSiguiente(1001, AreaType::IMPRESION, operadorId: 104);

sleep(1);

$procesoImpresion->finalizarProceso(1001, operadorId: 104);
$workflowManager->avanzarAreaSiguiente(1001, AreaType::CALIDAD, operadorId: 105);

sleep(1);

// ─── BONUS: Retroceso desde Calidad (RegistroIncidencia) ───────────────────
echo "\n━━━ [BONUS] RETROCESO desde CALIDAD con RegistroIncidencia ━━━━\n";
echo "[Calidad] Inspector detecta discrepancia oclusal — retrocede a Inyectado...\n";

$workflowManager->retrocederAArea(
    1001,
    AreaType::INYECTADO,
    operadorId: 105,
    motivoIncidencia: 'Discrepancia oclusal detectada en revisión final'
);

// ─── 7. Reporte de Eficiencia BI ───────────────────────────────────────────
echo "\n━━━ [7/7] REPORTE DE EFICIENCIA BI (GetEfficiencyReport) ━━━━━━\n";

$reportService = new EfficiencyReportService($auditLogger);
$reporte       = $reportService->GetEfficiencyReport();

// ─── Detalles técnicos JSON almacenados en la orden ────────────────────────
echo "\n━━━ DETALLES TÉCNICOS DE LA ORDEN #1001 (JSON) ━━━━━━━━━━━━━━━\n";
echo $orden->detallesTecnicosComoJson() . "\n";

// ─── Historial de Transiciones ─────────────────────────────────────────────
echo "\n━━━ HISTORIAL DE TRANSICIONES ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
foreach ($orden->getHistorial() as $i => $registro) {
    printf(
        "  #%d | %s → %s | Op.#%d | %s\n",
        $i + 1,
        $registro['area'],
        $registro['nuevaArea'],
        $registro['operadorId'],
        $registro['timestamp']
    );
}

// ─── Notificaciones enviadas ───────────────────────────────────────────────
echo "\n━━━ NOTIFICACIONES ENVIADAS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
foreach ($notificacionTecnico->getNotificacionesEnviadas() as $n) {
    echo "  → [{$n['timestamp']}] {$n['destinatario']}: {$n['mensaje']}\n";
}

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║             Demo completado exitosamente ✅                    ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";
