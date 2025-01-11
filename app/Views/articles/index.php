<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>

<div class="container mt-5">
    <h1 class="title is-1">Articles</h1>

    <div class="my-5">
        <a href="/articles/create" class="button is-primary is-medium">Create New Article</a>
    </div>

    <form method="get" action="/articles" class="mb-5">
        <div class="field has-addons">
            <div class="control is-expanded">
                <input class="input" type="text" name="search" placeholder="Search articles" value="<?= esc($search) ?>">
            </div>
            <div class="control">
                <button type="submit" class="button is-info">Search</button>
            </div>
        </div>
    </form>

    <div class="content">
        <?php if (empty($articles)): ?>
            <p class="has-text-grey">No articles found.</p>
        <?php else: ?>
            <div class="columns is-multiline">
                <?php foreach ($articles as $article): ?>
                    <div class="column is-12">
                        <div class="box">
                            <div class="level">
                                <div class="level-left">
                                    <div class="level-item">
                                        <a href="/articles/show/<?= esc($article['id']) ?>" class="is-size-4"><?= esc($article['title']) ?></a>
                                        <p class="is-size-6 ml-2"><?= esc($article['published_date']) ?></p>
                                    </div>
                                </div>
                                <div class="level-right">
                                    <div class="level-item buttons">
                                        <a href="/articles/edit/<?= esc($article['id']) ?>" class="button is-warning is-small">Edit</a>
                                        <a href="/articles/delete/<?= esc($article['id']) ?>" class="button is-danger is-small">Delete</a>
                                        <a href="/api/articles/<?= esc($article['id']) ?>" class="button is-info is-small">API</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
        <ul class="pagination-list">
            <?php for ($i = 1; $i <= ceil($totalArticles / $itemsPerPage); $i++): ?>
                <li>
                    <a href="/articles?page=<?= $i ?><?= !empty($search) ? '&search=' . esc($search) : '' ?>" 
                       class="pagination-link <?= $i === (int)($page ?? 1) ? 'is-current' : '' ?>"
                       aria-label="Page <?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

</div>