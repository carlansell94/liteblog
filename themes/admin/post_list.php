<div id="content">
    <header>
        <h1>Posts:</h1>
        <div id="controls">
            <a class="grey-button" href="<?= $this->getRootUrl() ?>post">New Post</a>
        </div>
    </header>
    <main>
        <table>
            <col width="110px">
            <col>
            <col>
            <col class="shrink-hide">
            <col class="shrink-hide" width="100px">
            <col max-width="210px">
            <thead>
                <tr>
                    <th>Post Date
                    <th>Title
                    <th>Excerpt
                    <th class="shrink-hide">Categories
                    <th class="shrink-hide">Status
                    <th>
                </tr>
            </thead>
            <tbody>
            <?php while ($post = $this->post_list->fetch_object("\carlansell94\Liteblog\Lib\Post")): ?>
                <tr>
                    <td><?= $post->getDate('Y-m-d') ?>
                    <td><?= $post->getTitle() ?>
                    <td><?= $post->getExcerpt(true) ?>
                    <td class="shrink-hide">
                        <?php if ($post->hasCategories()): ?>
                        <?php foreach($post->getCategories() as $category): ?>
                            <div class="tags"><?= $category ?></div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    <td class="shrink-hide"><?= $post->getStatus()->toString() ?>
                    <td>
                        <div class="controls">
                            <a href="<?= $this->getRootUrl() ?>post/<?= $post->getUri() ?>" class="green-button">Edit</a>
                            <a onclick="showStatusDialog('<?= $post->getUri() ?>', '<?= $post->getTitle(true) ?>', <?= $post->getStatus()->value ?>)" class="blue-button">
                                <?= $post->getStatus()->toAction() ?>
                            </a>
                            <a onclick="showDeleteDialog('<?= $post->getUri() ?>', '<?= $post->getTitle(true) ?>')" class="red-button">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile ?>
            </tbody>
        </table>
        <?= $this->getPagination() ?>
    </main>
</div>
<dialog id="deletePostDialog">
    <header>
        <h1>Delete Post</h1>
    </header>
    <p></p>
    <div class="controls">
        <a class="red-button" name="cancel" onclick=deletePostDialog.close()>Cancel</button>
        <a class="green-button" name="submit">Confirm</a>
    </div>
</dialog>
<script>
    async function changeStatus(postId, postTitle, change)
    {
        const url = "<?= $this->getRootUrl() ?>post/" + postId + "/status";
        const status = await sendRequest(url, 'PUT', '');

        if (status === 200) {
            let messageText = "The post '" + postTitle + "' has been ";
            change === 0 ? messageText += "published." : messageText += "converted to a draft.";

            let submit = document.getElementById('deletePostDialog').querySelectorAll('[name=submit]')[0];

            document.querySelectorAll('dialog')[0]
                .getElementsByTagName('p')[0].innerHTML = messageText;

            submit.innerHTML = 'Ok';
            submit.onclick = function () {
                window.location.reload(false);
            }
            submit.style.display = 'inline-block';
        } else {
            alert("An error occurred, please try again later.");
        }
    };

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

    async function deletePost(postId, postTitle) {
        const url = "<?= $this->getRootUrl() ?>post/" + postId;
        const status = await sendRequest(url, 'DELETE', '');

        if (status === 200) {
            const dialog = document.querySelector('dialog');
            const submit = dialog.querySelector('[name=submit]');

            dialog.querySelector('p').innerHTML = "The post '" + postTitle + "' has been deleted.'";
            dialog.querySelector('[name=cancel]').style.visibility = 'hidden';

            submit.innerHTML = 'Ok';
            submit.onclick = function () {
                deletePostDialog.close();
                window.location.reload(false);
            }
            submit.style.display = 'inline-block';
        } else {
            alert("An error occurred, please try again later.");
        }
    }

function setDialogData(headerText, messageText, onClickFunction)
{
    const dialog = document.querySelectorAll('dialog')[0];
    const message = dialog.getElementsByTagName('p')[0];
    const submit = dialog.querySelectorAll('[name=submit]')[0];
    const header = dialog.getElementsByTagName('h1')[0];

    header.innerHTML = headerText;
    message.innerHTML = messageText;
    submit.onclick = onClickFunction;

    return dialog;
}

function showStatusDialog(postId, postTitle, change)
{
    let headerText, messageText;

    if (change === 0) {
        headerText = "Publish Post:";
        messageText = "Are you sure you want to publish the post '" + postTitle + "'?";
    } else {
        headerText = "Convert To Draft:";
        messageText = "Are you sure you want to convert the post '" + postTitle + "' to a draft?";
    }

    const onClickFunction = function() {
        changeStatus(postId, postTitle, change)
    };

    const dialog = setDialogData(headerText, messageText, onClickFunction);
    dialog.showModal();
}

function showDeleteDialog(postId, postTitle) {
    const messageText = "Are you sure you want to delete the post '" + postTitle + "?'";
    const headerText = "Delete Post:";
    const onClickFunction = function() {
        deletePost(postId, postTitle)
    }

    const dialog = setDialogData(headerText, messageText, onClickFunction);
    dialog.showModal();
}
</script>
