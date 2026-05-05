# Especificaciones de Casos de Uso — Proyecto Digiturno APE

Este documento contiene las 28 especificaciones de Casos de Uso ajustadas a la lógica final del sistema.

---

### CU-01: Registro e Identificación de Ciudadano
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-01: Registro e Identificación de Ciudadano |
| **Actor** | Ciudadano / Usuario General |
| **Descripción** | Permite al ciudadano solicitar un turno de atención de manera ágil ingresando únicamente su identificación. |
| **Precondiciones** | El Kiosco debe estar en la pantalla de inicio y con los servicios disponibles. |
| **Flujo Principal** | 1. El usuario selecciona la categoría de atención.<br>2. El usuario selecciona el tipo de documento.<br>3. El usuario ingresa el número de documento.<br>4. El sistema valida el formato del documento.<br>5. El sistema registra el documento en la base de datos y lo vincula al nuevo turno. |
| **Postcondiciones** | El turno queda en estado "Pendiente" vinculado al documento del ciudadano. |
| **Excepciones** | Documento Inválido: Si el campo está vacío o contiene caracteres no numéricos, el sistema impide el avance. |

---

### CU-02: Generación de Ticket de Turno
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-02: Generación de Ticket de Turno |
| **Actor** | Sistema |
| **Descripción** | Asigna un código alfanumérico único al usuario basado en su prioridad. |
| **Precondiciones** | CU-01 finalizado exitosamente. |
| **Flujo Principal** | 1. El sistema identifica la categoría seleccionada.<br>2. Genera el prefijo (G, V, P, E) y el consecuente numérico.<br>3. Muestra el código en pantalla grande para el usuario.<br>4. Emite mensaje de éxito. |
| **Postcondiciones** | El usuario conoce su turno y espera el llamado en la TV. |
| **Excepciones** | Error de Base de Datos: Si no hay conexión, el sistema muestra aviso de "Servicio no disponible". |

---

### CU-03: Visualización de Turnos en Pantalla TV
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-03: Visualización de Turnos en Pantalla TV |
| **Actor** | Público / Ciudadano |
| **Descripción** | Muestra en tiempo real los turnos que están siendo atendidos y los que están en espera. |
| **Precondiciones** | El módulo `/tv` debe estar abierto en el navegador. |
| **Flujo Principal** | 1. El sistema consulta la base de datos cada 4 segundos.<br>2. Actualiza la lista de "Llamados Recientes" en el panel lateral.<br>3. Muestra el turno actual en el panel principal con el número de mesa. |
| **Postcondiciones** | El ciudadano se desplaza a la mesa indicada. |
| **Excepciones** | Pérdida de Conexión: La pantalla mantiene los últimos datos cargados hasta recuperar señal. |

---

### CU-04: Llamado por Voz y Sonido (TTS)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-04: Llamado por Voz y Sonido (TTS) |
| **Actor** | Sistema |
| **Descripción** | Emite una alerta auditiva y dicta el turno para usuarios con discapacidad visual o distraídos. |
| **Precondiciones** | El administrador debe haber activado el audio manualmente en la interfaz TV. |
| **Flujo Principal** | 1. El sistema detecta un cambio en el turno activo.<br>2. Reproduce un sonido de "Beep" institucional.<br>3. Utiliza síntesis de voz para decir: "Turno X, pase a la mesa Y". |
| **Postcondiciones** | El llamado es escuchado en el área de espera. |
| **Excepciones** | Navegador Silenciado: Si no se activó el botón de audio, el sistema solo hará el llamado visual. |

---

### CU-05: Reproducción de Video Guía APE
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-05: Reproducción de Video Guía APE |
| **Actor** | Sistema |
| **Descripción** | Reproduce un video informativo de YouTube para orientar a los ciudadanos. |
| **Precondiciones** | Conexión a internet activa en la TV. |
| **Flujo Principal** | 1. El sistema carga el reproductor embebido de YouTube.<br>2. Reproduce el video institucional en bucle infinito.<br>3. El video se mantiene en el lateral izquierdo sin interferir con los turnos. |
| **Postcondiciones** | El público recibe información mientras espera. |
| **Excepciones** | Video no disponible: Se muestra una imagen institucional de respaldo. |

---

