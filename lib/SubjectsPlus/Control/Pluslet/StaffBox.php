<?php


namespace SubjectsPlus\Control;
require_once("Pluslet.php");

class Pluslet_StaffBox extends Pluslet {
    
	protected $_staff_id;
	protected $_lname;
	protected $_fname;
	protected $_title;
	private $_tel;
	private $_department_id;
	private $_staff_sort;
	private $_email;
	private $_user_type_id;

	private $_ptags;
	private $_bio;
	private $_message;
	// new for UM
	private $_position_number;
	private $_job_classification;
	private $_room_number;
	private $_supervisor_id;
    private $_supervisor;
	private $_emergency_contact_name;
	private $_emergency_contact_relation;
	private $_emergency_contact_phone;
	private $_street_address;
	private $_city;
	private $_state;
	private $_zip;
	private $_home_phone;
	private $_cell_phone;
	private $_fax;
	private $_intercom;
	private $_lat_long;
	private $_fullname;
	protected $_debug;
	//new sp4
	private $_social_media;
	protected $_extra; 
    protected $_userType;

	protected $_department;   
    protected $_showOrNotKeys = ['Photo', 'StaffMain', 'Address', 'Map', 'EmergencyContacts', 'SocialMedia', 'BioForm'];
             
    protected $showPhoto;
    protected $showStaffMain;
    protected $showAddress;
    protected $showMap;
    protected $showEmergencyContacts;
    protected $showSocialMedia;
    protected $showBioForm;


    
    
    public function __construct($pluslet_id, $flag="", $subject_id, $isclone=0) {
        parent::__construct($pluslet_id, $flag, $subject_id, $isclone);

        $this->_type = "StaffBox"; 
        
        $this->_staff_id = self::getStaffId($subject_id);
        $this->_subject_id = $subject_id;
        $this->_pluslet_id = $pluslet_id;  
         $this->_staffArray = self::populateFields();
        if(strlen($this->_pluslet_id) > 0) {         
            $_tmp = self::getDatafromExtraAndBody_ColumnsInPlusletTable();

        $num_rows = count ( $_tmp );
            if($num_rows > 0) {
                $this->_extra_array = json_decode($_tmp[0]['extra'], true);
                $this->_bio = $_tmp[0]['body'];
            }
        }
        else {
            $this->_extra_array = json_decode($this->_extra, true);;
        }
    }
    
    /**
     * 
     * @param string $_extra
     * @return string
     */
    function getDatafromExtraAndBody_ColumnsInPlusletTable() {
        $db = new Querier();
        $q = "select body, extra from pluslet where pluslet_id = " . $this->_pluslet_id;
        $r = $db->query($q);
        return $r;
    }
    
    /**
     * 
     * @param array $settings
     */
    function setShowOrNotElementsArray(array $settings) {
        // $this->showName  = $settings['showName'];
        $this->showPhoto = $settings['showPhoto'];
        $this->showStaffMain = $settings['showStaffMain'];
        $this->showAddress = $settings['showAddress'];
        $this->showMap = $settings['showMap'];
        $this->showEmergencyContacts = $settings['showEmergencyContacts'];
        $this->showSocialMedia = $settings['showSocialMedia']; 
        $this->showBioForm = $settings['showBioForm'];
    }
    
    function getShowOrNotElementsArray() {
        $showStatusSettings = $this->verifyShowInExtra();
         $settings = array(
            // 'showName'      => isset($showStatusSettings['showName'])  ? $showStatusSettings['showName'] : "No",
            'showPhoto'     => isset($showStatusSettings['showPhoto'])  ? $showStatusSettings['showPhoto'] : "No",
            'showStaffMain'     => isset($showStatusSettings['showStaffMain'])  ? $showStatusSettings['showStaffMain'] : "No",
            'showAddress'     => isset($showStatusSettings['showAddress'])  ? $showStatusSettings['showAddress'] : "No",
            'showMap'     => isset($showStatusSettings['showMap'])  ? $showStatusSettings['showMap'] : "No",
            'showEmergencyContacts'  => isset($showStatusSettings['showEmergencyContacts'])  ? $showStatusSettings['showEmergencyContacts'] : "No",
            'showSocialMedia'   => isset($showStatusSettings['showSocialMedia'])   ? $showStatusSettings['showSocialMedia'] : "No",
            'showBioForm'   => isset($showStatusSettings['showBioForm'])   ? $showStatusSettings['showBioForm'] : "No",
          );
         return $settings;
    }
    
