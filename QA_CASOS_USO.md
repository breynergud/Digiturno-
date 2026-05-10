# Casos de Uso (CU) - Sistema Digiturno APE

Este documento detalla la lógica de negocio y los flujos de interacción del sistema.

---

## CU-01: Registro e Identificación de Ciudadano
**Actor:** Ciudadano / Usuario General  
**Descripción:** Permite al ciudadano solicitar un turno de atención de manera ágil ingresando únicamente su identificación.  
**Precondiciones:** El Kiosco debe estar en la pantalla de inicio y con los servicios disponibles.

### Flujo Principal:
1. El usuario selecciona el tipo de documento.
2. El usuario ingresa el número de documento.
3. El sistema valida si el documento tiene restricciones (bloqueos).
4. El sistema registra el documento en la base de datos (registro base) y lo vincula al nuevo turno.
5. El sistema genera el código de turno y lo muestra en pantalla.

**Postcondiciones:** El turno queda en estado **'ESPERA'** vinculado al documento. Los datos personales básicos serán completados por el asesor en la etapa de atención.

**Excepciones:**
- **Persona Bloqueada:** Si el documento tiene restricciones, el sistema muestra aviso legal.
- **Documento Inválido:** Si el número no cumple con los formatos, el sistema muestra alerta de validación.

### Notas Técnicas (10/05/2026):
- Se actualizó la postcondición de 'Pendiente' a 'ESPERA' para coincidir con la interfaz de la TV.
- El sistema realiza una limpieza de caracteres especiales (puntos, comas) antes de procesar el registro.

---

## CU-02: Generación de Ticket de Turno
**Actor:** Sistema  
**Descripción:** Asigna un código alfanumérico único al usuario basado en su categoría de prioridad.  
**Precondiciones:** CU-01 finalizado exitosamente.

### Flujo Principal:
1. El sistema identifica la categoría seleccionada (General, Víctimas, Especial, Empresario).
2. Genera el prefijo (G, V, P, E) y el consecutivo numérico correspondiente al día.
3. Muestra el código resaltado en la pantalla del Kiosco.
4. Emite sonido y mensaje de éxito.

**Postcondiciones:** El usuario conoce su turno y espera el llamado en la pantalla de TV.

**Excepciones:**
- **Error de Conexión:** Si falla el servidor, muestra aviso de 'Servicio no disponible'.

---

## CU-03: Visualización de Turnos en Pantalla TV
**Actor:** Público / Ciudadano  
**Descripción:** Muestra en tiempo real los turnos que están esperando y destaca los que están siendo llamados.  
**Precondiciones:** El módulo `/tv` debe estar abierto en el navegador.

### Flujo Principal:
1. El sistema consulta la API de turnos cada 4 segundos.
2. Actualiza la lista de **'EN ESPERA'** en el panel lateral para dar tranquilidad al usuario.
3. Cuando un asesor acepta un turno, el sistema despliega un **Modal de Llamado** a pantalla completa con el número de mesa.
4. El turno permanece visible por 30 segundos y luego desaparece para mantener la pantalla limpia.

**Postcondiciones:** El ciudadano se desplaza a la mesa indicada tras ver el llamado.

**Excepciones:**
- **Pérdida de Conexión:** El sistema reintenta la conexión automáticamente sin interrumpir el video institucional.

---

## CU-04: Llamado por Voz y Sonido (TTS)
**Actor:** Sistema  
**Descripción:** Emite una alerta auditiva y dicta el turno para captar la atención de los usuarios.  
**Precondiciones:** El audio debe estar habilitado manualmente en la TV (botón de activación).

### Flujo Principal:
1. El sistema detecta un nuevo turno aceptado por un asesor.
2. Reproduce un sonido de alerta institucional.
3. Utiliza síntesis de voz (SpeechSynthesis) para anunciar: 'Turno X, por favor diríjase a la mesa Y'.
4. Repite el anuncio a los 8 segundos para mayor claridad.

