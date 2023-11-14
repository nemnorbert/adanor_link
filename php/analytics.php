<?php
function analyticsREDCAT() {
    global $testServer;

    $siteURL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $config = [
        'host' => $testServer ? 'localhost' : 'localhost',
        'dbName' => $testServer ? 'redcat-analytics' : 'redcathu_redcat-analytics',
        'username' => $testServer ? 'root' : 'redcathu_analySlave',
        'password' => $testServer ? 'Noelke11' : 'ej6Q$hTc8a'
    ];

    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbName']};charset=utf8mb4";
        $db = new PDO($dsn, $config['username'], $config['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("SELECT id FROM websites WHERE url = ?");
        $stmt->execute([$siteURL]);
        $websiteID = $stmt->fetchColumn();

        if ($websiteID) {
            $stmt = $db->prepare("UPDATE visits SET visits = visits + 1 WHERE website_id = ? AND visits_date = ?");
            $stmt->execute([$websiteID, date('Y-m-d')]);
        } else {
            $stmt = $db->prepare("INSERT INTO websites (url) VALUES (?)");
            $stmt->execute([$siteURL]);
            $newWebsiteID = $db->lastInsertId();
            
            $stmt = $db->prepare("INSERT INTO visits (website_id, visits_date, visits) VALUES (?, ?, '1')");
            $stmt->execute([$newWebsiteID, date('Y-m-d')]);
        }

        $db = null; // Close connection
    } catch (PDOException $e) {
        die("Connection error: " . $e->getMessage());
    }
}
analyticsREDCAT();
?>