    /**
     * 
     * @return string
     */
    function verifyShowInExtra() {

        $item = array();
        for($i = 0; $i < count($this->_showOrNotKeys); ++$i) {
            $key = 'show'.$this->_showOrNotKeys[$i].$this->_staff_id;
            if(!isset($this->_extra_array) && $this->_extra_array == null) {
                $this->_extra_array = array();
            }
            $value = "";
            if( ( array_key_exists($key, $this->_extra_array) ) && ($key != null) ) {
                if(isset($value)) {
                    $value = $this->_extra_array[$key];
                }
            }

            $keyNoUserId = 'show'.$this->_showOrNotKeys[$i];
            if(isset($keyNoUserId)) {
                if(isset($value) && ($value != null)) {
                    $item[$keyNoUserId] = $value[0];
                } else {
                    $item[$keyNoUserId] = "No";
                }
               // echo($item[$keyNoUserId]);
            }
        }
        
        $status = array(
            'showPhoto' => $item['showPhoto'],
            'showStaffMain' => $item['showStaffMain'],
            'showAddress' => $item['showAddress'],
            'showMap'     => $item['showMap'],            
            'showEmergencyContacts'     => $item['showEmergencyContacts'],
            'showSocialMedia' => $item['showSocialMedia'],
            'showBioForm' => $item['showBioForm']
        );

        return $status;
    }
    
    /**
     * 
     * @param type $subject_id
     * @return int
     */
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
    
