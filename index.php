<?php include('functions.php') ?>
<!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itilucarelli Biblio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="https://icons.getbootstrap.com/assets/icons/book.svg">
</head>

<body>
    <div class="d-flex justify-content-center">
        <div class="form-check form-switch mt-3">
            <label class="form-check-label ms-3" for="lightSwitch">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-brightness-high" viewBox="0 0 16 16">
                    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
                </svg>
            </label>
            <input class="form-check-input" type="checkbox" id="lightSwitch" />
        </div>
    </div>
    <div class="container mt-3 mb-3">
        <form class="input-group mb-3" id="basic-search">
            <input type="text" id="general-search" class="form-control" placeholder="Ricerca" aria-label="Ricerca" value="<?php echo get_var('q', $_GET); ?>">
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-search" aria-expanded="false" aria-controls="collapse-search">
                <i class="bi bi-funnel"></i>
            </button>

            <button type="submit" class="btn btn-primary" id="button-basic-search"><i class="bi bi-search"></i></button>
        </form>
        <div class="collapse" id="collapse-search">
            <div class="card card-body">
                <form class="needs-validation accordion-body" id="advanced-search" novalidate>
                    <?php print_form_html(file_get_contents('json/form.json'), $_GET); ?>
                    <button type="submit" class="btn btn-primary">Effettua ricerca avanzata</button>
                </form>
            </div>
        </div>
    </div>
    <div class="container d-flex flex-column justify-content-center">
        <div class="table-responsive">
            <table class="table table-hover" id="tabella"></table>
        </div>
        <span class="text-center p-3" id="numero_risultati"></span>
        <span id="paginazione" class="d-flex justify-content-center"></span>
    </div>



    <script>
        function update_parameter(name, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(name, value);
            window.history.replaceState(null, null, url);
        }

        function get_parameter(name) {
            const url = new URL(window.location.href);
            return url.searchParams.get(name);
        }

        $("#basic-search").on("submit", function(event) {
            event.preventDefault();
            load_books_table(get_data_form('basic'));

        });

        Array.from(document.querySelectorAll('.needs-validation')).forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault();
                form.classList.remove('was-validated');

                var checkboxs = [];
                $.each($('.data-form[type="checkbox"]'), function(index, div) {
                    if (checkboxs.indexOf($(div).attr('data-name')) == -1) {
                        checkboxs.push($(div).attr('data-name'));
                    }
                });

                var error = false;
                $.each($(checkboxs), function(index, element) {
                    if ($('.data-form[type="checkbox"][data-name="' + element + '"]:checked').length < 1) {
                        $('.error-text-form-' + element).css('display', 'block');
                        error = true;
                    } else {
                        $('.error-text-form-' + element).css('display', 'none');
                    }

                });
                if (!error) {
                    $.when($('#collapse-search').collapse('hide'))
                        .then(load_books_table(get_data_form('advanced')));
                    $('#generic-search').val('');
                }
            }, false)
        });

        function load_books_table(data = "") {
            $.ajax({
                url: 'data-books.php',
                type: 'post',
                data: data,
                success: function(data) {
                    //console.log(data);
                    output = JSON.parse(data);
                    $("#tabella").html((output.number_result > 0) ? (output.html) : (""));
                    $("#paginazione").html((output.number_result > 0) ? (output.pagination) : (""));
                    $("#numero_risultati").html('Risultati ottenuti: ' + output.number_result);
                },
            });
        }

        function get_data_form(type) {
            data = {};
            
            if (type == 'basic') {
                data['search'] = type;
                data['q'] = $('#general-search').val();
            }

            if (type == 'advanced') {
                data['search'] = type;
                $.each($(".data-form"), function(index, div) {
                    switch ($(div).attr('type')) {
                        case 'search':
                            data[$(div).attr('data-name')] = $(div).val();
                            break;
                        case 'checkbox':
                            if ($(div).is(':checked')) {
                                if (!Array.isArray(data[$(div).attr('data-name')])) {
                                    data[$(div).attr('data-name')] = [];
                                }
                                data[$(div).attr('data-name')].push(div.value)
                            }
                            break;
                        case 'range':
                            data[$(div).attr('data-name')] = $(div).val();
                            break;
                        case 'select':
                            data[$(div).attr('data-name')] = $(div).find(":selected").val();
                            break;
                    }
                });
            }
            data['page'] = (get_parameter('page') != null) ? (get_parameter('page')) : (1);

            window.history.pushState('name', '', window.location.href.split("?")[0]);
            for (var element in data) {
                if (data[element] != '') {
                    update_parameter(element, data[element]);
                }
            }
            return data;
        }

        $(window).on('load', function() {
            load_books_table(get_data_form(get_parameter('search')));
        });

        $(document).on('click', '.page-link', function() {
            update_parameter('page', $(this).data('page_number'));
            load_books_table(get_data_form(get_parameter('search')));
        });
    </script>

    <script src="view-mode.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>