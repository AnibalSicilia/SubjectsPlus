<div id="staff-box-form-container">
    <?php
        $settings = array();
            $settings = $this->getShowOrNotElementsArray();
            $this->setShowOrNotElementsArray($settings);
    ?>
    <div class="chart-box-form">

        <h4><?php echo $this->_fname . ' ' . $this->_lname; ?></h4>
        <input type="text" name="ChartBox-staffId<?php echo $this->_staff_id; ?>" value="<?php echo $this->_staff_id; ?>" style="display:none;">
        <br>

        <input class="checkbox_cb" type="checkbox" name="ChartBox-extra-showOrgChart<?php echo $this->_staff_id; ?>" value="<?php echo $this->showOrgChart; ?>"/>
        <label style="display:inline;"> Show Org Chart</label>
        <br>

        <input class="checkbox_cb" type="checkbox" name="ChartBox-extra-showPieChart<?php echo $this->_staff_id; ?>" value="<?php echo $this->showPieChart; ?>">
        <label style="display:inline;"> Show PieChart</label>
        <br>
    </div>
</div>

<script>
    var ss = ChartBox();
    ss.init();
</script>