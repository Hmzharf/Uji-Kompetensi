<?php
/**
 * Class Database - Mengelola koneksi PDO ke MySQL
 * Unit Kompetensi 4: OOP | Unit 6: Basis Data | Unit 8: Debugging
 */
class Database {
    private string $host = 'localhost';
    private string $dbName = 'db_PembayaranSppSiswa';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $conn = null;

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
