<?php
require_once 'conn.php';

class Club {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    public function getAllClubs($disciplineId = null) {
        if ($disciplineId) {
            $sql = "SELECT id, name, state, postal_code, city, CONCAT(street, ' ', building_number) AS address, website, logo 
                    FROM clubs WHERE discipline_id = :discipline_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['discipline_id' => $disciplineId]);
        } else {
            $sql = "SELECT id, name, state, postal_code, city, CONCAT(street, ' ', building_number) AS address, website, logo FROM clubs";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new Database();
$club = new Club($db);

// Pobranie listy dyscyplin
$stmt = $db->getConnection()->query("SELECT * FROM disciplines");
$disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobranie ID wybranej dyscypliny
$selectedDisciplineId = $_GET['discipline_id'] ?? null;

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
        <div></div>
    </div> 
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
                <h3>Filtruj kluby według dyscypliny</h3>
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
                <br><br>        
                <h2>Lista klubów</h2>
                <table>
                    <tr>
                        <th>Id</th>
                        <th>Logo</th>
                        <th>Nazwa klubu</th>
                        <th>Województwo</th>
                        <th>Kod pocztowy</th>
                        <th>Miasto</th>
                        <th>Adres</th> <!-- Teraz mamy jedną kolumnę dla ulicy i numeru -->
                        <th>Strona</th>
                    </tr>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><img src="media/logos/<?= htmlspecialchars($row['logo']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" width="48" height="48"></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['state']); ?></td>
                            <td><?= htmlspecialchars($row['postal_code']); ?></td>
                            <td><?= htmlspecialchars($row['city']); ?></td>
                            <td><?= htmlspecialchars($row['address']); ?></td> <!-- Połączona ulica + numer -->
                            <td><a href="<?= htmlspecialchars($row['website']); ?>" target="_blank">Strona</a></td>
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
