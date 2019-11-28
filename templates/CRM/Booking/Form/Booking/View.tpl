{crmScope extensionKey=uk.co.compucorp.civicrm.booking}
    {* View existing booking record. *}
  <div class="crm-block crm-content-block crm-booking-view-form-block">
    <h3>{ts}View Booking - ID {/ts}{$id}</h3>
    <div class="action-link">
      <div class="crm-submit-buttons">
          {if !$is_cancelled}
              {* {if call_user_func(array('CRM_Core_Permission','check'), 'edit booking')} *}
              {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking"}
              {if ($context eq 'search' ) && $searchKey}
                  {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking&key=$searchKey"}
              {/if}
            <a class="button" href="{crmURL p='civicrm/booking/edit/' q=$urlParams}" accesskey="e"><span><div class="icon edit-icon"></div> {ts}Edit{/ts}</span></a>
              {* {/if} *}
          {/if}
          {* {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviBooking')} *}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=event"}
          {if ($context eq 'search' ) && $searchKey}
              {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=booking&key=$searchKey"}
          {/if}
        <a class="button" href="{crmURL p='civicrm/contact/view/booking' q=$urlParams}"><span><div class="icon delete-icon"></div> {ts}Delete{/ts}</span></a>
          {* {/if} *}
          {include file="CRM/common/formButtons.tpl" location="top"}
      </div>
    </div>
    <table class="crm-info-panel">
      <tr class="crm-bookingview-form-block-displayName">
        <td class="label">{ts}Primary Contact{/ts}</td>
        <td class="bold">
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contact_id"}" title="view contact record">{$displayName}</a>
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking">
        <td class="label">{ts}Booking{/ts}</td><td>
              {$title}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-associated-contact">
        <td class="label">{ts}Associated Contact{/ts}</td><td>
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$secondary_contact_id"}" title="view contact record">
              {$secondaryContactDisplayName}
          </a>
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-date-made">
        <td class="label">{ts}Date Booking Made{/ts}</td><td>
              {$booking_date|crmDate}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-event-date">
        <td class="label">{ts}Start Date{/ts}</td><td>
              {$start_date|crmDate}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-event-date">
        <td class="label">{ts}End Date{/ts}</td><td>
              {$end_date|crmDate}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-po-no">
        <td class="label">{ts}PO NO{/ts}</td><td>
              {$po_number}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking_status">
        <td class="label">{ts}Booking status{/ts}</td><td>
              {$status}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking_payment_status">
        <td class="label">{ts}Payment status{/ts}</td><td>
              {if $payment_status eq ''}
                  {ts}Unpaid{/ts}
              {else}
                  {$payment_status}
              {/if}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-description">
        <td class="label">{ts}Description{/ts}</td><td>
              {$description}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-note">
        <td class="label">{ts}Note{/ts}</td><td>
              {$note}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-participant-estimate">
        <td class="label">{ts}Participant estimated{/ts}</td><td>
              {$participants_estimate}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-paritipant-actual">
        <td class="label">{ts}Participant actual{/ts}</td><td>
              {$participants_actual}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Sub Total{/ts}</td><td>
              {$sub_total|string_format:"%.2f"}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Discount amount{/ts}</td><td>
              {$discount_amount}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Total{/ts}</td><td>
              {$total_amount}
        </td>
      </tr>
    </table>
    {include file="CRM/common/customDataBlock.tpl"}
    <h3>{ts}Resources{/ts}</h3>
    <table class="selector">
      <thead class="sticky">
      <tr>
        <th scope="col">{ts}Resource{/ts}</th>
        <th scope="col">{ts}Start Date{/ts}</th>
        <th scope="col">{ts}End Date{/ts}</th>
        <th scope="col">{ts}Configuration{/ts}</th>
        <th scope="col">{ts}Note{/ts}</th>
        <th scope="col">{ts}Price Per Unit{/ts}</th>
        <th scope="col">{ts}Quantity{/ts}</th>
        <th scope="col">{ts}Total Amount{/ts}</th>
      </tr>
      </thead>
        {foreach from=$slots item=slot}
          <tr class="{cycle values="odd-row,even-row"}">
            <td>{$slot.resource_label}</td>
            <td>{$slot.start|crmDate}</td>
            <td>{$slot.end|crmDate}</td>
            <td>{$slot.config_label}</td>
            <td>{$slot.note}</td>
            <td>{$slot.unit_price}</td>
            <td>{$slot.quantity}</td>
            <td>{$slot.total_amount|number_format:2:".":","}</td>
          </tr>
        {/foreach}
    </table>
      {if $sub_slots}
        <h3>{ts}Unlimited Resources{/ts}</h3>
        <table class="selector">
          <thead class="sticky">
          <tr>
            <th scope="col">{ts}Resource{/ts}</th>
            <th scope="col">{ts}Parent Resource{/ts}</th>
            <th scope="col">{ts}Time Required{/ts}</th>
            <th scope="col">{ts}Configuration{/ts}</th>
            <th scope="col">{ts}Note{/ts}</th>
            <th scope="col">{ts}Price Per Unit{/ts}</th>
            <th scope="col">{ts}Quantity{/ts}</th>
            <th scope="col">{ts}Total Amount{/ts}</th>
          </tr>
          </thead>

            {foreach from=$sub_slots item=subSlot}
              <tr class="{cycle values="odd-row,even-row"}">
                <td>{$subSlot.resource_label}</td>
                <td>{$subSlot.parent_resource_label}</td>
                <td>{$subSlot.time_required|crmDate}</td>
                <td>{$subSlot.config_label}</td>
                <td>{$subSlot.note}</td>
                <td>{$subSlot.unit_price}</td>
                <td>{$subSlot.quantity}</td>
                <td>{$subSlot.total_amount|number_format:2:".":","}</td>
              </tr>
            {/foreach}
        </table>
      {/if}
      {if $adhoc_charges}
        <h3>{ts}Adhoc Charges Items{/ts}</h3>
        <table class="selector">
          <thead class="sticky">
          <tr>
            <th scope="col">{ts}Item{/ts}</th>
            <th scope="col">{ts}Price Per Unit{/ts}</th>
            <th scope="col">{ts}Quantity{/ts}</th>
            <th scope="col">{ts}Total Amount{/ts}</th>
          </tr>
          </thead>
            {foreach from=$adhoc_charges item=charges}
              <tr class="{cycle values="odd-row,even-row"}">
                <td>{$charges.item_label}</td>
                <td>{$charges.unit_price}</td>
                <td>{$charges.quantity}</td>
                <td>{$charges.total_amount|number_format:2:".":","}</td>
              </tr>
            {/foreach}
        </table>
      {/if}

      {if $cancellation_charges}
        <h3>{ts}Cancellation{/ts}</h3>
        <table class="crm-info-panel">
            {foreach from=$cancellation_charges item=cancellation}
              <tr class="crm-bookingview-form-block-displayName">
                <td class="label">{ts}Date Cancelled{/ts}</td>
                <td class="bold">
                    {$cancellation.cancellation_date|crmDate}
                </td>
              </tr>
              <tr class="crm-bookingview-form-block-booking">
                <td class="label">{ts}Booking Price{/ts}</td><td>
                      {$cancellation.booking_price|number_format:2:".":","}
                </td>
              </tr>
              <tr class="crm-bookingview-form-block-associated-contact">
                <td class="label">{ts}This booking was cancelled{/ts}</td><td>
                      {$cancellation.prior_days} day(s) before the event was intended to take place
                </td>
              </tr>
              <tr class="crm-bookingview-form-block-date-made">
                <td class="label">{ts}Cancellation Subtotal{/ts}</td><td>
                      {$cancellation.cancellation_fee}
                </td>
              </tr>
              <tr class="crm-bookingview-form-block-event-date">
                <td class="label">{ts}Other Cancellation Charges{/ts}</td><td>
                      {$cancellation.additional_fee}
                </td>
              </tr>
              <tr class="crm-bookingview-form-block-event-date">
                <td class="label">{ts}Description{/ts}</td><td>
                      {$cancellation.comment}
                </td>
              </tr>
              <tr class="crm-bookingview-form-block-event-date">
                <td class="label">{ts}Cancellation Fee{/ts}</td><td>
                      {$cancellation.cancellation_total_fee|number_format:2:".":","}
                </td>
              </tr>
            {/foreach}
        </table>
      {/if}

      {if $contribution}
        <h3>{ts}Contribution{/ts}</h3>
        <table class="selector">
          <thead class="sticky">
          <tr>
            <th scope="col">{ts}Name{/ts}</th>
            <th scope="col">{ts}Amount{/ts}</th>
            <th scope="col">{ts}Type{/ts}</th>
            <th scope="col">{ts}Source{/ts}</th>
            <th scope="col">{ts}Received Date{/ts}</th>
            <th scope="col">{ts}Thank-you sent{/ts}</th>
            <th scope="col">{ts}Status{/ts}</th>
            <th scope="col">{ts}Premium{/ts}</th>
          </tr>
          </thead>
            {foreach from=$contribution item=contributionItem}
              <tr class="{cycle values="odd-row,even-row"}">
                <td>{$contributionItem.sort_name}</td>
                <td>{$contributionItem.total_amount}</td>
                <td>{$contributionItem.financial_type}</td>
                <td>{$contributionItem.contribution_source}</td>
                <td>{$contributionItem.receive_date|crmDate}</td>
                <td>{$contributionItem.thankyou_date}</td>
                <td>{$contributionItem.contribution_status}</td>
                <td>{$contributionItem.product_name}</td>
              </tr>
            {/foreach}
        </table>
      {/if}

    <div class="crm-submit-buttons">
        {if !$is_cancelled}
            {* {if call_user_func(array('CRM_Core_Permission','check'), 'edit booking')} *}
            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking"}
            {if ($context eq 'search' ) && $searchKey}
                {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking&key=$searchKey"}
            {/if}
          <a class="button" href="{crmURL p='civicrm/booking/add/' q=$urlParams}" accesskey="e"><span><div class="icon edit-icon"></div> {ts}Edit{/ts}</span></a>
            {*  {/if} *}
        {/if}
        {* {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviBooking')} *}
        {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=booking"}
        {if ($context eq 'search' ) && $searchKey}
            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=booking&key=$searchKey"}
        {/if}
      <a class="button" href="{crmURL p='civicrm/contact/view/booking' q=$urlParams}"><span><div class="icon delete-icon"></div> {ts}Delete{/ts}</span></a>
        {*  {/if} *}
        {include file="CRM/common/formButtons.tpl" location="top"}
    </div>
  </div>
{/crmScope}
