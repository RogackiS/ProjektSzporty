<?php
require_once 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Błąd: brak połączenia z bazą danych.");
}

class Club {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    public function getAllClubs($disciplineId = null, $leagueId = null, $orderBy = 'id', $orderDir = 'ASC') {
        $validColumns = ['id', 'name', 'state', 'postal_code', 'city', 'address', 'website', 'league_name', 'discipline_name'];
        if (!in_array($orderBy, $validColumns)) {
            $orderBy = 'id';
        }
        $orderDir = ($orderDir === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT c.id, c.name, c.state, c.postal_code, c.city, 
                       CONCAT(c.street, ' ', c.building_number) AS address, 
                       c.website, c.logo, 
                       l.name AS league_name, d.name AS discipline_name
                FROM clubs c
                LEFT JOIN leagues l ON c.league_id = l.id
                LEFT JOIN disciplines d ON c.discipline_id = d.id";

        $conditions = [];
        $params = [];

        if ($disciplineId) {
            $conditions[] = "c.discipline_id = :discipline_id";
            $params['discipline_id'] = $disciplineId;
        }
        
        if ($leagueId) {
            $conditions[] = "c.league_id = :league_id";
            $params['league_id'] = $leagueId;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY c.id ORDER BY $orderBy $orderDir";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new Database();
$club = new Club($db);
$selectedDisciplineId = $_GET['discipline_id'] ?? null;
$selectedLeagueId = $_GET['league_id'] ?? null;
$orderBy = $_GET['order_by'] ?? 'id';
$orderDir = $_GET['order_dir'] ?? 'ASC';
$records = $club->getAllClubs($selectedDisciplineId, $selectedLeagueId, $orderBy, $orderDir);

$query = "SELECT id, name FROM leagues";
if ($selectedDisciplineId) {
    $query = "SELECT id, name FROM leagues WHERE id IN (SELECT league_id FROM clubs WHERE discipline_id = :discipline_id)";
    $stmtLeagues = $db->getConnection()->prepare($query);
    $stmtLeagues->execute(['discipline_id' => $selectedDisciplineId]);
} else {
    $stmtLeagues = $db->getConnection()->query($query);
}
$leagues = $stmtLeagues->fetchAll(PDO::FETCH_ASSOC);

$stmtDisciplines = $db->getConnection()->query("SELECT id, name FROM disciplines");
$disciplines = $stmtDisciplines->fetchAll(PDO::FETCH_ASSOC);
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
            <li><a href="disciplines_type.php">Typy dyscyplin</a></li>
            <li><a href="disciplines.php">Dyscypliny</a></li>
            <li><a href="organizations.php">Organizacje</a></li>
            <li id="first" class="active"><a href="clubs.php">Kluby</a></li>
        </ul>
        <div></div>
    </div>
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
                <h3>Filtruj kluby według dyscypliny i rozgrywek</h3>
                <form method="GET" action="">
                    <label for="discipline">Dyscyplina:</label>
                    <select name="discipline_id" id="discipline" onchange="this.form.submit()">
                        <option value="">Wszystkie</option>
                        <?php foreach ($disciplines as $discipline): ?>
                            <option value="<?= $discipline['id']; ?>" <?= ($selectedDisciplineId == $discipline['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($discipline['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="league">Rozgrywki:</label>
                    <select name="league_id" id="league" onchange="this.form.submit()">
                        <option value="">Wszystkie</option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?= $league['id']; ?>" <?= ($selectedLeagueId == $league['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($league['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <br><br>
                <h2>Lista klubów</h2>
                <table>
                    <tr>
                        <?php
                        $columns = ['id' => 'ID', 'name' => 'Nazwa', 'discipline_name' => 'Dyscyplina', 'league_name' => 'Rozgrywki', 'state' => 'Kraj', 'city' => 'Miasto', 'website' => 'Strona'];
                        foreach ($columns as $col => $name) {
                            $newOrderDir = ($orderBy == $col && $orderDir == 'ASC') ? 'DESC' : 'ASC';
                            echo "<th><a href='?order_by=$col&order_dir=$newOrderDir'>$name</a></th>";
                        }
                        ?>
                        <th>Logo</th>
                    </tr>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <?php foreach ($columns as $col => $name): ?>
                                <td><?= $col === 'website' ? "<a href='" . htmlspecialchars($row[$col]) . "' target='_blank'>" . htmlspecialchars($row[$col]) . "</a>" : htmlspecialchars($row[$col]); ?></td>
                            <?php endforeach; ?>
                            <td><img src="media/logos/<?= htmlspecialchars($row['logo']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" width="48"></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
