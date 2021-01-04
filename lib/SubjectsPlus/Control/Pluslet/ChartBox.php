<?php
namespace SubjectsPlus\Control;
require_once("Pluslet.php");

class Pluslet_ChartBox extends Pluslet {
    
	protected $_staff_id;
	protected $_lname;
	protected $_fname;
	protected $_title;
	private $_department_id;
    private $_email;
	private $_user_type_id;
	private $_bio;
    private $_fullname;
	protected $_supervisor_id;
	//new sp4
	private $_social_media;

	public $_ok_departments = array();   
    protected $_showOrNotKeys = ['Photo', 'StaffMain', 'Address', 'Map', 'EmergencyContacts', 'SocialMedia', 'Permissions', 'Password', 'SaveChanges'];
        
    public function __construct($pluslet_id, $flag="", $subject_id, $isclone=0) {
        parent::__construct($pluslet_id, $flag, $subject_id, $isclone);

        $this->_type = "ChartBox"; 
        
        $this->_staff_id = self::getStaffId($subject_id);
        $this->_subject_id = $subject_id;
        $this->_pluslet_id = $pluslet_id;  


        $this->_staffArray = self::populateFields();
                if(strlen($this->_pluslet_id) > 0) {         
            $_tmp = self::getDatafromExtraAndBody_ColumnsInPlusletTable();

        $num_rows = count ( $_tmp );
            if($num_rows > 0) {
                // $this->_extra_array = json_decode($_tmp[0]['extra'], true);
                $this->_bio = $_tmp[0]['body'];
            }
        }
        else {
            // $this->_extra_array = json_decode($this->_extra, true);
            $this->_bio = "";
        }
    }
    
    function getDatafromExtraAndBody_ColumnsInPlusletTable() {
        $db = new Querier();
        $q = "select body, extra from pluslet where pluslet_id = " . $this->_pluslet_id;
        $r = $db->query($q);
        return $r;
    }

    function getStaffId($subject_id) {
        $q = "Select staff_id from staff_subject where subject_id = " . $subject_id;
        $db = new Querier ();
        $r = $db->query ( $q );
        $num_rows = count ( $r );
        if($num_rows > 0) {
            return $r[0]['staff_id'];
        }
        return 0;
    }
    
    function populateFields() {

        $db = new Querier;
            $q1 = "select staff_id, lname, fname, title, department_id, email, active, bio, position_number, job_classification, supervisor_id, social_media, extra
                from staff where active = 1 and staff_id = " . $this->_staff_id;
        $staffArray = $db->query( $q1 );

        if ( $staffArray == false ) {
                $this->_message = "There is no active record with that ID.  Why not create a new one?";
        } else {
                $this->_lname    = $staffArray[0]['lname'];
                $this->_fname    = $staffArray[0]['fname'];
                $this->_fullname = $this->_fname . " " . $this->_lname;
                $this->_title    = $staffArray[0]['title'];

                //$this->_department_id = $staffArray[0]['department_id'];
                $this->_email        = $staffArray[0]['email'];

                $this->_bio          = $staffArray[0]['bio'];

                //if ( $full_record == true ) {
                        //$this->_password = $staffArray[0]['password'];

                //New for UM
                  $this->_supervisor_id  = $staffArray[0]['supervisor_id'];
                  

                //new for sp4
                $this->_extra        = $staffArray[0]['extra'];
        }       
    }
    
    protected function onViewOutput() {
       $output = $this->outputChartPluslet();
       $this->_body = $output;
    }
    
    protected function onEditOutput() {
        $pluslet_body = $this->_body;
        $output = $this->selectChartType();
        $this->_body = $output;

        $_ckeditor_body = "<p>" . _( "Please only include professional details." ) . "</p><br />";
        $_ckeditor_body .= self::getCkEditorForOutputBioForm($pluslet_body);

        $this->_body .= $_ckeditor_body;
    }

    private function selectChartType() {

        $this->_hierarchicalArray = "<select>
                                    <option value=\"\" disabled selected>Select</option>
                                    <option value=\"0\">Org Chart</option>
                                    </select>";  
        $_body = "<div class=\"pluslet no_overflow\">
                <div class=\"pluslet_body chart-ol chart-pseudo-root chart-pseudo-body\" style=\"padding:0\">                         
                <div class=\"show-staff-main pure-form\" id=\"dropdown_cb\"><label for=\"supervisor\">" . _( "Charts: " ) . "</label>{$this->_hierarchicalArray}</div>";
        $_body .=  "</div></div>";

        return $_body;
    }

    function getCkEditorForOutputBioForm($pluslet_body) {

        global $CKPath;
        global $CKBasePath;

         include( $CKPath );
         global $BaseURL;

        $_outputBio = "";

        $oCKeditor = new CKEditor( $CKBasePath );
        $oCKeditor->timestamp = time();
        $oCKeditor->returnOutput = true;
        $config['toolbar'] = 'TextFormat';
        $config['height'] = '300';
        $config['filebrowserUploadUrl'] = $BaseURL . "ckeditor/php/uploader.php";
            
        $_outputBio = $oCKeditor->editor( "editor-chart", $pluslet_body, $config );
        return $_outputBio;
    }

    function outputChartPluslet() {
        // start form
        $_body = self::buildStaffFormBody();

        return $_body;
    }
    
