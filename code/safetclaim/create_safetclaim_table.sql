-- Script SQL para crear la tabla safetclaim
-- Ejecutar este script en phpMyAdmin o cliente MySQL

-- Usar la base de datos correcta (cambia 'aswworking' por el nombre correcto si es diferente)
-- USE aswworking;

CREATE TABLE safetclaim (
    id_safetclaim INT AUTO_INCREMENT PRIMARY KEY,
    id_sell INT NOT NULL,
    sell_order VARCHAR(255) NOT NULL,
    safet_reimbursement DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Safe-T Reimbursement (Suma)',
    shipping_reimbursement DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Shipping Reimbursement (Suma)', 
    tax_reimbursement DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Tax Reimbursement (Resta)',
    label_avoid DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Label Avoid (Suma)',
    other_fee_reimbursement DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Other Fee Reimbursement (Suma)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sell_order (sell_order),
    INDEX idx_id_sell (id_sell),
    FOREIGN KEY (id_sell) REFERENCES sell(id_sell) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Descripción de los campos:
-- 78- safet_reimbursement: Safe-T Reimbursement (Suma) (Se diligencia manual)
-- 79- shipping_reimbursement: Shipping Reimbursement (Suma) (Se diligencia manual)
-- 80- tax_reimbursement: Tax Reimbursement (Resta) (Se diligencia manual)
-- 81- label_avoid: Label Avoid (Suma)(Se diligencia manual)
-- 82- other_fee_reimbursement: Other Fee Reimbursement (Suma) (Se diligencia manual)
