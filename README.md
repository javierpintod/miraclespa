# 🌿 Miracle Spa Sanctuary - Sistema de Gestión & Agendamiento de Citas

Sistema web completo y modular desarrollado en **PHP 8** y **MySQL/MariaDB**, diseñado específicamente para despliegue ágil en entornos **XAMPP** o servidores de producción (Apache / Nginx).

---

## 🌟 Características Principales

### 1. Flujo de Agendamiento para Clientes (Wizard Interactivo)
- **Selección de Servicio:** Catálogo categorizado de Uñas (manicura de lujo, esculpidas, semipermanente), Corte & Peluquería, Pintura & Balayage, Masajes y Faciales. Cada servicio define su duración en minutos y precio en USD.
- **Selección de Fecha:** Calendario dinámico con restricción de fechas pasadas y días inhábiles.
- **Selección de Horario Disponible:** Cálculo en tiempo real de turnos libres agrupados en Turno Mañana, Tarde y Noche, con respeto estricto de los horarios ocupados y buffers de higiene.
- **Selección de Profesional:** El cliente puede elegir a un especialista específico o seleccionar la opción inteligente *"Cualquier Profesional Disponible"* (auto-asignación del especialista calificado con mayor disponibilidad).
- **Confirmación Inmediata:** Bloqueo atómico contra dobles reservas con generación de código único (`MRC-YYYY-XXXXX`) y token criptográfico seguro para autogestión.

### 2. Notificaciones por Correo Electrónico
- **Confirmación Oficial:** Envío automático de correo con plantilla HTML de lujo que incluye servicio, fecha, hora, terapeuta, precio, dirección y botones interactivos para **Cancelar** o **Reprogramar**.
- **Reprogramación y Cancelación:** Notificaciones de confirmación con actualización de agenda y liberación inmediata de horarios.
- **Modos de Entrega:**
  - *Modo Simulado:* Ideal para desarrollo en XAMPP. Almacena el correo con su HTML en la base de datos y permite visualizarlo directamente en el panel administrativo.
  - *Modo SMTP:* Conexión directa a servidores de correo (Gmail, Mailtrap, Hostinger, cPanel) con encriptación TLS/SSL.
  - *Modo PHP mail():* Compatible con la función nativa de PHP.

### 3. Portal de Autogestión del Cliente (`gestionar-cita.php`)
- Los clientes pueden consultar su reserva ingresando su código o haciendo clic directo en el enlace recibido en su correo electrónico.
- Posibilidad de **Reprogramar** hacia una nueva fecha y horario disponible.
- Posibilidad de **Cancelar** registrando el motivo, validando la política de anticipación mínima (por defecto 4 horas).

### 4. Panel de Administración Completo (`/admin/`)
- **Dashboard de Ocupación & Demanda:**
  - Indicadores porcentuales de ocupación por **Día**, **Semana** y **Mes**.
  - Tarjetas de KPIs (citas de hoy, ingresos proyectados, facturación mensual, citas completadas vs canceladas).
  - Gráficos interactivos (Chart.js) de **Horas Pico del Negocio** y **Servicios con Mayor Demanda**.
  - Tabla de rendimiento individual por profesional (citas atendidas, horas en cabina, índice de ocupación e ingresos generados).
- **Calendario Global (`calendario.php`):** Vista mensual interactiva de todas las citas y cabinas, filtrable por especialista, con modales de detalle rápido y cambio de estado.
- **Gestión de Citas (`citas.php`):** Tabla con buscador, filtros avanzados, cambio de estados (`confirmada`, `completada`, `cancelada`, `reprogramada`) y creación de reservas manuales para recepción.
- **Servicios & Precios (`servicios.php`):** CRUD completo para dar de alta o modificar tratamientos, duraciones y precios.
- **Profesionales & Horarios (`profesionales.php`):** CRUD de especialistas, asignación de qué servicios puede atender cada uno y configuración de horarios semanales por día.
- **Directorio de Clientes (`clientes.php`):** Listado de clientes, visitas históricas y gasto acumulado.
- **Bandeja de Correos (`correos.php`):** Visor con iframe para inspeccionar correos generados en tiempo real.
- **Configuración del Spa (`configuracion.php`):** Ajustes de nombre, horarios de apertura/cierre, buffer de higienización, anticipación mínima y servidor SMTP con prueba en vivo.

### 5. Autenticación con JWT (JSON Web Tokens - RFC 7519)
- **Generación de Tokens HS256:** Cifrado simétrico HMAC-SHA256 con payload estándar (`iss`, `iat`, `exp`, `sub`, `username`, `role`).
- **Autenticación Híbrida:** Soporte simultáneo para sesiones web de navegador y encabezados REST `Authorization: Bearer <token>`.
- **Endpoints de la API:**
  - `POST /api/auth/login.php`: Recibe credenciales y emite el token JWT con expiración de 24h.
  - `GET /api/auth/verify.php`: Valida la firma criptográfica y retorna los datos del token vigente.

---

## 🧪 Pruebas Automatizadas

El sistema cuenta con una suite de **28 pruebas unitarias y de integración** que cubren:
1. Lógica matemática de tiempos y duraciones.
2. Detección de colisiones temporales considerando buffers de higienización.
3. Validación estricta contra dobles reservas.
4. Concurrencia de reservas simultáneas para diferentes especialistas.
5. Políticas de cancelación anticipada.
6. Liberación de cupos tras cancelación.
7. Generación y estructura de tokens JWT (HS256 de 3 segmentos).
8. Validación de claims de identidad de usuario en JWT.
9. Detección y rechazo de tokens manipulados/adulterados.
10. Rechazo estricto de tokens JWT expirados.
11. Codificación segura Base64Url sin colisiones.

### Ejecutar Pruebas:
- **En el Navegador:** Abre `http://localhost/miraclespa/tests.php`
- **En la Consola:** Ejecuta `php tests/run_tests.php`

---

## ⚙️ Instalación Rápida en XAMPP

1. Inicia **Apache** y **MySQL** en el panel de control de XAMPP.
2. Abre tu navegador en:
   👉 **`http://localhost/miraclespa/install.php`**
3. Presiona el botón de instalación con 1 clic.
4. Accede al sistema:
   - Portal de Clientes: `http://localhost/miraclespa/`
   - Panel de Administración: `http://localhost/miraclespa/admin/login.php` (Usuario: `admin` | Clave: `admin123`)

Consulta el archivo [INSTRUCCIONES_XAMPP.md](file:///c:/Users/pinto/Downloads/clase%207%20de%20octubre/miraclespa/INSTRUCCIONES_XAMPP.md) para más detalles.
