<div id="page-container">
    <article>
        <header>
            <h1><?= $this->post->getTitle() ?></h1>
        </header>
        <div id="meta">
            <img src="<?= self::getThemeAssetsUrl() ?>icons/calendar.svg" alt="calendar" />
            <p><?= $this->post->getDate('jS F Y'); ?></p>
            <?php if ($this->post->hasCategories()): ?>
                <img src="<?= self::getThemeAssetsUrl() ?>icons/categories.svg" alt="categories" />
                <?php foreach($this->post->getCategories() as $slug => $category): ?>
                    <a class="meta-category" href="<?= $this->getRootUrl() ?>category/<?= $slug ?>"><?= $category ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($this->post->hasTags()): ?>
                <img src="<?= self::getThemeAssetsUrl() ?>icons/tags.svg" alt="tags" />
                <?php foreach($this->post->getTags() as $slug => $tag): ?>
                    <a class="meta-tag" href="<?= $this->getRootUrl() ?>tag/<?= $slug ?>"><?= $tag ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <main>
            <?= $this->post->getContent() ?>
        </main>
    </article>
