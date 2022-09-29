<?php

namespace carlansell94\Liteblog\View;

use carlansell94\Liteblog\Config\Config;
use carlansell94\Liteblog\Session\Session;

class View
{
    private ?int $page = null;
    private ?int $max_page = null;
    private ?string $page_title = null;
    protected static ?string $theme_dir = null;

    public function __construct(
        private ?string $template = null,
        private bool $is_admin_url = false
    ) {
        if (self::$theme_dir === null) {
            $this->setThemeDir();
        }
    }

    public function setData(mixed ...$params): void
    {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

    public function setPageInfo(int $current_page, int $max_page): bool
    {
        if ($current_page > $max_page) {
            return false;
        }

        $this->page = $current_page;
        $this->max_page = $max_page;

        return true;
    }

    public function setPageTitle(string $page_title): void
    {
        $this->page_title = $page_title;
    }

    public static function getAssetsUrl(): string
    {
        return '/' . SITE_ROOT . '/assets/';
    }

    public static function getThemeAssetsUrl(): string
    {
        return '/' . SITE_ROOT . '/themes' . THEME_DIR . '/assets/';
    }

    public function getSiteName(): string
    {
        return SITE_NAME;
    }

    public function getSiteTagline(): string
    {
        return SITE_TAGLINE;
    }

    public function getCurrentUrl(bool $trailing_slash = true): string
    {
        $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (str_ends_with($url, '/page/' . $this->page)) {
            $url = substr($url, 0, -strlen('/page/' . $this->page));
        }

        if (empty($_SERVER['HTTPS'])) {
            $protocol = 'http://';
        } else {
            $protocol = 'https://';
        }

        return self::formatUrl($protocol . $url, $trailing_slash);
    }

    public function getAdminUrlStatus(): bool
    {
        return $this->is_admin_url;
    }

    public function getRootUrl(bool $trailing_slash = true): string
    {
        $url = '/' . SITE_ROOT;

        if ($this->is_admin_url) {
            $url .= '/' . ADMIN_URL;
        }

        return self::formatUrl($url, $trailing_slash);
    }

    public function getThemeDir(): string|null
    {
        return self::$theme_dir;
    }

    public function getPageTitle(): string
    {
        if ($this->page_title === null) {
            return SITE_NAME;
        }

        if ($this->is_admin_url) {
            return SITE_NAME . ' Admin - ' . $this->page_title;
        }

        return SITE_NAME . ' - ' . $this->page_title;
    }

    public function getHead(): void
    {
        require_once self::$theme_dir . '/head.php';
    }

    public function getNav(): void
    {
        require_once self::$theme_dir . '/nav.php';
    }

    public function getSideBar(): bool
    {
        if (isset($this->elements['sidebar'])) {
            $this->elements['sidebar']->load();
            $this->elements['sidebar']->loadView();
            $this->elements['sidebar']->output();
            return true;
        }

        return false;
    }

    public function getFooter(): void
    {
        require_once self::$theme_dir . '/footer.php';
    }

    public function getPagination(): string
    {
        $html = '<ul class="pagination">';

        if ($this->page > 1) {
            $prev = $this->page - 1;
            $html .=
                  '<li><a href="' . $this->getCurrentUrl() . "\"><<</a></li>"
                . '<li><a href="' . $this->getCurrentUrl() . "page/{$prev}\"><</a></li>"
                . '<li><a href="' . $this->getCurrentUrl() . "page/{$prev}\">{$prev}</a></li>";
        }

        $html .= '<li id="current-page">' . $this->page . '</li>';

        if ($this->page < $this->max_page) {
            $next = $this->page + 1;
            $html .=
                  '<li><a href="' . $this->getCurrentUrl() . "page/{$next}\">{$next}</a></li>"
                . '<li><a href="' . $this->getCurrentUrl() . "page/{$next}\">></a></li>"
                . '<li><a href="' . $this->getCurrentUrl() . "page/{$this->max_page}\">>></a></li>";
        }

        $html .= '</ul>';

        return $html;
    }

    public function isLoggedIn(): bool
    {
        return Session::isLoggedIn();
    }

    public function render(): void
    {
        $this->getNav();
        $this->getTemplate();
    }

    protected function getTemplate(): bool
    {
        $file = match ($this->template) {
            'post'              => '/post.php',
            'post_list'         => '/post_list.php',
            'category_list'     => '/category_list.php',
            'tag_list'          => '/tag_list.php',
            'sidebar'           => '/sidebar.php',
            default             => ''
        };

        if ($file === '') {
            return false;
        }

        require_once self::$theme_dir . $file;

        return true;
    }

    private function setThemeDir(): void
    {
        if ($this->template === 'login') {
            self::$theme_dir = __DIR__ . '../Session';
            return;
        }

        if ($this->is_admin_url) {
            self::$theme_dir = __DIR__ . '/../../themes/admin';
        } else {
            self::$theme_dir = __DIR__ . '/../../themes' . THEME_DIR;
        }

        $config = new Config(self::$theme_dir . '/theme.ini');
        $config->load();
    }

    private static function formatUrl(string $url, bool $trailing_slash): string
    {
        if ($trailing_slash === str_ends_with($url, '/')) {
            return $url;
        }

        if ($trailing_slash) {
            return $url . '/';
        }

        return substr($url, 0, -1);
    }
}
