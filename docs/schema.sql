-- ============================================================
-- School Control System - PostgreSQL Schema
-- ============================================================

-- Drop existing tables (for development re-runs)
DROP TABLE IF EXISTS grades CASCADE;
DROP TABLE IF EXISTS student_groups CASCADE;
DROP TABLE IF EXISTS group_subjects CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS subjects CASCADE;
DROP TABLE IF EXISTS careers CASCADE;
DROP TABLE IF EXISTS students CASCADE;
DROP TABLE IF EXISTS teachers CASCADE;
DROP TABLE IF EXISTS admins CASCADE;
DROP TABLE IF EXISTS users CASCADE;

DROP TYPE IF EXISTS user_role CASCADE;

-- ============================================================
-- Custom types (ENUMs)
-- ============================================================

CREATE TYPE user_role AS ENUM ('admin', 'teacher', 'student');

-- ============================================================
-- Table: users
-- ============================================================

CREATE TABLE users (
    id          SERIAL          PRIMARY KEY,
    name        VARCHAR(255)    NOT NULL,
    code        VARCHAR(8)      UNIQUE NOT NULL,
    last_name   VARCHAR(255)    NOT NULL,
    phone       VARCHAR(20),
    email       VARCHAR(255)    UNIQUE NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    role        user_role       NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: admins
-- ============================================================

CREATE TABLE admins (
    user_id     INTEGER         PRIMARY KEY
                                REFERENCES users(id) ON DELETE CASCADE,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: teachers
-- ============================================================

CREATE TABLE teachers (
    user_id     INTEGER         PRIMARY KEY
                                REFERENCES users(id) ON DELETE CASCADE,
    specialty   VARCHAR(150),
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: students
-- ============================================================

CREATE TABLE students (
    user_id     INTEGER         PRIMARY KEY
                                REFERENCES users(id) ON DELETE CASCADE,
    birth_date  DATE,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: careers
-- ============================================================

CREATE TABLE careers (
    id              SERIAL          PRIMARY KEY,
    name            VARCHAR(150)    NOT NULL,
    num_semesters   INTEGER         NOT NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: subjects
-- ============================================================

CREATE TABLE subjects (
    id                  SERIAL          PRIMARY KEY,
    career_id           INTEGER         NOT NULL REFERENCES careers(id),
    name                VARCHAR(150)    NOT NULL,
    suggested_semester  INTEGER         NOT NULL,
    created_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: groups
-- ============================================================

CREATE TABLE groups (
    id          SERIAL          PRIMARY KEY,
    career_id   INTEGER         NOT NULL REFERENCES careers(id),
    name        VARCHAR(20)     NOT NULL,
    semester    INTEGER         NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_group_name_per_career UNIQUE (name, semester, career_id)
);

-- ============================================================
-- Table: group_subjects
-- ============================================================

CREATE TABLE group_subjects (
    id          SERIAL      PRIMARY KEY,
    group_id    INTEGER     NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    subject_id  INTEGER     NOT NULL REFERENCES subjects(id) ON DELETE CASCADE,
    teacher_id  INTEGER     NOT NULL REFERENCES teachers(user_id),
    created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_group_subject UNIQUE (group_id, subject_id)
);

-- ============================================================
-- Table: student_groups
-- ============================================================

CREATE TABLE student_groups (
    id          SERIAL      PRIMARY KEY,
    student_id  INTEGER     NOT NULL REFERENCES students(user_id) ON DELETE CASCADE,
    group_id    INTEGER     NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_student_group UNIQUE (student_id, group_id)
);

-- ============================================================
-- Table: grades
-- ============================================================

CREATE TABLE grades (
    id                SERIAL        PRIMARY KEY,
    student_id        INTEGER       NOT NULL REFERENCES students(user_id) ON DELETE CASCADE,
    group_subject_id  INTEGER       NOT NULL REFERENCES group_subjects(id) ON DELETE CASCADE,
    grade             NUMERIC(5,2)  NOT NULL,
    registration_date TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_student_group_subject UNIQUE (student_id, group_subject_id)
);
