$(function () {
    const perPage = 3;
    let currentPage = 1;

    function loadComments(page) {
        currentPage = page || 1;
        const sort = $('#sortBy').val();
        const dir  = $('#sortDir').val();

        $.getJSON('/comments', { page: currentPage, sort: sort, dir: dir })
            .done(function (res) {
                renderComments(res.items);
                renderPagination(res.page, res.totalPages);
            })
            .fail(function () {
                $('#commentsList').html(
                    '<p class="text-danger">Ошибка загрузки комментариев</p>'
                );
            });
    }

    function renderComments(items) {
        const $wrap = $('#commentsList').empty();

        if (!items || !items.length) {
            $wrap.append('<p class="text-muted">Комментариев пока нет</p>');
            return;
        }

        items.forEach(function (c) {
            const $card = $(`
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-1"></h5>
                        <h6 class="card-subtitle mb-2 text-muted"></h6>
                        <p class="card-text"></p>
                        <button class="btn btn-sm btn-danger delete-btn"
                                data-id="${c.id}">Удалить</button>
                    </div>
                </div>
            `);
            $card.find('.card-title').text(c.name);
            $card.find('.card-subtitle').text(c.date);
            $card.find('.card-text').text(c.text);
            $wrap.append($card);
        });
    }

    function renderPagination(page, totalPages) {
        const $pag = $('#pagination').empty();
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            $pag.append(
                `<li class="page-item ${i === page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                 </li>`
            );
        }
    }

    // смена сортировки — всегда с 1-й страницы
    $('#sortBy, #sortDir').on('change', function () {
        loadComments(1);
    });

    // клик по странице
    $('#pagination').on('click', 'a.page-link', function (e) {
        e.preventDefault();
        loadComments(Number($(this).data('page')));
    });

    // удаление
    $('#commentsList').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Удалить комментарий?')) return;

        $.ajax({
            url: '/comments/' + id,
            type: 'DELETE'
        }).done(function () {
            loadComments(currentPage);
        }).fail(function () {
            alert('Не удалось удалить комментарий');
        });
    });

    // добавление
    $('#commentForm').on('submit', function (e) {
        e.preventDefault();

        const $email = $('#name');
        const $err   = $('#nameError');
        $email.removeClass('is-invalid');
        $err.text('');

        // клиентская валидация email
        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRe.test($email.val())) {
            $email.addClass('is-invalid');
            $err.text('Введите корректный email');
            return;
        }

        $.ajax({
            url: '/comments',
            type: 'POST',
            data: $(this).serialize()
        }).done(function (res) {
            if (res.success) {
                $('#commentForm')[0].reset();
                loadComments(1);
            }
        }).fail(function (xhr) {
            const errors = xhr.responseJSON && xhr.responseJSON.errors;
            if (errors && errors.name) {
                $email.addClass('is-invalid');
                $err.text(errors.name);
            } else {
                alert('Ошибка при отправке');
            }
        });
    });

    loadComments(1);
});