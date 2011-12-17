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
{* this template is used for displaying PCP information *} 
{if $owner}
{capture assign="contribPageURL"}{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$pcp.contribution_page_id`"}{/capture}
<div class="messages status">
  <dl>
	<dt><div class="icon inform-icon"></div></dt>
	<dd><p><strong>{ts}Personal Fundraiser View{/ts}</strong> - {ts 1=$contribPageURL 2=$pageName}This is a preview of your Personal Campaign Page in support of <a href="%1"><strong>"%2"</strong></a>.{/ts}</p></dd>
    <dt></dt>
    <dd>
        {ts}The current status of your page is{/ts}: <strong {if $pcp.status_id NEQ 2}class=disabled {/if}>{$owner.status}</strong>.
        {if $pcp.status_id NEQ 2}<br /><span class="description">{ts}You will receive an email notification when your page is Approved and you can begin promoting your campaign.{/ts}</span>{/if}
        {if $owner.start_date}<br />{ts}This campaign is active from{/ts} <strong>{$owner.start_date|truncate:10:''|crmDate}</strong> {ts}until{/ts} <strong>{$owner.end_date|truncate:10:''|crmDate}</strong>.{/if}
    </dd>
    <dt></dt>
    <dd>
        <table class="form-layout-compressed">
        <tr><td colspan="2"><strong>{ts}You can{/ts}:</strong></td></tr>
		{foreach from = $links key = k item = v}
          <tr>
            <td>
                <a href="{crmURL p=$v.url q=$v.qs|replace:'%%pcpId%%':$replace.id|replace:'%%pcpBlock%%':$replace.block}" title="{$v.title}"{if $v.extra}{$v.extra}{/if}><strong>&raquo; {$v.name}</strong></a>
		   </td>
  		   <td>&nbsp;<cite>{$hints.$k}</cite></td>
	 	 </tr>
        {/foreach}
  	   </table>
	</dd>
	<dt><div class="icon tip-icon"></div></dt>
    <dd><strong>{ts}Tip{/ts}</strong> - <span class="description">{ts}You must be logged in to your account to access the editing options above. (If you visit this page without logging in, you will be viewing the page in "live" mode - as your visitors and friends see it.){/ts}</span></dd>
 </dl>
</div>
{/if}
<div class="campaign">
    <div class="pcp-intro-text">
    	{$pcp.intro_text}
	</div>   	
    {if $image}
    <div class="pcp-image">
   		{$image}
   	</div>
   	{/if}
   	{if $pcp.is_thermometer OR $pcp.is_honor_roll}  
      <div class="pcp-widgets">
	    {if $pcp.is_thermometer}
	    <div class="thermometer-wrapper">
	        <div class="pcp-amount-goal">
		        {ts}Goal{/ts} <span class="goal-amount crmMoney">{$pcp.goal_amount|crmMoney}</span>
		    </div>
		    <div class="thermometer-fill-wrapper">
		        <div style="height: {$achieved}%;" class="thermometer-fill">
		        	<div class="thermometer-pointer"><span class="pcp-percent-raised">{$achieved}%</span> towards our goal</div>
		        </div><!-- /.thermometer-fill -->
		    </div><!-- /.thermometer-fill-wrapper -->
		    <div class="pcp-amount-raised">
		         <span class="raised-amount crmMoney">{$total|crmMoney}</span> {ts}raised{/ts}
		    </div>
		</div>
	    {/if} 
	    {if $pcp.is_honor_roll}
	    <div class="honor-roll-wrapper">
	    	<div class="honor-roll-title">{ts}HONOR ROLL{/ts}</div>
        	<div class="honor_roll">
        	    <marquee behavior="scroll" direction="up" id="pcp_roll"	scrolldelay="200" bgcolor="#fafafa"> 
        	      {foreach from = $honor item = v} 
        	      <div class="pcp_honor_roll_entry">
        	          <div class="pcp-honor_roll-nickname">{$v.nickname}</div>
        	          <div class="pcp-honor_roll-total_amount">{$v.total_amount}</div>
        	          <div class="pcp-honor_roll-personal_note">{$v.personal_note}</div>
	    	  </div>
        	      {/foreach} 
        	    </marquee>
        	</div>	
        	<div class="description">
        	    [<a href="javascript:roll_start_stop();" id="roll" title="Stop scrolling">{ts}Stop{/ts}</a>]
        	</div>
        </div>
	   {/if}
	   
	   </div>
      {/if}
   	
   	 
    <div class="pcp-page-text">
    	{$pcp.page_text}
    </div>
    
    {if $validDate && $contributeURL}
    	<div class="pcp-donate"> 
        {* Show link to PCP contribution if configured for online contribution *}
            <a href={$contributeURL} class="button contribute-button pcp-contribute-button"><span>{$contributionText}</span></a>
        </div>
    {/if}

      

   {if $linkText}
   <div class="pcp-create-your-own"> 
        <a href={$linkTextUrl} class="pcp-create-link"><span>{$linkText}</span></a>
   </div>     
   {/if}
</div><!-- /.campaign -->




{literal}
<script language="JavaScript">


var start=true;
function roll_start_stop( ) {
	if ( start ) {
		document.getElementById('roll').innerHTML = "{/literal}{ts}Start{/ts}{literal}";
		document.getElementById('roll').title = "{/literal}{ts}Start scrolling{/ts}{literal}";
		document.getElementById('pcp_roll').stop();
		start=false;
      	 } else {
		document.getElementById('roll').innerHTML = "{/literal}{ts}Stop{/ts}{literal}";
		document.getElementById('roll').title = "{/literal}{ts}Stop scrolling{/ts}{literal}";
		document.getElementById('pcp_roll').start();
		start=true;
       	}
}
</script>
{/literal}
