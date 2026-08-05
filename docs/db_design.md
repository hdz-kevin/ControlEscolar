# Diseño Lógico de la Base de Datos


## Diagrama gráfico
https://drawsql.app/draw?t=d7200c96-0b78-4c36-8ff4-6d14227ed82a&view=1

---


## Convenciones

- Todos los IDs son `INT AUTO_INCREMENT`.
- Todas las tablas tienen `created_at` y `updated_at` y como `DATETIME`.
- Todas las tablas y columnas están nombradas en inglés, y siguen la nomenclatura de snake_case

---

## Tabla: `users`

Tabla base para autenticación. Los usuarios `admin`, `teacher` y `student` extienden esta tabla con tablas separadas (herencia 1:1).

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `first_name` | VARCHAR(100) | NOT NULL | Nombre(s) |
| `last_name` | VARCHAR(100) | NOT NULL | Apellido(s) |
| `phone` | VARCHAR(20) | NULLABLE | Teléfono de contacto |
| `email` | VARCHAR(150) | UNIQUE, NOT NULL | Correo electrónico |
| `password` | VARCHAR(255) | NOT NULL | Contraseña hasheada |
| `role` | ENUM('admin','teacher','student') | NOT NULL | Rol del usuario |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Fecha de creación |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- 1:1 con `admins` (si role = 'admin')
- 1:1 con `teachers` (si role = 'teacher')
- 1:1 con `students` (si role = 'student')

---

## Tabla: `admins`

Usuarios administrativos.

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `user_id` | INT | PK, FK → users(id) ON DELETE CASCADE | Referencia al usuario |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

---

## Tabla: `teachers`

Usuarios docentes.

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `user_id` | INT | PK, FK → users(id) ON DELETE CASCADE | Referencia al usuario |
| `specialty` | VARCHAR(150) | NULLABLE | Área de especialización |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

---

## Tabla: `students`

Usuarios alumnos.

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `user_id` | INT | PK, FK → users(id) ON DELETE CASCADE | Referencia al usuario |
| `code` | VARCHAR(20) | UNIQUE, NOT NULL | Matrícula institucional |
| `birth_date` | DATE | NULLABLE | Fecha de nacimiento |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

---

## Tabla: `careers`

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(150) | NOT NULL | Nombre completo |
| `num_semesters` | INT | NOT NULL | Duración en semestres |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

---

## Tabla: `subjects`

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `career_id` | INT | FK → careers(id) | Carrera a la que pertenece |
| `name` | VARCHAR(150) | NOT NULL | Nombre de la materia |
| `suggested_semester` | INT | NOT NULL | Semestre en que se cursa |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

**Relaciones:**
- Muchos a 1 con `careers`

---

## Tabla: `groups`

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `career_id` | INT | FK → careers(id) | Carrera a la que pertenece |
| `name` | VARCHAR(20) | NOT NULL | Nombre (ej: `1A`, `3B`) |
| `semester` | INT | NOT NULL | Semestre del grupo |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

**Índice único:** `(name, semester, career_id)` — no pueden existir dos grupos con el mismo nombre y semestre en la misma carrera.

---

## Tabla: `group_subjects`

Tabla pivote que asigna un **docente** a una **materia** dentro de un **grupo**.

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `group_id` | INT | FK → groups(id) ON DELETE CASCADE | Grupo |
| `subject_id` | INT | FK → subjects(id) ON DELETE CASCADE | Materia |
| `teacher_id` | INT | FK → teachers(user_id) | Docente que imparte |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

**Índice único:** `(group_id, subject_id)` — no se puede asignar la misma materia dos veces al mismo grupo.

---

## Tabla: `student_groups`

Inscripción de un alumno a un grupo.

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `student_id` | INT | FK → students(user_id) ON DELETE CASCADE | Alumno |
| `group_id` | INT | FK → groups(id) ON DELETE CASCADE | Grupo |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

**Índice único:** `(student_id, group_id)` — un alumno no puede inscribirse dos veces al mismo grupo.

---

## Tabla: `grades`

| Columna | Tipo | Restricciones | Descripción |
|---|---|---|---|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `student_id` | INT | FK → students(user_id) ON DELETE CASCADE | Alumno calificado |
| `group_subject_id` | INT | FK → group_subjects(id) ON DELETE CASCADE | Materia cursada |
| `grade` | DECIMAL(5,2) | NOT NULL | Nota final |
| `registration_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Cuándo se registró |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | |
| `updated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | |

**Índice único:** `(student_id, group_subject_id)` — un alumno tiene una sola calificación por materia en un grupo.
