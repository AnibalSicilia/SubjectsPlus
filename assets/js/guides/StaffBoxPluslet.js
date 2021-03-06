function staffBox() {

    var myStaffBox = {

        settings: {},
        strings: {},
        bindUiActions: function () {
            myStaffBox.clickCheckboxes();

        },
        init: function () {
            myStaffBox.bindUiActions();
            myStaffBox.setCheckboxes();
        },


        setCheckboxes: function () {
            $(".checkbox_ss").each(function () {
                if ($(this, "input").val() == "Yes") {
                    $(this, "input").prop("checked", true);
                }
            });
        },

        clickCheckboxes: function () {
            $('.checkbox_ss').on('click', function () {
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

    return myStaffBox;
}