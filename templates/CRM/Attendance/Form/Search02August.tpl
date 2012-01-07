{*
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
*}
{assign var="showBlock" value="'searchForm'"}
{assign var="hideBlock" value="'searchForm_show'"}

{if $status EQ 'save' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th  scope="col">Attendance has been successfully saved</th>
    </tr>
  </thead>
 </table>
 {/if} 
 
 {if $partial_update EQ 'yes' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th  scope="col">Attendance forced to 100 percent has been successfully saved</th>
    </tr>
  </thead>
 </table>
 {/if} 
<div id="searchForm" class="form-item">
<fieldset><legend>{ts}Search Criteria{/ts}</legend>
{strip}
     <table class="form-layout">
        {include file="CRM/Attendance/Form/Search/Common.tpl"}
        <tr>
            <td align="right" valign="middle">{$form.buttons.html}</td>
             <td  align="left" valign="middle"> <input type="submit" value="Export Attendance" name="export_attendance" onclick = "return checkSearchvalidation(); " class="form-submit default"/></td>
             <td  align="left" valign="middle"> <input type="submit" value="Outstanding Amount" name="outstanding_amount" class="form-submit default" onclick = "return outstandingcheck();"/></td>
        </tr>
    </table>
{/strip} 
</fieldset>
</div>
{strip}
{if $rowdata EQ 'yes' } 
<table class="selector" summary="Search results listings." style="position: relative;">
<tr>
           <th scope="col" align="left" valign="middle">
                    <h2>Class Register</h2>
                </th>
          </tr>
          <tr>
           <th scope="col" align="left" valign="middle">
                    <h3>Please enter only 1 for yes, 0 for no and free, 0.1 to 0.9 for partial attendance value</h3>
                </th>
          </tr>
 </table>
<table class="selector" summary="Search results listings." style="position: relative;">
<tr>
              <th scope="col">
                    Time
                </th>
                 <th scope="col">
                   <input type="text" value="{$register_data_array.time}" name="time" id="time" />
                </th>
          </tr>
          <tr>
                   <th scope="col">
                      Class Room
                    </th>
                     <th scope="col">
                   <input type="text" value="{$register_data_array.classroom}" name="classroom" id="classroom" />
                </th>
          </tr>
           <tr>
                     <th scope="col">
                      Teacher
                    </th>
             		 <th scope="col">
                   <input type="text" value="{$register_data_array.teacher}" name="teacher" id="teacher" />
                </th>
              
          </tr>
           <tr>
                  <th scope="col">
                      Level
                    </th>
                     <th scope="col">
                   <input type="text" value="{$register_data_array.level}" name="level" id="level" />
                </th>
          </tr>
 </table>
<table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
   
    <tr>
              <th scope="col">
                    Name
                </th>
              {foreach from=$aryDates item=arrdate}
                  <th scope="col">
                        {$arrdate}    
                    </th>
              {/foreach}      
              
          </tr>
  </thead>
  <tbody>
  	{assign var=i value=0 }
  	 {foreach from=$mydata item=rowdata}
  			
        <tr title="Click contact name to view a summary. Right-click anywhere in the row for an actions menu." class="odd-row" id="rowid1851">
               
              <td><a href="/ulearn/civicrm/contact/view?reset=1&amp;cid={$rowdata.id}">{$rowdata.name}</a></td>
               {assign var=k value=$i}
           		 <span style="display:none">{$i++}</span>
                  <input type="hidden" name="contact_{$i}" id="contact_{$i}" value="{$rowdata.id}" />
              {assign var=j value=0 }
              {foreach from=$aryDates item=arrdate}
              		
                    {assign var=m value=$j}
                   
             	 <span style="display:none">{$j++}</span>
              	
                <!--<td><select name="select_attendance_{$i}_{$j}" id = "select_attendance_{$i}_{$j}" ><option  {if $selectbox_values_arr[$k][$m] EQ ''} selected="selected" {/if} value="">[Select]</option><option  {if $selectbox_values_arr[$k][$m] EQ $arrdate|cat:'_Y'} selected="selected" {/if} value="{$arrdate}_Y">Yes</option><option  {if $selectbox_values_arr[$k][$m] EQ $arrdate|cat:'_N'} selected="selected" {/if} value="{$arrdate}_N">No</option><option  {if $selectbox_values_arr[$k][$m] EQ $arrdate|cat:"_F"} selected="selected" {/if} value="{$arrdate}_F">Free</option><option  {if $selectbox_values_arr[$k][$m] EQ $arrdate|cat:"_PA"} selected="selected" {/if} value="{$arrdate}_PA">Partial</option></select></td>  -->  
                
                <td><input type="hidden" name="select_attendance_{$i}_{$j}" id = "select_attendance_{$i}_{$j}" value= "{$selectbox_values_arr[$k][$m]}" size="1" /><input type="text" name="select_attendance_nothide_{$i}_{$j}" id = "select_attendance_nothide_{$i}_{$j}" value="{$selectbox_values_arr_to_show[$k][$m]}" size="1" /></td>  
                       
              {/foreach}               
         </tr>
      {/foreach}       
        
    </tbody>
      
    </table>
    
    <table >
       <tr>
     		   <td >
                 <input type="hidden" name="hide_group_id" id="hide_group_id" value="{$data_group_id}" />
                <input type="hidden" name="countColumn" id="countColumn" value="{$countColumn}" />
                 <input type="hidden" name="countRow" id="countRow" value="{$countRow}" />
                <input type="submit" name="Save" value="Save" onclick="return checkvalidation('{$countRow}', '{$countColumn}')"/>
                </td>
          </tr>   
    </table>
{/if} 
{if $rowdata EQ 'no' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th title="Select All Rows" scope="col">No result found</th>
    </tr>
  </thead>
 </table>
 {/if} 
 
 
 {if $partial_data_array_status EQ 'No partial data' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th scope="col">No result found</th>
    </tr>
    
  </thead>
 </table>
 {/if} 
 {if $rowdata1 EQ 'yes' } 
 {if $from EQ 'outstanding' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th scope="col"><h2>Outstanding Amount Report</h2></th>
    </tr>
    
  </thead>
 </table>
  <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Total paid amount (&euro;)</th>
      <th scope="col">Non deductable amount (&euro;)</th>
      <th scope="col">Net Amount (Amount outstanding due) (&euro;)</th>
       <th scope="col">Contribution Type</th>
    </tr>
  </thead>
  <tbody>
  
   {foreach from=$outstandingdata item=dataitem}
                  <tr class="odd-row">
                    <td scope="col">
                        <a href="/ulearn/civicrm/contact/view?reset=1&amp;cid={$dataitem.id}">{$dataitem.name} </a> 
                    </td>
                     <td scope="col">
                        {$dataitem.total_amount}    
                    </td>
                     <td scope="col">
                        {$dataitem.non_deductible_amount}    
                    </td>
                     <td scope="col">
                        {$dataitem.net_amount}    
                    </td>
                     <td scope="col">
                        {$dataitem.contribution_type}    
                    </td>
                    
                  </tr>
    {/foreach}  
   </tbody>  
 </table>
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead >
    <tr>
      <td scope="col" align="center">{$pageNumbers}</th>
    </tr>
    
  </thead>
 </table>

 {/if} 
{/if} 

{if $rowdata1 EQ 'no' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th scope="col">No result found</th>
    </tr>
    
  </thead>
 </table>
 {/if} 
 
 {if $partial_data_array_status EQ 'Partial data'} 
 {if $from EQ 'from partial data' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th scope="col"><h2>Partial Attendance Report</h2></th>
    </tr>
    
  </thead>
 </table>
 {/if} 
 {if $from EQ 'from view data' } 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
      <th scope="col"><h2>Attendance Report</h2></th>
    </tr>
    
  </thead>
 </table>
 {/if} 
 <table class="selector" summary="Search results listings." style="position: relative;">
  <thead class="sticky">
    <tr>
    {if $from EQ 'from partial data'} 
      <th scope="col"><input type="checkbox" name="selectallcheckbox" id="selectallcheckbox" onclick="toggel_select_all('{$partial_row_count}');"/></th>
     {/if}
      <th scope="col">Name</th>
      <th scope="col">Total class days</th>
      <th scope="col">Total attended days</th>
      <th scope="col">Percentage</th>
    </tr>
  </thead>
  <tbody>
  {assign var=partialcount value=0}
   {foreach from=$partial_data_array item=partialdate}
                  <tr class="odd-row">
                  	 {if $from EQ 'from partial data'}  
                      <span style="display:none">{$partialcount++}</span>
                     <td scope="col"><input type="checkbox" name="selectbox_contact_{$partialcount}" id="selectbox_contact_{$partialcount}" value="{$partialdate.id}"/></td>
                     {/if}
                    <td scope="col">
                        <a href="/ulearn/civicrm/contact/view?reset=1&amp;cid={$partialdate.contact_id}">{$partialdate.sort_name} </a> 
                    </td>
                     <td scope="col">
                        {$partialdate.total_class_days}    
                    </td>
                     <td scope="col">
                        {$partialdate.total_attended_days}    
                    </td>
                     <td scope="col">
                        {$partialdate.persentage}    
                    </td>
                  </tr>
    {/foreach}  
    {if $from EQ 'from partial data'}  
     <tr class="odd-row">
                  	 <td scope="col" colspan="5">
                      <input type="hidden" name="force_to_100_count" id="force_to_100_count" value="{$partial_row_count}" />
                      <input type="hidden" name="hide_group" id="hide_group" value="{$hide_group_value}" />
                     <input type="submit" name="force_to_100" id="force_to_100" value="Force To 100 Percent" onclick="return check_select_all('{$partial_row_count}');"/></td>
                    
      </tr>
     {/if}
   </tbody>  
 </table>
  {/if} 
 {/strip}  
 {literal} 
<script type="text/javascript" >
function check_select_all(countrow)
{
	var i;
	for (i = 1; i <= countrow; i++)
	{
	  if (document.getElementById("selectbox_contact_"+i).checked == true)
	  {
		  return true;
	  }
    }
	alert("Please check atleast one checkbox");
	return false;
}
function toggel_select_all(countrow)
{
	var i;
	for (i = 1; i <= countrow; i++)
	{
	  if (document.getElementById("selectbox_contact_"+i).checked == true)
	  {
		  document.getElementById("selectbox_contact_"+i).checked = false;
	  }
	  else
	  {
		  document.getElementById("selectbox_contact_"+i).checked = true;
	  }
    }
}
function days_between(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime()
    var date2_ms = date2.getTime()

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms)
    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY)

}
function checkSearchvalidation()
{
		var start_day;
		var end_day;
		var date1;
		var date2;
		var diff;
		var start_day_array;
		var end_day_array;
		start_day = document.getElementById("member_start_date_low").value;
		end_day = document.getElementById("member_end_date_low").value;
		start_day_array = start_day.split("-");
		end_day_array = end_day.split("-");
		date1 = new Date(start_day_array[2]*1,start_day_array[1]*1,start_day_array[0]*1);
		date2 = new Date(end_day_array[2]*1,end_day_array[1]*1,end_day_array[0]*1);
		diff = days_between(date1, date2);
		diff = diff+1;
		if (document.getElementById("group").value == '')
		{
			alert("Please select group");
			return false;
		}
		if (start_day == '')
		{
			alert("Please select start date");
			return false;
		}
		if (end_day == '')
		{
			alert("Please select end date");
			return false;
		}
		if (diff > 10)
		{
			alert("Search date range exceeded more than 10 days");
			return false;
		}
		return true;
	
}

function outstandingcheck()
{
	start_day = document.getElementById("member_start_date_low").value;
	end_day = document.getElementById("member_end_date_low").value;
	if(start_day != '' && end_day == '')
	{
		alert('Plese select end date');
		return false;
	}
	if(start_day == '' && end_day != '')
	{
		alert('Plese select start date');
		return false;
	}
	return true;
}


function checkvalidation(row, col)
	{
		var i,k,m;
		var j;
		var start_day;
		var end_day;
		var date1;
		var date2;
		var diff;
		var start_day_array;
		var end_day_array;
		var nothideval; 
		var hideval;
		var tosave_indatabase;
		start_day = document.getElementById("member_start_date_low").value;
		end_day = document.getElementById("member_end_date_low").value;
		start_day_array = start_day.split("-");
		end_day_array = end_day.split("-");
		date1 = new Date(start_day_array[2]*1,start_day_array[1]*1,start_day_array[0]*1);
		date2 = new Date(end_day_array[2]*1,end_day_array[1]*1,end_day_array[0]*1);
		diff = days_between(date1, date2);
		diff = diff+1;
		if (diff > 10)
		{
			alert("Search date range exceeded more than 10 days");
			return false;
		}
		if(document.getElementById("time").value == '')
				{
					alert("Please enter the time value");
					return false;
					
				}
		if(document.getElementById("classroom").value == '')
				{
					alert("Please enter the classroom value");
					return false;
					
				}
				
		for(i=1; i <= row; i++)
		{
		  for(j=1; j <= col; j++)
			{
				if(document.getElementById("select_attendance_nothide_"+i+"_"+j).value == '')
				{
					alert("Please enter the attendance value");
					return false;
					
				}
				if(document.getElementById("select_attendance_nothide_"+i+"_"+j).value > 1.0)
				{
					alert("Please enter only 1 for yes, 0 for no and free, 0.1 to 0.9 for partial attendance value");
					return false;
					
				}
				if(isNaN(document.getElementById("select_attendance_nothide_"+i+"_"+j).value))
				{
					alert("Please enter only 1 for yes, 0 for no and free, 0.1 to 0.9 for partial attendance value");
					return false;
					
				}
				nothideval = document.getElementById("select_attendance_nothide_"+i+"_"+j).value;
				hideval = document.getElementById("select_attendance_"+i+"_"+j).value;
				tosave_indatabase = hideval+"_"+nothideval;
				document.getElementById("select_attendance_"+i+"_"+j).value = tosave_indatabase;
			}
		}
		return true;
	}
</script>
<style>
.numbers {
    line-height: 20px;
    word-spacing: 4px;
   }
.numbers a.selected{
    font-weight: bold;
	font-size:15px;
	color:#94AB3F;
   }
</style>
{/literal}