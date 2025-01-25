<?php
require 'conn.php';
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
			<li><a href="disciplines.php">Dyscypliny</a></li>
			<li><a href="#2">Kluby</a></li>
			<li><a href="#3">Zespoły</a></li>
			<li><a href="#4">Organizacje</a></li>
			<li><a href="#5">Stwarzyszenia</a></li>
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

						// Pobranie tabel w bazie danych
						$queryTables = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = :dbname";
						$stmtTables = $pdo->prepare($queryTables);
						$stmtTables->execute([':dbname' => $dbname]);

						// Wyświetlenie listy tabel
						echo "<h2>Tabele w bazie danych:</h2>";
						echo "<ul>";
						if ($stmtTables->rowCount() > 0) {
							while ($row = $stmtTables->fetch(PDO::FETCH_ASSOC)) {
								echo "<li>" . htmlspecialchars($row['TABLE_NAME']) . "</li>";
							}
						} else {
							echo "<li>Brak tabel w bazie danych.</li>";
						}
						echo "</ul>";
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