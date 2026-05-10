# Plan de Pruebas de Calidad (QA) - Sistema Digiturno APE

Este documento registra los casos de prueba ejecutados para validar las funcionalidades del sistema.

---

## CP-001: Registro de ciudadano con documento válido
**Prioridad:** Alta  
**Módulo:** CU-01: Registro e Identificación de Ciudadano  
**Precondiciones:** Kiosco en pantalla de inicio con servicios disponibles.  
**Descripción:** Verificar que el sistema acepta un documento válido, lo registra y genera un turno.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Tipo: Cédula, Número: 1098765432 | El sistema registra el documento, genera un código (ej. G-001) y lo muestra. Turno en estado 'ESPERA'. | El sistema genera exitosamente el turno y lo muestra en pantalla. | **Pasa** |
| Tipo: Cédula, Número: AAAAA | El sistema muestra una alerta: "El número de documento debe contener solo dígitos." | El sistema bloquea letras en el teclado físico y muestra alerta si se intenta procesar basura. | **Pasa** |

### Notas de Corrección (10/05/2026):
- Se ajustó el Kiosco para que limpie el mensaje de error automáticamente al escribir con teclado físico.
- Se actualizó el resultado esperado para reflejar el estado 'ESPERA' en lugar de 'Pendiente', acorde a la nueva interfaz de TV.

---

## CP-002: Generación de Ticket de Turno
**Prioridad:** Alta  
**Módulo:** CU-02: Generación de Ticket de Turno  
**Precondiciones:** CP-001 finalizado exitosamente.  
**Descripción:** Verificar que el código generado sea coherente con la categoría seleccionada.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Categoría: Víctimas | Genera código tipo V-XXX y se muestra en la TV de inmediato. | El ticket se genera exitosamente para el tipo víctimas y se muestra en pantalla correctamente. | **Pasa** |

---

## CP-003: Visualización de Turnos en Pantalla TV
**Prioridad:** Alta  
**Módulo:** CU-03: Visualización de Turnos en Pantalla TV  
**Precondiciones:** El módulo /tv abierto en navegador.  
**Descripción:** Verificar que la TV actualiza los turnos en tiempo real.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Llamado de nuevo turno | La TV se actualiza en máximo 4 segundos sin intervención manual. | El sistema utiliza un temporizador automático que consulta la API cada 4 segundos. La interfaz se actualiza dinámicamente. | **Pasa** |

---

## CP-004: Llamado por Voz y Sonido (TTS)
**Prioridad:** Alta  
**Módulo:** CU-04: Llamado por Voz y Sonido (TTS)  
**Precondiciones:** Audio habilitado en la TV.  
**Descripción:** Verificar que la síntesis de voz anuncia correctamente el turno y la mesa.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Turno V-002, Mesa 18 | Voz dicta: 'Turno V cero cero dos, por favor diríjase a la mesa dieciocho'. | El sistema de voz indica el número de turno y la mesa correctamente (2 veces por llamado). | **Pasa** |

---

## CP-005: Reproducción de Video Guía APE
**Prioridad:** Baja  
**Módulo:** CU-05: Reproducción de Video Guía APE  
**Precondiciones:** Conexión a internet activa en la TV.  
**Descripción:** Validar la integración con YouTube y reproducción en bucle.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Carga de página TV | El video inicia automáticamente y se reinicia al terminar en bucle. | El video se reproduce exitosamente en bucle continuo. | **Pasa** |

---

## CP-006: Inicio de Sesión Administrativa
**Prioridad:** Alta  
**Módulo:** CU-06: Inicio de Sesión Administrativa  
**Precondiciones:** Asesor registrado en la base de datos.  
**Descripción:** Verificar que las credenciales válidas permiten acceso y las inválidas son rechazadas.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Correo: asesor@ape.gov.co, Clave: correcta | Acceso al dashboard del asesor. | El sistema permite iniciar sesión y redirige correctamente al dashboard. | **Pasa** |
| Correo: incorrecto, Clave: cualquiera | Mensaje: "Credenciales incorrectas." | El sistema deniega el acceso y muestra el error de validación, protegiendo la cuenta. | **Pasa** |

