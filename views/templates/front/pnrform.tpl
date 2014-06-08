<style>
#pnr_form {
    max-width:400px;
    background-color:eee;
}

#pnr_form h3 {
	padding:8px;
	border-bottom:1px solid #ccc;
	font-weight:bold;
	font-size:12px;
	color:#4E0B0B;
	text-transform:uppercase;
	background:url(/themes/pame/img/bg_form_h3.png) repeat-x 0 0 #989898
}

#pnr_form .form_content {
    padding-top:2px;
}

#pnr_form label {
    width:100px;
    color:#4E0B0B;
    display:inline-block;
    padding: 0 4px;
    
}

#pnr_form span {
    width:100px;
    color:#4E0B0B;
    display:inline-block;
    padding: 0 4px;
}

#pnr_form input {
    width:150px;
}

#pnr_form input.text {
    padding:0 5px;
	height:20px;
	width:220px;/* 230 */
	border:1px solid #ccc;
	color:#4e0b0b;
	background:url(/themes/pame/img/bg_discount_name.png) repeat-x 0 0 #fff;
	line-height:20px;
}
#pnr_form p.radio input {
    width:20px;
}
#pnr_form p.radio label {
    display:inline;
}

#pnr_form p.radio {
    display: inline-block;
}



table thead {
    color:grey;
}

table .info {
    color:grey;
}

table thead tr {
    height:10px;
    border: 0;
}

div.info {
    background-color:white;
    color:#4E0B0B;
    width:510px;
}

div.form_content *,label {
    color:#4E0B0B;
    
}

div.info h6 {
    width:100%;
    height:20px;
    background-color:none repeat scroll 0 0 white;
    color:#4E0B0B;
    font-size: 12px;
    font-weight: bold;
    padding: 6px 11px; 
    text-transform: uppercase;
}

table.infotable{
    margin-bottom: 20px;
    border:0px;
    width:100%;
}

table.infotable td{
    padding:4px 6px 4px 6px;
}

div.info .title_block {    
    background: none repeat scroll 0 0 #6A7476;
    color: #FFFFFF;
    font-size: 12px;
    font-weight: bold;
    padding: 6px 11px;
    text-shadow: 0 1px 0 #000000;
    text-transform: uppercase;
}

#submitPnr {
    margin: 8px 2px 4px 0px;
}

.formError {
    border:1px solid #4E0B0B;
}
 
</style>
<div class='info'>
    <p class="title_block">{l s='Ticket informatie'}</p>
    <div>
        <h6>{strtotime($s1.departure)|date_format:'%A %d %B %Y'}</h6>
        <table class='infotable'>
        <thead>
        <tr>
            <td>vertrek</td>
            <td></td>
            <td>aankomst</td>
            <td></td>
            <td>reisduur</td>
            <td>vluchtnummer</td>
            <td></td>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{strtotime($s1.departure)|date_format:'%I:%M %p'}</td>
                <td>{$s1.origin}</td>
                <td>{$s1.arrival|date_format:'%I:%M %p'}</td>
                <td>{$s1.destination}</td>
                <td>{$s1.traveltime}</td>
                <td>{$s1.designation}</td>
                <td>{$s1.type}</td>
            </tr>
        </tbody>
        </table>
    </div>

    {if $s2}
    <div>
    <h6>{strtotime($s2.departure)|date_format:'%A %d %B %Y'}</h6>
    <table class='infotable'>
    <thead>
    <tr>
        <td>vertrek</td>
        <td></td>
        <td>aankomst</td>
        <td></td>
        <td>reisduur</td>
        <td>vluchtnummer</td>
        <td></td>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>{strtotime($s2.departure)|date_format:'%I:%M %p'}</td>
            <td>{$s2.origin}</td>
            <td>{$s2.arrival|date_format:'%I:%M %p'}</td>
            <td>{$s2.destination}</td>
            <td>{$s2.traveltime}</td>
            <td>{$s2.designation}</td>
            <td>{$s2.type}</td>
        </tr>
    </tbody>
    </table>
    </div>
    {/if}
 </div>   
 <script>
 $(document).ready(function(){
	    $('#pnr_form').bind('submit',checkForm);

	 }); 

function checkForm()
{
	var fdata = $('#pnr_form').serialize() + '&checkform=true';
	var formOk = false;
	
    $.ajax({
        url : '{$link->getModuleLink('booking', 'Pnr')}',
        dataType: 'json',
        data: fdata,
        type: 'post',
        async: false,
        success: function(data,txt) {
            //var json = jQuery.parseJSON(data);
            var names= '';
            var elm = '';
            formOk = true;
            
            $.each(data.result, function(index,item) {
                $.each(item, function(name,val) {
                   elm = '#Passenger_' + name + '_' + index+'_error';
                   if (name == 'days') {
                       elm = '#Passenger_birthdate_'+ index +'_error';
                   }
                   if (!val.valid) {
                       $(elm).html(val.msg);
                       formOk = false;
                   } else {
                	   $(elm).html('');
                   }
                });
             });
            
        },
        error: function(data,txt) {
            alert('Er is iets fout gegaan. Probeer het later opnieuw.');
        },
        complete: function(data,txt) {
           // alert(txt);
        }
    
    });
    if (formOk) {
        return true;
    }
	return false;
}
 
 </script>
 <div id='errors'></div>
