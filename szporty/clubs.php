<?php
require_once 'conn.php';

class Club {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    public function getAllClubs($disciplineId = null) {
        if ($disciplineId) {
            $sql = "SELECT * FROM clubs WHERE discipline_id = :discipline_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['discipline_id' => $disciplineId]);
        } else {
            $sql = "SELECT * FROM clubs";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addClub($name, $city, $disciplineId) {
        $sql = "INSERT INTO clubs (name, city, discipline_id) VALUES (:name, :city, :discipline_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['name' => $name, 'city' => $city, 'discipline_id' => $disciplineId]);
    }

    public function updateClubDiscipline($clubId, $disciplineId) {
        $sql = "UPDATE clubs SET discipline_id = :discipline_id WHERE id = :club_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['discipline_id' => $disciplineId, 'club_id' => $clubId]);
    }
}

$db = new Database();
$club = new Club($db);

// Pobranie listy dyscyplin
$stmt = $db->getConnection()->query("SELECT * FROM disciplines");
$disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobranie ID wybranej dyscypliny
$selectedDisciplineId = $_GET['discipline_id'] ?? null;

// Obsługa aktualizacji dyscypliny klubu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['club_id'], $_POST['discipline_id'])) {
    $clubId = (int) $_POST['club_id'];
    $disciplineId = (int) $_POST['discipline_id'];
    
    $club->updateClubDiscipline($clubId, $disciplineId);
    header("Location: clubs.php?discipline_id=" . $selectedDisciplineId); // Zachowanie filtrowania po zmianie
    exit();
}

// Pobranie klubów według wybranej dyscypliny
$records = $club->getAllClubs($selectedDisciplineId);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <title>Sportowiada! JSM - Kluby</title>
</head>
<body>
    <div id="header">
        <h1>Sportowiada! JSM</h1>
    </div>
    <div id="menu">
        <ul>
            <li><a href="index.php">Start</a></li>
            <li><a href="disciplines.php">Dyscypliny</a></li>
            <li class="active"><a href="clubs.php">Kluby</a></li>
            <li><a href="#">Zespoły</a></li>
            <li><a href="#">Organizacje</a></li>
            <li><a href="#">Stowarzyszenia</a></li>
        </ul>
    </div> 
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
                <h2>Filtruj kluby według dyscypliny</h2>
                <form method="GET" action="">
                    <label for="discipline">Wybierz dyscyplinę:</label>
                    <select name="discipline_id" id="discipline" onchange="this.form.submit()">
                        <option value="">Wszystkie</option>
                        <?php foreach ($disciplines as $discipline): ?>
                            <option value="<?= $discipline['id']; ?>" <?= ($selectedDisciplineId == $discipline['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($discipline['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                
                <h2>Lista klubów</h2>
                <table border="1">
                    <tr>
                        <th>Id</th>
                        <th>Nazwa klubu</th>
                        <th>Miasto</th>
                        <th>Dyscyplina</th>
                    </tr>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['city']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="club_id" value="<?= $row['id']; ?>">
                                    <input type="hidden" name="discipline_id" value="<?= $selectedDisciplineId; ?>">
                                    <select name="discipline_id" onchange="this.form.submit()">
                                        <option value="">Brak</option>
                                        <?php foreach ($disciplines as $discipline): ?>
                                            <option value="<?= $discipline['id']; ?>" <?= ($row['discipline_id'] == $discipline['id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($discipline['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
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