    /**
     * 
     * @global type $stats_encryption_enabled
     * @return type
     */
    function populateFields() {
        global $stats_encryption_enabled;


        $db = new Querier;
            $q1 = "Select staff_id, lname, fname, title, tel, department_id, staff_sort, email, ip, user_type_id, password, ptags, active, bio
                , position_number, job_classification, room_number, supervisor_id, emergency_contact_name, emergency_contact_relation, emergency_contact_phone,
                street_address, city, state, zip, home_phone, cell_phone, fax, intercom, lat_long, social_media, extra
                FROM staff WHERE staff_id = " . $this->_staff_id;
        $staffArray = $db->query( $q1 );

        // $this->_debug .= "<p class=\"debug\">Staff query: $q1";
        // Test if these exist, otherwise go to plan B
        if ( $staffArray == false ) {
                $this->_message = "There is no active record with that ID.  Why not create a new one?";
        } else {
                $this->_lname    = $staffArray[0]['lname'];
                $this->_fname    = $staffArray[0]['fname'];
                $this->_fullname = $this->_fname . " " . $this->_lname;
                $this->_title    = $staffArray[0]['title'];
                $this->_tel      = $staffArray[0]['tel'];
                $this->_department_id = $staffArray[0]['department_id'];
                $this->_staff_sort   = $staffArray[0]['staff_sort'];
                $this->_email        = $staffArray[0]['email'];
                $this->_ip           = $staffArray[0]['ip'];
                $this->_user_type_id = $staffArray[0]['user_type_id'];
                $this->_active       = $staffArray[0]['active'];
                $this->_ptags        = $staffArray[0]['ptags'];
                $this->_bio          = $staffArray[0]['bio'];

                //if ( $full_record == true ) {
                        //$this->_password = $staffArray[0]['password'];

                //New for UM
                $this->_position_number            = $staffArray[0]['position_number'];
                $this->_job_classification         = $staffArray[0]['job_classification'];
                $this->_room_number                = $staffArray[0]['room_number'];
                $this->_supervisor_id              = $staffArray[0]['supervisor_id'];
                $this->_emergency_contact_name     = $stats_encryption_enabled ? !empty($staffArray[0]['emergency_contact_name']) ? decryptIt( $staffArray[0]['emergency_contact_name']) : "" :  $staffArray[0]['emergency_contact_name'];
                $this->_emergency_contact_relation = $stats_encryption_enabled ? !empty( $staffArray[0]['emergency_contact_relation']) ? decryptIt( $staffArray[0]['emergency_contact_relation']) : "" :  $staffArray[0]['emergency_contact_relation'];
                $this->_emergency_contact_phone    = $stats_encryption_enabled ? !empty($staffArray[0]['emergency_contact_phone']) ? decryptIt( $staffArray[0]['emergency_contact_phone']) : "" :  $staffArray[0]['emergency_contact_phone'];
                $this->_street_address             = $stats_encryption_enabled ? !empty($staffArray[0]['street_address']) ? decryptIt( $staffArray[0]['street_address']) : "" :  $staffArray[0]['street_address'];
                $this->_city                       = $stats_encryption_enabled ? !empty($staffArray[0]['city']) ? decryptIt( $staffArray[0]['city']) : "" :  $staffArray[0]['city'];
                $this->_state                      = $stats_encryption_enabled ? !empty($staffArray[0]['state']) ? decryptIt( $staffArray[0]['state']) : "" :  $staffArray[0]['state'];
                $this->_zip                        = $stats_encryption_enabled ? !empty($staffArray[0]['zip']) ? decryptIt( $staffArray[0]['zip']) : "" :  $staffArray[0]['zip'];
                $this->_home_phone                 = $stats_encryption_enabled ? !empty($staffArray[0]['home_phone']) ? decryptIt( $staffArray[0]['home_phone']) : "" :  $staffArray[0]['home_phone'];
                $this->_cell_phone                 = $stats_encryption_enabled ? !empty($staffArray[0]['cell_phone']) ? decryptIt( $staffArray[0]['cell_phone']) : "" :  $staffArray[0]['cell_phone'];
                $this->_fax                        = $staffArray[0]['fax'];
                $this->_intercom                   = $staffArray[0]['intercom'];
                $this->_lat_long                   = $stats_encryption_enabled ? !empty($staffArray[0]['lat_long']) ? decryptIt( $staffArray[0]['lat_long']) : ""  :  $staffArray[0]['lat_long'];

                //new for sp4

                $this->_social_media = $staffArray[0]['social_media'];
                $this->_extra        = $staffArray[0]['extra'];

                
                $this->_department = self::getAssociatedDepartments($this->_department_id);

                $this->_supervisor = self::getSupervisor($this->_supervisor_id);

                return $staffArray;
        }       
    }

    protected function getSupervisor($supervisor_id) {
        $db = new  Querier();
        $q       = "select staff_id, CONCAT( fname, ' ', lname ) AS fullname FROM staff WHERE staff_id = " . $supervisor_id;
        $supervisor   = $db->query($q);
        $r_count = count($supervisor);
        
        if ($r_count > 0) {
            return  $supervisor[0]['fullname'];
        }
        return '*External Supervisor';      
    }

    public function getAssociatedDepartments($department_id) {

        $db = new Querier();
        $q2 = "Select name from department where department_id = " . $department_id;

        $departments = $db->query( $q2 );
        
        $num_rows = count ( $departments );

        if($num_rows > 0) {
            return $departments[0]['name'];
        } 
        return "";
    }
    
    protected function onViewOutput() {
       $output = $this->outputStaffPluslet();
       $this->_body = $output;
    }
    
