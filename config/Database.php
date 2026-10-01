<?php
    /**
     * Class Database
     *
     * Bertanggung jawab mengelola siklus hidup koneksi ke basis data MySQL
     * menggunakan ekstensi PDO (PHP Data Objects).
     * Standar: Unit 4 (OOP) & Unit 6 (Basis Data)
     *
     * @property string $host     Nama host server database (localhost)
     * @property string $dbName   Nama basis data aplikasi (db_PembayaranSppSiswa)
     * @property string $username Kredensial username basis data (root)
     * @property string $password Kredensial password basis data
     * @property ?PDO   $conn     Instansiasi objek aktif PDO
     */
class Database {
    private string $host = 'localhost';
    private string $dbName = 'db_PembayaranSppSiswa';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $conn = null;
        /**
         * Membuka dan mengembalikan koneksi aktif PDO ke server MySQL
         *
         * Menerapkan konfigurasi error mode exception untuk mempermudah debugging
         * serta default fetch mode associative array.
         *
         * @return PDO Objek koneksi aktif PDO yang siap mengeksekusi query
         * @throws PDOException Ditangkap otomatis jika kredensial / database gagal diakses
         */
    public function getConnection(): ?PDO {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die('Koneksi Gagal: ' . $e->getMessage());
        }
        return $this->conn;
    }
}
