<?php
require_once 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Błąd: brak połączenia z bazą danych.");
}

class Discipline {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllDisciplines() {
        $query = "SELECT d.id, d.name, d.icon, 
                         GROUP_CONCAT(CONCAT(dt.name, ' <a href=\"?remove_type=', dht.type_id, '&discipline_id=', d.id, '\" class=\"remove-type\">✖</a>') SEPARATOR ', ') AS types 
                  FROM disciplines d
                  LEFT JOIN disciplines_has_types dht ON d.id = dht.discipline_id
                  LEFT JOIN disciplines_types dt ON dht.type_id = dt.id
                  GROUP BY d.id";
        return $this->conn->query($query);
    }
    
    public function getAllTypes() {
        $query = "SELECT * FROM disciplines_types";
        return $this->conn->query($query);
    }
    
    public function addDiscipline($name, $icon, $types) {
        $query = "INSERT INTO disciplines (name, icon) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name, $icon]);
        $discipline_id = $this->conn->lastInsertId();
        
        if (!empty($types)) {
            $query = "INSERT INTO disciplines_has_types (discipline_id, type_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            foreach ($types as $type_id) {
                $stmt->execute([$discipline_id, $type_id]);
            }
        }
    }
    
    public function removeDisciplineType($discipline_id, $type_id) {
        $query = "DELETE FROM disciplines_has_types WHERE discipline_id = ? AND type_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$discipline_id, $type_id]);
    }
}

$discipline = new Discipline($conn);
$types = $discipline->getAllTypes();
$records = $discipline->getAllDisciplines();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $icon = $_POST['icon'];
    $types_selected = $_POST['types'] ?? [];
    
    $discipline->addDiscipline($name, $icon, $types_selected);
    echo "Dyscyplina została dodana!";
}

if (isset($_GET['remove_type']) && isset($_GET['discipline_id'])) {
    $discipline->removeDisciplineType($_GET['discipline_id'], $_GET['remove_type']);
    header("Location: disciplines.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <title>Sportowiada! JSM</title>
    <style>
        .remove-type {
            color: red;
            text-decoration: none;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>Sportowiada! JSM</h1>
    </div>
    <div id="menu">
        <ul>
            <li><a href="index.php">Start</a></li>
            <li><a href="disciplines_type.php">Typy dyscyplin</a></li>
            <li id="first" class="active"><a href="disciplines.php">Dyscypliny</a></li>
            <li><a href="organizations.php">Organizacje</a></li>
            <li><a href="clubs.php">Kluby</a></li>
        </ul>
        <div></div>
    </div>
    <div id="container">
        <div id="primarycontainer">
            <div id="primarycontent">
                <h3>Dodaj nową dyscyplinę</h3>
                <form method="POST">
                    <label for="name">Nazwa dyscypliny:</label>
                    <input type="text" name="name" required>
                    
                    <label for="icon">Ikona:</label>
                    <input type="text" name="icon" required>
                    
                    <label for="types">Typy dyscypliny:</label>
                    <select name="types[]" multiple>
                        <?php while ($row = $types->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>         
                    <button type="submit">Dodaj</button>
                </form>
                <br><br>
                <h2>Lista dyscyplin</h2>
                <table>
                    <tr>
                        <th>Id</th>
                        <th>Ikona</th>
                        <th>Nazwa dyscypliny</th>
                        <th>Typy</th>
                    </tr>
                    <?php while ($row = $records->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>
                                <img src="media/icons_discipline/<?php echo htmlspecialchars($row['icon']); ?>" alt="Ikona" width="48" height="48">
                            </td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo $row['types']; ?></td>
                        </tr>
                    <?php endwhile; ?>
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
