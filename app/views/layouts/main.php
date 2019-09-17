<?php

use app\components\Asset;
use app\components\Session;
use app\components\Config;

?>

<!DOCTYPE html>
<html lang="en" class="<?= $this->getHtmlClass() ?> html-ssz1">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="_token" content="<?= Session::getInstance()->getToken() ?>">
    <meta name="_envyronment" content="<?= Config::getInstance()->get( 'app.envyronment' ) ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="<?= Asset::cacheBooster( '/assets/css/common.min.css' ) ?>">
    <?= $this->getBlockHead() ?>
    <title><?= $this->getPageTitle() ?></title>
</head>
<body class="<?= $this->getBodyClass() ?>">

<?= $this->staticRender( 'common/header' ) ?>
<?= $this->staticRender( 'common/breadcrumb' ) ?>
<?= $this->getBlockBodyStart() ?>
<?= $content ?? '' ?>

<div class="container">
    Rendered Templates:
    <ul class="text-muted">
        <?php foreach ( $this->templates as $template => $path ) : ?>
            <li>
                <div><?= $template ?></div>
                <div class="small"><?= $path ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script src="<?= Asset::cacheBooster( '/assets/js/vendors.min.js' ) ?>"></script>
<script src="<?= Asset::cacheBooster( '/assets/js/common.min.js' ) ?>"></script>
<?= $this->getBlockBodyEnd() ?>
</body>
</html>
