(function ($) {
    'use strict';

    // Выбор категории в товаре и обновление блока характеристик.
    $('body').on('change', '.js-category', function () {
        var category = [];
        var propState = {};
        var $properties = $('.property_all');

        $(this).closest('.add_good_name_category')
            .toggleClass('category_checked is-selected', this.checked);

        $('.category_checked').each(function () {
            category[category.length] = $(this).attr('data-category-id');
        });

        $properties.addClass('is-loading').attr('aria-busy', 'true');

        $properties.find('.property-field').each(function () {
            var $field = $(this);
            var propertyId = $field.attr('data-property-id');
            var $control = $field.find('.ag_pole_good');
            var values;

            if ($control.hasClass('checkbox_property')) {
                values = [];
                $control.find('input:checked').each(function () {
                    values.push($(this).siblings('.ckeck_param').attr('data-val'));
                });
            } else {
                values = $control.val();
            }

            if (propertyId === undefined || propertyId === '') {
                return;
            }

            if ($.isArray(values)) {
                if (values.length) {
                    propState[propertyId] = values;
                }
                return;
            }

            if (values !== undefined && values !== '') {
                propState[propertyId] = values;
            }
        });


        $.ajax({
            type: 'POST',
            url: './admin/ajax/property/Refresh_Property_Good.php',
            dataType: 'html',
            data: { category: category, properties: propState },
            success: function (data) {
                if (data != 'no') {
                    $properties.html(data);
                } else {
                    $properties.html('');
                }
            },
            error: function () {
                $properties.html('<div class="error-state">Не удалось обновить характеристики.</div>');
            },
            complete: function () {
                $properties.removeClass('is-loading').attr('aria-busy', 'false');
            }
        });
    });
}(jQuery));
