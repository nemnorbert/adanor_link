<?php
if (isset($linkDB["rapid"])) {
    header("HTTP/1.1 302 Found");
    header("Location:".$siteInfo->redirectURL);
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= $siteLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDCAT Link</title>
    <link rel="stylesheet" href="<?= $siteInfo->redcatPath ?>package/bootstrap-icons-<?= $siteJSON["version"]["bootstrap"] ?>/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
</head>
<body>

<div class="box">
    <div class="logo">
        <a target="_blank" href="<?= $siteJSON["creator"] ?>">
            <div><img src="<?= $siteInfo -> redcatPath ?>img/logo/logo1.svg" alt="REDCAT logo"></div>
            <div class="link">
                Link <i class="bi bi-link-45deg"></i> BETA
            </div>
        </a>
    </div>
    <?php buildTitle(); ?>
    <div><a target="_blank" href="<?= $siteJSON["creator"] ?>">Powered by<br><b>REDCAT</b></a></div>
</div>
    
</body>
<script>
    let redirectURL = "<?php if (isset($linkDB["redirect"])) {echo $siteInfo->redirectURL;}?>";
</script>
<script src="<?= $siteInfo->mainPath ?>js/main.js?v=<?= time() ?>"></script>
</html>