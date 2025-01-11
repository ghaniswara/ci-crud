<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>

<div class="container">
    <h1 class="title is-1 my-5">Create Article</h1>
    
    <form method="post" action="/articles/store" class="box">
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" name="content" required></textarea>
            </div>
        </div>

        <div class="field">
            <label class="label">Author</label>
            <div class="control">
                <input class="input" type="text" name="author" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Published Date</label>
            <div class="control">
                <input class="input" type="date" name="published_date" required>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Save</button>
            </div>
        </div>
    </form>
</div>
