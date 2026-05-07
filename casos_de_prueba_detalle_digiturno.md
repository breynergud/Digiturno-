# Detalle de Casos de Prueba — Proyecto Digiturno APE

Este documento contiene el desglose paso a paso de las 48 pruebas técnicas del sistema.

---

### CP-001: Registro de ciudadano con documento válido
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-001: Registro de ciudadano con documento válido |
| **Prioridad** | Alta |
| **Módulo** | CU-01: Registro e Identificación de Ciudadano |
| **Precondiciones** | 1. Kiosco en pantalla de inicio.<br>2. Servicios disponibles. |
| **Descripción** | Verificar que el sistema acepta un documento válido, lo registra y genera un turno. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Tipo: Cédula, Número: 1098765432 | El sistema registra el documento, genera un código (ej: G-001) y lo muestra. Turno en estado 'Pendiente'. | | |
| Tipo: NIT, Número: 900123456 | El sistema registra el documento, genera código empresarial (ej: E-001). | | |

---

### CP-002: Bloqueo de ciudadano con restricciones
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-002: Bloqueo de ciudadano con restricciones |
| **Prioridad** | Alta |
| **Módulo** | CU-01: Registro e Identificación de Ciudadano |
| **Precondiciones** | 1. Kiosco en pantalla de inicio. |
| **Descripción** | Verificar que el sistema impide el registro si los datos son inválidos o el usuario está bloqueado. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Número: (Vacío) | El sistema resalta el error y no permite presionar "Confirmar". | | |
| Número: "ABC123" | El sistema impide el ingreso de caracteres no numéricos. | | |

---

### CP-003: Selección de categoría disponible
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-003: Selección de categoría disponible |
| **Prioridad** | Alta |
| **Módulo** | CU-02: Solicitar Turno por Categoría |
| **Precondiciones** | 1. Pantalla de selección activa. |
| **Descripción** | Validar que el usuario pueda elegir entre las 4 categorías institucionales. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Empresario" | El sistema redirige al formulario de ingreso de NIT. | | |

---

### CP-004: Selección de categoría no habilitada
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-004: Selección de categoría no habilitada |
| **Prioridad** | Media |
| **Módulo** | CU-02: Solicitar Turno por Categoría |
| **Precondiciones** | N/A |
| **Descripción** | Validar que el sistema maneje estados donde una categoría no tenga atención. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en categoría cerrada | El sistema permite sacar el turno (se atiende por desbordamiento después). | | |

---

### CP-005: Generación correcta de prefijo por servicio
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-005: Generación correcta de prefijo por servicio |
| **Prioridad** | Alta |
| **Módulo** | CU-03: Generación de Prefijos Inteligentes |
| **Precondiciones** | CU-01 finalizado. |
| **Descripción** | Verificar que el código generado sea coherente con la población atendida. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Categoría: Víctimas | Genera código tipo V-XXX. | | |
| Categoría: Prioritario | Genera código tipo P-XXX. | | |

---

### CP-006: Fallo de BD en generación de prefijo
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-006: Fallo de BD en generación de prefijo |
| **Prioridad** | Media |
| **Módulo** | CU-03: Generación de Prefijos Inteligentes |
| **Precondiciones** | Simular pérdida de conexión a DB. |
| **Descripción** | Validar la robustez del sistema ante fallos de persistencia. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Confirmar turno | El sistema muestra alerta de error y no consume el correlativo. | | |

---

### CP-007: Pre-asignación por Mesa Fija
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-007: Pre-asignación por Mesa Fija |
| **Prioridad** | Alta |
| **Módulo** | CU-13: Llamado de Turno Siguiente |
| **Precondiciones** | Varias mesas activas. |
| **Descripción** | Verificar que los turnos se asignen primero a la mesa de origen. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Turno creado para Mesa 5 | El asesor de Mesa 5 lo recibe al presionar "Llamar Siguiente". | | |

---

### CP-008: Pre-asignación sin asesores disponibles
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-008: Pre-asignación sin asesores disponibles |
| **Prioridad** | Alta |
| **Módulo** | CU-13: Llamado de Turno Siguiente |
| **Precondiciones** | Todos los asesores en "Pausa". |
| **Descripción** | Validar que el turno quede en cola de espera correctamente. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Generar 5 turnos | Los turnos aparecen en el panel de monitorización como "Pendientes". | | |

---

### CP-009: Actualización de lista de turnos en TV
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-009: Actualización de lista de turnos en TV |
| **Prioridad** | Alta |
| **Módulo** | CU-03: Visualización de Turnos (TV) |
| **Precondiciones** | Pantalla TV encendida. |
| **Descripción** | Verificar el refresco automático de la interfaz pública. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Llamado de nuevo turno | La TV se actualiza en máximo 4 segundos sin intervención manual. | | |

