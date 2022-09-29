<aside id="sidebar">
        <section id="categories">
            <h2>Categories</h2>
            <?php if (($categories = $this->getCategories()) !== null): ?>
                <?php foreach ($categories as $category): ?>
                    <?php if ($category->getPostCount() > 0): ?>
                            <a href="<?= $this->getRootUrl() ?>category/<?= $category->getUri() ?>">
                                <p><?= $category->getName() . " (" . $category->getPostCount() . ")" ?></p>
                            </a>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        </section>
        <section>
            <h2>Archive</h2>
            <?php if (($posts = $this->getPosts()) !== null): ?>
                <?php foreach ($posts as $year => $months): ?>
                    <details>
                        <summary><?= $year ?></summary>
                        <?php foreach ($months as $month => $posts): ?>
                        <details>
                            <summary><?= $month . " (" . count($posts) . ")" ?></summary>
                            <ul>
                            <?php foreach ($posts as $post): ?>
                                <li>
                                    <a href="<?= $this->getRootUrl() ?>post/<?= $post->getUri() ?>"><?= $post->getTitle() ?></a>
                                </li>
                            <?php endforeach ?>
                            </ul>
                        </details>
                        <?php endforeach; ?>
                    </details>
                <?php endforeach ?>
            <?php endif ?>
        </section>
        <section>
            <div id="tag-container">
            <?php if (($tags = $this->getTags()) !== null): ?>
                <?php foreach ($tags as $tag): ?>
                    <?php if ($tag->getPostCount() > 0): ?>
                        <a class="meta-tag" href="<?= $this->getRootUrl() ?>tag/<?= $tag->getUri() ?>">
                            <?= $tag->getName() ?>
                        </a>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
            </div>
        </section>
    </aside>
</div>