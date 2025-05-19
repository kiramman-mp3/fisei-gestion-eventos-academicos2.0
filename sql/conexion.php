<?php
class Conexion {
    private $host = "localhost";
    private $db = "UTA";
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";
    private $conn;

    public function conectar() {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->db;charset=$this->charset",
                $this->user,
                $this->pass
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die("❌ Error de conexión: " . $e->getMessage());
        }
    }
}
?>
