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

### 1. Asignación de Mesas Dinámica
- **Pre-asignación Estricta**: Cada turno se asigna automáticamente a una mesa específica (1-20) en el momento de su creación, basándose en la carga de trabajo de los asesores.
- **Identificación en TV**: El ciudadano conoce su mesa desde el inicio, y la voz anuncia: *"Turno G-001, por favor diríjase a la Mesa 5"*.

### 2. Seguridad y Aislamiento de Pestañas
- **Tab Isolation (Sesión Inteligente)**: El sistema utiliza un ID único de pestaña (`window_id`). Esto evita que si el asesor tiene abierta la TV o el Kiosco en otra pestaña, la actividad en esas pestañas resetee su tiempo de inactividad de 15 minutos.
- **Cierre Protegido**: Si la sesión expira en el servidor, el dashboard lo detecta por AJAX y bloquea la interfaz redirigiendo al usuario de inmediato.

### 3. Panel de Asesor Optimizado
- **Colas Separadas**: Los asesores tipo "General" tienen secciones fijas para turnos **Prioritarios** y **Generales**, facilitando la gestión visual sin interferencias.
- **Enfoque en Operación**: Se eliminaron contadores de historial innecesarios para el asesor, centralizando esa información en el panel del Coordinador.

## ⌨️ Soporte Híbrido (Kiosco)

El formulario de registro permite el uso simultáneo de:
- **Pantalla Táctil**: Teclado virtual integrado con validación táctil.
- **Teclado Físico y Mouse**: Ideal para estaciones de prueba o administración rápida.

---
*Agencia Pública de Empleo - SENA Regional Distrito Capital*
