-- Comandos SQL utilizados

CREATE SCHEMA Users;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    saldo DECIMAL(10, 2) DEFAULT 0
);

CREATE TABLE transferencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pagador INT NOT NULL,
    id_recebedor INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    status VARCHAR(100),
    data_tranferencia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pagador) REFERENCES usuarios(id),
    FOREIGN KEY (id_recebedor) REFERENCES usuarios(id)
);
