<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Attendance/Selector/Search.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'pagination.class.php';




/**
 * This file is for civimember search
 */
class CRM_Attendance_Form_Search extends CRM_Core_Form 
{
    /** 
     * Are we forced to run a search 
     * 
     * @var int 
     * @access protected 
     */ 
    protected $_force; 
 
    /** 
     * name of search button 
     * 
     * @var string 
     * @access protected 
     */ 
    protected $_searchButtonName;

    /** 
     * name of print button 
     * 
     * @var string 
     * @access protected 
     */ 
    protected $_printButtonName; 
 
    /** 
     * name of action button 
     * 
     * @var string 
     * @access protected 
     */ 
    protected $_actionButtonName;

    /** 
     * form values that we will be using 
     * 
     * @var array 
     * @access protected 
     */ 
    protected $_formValues; 

    /**
     * the params that are sent to the query
     * 
     * @var array 
     * @access protected 
     */ 
    protected $_queryParams;

    /** 
     * have we already done this search 
     * 
     * @access protected 
     * @var boolean 
     */ 
    protected $_done; 

    /**
     * are we restricting ourselves to a single contact
     *
     * @access protected  
     * @var boolean  
     */  
    protected $_single = false;

    /** 
     * are we restricting ourselves to a single contact 
     * 
     * @access protected   
     * @var boolean   
     */   
    protected $_limit = null;

    /** 
     * what context are we being invoked from 
     *    
     * @access protected      
     * @var string 
     */      
    protected $_context = null; 
	public $_mydata;
	public $_rowdata;

    protected $_defaults;
	

    /** 
     * prefix for the controller
     * 
     */
    protected $_prefix = "member_";

    /** 
     * processing needed for buildForm and later 
     * 
     * @return void 
     * @access public 
     */ 
	 
	 function get_contribution_type($conttypeid)
	 {
		 	$str_attresult = '';
			$select_query = "SELECT name from civicrm_contribution_type where id = '$conttypeid' limit 1";
			$select_query_result = &CRM_Core_DAO::executeQuery($select_query);
			while ( $select_query_result->fetch()) 
			{
						$str_attresult = $select_query_result->name;
						
			}
			return $str_attresult;
		 
	 }
	 
	 function createDateRangeArray($strDateFrom,$strDateTo)
	 {
	 
	 	// takes two dates formatted as YYYY-MM-DD and creates an
  		// inclusive array of the dates between the from and to dates.

  		// could test validity of dates here but I'm already doing
  		// that in the main script
  		$aryRange=array();
  		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
  		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
  		if ($iDateTo>=$iDateFrom) 
		{
    			array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
    			while ($iDateFrom<$iDateTo) 
				{
      					$iDateFrom+=86400; // add 24 hours
      					array_push($aryRange,date('Y-m-d',$iDateFrom));
    			}
  		}
  		return $aryRange;
	 
	 }
	 
	 
	 function getContactAttendance($contactid)
	 {
	 		$str_attresult = '';
			$select_query = "SELECT attendance from civicrm_course_attendance where contact_id = '$contactid'";
			$select_query_result = &CRM_Core_DAO::executeQuery($select_query);
			while ( $select_query_result->fetch()) 
			{
						$str_attresult = $select_query_result->attendance;
			}
			return $str_attresult;
	 
	 }
	 
	 function checkExistingGroup($group_id)
	 {
	 		$str_attresult = '';
			$select_query = "SELECT group_id from civicrm_course_register_detail where group_id = '$group_id'";
			$select_query_result = &CRM_Core_DAO::executeQuery($select_query);
			while ( $select_query_result->fetch()) 
			{
				$str_attresult = $select_query_result->group_id;
			}
			return $str_attresult;
	 
	 }
	 
