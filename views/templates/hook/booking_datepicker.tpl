{if !($is_snapper OR $is_arrangement OR $is_flight) }
    <script>
    	var bookingUpdateQty = {if isset($noQntyUpdate)}{$noQntyUpdate}{else} 0{/if};
    </script>
    <label for="booking_from">Van</label>
    <input type="text" id="booking_from_{$product.id_product}" name="booking_from_{$product.id_product}" class="booking_date {strtolower($product.category)}" value="{$arrival_date}">
    {if !($is_snapper || $is_arrangement)}
    <label for="booking_to">tot</label>
    <input type="text" id="booking_to_{$product.id_product}" name="booking_to_{$product.id_product}" class="booking_date {strtolower($product.category)}"  value="{$departure_date}">
    {/if}
{/if}