<form action="{$link->getModuleLink('booking', 'Pnr')|escape:'html'}" method="post" id="pnr_form" name='pnr_form' class="std" autocomplete="on" autofill="on">
    <input type='hidden' name='token' value='{$token}' />
    {for $c=1 to ($num_adults + $num_children + $num_baby)}
 	<div class='info'>
 	  	<p class='title_block'>{l s='Passagier'} #{$c} {if $c ==1}Hoofd{/if}</p>
    	<div class='form_content'>
    	   {if $c == 1}
    		<p class="required text">
    			<label for="email">{l s='Email'} <sup>*</sup></label>
    			<input type="text" class="text" id="Passenger_email_{$c}" name="Passenger[{$c}][email]" value="{if isset($guestInformations) && $guestInformations.email}{$guestInformations.email}{/if}" />
    		      <span id='Passenger_email_{$c}_error'></span>
    		</p>
    		{/if}
    		<p class="required radio required">
    			<span>{l s='Geslacht'}<sup>*</sup></span>
    			{foreach from=$genders key=k item=gender}
    				<input type="radio" name="Passenger[{$c}][id_gender]" id="Passenger_id_gender{$gender->id_gender}" value="{$gender->id_gender}" {if isset($guestInformations.id_gender) && $guestInformations.id_gender == $gender->id_gender}checked="checked"{/if} />
    				<label for="Passenger_id_gender{$gender->id_gender}" class="top">{$gender->name}</label>
    			{/foreach}
    			<span id='Passenger_id_gender_{$c}_error'></span>
    		</p>
    		<p class="required text">
    	       <label for="Passenger_firstname_{$c}">{l s='Voornaam voluit als in ID:'}<sup>*</sup></label>
    	       <input type="text" class="text" id="Passenger_firstname_{$c}" name="Passenger[{$c}][firstname]" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
                <span id='Passenger_firstname_{$c}_error'></span>
            </p>
            <p class="required text">
    	       <label for="Passenger_lastname_{$c}">{l s='Achternaam als in ID:'}<sup>*</sup></label>
    	       <input type="text" class="text" id="Passenger_lastname_{$c}" name="Passenger[{$c}][lastname]" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
                <span id='Passenger_lastname_{$c}_error'></span>
            </p>
            <p class="required  select">
    	       <span>{l s='Geboortedatum'}<sup>*</sup></span>
    	       <select id="Passenger_days_{$c}" name="Passenger[{$c}][days]">
    		      <option value="">-</option>
    		      {foreach from=$days item=day}
    			     <option value="{$day|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
    		      {/foreach}
    	           </select>
    		      <select id="Passenger_months_{$c}" name="Passenger[{$c}][months]">
    			 <option value="">-</option>
    			 {foreach from=$months key=k item=month}
    				<option value="{$k|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
    			{/foreach}
    		  </select>
    		  <select id="Passenger_years_{$c}" name="Passenger[{$c}][years]">
    			<option value="">-</option>
    			{foreach from=$years item=year}
    				<option value="{$year|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
    			{/foreach}
    		  </select>
    		  <span id='Passenger_birthdate_{$c}_error'></span>
    	   </p>
        	<p class="required text">
        		<label for="Passenger_address1_{$c}">{l s='Adres'} <sup>*</sup></label>
    	       	<input type="text" class="text" name="Passenger[{$c}][address1]" id="Passenger_address1_{$c}" value="{if isset($guestInformations) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
    	       <span id='Passenger_address1_{$c}_error'></span>
    	   </p>
    	   <p class="required postcode text">
    		  <label for="Passenger_postcode_{$c}">{l s='Postcode '} <sup>*</sup></label>
    		  <input type="text" class="text" name="Passenger[{$c}][postalcode]" id="Passenger_postalcode_{$c}" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#Passenger_postalcode_{$c}').val($('#Passenger_postalcode_{$c}').val().toUpperCase());" />
                <span id='Passenger_postalcode_{$c}_error'></span>
            </p>
            <p class="required text">
    	       <label for="Passenger_city_{$c}">{l s='Woonplaats'} <sup>*</sup></label>
    	       <input type="text" class="text" name="Passenger[{$c}][city]" id="Passenger_city_{$c}" value="{if isset($guestInformations) && $guestInformations.city}{$guestInformations.city}{/if}" />
                <span id='Passenger_city_{$c}_error'></span>
            </p>
        
            <p class="required text">
    	       <label for="Passenger_id_number_{$c}">{l s='Legitimatienummer'}<sup>*</sup></label>
    	       <input type="text" class="text" name="Passenger[{$c}][id_number]" id="Passenger_id_number_{$c}" value="" />
                <span id='Passenger_id_number_{$c}_error'></span>
            </p>
        
            {if $c == 1}
            <p class="required text">
    	       <label for="Passenger_phone_{$c}">{l s='Telefoon'}<sup>*</sup></label>
    	       <input type="text" class="text" name="Passenger[{$c}][phone]" id="Passenger_phone_{$c}" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
                <span id='Passenger_phone_{$c}_error'></span>
            </p>
            {/if}
         </div>
    </div>   
    {/for}
    <p>
        <input type="submit" name="submitPnr" id="submitPnr" value="{l s='Samenvatting >'}" class="button_large" />
    </p>
 </form>