	function preProcess( ) 
    { 
		
		/** 
         * set the button names 
         */ 
        $this->_searchButtonName = $this->getButtonName( 'refresh' ); 
        $this->_printButtonName  = $this->getButtonName( 'next'   , 'print' ); 
        $this->_actionButtonName = $this->getButtonName( 'next'   , 'action' ); 
        $this->_done = false;
        $this->defaults = array( );
        /* 
         * we allow the controller to set force/reset externally, useful when we are being 
         * driven by the wizard framework 
         */ 
        $this->_reset   = CRM_Utils_Request::retrieve( 'reset', 'Boolean',  CRM_Core_DAO::$_nullObject ); 
       	$this->_force   = CRM_Utils_Request::retrieve( 'force', 'Boolean',  $this, false ); 
        $this->_limit   = CRM_Utils_Request::retrieve( 'limit', 'Positive', $this );
        $this->_context = CRM_Utils_Request::retrieve( 'context', 'String', $this, false, 'search' );
        $this->assign( "context", $this->_context );
		
		if((isset($_POST['outstanding_amount']) && $_POST['outstanding_amount'] == 'Outstanding Amount') || (isset($_GET['outstanding_amount']) && $_GET['outstanding_amount'] == 'Outstanding Amount') )
			{
				$pagination = new pagination;
				if(isset($_POST['group']))
				{
					$group_id = $_POST['group'];
				}elseif(isset($_GET['group']))
				{
					$group_id = $_GET['group'];
				}
				
				if(isset($_POST['member_start_date_low']))
				{
					$member_start_date_low = $_POST['member_start_date_low'];
					$member_start_date_low = date('Y-m-d', strtotime($member_start_date_low));
				}elseif(isset($_GET['member_start_date_low']))
					{
					  	$member_start_date_low = $_GET['member_start_date_low'];
						$member_start_date_low = date('Y-m-d', strtotime($member_start_date_low));
					}
					
				if(isset($_POST['member_end_date_low']))
				{
					$member_end_date_low = $_POST['member_end_date_low'];
					$member_end_date_low = date('Y-m-d', strtotime($member_end_date_low));
				}elseif(isset($_GET['member_end_date_low']))
					{
					  	$member_end_date_low = $_POST['member_end_date_low'];
						$member_end_date_low = date('Y-m-d', strtotime($member_end_date_low));
					}
				
				//case when all are not empty
				/*if(!empty($_POST['group']) && !empty($_POST['member_start_date_low'])  && !empty($_POST['member_end_date_low']))
				 { //all are not empty
				 	echo 'DATE';
					 $select_query = "SELECT DISTINCT CONCAT(civicrm_contact.first_name , ' ', civicrm_contact.last_name) as name, civicrm_contact.id as id FROM civicrm_contact left join civicrm_membership on (civicrm_membership.contact_id = civicrm_contact.id) join civicrm_group_contact on (civicrm_group_contact.contact_id = civicrm_membership.contact_id)  where civicrm_membership.start_date <= '$member_start_date_low' AND civicrm_membership.end_date >= '$member_end_date_low'  AND civicrm_group_contact.group_id = '$group_id' ";
					 
				 }elseif(!empty($_POST['group']) && empty($_POST['member_start_date_low'])  && empty($_POST['member_end_date_low'])){// only group is not empty
				 	echo 'Group-DATE';
					$select_query = "SELECT DISTINCT CONCAT(civicrm_contact.first_name , ' ', civicrm_contact.last_name) as name, civicrm_contact.id as id FROM civicrm_contact left join civicrm_membership on (civicrm_membership.contact_id = civicrm_contact.id) join civicrm_group_contact on (civicrm_group_contact.contact_id = civicrm_membership.contact_id)  where civicrm_group_contact.group_id = '$group_id' ";
				
				}else{//all are empty
					 echo "<li>all are empty";
					$select_query = "SELECT DISTINCT CONCAT(civicrm_contact.first_name , ' ', civicrm_contact.last_name) as name, civicrm_contact.id as id FROM civicrm_contact left join civicrm_membership on (civicrm_membership.contact_id = civicrm_contact.id) ";
				 }*/
				 $and_str ='';
				 $additional_str = '';
				 if(!empty($_POST['group'])){
				 	$and_str.=" AND civicrm_group_contact.group_id = '$group_id' ";
				 }
				 if(!empty($_POST['member_start_date_low'])){
				 	$and_str.=" AND civicrm_membership.start_date >= '$member_start_date_low' ";
				 }
				 if(!empty($_POST['member_end_date_low'])){
				 	$and_str.=" AND civicrm_membership.end_date <= '$member_end_date_low' ";
				 }
				 $additional_str = ' join civicrm_group_contact on (civicrm_group_contact.contact_id = civicrm_membership.contact_id) join civicrm_group on (civicrm_group_contact.group_id = civicrm_group.id)  where 1 ';
				 $select_query = "SELECT DISTINCT CONCAT(civicrm_contact.first_name , ' ', civicrm_contact.last_name) as name, civicrm_contact.id as id, civicrm_group.title FROM civicrm_contact left join civicrm_membership on (civicrm_membership.contact_id = civicrm_contact.id) ".$additional_str." ".$and_str."";
				 //echo "<li>".$select_query;
				 $select_query_result = &CRM_Core_DAO::executeQuery($select_query);
				 $i = 0;
				 while ( $select_query_result->fetch()) {
					//checking paid or not
					$net_amount_status = false;
					$select_query_netamount_check = "SELECT contact_id , net_amount, non_deductible_amount,total_amount, contribution_type_id FROM  civicrm_contribution as cc  where cc.contact_id = '$select_query_result->id' AND cc.net_amount > 0 AND cc.net_amount != 'NULL'";
					$select_query_netamount_check_result = &CRM_Core_DAO::executeQuery($select_query_netamount_check);
					while ($select_query_netamount_check_result->fetch()) 
						{
						//$net_amount_status = true;
						$data1[$i]['contribution_type'] = $this->get_contribution_type($select_query_netamount_check_result->contribution_type_id);
						$data1[$i]['name'] = $select_query_result->name;
						$data1[$i]['id'] = $select_query_result->id;
						$data1[$i]['net_amount'] = $select_query_netamount_check_result->net_amount;
						$data1[$i]['non_deductible_amount'] = $select_query_netamount_check_result->non_deductible_amount;
						$data1[$i]['total_amount'] = $select_query_netamount_check_result->total_amount;
						$data1[$i]['group_name'] =  ($select_query_result->title!='') ? $select_query_result->title : 'NA';
						++$i;
						}
				$select_query_netamount_check_result->free();
				}
				
				if($i > 0)
				{
					$rowdata1 = 'yes';
					
					  // Parse through the pagination class
					  asort($data1);
					  $productPages = $pagination->generate($data1, 10);
					  // If we have items 
					  if (count($productPages) != 0) {
						// Create the page numbers
						$pageNumbers = '<div class="numbers">'.$pagination->links().'</div>';
					
					  }
				}
				else
				{
					$rowdata1 = 'no';
				}
				//assigning smarty variable
				$select_query_result->free();
				$this->assign( 'from', 'outstanding');
				$this->assign( 'countRow', $i);
				$this->assign( 'data_group_id', $group_id);
				$this->assign( 'rowdata1', $rowdata1);
				$this->assign( 'pageNumbers', $pageNumbers);
				$this->assign( 'outstandingdata', $productPages);
		}
		
		if(isset($_POST['force_to_100']) && $_POST['force_to_100'] == 'Force To 100 Percent')
			{
				$_POST['view_partial_attendance'] = 'View Partial Attendance Report';
				$_POST['group'] =  $_POST['hide_group'];
				$row_count = $_POST["force_to_100_count"];
				for ($row_loop_start = 1 ; $row_loop_start <= $row_count ; $row_loop_start++)
				{
					$key_to_check = 'selectbox_contact_'.$row_loop_start;
					if(isset($_POST["$key_to_check"]) && !empty($_POST["$key_to_check"]))
					{
						$id_to_update = $_POST["$key_to_check"];
						$select_query_to_partial_attendance = "SELECT attendance from civicrm_course_attendance where id = '$id_to_update'";
						$select_query_to_partial_attendance_result = &CRM_Core_DAO::executeQuery($select_query_to_partial_attendance);
						$data_to_update = array();
						
						while ($select_query_to_partial_attendance_result->fetch()) 
						{
							  $str_att_val = $select_query_to_partial_attendance_result->attendance;
							  $str_att_val_exploded = explode("|",$str_att_val);
							  $data_to_update_count = 0;
							  foreach ($str_att_val_exploded as $dataitem)
							  {
								  $dataitem_exploded_arr = explode("_", $dataitem);
								  $dataitem_date = $dataitem_exploded_arr[0];
								  $concat_dataitem = $dataitem_date."_1";
								  $data_to_update[$data_to_update_count] = $concat_dataitem;
								  $data_to_update_count++;
							  }
							  $str_data_to_update = implode("|", $data_to_update);
							  $update_query_to_partial_attendance = "update civicrm_course_attendance set attendance = '$str_data_to_update' , total_class_days = '$data_to_update_count' , total_attended_days = '$data_to_update_count', persentage = 100 , forcly =1  where id = '$id_to_update'";
							  $update_query_to_partial_attendance_result = &CRM_Core_DAO::executeQuery($update_query_to_partial_attendance);
						
						}
						$this->assign( 'partial_update', 'yes');
					}
					
				}
			}
		
		if(isset($_POST['view_partial_attendance']) && $_POST['view_partial_attendance'] == 'View Partial Attendance Report' && !empty($_POST['group']))
			{
				 $group_partial = $_POST['group'];
				 $this->assign( 'from', 'from partial data');
				$select_query_partial = "SELECT sort_name, total_class_days, total_attended_days, persentage, civicrm_course_attendance.id, contact_id  FROM  civicrm_course_attendance join civicrm_contact on (civicrm_contact.id = civicrm_course_attendance.contact_id) where groupid = '$group_partial' and  persentage < 100 order by sort_name";
				$select_query_partial_detail_result = &CRM_Core_DAO::executeQuery($select_query_partial);
				$partial_count = 1;
				while ($select_query_partial_detail_result->fetch()) 
				{
					$partial_data_array["$partial_count"]["total_class_days"] = $select_query_partial_detail_result->total_class_days;
					$partial_data_array["$partial_count"]["total_attended_days"] = $select_query_partial_detail_result->total_attended_days;
					$partial_data_array["$partial_count"]["persentage"] = $select_query_partial_detail_result->persentage;
					$partial_data_array["$partial_count"]["id"] = $select_query_partial_detail_result->id;
					$partial_data_array["$partial_count"]["sort_name"] = $select_query_partial_detail_result->sort_name;
					$partial_data_array["$partial_count"]["contact_id"] = $select_query_partial_detail_result->contact_id;
					
					$partial_count++;
				}
				if($partial_count > 1)
				{
				  $this->assign( 'partial_data_array', $partial_data_array);
				  $this->assign( 'partial_data_array_status', 'Partial data');
				  $this->assign( 'partial_row_count', $partial_count-1);
				   $this->assign( 'hide_group_value', $group_partial);
				}
				else
				{
				  $this->assign( 'partial_data_array_status', 'No partial data');
				}
			}
		if(isset($_POST['view_attendance']) && $_POST['view_attendance'] == 'View Attendance Report' && !empty($_POST['group']))
			{
				$group_partial = $_POST['group'];
				$this->assign( 'from', 'from view data');
				$select_query_partial = "SELECT sort_name, total_class_days, total_attended_days, persentage, civicrm_course_attendance.id, contact_id  FROM  civicrm_course_attendance join civicrm_contact on (civicrm_contact.id = civicrm_course_attendance.contact_id) where groupid = '$group_partial' order by sort_name";
				$select_query_partial_detail_result = &CRM_Core_DAO::executeQuery($select_query_partial);
				$partial_count = 1;
				while ($select_query_partial_detail_result->fetch()) 
				{
					$partial_data_array["$partial_count"]["total_class_days"] = $select_query_partial_detail_result->total_class_days;
					$partial_data_array["$partial_count"]["total_attended_days"] = $select_query_partial_detail_result->total_attended_days;
					$partial_data_array["$partial_count"]["persentage"] = $select_query_partial_detail_result->persentage;
					$partial_data_array["$partial_count"]["id"] = $select_query_partial_detail_result->id;
					$partial_data_array["$partial_count"]["sort_name"] = $select_query_partial_detail_result->sort_name;
					$partial_count++;
				}
				if($partial_count > 1)
				{
				  $this->assign( 'partial_data_array', $partial_data_array);
				  $this->assign( 'partial_data_array_status', 'Partial data');
				}
				else
				{
				  $this->assign( 'partial_data_array_status', 'No partial data');
				}
				
			}
		
		if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
			{
					$_POST['_qf_Search_submit'] = 'View Register';
					$attendancarr = array();
					$attendancarr_only_date = array();
					$str_attendance = '';
				    $count_of_rows = $_POST['countRow'];
					$count_of_column = $_POST['countColumn'];
					$hide_group_id = $_POST['hide_group_id'];
					$timeval = addslashes($_POST["time"]);
					$levelval = addslashes($_POST["level"]);
					$classroomval = addslashes($_POST["classroom"]);
					$teacher = addslashes($_POST["teacher"]);
					//checking existing group
					$existing_group = $this->checkExistingGroup($hide_group_id);
					if(empty($existing_group))
					{
						//insert
						$insert_query_register = "insert into civicrm_course_register_detail(group_id, time, level , classroom , teacher) values ('$hide_group_id', '$timeval', '$levelval' , '$classroomval', '$teacher')";
						$insert_query_result_register = &CRM_Core_DAO::executeQuery($insert_query_register);
					}
					else
					{
						//update
						  $update_query_register = "update civicrm_course_register_detail set time = '$timeval' , level = '$levelval' , classroom = '$classroomval', teacher = '$teacher' where group_id = '$hide_group_id'";
						  $update_query_result_register = &CRM_Core_DAO::executeQuery($update_query_register);
					}
					//saving attendance record
					for($contact_row = 1 ; $contact_row <= $count_of_rows; $contact_row++)
					 {
							$contact_id_to_sql = $_POST["contact_$contact_row"];
							$total_class_attendance = 0;
							for($contact_column = 1 ; $contact_column <= $count_of_column; $contact_column++)
					 		{
					 				$key_att = "select_attendance_$contact_row"."_".$contact_column;
									$attendancarr["$contact_column"] = addslashes($_POST["$key_att"]);
									$total_class = $contact_column;
									$total_class_attendance_arr = explode("_", $attendancarr["$contact_column"]);
									$total_class_attendance += $total_class_attendance_arr[1];
							}
							
							$str_attendance = implode('|',$attendancarr);
							$existing_atten_str = $this->getContactAttendance($contact_id_to_sql);
							if(empty($existing_atten_str))
							{
								$persentage1 = ($total_class_attendance/$total_class)*100;
								$insert_query = "insert into civicrm_course_attendance(contact_id, attendance, groupid, total_class_days, total_attended_days, persentage, forcly) values ('$contact_id_to_sql', '$str_attendance', '$hide_group_id', '$total_class', '$total_class_attendance', '$persentage1', 0)";
								$insert_query_result = &CRM_Core_DAO::executeQuery($insert_query);
							}
							else
							{
							   $atten_expo_arr = explode('|', $existing_atten_str);
							   $union = array_merge($atten_expo_arr, $attendancarr);
								$arr_string_to_save = array();
								$arr_string_to_check_existing_date = array();
								$x = 0;
								foreach ($union as $dataouter)
								{
									$dataouter_date_value_arr = explode("_", $dataouter);
									$dataouter_date = $dataouter_date_value_arr[0];
									if (!in_array($dataouter_date, $arr_string_to_check_existing_date))
									{	
										foreach ($union as $datainner)
										{
											$datainner_date_value_arr = explode("_", $datainner);
											$datainner_date = $datainner_date_value_arr[0];
											if($dataouter_date == $datainner_date)
											{
												$arr_string_to_save[$x] = $datainner;
												$arr_string_to_check_existing_date[$x] = $dataouter_date;
											}
										
										}
									$x++;
									}
								}
								sort($arr_string_to_save);
								$class_count = 0;
								$total_class_attendance2 = 0;
								foreach($arr_string_to_save as $itemvalue)
								{
									$class_count++;
									$total_class_attendance_arr2 = explode("_", $itemvalue);
									$total_class_attendance2 += $total_class_attendance_arr2[1];
								}
								$persentage2 = ($total_class_attendance2/$class_count)*100;
								$str_to_save = implode("|",$arr_string_to_save);
							   	$update_query = "update civicrm_course_attendance set total_class_days = '$class_count' , total_attended_days = '$total_class_attendance2' , persentage = '$persentage2' , attendance ='$str_to_save' , forcly = 0  where contact_id ='$contact_id_to_sql'";
								$update_query_result = &CRM_Core_DAO::executeQuery($update_query);
							}
					 }
					 $this->assign( 'status', 'save'); 
			
			}
		
		if(isset($_POST['group']) &&  isset($_POST['export_attendance']) && !empty($_POST['member_start_date_low']) && !empty($_POST['member_end_date_low']))
		{
			$group_id = $_POST['group'];
			$member_start_date_low = $_POST['member_start_date_low'];
			$member_start_date_low = date('Y-m-d', strtotime($member_start_date_low));
			$member_end_date_low = $_POST['member_end_date_low'];
			$member_end_date_low = date('Y-m-d', strtotime($member_end_date_low));
			$strDateFrom = $member_start_date_low;
			$strDateTo = $member_end_date_low;
			$aryDates = $this->createDateRangeArray($strDateFrom,$strDateTo);
			$countColumn = count($aryDates);
			$select_query = "SELECT DISTINCT CONCAT(civicrm_contact.first_name , ' ', civicrm_contact.last_name) as name, civicrm_contact.id as id FROM civicrm_contact left join civicrm_membership on (civicrm_membership.contact_id = civicrm_contact.id) join civicrm_group_contact on (civicrm_group_contact.contact_id = civicrm_membership.contact_id)  where civicrm_membership.start_date <= '$member_start_date_low' AND civicrm_membership.end_date >= '$member_end_date_low'  AND civicrm_group_contact.group_id = '$group_id'";
			$select_query_result = &CRM_Core_DAO::executeQuery($select_query);
			$data = array();
			$i = 0;
			while ( $select_query_result->fetch()) 
			{
        		//checking paid or not
				$paid_status1 = false;
				$select_query_paid_check = "SELECT DISTINCT contact_id FROM  civicrm_contribution where contact_id = '$select_query_result->id' AND contribution_status_id = 1";
				$select_query_paid_check_result = &CRM_Core_DAO::executeQuery($select_query_paid_check);
				while ($select_query_paid_check_result->fetch()) 
					{
						$paid_status1 = true;
					}
				if($paid_status1)
				{
					$subarray_value = array();
					$subarray_value['Name'] = $select_query_result->name;
					foreach ($aryDates as $value)
					{
						$subarray_value[$value] = "";
					}
					$data[$i] = $subarray_value;
					$i++;
				}
			
			}
			$csvTitle = "Attendance Register";
			$csvTime = "Time";
			$csvClassRoom = "Class Room";
			$csvTeacher = "Teacher";
			$csvLevel = "Level";
			$titleArray = array_keys($data[0]);
			$delimiter = "\t";
			$filename="Attendance_Export.xls";
			//Send headers
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$filename");
			header("Pragma: no-cache");
			header("Expires: 0");
			print $csvTitle . "\r\n\r\n";
			print $csvTime . "\r\n";
			print $csvClassRoom . "\r\n";
			print $csvTeacher . "\r\n";
			print $csvLevel . "\r\n\r\n";
			//Separate each column title name with the delimiter
			$titleString = implode($delimiter, $titleArray);
			print $titleString . "\r\n";
			if($i > 0)
			{
				//Loop through each subarray, which are our data sets
				foreach ($data as $subArrayKey => $subArray) {
							//Separate each datapoint in the row with the delimiter
							$dataRowString = implode($delimiter, $subArray);
							print $dataRowString . "\r\n";
					}
			}
			else
			{
				print "No result found";
			}
			exit();
		}
		
		if(isset($_POST['group']) &&  isset($_POST['_qf_Search_submit']) && !empty($_POST['member_start_date_low']) && !empty($_POST['member_end_date_low']))
		{
			$group_id = $_POST['group'];
			$member_start_date_low = $_POST['member_start_date_low'];
			$member_start_date_low = date('Y-m-d', strtotime($member_start_date_low));
			$member_end_date_low = $_POST['member_end_date_low'];
			$member_end_date_low = date('Y-m-d', strtotime($member_end_date_low));
			$strDateFrom = $member_start_date_low;
			$strDateTo = $member_end_date_low;
			$aryDates = $this->createDateRangeArray($strDateFrom,$strDateTo);
			$countColumn = count($aryDates);
			$select_query = "SELECT DISTINCT CONCAT(civicrm_contact.first_name , ' ', civicrm_contact.last_name) as name, civicrm_contact.id as id FROM civicrm_contact left join civicrm_membership on (civicrm_membership.contact_id = civicrm_contact.id) join civicrm_group_contact on (civicrm_group_contact.contact_id = civicrm_membership.contact_id)  where civicrm_membership.start_date <= '$member_start_date_low' AND civicrm_membership.end_date >= '$member_end_date_low'  AND civicrm_group_contact.group_id = '$group_id'";
			
			$select_query_result = &CRM_Core_DAO::executeQuery($select_query);
			$data1 = array();
			$selectbox_values_arr = array();
			$selectbox_values_arr_to_show = array();
			$i = 0;
			while ( $select_query_result->fetch()) 
			{
				//checking paid or not
				$paid_status = false;
				$select_query_paid_check = "SELECT DISTINCT contact_id FROM  civicrm_contribution where contact_id = '$select_query_result->id' AND contribution_status_id = 1";
				$select_query_paid_check_result = &CRM_Core_DAO::executeQuery($select_query_paid_check);
				while ($select_query_paid_check_result->fetch()) 
					{
						$paid_status = true;
					}
				if($paid_status)
				{
						
					$data1[$i]['name'] = $select_query_result->name;
					$data1[$i]['id'] = $select_query_result->id;
					$atten_string = $this->getContactAttendance($select_query_result->id);
					if(!empty($atten_string))
					{
							$atten_string_arr = explode('|', $atten_string);
							$arr_string_to_save2 = array();
							$x2 = 0;
							foreach ($aryDates as $dataouter2)
							{
									$arr_string_to_save2[$x2] = $dataouter2;
									foreach ($atten_string_arr as $datainner2)
									{
										
										$datainner_date_value_arr2 = explode("_", $datainner2);
										$datainner_date2 = $datainner_date_value_arr2[0];
										if($dataouter2 == $datainner_date2)
										{
											$arr_string_to_save2[$x2] = $datainner2;
										}
												
									}
								$x2++;
							}
							sort($arr_string_to_save2);
							$lr = 0;
							foreach ($arr_string_to_save2 as $searchdata2)
									{
										$searchdata2_date_value_arr2 = explode("_", $searchdata2);
										$searchdata2_date = $searchdata2_date_value_arr2[0];
										if(isset($searchdata2_date_value_arr2[1]))
										{
											$searchdata2_value = $searchdata2_date_value_arr2[1];
											$selectbox_values_arr_to_show[$i][$lr] = $searchdata2_value;
										}
									    $selectbox_values_arr[$i][$lr] = $searchdata2_date;
									   $lr++;
									}
						
						}
					else
					{
							for($q = 0; $q < count($aryDates); $q++)
									  {
												$selectbox_values_arr[$i][$q] = $aryDates[$q];								  
									  }
							
						}
					++$i;
				
				}
					
			}
			//if search result is not empty
			if($i > 0)
			{
				$rowdata = 'yes';
				$register_data_array = array();
				$u = 0;
				$select_query_register_detail = "SELECT time, level, classroom, teacher FROM  civicrm_course_register_detail where group_id = '$group_id'";
				$select_query_register_detail_result = &CRM_Core_DAO::executeQuery($select_query_register_detail);
				while ($select_query_register_detail_result->fetch()) 
				{
					$register_data_array["time"] = $select_query_register_detail_result->time;
					$register_data_array["level"] = $select_query_register_detail_result->level;
					$register_data_array["classroom"] = $select_query_register_detail_result->classroom;
					$register_data_array["teacher"] = $select_query_register_detail_result->teacher;
				}
				$this->assign( 'register_data_array', $register_data_array);
			}
			else
			{
				$rowdata = 'no';
			}
			//assigning smarty variable
		  	$this->_mydata = $data1;
	        $this->_rowdata = $rowdata;
			$select_query_result->free();
			$this->assign( 'aryDates', $aryDates);
			$this->assign( 'countColumn', $countColumn);
			$this->assign( 'countRow', $i);
			$this->assign( 'data_group_id', $group_id);
			$this->assign( 'selectbox_values_arr', $selectbox_values_arr);
			$this->assign( 'selectbox_values_arr_to_show', $selectbox_values_arr_to_show);
			$this->assign( 'rowdata', $this->_rowdata);
			$this->assign( 'mydata', $this->_mydata);
		}
	}

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
		
