# 🌿 Miracle Spa Sanctuary - Guía de Instalación y Despliegue en XAMPP

Esta guía detalla paso a paso cómo desplegar y ejecutar el sistema de gestión y agendamiento de **Miracle Spa** en un entorno local con **XAMPP** (Apache, MySQL/MariaDB y PHP).

---

## 📋 Requisitos Previos

1. **XAMPP** instalado (con PHP 8.0 o superior y MySQL / MariaDB).
2. Los servicios **Apache** y **MySQL** iniciados desde el **XAMPP Control Panel**.

---

## 🚀 Paso 1: Ubicación del Proyecto en XAMPP

Para que Apache sirva la aplicación en `http://localhost/miraclespa`, el proyecto debe estar ubicado dentro de la carpeta `htdocs`:

- **Ruta de XAMPP:** `C:\xampp\htdocs\miraclespa`

*(Nota: En este equipo ya se ha configurado un enlace simbólico/junction directo entre la carpeta del proyecto y `C:\xampp\htdocs\miraclespa`, por lo que está listo para usarse inmediatamente).*

---

## 🗄️ Paso 2: Instalación de la Base de Datos

Tienes **dos opciones** sencillas:

### Opción A (Recomendada - 1 Solo Clic):
1. Abre tu navegador web e ingresa a:
   👉 **`http://localhost/miraclespa/install.php`**
2. Haz clic en el botón verde **"⚡ Instalar Base de Datos con 1 Clic"**.
3. El instalador creará automáticamente la base de datos `miraclespa_db`, todas sus tablas, los servicios de uñas, cabello, pintura y masajes, los especialistas con sus turnos laborales y el usuario administrador inicial.

### Opción B (Manual vía phpMyAdmin):
1. Abre `http://localhost/phpmyadmin/` en tu navegador.
2. Crea una nueva base de datos llamada `miraclespa_db` con cotejamiento `utf8mb4_unicode_ci`.
3. Haz clic en la pestaña **Importar**.
4. Selecciona el archivo `database/database.sql` ubicado en la raíz del proyecto y haz clic en **Continuar**.

---

## 🔑 Credenciales de Acceso al Panel de Administración

Una vez instalada la base de datos, puedes acceder al panel de control:

- **URL del Panel Admin:** 👉 **`http://localhost/miraclespa/admin/login.php`**
- **Usuario:** `admin`
- **Contraseña:** `admin123`

---

## ✉️ Paso 3: Configuración del Correo Electrónico

La aplicación cuenta con un módulo de notificaciones que despacha correos automáticos al cliente al:
- **Confirmar una cita** (incluye servicio, fecha, hora, especialista, dirección y botones directos para reprogramar o cancelar).
- **Reprogramar una cita** (notifica la nueva fecha y hora).
- **Cancelar una cita** (registra el motivo y confirma la liberación del turno).

### Modo 1: Simulado / Visor Integrado (Predeterminado para XAMPP)
- **Ventaja:** En entornos locales XAMPP, el servidor `sendmail` suele no estar configurado. El modo **simulado** genera el 100% de la plantilla de diseño de lujo y la registra en la base de datos.
- **Cómo probarlo:** En el Panel de Administración, ve a **"Bandeja de Correos"** (`admin/correos.php`). Podrás ver cada correo emitido y hacer clic en **"👁 Ver HTML"** para inspeccionar la vista exacta que recibe el cliente y probar los enlaces interactivos.

### Modo 2: Servidor SMTP Real (Gmail, Mailtrap, Hostinger, cPanel)
Si deseas que los correos se envíen por internet a buzones reales:
1. Inicia sesión en el panel admin y ve a **Configuración & SMTP** (`admin/configuracion.php`).
2. En la sección **"3. Configuración de Correo Electrónico"**:
   - **Controlador de Correo:** Cambia a `SMTP`.
   - **Servidor SMTP (Host):** Ej. `smtp.gmail.com` o `sandbox.smtp.mailtrap.io`.
   - **Puerto:** `587` (TLS) o `465` (SSL).
   - **Seguridad:** `TLS` o `SSL`.
   - **Usuario SMTP:** Tu correo o usuario de Mailtrap.
   - **Contraseña SMTP:** Tu contraseña o App Password de Google.
3. Haz clic en **Guardar Todos los Cambios**.
4. Utiliza el formulario inferior **"Probar Envío de Correo Electrónico"** para enviar un mensaje de prueba a tu email personal.

---

## 🧪 Paso 4: Ejecución de Pruebas Automatizadas

El sistema incluye una suite de pruebas exhaustiva que valida:
1. Cálculo matemático de duración y horarios de fin.
2. Detección de solapamiento de intervalos temporales con buffer de higienización de 15 minutos.
3. Bloqueo estricto contra dobles reservas para el mismo especialista.
4. Concurrencia permitida para especialistas distintos en cabinas simultáneas.
5. Políticas de cancelación (mínimo 4 horas de anticipación).
6. Liberación inmediata del horario tras una cancelación.

