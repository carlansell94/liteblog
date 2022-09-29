<div id="page-container">
    <main id="post-container">
        <?php while ($post = $this->post_list->fetch_object("\carlansell94\Liteblog\Lib\Post")): ?>
            <div>
                <a href="<?= $this->getRootUrl() ?>post/<?= $post->getUri() ?>">
                    <h2><?= $post->getTitle() ?></h2>
                    <p class="excerpt"><?= $post->getExcerpt() ?></p>
                </a>
                <div class="meta">
                    <img src="<?= self::getThemeAssetsUrl() ?>icons/calendar.svg" alt="calendar" />
                    <p class="post-date"><?= $post->getDate() ?></p>
                    <?php if ($post->hasCategories()): ?>
                        <img src="<?= self::getThemeAssetsUrl() ?>icons/categories.svg" alt="categories" />
                        <?php foreach ($post->getCategories() as $slug => $category): ?>
                            <a class="meta-category" href="<?= $this->getRootUrl() ?>category/<?= $slug ?>"><?= $category ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
        <?php endwhile ?>
        <?= $this->getPagination() ?>
    </main>