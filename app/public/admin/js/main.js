(function ($) {
    'use strict';

    function collectCategories() {
        var cats = [];

        $('.category_checked').each(function () {
            cats[cats.length] = $(this).attr('data-category-chpu');
        });

        return cats;
    }

    // Фрагмент упрощён из legacy main.js. Сейчас он неверно собирает часть типов полей.
    function collectPropertyValues() {
        var propertyMas = {};

        $('.name_select_rielt').each(function () {
            var propertyId = $(this).attr('data-property');
            var $control = $(this).find('.ag_pole_good');
            if ($control.hasClass('checkbox_property')) {
                var values = [];
                $control.find('input:checked').each(function () {
                    values.push($(this).siblings('.ckeck_param').attr('data-val'));
                });
                var value = values.join(':')
            } else {
                var value = $control.val();
            }


            if (value !== undefined && value !== '') {
                propertyMas[propertyId] = value;
            }
        });

        return propertyMas;
    }

    $('body').on('click', '.addgood_click', function () {
        var $button = $(this);

        $button.prop('disabled', true).text('Проверяем…');

        $.ajax({
            type: 'POST',
            url: './admin/ajax/Preview_Good_Payload.php',
            dataType: 'json',
            data: {
                cats: collectCategories(),
                property_mas: collectPropertyValues()
            },
            success: function (data) {
                $('.js-payload-preview').text(JSON.stringify(data, null, 2));
            },
            error: function () {
                $('.js-payload-preview').text('Не удалось проверить отправку.');
            },
            complete: function () {
                $button.prop('disabled', false).text('Проверить отправку');
            }
        });
    });
}(jQuery));