**Postcondiciones:** El ciudadano escucha su llamado desde cualquier punto de la sala.

**Excepciones:**
- **Navegador Silenciado:** Si no se activó el audio, el sistema solo hará el llamado visual en pantalla.

---

## CU-05: Reproducción de Video Guía APE
**Actor:** Sistema  
**Descripción:** Reproduce un video informativo para orientar a los ciudadanos mientras esperan.  
**Precondiciones:** Conexión a internet activa en la TV (para YouTube).

### Flujo Principal:
1. El sistema carga el reproductor embebido de YouTube.
2. Reproduce el video institucional configurado en bucle infinito.
3. El video se mantiene en el panel izquierdo sin interferir con la visibilidad de los turnos.

**Postcondiciones:** El ciudadano recibe información institucional durante su espera.

**Excepciones:**
- **Video no disponible / Sin Internet:** Se muestra una imagen institucional de respaldo.

---

## CU-06: Inicio de Sesión Administrativa
**Actor:** Asesor / Coordinador  
**Descripción:** Permite el acceso restringido a los paneles de gestión mediante autenticación.  
**Precondiciones:** El usuario debe estar registrado en la base de datos.

### Flujo Principal:
1. El usuario ingresa su correo institucional.
2. El usuario ingresa su contraseña.
3. El sistema valida las credenciales y el rol asignado.
4. El sistema genera un ID de ventana único (`TAB_ID`) para aislar la sesión.
5. Redirige al dashboard correspondiente (Asesor o Coordinador).

**Postcondiciones:** El usuario accede a sus herramientas de gestión y queda registrado su acceso.

**Excepciones:**
- **Credenciales Incorrectas:** El sistema muestra aviso de error y deniega el acceso por seguridad.

---

## CU-07: Cierre de Sesión Manual
**Actor:** Asesor / Coordinador  
**Descripción:** Finaliza la jornada de trabajo y protege la integridad de los datos.  
**Precondiciones:** Haber iniciado sesión previamente.

### Flujo Principal:
1. El usuario presiona el botón rojo de 'Cerrar Sesión'.
2. El sistema valida que no haya turnos activos (o los cierra automáticamente según configuración).
3. Destruye las variables de sesión y limpia el `localStorage`.
4. Redirige al formulario de login inicial.

**Postcondiciones:** El acceso queda bloqueado y el asesor desaparece del monitor de coordinación.

**Excepciones:** N/A

---

## CU-08: Cierre de Sesión por Inactividad (Timeout)
**Actor:** Sistema  
**Descripción:** Cierra la sesión automáticamente tras un periodo de inactividad para proteger la estación de trabajo.  
**Precondiciones:** Sesión abierta sin interacción del mouse o teclado.

### Flujo Principal:
1. El sistema detecta **9 minutos** de inactividad.
2. Muestra un modal de advertencia a pantalla completa con una cuenta regresiva de **60 segundos**.
3. Si el usuario no interactúa en ese minuto, el sistema ejecuta el logout automático (Backend y Frontend).
4. El sistema redirige al login con un mensaje de "Sesión expirada".

**Postcondiciones:** Sesión finalizada por seguridad y persistencia de estados limpiada.

**Excepciones:**
- **Respuesta de Usuario:** Si el usuario presiona 'Seguir conectado' o mueve el mouse/teclado durante el aviso, el contador se reinicia a cero.

---

## CU-09: Mantenimiento de Sesión (Refresco F5)
**Actor:** Sistema / Asesor / Coordinador  
**Descripción:** Evita que el usuario pierda su sesión o estado de trabajo al recargar la página.  
**Precondiciones:** Sesión activa en el servidor.

### Flujo Principal:
1. El usuario presiona F5 o refresca el navegador.
2. El sistema recupera el `TAB_ID` del almacenamiento persistente del navegador.
3. El servidor verifica la validez del token de sesión.
4. El sistema restaura el estado del dashboard (cronómetro, turno activo, etc.) sin solicitar credenciales.

