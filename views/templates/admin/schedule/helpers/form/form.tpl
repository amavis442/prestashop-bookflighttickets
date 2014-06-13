{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == "datetime"}
        <input type="text"
               size="{$input.size}"
               data-hex="true"
               {if isset($input.class)}class="{$input.class}"
               {else}class="datepicker"{/if}
               name="{$input.name}"
               id="{$input.name}"
               value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
        <script>
            $(document).ready(function() {
                $('#{$input.name}').datetimepicker({
                        'dateFormat' : 'yy-mm-dd',
                        'timeFormat' : 'hh:mm:ss',
                    });
            });
        </script>
    {else}
        {$smarty.block.parent}
    {/if} 
{/block}