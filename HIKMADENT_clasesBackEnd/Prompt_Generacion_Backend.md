# Descripción Técnica para el Prompt de Generación de IA

Para que la IA genere el código backend correctamente para el sistema **Hokma Dent**, copia y pega el siguiente prompt:

---

## PROMPT DE GENERACIÓN DE CÓDIGO:

"Actúa como un Arquitecto de Software Senior. Genera el código backend (en Laravel 11 o ASP.NET Core 8) para un sistema de Laboratorio Dental llamado **'Hokma Dent'**.

### REQUERIMIENTOS ARQUITECTÓNICOS:

- **SOLID:** Usa Interfaces para los procesos de manufactura e inyección de dependencias para los servicios de cada área (Digital, Fresado, Inyectado, Cerámica).
- **CLEAN CODE:** Nombres de métodos descriptivos (`IniciarProcesoDeSintetizado`, `ValidarEscaneoIntraoral`). Usa tipos fuertemente tipados (Enums para materiales y estados).
- **TRAZABILIDAD (BI):** Cada cambio de área debe disparar un evento que registre el Timestamp y el OperadorId en una tabla de auditoría para análisis de Business Intelligence.
- **FLUJO DE TRABAJO:** Implementa una máquina de estados para la `OrdenTrabajo` que siga este flujo: Digital -> (Fresado/Yeso) -> Inyectado -> Cerámica -> Calidad.
- **REGLAS DE NEGOCIO:**
    - Si el `AreaCalidad` detecta un error, debe permitir el retroceso a un área específica (`RegistroIncidencia`).
    - El área de Fresado debe validar disponibilidad de `Maquina` y `Stock` antes de iniciar.

### PASOS DE IMPLEMENTACIÓN:

1. **Capa de Datos:** Crear una tabla `Ordenes` con un campo JSON o tablas relacionadas para los detalles técnicos de cada área (temperaturas, tiempos de fraguado).
2. **Capa de Servicios:** Crear un `WorkflowManager` que sea el único encargado de mover la orden de una estación a otra. Esto evita que las áreas estén "pegadas" entre sí (bajo acoplamiento).
3. **Capa de Notificaciones:** Implementar un sistema de observadores (Observer Pattern) para que cuando una pieza termine en "Fresado", le llegue una notificación al técnico de "Inyectado" automáticamente.
4. **Dashboards BI:** El código debe incluir una función `GetEfficiencyReport()` que calcule el tiempo promedio que cada orden pasa en cada una de las interfaces `IProcesable`.

Este enfoque garantiza una calificación excelente en arquitectura de software, permitiendo el crecimiento del sistema (ej. agregar un área de Ortodoncia implementando `IProcesable` sin romper lo existente).

### DIAGRAMAS DE CLASES DE REFERENCIA:

#### ## DENTAL
```mermaid
classDiagram
    class PiezaDental {
        +int id
        +string tipoDiseño
        +DateTime fechaIngreso
        +iniciarProcesoCeramico()
    }
    class Maquillaje {
        +string tipo (Corona/Estratificado)
        +int tiempoEstimado
        +completarMaquillaje()
    }
    class Ajuste {
        +bool formaAdaptacion
        +bool texturaAltura
        +validarCalidad() : bool
    }
    class RegistroIncidencia {
        +string problema
        +retornarAAdaptacion()
    }

    PiezaDental --> Maquillaje : pasa a
    Maquillaje --> Ajuste : requiere
    Ajuste --> RegistroIncidencia : si falla calidad
```

#### ## INYECTADO
```mermaid
classDiagram
    class ProcesoInyectado {
        +int ordenId
        +string material
        +registrarFase(nombreFase)
        +verificarCalidad() bool
    }
    
    class FaseTermica {
        +float temperaturaInicial
        +int tiempoMinutos
        +validarCurvaCalor()
    }
    
    class AjusteAdaptacion {
        +bool escaneoIntraoralOk
        +bool ajusteOclusalOk
        +finalizarOrden()
    }

    ProcesoInyectado *-- FaseTermica
    ProcesoInyectado *-- AjusteAdaptacion
```

#### ## FRESADO
```mermaid
classDiagram
    class TrabajoFresado {
        +int id_trabajo
        +string archivo_diseno
        +string material_seleccionado
        +iniciarCarga()
        +finalizarSintetizado()
    }

    class Maquina {
        +string nombre
        +bool aceptaZirconio
        +bool aceptaPMMA
        +verificarDisponibilidad()
    }

    class Inventario {
        +string tipo_material
        +int cantidad_disponible
        +descontarStock(cantidad)
    }

    TrabajoFresado --> Maquina : asignado a
    TrabajoFresado --> Inventario : consume
```

#### ## ADMINISTRACION
```mermaid
classDiagram
    class Cliente_Doctor {
        +int id_doctor
        +string nombre
        +string clinica
    }
    class Paciente {
        +int id_paciente
        +string nombre
        +int edad
    }
    class OrdenTrabajo {
        +int id_orden
        +DateTime fecha_ingreso
        +string tipo_entrada (Escaneo/ModeloFisico)
        +string estado_diseno
        +crearOrden()
    }
    class DiseñoExocad {
        +string nombre_archivo
        +float tiempo_diseno
        +string version_software
        +vincularArchivo()
    }

    Cliente_Doctor "1" -- "*" OrdenTrabajo
    Paciente "1" -- "*" OrdenTrabajo
    OrdenTrabajo "1" -- "1" DiseñoExocad
```

#### ## YESO
```mermaid
classDiagram
    class AreaYeso {
        +int id_proceso
        +string material_base
        +DateTime hora_secado
        +registrarSalida()
    }

    class MaterialYeso {
        +string nombre (Zirconio/PMMA/Resina)
        +float cantidad_gramos_ml
        +bool requiereSintetizado
    }

    AreaYeso "1" -- "1" MaterialYeso : utiliza
```

#### ## CERAMICA
```mermaid
classDiagram
    class OrdenImpresion {
        +int id_orden
        +DateTime fecha_recepcion
        +Enum estado (Lavado, Fotocurado, Terminado)
        +registrarError(tipoError)
        +actualizarEstado()
    }

    class PostProcesado {
        +int tiempo_lavado
        +int tiempo_fotocurado
        +float temperatura
        +validarProceso() bool
    }

    class ControlMantenimiento {
        +DateTime ultima_limpieza
        +int total_impresiones
        +notificarMantenimiento()
    }

    OrdenImpresion "1" -- "1" PostProcesado
    OrdenImpresion "*" -- "1" ControlMantenimiento : afecta a
```

#### ## IMPRESION
```mermaid
classDiagram
    class ModuloImpresion {
        +int id_recepcion
        +bool impresioValida
        +string tipoYeso
        +DateTime inicioFraguado
        +registrarExcepcion(descripcion)
    }

    class GestionPrioridad {
        +Enum nivel (Urgente, Normal, Retrasado)
        +calcularOrdenCola()
    }

    class TransferenciaArea {
        +string areaDestino
        +DateTime horaTransferencia
        +notificarSiguienteEstacion()
    }

    ModuloImpresion --> GestionPrioridad : consulta
    ModuloImpresion --> TransferenciaArea : finaliza con
```
"
---