---

### CP-010: Pérdida de conexión en pantalla TV
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-010: Pérdida de conexión en pantalla TV |
| **Prioridad** | Media |
| **Módulo** | CU-03: Visualización de Turnos (TV) |
| **Precondiciones** | Desconectar cable de red. |
| **Descripción** | Validar que la TV no se rompa visualmente si falla el internet. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Desconexión activa | El sistema mantiene la última lista de turnos y muestra un pequeño aviso de reconexión. | | |

---

### CP-011: Notificación sonora y visual al llamar turno
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-011: Notificación sonora y visual al llamar turno |
| **Prioridad** | Alta |
| **Módulo** | CU-04: Llamado por Voz y Sonido |
| **Precondiciones** | Audio habilitado en TV. |
| **Descripción** | Verificar el impacto visual y auditivo del llamado. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Llamar Siguiente" | El turno parpadea en la TV y suena la alerta sonora. | | |

---

### CP-012: Notificación solo visual con audio desactivado
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-012: Notificación solo visual con audio desactivado |
| **Prioridad** | Media |
| **Módulo** | CU-04: Llamado por Voz y Sonido |
| **Precondiciones** | Botón de audio en "Apagado". |
| **Descripción** | Validar que el sistema respete la configuración de silencio. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Nuevo llamado | El turno cambia visualmente pero no se emite ningún sonido. | | |

---

### CP-013: Síntesis de voz anuncia turno y mesa
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-013: Síntesis de voz anuncia turno y mesa |
| **Prioridad** | Alta |
| **Módulo** | CU-04: Llamado por Voz y Sonido |
| **Precondiciones** | Audio activo. |
| **Descripción** | Verificar que el texto dictado sea el correcto. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Turno V-002, Mesa 18 | Voz dicta: "Turno V cero cero dos, por favor diríjase a la mesa dieciocho". | | |

---

### CP-014: TTS no disponible — fallback a sonido ding
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-014: TTS no disponible — fallback a sonido ding |
| **Prioridad** | Media |
| **Módulo** | CU-04: Llamado por Voz y Sonido |
| **Precondiciones** | Navegador sin soporte Web Speech API. |
| **Descripción** | Asegurar que al menos el "Beep" suene si falla la voz. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Nuevo llamado | El sistema reproduce el archivo .mp3 de alerta institucional. | | |

---

### CP-015: Reproducción de video institucional en bucle
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-015: Reproducción de video institucional |
| **Prioridad** | Baja |
| **Módulo** | CU-05: Reproducción de Video Guía |
| **Precondiciones** | TV cargada. |
| **Descripción** | Validar la integración con YouTube. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Carga de página | El video inicia automáticamente y se reinicia al terminar. | | |

---

### CP-016: Fallo de internet — imagen estática en TV
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-016: Fallo de internet — imagen estática |
| **Prioridad** | Media |
| **Módulo** | CU-05: Reproducción de Video Guía |
| **Precondiciones** | Sin conexión externa. |
| **Descripción** | Evitar pantallas negras en el área de video. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Carga de TV sin red | Se muestra el logo institucional APE de alta resolución. | | |

---

### CP-017: Desplazamiento de mensajes en marquesina
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-017: Desplazamiento de mensajes |
| **Prioridad** | Baja |
| **Módulo** | CU-03: Visualización de Turnos (TV) |
| **Precondiciones** | N/A |
| **Descripción** | Verificar legibilidad del texto en movimiento. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Observación visual | El texto se desplaza de derecha a izquierda sin saltos (suave). | | |

---

### CP-018: Consulta de Historial de Atenciones del Día (Asesor)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-018: Consulta de Historial de Atenciones del Día |
| **Prioridad** | Media |
| **Módulo** | CU-18: Consulta de Historial de Atenciones del Día (Asesor) |
| **Precondiciones** | El asesor debe haber finalizado al menos una atención en el día actual. |
| **Descripción** | Verificar que el asesor pueda visualizar correctamente su historial de trabajo diario en la parte inferior del dashboard. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Desplazarse al final del Dashboard del Asesor | El sistema muestra una tabla con los códigos de los turnos atendidos, su hora de inicio y su hora de finalización. | El sistema utiliza la función interna `getHistorialHoy` para recuperar y mostrar cronológicamente las atenciones del asesor en la sesión actual. | Cumple |

---

