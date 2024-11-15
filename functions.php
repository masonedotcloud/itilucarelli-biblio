<?php


function get_var($name, $_DATA)
{
    return (isset($_DATA) && isset($_DATA[$name])) ? (strval($_DATA[$name])) : ("");
}

function print_form_html($json_string, $_DATA = null)
{
    include_once('db.php');
    function data_checkbox_sql($column, $conn)
    {
        $sql = "SELECT id, nome, visibile FROM " . $column . " WHERE visibile = 1";
        $result = mysqli_query($conn, $sql);
        $array = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($array, $row);
        }
        return $array;
    }

    function addon_html($name, $title)
    {
        return '<span class="input-group-text" id="basic-addon-' . strtolower($name) . '">' . $title . '</span>';
    }

    $json_setting_form = json_decode($json_string, true);
    foreach ($json_setting_form as $element) {
        $html = '<div class="input-group mb-3">';
        switch ($element['type']) {
            case 'search':
                $value = get_var($element['name'], $_DATA);
                $html .= addon_html($element['name'], $element['title']);
                $html .= '<input data-name="' . $element['name'] . '" value="' . $value . '" id="' . str_replace(" ", "-", strtolower($element['name'])) . '" type="' . $element['type'] . '" class="form-control data-form" aria-label="' . $element['name'] . '" aria-describedby="basic-addon-' . strtolower($element['title']) . '">';
                break;
            case 'checkbox':
                $html .= addon_html($element['name'], $element['title']);
                $checkboxs = (isset($element['item']['sql']) && $element['item']['sql'] == 'true') ? (data_checkbox_sql($element['item']['column'], $conn)) : ($element['item']);
                $html .= '<div class="form-control" aria-describedby="basic-addon-' . $element['name'] . '">';
                $value = (get_var($element['name'], $_DATA) != "") ? (explode(',', get_var($element['name'], $_DATA))) : (null);
                foreach ($checkboxs as $checkbox) {
                    $status = ($value == null) ? ("checked") : ((in_array($checkbox['id'], $value)) ? ("checked") : (""));
                    $html .= '<div class="form-check form-check-inline">';
                    $html .= '<input data-name="' . $element['name'] . '" class="form-check-input data-form" type="checkbox" value="' . $checkbox['id'] . '" ' . $status . '>';
                    $html .= '<label class="form-check-label">' . $checkbox['nome'] . '</label>';
                    $html .= '</div>';
                }
                $html .= '<div class="invalid-feedback error-text-form-' . $element['name'] . '">Almeno un opzione deve essere selezionata</div>';
                $html .= '</div>';
                break;
            case 'range':
                $value = (get_var($element['name'], $_DATA) != "") ? (get_var($element['name'], $_DATA)) : ($element['value']);
                $html .= addon_html($element['name'], $element['title']);
                $html .= '<div class="form-control d-flex" aria-describedby="basic-addon-' . $element['name'] . '">';
                $html .= '<input data-name="' . $element['name'] . '" type="range" min="' . $element['min'] . '" max="' . $element['max'] . '" value="' . $value . '" class="data-form form-range" id="' . $element['type'] . '-' . $element['name'] . '" oninput="document.getElementById(\'' . $element['type'] . '-' . $element['name'] . '-output\').innerHTML = this.value;">';
                $html .= '<span id="' . $element['type'] . '-' . $element['name'] . '-output" class="ms-3">' . $value . '</span>';
                $html .= '</div>';
                break;
            case 'select':
                $html .= addon_html($element['name'], $element['title']);
                $html .= '<select data-name="' . $element['name'] . '" type="select" class="form-select data-form" aria-label="' . $element['title'] . '" aria-describedby="basic-addon-' . $element['name'] . '" id="' . $element['name'] . '">';
                foreach ($element['item'] as $option) {
                    $html .= ($option['visibile']) ? ('<option value="' . $option['id'] . '" ' . (($option['id'] == get_var($element['name'], $_DATA)) ? ("selected") : ("")) . '>' . $option['nome'] . '</option>') : ("");
                }
                $html .= '</select>';
                break;
        }
        $html .= '</div>';
        echo $html;
    }
}