### CU-06: Inicio de Sesión Administrativa
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-06: Inicio de Sesión Administrativa |
| **Actor** | Asesor / Coordinador |
| **Descripción** | Permite el acceso restringido a los paneles de gestión. |
| **Precondiciones** | El usuario debe estar registrado en la base de datos. |
| **Flujo Principal** | 1. El usuario ingresa su correo institucional.<br>2. El usuario ingresa su contraseña.<br>3. El sistema valida las credenciales.<br>4. El sistema crea la sesión y redirige al dashboard correspondiente. |
| **Postcondiciones** | El usuario accede a sus herramientas de trabajo. |
| **Excepciones** | Credenciales Incorrectas: El sistema muestra aviso de error y deniega el acceso. |

---

### CU-07: Cierre de Sesión Manual
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-07: Cierre de Sesión Manual |
| **Actor** | Asesor / Coordinador |
| **Descripción** | Finaliza la jornada de trabajo y protege los datos del panel. |
| **Precondiciones** | Haber iniciado sesión previamente. |
| **Flujo Principal** | 1. El usuario presiona el botón rojo de "Cerrar Sesión".<br>2. El sistema destruye las variables de sesión.<br>3. El sistema redirige al formulario de login. |
| **Postcondiciones** | El acceso queda bloqueado hasta un nuevo login. |
| **Excepciones** | N/A |

---

### CU-08: Cierre de Sesión por Inactividad (Timeout)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-08: Cierre de Sesión por Inactividad (Timeout) |
| **Actor** | Sistema |
| **Descripción** | Cierra la sesión automáticamente tras 15 minutos de inactividad para proteger la estación de trabajo. |
| **Precondiciones** | Sesión abierta sin interacción del mouse o teclado. |
| **Flujo Principal** | 1. El sistema detecta 14 min de inactividad.<br>2. Muestra modal de advertencia con cuenta regresiva de 60 seg.<br>3. Si no hay respuesta, ejecuta el logout automático. |
| **Postcondiciones** | Sesión finalizada por seguridad. |
| **Excepciones** | Respuesta de Usuario: Si el usuario presiona "Seguir conectado", el tiempo se reinicia. |

---

### CU-09: Mantenimiento de Sesión (Refresco F5)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-09: Mantenimiento de Sesión (Refresco F5) |
| **Actor** | Sistema / Asesor |
| **Descripción** | Evita que el usuario pierda su sesión al recargar la página accidentalmente. |
| **Precondiciones** | Sesión activa en el servidor. |
| **Flujo Principal** | 1. El usuario presiona F5 o refresca el navegador.<br>2. El sistema verifica el token de sesión persistente.<br>3. Mantiene al usuario en su dashboard sin pedir credenciales. |
| **Postcondiciones** | El flujo de trabajo no se interrumpe por refrescos de página. |
| **Excepciones** | Sesión Expirada: Si el tiempo de vida terminó, el refresco llevará al login. |

---

### CU-10: Registro de Nuevo Asesor (Automatizado)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-10: Registro de Nuevo Asesor (Automatizado) |
| **Actor** | Coordinador / Asesor |
| **Descripción** | Crea una cuenta de asesor vinculando automáticamente su perfil a la mesa. |
| **Precondiciones** | Mesa física disponible (1 a 20). |
| **Flujo Principal** | 1. Se ingresan datos básicos del asesor.<br>2. Se digita el número de mesa.<br>3. **Automatización**: Mesa 16-19 asigna perfil "Víctimas". Mesa 1-15 y 20 asigna "General".<br>4. El sistema bloquea la edición del perfil para el asesor.<br>5. Se guarda el registro. |
| **Postcondiciones** | Nuevo asesor listo para operar en su mesa asignada. |
| **Excepciones** | Mesa Duplicada: El sistema alerta si la mesa ya está siendo usada por otro asesor activo. |

---

### CU-11: Login de Asesor con Validación de Perfil
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-11: Login de Asesor con Validación de Perfil |
| **Actor** | Asesor |
| **Descripción** | El asesor ingresa y el sistema carga su configuración de mesa y cola. |
| **Precondiciones** | CU-10 completado. |
| **Flujo Principal** | 1. El asesor inicia sesión.<br>2. El sistema identifica su tipo (G o V).<br>3. Carga el dashboard con los colores y colas correspondientes a su perfil. |
| **Postcondiciones** | El asesor visualiza solo los turnos que le corresponden. |
| **Excepciones** | Perfil no asignado: El sistema redirige a contacto con el coordinador. |

