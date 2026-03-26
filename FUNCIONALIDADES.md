# Funcionalidades del Sistema APE Digiturno

Este sistema ha sido diseñado para optimizar el flujo de atención al ciudadano en las oficinas de la Agencia Pública de Empleo.

## 🛠️ Listado de Funcionalidades

### 1. Generación de Turnos (Autogestión)
- **Interfaz Tactil:** Pantalla optimizada para kioscos con teclado numérico en pantalla.
- **Categorización:** Generación de turnos específicos según el tipo de atención:
  - **General (G):** Atención estándar.
  - **Víctimas (V):** Prioridad legal.
  - **Especial (E):** Personas con discapacidad o adultos mayores.
  - **Preferencial (P):** Atención prioritaria.
  - **Empresario (EMP):** Atención exclusiva para empleadores.
- **Validación:** Registro rápido mediante número de documento y teléfono.

### 2. Monitorización (Pantalla TV)
- **Visualización en Tiempo Real:** Lista de turnos siendo atendidos y turnos en espera.
- **Notificaciones Sonoras:** Alerta auditiva ("ding") cuando se llama a un nuevo turno.
- **Identidad APE:** Diseño corporativo con marquesina informativa.

### 3. Panel del Asesor
- **Gestión de Turnos:** Llamar al siguiente turno, finalizar atención o poner en espera.
- **Aislamiento de Actividad:** Cada pestaña del dashboard se aísla mediante un ID único (`window_id`), evitando que otras pestañas del mismo navegador reseteen el contador de inactividad.
- **Seguridad Dinámica:** Detección de redirección por AJAX; si la sesión expira en el servidor, la interfaz se bloquea y redirige al usuario automáticamente.
- **Colas Personalizadas:** Visualización separada y permanente de turnos **Prioritarios** y **Generales** según la pre-asignación de la mesa.

### 4. Panel del Coordinador (Administración)
- **Asignación de Mesas:** Gestión de mesas (1-20) para cada asesor desde su registro.
- **Monitor de Estados:** Visualización en tiempo real del estado de cada asesor y su carga de trabajo.
- **Asignación de Colas:** Posibilidad de cambiar el tipo de atención que recibe cada asesor dinámicamente.
- **Reportes:** Generación de reportes semanales de rendimiento y flujo de usuarios.

## 🎨 Diseño y UX
- **Responsive Design:** Adaptable desde dispositivos móviles hasta pantallas de gran formato (40"+).
- **Estética Moderno:** Uso de Tailwind CSS con efectos de cristal (glassmorphism) y animaciones fluidas.
- **Accesibilidad:** Fuentes de alta legibilidad (Montserrat) y contrastes optimizados.