**Postcondiciones:** El flujo de trabajo continúa sin interrupciones.

**Excepciones:**
- **Sesión Expirada:** Si el tiempo de inactividad se cumplió antes del refresco, el sistema redirige al login.

---

## CU-10: Registro de Nuevo Asesor (Automatizado)
**Actor:** Coordinador / Asesor  
**Descripción:** Crea una cuenta de asesor vinculando automáticamente su perfil a la mesa asignada.  
**Precondiciones:** Mesa física disponible (rango 1 a 20).

### Flujo Principal:
1. Se ingresan los datos básicos del asesor (Nombre, Correo, Contraseña).
2. Se digita el número de mesa física.
3. **Automatización:** 
    - Mesa **16 a 19**: El sistema asigna automáticamente el perfil **'Víctimas'**.
    - Mesa **1 a 15 y 20**: El sistema asigna perfil **'General'**.
4. El sistema bloquea el campo de perfil para evitar cambios manuales inconsistentes.
5. Se guarda el registro y se habilita el acceso.

**Postcondiciones:** El nuevo asesor queda registrado y vinculado permanentemente a su rol por mesa.

**Excepciones:**
- **Mesa Duplicada:** El sistema alerta si la mesa ya está asignada a otro registro activo.

---

## CU-11: Login de Asesor con Validación de Perfil
**Actor:** Asesor  
**Descripción:** El asesor ingresa y el sistema carga automáticamente su configuración de mesa y cola de atención.  
**Precondiciones:** El asesor debe estar previamente registrado (CU-10).

### Flujo Principal:
1. El asesor inicia sesión con sus credenciales.
2. El sistema identifica el tipo de atención asignado (General o Víctimas).
3. Carga el dashboard aplicando los filtros de cola correspondientes a su perfil.
4. El sistema establece el estado inicial como **'Inactivo'**.

**Postcondiciones:** El asesor visualiza su mesa de trabajo y los turnos que le corresponden según su rol.

**Excepciones:**
- **Perfil Desactualizado:** Si el coordinador cambió el perfil mientras el asesor estaba fuera, el sistema carga la nueva configuración automáticamente.

---

## CU-12: Cambio de Estado de Disponibilidad (Asesor)
**Actor:** Asesor  
**Descripción:** Permite al asesor indicar si está listo para atender o si se encuentra en una pausa/descanso.  
**Precondiciones:** Sesión iniciada y jornada de trabajo activa (Play).

### Flujo Principal:
1. El asesor presiona el botón/badge de estado en su dashboard.
2. El sistema alterna entre **'Disponible'** (Verde) y **'En Espera'** (Gris/Amarillo).
3. El sistema actualiza el indicador visual en tiempo real tanto para el asesor como para el monitor del coordinador.
4. Si el estado es 'En Espera', el botón de 'Llamar Siguiente' queda deshabilitado.

**Postcondiciones:** El sistema solo permite la asignación automática de turnos cuando el asesor está en estado 'Disponible'.

**Excepciones:**
- **Atención Activa:** Si el asesor está en estado 'Ocupado' (atendiendo un turno), el sistema bloquea el cambio de estado de disponibilidad hasta finalizar la atención.

---

## CU-13: Llamado de Turno Siguiente (Algoritmo)
**Actor:** Asesor  
**Descripción:** El sistema selecciona el turno más adecuado para el asesor basado en la jerarquía de prioridades y el tiempo de espera.  
**Precondiciones:** Estado en **'Disponible'** y existencia de turnos en cola.

### Flujo Principal (Jerarquía de Asignación):
1. El asesor presiona el botón **'Llamar Siguiente'**.
2. El sistema evalúa la cola bajo el siguiente orden de prioridad:
    - **Regla de Inanición:** Turnos de cualquier tipo con más de **35 minutos** de espera.
    - **Perfil del Asesor:** Si el asesor es tipo 'Víctimas', se priorizan turnos tipo **'V'**.
    - **Prioridad General:** Turnos tipo **'P'** (Especial/Prioritario).
    - **Cola General:** Turnos tipo **'G'** por orden de llegada (FIFO).
