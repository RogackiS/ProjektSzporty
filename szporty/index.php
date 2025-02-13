<?php
require_once 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Błąd: brak połączenia z bazą danych.");
}

$db = new Database();
$pdo = $db->getConnection();

// Pobranie listy tabel
$tables = [];
$query = $pdo->query("SHOW TABLES");
while ($row = $query->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
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
</head>
<body>
	<div id="header">
		<h1>Sportowiada! JSM</h1>
		<!-- <h2>100% sportu. 0% sensacji.</h2> -->
	</div>
	<div id="menu">
		<ul>
			<li id="first" class="active"><a href="index.php">Start</a></li>
			<li><a href="disciplines_type.php">Typy dyscyplin</a></li>
			<li><a href="disciplines.php">Dyscypliny</a></li>
			<li><a href="organizations.php">Organizacje</a></li>
			<li><a href="clubs.php">Kluby</a></li>
		</ul>
		<div></div>
	</div>
	<div id="container">
		<div id="primarycontainer">
			<div id="primarycontent">
				<?php
				try {
					// Pobranie bieżącej bazy danych
					$stmtDb = $pdo->query("SELECT DATABASE() AS current_db");
					$dbname = $stmtDb->fetchColumn();

					// Wyświetlenie nazwy bazy danych
					echo "<h2>Baza danych: </h2><ul><li>" . htmlspecialchars($dbname) . "</li></ul>";

					// Wyświetlenie listy tabel
					echo "<h2>Lista tabel w bazie <i>$dbname</i>:</h2>";
					echo "<ul>";
					foreach ($tables as $table) {
						echo "<li><strong>$table</strong> (<a href='?table=$table'>Zobacz strukturę</a>)</li>";
					}
					echo "</ul>";

					// Wyświetlenie struktury wybranej tabeli
					if (isset($_GET['table'])) {
						$tableName = $_GET['table'];
						echo "<h2>Struktura tabeli: <i>$tableName</i></h2>";

						// Pobranie informacji o kolumnach
						$stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
						echo "<table>
							<tr>
								<th>Nazwa kolumny</th>
								<th>Typ danych</th>
								<th>NULL</th>
								<th>Klucz</th>
								<th>Domyślna wartość</th>
								<th>Inne</th>
							</tr>";

						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							echo "<tr>
								<td>{$row['Field']}</td>
								<td>{$row['Type']}</td>
								<td>{$row['Null']}</td>
								<td>{$row['Key']}</td>
								<td>{$row['Default']}</td>
								<td>{$row['Extra']}</td>
							</tr>";
						}
						echo "</table>";

						// Liczba rekordów w tabeli
						$countStmt = $pdo->query("SELECT COUNT(*) AS count FROM $tableName");
						$countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
						echo "<p><strong>Liczba rekordów:</strong> " . $countResult['count'] . "</p>";
					}

				} catch (PDOException $e) {
					echo "<p>Błąd podczas pobierania danych: " . $e->getMessage() . "</p>";
				}
				?>
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
