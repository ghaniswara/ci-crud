<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>


<div class="container">
    <h1 class="title is-1 my-5">Edit Article</h1>

    <form method="POST" action="/articles/update/<?= esc($article['id']) ?>" class="box">
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" id="title" name="title" value="<?= esc($article['title']) ?>" />
            </div>
        </div>

        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" id="content" name="content"><?= esc($article['content']) ?></textarea>
            </div>
        </div>

        <div>
            <label class="label">Author</label>
            <div class="control">
                <input class="input" type="text" id="author" name="author" value="<?= esc($article['author']) ?>" />
            </div>
        </div>

        <div>
            <label for="published_date">Published Date</label>
            <div class="control">
                <input class="input" type="text" id="published_date" name="published_date" value="<?= esc($article['published_date']) ?>" />
            </div>
        </div>

        <div class="field mt-5">
            <div class="control">
                <button type="submit" class="button is-primary">Update Article</button>
            </div>
        </div>
    </form>
</div>