---

## CP-007: Cierre de Sesión Manual
**Prioridad:** Alta  
**Módulo:** CU-07: Cierre de Sesión Manual  
**Precondiciones:** Haber iniciado sesión previamente.  
**Descripción:** Verificar que el botón de cerrar sesión funciona correctamente.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en botón 'Cerrar Sesión' | El sistema destruye la sesión y redirige al login. | Al darle al botón de cerrar sesión redirige exitosamente y limpia la sesión. | **Pasa** |

---

## CP-008: Cierre de Sesión por Inactividad (Timeout)
**Prioridad:** Alta  
**Módulo:** CU-08: Cierre de Sesión por Inactividad (Timeout)  
**Precondiciones:** Sesión abierta sin interacción del mouse o teclado.  
**Descripción:** Verificar que la sesión se cierre tras **10 minutos** de inactividad (9 min de espera + 1 min de aviso).

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Inactividad prolongada | A los 9 min sale aviso. A los 10 min redirige al login y limpia cookies. | El sistema avisa y cierra sesión automáticamente si el asesor está en "Disponible". Si está en "En espera" u "Ocupado", mantiene la sesión activa. | **Pasa** |

---

## CP-009: Mantenimiento de Sesión (Refresco F5)
**Prioridad:** Media  
**Módulo:** CU-09: Mantenimiento de Sesión (Refresco F5)  
**Precondiciones:** Sesión activa en el servidor.  
**Descripción:** Verificar que el refresco de página (F5) no interrumpe la sesión.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Presionar F5 en el navegador | El sistema mantiene al usuario en su dashboard sin pedir credenciales. | El sistema mantiene exitosamente la sesión y el estado del asesor. | **Pasa** |

---

## CP-010: Registro de Nuevo Asesor (Automatizado)
**Prioridad:** Alta  
**Módulo:** CU-10: Registro de Nuevo Asesor (Automatizado)  
**Precondiciones:** Mesa física disponible (1 a 20).  
**Descripción:** Verificar la asignación automática del perfil (rol) según el número de mesa.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Ingresar Mesa 18 (rango 16-19) | Sistema asigna perfil 'Víctimas' y bloquea edición manual. | El sistema asigna correctamente el rol según la mesa y restringe cambios manuales. | **Pasa** |

---

## CP-011: Login de Asesor con Validación de Perfil
**Prioridad:** Alta  
**Módulo:** CU-11: Login de Asesor con Validación de Perfil  
**Precondiciones:** CP-010 completado.  
**Descripción:** Verificar que el dashboard carga con la configuración correcta de turnos según el rol.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Asesor inicia sesión | Sistema carga dashboard con turnos de su perfil (G o V). | El sistema carga exitosamente sus turnos dependiendo su rol asignado. | **Pasa** |

---

## CP-012: Cambio de Estado de Disponibilidad (Asesor)
**Prioridad:** Alta  
**Módulo:** CU-12: Cambio de Estado de Disponibilidad (Asesor)  
**Precondiciones:** Sesión iniciada.  
**Descripción:** Validar que el cambio de disponibilidad (Pausa) se refleje correctamente en la interfaz.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en Switch de estado | El badge cambia a 'En Espera' y el color a amarillo. | El badge cambia correctamente a "En Espera" y su color a amarillo. | **Pasa** |

---

## CP-013: Llamado de Turno Siguiente (Prioridad y FIFO)
**Prioridad:** Alta  
**Módulo:** CU-13: Llamado de Turno Siguiente (Algoritmo)  
**Precondiciones:** Estado en 'Disponible' y turnos en cola.  
**Descripción:** Verificar que el sistema asigna el turno siguiendo la jerarquía de prioridad y luego el tiempo de espera (FIFO).

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en 'Llamar Siguiente' | El sistema asigna el turno más prioritario (Empresario > Víctimas > ...) y antiguo. | El sistema asigna el turno siguiendo la jerarquía de prioridad y el orden FIFO correctamente. | **Pasa** |

---

