<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<link rel="stylesheet" href="https://unpkg.com/@yaireo/tagify/dist/tagify.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<div id="content">
    <header>
        <?php if (isset($this->post)): ?>
            <h1>Editing Post: <?= $this->post->getTitle() ?></h1>
        <?php else: ?>
            <h1>New Post:</h1>
        <?php endif ?>
        <div id="controls">
            <a href="<?= $this->getRootUrl() ?>posts" class="red-button">Cancel</a>
            <a onclick="window.location.reload(true)" class="blue-button">Reset</a>
            <a onclick="submit('<?= $this->post?->getUri() ?>')" class="green-button">Save</a>
        </div>
    </header>
    <form>
        <main class="admin-panel">
            <input id="title-input" name="post_title" type="text" placeholder="Title" value="<?= $this->post?->getTitle() ?>" />
            <textarea id="content-input" spellcheck="true"><?= $this->post?->getRawContent() ?></textarea>
        </main>
        <aside class="admin-panel">
            <h2>Excerpt</h2>
            <textarea id="excerpt-input" spellcheck="true" name="post_excerpt"><?= $this->post?->getExcerpt() ?></textarea>
            <h2>Date</h2>
            <input type="datetime-local" value="<?= isset($this->post) ? $this->post->getDate('Y-m-d\TH:i:s') : date('Y-m-d\TH:i:s')?>" name="post_date" required></input>
            <h2>Status</h2>
            <select name="post_status_id">
                <?php foreach (\carlansell94\Liteblog\Lib\PostStatus::cases() as $value => $status): ?>
                    <option value="<?= $value ?>"<?= $status === $this->post?->getStatus() ? ' Selected' : '' ?>><?= $status->toString() ?></option>
                <?php endforeach; ?>
            </select>
            <h2>Categories</h2>
            <input type="text" id="categories" />
            <h2>Tags</h2>
            <input type="text" id="tags" />
        </aside>
    </form>
</div>
<script>
    let easyMDE = new EasyMDE({
        element: document.getElementById('content-input'),
        uploadImage: false,
        toolbar: [
            "undo", "redo", "|",
            "bold", "italic", "heading", "|",
            "code", "quote", "unordered-list", "ordered-list", "|",
            "link", "table"
        ]
    });

    let tagifyTags = new Tagify(document.querySelector('#tags'), {
        editTags    : {
            clicks: 2
        },
        maxTags     : 10,
        backspace    : "edit",
        placeholder  : "Add a tag"
    });

    <?php if ($this->post?->hasTags()): ?>
        tagifyTags.addTags(<?= json_encode(array_values($this->post->getTags())) ?>);
    <?php endif ?>

    <?php
        $cats = array();

        if ($this->categories) {
            foreach ($this->categories as $category) {
                $cats[] = array('value' => $category['category_name'], 'id' => $category['category_id']);
            }
        }
    ?>

    let tagifyCategories = new Tagify(document.querySelector('#categories'), {
        userInput: false,
        whitelist: <?= json_encode($cats) ?>
    });

    <?php if ($this->post?->hasCategories()): ?>
        tagifyCategories.addTags(<?= json_encode(array_values($this->post->getCategories())) ?>);
    <?php endif ?>

    function getUpdatedMeta()
    {
        const {added_tags, removed_tags} = (() => {
            const existing_tags = <?= json_encode(array_values($this->post?->getTags() ?? array())) ?>;
            const input_tags = tagifyTags.value.map((tag) => {
                return `${tag.value}`;
            })

            return {
                added_tags: input_tags.filter(x => !existing_tags.includes(x)),
                removed_tags: existing_tags.filter(x => !input_tags.includes(x))
            }
        })()

        const {added_categories, removed_categories} = (() => {
            const existing_categories = <?= json_encode(array_values($this->post?->getCategories() ?? array())) ?>;
            const input_categories = tagifyCategories.value.map((tag) => {
                return `${tag.value}`;
            })

            return {
                added_categories: input_categories.filter(x => !existing_categories.includes(x)),
                removed_categories: existing_categories.filter(x => !input_categories.includes(x))
            }
        })()

        return {
            added_tags: added_tags,
            removed_tags: removed_tags,
            added_categories: added_categories,
            removed_categories: removed_categories
        }
    }

    async function submit(uri = null)
    {
        if (!isValid()) {
            alert('Highlighted fields must not be empty.');
            return false;
        }

        let data = {
            post_content: easyMDE.value(),
        }

        if (uri) {
            data = {...data, ...getUpdatedMeta()};
        } else {
            data = {
                ...data,
                categories: tagifyCategories.value,
                tags: tagifyTags.value
            };
        }

        const form = document.querySelector('form').querySelectorAll('[name]').forEach(element => {
            data[element.name] = element.value;
        });

        const method = uri ? 'PUT' : 'POST';
        const url = '<?= $this->getRootUrl() ?>post' + ('/' + uri ?? '');
        const status = await sendRequest(url, method, data);

        if (status === 200) {
            alert('The post has been ' + (method == 'PUT' ? 'updated.' : 'created.'));
        } else {
            alert('Unable to ' + (method == 'PUT' ? 'update' : 'create') + ' the post.');
        }

        window.location.reload(false);
    }

    function isValid()
    {
        const requiredInputs = ['post_title', 'post_excerpt', 'post_date'];
        let valid = true;
        
        requiredInputs.forEach(element => {
            input = document.querySelector("[name='" + element + "']");
            
            if (input.value == '') {
                input.style.borderColor = 'red';
                valid = false;
            } else {
                input.style.borderColor = '#f0f0f0';
            }
        });
        
        if (easyMDE.value() == '') {
            valid = false;
        }

        return valid;
    }

    async function sendRequest(uri, method, data)
    {
        const response = await fetch(uri, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
        });

        await response.status;
        console.log(await response.text());

        return response.status;
    }
</script>
