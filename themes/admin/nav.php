<script>
    window.addEventListener('load', () => {
        const mainHeader = document.querySelector('#main-header');
        const navMenu = mainHeader.querySelectorAll('nav');
        const button = mainHeader.querySelector('button');       

        button.addEventListener('click', () => {
            navMenu.forEach((menu) => {
                menu.style.display == 'flex' ? menu.style.display = 'none' : menu.style.display = 'flex';
            });
        });

        window.addEventListener('resize', () => {
            navMenu.forEach((menu) => {
                if (menu.style.display == 'none' && window.innerWidth > 992) {
                    menu.style.display = 'block';
                } else if (menu.style.display == 'flex' && window.innerWidth <= 992) {
                    menu.style.display = 'none';
                }
            });
        });
    });
</script>
<header id="main-header">
    <div id="logo">
        <div id="logo-text">
            <p id="logo-site-name"><?= $this->getSiteName() ?></p>
            <p id="logo-site-tagline"><?= $this->getSiteTagline() ?></p>
        </div>
    </div>
    <button type="button">
        <img src="<?= self::getAssetsUrl() ?>icons/menu.svg" alt="Menu" />
    </button>
    <nav id="main-header-nav">
        <ul>
            <li>
                <a href="<?= $this->getRootUrl() ?>posts">
                    <img src="<?= self::getAssetsUrl() ?>icons/post.svg" alt="Posts" />
                    Posts
                </a>
            </li>
            <li>
                <a href="<?= $this->getRootUrl() ?>categories">
                    <img src="<?= self::getAssetsUrl() ?>icons/categories.svg" alt="categories" />
                    Categories
                </a>
            </li>
            <li>
                <a href="<?= $this->getRootUrl() ?>tags">
                    <img src="<?= self::getAssetsUrl() ?>icons/tags.svg" alt="Tags" />
                    Tags
                </a>
            </li>
            <li>
                <a href="/<?= SITE_ROOT ?>">
                    <img src="<?= self::getAssetsUrl() ?>icons/preview.svg" alt="Preview" />
                    Preview Site
                </a>
            </li>
        </ul>
    </nav>
    <nav id="account-controls">
        <ul>
            <li>
                <a href="<?= $this->getRootUrl() ?>logout">
                    <img src="<?= self::getAssetsUrl() ?>icons/logout.svg" alt="Logout" />
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</header>