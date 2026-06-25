CREATE DATABASE IF NOT EXISTS ippek_biometric;
USE ippek_biometric;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    numero_identificacao VARCHAR(50) UNIQUE NOT NULL,
    foto VARCHAR(255) DEFAULT 'default_user.png',
    tipo ENUM('administrador', 'funcionario', 'aluno') NOT NULL,
    senha VARCHAR(255) NOT NULL,
    pin VARCHAR(8),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS biometria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    credencial_id VARCHAR(255) NOT NULL,
    chave_publica TEXT NOT NULL,
    contador INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS presencas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    entrada TIME NOT NULL,
    saida TIME,
    data DATE NOT NULL,
    status ENUM('presente', 'atrasado', 'ausente') DEFAULT 'presente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Inserir um administrador padrão (senha: admin123)
INSERT INTO usuarios (nome, numero_identificacao, tipo, senha) 
VALUES ('Administrador Geral', 'ADM001', 'administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