3. El sistema vincula el ID del turno al asesor y cambia su estado a **'Ocupado'**.

**Postcondiciones:** El turno se anuncia en la TV y se carga en la tarjeta de 'Atención Activa' del asesor.

**Excepciones:**
- **Cola Vacía:** El sistema muestra aviso 'No hay turnos pendientes por el momento'.

---

## CU-14: Llamado Manual de Turnos Prioritarios/Víctimas
**Actor:** Asesor  
**Descripción:** Permite al asesor elegir y llamar un turno específico de las colas de alta prioridad de forma manual.  
**Precondiciones:** Existencia de turnos tipo **'V'** o **'P'** en el panel lateral del dashboard.

### Flujo Principal:
1. El asesor visualiza la lista de turnos en las secciones de **'Víctimas'** o **'Atención Especial'**.
2. Selecciona un turno específico mediante el botón **'Atender'** ubicado junto al código.
3. El sistema valida en el servidor que el turno aún esté disponible (estado 'espera').
4. Vincula el turno al asesor, dispara el llamado en la TV e inicia la atención inmediatamente.

**Postcondiciones:** El turno seleccionado pasa a estado **'OCUPADO'** y se carga en la tarjeta principal.

**Excepciones:**
- **Turno ya tomado:** Si otro asesor llamó el mismo turno simultáneamente, el sistema muestra una notificación de error y refresca la lista.

---

## CU-15: Atención y Captura de Datos de Ciudadano
**Actor:** Asesor  
**Descripción:** Proceso en el cual el asesor interactúa con el ciudadano y completa su perfil de información personal en el sistema.  
**Precondiciones:** El asesor debe tener un turno en estado de atención activa (Ocupado).

### Flujo Principal:
1. El asesor solicita nombres y teléfono al ciudadano.
2. Ingresa la información en el formulario de la tarjeta central.
3. Presiona el botón **'Guardar Datos'**.
4. El sistema valida los datos y actualiza el registro de la persona de forma persistente en la base de datos.

**Postcondiciones:** La información del ciudadano queda vinculada permanentemente a su documento de identidad.

**Excepciones:**
- **Datos Incompletos:** Si se intentan guardar campos obligatorios vacíos, el sistema resalta los errores visualmente.

---

## CU-16: Finalización de Atención
**Actor:** Asesor  
**Descripción:** Concluye formalmente el servicio al ciudadano y libera la mesa para el siguiente turno.  
**Precondiciones:** La atención debe estar activa en el dashboard.

### Flujo Principal:
1. El asesor presiona el botón **'V Finalizar'** (Color Verde).
2. El sistema registra la hora exacta de cierre en la base de datos.
3. El sistema calcula la duración total de la atención (Hora Fin - Hora Inicio).
4. El estado del asesor vuelve automáticamente a **'Disponible'** (Verde).

**Postcondiciones:** El turno desaparece de la pantalla de TV y se consolida en el historial de reportes.

**Excepciones:** N/A

---

## CU-17: Registro de Usuario Ausente
**Actor:** Asesor  
**Descripción:** Permite marcar un turno como no atendido debido a que el ciudadano no se presentó al llamado.  
**Precondiciones:** El turno debe haber sido llamado previamente y estar activo en el dashboard.

### Flujo Principal:
1. El asesor espera un tiempo prudencial según el protocolo de la sede.
2. Presiona el botón rojo **'X Marcar Ausente'** en el dashboard.
3. El sistema registra la hora de cierre con el estado específico **'ausente'**.
4. El sistema libera la mesa y pone al asesor en estado **'Disponible'**.

