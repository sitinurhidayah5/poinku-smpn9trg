-- Database: smpn9_poin_siswa

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE, -- Admin, Guru/Wali Kelas, Siswa, Orang Tua, Kepala Sekolah
    description VARCHAR(255)
);

CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year_name VARCHAR(20) NOT NULL, -- e.g. "2025/2026"
    semester ENUM('Ganjil', 'Genap') NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade ENUM('VII', 'VIII', 'IX') NOT NULL,
    rombel VARCHAR(10) NOT NULL, -- e.g. "A", "B", "C"
    teacher_id BIGINT NULL, -- Wali Kelas
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE students (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNIQUE NULL, -- Untuk akun login siswa
    parent_user_id BIGINT UNIQUE NULL, -- Untuk akun login orang tua
    class_id INT NOT NULL,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    parent_phone VARCHAR(20) NOT NULL,
    photo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE violation_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE, -- e.g. "PLG-001"
    name VARCHAR(150) NOT NULL,
    category ENUM('Ringan', 'Sedang', 'Berat') NOT NULL,
    points INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE reward_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE, -- e.g. "PRE-001"
    name VARCHAR(150) NOT NULL,
    level ENUM('Sekolah', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional') NOT NULL,
    points INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE violation_records (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    violation_type_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    recorded_by BIGINT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    description TEXT NULL,
    proof_photo VARCHAR(255) NULL,
    status ENUM('Valid', 'Dibatalkan') DEFAULT 'Valid',
    cancel_reason TEXT NULL,
    cancelled_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (violation_type_id) REFERENCES violation_types(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (recorded_by) REFERENCES users(id),
    FOREIGN KEY (cancelled_by) REFERENCES users(id)
);

CREATE TABLE reward_records (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    reward_type_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    recorded_by BIGINT NOT NULL,
    date DATE NOT NULL,
    description TEXT NULL,
    certificate_file VARCHAR(255) NULL,
    status ENUM('Valid', 'Dibatalkan') DEFAULT 'Valid',
    cancel_reason TEXT NULL,
    cancelled_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (reward_type_id) REFERENCES reward_types(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (recorded_by) REFERENCES users(id),
    FOREIGN KEY (cancelled_by) REFERENCES users(id)
);

CREATE TABLE sanction_thresholds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    min_points INT NOT NULL,
    max_points INT NOT NULL,
    action_name VARCHAR(150) NOT NULL, -- e.g. "Panggilan Orang Tua I"
    requires_letter BOOLEAN DEFAULT FALSE,
    letter_template TEXT NULL
);

CREATE TABLE audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    action VARCHAR(50) NOT NULL, -- e.g. "CREATE", "UPDATE", "CANCEL"
    module VARCHAR(50) NOT NULL, -- e.g. "ViolationRecord"
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);