    protected function onEditOutput() {
        $pluslet_body = $this->_body;
        $output = $this->loadHtml(__DIR__ . '/views/StaffBoxEdit.php' ); // $this->outputStaffPluslet(); // $this->outputForm($wintype);
        $this->_body = $output;                
                
        // if($this->showBioForm === 'Yes') {
        $_ckeditor_body = self::getSectionHeading("Additional Info");
        $_ckeditor_body .= "<p>" . _( "Please only include professional details." ) . "</p><br />";
            $_ckeditor_body .= self::getCkEditorForOutputBioForm($pluslet_body);
            // $_ckeditor_body .=  "</div></div>";
        // }

        $this->_body .= $_ckeditor_body;
    }

    function outputStaffPluslet() {
        //global $wysiwyg_desc;
        //global $CKPath;
        //global $CKBasePath;
        //global $IconPath;
        global $all_ptags;
        global $tel_prefix;
        global $omit_user_columns;
        global $use_shibboleth;

        ///////////////
        // User Types
        ///////////////
        $this->_userType = self::getUserType();

        $activeArray = array(
                '0' => array( '0', 'No' ),
                '1' => array( '1', 'Yes' )
        );

        // create type dropdown
        $activateMe = new  Dropdown( "active", $activeArray, isset($this->_active) && !empty($this->_active) ? $this->_active : 0);
        $this->_active_or_not = $activateMe->display();


        /////////////
        // Start the form
        /////////////

        $action = htmlentities( $_SERVER['PHP_SELF'] ) . "?staff_id=" . $this->_staff_id;

        // start form
        $_body = self::buildStaffFormBody($action, $all_ptags, $use_shibboleth, $omit_user_columns);

        return $_body;
    }

    function getUserType() {
        $querierUserType = new  Querier();
        $qUserType       = "select user_type_id, user_type from user_type where user_type_id = " . $this->_user_type_id ;
        $userTypeArray   = $querierUserType->query( $qUserType );

        $count_r = count($userTypeArray);

        if($count_r > 0) { 
            return $userTypeArray[0]['user_type'];
        }
        return "Not Found";
    }
    
    function buildStaffFormBody($action, $all_ptags, $use_shibboleth, $omit_user_columns) {
        $settings = $this->getShowOrNotElementsArray();
        $this->setShowOrNotElementsArray($settings);
             
        $_body = "<form class=\"pure-form pure-form-stacked staffbox_pluslet\">
            <input type=\"hidden\" name=\"staff_id\" value=\"" . $this->_staff_id . "\" />
            <div class=\"pure-u\">
                <div class=\"pluslet no_overflow\">
                    <div class=\"titlebar\">
                        <div class=\"titlebar_text\">" . _( "Staff Member" ) . "</div>
                    </div>";
                        if($this->showPhoto == 'Yes') {
                            $_body .= self::getHeadshot( $this->_email) . "<br />"; 
                        }

                        if($this->showStaffMain == 'Yes') { 
                            $_body .=  "<div class=\"show-staff-main\" ><span class=\"show-staff-main-label\">Full&nbspName: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_fullname</span></span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Position Title: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_title</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Position Number: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_position_number</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Job&nbspClassification: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_job_classification</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Department: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_department</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Display Priority: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_staff_sort</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Supervisor: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_supervisor</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Telephone: </span><span style=\"border-bottom:solid; border-width:1px;\"> $this->_tel</span></div>";
                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Fax: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_fax</span></div>";


                            $_body .=  "<div style=\"margin-right: 0; width:45%\"><label for=\"intercom\">" . _( "Intercom" ) . "</label>
                                        <input type=\"text\" name=\"intercom\" id=\"intercom\" class=\"pure-input-1-4\" readonly=\"readonly\" value=\"" . $this->_intercom . "\" /></div>";
   
                            $_body .=  "<div style=\"width:45%\"><label for=\"room_number\">" . _( "Room #" ) . "</label>
                                        <input type=\"text\" name=\"room_number\" id=\"room_number\" class=\"pure-input-1-3\" readonly=\"readonly\" value=\"" . $this->_room_number . "\" /></div>";

                            $_body .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Email: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_email</span></div>";

                            //$_body .= "</div></div>";
                        }

                        if($this->showAddress === 'Yes') {
                           // $_body .=  self::getSectionHeading("Home Address Info");
                            $_body .=  self::outputPersonalInfoForm();
                            if($this->showMap == 'Yes') {
                                $_body .=  self::outputLatLongForm();
                            }
                           // $_body .= "</div></div>";
                        }

                        if($this->showEmergencyContacts === 'Yes') {
                           // $_body .=  self::getSectionHeading("Emergency Contacts Info");
                            $_body .=  self::outputEmergencyInfoForm();
                           // $_body .= "</div></div>";
                        }

                        if($this->showSocialMedia === 'Yes') {
                          // $_body .= self::getSectionHeading("Social Media Info");
                            $socialMediaForm = self::outputSocialMediaForm();
                            $_body .= $socialMediaForm;
                           //   $_body .= "</div></div>";
                        }

                        if($this->showBioForm === 'Yes' && strlen($this->_bio) > 0) {
                           // $_body .=  self::getSectionHeading("Description and Comments");
                            $_body .=  "<div class=\"show-staff-main\">$this->_bio</div>";
                           // $_body .= "</div></div>";
                        }

        $_body .= "</div></div></form>"; // end pure-u / end form

        return $_body;
    }

