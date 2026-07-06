DROP TABLE IF EXISTS login_attempts CASCADE;
DROP TABLE IF EXISTS two_factor_codes CASCADE;
DROP TABLE IF EXISTS audit_logs CASCADE;
DROP TABLE IF EXISTS medical_records CASCADE;
DROP TABLE IF EXISTS patient_assignments CASCADE;
DROP TABLE IF EXISTS staff_verifications CASCADE;
DROP TABLE IF EXISTS license_registry CASCADE;
DROP TABLE IF EXISTS patients CASCADE;
DROP TABLE IF EXISTS users CASCADE;

DROP TYPE IF EXISTS user_role CASCADE;
DROP TYPE IF EXISTS account_status CASCADE;
DROP TYPE IF EXISTS staff_type CASCADE;
DROP TYPE IF EXISTS license_status CASCADE;
DROP TYPE IF EXISTS verification_method CASCADE;

CREATE TYPE user_role AS ENUM ('admin', 'doctor', 'nurse', 'patient');
CREATE TYPE account_status AS ENUM ('pending', 'approved', 'rejected');
CREATE TYPE staff_type AS ENUM ('doctor', 'nurse');
CREATE TYPE license_status AS ENUM ('valid', 'expired', 'revoked');
CREATE TYPE verification_method AS ENUM ('manual', 'automatic');

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    system_id VARCHAR(30) UNIQUE NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role user_role NOT NULL,
    status account_status DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patients (
    id SERIAL PRIMARY KEY,
    user_id INT UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    date_of_birth DATE,
    gender VARCHAR(20),
    phone VARCHAR(40),
    address TEXT
);

CREATE TABLE license_registry (
    id SERIAL PRIMARY KEY,
    license_number VARCHAR(100) UNIQUE NOT NULL,
    staff_type staff_type NOT NULL,
    holder_name VARCHAR(120),
    status license_status DEFAULT 'valid'
);

CREATE TABLE staff_verifications (
    id SERIAL PRIMARY KEY,
    user_id INT UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    license_number VARCHAR(100) UNIQUE NOT NULL,
    license_file VARCHAR(255),
    verification_method verification_method DEFAULT 'manual',
    verification_status account_status DEFAULT 'pending',
    admin_comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patient_assignments (
    id SERIAL PRIMARY KEY,
    patient_id INT NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    doctor_id INT REFERENCES users(id) ON DELETE SET NULL,
    nurse_id INT REFERENCES users(id) ON DELETE SET NULL,
    assigned_by INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE medical_records (
    id SERIAL PRIMARY KEY,
    patient_id INT NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    doctor_id INT REFERENCES users(id) ON DELETE SET NULL,
    nurse_id INT REFERENCES users(id) ON DELETE SET NULL,
    diagnosis TEXT,
    treatment TEXT,
    vital_signs TEXT,
    nursing_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE audit_logs (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(100),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE login_attempts (
    id SERIAL PRIMARY KEY,
    email VARCHAR(120),
    ip_address VARCHAR(100),
    attempts INT DEFAULT 1,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE two_factor_codes (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    otp_code VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_assignments_doctor ON patient_assignments(doctor_id);
CREATE INDEX idx_assignments_nurse ON patient_assignments(nurse_id);
CREATE INDEX idx_records_patient ON medical_records(patient_id);
CREATE INDEX idx_two_factor_codes_user_id ON two_factor_codes(user_id);

-- Default password is Admin@123
INSERT INTO users (system_id, full_name, email, password_hash, role, status)
VALUES (
    'ADM-2026-0001',
    'System Administrator',
    'admin@ehealth.local',
    '$2y$10$s3d5DQEPW0EcUD9cydl7o.AZky0HJkM/.DX5erXbdcO8PHfhUvGci',
    'admin',
    'approved'
);

INSERT INTO license_registry (license_number, staff_type, holder_name, status)
VALUES
('DOC-LIC-1001', 'doctor', 'Demo Doctor', 'valid'),
('NUR-LIC-2001', 'nurse', 'Demo Nurse', 'valid');
