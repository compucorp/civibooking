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
{* Search form and results for resources *}
<div id="brs-container" class="brs-container">
  <div id="search-layout">
    <div class="crm-form-block crm-resorce-search-form-block">
      <div class="crm-accordion-wrapper crm-custom_search_form-accordion {if $rows}collapsed{/if}">
        <div class="crm-accordion-header crm-master-accordion-header">{ts}Check resource avaliability{/ts}</div><!-- /.crm-accordion-header -->
        <div id="search-form" class="crm-accordion-body">
          
        </div><!-- /.crm-accordion-body -->
      </div><!-- /.crm-accordion-wrapper -->
    </div><!-- /.crm-form-block -->
    <div id="search-result">

    </div> 

 </div>

  <div class="crm-container crm-form-block">
     <div class="crm-section selected-resources-section" style="display:none;">
          <div class="label">
              {$form.resources.label} -- to be hidden
            </div>
              <div class="content">
                {$form.resources.html}
              </div>
              <div class="clear"></div>
        </div>
    </div>
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>

</div>

{literal}
<script type="text/javascript">
cj(function() {
   cj().crmAccordions();
   //start backbone.js
   CRM.ResourceSearch.start();
});
</script>
{/literal}