**Postcondiciones:** El turno se registra en las estadísticas de inasistencia y el asesor queda listo para el siguiente llamado.

**Excepciones:** N/A

---

## CU-18: Recuperación de Información de Ciudadanos Frecuentes
**Actor:** Asesor  
**Descripción:** Permite que el sistema cargue automáticamente los datos personales (Nombres y Teléfono) de un ciudadano si este ya ha sido registrado en atenciones previas.  
**Precondiciones:** El ciudadano debe haber sido atendido al menos una vez anteriormente en el sistema.

### Flujo Principal:
1. El asesor inicia la atención de un turno.
2. El sistema identifica el número de documento vinculado al turno.
3. El sistema consulta la tabla `personas` en segundo plano.
4. Si encuentra coincidencia, rellena automáticamente los campos de Nombres y Teléfono en el dashboard del asesor.

**Postcondiciones:** El asesor ahorra tiempo en la captura de datos y asegura la consistencia de la información.

**Excepciones:**
- **Primer Registro:** Si el documento no existe en la base de datos, los campos permanecen vacíos para su captura manual.

---

## CU-19: Monitorización de Asesores (Coordinador)
**Actor:** Coordinador  
**Descripción:** Supervisión visual y en tiempo real de todo el personal activo en el centro de empleo.  
**Precondiciones:** El coordinador debe haber iniciado sesión y estar en el módulo de 'Monitor de Asesores'.

### Flujo Principal:
1. El sistema consulta periódicamente el estado de todos los asesores conectados.
2. El coordinador visualiza una tabla con el nombre, mesa y estado actual (color) de cada funcionario.
3. El sistema actualiza los indicadores si un asesor pasa de 'Disponible' a 'Ocupado' o 'En Espera'.
4. Permite identificar cuellos de botella o asesores inactivos.

**Postcondiciones:** El coordinador tiene una visión global operativa de la sede.

**Excepciones:** N/A

---

## CU-20: Reasignación Remota de Perfiles
**Actor:** Coordinador  
**Descripción:** Permite al coordinador cambiar la cola de un asesor (G o V) remotamente sin que este deba cerrar sesión.  
**Precondiciones:** El asesor debe estar conectado al sistema.

### Flujo Principal:
1. El coordinador selecciona un nuevo perfil en la fila del asesor dentro del monitor.
2. El sistema envía la instrucción al servidor y actualiza la base de datos.
3. El asesor recibe una notificación automática en su dashboard.
4. El dashboard del asesor se refresca y comienza a recibir turnos del nuevo perfil inmediatamente.

**Postcondiciones:** Se optimiza el flujo de atención según la demanda del momento.

---

## CU-21: Atención de Turnos del Sector Empresarial
**Actor:** Asesor  
**Descripción:** Permite que cualquier asesor disponible atienda turnos de la categoría "Empresario" (E-xxx) de forma manual.  
**Precondiciones:** Existencia de turnos de tipo 'Empresario' en la cola.

### Flujo Principal:
1. El asesor visualiza la sección de 'Cola Empresarial' en el panel lateral de su dashboard.
2. El sistema habilita el botón 'Atender' para los turnos E-xxx.
3. El asesor inicia la atención, disparando el llamado prioritario en la TV.
4. Captura los datos y finaliza la atención siguiendo el flujo estándar.

**Postcondiciones:** El empresario es atendido con la máxima prioridad administrativa.

---

## CU-22: Prioridad por Inanición (35 min)
**Actor:** Sistema  
**Descripción:** Eleva automáticamente la prioridad de un turno general si ha superado el tiempo de espera crítico.  
**Precondiciones:** Tiempo de espera superior a 35 minutos.

### Flujo Principal:
1. El sistema chequea los tiempos de espera de la cola general (`G`) en cada petición de 'Llamar Siguiente'.
2. Si detecta un turno con más de 35 min, el sistema lo antepone a cualquier otro turno prioritario (excepto Empresarios).
3. La asignación es silenciosa: no cambia el código pero garantiza el llamado inmediato.

