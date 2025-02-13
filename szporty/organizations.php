<?php
require_once 'conn.php';

class Organization {
    private $pdo;

    public function __construct(Database $db) {
        $this->pdo = $db->getConnection();
    }

    public function getAllOrganizations($disciplineId = null) {
        $sql = "SELECT o.id, o.name, o.alias, o.country, o.city, o.website, o.logo, 
                       COALESCE(GROUP_CONCAT(DISTINCT d.name ORDER BY d.name SEPARATOR ', '), '') AS discipline_name
                FROM organizations o
                LEFT JOIN organizations_has_disciplines ohd ON o.id = ohd.organization_id
                LEFT JOIN disciplines d ON ohd.discipline_id = d.id";
        
        $conditions = [];
        $params = [];

        if ($disciplineId) {
            $conditions[] = "ohd.discipline_id = :discipline_id";
            $params['discipline_id'] = $disciplineId;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY o.id ORDER BY o.name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new Database();
$organization = new Organization($db);
$selectedDisciplineId = $_GET['discipline_id'] ?? null;
$records = $organization->getAllOrganizations($selectedDisciplineId);

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
    <title>Sportowiada! JSM - Organizacje</title>
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
            <li id="first" class="active"><a href="organizations.php">Organizacje</a></li>
            <li><a href="clubs.php">Kluby</a></li>
        </ul>
        <div></div>
    </div>
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
                <h3>Filtruj organizacje według dyscypliny</h3>
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
                <h2>Lista organizacji sportowych</h2>
                <table>
                    <tr>
                        <th>Id</th>
                        <th>Logo</th>
                        <th>Nazwa</th>
                        <th>Dyscyplina</th>
                        <th>Kraj</th>
                        <th>Miasto</th>
                        <th>Strona internetowa</th>
                    </tr>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                            <td>
                                <img src="media/icons_organizations/<?php echo htmlspecialchars($row['logo'] ?? ''); ?>" alt="Logo" height="48">
                            </td>
                            <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['discipline_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['country'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['city'] ?? ''); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($row['website'] ?? ''); ?>" target="_blank">
                                    <?php echo htmlspecialchars($row['website'] ?? ''); ?>
                                </a>
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
                        