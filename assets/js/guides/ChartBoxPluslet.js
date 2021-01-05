function ChartBox() {

    var myChartBox = {

        settings: {},
        strings: {},
        bindUiActions: function () {
            myChartBox.clickCheckboxes();

        },
        init: function () {
            myChartBox.bindUiActions();
            myChartBox.setCheckboxes();
        },

        setCheckboxes: function () {
            $(".checkbox_cb").each(function () {
                if ($(this, "input").val() == "Yes") {
                    $(this, "input").prop("checked", true);
                }
            });
        },

        clickCheckboxes: function () {
            $('.checkbox_cb').on('click', function () {
                if (($(this).attr('value') == "No") || $(this).attr('value') == "") {
                    $(this).attr('value', 'Yes');
                    $(this, "input").prop("checked", true);
                } else {
                    $(this).attr('value', 'No');
                    $(this, "input").prop("checked", false);
                }
            });
        }
    };

    return myChartBox;
}