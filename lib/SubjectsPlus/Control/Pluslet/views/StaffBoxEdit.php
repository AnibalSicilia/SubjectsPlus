<?php
/**
 * Created by PhpStorm.
 * User: cbrownroberts
 * Date: 8/19/16
 * Time: 3:11 PM
 */

?>
<div id="staff-box-form-container">

    <?php
        $settings = array();
        foreach($this->_staffArray as $staff):
            $settings = $this->getShowOrNotElementsArray();
            $this->setShowOrNotElementsArray($settings);
    ?>
    <div class="staff-box-form">

        <h4><?php echo $this->_fname . ' ' . $this->_lname; ?></h4>
        <input type="text" name="StaffBox-staffId<?php echo $this->_staff_id; ?>" value="<?php echo $this->_staff_id; ?>" style="display:none;">
        <br>



        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showPhoto<?php echo $this->_staff_id; ?>" value="<?php echo $this->showPhoto; ?>"/>
        <label style="display:inline;"> Show Photo</label>
        <br>

        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showStaffMain<?php echo $this->_staff_id; ?>" value="<?php echo $this->showStaffMain; ?>">
        <label style="display:inline;"> Show Staff Main</label>
        <br>

        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showAddress<?php echo $this->_staff_id; ?>" value="<?php echo $this->showAddress; ?>"/>
        <label style="display:inline;"> Show Address</label>
        <br>

        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showMap<?php echo $this->_staff_id; ?>" value="<?php echo $this->showMap; ?>" />
        <label style="display:inline;"> Show Map</label>
        <br>

        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showEmergencyContacts<?php echo $this->_staff_id; ?>" value="<?php echo $this->showEmergencyContacts; ?>" />
        <label style="display:inline;"> Show Emergency Contacts</label>
        <br>
        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showSocialMedia<?php echo $this->_staff_id; ?>" value="<?php echo $this->showSocialMedia; ?>" />
        <label style="display:inline;"> Show Social Media</label>
        <br>

        <input class="checkbox_ss" type="checkbox" name="StaffBox-extra-showBioForm<?php echo $this->_staff_id; ?>" value="<?php echo $this->showBioForm; ?>" />
        <label style="display:inline;"> Show Bio Form</label>
        <br>


    </div>
    <?php endforeach; ?>

</div>


<script>
    var ss = staffBox();
    ss.init();
</script>