## CP-014: Llamado Manual de Turnos Prioritarios/Víctimas
**Prioridad:** Alta  
**Módulo:** CU-14: Llamado Manual de Turnos Prioritarios/Víctimas  
**Precondiciones:** Existencia de turnos V o P en el panel lateral.  
**Descripción:** Permitir al asesor seleccionar y atender un turno prioritario específico desde el panel lateral.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en botón 'Atender' en panel lateral | El sistema valida, asigna el turno al asesor e inicia la atención inmediatamente. | El sistema genera botones dinámicos y permite la atención manual del ID seleccionado exitosamente. | **Pasa** |

---

## CP-015: Atención y Captura de Datos de Ciudadano
**Prioridad:** Alta  
**Módulo:** CU-15: Atención y Captura de Datos de Ciudadano  
**Precondiciones:** Turno activo en mesa.  
**Descripción:** Verificar la persistencia de los datos del ciudadano capturados durante la atención.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Nombre: Juan, Teléfono: 3001234567 | Sistema confirma 'Datos guardados' y los persiste en la base de datos. | El sistema guarda exitosamente la información vinculada al ciudadano y su atención. | **Pasa** |

---

## CP-016: Finalización de Atención
**Prioridad:** Alta  
**Módulo:** CU-16: Finalización de Atención  
**Precondiciones:** Datos de atención capturados.  
**Descripción:** Verificar el cierre correcto del turno y liberación del asesor.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en 'Finalizar Atención' | Sistema guarda hora cierre, calcula duración y asesor queda disponible. | El sistema registra el fin de la atención, calcula los tiempos y libera al asesor para el siguiente turno. | **Pasa** |

---

## CP-017: Registro de Usuario Ausente
**Prioridad:** Media  
**Módulo:** CU-17: Registro de Usuario Ausente  
**Precondiciones:** Turno llamado previamente.  
**Descripción:** Marcar un turno como no atendido (Ausente).

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Clic en 'Marcar como Ausente' | Sistema guarda estado como 'Ausente' y libera al asesor. | El sistema registra la hora de cierre con estado 'ausente' y libera al asesor inmediatamente. | **Pasa** |

---

## CP-018: Recuperación de Información de Ciudadanos Frecuentes
**Prioridad:** Media  
**Módulo:** CU-18: Recuperación de Información de Ciudadanos Frecuentes  
**Precondiciones:** Existe un registro previo en la base de datos para el documento digitado.  
**Descripción:** Verificar que el sistema recupere correctamente la información histórica del ciudadano.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Cédula "1098764678" | Los campos "Nombres" y "Teléfono" aparecen automáticamente. | El sistema identifica el documento y carga exitosamente los nombres y teléfono de forma inmediata. | **Pasa** |

---

## CP-019: Monitorización de Asesores (Coordinador)
**Prioridad:** Alta  
**Módulo:** CU-19: Monitorización de Asesores (Coordinador)  
**Precondiciones:** Login de coordinador activo.  
**Descripción:** Supervisión visual de todo el personal en tiempo real.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Vista de tabla de asesores | Muestra estados en colores (Verde: Disponible, Azul: Ocupado, Gris: Espera). | El sistema visualiza correctamente los estados de todo el personal mediante indicadores de color. | **Pasa** |

---

## CP-020: Reasignación Remota de Perfiles
**Prioridad:** Alta  
**Módulo:** CU-20: Reasignación Remota de Perfiles  
**Precondiciones:** El asesor debe estar conectado.  
**Descripción:** Cambiar la cola de atención de un asesor remotamente desde el panel de coordinación.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Coordinador cambia perfil a 'Víctimas' | El asesor recibe notificación y comienza a recibir turnos del nuevo tipo asignado. | El asesor es notificado del cambio de perfil y su dashboard se actualiza automáticamente. | **Pasa** |

---

## CP-021: Llamado Directo de Turnos Empresarios
**Prioridad:** Alta  
**Módulo:** CU-21: Llamado Directo de Turnos Empresarios  
**Precondiciones:** Existencia de turnos de tipo 'Empresario'.  
**Descripción:** Atención VIP para empresarios desde cualquier mesa.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Llamado desde 'Cola Empresarial' | El sistema vincula el turno E-xxx y permite la atención inmediata. | Los asesores pueden llamar a estos usuarios de forma independiente desde su dashboard. | **Pasa** |