---

### CU-12: Cambio de Estado de Disponibilidad (Asesor)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-12: Cambio de Estado de Disponibilidad (Asesor) |
| **Actor** | Asesor |
| **Descripción** | Permite al asesor indicar si está listo para atender o si está en descanso/pausa. |
| **Precondiciones** | Sesión iniciada. |
| **Flujo Principal** | 1. El asesor presiona el botón de estado.<br>2. El sistema cambia entre "Disponible" y "En Espera".<br>3. Actualiza el badge visual en el panel del asesor y del coordinador. |
| **Postcondiciones** | El sistema solo enviará turnos si el estado es "Disponible". |
| **Excepciones** | Ocupado: Si está atendiendo a alguien, el sistema bloquea el cambio de estado. |

---

### CU-13: Llamado de Turno Siguiente (Algoritmo)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-13: Llamado de Turno Siguiente (Algoritmo) |
| **Actor** | Asesor |
| **Descripción** | El sistema selecciona el mejor turno para el asesor basado en prioridad y mesa. |
| **Precondiciones** | Estado en "Disponible" y turnos en cola. |
| **Flujo Principal** | 1. El asesor presiona "Llamar Siguiente".<br>2. El sistema busca turnos asignados a su mesa específica.<br>3. Si no hay, busca por desbordamiento en otras mesas de su mismo tipo.<br>4. Vincula el turno y lo muestra en pantalla. |
| **Postcondiciones** | El turno se anuncia en la TV y se carga en el dashboard. |
| **Excepciones** | Cola Vacía: El sistema muestra aviso "No hay turnos pendientes". |

---

### CU-14: Llamado Manual de Turnos Prioritarios/Víctimas
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-14: Llamado Manual de Turnos Prioritarios/Víctimas |
| **Actor** | Asesor |
| **Descripción** | Permite al asesor elegir un turno específico de las colas de alta prioridad. |
| **Precondiciones** | Existencia de turnos V o P en el panel lateral. |
| **Flujo Principal** | 1. El asesor visualiza la lista de prioritarios.<br>2. Selecciona un turno específico mediante el botón de llamado directo.<br>3. El sistema valida que el turno aún no haya sido tomado.<br>4. Inicia la atención de ese código. |
| **Postcondiciones** | El turno seleccionado pasa a estado atendiendo. |
| **Excepciones** | Turno ya tomado: Si otro asesor lo llamó milisegundos antes, el sistema alerta y cancela la acción. |

---

### CU-15: Atención y Captura de Datos de Ciudadano
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-15: Atención y Captura de Datos de Ciudadano |
| **Actor** | Asesor |
| **Descripción** | Proceso donde el asesor interactúa con el ciudadano y completa su información personal. |
| **Precondiciones** | Turno activo en mesa. |
| **Flujo Principal** | 1. El asesor solicita nombres, apellidos y teléfono.<br>2. Digita la información en los campos habilitados.<br>3. Presiona "Guardar Datos".<br>4. El sistema actualiza el registro de la persona de forma persistente. |
| **Postcondiciones** | La base de datos de ciudadanos queda actualizada. |
| **Excepciones** | Error de Validación: Si faltan campos obligatorios, el sistema resalta el error en rojo. |

---

### CU-16: Finalización de Atención
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-16: Finalización de Atención |
| **Actor** | Asesor |
| **Descripción** | Concluye el servicio al ciudadano y libera la mesa. |
| **Precondiciones** | Datos de atención capturados. |
| **Flujo Principal** | 1. El asesor presiona "Finalizar Atención".<br>2. El sistema guarda la hora de cierre.<br>3. Calcula la duración de la entrevista.<br>4. El asesor queda en estado "Disponible". |
| **Postcondiciones** | El turno desaparece de la TV y pasa al historial de atenciones finalizadas. |
| **Excepciones** | N/A |

---