    public function getSectionHeading($title) {

            $heading = "<div class=\"pluslet\">
                    <div class=\"titlebar\">
                        <div class=\"titlebar_text\">" . _( $title ) . "</div>
                    </div>
                    <div class=\"pluslet_body\">";
        return $heading;
    }
    
    public function getHeadshot( $email ) {

        global $AssetPath;
        $headshot = "<div class=\"show-staff-main\" style=\"float:left; width:60%\"><span class=\"show-staff-main-label\">User Type: </span>
        <span style=\"border-bottom:solid; border-width:1px;\">$this->_userType</span></div>";
                     
        $name_id             = explode( "@", $email );
        $lib_image           = "_" . $name_id[0];
        $this->_headshot_loc = $AssetPath . "users/$lib_image/headshot.jpg";

        if ( $email != "" ) {
                $headshot .= "<img id=\"headshot\" src=\"" . $this->_headshot_loc . "\" alt=\"$this->_fullname\" title=\"$this->_fullname\"";
        } else {
                $headshot .= "<img id=\"headshot\" src=\"$AssetPath" . "images/placeholder-image.jpg\" alt=\"No picture\" title=\"No picture\"";
        }

        $headshot .= " width=\"70\"";

        $headshot .= " class=\"staff_photo\"/ style=\"float:right ; border:solid; border-width:1px; \">";
        $headshot .= "<div class=\"show-staff-main\" style=\"float:left; width:70%\"><span class=\"show-staff-main-label\">Active? : </span><span style=\"border-bottom:solid; border-width:1px;\">Yes</span></div>";

        return $headshot;
	}

    public function outputPersonalInfoForm() {

        $personal_info = "";
        $personal_info .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Street: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_street_address</span></div>";
        $personal_info .=  "<div class=\"show-staff-main\" style=\"float:left\"><span class=\"show-staff-main-label\">City: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_city</span></div>";
        $personal_info .=  "<div class=\"show-staff-main\" style=\"float:left\"><span class=\"show-staff-main-label\">State: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_state</span></div>";
        $personal_info .=  "<div class=\"show-staff-main\" style=\"float:left\"><span class=\"show-staff-main-label\">Zip: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_zip</span></div>";
        $personal_info .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Home Phone: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_home_phone</span></div>";
        $personal_info .=  "<div class=\"show-staff-main\"><span class=\"show-staff-main-label\">Cell Phone: </span><span style=\"border-bottom:solid; border-width:1px;\">$this->_cell_phone</span></div>";

        // <br class=\"clear-both\" />

        return $personal_info;
	}

