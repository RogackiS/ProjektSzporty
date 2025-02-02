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
            <li><a href="index.php">Start</a></li>
			<li id="first" class="active"><a href="disciplines.php">Dyscypliny</a></li>
			<li><a href="clubs.php">Kluby</a></li>
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
					
					// Zapytanie SQL
					$sql = "SELECT * FROM disciplines";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();

					// Pobranie wyników jako tablica asocjacyjna
					$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

					// Tłumaczenie nagłówków kolumn
					$translations_pl = [
						'id' => 'Id',
						'name' => 'Nazwa dyscypliny',
						'icon' => 'Ikona'
					];

					// Wyświetlenie rekordów w tabeli HTML
					echo "<table border='1' cellpadding='10'>";

					// Nagłówki tabeli
					echo "<tr>";
					foreach ($translations_pl as $column => $translated) {
						echo "<th>" . htmlspecialchars($translated) . "</th>";
					}
					echo "</tr>";

					// Dane tabeli
					foreach ($records as $row) {
						echo "<tr>";
						foreach (array_keys($translations_pl) as $column) {
							echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
						}
						echo "</tr>";
					}

					echo "</table>";
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