    function buildStaffFormBody() {

        $db = new Querier;
            $q1 = "select staff_id, lname, fname, title,  active, supervisor_id, social_media
                    from staff where active = 1 and supervisor_id = " . $this->_supervisor_id;
            $staffArray = $db->query( $q1 );
             
        $_body = "<div class=\"pluslet no_overflow\">
                   <div class=\"pluslet_body chart-ol chart-pseudo-root chart-pseudo-body\" style=\"padding:0\">
                   <div>";

                    $supervisor = self::getSupervisor($this->_supervisor_id);
                    $r_count = count($supervisor);
                    $_body .= "<h1  class=\"chart-level-1 chart-rectangle\">
                        <div>" . $supervisor[0]['fullname'] . "</div>
                        <div style=\"font-weight: normal; padding-top:5px; font-size:.9em;\">". $supervisor[0]['title'] . "</div>     
                    </h1>";
                    
                    $_body .= "<ol class=\"chart-level-2-wrapper\">";
                   for ($i = 0; $i < count($staffArray) - 1; $i=$i+2)  {  
                            
                      $_body .= "<li>
                                    <h2 class=\"chart-level-2 chart-rectangle\">
                                    <div>" . $staffArray[$i]['fname'] . " " . $staffArray[$i]['lname'] . "</div>
                                    <span>". $staffArray[$i]['title'] . "</span>
                                    </h2>";
                                    $dbi = new Querier;
                                    $q1i = "select staff_id, lname, fname, title,  active, supervisor_id, social_media
                                            from staff where active = 1 and supervisor_id = " . $staffArray[$i]['staff_id'];
                                    $tmpArray = $dbi->query( $q1i );
                                    if(count($tmpArray) > 0) {
                                        for ($x = 0; $x < count($tmpArray) - 1; $x=$x+2)  {  
                                         $_body .= "<ol class=\"chart-level-3-wrapper\">
                                                      <li>
                                                        <h3 class=\"chart-level-3 chart-rectangle\">
                                                        <div>" . $tmpArray[$x]['fname'] . " " . $tmpArray[$x]['lname'] . "</div>
                                                        <span>". $tmpArray[$x]['title'] . "</span>                                                    
                                                        </h3>
                                                      </li>
                                                      <li>
                                                        <h3 class=\"chart-level-3 chart-rectangle\">
                                                        <div>" . $tmpArray[$x+1]['fname'] . " " . $tmpArray[$x+1]['lname'] . "</div>
                                                        <span>". $tmpArray[$x+1]['title'] . "</span>                                                    
                                                        </h3>
                                                      </li>
                                                    </ol>";   
                                        }
                                    }
                                     $_body .= "</li>
                                  <li>
                                    <h2 class=\"chart-level-2 chart-rectangle\">
                                    <div>" . $staffArray[$i+1]['fname'] . " " . $staffArray[$i+1]['lname'] . "</div>
                                    <span>". $staffArray[$i+1]['title'] . "</span>
                                    </h2>";
                                    $dbi = new Querier;
                                    $q1i = "select staff_id, lname, fname, title,  active, supervisor_id, social_media
                                            from staff where active = 1 and supervisor_id = " . $staffArray[$i + 1]['staff_id'];
                                    $tmpArray = $dbi->query( $q1i );
                                    if(count($tmpArray) > 0) {
                                        for ($x = 0; $x < count($tmpArray) - 1; $x=$x+2)  {  
                                         $_body .= "<ol class=\"chart-level-3-wrapper\">
                                                      <li>
                                                        <h3 class=\"chart-level-3 chart-rectangle\">
                                                        <div>" . $tmpArray[$x]['fname'] . " " . $tmpArray[$x]['lname'] . "</div>
                                                        <span>". $tmpArray[$x]['title'] . "</span>                                                    
                                                        </h3>
                                                      </li>
                                                      <li>
                                                        <h3 class=\"chart-level-3 chart-rectangle\">
                                                        <div>" . $tmpArray[$x+1]['fname'] . " " . $tmpArray[$x+1]['lname'] . "</div>
                                                        <span>". $tmpArray[$x+1]['title'] . "</span>                                                    
                                                        </h3>
                                                      </li>
                                                    </ol>";   
                                        }
                                    }
                                     $_body .= "</li>";
                   }

                    $_body .=  "</ol></div></div></div>";
        

        return $_body;
    }

    protected function getSupervisor($supervisor_id) {
        $db = new  Querier();
        $q       = "select staff_id, title, CONCAT( fname, ' ', lname ) AS fullname FROM staff WHERE staff_id = " . $supervisor_id;
        $supervisor   = $db->query($q);
        $r_count = count($supervisor);
        
        return $supervisor;      
    }  
    
    static function getMenuName() {
        return _('Chart Box');
    }

    static function getMenuIcon() {
        /*<i class="fas fa-user-circle"></i>*/
        // $icon="<i title=\"" . _("Staff") . "\" ><img src=\"../../control/includes/images/icons/staff_small.png\" height=\"35px\"></i><span class=\"icon-text\">"  . _("Staff") . "</span>";
        $icon="<i class=\"fa fa-bar-chart\" aria-hidden=\"true\" title=\"" . _("Charts") . "\" ></i><span class=\"icon-text\">"  . _("Charts") . "</span>";
        return $icon;
    }  
}

?>


<script>
$(document).on('change', '#dropdown_cb', function(){
    console.log($( "#dropdown_cb option:selected" ).val());    
    }); 
</script>




