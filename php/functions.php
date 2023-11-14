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

function manageDatabase($siteINFO, $sql) {
    $db = connectDB($siteINFO);
    
    if ($db->connect_error) {
        die("Sikertelen kapcsolódás: " . $db->connect_error);
    }

    if ($db->query($sql) === TRUE) {
        //echo "Siker!!!";
    } else {
        echo "Error: " . $db->error;
    }

    $db->close();
}

///////////////////////////// HTML /////////////////////////////
function buildTitle() {
    global $siteJSON, $siteLang, $linkDB, $siteInfo;

    if (isset($linkDB["redirect"])) {
        $text = '<div class="status">';
        $text .= '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
        $text .= '<div>'.$siteJSON["languages"][$siteLang]["redirect"].'</div>';
        $text .= '<div><i class="bi bi-arrow-down"></i></div>';
        $text .= '<div class="url"><a target="_blank" href="'.$siteInfo->redirectURL.'">'.$siteInfo->redirectURL.'</a></div>';
        $text .= '</div>';
    } else {
        $text = '<div class="status error">';
        $text .= '<b><i class="bi bi-shield-exclamation"></i><br>'.$siteJSON["languages"][$siteLang]["missing"].'</b><br></div>';
    }

    echo $text;
}

///////////////////////////// ERROR HANDLER /////////////////////////////
function errorHandler($type, $message) {
    /*
    $subject = "Hiba történt: REDCAT Link";
    $message = "Hiba típusa: $errorType\n Hiba üzenet: $errorMessage";
    $to = "redcat.hungary+error@gmail.com";

    // Send email
    mail($to, $subject, $message);

    // If critical error
    if ($errorType === 'critical') {
        echo "A critical error has occurred. Please contact the administrator.";
    }
    */
}
?>