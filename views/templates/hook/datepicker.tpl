<p id="booking-datepicker">
    <label>{l s='Checkin'}</label> <input type="hidden" name='booking-datepicker-checkin-text' id='booking-datepicker-checkin-text' style="width:100px;" />
<div id="datepicker-checkin"></div>
{if $hasCheckOutDate}
    <label>{l s='Checkout'}</label> <input type="hidden" name='booking-datepicker-checkout-text' id='booking-datepicker-checkout-text' style="width:100px;" />
    <div id="datepicker-checkout"></div>
{/if}
<script>
    $('document').ready(function(){
        $('#datepicker-checkin').datepicker({
            inline: true,
            altField: '#booking-datepicker-checkin-text',
            minDate: '{date('d-m-Y')}',
            onSelect: function(dateText, Obj) {
                
                
                $.ajax({
                    url :  '{$link->getModuleLink('booking', 'Productbooking')}',
                    data: {literal}{id_product : id_product, token : token, checkin_date: dateText, ajax:true}{/literal},
                    type: 'post',
                    async: false,
                    success: function(data, txt) {
                            formOk = true;
                    },
                    error: function(data, txt) {
                        alert('{l s='Er is iets fout gegaan. Probeer het later opnieuw.'}');
                    },
                    complete: function(data, txt) {
                        // alert(txt);
                    }
                });
            }
        });
        {if $hasCheckOutDate}
            $('#datepicker-checkout').datepicker({
                inline: true,
                altField: '#booking-datepicker-checkout-text',
                minDate: '{date('d-m-Y')}',
                onSelect: function(dateText, Obj) {


                    $.ajax({
                        url :  '{$link->getModuleLink('booking', 'Productbooking')}',
                        data: {literal}{id_product : id_product, token : token, checkout_date: dateText, ajax:true}{/literal},
                        type: 'post',
                        async: false,
                        success: function(data, txt) {
                                formOk = true;
                        },
                        error: function(data, txt) {
                            alert('{l s='Er is iets fout gegaan. Probeer het later opnieuw.'}');
                        },
                        complete: function(data, txt) {
                            // alert(txt);
                        }
                    });
                }
            });
        {/if}

    });
</script>
</p>

