<div id="content">
    <header>
        <h1>Tags:</h1>
    </header>
    <aside>
        <h2>Add A Tag</h2>
        <form>
            <label for="tag_label">Tag Name:</label>
            <input type="text" name="tag_label" required>
        </form>
        <a class="green-button" name="submit" onclick=addTagDialog()>Add Tag</a>
    </aside>
    <main>
        <table>
            <col>
            <col class="shrink-hide">
            <col>
            <col class="shrink-hide">
            <col width="40px">
            <thead>
                <tr>
                    <th>Tag Name
                    <th class="shrink-hide">Tag Slug
                    <th>Post Count
                    <th class="shrink-hide">Latest Post Date
                    <th>
                </tr>
            </thead>
            <tbody>
            <?php while($tag = $this->tag_list->fetch_object("\carlansell94\Liteblog\Lib\Tag")): ?>
                <tr>
                    <td><?= $tag->getName() ?>
                    <td class="shrink-hide"><?= $tag->getSlug() ?>
                    <td><?= $tag->getPostCount() ?>
                    <td class="shrink-hide"><?= $tag->getLatestPost() ?>
                    <td class="controls" style="grid-template-columns: 1fr">
                        <a onclick="deleteTagDialog('<?= $tag->getUri() ?>')" class="red-button">Delete</a>
                    </td>
                </tr>
                <?php endwhile ?>
            </tbody>
        </table>
        <?= $this->getPagination() ?>
    </main>
</div>
<dialog id="deleteDialog">
    <header>
        <h1></h1>
    </header>
    <p></p>
    <div class="controls">
        <a class="red-button" name="cancel" onclick=deleteDialog.close()>Cancel</button>
        <a class="green-button" name="submit">Confirm</a>
    </div>
</dialog>
<script>
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

    async function addTag(tag_label)
    {
        const data = {
            tag_label: tag_label
        };

        const url = "<?= $this->getRootUrl() ?>tag";
        const status = await sendRequest(url, 'POST', data);

        if (status === 200) {
            alert('The tag has been created.');
        } else if (status === 409) {
            alert('A tag with the specified name already exists.');
        } else {
            alert('Unable to add tag, an error has occurred.');
        }

        document.querySelector('dialog').close();
        window.location.reload(false);
    }

    function addTagDialog()
    {
        const dialog = document.querySelector('dialog');
        const tag_label = document.querySelector('[name="tag_label"]').value;
    
        if (tag_label === '') {
            alert('Tag name cannot be empty.');
            return;
        }

        dialog.querySelector('h1').innerHTML = 'Add Tag';
        dialog.querySelector('p').innerHTML = 'Are you sure you want to add the tag?';
        dialog.querySelector('[name="submit"]').onclick = () => {
            addTag(tag_label);
        };

        dialog.showModal();
    }

    async function deleteTag(uri)
    {
        const data = {
            uri: uri
        };

        const url = "<?= $this->getRootUrl() ?>tag";
        const status = await sendRequest(url, 'DELETE', data);

        if (status === 200) {
            alert('The tag has been deleted.');
        } else {
            alert('Unable to delete tag, an error has occurred.');
        }

        document.querySelector('dialog').close();
        window.location.reload(false);
    }

    function deleteTagDialog(uri)
    {
        const dialog = document.querySelector('dialog');

        dialog.querySelector('h1').innerHTML = 'Delete Tag';
        dialog.querySelector('p').innerHTML = 'Are you sure you want to delete the tag ' + uri + '?';
        dialog.querySelector('[name="submit"]').onclick = () => {
            deleteTag(uri);
        };

        dialog.showModal();
    }
</script>
