<div id="content">
    <header>
        <h1>Categories:</h1>
    </header>
    <aside>
        <h2>Add A Category</h2>
        <form>
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" required>
        </form>
        <a class="green-button" name="submit" onclick=addCategoryDialog()>Add Category</a>
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
                    <th>Category Name
                    <th class="shrink-hide">Category Slug
                    <th>Post Count
                    <th class="shrink-hide">Latest Post Date
                    <th>
                </tr>
            </thead>
            <tbody>
            <?php while($category = $this->category_list->fetch_object("\carlansell94\Liteblog\Lib\Category")): ?>
                <tr>
                    <td><?= $category->getName() ?>
                    <td class="shrink-hide"><?= $category->getSlug() ?>
                    <td><?= $category->getPostCount() ?>
                    <td class="shrink-hide"><?= $category->getLatestPost() ?>
                    <td class="controls" style="grid-template-columns: 1fr">
                        <a onclick="deleteCategoryDialog('<?= $category->getUri() ?>')" class="red-button">Delete</a>
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

    async function addCategory(category_name)
    {
        const data = {
            category_name: category_name
        };

        const url = "<?= $this->getRootUrl() ?>category";
        const status = await sendRequest(url, 'POST', data);

        if (status === 200) {
            alert('The category has been created.');
        } else if (status === 409) {
            alert('A category with the specified name already exists.');
        } else {
            alert('Unable to add category, an error has occurred.');
        }

        document.querySelector('dialog').close();
        window.location.reload(false);
    }

    function addCategoryDialog()
    {
        const dialog = document.querySelector('dialog');
        const category_name = document.querySelector('[name="category_name"]').value;
    
        if (category_name === '') {
            alert('Category name cannot be empty.');
            return;
        }

        dialog.querySelector('h1').innerHTML = 'Add Category';
        dialog.querySelector('p').innerHTML = 'Are you sure you want to add the category?';
        dialog.querySelector('[name="submit"]').onclick = () => {
            addCategory(category_name);
        };

        dialog.showModal();
    }

    async function deleteCategory(uri)
    {
        const data = {
            uri: uri
        };

        const url = "<?= $this->getRootUrl() ?>category";
        const status = await sendRequest(url, 'DELETE', data);

        if (status === 200) {
            alert('The category has been deleted.');
        } else {
            alert('Unable to delete category, an error has occurred.');
        }

        document.querySelector('dialog').close();
        window.location.reload(false);
    }

    function deleteCategoryDialog(uri)
    {
        const dialog = document.querySelector('dialog');

        dialog.querySelector('h1').innerHTML = 'Delete Category';
        dialog.querySelector('p').innerHTML = 'Are you sure you want to delete the category ' + uri + '?';
        dialog.querySelector('[name="submit"]').onclick = () => {
            deleteCategory(uri);
        };

        dialog.showModal();
    }
</script>