### CP-019: Cambio de estado a 'Descanso' sin turno activo
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-019: Cambio de estado a 'Descanso' |
| **Prioridad** | Alta |
| **Módulo** | CU-12: Cambio de Estado |
| **Precondiciones** | Asesor disponible. |
| **Descripción** | Validar cambio de disponibilidad. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en Switch | El badge cambia a "En Pausa" y el color a gris/amarillo. | | |

---

### CP-020: Cambio de estado prohibido con turno activo
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-020: Cambio de estado prohibido |
| **Prioridad** | Alta |
| **Módulo** | CU-12: Cambio de Estado |
| **Precondiciones** | Asesor atendiendo usuario. |
| **Descripción** | Evitar que el asesor se ponga en pausa mientras tiene a alguien en mesa. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en Switch | El sistema muestra alerta: "Finalice la atención primero". | | |

---

### CP-021: Llamar siguiente turno disponible
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-021: Llamar siguiente turno |
| **Prioridad** | Alta |
| **Módulo** | CU-13: Llamado de Turno Siguiente |
| **Precondiciones** | Cola con turnos. |
| **Descripción** | Flujo estándar de atención. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Llamar Siguiente" | El sistema carga el turno más antiguo y lo asigna al asesor. | | |

---

### CP-022: Llamar siguiente sin turnos en espera
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-022: Llamar sin turnos |
| **Prioridad** | Media |
| **Módulo** | CU-13: Llamado de Turno Siguiente |
| **Precondiciones** | Cola vacía. |
| **Descripción** | Manejo de colas vacías. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Llamar Siguiente" | Notificación: "No hay turnos pendientes por el momento". | | |

---

### CP-023: Atención de turno de desbordamiento de otra mesa
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-023: Atención de desbordamiento |
| **Prioridad** | Media |
| **Módulo** | CU-13: Llamado de Turno Siguiente |
| **Precondiciones** | Mi mesa está vacía, otra mesa tiene turnos. |
| **Descripción** | Validar algoritmo de apoyo entre mesas. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Llamar Siguiente" | Se asigna el turno de la otra mesa (Overflow). | | |

---

### CP-024: Prioridad obligatoria sobre desbordamiento
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-024: Prioridad sobre desbordamiento |
| **Prioridad** | Alta |
| **Módulo** | CU-13: Llamado de Turno Siguiente |
| **Precondiciones** | Turno V en espera (cualquier mesa). |
| **Descripción** | Asegurar que las víctimas se atiendan primero sin importar la mesa. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Llamar Siguiente" | El sistema asigna el turno de Víctimas (V) prioritariamente. | | |

---

### CP-025: Actualización de datos de persona durante atención
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-025: Actualización de datos de persona |
| **Prioridad** | Alta |
| **Módulo** | CU-15: Atención y Captura |
| **Precondiciones** | Atención activa. |
| **Descripción** | Verificar persistencia de datos del ciudadano. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Cambiar nombre y presionar Guardar | El sistema confirma "Datos actualizados" y los guarda en la base de datos. | | |

---

### CP-026: Documento duplicado al actualizar persona
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-026: Documento duplicado |
| **Prioridad** | Alta |
| **Módulo** | CU-15: Atención y Captura |
| **Precondiciones** | Atención activa. |
| **Descripción** | Evitar inconsistencias de identidad. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Cambiar documento a uno ya existente | El sistema arroja error de "Documento ya registrado". | | |

---

### CP-027: Finalizar atención con confirmación
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-027: Finalizar atención |
| **Prioridad** | Alta |
| **Módulo** | CU-16: Finalización de Atención |
| **Precondiciones** | Usuario en mesa. |
| **Descripción** | Cierre del ciclo de atención. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Finalizar Atención" | El sistema solicita confirmación y luego libera al asesor. | | |

---

### CP-028: Cierre accidental requiere confirmación
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-028: Cierre accidental |
| **Prioridad** | Media |
| **Módulo** | CU-16: Finalización de Atención |
| **Precondiciones** | Atención activa. |
| **Descripción** | Evitar cierres de turno por error de clic. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic fuera del botón o en escape | El sistema no cierra el turno; se requiere clic explícito en "Finalizar". | | |

---

### CP-029: Aislamiento de pestañas — invalidación de sesión anterior
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-029: Aislamiento de pestañas |
| **Prioridad** | Alta |
| **Módulo** | CU-10: Seguridad |
| **Precondiciones** | Abrir dos pestañas del dashboard. |
| **Descripción** | Evitar que una pestaña interfiera con el tiempo de inactividad de la otra. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Actividad en pestaña A | La pestaña B sigue su propio contador de inactividad. | | |

