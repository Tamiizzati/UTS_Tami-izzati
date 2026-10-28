-- Jalankan SEKALI di phpMyAdmin (database db5d > tab SQL)
ALTER TABLE nilai_mhsw
  ADD COLUMN tgl_diubah TIMESTAMP NOT NULL
  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Samakan tanggal diubah data lama dengan tanggal dibuatnya
UPDATE nilai_mhsw SET tgl_diubah = reg_date;