    private function outputLatLongForm() {

        // let's stick the address together for fun
        $full_address = $this->_street_address . " " . $this->_city . " " . $this->_state . " " . $this->_zip;

        $lat_long = "<br />
        <div style=\"float: left; margin-right: 1em;\"><label for=\"city\">" . _( "Latitude/Longitude" ) . "</label>
        <input type=\"text\" name=\"lat_long\" id=\"lat_long\" class=\"pure-input-1\" value=\"" . $this->_lat_long . "\" /></div>

        <div style=\"margin-right: 1em;\"><label for=\"city\">" . _( "Get Coordinates" ) . "</label>
        <span class=\"lookup_button\" value=\"$full_address\">look up now</span></div>";

        return $lat_long;
	}
    
    function outputEmergencyInfoForm() {

        $emergency_info = "<div style=\"float: left; margin-right: 1em;\"><label for=\"city\">" . _( "Emergency Contact" ) . "</label>
        <input type=\"text\" name=\"emergency_contact_name\" id=\"emergency_contact_name\" class=\"pure-input-1\" value=\"" . $this->_emergency_contact_name . "\" /></div>
        <div style=\"float: left; margin-right: 1em;\"><label for=\"state\">" . _( "Relationship" ) . "</label>
        <input type=\"text\" name=\"emergency_contact_relation\" id=\"emergency_contact_relation\" class=\"pure-input-1\" value=\"" . $this->_emergency_contact_relation . "\" /></div>
        <div><label for=\"zip\">" . _( "Phone" ) . "</label>
        <input type=\"text\" name=\"emergency_contact_phone\" id=\"emergency_contact_phone\" class=\"pure-input-1\" value=\"" . $this->_emergency_contact_phone . "\" /></div>
        <br />";

        return $emergency_info;
    }

    function socialMediaForm() {
        $socialMediaForm = "";

        $extra = $this->getSocialMediaDataArray();

        $objSM      = new SocialMedia();
        $smAccounts = $objSM->toArray();

        foreach ( $smAccounts as $account ):
                $accountName = strtolower( $account['name'] );

                $socialMediaForm .= "<label for='social-{$accountName}'>{$account['name']}</label>";
                $socialMediaForm .= "<input type='text' name='social-{$accountName}' value='{$extra[$accountName]}' />";
        endforeach;

        return $socialMediaForm;
    }


    function outputSocialMediaForm() {
        $socialMediaForm = "";
        $socialArray = $this->getSocialMediaDataArray();

        $objSM      = new SocialMedia();
        $smAccounts = $objSM->toArray();

        foreach ( $smAccounts as $account ):
                $accountName = strtolower( $account['name'] );
                $accountValue = $socialArray[$accountName];
                $socialMediaForm .= "<span><a href=\"https://$accountName.com/" . $socialArray[$accountName] . "\"><i class=\"fa fa-$accountName\"></i></a>&#9&nbsp&#9</span>";
        endforeach;

        return $socialMediaForm;
    }

    protected function getSocialMediaDataArray() {

        $querier    = new  Querier();
        $q1         = "select social_media from staff where staff_id = '" . $this->_staff_id . "'";
        $staffArray = $querier->query( $q1 );

        $extra = array();

        if ( $staffArray != null ) {
                $json  = html_entity_decode( $staffArray[0]['social_media'] );
                $extra = json_decode( $json, true );
        } else {
                $extra['facebook']  = "";
                $extra['twitter']   = "";
                $extra['pinterest'] = "";
                $extra['instagram'] = "";
        }

        return $extra;
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
            
        $_outputBio = $oCKeditor->editor( "editor-staff", $pluslet_body, $config );
        return $_outputBio;
    }
    
    static function getMenuName() {
        return _('Staff Box');
    }

    static function getMenuIcon() {
        /*<i class="fas fa-user-circle"></i>*/
        $icon="<i title=\"" . _("Staff") . "\" ><img src=\"../../control/includes/images/icons/staff_small.png\" height=\"35px\"></i><span class=\"icon-text\">"  . _("Staff") . "</span>";
        //$icon="<i class=\"fa fa-user-circle\" title=\"" . _("Staff") . "\" ></i><span class=\"icon-text\">"  . _("Staff") . "</span>";
        return $icon;
    }  
}
