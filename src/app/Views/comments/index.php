<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Комментарии</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">

    <h1 class="mb-3">Комментарии</h1>

    <!-- Сортировка -->
    <div class="form-row mb-3">
        <div class="col-md-4 mb-2">
            <label for="sortBy">Сортировать по</label>
            <select id="sortBy" class="form-control">
                <option value="id">ID</option>
                <option value="date">Дате</option>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label for="sortDir">Направление</label>
            <select id="sortDir" class="form-control">
                <option value="desc">По убыванию</option>
                <option value="asc">По возрастанию</option>
            </select>
        </div>
    </div>

    <!-- Список -->
    <div id="commentsList"></div>

    <!-- Пагинация -->
    <nav aria-label="Пагинация комментариев">
        <ul class="pagination" id="pagination"></ul>
    </nav>

    <!-- Форма (под списком) -->
    <div class="card mt-4">
        <div class="card-body">
            <h4 class="card-title">Оставить комментарий</h4>
            <form id="commentForm" novalidate>
                <div class="form-group">
                    <label for="name">Email</label>
                    <input type="email" name="name" id="name"
                           class="form-control" required>
                    <div class="invalid-feedback" id="nameError"></div>
                </div>
                <div class="form-group">
                    <label for="date">Дата</label>
                    <input type="text" name="date" id="date"
                           class="form-control" required
                           placeholder="например 13-10-2026">
                </div>
                <div class="form-group">
                    <label for="text">Текст</label>
                    <textarea name="text" id="text"
                              class="form-control" rows="3" required></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Отправить</button>
            </form>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="/js/comments.js"></script>
</body>
</html>