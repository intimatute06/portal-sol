<h1 align="center">Portal Sol — Heladería</h1>

<p align="center">
  Aplicación web con <strong>Login</strong> y <strong>CRUD</strong> de helados, construida con el patrón <strong>MVC</strong> en Symfony.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Symfony-7.3-000000?logo=symfony&logoColor=white" alt="Symfony 7.3">
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white" alt="MySQL 8">
  <img src="https://img.shields.io/badge/Doctrine-ORM-FC6A31" alt="Doctrine ORM">
  <img src="https://img.shields.io/badge/estado-completado-brightgreen" alt="Estado: completado">
  <img src="https://img.shields.io/badge/licencia-MIT-blue" alt="Licencia MIT">
</p>

---

## Índice

- [Descripción del proyecto](#descripción-del-proyecto)
- [Estado del proyecto](#estado-del-proyecto)
- [Funcionalidades y demostración](#funcionalidades-y-demostración)
- [Arquitectura MVC](#arquitectura-mvc)
- [Seguridad](#seguridad)
- [Acceso al proyecto](#acceso-al-proyecto)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Tecnologías utilizadas](#tecnologías-utilizadas)
- [Persona desarrolladora](#persona-desarrolladora)
- [Licencia](#licencia)

---

## Descripción del proyecto

**Portal Sol** es una aplicación web para administrar el catálogo de una heladería. Permite que un usuario registrado inicie sesión y, dentro de una sección protegida, **cree, consulte, edite y elimine** helados (nombre, precio, stock y descripción).

El proyecto fue desarrollado para la materia de **Ingeniería Web** (UDLA) con dos objetivos:

1. Aplicar el patrón **Modelo–Vista–Controlador (MVC)** y las operaciones **CRUD**.
2. Implementar un sistema de **autenticación** en el que las URLs protegidas no sean accesibles sin iniciar sesión y las contraseñas se guarden **encriptadas**.

---

## Estado del proyecto

**Completado** — cumple todos los requisitos de la tarea *Desarrollo de la aplicación (CRUD y Login con MVC)*.

---

## Funcionalidades y demostración

### Autenticación
- **Inicio de sesión** con correo y contraseña.
- **Mensaje de error** si las credenciales son incorrectas (el correo se conserva en el formulario).
- **Cierre de sesión**.
- **Rutas protegidas**: todo lo que está bajo `/panel` exige sesión iniciada; sin ella, el sistema redirige al login.
- **Contraseñas encriptadas** con bcrypt.

### CRUD de helados

| Operación | Ruta | Descripción |
|---|---|---|
| **Crear** | `/panel/helado/new` | Formulario para registrar un helado |
| **Leer** | `/panel/helado` | Lista de todos los helados |
| **Leer** | `/panel/helado/{id}` | Detalle de un helado |
| **Actualizar** | `/panel/helado/{id}/edit` | Formulario para editar un helado |
| **Eliminar** | `/panel/helado/{id}` (POST) | Elimina un helado (con confirmación y token CSRF) |

### Video de demostración

[Ver el video de demostración](ENLACE_DEL_VIDEO)

En el video se muestra:
1. El funcionamiento del login (correcto e incorrecto).
2. Que no es posible acceder a la sección protegida sin iniciar sesión.
3. Que la contraseña se almacena encriptada en la base de datos.
4. Las operaciones del CRUD.

---

## Arquitectura MVC

| Capa | Ubicación | Componentes |
|---|---|---|
| **Modelo** | `src/Entity/`, `src/Repository/` | `Usuario`, `Helado` (entidades Doctrine) y sus repositorios |
| **Vista** | `templates/` | Plantillas Twig: `login/`, `panel/`, `helado/` |
| **Controlador** | `src/Controller/` | `LoginController`, `PanelController`, `HeladoController` |

**Flujo de una petición:**

```
Navegador → Ruta → Controlador → Modelo (Doctrine ↔ MySQL) → Vista (Twig) → HTML
```

- Las **entidades** definen las tablas; Doctrine (ORM) genera el SQL y las **migraciones** crean la estructura en MySQL.
- Los **controladores** reciben la petición, consultan el modelo y envían los datos a la vista.
- Las **vistas** Twig muestran la información y los formularios.
- El formulario del CRUD se define una sola vez en `src/Form/HeladoType.php` y se reutiliza para crear y editar.

---

## Seguridad

| Medida | Implementación |
|---|---|
| **Autenticación** | Firewall de Symfony con `form_login` (`config/packages/security.yaml`) |
| **Encriptación de contraseñas** | **bcrypt** (`password_hashers: auto`). Más seguro que md5: es lento a propósito y usa sal aleatoria |
| **Protección de URLs** | `access_control`: `^/panel` requiere `ROLE_USER` |
| **Protección CSRF** | Token en el formulario de login y en la eliminación de helados |
| **Datos sensibles** | La conexión a la base de datos va en `.env.local`, excluido de Git |

---

## Acceso al proyecto

### Requisitos
- PHP 8.2 o superior
- Composer
- Symfony CLI
- MySQL 8 (por ejemplo, con [Laragon](https://laragon.org/))

### Instalación

**1. Clonar el repositorio**

```bash
git clone https://github.com/intimatute06/portal-sol.git
cd portal-sol
```

**2. Instalar las dependencias**

```bash
composer install
```

**3. Configurar la base de datos**

Crear el archivo `.env.local` en la raíz del proyecto:

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/portal_sol?serverVersion=8.0&charset=utf8mb4"
```

Ajustar el usuario y la contraseña según la instalación local de MySQL.

**4. Crear la base de datos y las tablas**

```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

**5. Crear un usuario**

Generar el hash de la contraseña:

```bash
symfony console security:hash-password
```

Insertar el usuario en MySQL con el hash generado:

```sql
INSERT INTO usuario (email, roles, password)
VALUES ('admin@sol.com', '["ROLE_ADMIN"]', 'HASH_GENERADO');
```

**6. Levantar el servidor**

```bash
symfony serve
```

Abrir [http://localhost:8000](http://localhost:8000) e iniciar sesión con el usuario creado.

---

## Estructura del proyecto

```
portal-sol/
├── config/packages/security.yaml   # Firewall, login, logout y rutas protegidas
├── migrations/                     # Migraciones de la base de datos
├── public/css/intro.css            # Estilos
├── src/
│   ├── Controller/
│   │   ├── LoginController.php     # Login y logout
│   │   ├── PanelController.php     # Panel principal (protegido)
│   │   └── HeladoController.php    # CRUD de helados (protegido)
│   ├── Entity/
│   │   ├── Usuario.php             # Modelo de usuario
│   │   └── Helado.php              # Modelo de helado
│   ├── Form/
│   │   └── HeladoType.php          # Formulario del CRUD
│   └── Repository/                 # Consultas a la base de datos
└── templates/
    ├── login/                      # Vista del login
    ├── panel/                      # Vista del panel
    └── helado/                     # Vistas del CRUD
```

---

## Tecnologías utilizadas

- **PHP 8.3**
- **Symfony 7** — framework MVC
- **Doctrine ORM** — mapeo objeto-relacional y migraciones
- **Twig** — motor de plantillas
- **MySQL 8** — base de datos
- **Symfony Security** — autenticación, bcrypt y CSRF
- **Laragon** — entorno local en Windows
- **HTML5 y CSS3**
- **Git y GitHub** — control de versiones

---

## Persona desarrolladora

| [<img src="https://github.com/intimatute06.png" width="100px;" alt="Inti Matute"/><br><sub><b>Inti Matute</b></sub>](https://github.com/intimatute06) |
| :---: |

Estudiante de Ingeniería de Software — Universidad de Las Américas (UDLA), Quito, Ecuador.

---

## Licencia

Este proyecto está bajo la licencia **MIT**: puede usarse, copiarse y modificarse libremente, citando al autor.
