{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
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
{* action = edit/delete/view or close (cancel) *}
{if $action eq 4}
    {include file="CRM/Booking/Form/Booking/View.tpl"}
{elseif $action eq 2 or $action eq 8 }
    {include file="CRM/Booking/Form/Booking/Update.tpl"}
{elseif $action eq 262144}
    {include file="CRM/Booking/Form/Booking/Cancel.tpl"}
{else}
    <div class="view-content">
        {if $action eq 16 and $permission EQ 'edit'}
           {capture assign=newBookingURL}{crmURL p="civicrm/booking/add/" q="reset=1&action=add&cid=$cid"}{/capture}
            <div class="action-link">
                <a accesskey="N" href="{$newBookingURL}" class="button"><span><div class="icon add-icon"></div>{ts}Add a booking for this contact{/ts}</span>
                </a>
                <br />
                <br />
            </div>
      <div class='clear'> </div>
        {/if}
        {if $rows}
            <p> </p>
            {include file="CRM/Booking/Form/Selector.tpl"}
        {else}
            <div class="messages status no-popup">
                    <div class="icon inform-icon"></div>
                    {ts}No booking have been recorded from this contact.{/ts}
            </div>
        {/if}

        {if $associatedBooking}
            <div class="solid-border-top">
                <br />
                <div class="label"><strong>{ts}Associated Contact Booking{/ts}</strong></div>
                <div class="spacer"></div>
            </div>
            {include file="CRM/Booking/Page/AssociatedBooking.tpl"}
        {/if}
    </div>
{/if}
{/crmScope}
