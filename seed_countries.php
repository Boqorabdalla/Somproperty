<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=worksuite', 'root', '');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('DELETE FROM countries');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// Read file and extract countries array manually
$content = file_get_contents(__DIR__.'/database/seeders/CountriesTableSeeder.php');

// Find the start of the countries array
$start = strpos($content, '$countries = [');
$start = strpos($content, '[', $start);

// Find the matching closing bracket
$depth = 1;
$pos = $start + 1;
while ($depth > 0 && $pos < strlen($content)) {
    $char = $content[$pos];
    if ($char === '[') {
        $depth++;
    } elseif ($char === ']') {
        $depth--;
    }
    $pos++;
}
$arrayStr = substr($content, $start, $pos - $start);

// Parse the PHP array using token_get_all
// Instead, let's use a simpler regex approach
$rows = preg_split('/\],\s*\[/', $arrayStr);

$stmt = $pdo->prepare('INSERT INTO countries (id, iso, name, nicename, iso3, numcode, phonecode) VALUES (?, ?, ?, ?, ?, ?, ?)');

$count = 0;
foreach ($rows as $rowStr) {
    $rowStr = trim($rowStr, "[] \n\r\t");
    if (empty($rowStr)) {
        continue;
    }

    // Extract key-value pairs
    preg_match_all("/'(\w+)'\s*=>\s*'([^']*)'/", $rowStr, $matches);
    $data = array_combine($matches[1], $matches[2]);

    if (! isset($data['id'])) {
        continue;
    }

    $stmt->execute([
        (int) $data['id'],
        $data['iso'] ?? '',
        $data['name'] ?? '',
        $data['nicename'] ?? '',
        $data['iso3'] ?: null,
        $data['numcode'] !== '' ? (int) $data['numcode'] : null,
        (int) ($data['phonecode'] ?? 0),
    ]);
    $count++;
}

echo "Inserted $count countries\n";

$stmt = $pdo->query('SELECT COUNT(*) FROM countries');
echo 'Total countries: '.$stmt->fetchColumn()."\n";
$stmt = $pdo->query("SELECT iso, nicename FROM countries WHERE iso = 'XS'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo 'Somaliland: '.($row ? 'FOUND ('.$row['nicename'].')' : 'MISSING')."\n";
