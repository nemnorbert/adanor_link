<?php
if (isset($apiData["rapid"]) && $apiData["rapid"]) {
    header("HTTP/1.1 301 Moved Permanently");
    header('Location: ' . $apiData["redirect_to"]);
    //var_dump($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= $siteINFO -> langSite ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDCAT Link</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
</head>
<body
<?php if ($apiData["status"] !== "ready") {echo ' class="errors"';}?>>

<div class="box">
    <div class="logo">
        <a target="_blank" href="<?= $siteJSON["creator"] ?>">
            <div><img src="<?= $siteINFO -> redcatPath ?>img/logo/logo1.svg" alt="REDCAT logo"></div>
            <div class="link">
                Link BETA
            </div>
        </a>
    </div>
    <?php buildBox($apiData, $langJSON); ?>
    <div>
        <a target="_blank" href="<?= $siteJSON["creator"] ?>">Powered by<br><b>REDCAT</b></a>
    </div>
</div>
    
</body>
<script>
    const redirect_url = "<?php if (isset($apiData["redirect_to"])) {
        echo $apiData["redirect_to"];
    } ;?>";
    const redirect_status = "<?= $apiData["status"] ?>";
</script>
<script src="<?= $siteINFO->mainPath ?>js/main.js?v=<?= time() ?>"></script>
</html>