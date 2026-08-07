# Sistema de Control Escolar

## 1. Introducción

Sistema web para la gestión académica de una universidad. Desarrollado con Laravel.

---

## 2. Roles de Usuario

| Rol | Descripción |
|---|---|
| **Administrativo** | Personal de administración escolar. Gestiona usuarios, carreras, materias, grupos e inscripciones. |
| **Docente** | Profesor. Califica a los alumnos de las materias que imparte. |
| **Alumno** | Estudiante. Consulta sus materias y calificaciones. |

---

## 3. Módulo de Autenticación

### 3.1 Registro de usuarios
- Solo el rol **Administrativo** puede crear cuentas de cualquier tipo.
- No existe auto-registro público.
- Al crear un usuario, se asigna su rol (admin, docente o alumno) y se crea su perfil correspondiente.
- Al registrar un usuario, se genera una matrícula única, la cual es una combinación de letras mayúsculas y números con una longitud de 8 carácters
- La contraseña inicial de los usuarios es la misma matrícula y es generada automáticamente

### 3.2 Inicio de sesión
- Login con **email** y **contraseña**.
- La autenticación se maneja con **sessions** y **cookies**
- Las contraseñas se almacenan hasheadas.

### 3.3 Control de acceso
- Cada endpoint valida que le usuario esté autenticado y tenga el rol requerido.

---

## 4. Módulo de Gestión de Carreras

### 4.1 Entidad: Carrera

| Campo | Tipo | Descripción |
|---|---|---|
| id | INT (PK) | Identificador único |
| clave | VARCHAR(20) | Clave única de la carrera (ej: `IIS`, `IN`, `IA`) |
| nombre | VARCHAR(150) | Nombre completo (ej: `Ingeniería en Sistemas Computacionales`) |
| num_semestres | INT | Duración en semestres (ej: 9) |

### 4.2 Reglas de negocio
- CRUD completo solo para **Administrativo**.
- Docentes y Alumnos solo pueden consultar carreras.

---

## 5. Módulo de Gestión de Materias

### 5.1 Entidad: Materia

| Campo | Tipo | Descripción |
|---|---|---|
| id | INT (PK) | Identificador único |
| codigo | VARCHAR(20) | Código único de la materia (ej: `SC-101`) |
| nombre | VARCHAR(150) | Nombre de la materia (ej: `Cálculo I`) |
| creditos | INT | Valor en créditos |
| semestre_sugerido | INT | Semestre en el que se cursa típicamente (1 a N) |
| carrera_id | INT (FK) | Carrera a la que pertenece |

### 5.2 Reglas de negocio
- CRUD completo solo para **Administrativo**.
- Docentes y Alumnos solo pueden consultar materias.
- Una materia pertenece a **una sola carrera**.
- No se implementan prerrequisitos.

---

## 6. Módulo de Gestión de Grupos

### 6.1 Entidad: Grupo

| Campo | Tipo | Descripción |
|---|---|---|
| id | INT (PK) | Identificador único |
| nombre | VARCHAR(20) | Nombre del grupo (ej: `1A`, `3B`) |
| semestre | INT | Semestre al que pertenece el grupo |
| carrera_id | INT (FK) | Carrera a la que pertenece |
| turno | VARCHAR(20) | Turno (Matutino / Vespertino) |

### 6.2 Entidad: GrupoMateria (asignación docente-materia-grupo)

| Campo | Tipo | Descripción |
|---|---|---|
| id | INT (PK) | Identificador único |
| grupo_id | INT (FK) | Grupo |
| materia_id | INT (FK) | Materia |
| docente_id | INT (FK) | Docente asignado |

### 6.3 Reglas de negocio
- CRUD de grupos solo para **Administrativo**.
- El Administrativo asigna qué **docente** imparte qué **materia** en qué **grupo**.
- Un docente puede tener múltiples GrupoMateria asignados.
- Docentes consultan solo sus grupos/materias asignadas.

---

## 7. Módulo de Inscripciones

### 7.1 Entidad: AlumnoGrupo

| Campo | Tipo | Descripción |
|---|---|---|
| id | INT (PK) | Identificador único |
| alumno_id | INT (FK) | Alumno |
| grupo_id | INT (FK) | Grupo al que se inscribe |
| fecha_inscripcion | DATETIME | Fecha de inscripción |

### 7.2 Reglas de negocio
- Solo el **Administrativo** puede inscribir alumnos.
- Un alumno se inscribe a un **grupo completo** (no a materias sueltas).
- Al inscribir a un grupo, el alumno cursa automáticamente todas las materias asignadas a ese grupo.

---

## 8. Módulo de Calificaciones

### 8.1 Entidad: Calificación

| Campo | Tipo | Descripción |
|---|---|---|
| id | INT (PK) | Identificador único |
| alumno_id | INT (FK) | Alumno |
| grupo_materia_id | INT (FK) | GrupoMateria |
| calificacion | DECIMAL(5,2) | Calificación (0.00 a 100.00) |
| fecha_registro | DATETIME | Fecha de registro |

### 8.2 Reglas de negocio
- Solo el **Docente** puede registrar/editar calificaciones de las materias que imparte.
- El **Administrativo** puede ver y editar cualquier calificación.
- El **Alumno** solo puede **consultar** sus propias calificaciones.
- Escala: 0 a 100. Mínimo aprobatorio: **60**.
- Se registra una calificación final por materia.

---

## 9. Reportes

### 9.1 Kardex del alumno
- Historial completo de materias cursadas y calificaciones obtenidas.
- Disponible para: Alumno (suyo), Administrativo (de cualquiera), Docente (de sus alumnos).

### 9.2 Boleta de calificaciones
- Calificaciones de un semestre específico.
- Disponible para los mismos roles que el kardex.

---

## 10. Matriz de permisos por rol

| Entidad / Operación | Administrativo | Docente | Alumno |
|---|---|---|---|
| **Usuarios** | CRUD | — | — |
| **Carreras** | CRUD | Consulta | Consulta |
| **Materias** | CRUD | Consulta | Consulta |
| **Grupos** | CRUD | Consulta (suyos) | Consulta (suyo) |
| **GrupoMateria** | CRUD | Consulta (suyas) | Consulta (sus materias) |
| **Inscripciones** | CRUD | — | — |
| **Calificaciones** | CRUD | Crear/Editar (suyas) | Consulta (propias) |
| **Reportes** | Todos | De sus alumnos | Propios |

---

## 11. Modelo de datos (DER textual)

```
USUARIO (id, nombre, apellido, matricula, telefono, email, password_hash, rol, activo)
  │
  ├── ADMINISTRATIVO (usuario_id PK/FK)
  │
  ├── DOCENTE (usuario_id PK/FK, especialidad)
  │
  └── ALUMNO (usuario_id PK/FK, fecha_nacimiento)

CARRERA (id, clave, nombre, num_semestres)
  │
  └── MATERIA (id, codigo, nombre, creditos, semestre_sugerido, carrera_id FK)

GRUPO (id, nombre, semestre, carrera_id FK, turno)
  │
  └── GRUPO_MATERIA (id, grupo_id FK, materia_id FK, docente_id FK)

ALUMNO_GRUPO (id, alumno_id FK, grupo_id FK, fecha_inscripcion)

CALIFICACION (id, alumno_id FK, grupo_materia_id FK, calificacion, fecha_registro)
```
