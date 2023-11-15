<?php
// Load JSON
function loadJSON($filePath) {
    $json = file_get_contents($filePath);
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error decoding JSON: ' . json_last_error_msg());
    }

    return $data;
}

///////////////////////////// DATABASE /////////////////////////////
function connectDB($siteINFO) {
    try {
        $testMode = isset($siteINFO->test) ? $siteINFO->test : false;
        $servername = $testMode ? DB_LOCAL_HOST : DB_SERVER_HOST;
        $username = $testMode ? DB_LOCAL_USER : DB_SERVER_USER;
        $password = $testMode ? DB_LOCAL_PASSWORD : DB_SERVER_PASSWORD;
        $dbname = $testMode ? DB_LOCAL_NAME : DB_SERVER_NAME;

        $db = new mysqli($servername, $username, $password, $dbname);

        if ($db->connect_error) {
            throw new Exception("Sikertelen kapcsolódás: " . $db->connect_error);
        }

        return $db;

    } catch (Exception $e) {
        errorHandler("DB Connection", $e->getMessage());
        die("Sikertelen kapcsolódás: " . $e->getMessage());
    }
}

function manageDatabase($siteINFO) {
    $db = connectDB($siteINFO);
    $sql = 'SELECT l.url, s.url, l.redirect, l.rapid
    FROM `links` as l
    LEFT JOIN services as s ON l.service_id = s.id
    WHERE l.url = "'.$siteINFO->page.'" LIMIT 1;';

    if ($db->connect_error) {
        errorHandler("DB Connection", $db->connect_error);
        die("Sikertelen kapcsolódás: " . $db->connect_error);
    }

    $result = $db->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $siteINFO->outURL = $row["url"] . $row["redirect"];
            $siteINFO->rapid = $row["rapid"];
            $siteINFO->status = isset($row["redirect"]) ? "redirect" : "error_general";
        } else {
            $siteINFO->status = "not_found";
            errorHandler("Page Not found", $siteINFO->requestURI);
        }

        $result->free();
    } else {
        errorHandler("DB No result", $db->error);
        echo "Error: " . $db->error;
    }

    $db->close();
}

///////////////////////////// HTML /////////////////////////////
function buildBox() {
    global $siteINFO, $langJSON;
    $status = isset($siteINFO->status) && $siteINFO->status === "redirect";

    $title = $langJSON["title"][$siteINFO->status];
    $text = $status ? $siteINFO->outURL : (isset($langJSON["text"][$siteINFO->status]) ? $langJSON["text"][$siteINFO->status] : "");

    $html = '<div class="status ';
    $html .= $status ? 'ready">' : 'error">';
        $html .= '<div class="title">'.$title.'</div>';
        $html .= '<div class="text">'.$text.'</div>';
    $html .= '</div>';
    echo $html;
}
///////////////////////////// ERROR HANDLER /////////////////////////////
function errorHandler($type, $text) {
    global $siteINFO;

    $path = "log";
    $time = date('Y-m-d H:i:s');
    $message = "$time - [$type] $text" . PHP_EOL;
    $logFile = "log/".$path."_".date('y')."-".date('m').".txt";
    error_log($message, 3, $logFile);

    $siteINFO -> errTitle = $type;
    $siteINFO -> errText = $text;
}
?>