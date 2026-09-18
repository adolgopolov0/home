<?php

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/src/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

// Упрощённая обезличенная копия реального legacy-обработчика.
function property($property, $state = [])
{
    $place = '';
    if ($property['place_prop'] != '') {
        $place = '<div class="field-help">' . $property['place_prop'] . '</div>';
    }

    $idProp = $property['id'];
    $allOption = '';

    if ($property['type_prop'] == '1') {
        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $property['name_prop'] . '</div>
            ' . $place . '
            <input type="text" class="text-input add-inp ag_pole_good" placeholder="' . $property['name_prop'] . '" value="'.(!empty($state) ? $state:'' ).'">
        </div>';
    } elseif ($property['type_prop'] == '2') {
        $answers = db()->query(
            "SELECT * FROM property_answer_s WHERE id_prop = '" . $idProp . "' ORDER BY sort_answer"
        );

        while ($answer = $answers->fetch()) {
            $allOption .= '<option value="' . $answer['id'] . '" '.(($answer['id'] == $state) ? 'selected': '').'>' . $answer['answer_prop'] . '</option>';
        }

        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $property['name_prop'] . '</div>
            ' . $place . '
            <select class="text-input ag_pole_good">
                <option value="">Не выбрано</option>' . $allOption . '
            </select>
        </div>';
    } elseif ($property['type_prop'] == '3') {
        $answers = db()->query(
            "SELECT * FROM property_answer_s WHERE id_prop = '" . $idProp . "' ORDER BY sort_answer"
        );
        $checkboxes = '';
        while ($answer = $answers->fetch()) {
            $checkboxes .= '<label class="choice line_chek">
                <input type="checkbox" '. (in_array($answer['id'],$state) ? 'checked': '').'>
                <span class="ckeck_param" data-val="' . $answer['id'] . '">' . $answer['answer_prop'] . '</span>
            </label>';
        }

        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $property['name_prop'] . '</div>
            ' . $place . '
            <div class="choice-grid checkbox_property ag_pole_good">' . $checkboxes . '</div>
        </div>';
    } else {
        $result = '';
    }

    return $result;
}

$category = isset($_POST['category']) ? $_POST['category'] : array();
$propertyStates = isset($_POST['properties']) ? $_POST['properties'] : array();
$result = '';
$propertiesArray = [];

// Legacy-алгоритм намеренно содержит несколько связанных ошибок.
$where = 'cat_prop = ""';
if (is_array($category) && !empty($category)) {

    foreach ($category as $categoryId) {
        if (is_numeric($categoryId)) {
            $where .= " OR (FIND_IN_SET('".h($categoryId)."',cat_prop) OR cat_prop = '".h($categoryId)."')";
        }
    }
}
if(!empty($where)){
    $properties = db()->query(
        "SELECT * FROM property_s WHERE $where ORDER BY sort_prop"
    );
    foreach ($properties as $property) {
        $result .= property($property, isset($propertyStates[$property['id']]) ? $propertyStates[$property['id']] : []);
    }
}

echo $result === '' ? 'no' : $result;