**Postcondiciones:** Se garantiza que ningún usuario espere indefinidamente por falta de asesores generales.

---

## CU-23: Consulta de Reporte Semanal Consolidado
**Actor:** Coordinador  
**Descripción:** Genera una tabla resumen con el rendimiento de toda la sede APE de Lunes a Sábado.  
**Precondiciones:** Acceso al módulo de Reportes.

### Flujo Principal:
1. El sistema filtra las atenciones realizadas en la semana en curso.
2. Calcula totales de turnos atendidos, ausentes y tiempos promedio.
3. Clasifica la información por día y por funcionario.
4. Muestra totales generales de productividad de la sede.

**Postcondiciones:** Datos listos para el análisis y toma de decisiones gerenciales.

---

## CU-24: Filtrado de Reporte por Funcionario
**Actor:** Coordinador  
**Descripción:** Aísla los datos de un solo asesor para evaluar su desempeño individual y tiempos de respuesta.  
**Precondiciones:** CU-23 visualizado.

### Flujo Principal:
1. El coordinador selecciona un asesor específico en el filtro de búsqueda.
2. El sistema oculta el resto de registros y recalcula los promedios solo con los datos de ese funcionario.
3. Muestra la cantidad de turnos atendidos vs ausentes del asesor elegido.

**Postcondiciones:** Vista detallada del desempeño de un funcionario específico.

---

## CU-25: Filtrado de Reporte por Mesa
**Actor:** Coordinador  
**Descripción:** Permite ver el rendimiento histórico de una ubicación física específica (mesa).  
**Precondiciones:** CU-23 visualizado.

### Flujo Principal:
1. El coordinador ingresa el número de mesa en el buscador del reporte.
2. El sistema filtra todas las atenciones realizadas desde ese punto físico.
3. Muestra qué asesores trabajaron en dicha mesa y sus métricas de tiempo.

**Postcondiciones:** Permite auditar la eficiencia de los puntos de atención físicos.

---

## CU-26: Visualización de Historial Detallado
**Actor:** Coordinador  
**Descripción:** Muestra la lista cronológica de cada turno atendido con sus horas exactas de inicio y fin.  
**Precondiciones:** Haber seleccionado un asesor en el reporte general.

### Flujo Principal:
1. El coordinador accede a la sección de 'Historial Detallado'.
2. El sistema despliega la lista con: Código de Turno, Hora Inicio, Tiempo de Espera, Duración de Atención y Estado Final.
3. Permite verificar el cumplimiento de protocolos de tiempo.

---

## CU-27: Impresión de Reportes de Gestión
**Actor:** Coordinador  
**Descripción:** Permite exportar a formato físico o PDF el reporte de gestión.  
**Precondiciones:** Impresora configurada o PDF virtual disponible.

### Flujo Principal:
1. El coordinador presiona Ctrl+P o usa el botón de impresión.
2. El sistema aplica estilos CSS específicos que optimizan el documento (oculta menús, ajusta anchos de tabla).
3. Se genera un documento limpio apto para archivo administrativo.

---

## CU-28: Gestión de Jornada Laboral
**Actor:** Asesor  
**Descripción:** Permite al asesor controlar manualmente su tiempo de atención efectivo (Turno de Trabajo).  
**Precondiciones:** Sesión iniciada.

### Flujo Principal:
1. Al ingresar, el asesor se encuentra en estado **'Inactivo'**.
2. Presiona el botón verde de **'Iniciar Turno'**.
3. El sistema marca la hora de inicio de jornada y activa el cronómetro en tiempo real.
4. Al terminar sus labores, presiona el botón rojo **'Finalizar Turno'**.
5. El sistema registra la hora de fin y calcula el tiempo total de trabajo (descontando pausas).

**Postcondiciones:** Se crea un registro de jornada laboral para el reporte de nómina/gestión.

---
