$(function () {
    var
        $table = $('#tree-table'),
        rows = $table.find('tr'),
        classUp = 'glyphicon-chevron-right',
        classDown = 'glyphicon-chevron-down';

    rows.each(function (index, row) {
        var
            $row = $(row),
            level = $row.data('level'),
            id = $row.data('id'),
            $columnName = $row.find('td[data-column="name"]'),
            children = $table.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            var expander = $columnName.prepend('' +
                '<span class="treegrid-expander '+classUp+'"><i data-feather="chevron-down" style="width:15px;height:15px;"></i><span>' +
                '');
            feather.replace();
            //children.hide();

            expander.on('click', function (e) {
                var $target = $(e.target);
                if ($target.hasClass(classUp)) {
                    $target
                        .removeClass(classUp)
                        .addClass(classDown);

                    children.show();
                } else {
                    $target
                        .removeClass(classDown)
                        .addClass(classUp);

                    reverseHide($table, $row);
                }
            });
        }

        $columnName.prepend('' +
            '<i class="treegrid-indent" style="width:' + 15 * level + 'px"></i>' +
            '');
    });

    // Reverse hide all elements
    reverseHide = function (table, element) {
        var
            $element = $(element),
            id = $element.data('id'),
            children = table.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            children.each(function (i, e) {
                reverseHide(table, e);
            });

            $element
                .find(classDown)
                .removeClass(classDown)
                .addClass(classUp);

            children.hide();
        }
    };
});