### CU-17: Registro de Usuario Ausente
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-17: Registro de Usuario Ausente |
| **Actor** | Asesor |
| **Descripción** | Marca un turno como no atendido por incomparecencia del usuario. |
| **Precondiciones** | Turno llamado previamente. |
| **Flujo Principal** | 1. El asesor espera el tiempo prudencial.<br>2. Presiona la opción "Marcar como Ausente" en el selector de finalización.<br>3. El sistema guarda el estado como "Ausente".<br>4. El asesor queda disponible. |
| **Postcondiciones** | El turno cuenta en estadísticas pero no como atención efectiva. |
| **Excepciones** | N/A |

---

### CU-18: Consulta de Historial de Atenciones del Día (Asesor)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-18: Consulta de Historial de Atenciones del Día (Asesor) |
| **Actor** | Asesor |
| **Descripción** | Permite al asesor visualizar de forma inmediata la lista de todos los turnos que ha procesado durante su jornada actual. |
| **Precondiciones** | El asesor debe tener una sesión activa. |
| **Flujo Principal** | 1. El asesor se desplaza a la sección inferior de su dashboard.<br>2. El sistema consulta la base de datos filtrando las atenciones del asesor con la fecha de hoy.<br>3. Se muestra una tabla cronológica con el código del turno, la hora de inicio y la hora de finalización. |
| **Postcondiciones** | El asesor tiene visibilidad y control sobre su productividad diaria sin salir de su panel. |
| **Excepciones** | Sin atenciones: Si es el inicio de la jornada, el sistema muestra el mensaje "No hay atenciones registradas hoy". |

---

### CU-19: Monitorización de Asesores (Coordinador)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-19: Monitorización de Asesores (Coordinador) |
| **Actor** | Coordinador |
| **Descripción** | Supervisión visual de todo el personal activo en el centro de empleo. |
| **Precondiciones** | Login de coordinador activo. |
| **Flujo Principal** | 1. El coordinador observa la tabla de asesores.<br>2. El sistema muestra estados en colores (Verde: Disponible, Azul: Ocupado, Gris: Espera).<br>3. Muestra qué turno está atendiendo cada uno y en qué mesa. |
| **Postcondiciones** | El coordinador tiene panorama total de la operación. |
| **Excepciones** | N/A |

---

### CU-20: Reasignación Remota de Perfiles
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-20: Reasignación Remota de Perfiles |
| **Actor** | Coordinador |
| **Descripción** | Permite al coordinador cambiar la cola de un asesor sin que este se mueva de su sitio. |
| **Precondiciones** | El asesor debe estar conectado. |
| **Flujo Principal** | 1. El coordinador selecciona un nuevo perfil (G, V o E) en la fila del asesor.<br>2. El sistema envía la instrucción al servidor.<br>3. El asesor comienza a recibir turnos del nuevo perfil inmediatamente. |
| **Postcondiciones** | Se optimiza el flujo de atención según la demanda. |
| **Excepciones** | Error de conexión: El sistema notifica si el cambio no se guardó. |

---

### CU-21: Atención de Turnos del Sector Empresarial
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-21: Atención de Turnos del Sector Empresarial |
| **Actor** | Asesor |
| **Descripción** | Permite que cualquier asesor disponible atienda turnos de la categoría "Empresario" (E-xxx). |
| **Precondiciones** | Existencia de turnos tipo E-xxx en cola. |
| **Flujo Principal** | 1. El asesor visualiza la cola empresarial en su dashboard.<br>2. El sistema permite aceptar el turno E-xxx específico.<br>3. El asesor inicia la atención y captura los datos del empresario. |
| **Postcondiciones** | El turno empresarial es procesado por un asesor. |
| **Excepciones** | N/A |

---

### CU-22: Prioridad por Inanición (35 min)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-22: Prioridad por Inanición (35 min) |
| **Actor** | Sistema |
| **Descripción** | Eleva la prioridad de un turno general si ha esperado demasiado tiempo. |
| **Precondiciones** | Tiempo de espera > 35 minutos. |
| **Flujo Principal** | 1. El sistema chequea los tiempos de espera de la cola general.<br>2. Si detecta un turno con +35 min, lo marca como "Urgente".<br>3. Este turno aparecerá por encima de los nuevos turnos prioritarios en el siguiente llamado automático. |
| **Postcondiciones** | Se garantiza que ningún usuario espere indefinidamente. |
| **Excepciones** | N/A |

---

