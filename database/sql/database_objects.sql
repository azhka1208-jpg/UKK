-- =====================================================================
-- SISTEMA - Database Objects (MariaDB / MySQL)
-- File ini berisi Function, Stored Procedure, dan Trigger.
-- Jalankan setelah seluruh migration selesai:
--   mysql -u root sistema_db < database/sql/database_objects.sql
-- =====================================================================

DELIMITER $$

-- ---------------------------------------------------------------------
-- FUNCTION: fn_tool_available_stock
-- Mengembalikan jumlah stok alat yang benar-benar tersedia dengan
-- menghitung total stok dikurangi jumlah yang sedang dalam transaksi
-- aktif (disetujui, dipinjam, terlambat).
-- ---------------------------------------------------------------------
DROP FUNCTION IF EXISTS fn_tool_available_stock$$
CREATE FUNCTION fn_tool_available_stock(p_tool_id BIGINT)
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_total INT DEFAULT 0;
    DECLARE v_active INT DEFAULT 0;

    SELECT COALESCE(stock_total, 0) INTO v_total
    FROM tools WHERE id = p_tool_id;

    SELECT COALESCE(SUM(bd.quantity), 0) INTO v_active
    FROM borrowing_details bd
    INNER JOIN borrowings b ON b.id = bd.borrowing_id
    WHERE bd.tool_id = p_tool_id
      AND b.status IN ('disetujui', 'dipinjam', 'terlambat');

    RETURN GREATEST(v_total - v_active, 0);
END$$

-- ---------------------------------------------------------------------
-- STORED PROCEDURE: sp_approve_borrowing
-- Menyetujui pengajuan peminjaman secara atomik:
--   1. Memastikan status masih 'menunggu'
--   2. Memastikan stok mencukupi
--   3. Mengurangi stok tersedia
--   4. Mengubah status menjadi 'disetujui'
--   5. Mencatat log aktivitas
-- Menggunakan COMMIT saat berhasil dan ROLLBACK saat gagal.
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_approve_borrowing$$
CREATE PROCEDURE sp_approve_borrowing(
    IN p_borrowing_id BIGINT,
    IN p_approver_id BIGINT,
    OUT p_result VARCHAR(255)
)
BEGIN
    DECLARE v_status VARCHAR(30) DEFAULT NULL;
    DECLARE v_short INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result = 'Gagal: terjadi kesalahan pada database.';
    END;

    START TRANSACTION;

    SELECT status INTO v_status
    FROM borrowings
    WHERE id = p_borrowing_id
    FOR UPDATE;

    IF v_status IS NULL THEN
        SET p_result = 'Gagal: data peminjaman tidak ditemukan.';
        ROLLBACK;
    ELSEIF v_status <> 'menunggu' THEN
        SET p_result = 'Gagal: peminjaman tidak berada pada status menunggu.';
        ROLLBACK;
    ELSE
        SELECT COUNT(*) INTO v_short
        FROM borrowing_details bd
        INNER JOIN tools t ON t.id = bd.tool_id
        WHERE bd.borrowing_id = p_borrowing_id
          AND t.stock_available < bd.quantity;

        IF v_short > 0 THEN
            SET p_result = 'Gagal: stok alat tidak mencukupi.';
            ROLLBACK;
        ELSE
            UPDATE tools t
            INNER JOIN borrowing_details bd ON bd.tool_id = t.id
            SET t.stock_available = t.stock_available - bd.quantity
            WHERE bd.borrowing_id = p_borrowing_id;

            UPDATE borrowings
            SET status = 'disetujui',
                approved_by = p_approver_id,
                approved_at = NOW()
            WHERE id = p_borrowing_id;

            INSERT INTO activity_logs (user_id, action, description, created_at)
            VALUES (p_approver_id, 'approve',
                    CONCAT('Menyetujui peminjaman #', p_borrowing_id), NOW());

            COMMIT;
            SET p_result = 'OK';
        END IF;
    END IF;
END$$

-- ---------------------------------------------------------------------
-- TRIGGER: trg_tools_check_stock_before_update
-- Menjaga integritas stok agar tidak pernah negatif dan tidak melebihi
-- total stok.
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_tools_check_stock_before_update$$
CREATE TRIGGER trg_tools_check_stock_before_update
BEFORE UPDATE ON tools
FOR EACH ROW
BEGIN
    IF NEW.stock_available < 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok tersedia tidak boleh bernilai negatif.';
    END IF;

    IF NEW.stock_available > NEW.stock_total THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok tersedia tidak boleh melebihi total stok.';
    END IF;
END$$

-- ---------------------------------------------------------------------
-- TRIGGER: trg_activity_logs_prevent_update
-- Log aktivitas bersifat immutable (tidak dapat diubah).
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_activity_logs_prevent_update$$
CREATE TRIGGER trg_activity_logs_prevent_update
BEFORE UPDATE ON activity_logs
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Log aktivitas tidak dapat diubah.';
END$$

-- ---------------------------------------------------------------------
-- TRIGGER: trg_activity_logs_prevent_delete
-- Log aktivitas bersifat immutable (tidak dapat dihapus).
-- ---------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_activity_logs_prevent_delete$$
CREATE TRIGGER trg_activity_logs_prevent_delete
BEFORE DELETE ON activity_logs
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Log aktivitas tidak dapat dihapus.';
END$$

DELIMITER ;