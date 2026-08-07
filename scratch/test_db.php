<?php
try {
    $pdo = new PDO('mysql:host=bdq2s0s4mwl4dumwjxah-mysql.services.clever-cloud.com;port=3306;dbname=bdq2s0s4mwl4dumwjxah', 'utwjojudhgicq5h9', 'qf3CJigltSaXiMoE1KHI');
    echo "CONNECTED_SUCCESSFULLY\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES_COUNT: " . count($tables) . "\n";
    print_r($tables);
} catch (Exception $e) {
    echo "DB_ERROR: " . $e->getMessage() . "\n";
}
