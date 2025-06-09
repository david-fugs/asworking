-- Create cancellations table for managing cancellation records
CREATE TABLE cancellations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100) NOT NULL,
    refund_amount DECIMAL(10, 2) DEFAULT 0.00,
    shipping_refund DECIMAL(10, 2) DEFAULT 0.00,
    tax_refund DECIMAL(10, 2) DEFAULT 0.00,
    final_fee_refund DECIMAL(10, 2) DEFAULT 0.00,
    fixed_charge_refund DECIMAL(10, 2) DEFAULT 0.00,
    other_fee_refund DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
