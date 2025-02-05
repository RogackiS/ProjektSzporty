<?php
require_once 'conn.php';

class Discipline {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    public function getAllDisciplines() {
        $sql = "SELECT * FROM disciplines";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDiscipline($name, $icon) {
        $sql = "INSERT INTO disciplines (name, icon) VALUES (:name, :icon)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['name' => $name, 'icon' => $icon]);
    }
}

$db = new Database();
$discipline = new Discipline($db);

// Obsługa dodawania nowej dyscypliny
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'], $_POST['icon'])) {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    
    if (!empty($name) && !empty($icon)) {
        $discipline->addDiscipline($name, $icon);
        header("Location: disciplines.php"); // Odświeżenie strony po dodaniu
        exit();
    }
}

$records = $discipline->getAllDisciplines();
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
            <li id="first" class="active"><a href="disciplines.php">Dyscypliny</a></li>
            <li><a href="clubs.php">Kluby</a></li>
            <li><a href="#3">Zespoły</a></li>
            <li><a href="#4">Organizacje</a></li>
            <li><a href="#5">Stowarzyszenia</a></li>
        </ul>
        <div></div>
    </div>
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
                <h3>Dodaj nową dyscyplinę</h3>
                <form method="POST" action="">
                    <label for="name">Nazwa:</label>
                    <input type="text" id="name" name="name" required>
                    <label for="icon">Ikona (nazwa pliku):</label>
                    <input type="text" id="icon" name="icon" required>
                    <button type="submit">Dodaj</button>
                </form>
                <br><br>
                <h2>Lista dyscyplin</h2>
                <table>
                    <tr>
                        <th>Id</th>
                        <th>Ikona</th>
                        <th>Nazwa dyscypliny</th>
                    </tr>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>
                                <img src="media/icons_discipline/<?php echo htmlspecialchars($row['icon']); ?>" alt="Ikona" width="32" height="32">
                            </td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
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
