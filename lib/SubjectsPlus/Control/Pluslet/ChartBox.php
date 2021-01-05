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
	private $_description;
    private $_fullname;
	protected $_supervisor_id;
	//new sp4
	private $_social_media;
	public $_ok_departments = array();   
    public $colors_array =  ['#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6', 
		                    '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
		                    '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A', 
		                    '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
		                    '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC', 
		                    '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
		                    '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680', 
		                    '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
		                    '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3', 
		                    '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'];
    protected $_showOrNotKeys = ['OrgChart', 'PieChart'];
    protected $showOrgChart;
    protected $showPieChart;
        
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
                $this->_extra_array = json_decode($_tmp[0]['extra'], true);
                $this->_description = $_tmp[0]['body'];
            }
        }
        else {
            $this->_extra_array = json_decode($this->_extra, true);
            $this->_description = "";
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

                $this->_description          = $staffArray[0]['bio'];

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
        $output = $this->loadHtml(__DIR__ . '/views/ChartBoxEdit.php' ); // $this->selectChartType();
        $this->_body = $output;

        $_ckeditor_body = "<p>" . _( "Please only include professional details." ) . "</p><br />";
        $_ckeditor_body .= self::getCkEditorForOutputDescriptionForm($pluslet_body);

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

    function getCkEditorForOutputDescriptionForm($pluslet_body) {

        global $CKPath;
        global $CKBasePath;

         include( $CKPath );
         global $BaseURL;

        $_outputDescription = "";

        $oCKeditor = new CKEditor( $CKBasePath );
        $oCKeditor->timestamp = time();
        $oCKeditor->returnOutput = true;
        $config['toolbar'] = 'TextFormat';
        $config['height'] = '300';
        $config['filebrowserUploadUrl'] = $BaseURL . "ckeditor/php/uploader.php";
            
        $_outputDescription = $oCKeditor->editor( "editor-chart", $pluslet_body, $config );
        return $_outputDescription;
    }

    function setShowOrNotElementsArray(array $settings) {
        // $this->showName  = $settings['showName'];
        $this->showOrgChart = $settings['showOrgChart'];
        $this->showPieChart = $settings['showPieChart'];
    }
    
    function getShowOrNotElementsArray() {
        $showStatusSettings = $this->verifyShowInExtra();
         $settings = array(
            // 'showName'      => isset($showStatusSettings['showName'])  ? $showStatusSettings['showName'] : "No",
            'showOrgChart'     => isset($showStatusSettings['showOrgChart'])  ? $showStatusSettings['showOrgChart'] : "No",
            'showPieChart'     => isset($showStatusSettings['showPieChart'])  ? $showStatusSettings['showPieChart'] : "No"
          );
         return $settings;
    }

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
            'showOrgChart' => $item['showOrgChart'],
            'showPieChart' => $item['showPieChart']
        );

        return $status;
    }

    function outputChartPluslet() {
        // start form
        $settings = $this->getShowOrNotElementsArray();
        $this->setShowOrNotElementsArray($settings);
        if($this->showPieChart === "Yes" && $this->showOrgChart === "Yes") {
            $_body = self::buildOrgChartBoxBody();
            $_body .= self::buildPieChartBoxBody();
        } else if($this->showOrgChart === "Yes") {
            $_body = self::buildOrgChartBoxBody();
        }
        else if($this->showPieChart === "Yes"){
            $_body = self::buildPieChartBoxBody();
        }
        return $_body;
    }

    function buildPieChartBoxBody() {
        $db = new Querier;
        $q = "SELECT count(d.department_id) as department_count,`name`, `telephone`FROM department d, staff s 
        where s.department_id = d.department_id group by (d.department_id) order by count(d.department_id)";
        $departmentArray = $db->query($q);
         $_body = "<div class=\"pluslet no_overflow\">
                   <div class=\"pluslet_body chart-ol chart-pseudo-root chart-pseudo-body\" style=\"padding:0\">";
        $_rows = count($departmentArray);       
        $_totalStaff = array_sum(array_column($departmentArray,'department_count'));

        $_body .= "<div class=\"chart-pie\">";
        $_table =  "<table class=\"pie-chart-table\"><tr><th>Count</th><th>Dpt Name</th><th>Color</th></tr>";
        $previousValue = 0;
        if($_totalStaff > 0) {
            for ($i = 0; $i < count($departmentArray); $i++)  {
        
               $current_dpt_pct = $departmentArray[$i]['department_count'] / $_totalStaff * 100;
               $previousValue += $i > 0 ? $current_dpt_pct : 0;

                $department_name = $departmentArray[$i]['name'];
                                    
                $_body .= "<div class=\"pie-segment\" data-label=\"$department_name\" style=\"--offset: $previousValue; --value: $current_dpt_pct;";
            
                    if ($current_dpt_pct > 50) {
                        $_body .= "--over50:1\"></div>";
                    } else {
                        $_body .= "--bg:" . $this->colors_array[$i] . "\"></div>";
                    } 

                $_table .= "<tr><td>" . $departmentArray[$i]['department_count'] . "</td>
                       <td>" . $departmentArray[$i]['name'] . " </td>
                       <td style=\"background-color: " . $this->colors_array[$i] . "\"></td></tr>";
            }
        }
        $_table .= "</table>";
        $_body .= "</div>";
        $_body .= $_table . "</div></div>";

        return $_body;
        /*<div class=\"pie-segment\" data-label=\"Human Resources\" style=\"--offset: 0; --value: 25; --bg: red \"></div>
        <div class=\"pie-segment\" data-label=\"Statistics\" style=\"--offset: (25); --value: 10; --bg: blue \"></div>
        <div class=\"pie-segment\" data-label=\"Mathematical Analisis\" style=\"--offset: (25 + 10); --value: 60; --over50:1\"></div>
        <div class=\"pie-segment\" data-label=\"Spanish Literature\" style=\"--offset: (25 + 10 + 60); --value: 5; --bg: orange \"></div>*/
    }
    
    function buildOrgChartBoxBody() {

        $db = new Querier;
            $q1 = "select staff_id, lname, fname, title,  active, supervisor_id, social_media
                    from staff where active = 1 and supervisor_id = " . $this->_supervisor_id;
            $staffArray = $db->query( $q1 );
             
        $_body = "<div class=\"pluslet no_overflow\">
                   <div class=\"pluslet_body chart-ol chart-pseudo-root chart-pseudo-body\" style=\"padding:0\">";

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

                    $_body .=  "</ol>";
                    
                    $_body .= "<div style=\"padding-top:20px\">" . $this->_description . "</div></div></div>";

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
        $icon="<i class=\"fa fa-bar-chart\" aria-hidden=\"true\" title=\"" . _("Charts") . "\" ></i><span class=\"icon-text\">"  . _("Charts") . "</span>";
        return $icon;
    }  
}