---

## CP-022: Prioridad por Inanición (35 min)
**Prioridad:** Alta  
**Módulo:** CU-22: Prioridad por Inanición (35 min)  
**Precondiciones:** Tiempo de espera > 35 minutos.  
**Descripción:** Elevar automáticamente la prioridad de un turno General si su espera supera el umbral establecido.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Turno G con 36 min de espera | El sistema lo asigna antes que otros turnos de menor espera. | El sistema antepone silenciosamente estos turnos para garantizar la atención oportuna. | **Pasa** |

---

## CP-023: Consulta de Reporte Semanal Consolidado
**Prioridad:** Alta  
**Módulo:** CU-23: Consulta de Reporte Semanal Consolidado  
**Precondiciones:** Acceso al módulo de Reportes.  
**Descripción:** Generar tabla resumen con rendimiento de la sede de Lunes a Sábado.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Acceso a Reportes | Se visualiza consolidado semanal con métricas de atención y espera. | El sistema muestra exitosamente el resumen por asesor y los detalles de cada atención. | **Pasa** |

---

## CP-024: Filtrado de Reporte por Funcionario
**Prioridad:** Media  
**Módulo:** CU-24: Filtrado de Reporte por Funcionario  
**Precondiciones:** CP-023 visualizado.  
**Descripción:** Aislar datos de un solo asesor para análisis individual.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Selección de asesor en filtro | El sistema actualiza promedios y filas solo con los datos del asesor elegido. | El sistema filtra correctamente al asesor y genera el reporte individual solicitado. | **Pasa** |

---

## CP-025: Filtrado de Reporte por Mesa
**Prioridad:** Media  
**Módulo:** CU-25: Filtrado de Reporte por Mesa  
**Precondiciones:** CP-023 visualizado.  
**Descripción:** Ver el rendimiento histórico y atenciones realizadas en una mesa específica.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Búsqueda por número de Mesa | El sistema filtra las atenciones realizadas en esa mesa. | El sistema permite filtrar por mesa e identificar los asesores que trabajaron en ella. | **Pasa** |

---

## CP-026: Visualización de Historial Detallado
**Prioridad:** Media  
**Módulo:** CU-26: Visualización de Historial Detallado  
**Precondiciones:** Haber seleccionado un asesor en el reporte.  
**Descripción:** Mostrar la lista cronológica completa de turnos atendidos por un asesor.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Ver detalle de asesor | Lista turnos con Código, Tiempos y Estado Final (Atendido/Ausente). | El sistema despliega una tabla cronológica detallada con todas las métricas de la semana. | **Pasa** |

---

## CP-027: Impresión de Reportes de Gestión
**Prioridad:** Media  
**Módulo:** CU-27: Impresión de Reportes de Gestión  
**Precondiciones:** Impresora configurada y reporte cargado.  
**Descripción:** Exportar o imprimir el reporte de gestión de la sede.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Comando de impresión (Ctrl+P) | Se genera un documento limpio y optimizado para impresión. | El sistema genera el documento con la información general o filtrada lista para imprimir. | **Pasa** |

---

## CP-028: Flujo completo de inicio y fin de jornada laboral
**Prioridad:** Alta  
**Módulo:** CU-28: Gestión de Jornada Laboral  
**Precondiciones:** Sesión de asesor iniciada y en estado 'Inactivo'.  
**Descripción:** Verificar que el asesor pueda iniciar y finalizar su turno, y que el tiempo se registre correctamente.

| Datos de Entrada | Resultado Esperado | Resultado Actual | Estado |
| :--- | :--- | :--- | :--- |
| Iniciar y Finalizar Turno | El estado cambia a verde (cronómetro activo) y luego a gris tras confirmar cierre. | El sistema cambia de estado exitosamente, muestra cronómetro en tiempo real y solicita confirmación de cierre. | **Pasa** |

---
