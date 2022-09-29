<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (isset($this->post)): ?>
        <meta name="description" content="<?= $this->post->getExcerpt() ?>">
        <title><?= $this->getPageTitle() ?></title>
    <?php else: ?>
        <meta name="description" content="<?= $this->getSiteTagline() ?>">
        <title><?= $this->getPageTitle() ?></title>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= $this->getRootUrl() ?>themes<?= THEME_DIR ?>/style.css">
    <link rel="icon" type="image/svg" href="<?= self::getThemeAssetsUrl() ?>icons/favicon.svg">
    <link rel="apple-touch-icon" href="<?= self::getThemeAssetsUrl() ?>icons/favicon.svg">
</head>
<body>