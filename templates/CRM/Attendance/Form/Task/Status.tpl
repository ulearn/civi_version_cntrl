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
 
<div class="form-item" id="searchForm" style="display: block;">
<fieldset><legend>Search Criteria</legend>
<table class="form-layout"><tbody>

<tr>
    <td colspan="2">
     {$form.group.label}
    {$form.group.html}
    </td>
   
</tr>
<tr> 
    <td> 
     {$form.member_join_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_join_date_low}
    </td>
    <td> 
     {$form.member_join_date_high.label} <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_join_date_high}
    </td> 
</tr> 
<tr> 
    <td> 
     {$form.member_start_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_start_date_low}
    </td>
    <td>
     {$form.member_start_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_start_date_high}
    </td> 
</tr> 
<tr> 
    <td>  
     {$form.member_end_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_end_date_low}
    </td>
    <td> 
     {$form.member_end_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_end_date_high}
    </td> 
</tr> 
<tr>
  <td colspan="4">
      <input type="hidden" name="task" id="task" value="6"/>
     <input type="submit" name="Search_attendance" id="Search_attendance" value="Search"/>
  </td>
</tr>
{if $membershipGroupTree}
<tr>
    <td colspan="4">
    {include file="CRM/Custom/Form/Search.tpl" groupTree=$membershipGroupTree showHideLinks=false}
    </td>
</tr>
{/if}
</tbody>
</table>
 </fieldset>
 </div>
