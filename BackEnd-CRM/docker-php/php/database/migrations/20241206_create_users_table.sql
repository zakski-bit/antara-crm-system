CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee', 'customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@antara.local', '$2y$10$AgBV18LL.rR5irCrFx2d9u.mcbaWDmEEHm6C4T.1XDDwstXGXEa3G', 'admin'),
('Pegawai Redaksi', 'pegawai@antara.local', '$2y$10$E7Zbu9leC1lbemW3208VUOGtCkd/oFUB0cySj/sEMSaEQDZvqGDr.', 'employee'),
('Pelanggan Mitra', 'pelanggan@antara.local', '$2y$10$wbB2pYMOPHf1kVwmMe1zG.m.3qPxITFS.4LMrAja/pq5bEcrRQ6GG', 'customer'),
('Klien Alpha', 'alpha.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Klien Beta', 'beta.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Klien Gamma', 'gamma.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Klien Delta', 'delta.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Klien Epsilon', 'epsilon.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password = VALUES(password),
    role = VALUES(role),
    updated_at = CURRENT_TIMESTAMP;
