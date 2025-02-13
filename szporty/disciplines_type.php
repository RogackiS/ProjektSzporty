<?php
require_once 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Błąd: brak połączenia z bazą danych.");
}

class DisciplineType {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    public function getAllDisciplineTypes() {
        $sql = "SELECT * FROM disciplines_types ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDisciplineType($name, $description) {
        $sql = "INSERT INTO disciplines_types (name, description) VALUES (:name, :description)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['name' => $name, 'description' => $description]);
    }
}

$db = new Database();
$disciplineType = new DisciplineType($db);

// Obsługa dodawania nowego typu dyscypliny
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');

    if (!empty($name)) {
        $disciplineType->addDisciplineType($name, $description);
        header("Location: disciplines_types.php"); // Odświeżenie strony po dodaniu
        exit();
    }
}

$records = $disciplineType->getAllDisciplineTypes();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <title>Sportowiada! JSM</title>
</head>
<body>
    <div id="header">
        <h1>Sportowiada! JSM</h1>
    </div>
    <div id="menu">
        <ul>
            <li><a href="index.php">Start</a></li>
			<li id="first" class="active"><a href="disciplines_type.php">Typy dyscyplin</a></li>
			<li><a href="disciplines.php">Dyscypliny</a></li>
			<li><a href="organizations.php">Organizacje</a></li>
			<li><a href="clubs.php">Kluby</a></li>
        </ul>
        <div></div>
    </div>
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
            <h3>Dodaj nowy typ dyscypliny</h3>
    <form method="POST" action="">
        <label for="name">Nazwa typu:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="description">Opis (opcjonalnie):</label>
        <input type="text" id="description" name="description">

        <button type="submit">Dodaj</button>
    </form>
    
    <br><br>

    <h2>Lista typów dyscyplin</h2>
    <table>
        <tr>
            <th>Id</th>
            <th>Nazwa</th>
            <th>Opis</th>
        </tr>
        <?php foreach ($records as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
            </div>
        </div>
        <div id="secondarycontent"></div>
        <div class="clearit"></div>
    </div>
    <div id="footer">
        &copy; 2024 Rogacki! S.
    </div>
</body>
</html>
