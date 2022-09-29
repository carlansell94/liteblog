<header id="main-header">
    <a href="<?= $this->getRootUrl() ?>" id="logo">
        <div id="logo-text">
            <p id="logo-site-name"><?= $this->getSiteName() ?></p>
            <p id="logo-site-tagline"><?= $this->getSiteTagline() ?></p>
        </div>
    </a>
    <nav id="main-header-nav">
	    <ul>
            <?php if ($this->isLoggedIn()): ?>
            <li>
                <a href="<?= $this->getRootUrl() . ADMIN_URL ?>">Admin Dashboard</a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?= $this->getRootUrl() ?>">Latest Posts</a>
            </li>
	    </ul>
	</nav>
</header>