### Para ejecutar las pruebas en el Navegador:
👉 Ingresa a: **`http://localhost/miraclespa/tests.php`**  
Verás un reporte visual interactivo con medidores de éxito, tiempos en milisegundos y el estado individual de cada aserción.

### Para ejecutar las pruebas en la Consola (CLI):
Abre una terminal en la carpeta del proyecto y ejecuta:
```bash
php tests/run_tests.php
```

---

## 🗺️ Mapa de URLs de la Aplicación

| Módulo | URL Local | Descripción |
| :--- | :--- | :--- |
| **Portal de Reservas (Clientes)** | `http://localhost/miraclespa/` | Flujo de agendamiento en 5 pasos, catálogo y consulta de turnos |
| **Gestión de Mi Cita (Clientes)** | `http://localhost/miraclespa/gestionar-cita.php` | Permite cancelar o reprogramar citas con token de seguridad o código |
| **Dashboard de Ocupación** | `http://localhost/miraclespa/admin/index.php` | Métricas de ocupación (día, semana, mes), horas pico y demanda |
| **Calendario Global** | `http://localhost/miraclespa/admin/calendario.php` | Vista mensual interactiva de todas las citas y cabinas |
| **Gestión de Citas (CRUD)** | `http://localhost/miraclespa/admin/citas.php` | Listado, filtros, búsqueda y creación manual de reservas |
| **Servicios & Precios** | `http://localhost/miraclespa/admin/servicios.php` | CRUD de tratamientos, duración en minutos y tarifas |
| **Profesionales & Horarios** | `http://localhost/miraclespa/admin/profesionales.php` | CRUD de especialistas, servicios que atienden y jornadas laborales |
| **Directorio de Clientes** | `http://localhost/miraclespa/admin/clientes.php` | Registro de clientes, gasto acumulado y visitas |
| **Bandeja de Correos** | `http://localhost/miraclespa/admin/correos.php` | Visor de correos HTML simulados y registro de auditoría |
| **Configuración & SMTP** | `http://localhost/miraclespa/admin/configuracion.php` | Parámetros del spa, horarios comerciales y credenciales de email |
| **Ejecutor de Pruebas** | `http://localhost/miraclespa/tests.php` | Validador visual de algoritmos de disponibilidad |
| **Instalador Rápido** | `http://localhost/miraclespa/install.php` | Creación y reinicialización de la base de datos |

---

## 🛡️ Estructura y Modularidad del Código

```
miraclespa/
├── admin/                    # Panel Administrativo
│   ├── index.php             # Dashboard de Ocupación & Demanda (Chart.js)
│   ├── calendario.php        # Calendario Global interactivo
│   ├── citas.php             # CRUD de Citas & Reservas
│   ├── servicios.php         # CRUD de Servicios, Precios y Tiempos
│   ├── profesionales.php     # CRUD de Especialistas y Horarios Semanales
│   ├── clientes.php          # Historial y datos de clientes
│   ├── correos.php           # Bandeja de Correos (Simulador & Outbox)
│   ├── configuracion.php     # Configuración del Spa y credenciales SMTP
│   ├── login.php / logout.php# Autenticación y control de sesión
│   ├── layout_header.php     # Plantilla superior y barra lateral
│   └── layout_footer.php     # Plantilla inferior
├── api/                      # Endpoints REST / AJAX
│   ├── book.php              # Creación atómica de citas
│   ├── get_slots.php         # Cálculo en tiempo real de turnos libres
│   ├── get_professionals.php # Especialistas calificados y disponibles
│   ├── calendar_events.php   # Alimentación de eventos para el calendario
│   └── appointment_actions.php# Acciones de cancelación y reprogramación
├── app/                      # Lógica de Negocio y Modelos
│   ├── Helpers/              # ViewHelper, AuthHelper
│   ├── Models/               # Appointment, Service, Professional, Client, Schedule, EmailLog
│   └── Services/             # AvailabilityService, BookingService, MailerService, AnalyticsService
├── assets/                   # Recursos visuales
│   ├── css/app.css           # Estilos de lujo y tokens visuales
│   └── js/booking.js         # Lógica interactiva del Wizard del cliente
├── config/                   # Configuración y Conexión
│   ├── app.php               # Ajustes globales y base URL
│   └── database.php          # Singleton PDO para MySQL/MariaDB
├── database/                 # Scripts SQL
│   ├── database.sql          # Esquema completo + Datos semilla
│   └── schema.sql            # Esquema limpio DDL
├── tests/                    # Suite de Pruebas Automatizadas
│   ├── AvailabilityTest.php  # Pruebas unitarias de solapamiento y buffers
│   ├── BookingTest.php       # Pruebas de integración de doble reserva
│   └── run_tests.php         # Ejecutor de pruebas por consola
├── index.php                 # Página Principal & Wizard de Reserva del Cliente
├── gestionar-cita.php        # Portal de autogestión para clientes (Reprogramar/Cancelar)
├── tests.php                 # Ejecutor visual de pruebas en navegador
└── install.php               # Instalador de base de datos con 1 clic
```
