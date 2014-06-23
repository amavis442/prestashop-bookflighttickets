<div id="bookflightticketsdiv" class="block">
	<p class="title_block">Zoek je vliegtickets</p>
	<form method="post" action="{$link->getModuleLink('bookflighttickets', 'Searchflight', [], true)|escape:'html'}" class="block" id="bookflightticketsform">
	<input type='hidden' name='fl_l1' id='fl_l1' value='{if (isset($flightdata)) }{$flightdata.fl_l1}{/if}' />
	<input type='hidden' name='fl_l2' id='fl_l2' value='{if (isset($flightdata)) }{$flightdata.fl_l2}{/if}' />
	 

	<p>Selecteer een bestemming en wij zoeken voor jou altijd de beste deals in ons gehele aanbod</p>
	<div><input type='radio' name='flighttype' value="1" {if (isset($flightdata) && $flightdata['flighttype'] == 1)}checked{/if} > Retour</div>
	<div><input type='radio' name='flighttype' value="2" {if (isset($flightdata) && $flightdata['flighttype'] == 2)}checked{/if} > Enkele reis</div>
	<!-- // <div><input type='radio' name='flighttype' value="3"> Meerdere bestemmingen / stopover</div>// -->
    
			<p>Mijn vlucht</p>
	<div for="flightfrom"><label>Van</label><input type="text" name="flightfrom" id="flightfrom" value='{if (isset($flightdata)) }{$flightdata.flightfrom}{/if}' /></div>
	<div><label for="flightto">Naar</label><input type="text" name="flightto" id="flightto" value='{if (isset($flightdata)) }{$flightdata.flightto}{/if}' /></div>
	<label>Heen</label><input type="text" name="flightdeparturedate" id="flightdeparturedate" value="{if (isset($flightdata)) }{$flightdata.flightdeparturedate}{/if}">
	<label>Terug</label><input type="text" name="flightreturndate"  id="flightreturndate" value="{if (isset($flightdata)) }{$flightdata.flightreturndate}{/if}">

	<div><input type="checkbox" value="1" name="flightspread">Zoek rond deze data (+/- 3 dagen)</div>
	<div><label for="flightnumadults" class="select1">Volwassenen</label> <select name="flightnumadults">
			{foreach from=array(1,2,3,4,5,6,7,8,9) item=n}
				<option value="{$n}" {if $n eq $flightdata.flightnumadults}selected{/if}>{$n}</option>
			{/foreach}
	</select></div>
	<div><label for="flightnumchildren" class="select1">Kinderen (2-11)</label>	<select name="flightnumchildren">
				{foreach from=array(0,1,2,3,4,5,6,7,8) item=n}
					<option value="{$n}" {if $n == $flightdata['flightnumchildren']}selected{/if}>{$n}</option>
				{/foreach}
	</select></div>
	<div><label for="flightnumbaby" class="select1">Baby's (0-1)</label>	<select name="flightnumbaby">
				{foreach from=array(0,1) item=n}
				<option value="{$n}" {if $n == $flightdata['flightnumbaby']}selected{/if}>{$n}</option>
			{/foreach}
	</select></div>
	<div class="search"><input type="submit" value="" name="btnBookingAanbod" id="btnBookingAanbod" class="submitbutton"></div>
	</form>
</div>
{include file="$self/js/bookflighttickets.tpl"}