<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <title>View Article</title>
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title is-2"><?= esc($article['title']) ?></h1>

            <div class="box">
                <div class="content">
                    <p>
                        <span class="has-text-weight-bold">Author:</span> 
                        <?= esc($article['author']) ?>
                    </p>
                    <p>
                        <span class="has-text-weight-bold">Published Date:</span> 
                        <?= esc($article['published_date']) ?>
                    </p>

                    <div class="mt-5">
                        <h3 class="title is-4">Content:</h3>
                        <p class="is-size-5"><?= esc($article['content']) ?></p>
                    </div>
                </div>
            </div>

            <a href="/articles" class="button is-link mt-4">
                <span class="icon">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span>Back to Articles List</span>
            </a>

            <a href="/articles/edit/<?= esc($article['id']) ?>" class="button is-link mt-4">
                <span class="icon">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span>Edit Article</span>
            </a>
        </div>
    </section>
</body>
</html>