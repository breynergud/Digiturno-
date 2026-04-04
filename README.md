# APE Digiturno

Sistema institucional de gestión de turnos para la **Agencia Pública de Empleo (APE)**.

## 🚀 Guía de Inicio Rápido (First-time Users)

Sigue estos pasos para poner en marcha el proyecto en tu entorno local:

### Requisitos Previos
- **PHP 8.2+**
- **Composer**
- **Node.js & NPM**
- **MySQL/MariaDB**
- **Laravel Herd** (Recomendado para Windows/Mac)

### Instalación Paso a Paso

1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd laravel_pb
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Frontend:**
   ```bash
   pnpm install  # o npm install
   ```

4. **Configurar el entorno:**
   - Copia el archivo `.env.example` a `.env`.
   - Modifica las credenciales de la base de datos en el `.env`.
   - Genera la clave de la aplicación:
     ```bash
     php artisan key:generate
     ```

5. **Migraciones y Datos iniciales:**
   ```bash
   php artisan migrate:fresh --seed
   # Ejecutar el seeder de seguridad para usuarios de prueba
   php artisan db:seed --class=SecuritySeeder
   ```

6. **Ejecutar el servidor de desarrollo:**
   ```bash
   pnpm run dev
   ```

- **Página Principal (Turnos):** `http://laravel-pb.test/digiturno`
- **Pantalla TV:** `http://laravel-pb.test/digiturno/tv`
- **Panel Asesor:** `http://laravel-pb.test/asesor/login`
- **Panel Coordinador:** `http://laravel-pb.test/coordinador/login`

## ⚙️ Funcionalidades Avanzadas de Gestión

### 1. Asignación de Mesas y Desbordamiento
- **Pre-asignación Estricta**: Cada turno se asigna automáticamente a una mesa específica en el momento de su creación.
- **Desbordamiento de Mesas (Overflow)**: El sistema permite que si un asesor queda libre y su mesa no tiene turnos pendientes, el sistema "robe" o desborde automáticamente el turno más antiguo de otras mesas hacia la suya para no detener el ritmo de atención.
- **Identificación en TV**: El ciudadano conoce su mesa desde el inicio, y la voz anuncia: *"Turno G-001, por favor diríjase a la Mesa 5"*.

### 2. Gestión de Prioridad y Inanición
- **Wait-time Priority (Inanición)**: Si un turno de tipo "General" alcanza los 35 minutos de espera, el sistema le otorga prioridad instantánea por encima de los turnos especiales para garantizar equidad.
- **Recordatorios Dinámicos**: Los asesores reciben notificaciones modales (recordatorios) al finalizar una atención si existen turnos prioritarios pendientes asignados a su mesa, invitándolos a atenderlos de inmediato.

### 3. Seguridad y Aislamiento de Pestañas
- **Tab Isolation (Sesión Inteligente)**: El sistema utiliza un ID único de pestaña (`window_id`). Esto evita que si el asesor tiene abierta la TV o el Kiosco en otra pestaña, la actividad en esas pestañas resetee su tiempo de inactividad de 15 minutos.
- **Cierre Protegido**: Si la sesión expira en el servidor, el dashboard lo detecta por AJAX y bloquea la interfaz redirigiendo al usuario de inmediato.

### 4. Panel de Asesor y Gestión de Ausencias
- **Gestión de Ausencias**: El asesor dispone de un botón dedicado para marcar usuarios que no se presentaron (**"No Asistió"**), lo cual se refleja en el campo `atnc_estado` de la base de datos para analítica.
- **Perfiles Especializados**: Soporte para el perfil de asesor **"Solo General" (GO)**, encargado exclusivamente de evacuar el volumen de la cola estándar sin distracciones de turnos preferenciales.

## ⌨️ Soporte Híbrido (Kiosco)

El formulario de registro permite el uso simultáneo de:
- **Pantalla Táctil**: Teclado virtual integrado con validación táctil.
- **Teclado Físico y Mouse**: Ideal para estaciones de prueba o administración rápida.

## 🚀 Rapidez y Eficiencia

- **Memoria de Alta Velocidad**: El sistema usa una memoria especial para mostrar los turnos en la TV casi al instante, evitando que el servidor se esfuerce innecesariamente.
- **Búsqueda Inteligente**: Optimizamos la forma en que se lee la información para que el sistema soporte a muchos asesores al mismo tiempo sin ponerse lento.

---
*Agencia Pública de Empleo - SENA Regional Distrito Capital*
