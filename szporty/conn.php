<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'szporty';
    private $username = 'root';
    private $password = 'TwojeNoweHaslo';
    private $pdo;

    // Konstruktor automatycznie łączy się z bazą
    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }

    // Metoda zwracająca połączenie
    public function getConnection() {
        return $this->pdo;
    }
}
?>
