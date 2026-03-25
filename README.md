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

7. **Acceso:**
   - **Página Principal (Turnos):** `http://laravel-pb.test/digiturno`
   - **Pantalla TV:** `http://laravel-pb.test/digiturno/tv`
   - **Panel Asesor:** `http://laravel-pb.test/asesor/login`
   - **Panel Coordinador:** `http://laravel-pb.test/coordinador/login`

## 👥 Credenciales de Prueba
- **Asesor:** `asesor1@sena.gov.co` / `asesor123`
- **Coordinador:** `coordinador@sena.gov.co` / `coord123`
