<?php

include('db.php');

$start = 0;
$limit = (isset($_POST['result']) && $_POST['result'] >= 10) ? ($_POST['result']) : (20);
$page = 1;

if (isset($_POST['page'])) {
    if ($_POST['page'] > 1) {
        $start = (($_POST['page'] - 1) * $limit);
        $page = $_POST['page'];
    } else {
        $start = 0;
    }
} else {
    $start = 0;
    $page = 1;
}

$sql = "
    SELECT
        libro.isbn AS isbn,
        libro.id AS id_libro,
        dati_libro.id AS id_copia,
        libro.titolo AS titolo,
        genere.nome AS genere,
        editore.nome AS editore,
        dati_libro.codice AS codice,
        scaffale.nome AS scaffale,
        dati_libro.ripiano AS ripiano,
        condizione.nome AS condizione,
        luogo.nome AS luogo,
        dati_libro.stato AS stato,
        libro.visibile AS visibile,
        GROUP_CONCAT(DISTINCT autore.nome SEPARATOR ', ') AS autori,
        COUNT(DISTINCT dati_libro.id) AS copie
    FROM
        libro libro
    LEFT JOIN genere genere ON
        libro.id_genere = genere.id
    LEFT JOIN editore editore ON
        libro.id_editore = editore.id
    LEFT JOIN dati_libro dati_libro ON
        dati_libro.id_libro = libro.id AND dati_libro.visibile = 1
    LEFT JOIN scaffale scaffale ON
        dati_libro.id_scaffale = scaffale.id
    LEFT JOIN autori_libro autori_libro ON
        autori_libro.id_libro = libro.id
    LEFT JOIN autore autore ON
        autori_libro.id = autore.id
    LEFT JOIN condizione condizione ON
        dati_libro.id_condizione = condizione.id
    LEFT JOIN luogo luogo ON
        dati_libro.id_condizione = condizione.id
    WHERE TRUE
";

if (isset($_POST['search']) && $_POST['search'] == 'basic') {

    $columns = array("libro.titolo", "autore.nome", "libro.isbn", "genere.nome", "editore.nome");
    if (isset($_POST['q']) && $_POST['q'] != '') {
        $sql .= ' AND (';
        foreach ($columns as $index => $column) {
            $sql .= ($index != 0) ? (" OR ") : ("");
            $sql .= $column . ' LIKE "%' . str_replace(' ', '%', str_replace('"', '\"', $_POST['q'])) . '%"';
        }
        $sql .= ') ';
    }
    $sql .= ' GROUP BY libro.id ORDER BY libro.id ASC';
} else if (isset($_POST['search']) && $_POST['search'] == 'advanced') {
    $file_json = 'json/form.json';
    $json_form = json_decode(file_get_contents($file_json), true);
    foreach ($json_form as $key => $column) {
        switch ($column['type']) {
            case 'search':
                $sql .= (isset($column['column']) && isset($_POST[$column['name']]) && $_POST[$column['name']] != '') ? (' AND ' . $column['column'] . '' . ' LIKE "%' . str_replace(' ', '%', str_replace('"', '\"', $_POST[$column['name']])) . '%"') : ("");
                break;
            case 'checkbox':
                if (isset($column['column']) && isset($_POST[$column['name']]) && $_POST[$column['name']] != '') {
                    $sql .= ' AND (';
                    foreach ($_POST[$column['name']] as $key => $condition) {
                        $sql .= (($key == 0) ? ('') : (' OR ')) . $column['column'] . ' = ' . $condition;
                    }
                    $sql .= ')';
                }
                break;
            case 'select':
                foreach ($column['item'] as $option) {
                    if ($option['id'] == $_POST[$column['name']]) {
                        $sql .= ' GROUP BY libro.id ORDER BY ' . $option['column'];
                        break;
                    }
                }
                break;
        }
    }
} else {
    $sql .= ' GROUP BY libro.id ORDER BY libro.id ASC';
}
$filter_query = $sql . ' LIMIT ' . $start . ', ' . $limit . '';

$result = mysqli_query($conn, $sql);
$result_filter = mysqli_query($conn, $filter_query);
$number_result = mysqli_num_rows($result);

$html = '';
$file_json = 'json/table.json';
$json_table = json_decode(file_get_contents($file_json), true);
$html .= '<thead><tr>';
foreach ($json_table as $key => $column) {
    $html .= '<th scope="col">' . $column['title'] . '</th>';
}
$html .= '</tr></thead>';
$html .= '<tbody>';
while ($libro = mysqli_fetch_assoc($result_filter)) {
    $html .= '<tr>';
    foreach ($json_table as $key => $column) {
        $html .= '<td class="text-center">' . ((!is_null($libro[$column['column']])) ? ($libro[$column['column']]) : ('#')) . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</tbody>';
$pagination = "";

if ($number_result > 0) {

    $pagination = '<ul class="pagination d-flex justify-content-center mt-3">';
    $total_links = ceil($number_result / $limit);
    $previous_link = '';
    $next_link = '';
    $page_link = '';
    if ($total_links > 4) {
        if ($page < 5) {
            for ($count = 1; $count <= 5; $count++) {
                $page_array[] = $count;
            }
            $page_array[] = '...';
            $page_array[] = $total_links;
        } else {
            $end_limit = $total_links - 5;
            if ($page > $end_limit) {
                $page_array[] = 1;
                $page_array[] = '...';
                for ($count = $end_limit; $count <= $total_links; $count++) {
                    $page_array[] = $count;
                }
            } else {
                $page_array[] = 1;
                $page_array[] = '...';
                for ($count = $page - 1; $count <= $page + 1; $count++) {
                    $page_array[] = $count;
                }
                $page_array[] = '...';
                $page_array[] = $total_links;
            }
        }
    } else {
        for ($count = 1; $count <= $total_links; $count++) {
            $page_array[] = $count;
        }
    }
    for ($count = 0; $count < count($page_array); $count++) {
        if ($page == $page_array[$count]) {
            $page_link .= '
                    <li class="page-item active">
                        <a class="page-link" href="javascript: void(0)">' . $page_array[$count] . ' <span class="sr-only"></span></a>
                    </li>
                ';
            $previous_id = $page_array[$count] - 1;
            if ($previous_id > 0) {
                $previous_link = '
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    ';
            } else {
                $previous_link = '
                        <li class="page-item disabled">
                            <a class="page-link" href="javascript: void(0)">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    ';
            }
            $next_id = $page_array[$count] + 1;
            if ($next_id > $total_links) {
                $next_link = '
                        <li class="page-item disabled">
                            <a class="page-link" href="javascript: void(0)">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    ';
            } else {
                $next_link = '
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    ';
            }
        } else {
            if ($page_array[$count] == '...') {
                $page_link .= '
                        <li class="page-item disabled">
                            <a class="page-link" href="javascript: void(0)">...</a>
                        </li>
                    ';
            } else {
                $page_link .= '
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a>
                        </li>
                    ';
            }
        }
    }
    $pagination .= $previous_link . $page_link . $next_link;
    $pagination .= '</ul>';
}

$data = array(
    "html" => $html,
    "number_result" => $number_result,
    "pagination" => $pagination
);

echo json_encode($data);
