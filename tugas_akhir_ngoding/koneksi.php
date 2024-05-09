<?php

class Database
{
    private $host = "localhost";
    private $username = "root"; // Ganti dengan username database Anda
    private $password = ""; // Ganti dengan password database Anda
    private $database = "deteksi_hoax"; // Ganti dengan nama database Anda
    private $conn;

    // Fungsi untuk melakukan koneksi ke database
    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Koneksi ke database berhasil";
        } catch (PDOException $e) {
            echo "Koneksi ke database gagal: " . $e->getMessage();
        }

        return $this->conn;
    }
}