---

### CP-030: Refresco de página mantiene el mismo window_id
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-030: Refresco mantiene window_id |
| **Prioridad** | Media |
| **Módulo** | CU-09: Persistencia |
| **Precondiciones** | Sesión activa. |
| **Descripción** | Asegurar que el F5 no sea detectado como un nuevo dispositivo. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| F5 en el navegador | El sistema reconoce la misma pestaña y mantiene el estado del asesor. | | |

---

### CP-031: Dashboard muestra datos actualizados del coordinador
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-031: Monitorización Coordinador |
| **Prioridad** | Alta |
| **Módulo** | CU-19: Monitorización |
| **Precondiciones** | Coordinador logueado. |
| **Descripción** | Fidelidad de los datos administrativos. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Observar tabla de asesores | Los estados coinciden 100% con lo que el asesor ve en su pantalla. | | |

---

### CP-032: Dashboard sin actividad registrada
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-032: Dashboard vacío |
| **Prioridad** | Baja |
| **Módulo** | CU-19: Monitorización |
| **Precondiciones** | No hay asesores conectados. |
| **Descripción** | Manejo de estados vacíos. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Entrar al monitor | Se muestra mensaje: "No hay asesores conectados actualmente". | | |

---

### CP-033: Reasignación de fila de asesor conectado
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-033: Reasignación de Rol |
| **Prioridad** | Alta |
| **Módulo** | CU-20: Reasignación Remota |
| **Precondiciones** | Asesor en mesa. |
| **Descripción** | Flexibilidad operativa. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Cambiar G a V en monitor | El asesor recibe notificación visual y su cola cambia de inmediato. | | |

---

### CP-034: Cambio de fila efectivo en próximo turno
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-034: Cambio de Rol Efectivo |
| **Prioridad** | Media |
| **Módulo** | CU-20: Reasignación Remota |
| **Precondiciones** | Asesor atendiendo. |
| **Descripción** | No interrumpir la atención actual. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Cambiar rol durante atención | El sistema espera a que el asesor finalice para aplicar el nuevo perfil de llamado. | | |

---

### CP-035: Crear nuevo asesor y asignar mesa libre
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-035: Registro Automatizado Mesa |
| **Prioridad** | Alta |
| **Módulo** | CU-10: Registro Asesor |
| **Precondiciones** | Formulario registro. |
| **Descripción** | Lógica de mesa física. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Ingresar Mesa 20 | Sistema asigna perfil "General" automáticamente. | | |

---

### CP-036: Mesa ocupada al crear asesor
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-036: Error Mesa Ocupada |
| **Prioridad** | Alta |
| **Módulo** | CU-10: Registro Asesor |
| **Precondiciones** | Mesa 5 ya tiene un asesor. |
| **Descripción** | Integridad de ubicaciones. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Intentar registrar Mesa 5 | Error: "La mesa ya tiene un asesor asignado". | | |

---

### CP-037: Asesor atiende turno empresarial
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-037: Llamado Empresarial Asesor |
| **Prioridad** | Alta |
| **Módulo** | CU-21: Atención Empresarial |
| **Precondiciones** | Turno E pendiente. |
| **Descripción** | Los asesores pueden seleccionar y atender turnos de empresarios. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "ATENDER" (Asesor) | El sistema asigna el turno E al asesor y suena en TV con su número de mesa. | El sistema asigna el turno al asesor de forma exitosa y permite la captura de datos. | Cumple |

---

### CP-038: Bloqueo de atención para Coordinadores
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-038: Bloqueo Atención Admin |
| **Prioridad** | Media |
| **Módulo** | CU-21: Atención Empresarial |
| **Precondiciones** | Sesión de Coordinador activa. |
| **Descripción** | El coordinador solo supervisa; no debe tener botones de atención. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Observar panel Coordinador | No existe botón de "Llamar Siguiente" ni "Atender". | El dashboard del coordinador solo muestra la cola informativa, sin botones de acción. | Cumple |

---

### CP-039: Reporte de tiempos de espera con datos correctos
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-039: Reporte de Espera |
| **Prioridad** | Alta |
| **Módulo** | CU-23: Reporte Semanal |
| **Precondiciones** | Atenciones finalizadas. |
| **Descripción** | Exactitud de métricas. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Consultar reporte | El tiempo de espera coincide con: Hora Llamado - Hora Ticket. | | |

---

