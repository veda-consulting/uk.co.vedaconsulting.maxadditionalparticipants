{* add single row *}
{if $soInstance}
     {foreach from=$elementNames key=key item=elementName}
        <tr id="string_override_table_{$key}">
          {foreach from=$elementName item=fields key=fieldid}
             <td class="label">   {$form.$fields.label}</td>
             <td class="even-row"> {$form.$fields.html}<br />
             {if $fieldid eq 0 }
                <span class="description"> {ts}Please select an event{/ts}</span></td>
             {/if}
           {/foreach}  
        </tr>
    {/foreach}

{else}
{* this template is used for adding/editing string overrides  *}
    <div class="crm-block crm-form-block crm-mailchimp-setting-form-block">
        <div class="crm-accordion-header">
          <div class="icon crm-accordion-pointer"></div> 
          {ts}Max Participants Setting{/ts}
        </div><!-- /.crm-accordion-header -->
        <table class="form-layout-compressed">
            <tr>
                <td>
                    <table>
                        {foreach from=$elementNames key=key item=elementName}
                           <tr id="string_override_table_{$key}">
                             {foreach from=$elementName item=fields key=fieldid}
                                <td class="label">   {$form.$fields.label}</td>
                                <td class="even-row"> {$form.$fields.html}<br />
                                {if $fieldid eq 0 }
                                    <span class="description"> {ts}Please select an event{/ts}</span></td>
                                {/if}
                              {/foreach}  
                            </tr>
                        {/foreach}
                    </table>
                </td>
            </tr>
        </table>
        <div style='display:none;'>{$form.totalRowCount.html}</div>
        <div class="crm-submit-buttons">
            <a class="button" onClick="Javascript:buildStringOverrideTable( false );return false;"><span><div class="icon add-icon"></div>{ts}Add Event{/ts}</span></a>
          {include file="CRM/common/formButtons.tpl" location="bottom"}
        </div> 
    </div>
{/if}
{literal}
      <style>
        .form-layout-compressed{
          background-color: #e6e6dc;
          color           : #3e3e3e;
        }
      </style>
{/literal}
{literal}
    <script type="text/javascript">
        function buildStringOverrideTable(curInstance){
            var tableId = 'string_override_table_';
            if ( curInstance ) {
                currentInstance  = curInstance;
                previousInstance = currentInstance - 1;
            } else {
                var previousInstance = cj( '[id^="'+ tableId +'"]:last' ).attr('id').slice( tableId.length );
                var currentInstance = parseInt( previousInstance ) + 1 ;
            }
            var dataUrl  = {/literal}"{crmURL q='snippet=4' h=0}"{literal} ;
            var prevInstRowId = '#string_override_table_' + previousInstance;
            dataUrl     += "&instance="+currentInstance;
            cj.ajax({ url     : dataUrl,
                      async   : false,
                      success : function( html ) {
                                cj( prevInstRowId ).after( html );
                      }
            });
        }
        cj( function( ) {
            {/literal}
              {if $stringOverrideInstances}
                  {foreach from=$stringOverrideInstances key="index" item="instance"}
                      buildStringOverrideTable( {$instance} );
                  {/foreach}
              {/if}
            {literal}
        });
    </script>
   {/literal}
   