<?php
// Load JSON
function loadJSON($filePath) {
    try {
        $json = file_get_contents($filePath);

        if ($json === false) {
            throw new Exception('Error reading JSON file: ' . error_get_last()['message']);
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decoding JSON: ' . json_last_error_msg());
        }

        return $data;
    } catch (Exception $e) {
        errorHandler("general_error", $e->getMessage());
        return null;
    }
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
            $db->close();
            throw new Exception("Sikertelen kapcsolódás: " . $db->connect_error);
        }
        return $db;

    } catch (Exception $e) {
        errorHandler("connect_error", $e->getMessage());
    }
}

function manageDatabase($siteINFO) {
    $db = connectDB($siteINFO);
    
    if ($siteINFO->status !== "connect_error") {
        $sql = 'SELECT l.url, s.url, l.redirect, l.rapid, l.service_id, u.blocked, u.premium
                FROM `links` as l
                LEFT JOIN services as s ON l.service_id = s.id
                LEFT JOIN users as u ON l.user_id = u.user_id
                WHERE l.url = ? LIMIT 1;';

        try {
            $stmt = $db->prepare($sql);

            if (!$stmt) {
                throw new Exception("Prepared Statement Error: " . $db->error);
            }

            $stmt->bind_param("s", $siteINFO->page);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $siteINFO->outURL = $row["url"] . $row["redirect"];
                    $siteINFO->rapid = ($row["rapid"] === 1) || ($row["service_id"] > 1) || $row["premium"];
                    $siteINFO->status = isset($row["redirect"]) ? "redirect" : "general_error";

                    if ($row["blocked"]) {
                        $siteINFO->status = "blocked";
                        $siteINFO->outURL = "";
                    }
                } else {
                    errorHandler("not_found", $siteINFO->requestURI);
                }

                $result->free();
            }

            $stmt->close();

        } catch (Exception $e) {
            errorHandler("sql_error", $e->getMessage());
        }

        $db->close();
    }
}

///////////////////////////// HTML /////////////////////////////
function buildBox() {
    global $siteINFO, $langJSON;
    $status = isset($siteINFO->status) && $siteINFO->status === "redirect";
    $logo_error = '<svg xmlns="http://www.w3.org/2000/svg" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>';
    $logo_redirect = '<svg xmlns="http://www.w3.org/2000/svg" class="bi bi-shield-fill-check" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647z"/>
        </svg>';

    $title = isset($langJSON["title"][$siteINFO->status]) ? $langJSON["title"][$siteINFO->status] : "Error";
    $text = $status ? $siteINFO->outURL : (isset($langJSON["text"][$siteINFO->status]) ? $langJSON["text"][$siteINFO->status] : "");

    $html = '<div class="status ';
    $html .= $status ? 'ready">' : 'error">';
        $html .= $status ? $logo_redirect : $logo_error;
        $html .= '<div class="title">'.$title.'</div>';
        $html .= '<div class="text">'.$text.'</div>';
    $html .= '</div>';
    echo $html;
}
///////////////////////////// ERROR HANDLER /////////////////////////////
function errorHandler($type, $text) {
    global $siteINFO;
    $siteINFO->status = $type;

    $path = "log";
    $time = date('Y-m-d H:i:s');
    $message = "$time - [$type] $text" . PHP_EOL;
    $logFile = "log/".$path."_".date('y')."-".date('m').".txt";
    error_log($message, 3, $logFile);
}
?>