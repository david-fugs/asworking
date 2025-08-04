USE asworking;

-- Tabla para guardar el resumen de ventas por sell_order
CREATE TABLE IF NOT EXISTS sell_summary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sell_order VARCHAR(255) NOT NULL,
    total_items DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    final_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fixed_charge DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    final_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    id_store INT,
    id_sucursal INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_sell_order (sell_order)
);

SELECT "Tabla sell_summary creada exitosamente" as resultado;
