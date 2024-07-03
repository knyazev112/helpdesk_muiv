$(document).ready(function () {
    if (location.pathname == '/settings/') {
        $('#nav-link-home').removeClass('active');
        $('#nav-link-settings').addClass('active');
    }
    if (location.pathname == '/users/') {
        $('#nav-link-home').removeClass('active');
        $('#nav-link-users').addClass('active');
    }
    $('#btn-create-ticket').click(function (e) {
        e.preventDefault();
        if (!$("#container-show-tickets").hasClass('hidden')) {
            $("#container-show-tickets").addClass('hidden');
        }
        if ($("#container-new-ticket").hasClass('hidden')) {
            $("#container-new-ticket").removeClass('hidden');
        } else {
            $("#container-new-ticket").addClass('hidden');
        }
    });
    $('#btn-all-ticket').click(function (e) {
        e.preventDefault();
        if (!$("#container-new-ticket").hasClass('hidden')) {
            $("#container-new-ticket").addClass('hidden');
        }
        if ($("#container-show-tickets").hasClass('hidden')) {
            $("#container-show-tickets").removeClass('hidden');
        } else {
            $("#container-show-tickets").addClass('hidden');
        }
    });
    if ($('#theme-name').length) {
        $('#theme').val($('#theme-name')[0].dataset.id);
    }
    $('.themes-edit:button').click(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'delete_themes', id: e.currentTarget.dataset.id }, function (data) {
            location.reload();
        });
    });
    $('.button-add-user-to-theme').click(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'add_user_to_theme', id: e.currentTarget.dataset.idtheme, username: $('#input-user-to-theme-' + e.currentTarget.dataset.idtheme).val() }, function (data) {
            if (data == 'success') {
                location.reload();
            } else {
                $('.toast-body').empty().html(data);
                const toastLiveExample = document.getElementById('liveToast');
                const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
                toastBootstrap.show();
            }
        });
    });
    $('.del-user-from-theme').click(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'delete_user_from_theme', iduser: e.currentTarget.dataset.iduser, idtheme: e.currentTarget.dataset.idtheme }, function (data) {
            location.reload();
        });
    });
    $('[data-col="name"]').focusout(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'update_themes_name', id: e.currentTarget.dataset.id, name: e.currentTarget.value }, function (data) {
            const toastLiveExample = document.getElementById('liveToast');
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
            toastBootstrap.show();
        });
    });
    $('[data-col="info"]').focusout(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'update_themes_info', id: e.currentTarget.dataset.id, info: e.currentTarget.value }, function (data) {
            const toastLiveExample = document.getElementById('liveToast');
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
            toastBootstrap.show();
        });
    });
    $('#button-theme-add').click(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'add_theme' }, function (data) {
            location.reload();
        });
    });
    $('#change-theme').click(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'change_theme', id:$.urlParam('id'), theme:$('#theme').val()}, function (data) {
            location.reload();
        });
    });
    $('#create-ticket').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        $.each($("#form-file")[0].files, function (key, input) {
            formData.append('file[]', input);
        });
        formData.append('func', 'create_ticket');
        formData.append('subject', $('#heading').val());
        formData.append('theme', $('#theme').val());
        formData.append('message', $('#message').val());
        $.ajax({
            type: "POST",
            url: '/core/process.php',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            complete: function (data) {
                var r = data.responseText.split(' ');
                if (r[0] == 'success') {
                    location.href = '/ticket/?id=' + r[1];
                } else {
                    $('#create_ticket_error').html(data);
                }
            }
        });
    });
    $('#theme').change(function () {
        $("#theme-info-li").children().addClass("hidden");
        $('#theme-info-' + $(this).val()).removeClass('hidden');
    });

    $('#reply').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        $.each($("#form-file")[0].files, function (key, input) {
            formData.append('file[]', input);
        });
        formData.append('func', 'reply');
        formData.append('ticket', $.urlParam('id'));
        formData.append('text', $('#reply-text').val());
        $.ajax({
            type: "POST",
            url: '/core/process.php',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            complete: function (data) {
                location.reload();
            }
        });
    });
    $('#reply-and-close').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        $.each($("#form-file")[0].files, function (key, input) {
            formData.append('file[]', input);
        });
        formData.append('func', 'reply');
        formData.append('ticket', $.urlParam('id'));
        formData.append('text', $('#reply-text').val());
        $.ajax({
            type: "POST",
            url: '/core/process.php',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            complete: function (data) {
                $.post('/core/process.php', { func: 'close_ticket', ticket: $.urlParam('id') }, function (data) {
                    if (data == 'success') {
                        location.reload();
                    }
                });
            }
        });
    });

    $('#auth').submit(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'auth', name: $("input[name='user']").val(), password: $("input[name='password']").val() }, function (data) {
            if (data == 'success') {
                console.log(data);
                location.reload();
            } else {
                $('#alerts').html('<div class="alert alert-danger" role="alert">' + data + '</div>');
                $('#alerts').delay(800).fadeOut('normal', function () {
                    $('#alerts').html('').fadeIn('0');
                });
            }
        });
    });

    $('#no_longer_help').click(function (e) {
        e.preventDefault();
        $.post('/core/process.php', { func: 'no_longer_help', ticket: $.urlParam('id') }, function (data) {
            if (data == 'success') {
                location.reload();
            }
        });
    });

    $('#close_ticket').click(function (e) {
        $.post('/core/process.php', { func: 'close_ticket', ticket: $.urlParam('id') }, function (data) {
            if (data == 'success') {
                location.reload();
            }
        });
    });

});

$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
}