		$this->addElement('text', 'sort_name', ts('Member Name or Email'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        require_once 'CRM/Attendance/BAO/Query.php';
        CRM_Attendance_BAO_Query::buildSearchForm( $this );
		
        /* 
         * add form checkboxes for each row. This is needed out here to conform to QF protocol 
         * of all elements being declared in builQuickForm 
         */ 
		 
        $rows = $this->get( 'rows' ); 
        if ( is_array( $rows ) ) {
            if ( !$this->_single ) {
                $this->addElement( 'checkbox', 'toggleSelect', null, null, array( 'onclick' => "toggleTaskAction( true ); return toggleCheckboxVals('mark_x_',this);" ) ); 
                foreach ($rows as $row) { 
                    $this->addElement( 'checkbox', $row['checkbox'], 
                                       null, null, 
                                       array( 'onclick' => "toggleTaskAction( true ); return checkSelectedBox('" . $row['checkbox'] . "', '" . $this->getName() . "');" )
                                       ); 
                }
            }

            $total = $cancel = 0;

            require_once "CRM/Core/Permission.php";
            $permission = CRM_Core_Permission::getPermission( );
            
            require_once 'CRM/Member/Task.php';
            $tasks = array( '' => ts('- more actions -') ) + CRM_Member_Task::permissionedTaskTitles( $permission );
			$tasks[] = ts('Attendance');
            $this->add('select', 'task'   , ts('Actions:') . ' '    , $tasks    ); 
            $this->add('submit', $this->_actionButtonName, ts('Go'), 
                       array( 'class'   => 'form-submit',
                              'id'      => 'Go',
                              'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 0);" ) ); 

            $this->add('submit', $this->_printButtonName, ts('Print'), 
                       array( 'class' => 'form-submit', 
                              'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) ); 

            
            // need to perform tasks on all or selected items ? using radio_ts(task selection) for it 
            $this->addElement('radio', 'radio_ts', null, '', 'ts_sel', array( 'checked' => 'checked') ); 
            $this->addElement('radio', 'radio_ts', null, '', 'ts_all', array( 'onclick' => $this->getName().".toggleSelect.checked = false; toggleCheckboxVals('mark_x_',this); toggleTaskAction( true );" ) );
        }
        

        // add buttons 
		$js = array( 'onclick' => "return checkSearchvalidation();" ); 
        $this->addButtons( array( 
                                 array ( 'type'      => 'submit', 
                                       'name'      => ts('View Register') , 
									     'js'        => $js,
                                         'isDefault' => true ) 
                                 )    );     
   
    
     
    }

    public function getTitle( ) 
    {
		
		return ts('Find Members');
    }
   
}