### CP-040: Reporte ignora turnos con tiempos inconsistentes
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-040: Filtro de Calidad Datos |
| **Prioridad** | Media |
| **Módulo** | CU-23: Reporte Semanal |
| **Precondiciones** | Turno con error de fecha (ej: año 1970). |
| **Descripción** | Evitar promedios basura. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Procesar reporte | El sistema excluye registros con tiempos negativos o irreales. | | |

---

### CP-041: Reporte de tiempos de atención semanal
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-041: Reporte Semanal |
| **Prioridad** | Alta |
| **Módulo** | CU-23: Reporte Semanal |
| **Precondiciones** | Datos de la semana. |
| **Descripción** | Agregación de datos. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Entrar al reporte | Se visualiza el consolidado de Lunes a Sábado de la semana actual. | | |

---

### CP-042: Atenciones abiertas no se incluyen en reporte
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-042: Exclusión Atenciones Activas |
| **Prioridad** | Media |
| **Módulo** | CU-23: Reporte Semanal |
| **Precondiciones** | Asesor atendiendo actualmente. |
| **Descripción** | Solo reportar ciclos cerrados. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Generar reporte | El turno actual no aparece en las estadísticas de duración todavía. | | |

---

### CP-043: Historial detallado de atenciones por asesor
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-043: Historial Detallado |
| **Prioridad** | Alta |
| **Módulo** | CU-26: Historial Cronológico |
| **Precondiciones** | Filtro por asesor aplicado. |
| **Descripción** | Detalle de auditoría. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Bajar al historial | Se listan todos los turnos del asesor con sus marcas de tiempo exactas. | | |

---

### CP-044: Prioridad por inanición después de 35 minutos
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-044: Prioridad por Inanición |
| **Prioridad** | Alta |
| **Módulo** | CU-22: Prioridad Inanición |
| **Precondiciones** | Turno G con 36 min espera. |
| **Descripción** | Equidad de servicio. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Llamar Siguiente" | El sistema asigna el turno antiguo (G) antes que los nuevos prioritarios. | | |

---

### CP-045: Notificación Visual de Llamado (TV)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-045: Notificación Visual TV |
| **Prioridad** | Alta |
| **Módulo** | CU-28: Notificación TV |
| **Precondiciones** | Módulo TV cargado. |
| **Descripción** | Verificar que el turno aparezca resaltado en la pantalla de TV. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Aceptar turno G-001 | El número G-001 aparece en la sección principal de la TV con su mesa. | El sistema actualiza la vista de la TV al instante mostrando el turno llamado en tamaño destacado. | Cumple |

---

### CP-046: Notificación Sonora de Llamado (TV)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-046: Timbre de Llamado |
| **Prioridad** | Alta |
| **Módulo** | CU-28: Notificación TV |
| **Precondiciones** | Altavoces de TV activos. |
| **Descripción** | Asegurar que el sistema emita el sonido de alerta. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Generar nuevo llamado | La TV reproduce un audio claro para captar la atención. | El navegador de la TV reproduce el archivo de audio configurado en el momento del refresco de datos. | Cumple |

---

### CP-047: Cierre de sesión automático por inactividad
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-047: Logout Inactividad |
| **Prioridad** | Alta |
| **Módulo** | CU-08: Inactividad Protegida |
| **Precondiciones** | 15 min sin mover mouse. |
| **Descripción** | Seguridad de estación. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Tiempo agotado | El sistema redirige al login y limpia cookies de sesión. | | |

---

### CP-048: Sesión activa durante atención aunque no haya movimiento
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-048: Protección Sesión Activa |
| **Prioridad** | Alta |
| **Módulo** | CU-08: Inactividad Protegida |
| **Precondiciones** | Atención larga (> 15 min). |
| **Descripción** | No cerrar sesión mientras se atiende a alguien. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
---

### CP-049: Flujo completo de inicio y fin de jornada laboral
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CP-049: Flujo completo de inicio y fin de jornada laboral |
| **Prioridad** | Alta |
| **Módulo** | CU-29: Gestión de Jornada Laboral |
| **Precondiciones** | 1. Sesión de asesor iniciada.<br>2. El asesor debe estar en estado 'Inactivo'. |
| **Descripción** | Verificar que el asesor puede iniciar y finalizar su turno, y que el tiempo se registra correctamente. |

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en "Iniciar Turno" | El estado cambia a 'Disponible'. Inicia el cronómetro. El coordinador ve al asesor en verde. | | |
| Clic en "Finalizar Turno" | El sistema solicita confirmación. Al aceptar, el estado vuelve a 'Inactivo'. El cronómetro se detiene y reinicia. | | |
| Verificar Reporte | El reporte del coordinador muestra la nueva jornada con la duración exacta y atenciones realizadas. | | |
