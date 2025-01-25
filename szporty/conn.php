<?php
// Dane połączenia z bazą danych
$host = 'localhost'; // Adres serwera bazy danych
$dbname = 'szporty'; // Nazwa bazy danych
$username = 'root'; // Użytkownik bazy danych
$password = 'TwojeNoweHaslo'; // Hasło do bazy danych

try {
    // Tworzenie połączenia za pomocą PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Ustawienie trybu obsługi błędów
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Obsługa błędów połączenia
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}
?>