### CU-23: Consulta de Reporte Semanal Consolidado
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-23: Consulta de Reporte Semanal Consolidado |
| **Actor** | Coordinador |
| **Descripción** | Genera una tabla resumen con el rendimiento de toda la sede APE. |
| **Precondiciones** | Acceso al módulo de Reportes. |
| **Flujo Principal** | 1. El sistema filtra atenciones del Lunes al Sábado de la semana en curso.<br>2. Calcula totales por día y por asesor.<br>3. Muestra tiempos promedio de espera y atención.<br>4. Muestra totales generales de la sede. |
| **Postcondiciones** | Datos listos para análisis gerencial. |
| **Excepciones** | N/A |

---

### CU-24: Filtrado de Reporte por Funcionario
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-24: Filtrado de Reporte por Funcionario |
| **Actor** | Coordinador |
| **Descripción** | Aísla los datos de un solo asesor para evaluar su desempeño individual. |
| **Precondiciones** | CU-23 visualizado. |
| **Flujo Principal** | 1. El coordinador elige un asesor en el buscador o dropdown.<br>2. El sistema oculta el resto de filas.<br>3. Actualiza los promedios solo con los datos de ese asesor. |
| **Postcondiciones** | Vista detallada del funcionario seleccionada. |
| **Excepciones** | Asesor sin atenciones: Muestra la tabla vacía con mensaje informativo. |

---

### CU-25: Filtrado de Reporte por Mesa
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-25: Filtrado de Reporte por Mesa |
| **Actor** | Coordinador |
| **Descripción** | Permite ver el rendimiento histórico de una ubicación física específica (mesa). |
| **Precondiciones** | CU-23 visualizado. |
| **Flujo Principal** | 1. El coordinador ingresa el número de mesa en el buscador.<br>2. El sistema filtra las atenciones realizadas en esa mesa.<br>3. Muestra qué asesores trabajaron en ella y sus métricas. |
| **Postcondiciones** | Análisis de eficiencia por punto de atención físico. |
| **Excepciones** | N/A |

---

### CU-26: Visualización de Historial Detallado
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-26: Visualización de Historial Detallado |
| **Actor** | Coordinador |
| **Descripción** | Muestra la lista cronológica de cada turno atendido con sus horas exactas. |
| **Precondiciones** | Haber seleccionado un asesor en el reporte. |
| **Flujo Principal** | 1. El coordinador baja a la sección de "Historial Detallado".<br>2. Observa Código, Hora Inicio, Tiempo de Espera y Tiempo de Atención de cada turno.<br>3. Verifica el estado final (Atendido/Ausente). |
| **Postcondiciones** | Auditoría completa de la jornada del asesor. |
| **Excepciones** | N/A |

---

### CU-27: Impresión de Reportes de Gestión
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-27: Impresión de Reportes de Gestión |
| **Actor** | Coordinador |
| **Descripción** | Permite exportar a físico el reporte de atenciones. |
| **Precondiciones** | Impresora conectada y reporte cargado. |
| **Flujo Principal** | 1. El coordinador presiona Ctrl+P o el icono de imprimir.<br>2. El sistema aplica una hoja de estilos (CSS Print) que oculta menús y botones innecesarios.<br>3. Se genera un documento limpio con las tablas de datos. |
| **Postcondiciones** | Documento físico de reporte obtenido. |
| **Excepciones** | N/A |

---

### CU-28: Notificación Sonora y Visual de Llamado (Módulo TV)
| Campo | Detalle |
| :--- | :--- |
| **ID / Nombre** | CU-28: Notificación Sonora y Visual de Llamado |
| **Actor** | Sistema / Usuario |
| **Descripción** | Emite una alerta auditiva y resalta visualmente el turno llamado en la pantalla principal (TV). |
| **Precondiciones** | El Módulo TV debe estar abierto en una pantalla visible al público. |
| **Flujo Principal** | 1. El asesor presiona "Aceptar Turno".<br>2. El sistema actualiza el estado del turno.<br>3. La pantalla de TV detecta el nuevo llamado mediante polling.<br>4. La TV reproduce un sonido de notificación ("Ding") y muestra el número de turno y mesa en el área de destacados. |
| **Postcondiciones** | El ciudadano es alertado para dirigirse a la mesa asignada. |
| **Excepciones** | N/A |
