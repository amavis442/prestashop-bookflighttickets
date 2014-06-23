{if $foundSchedule}
<style>
#samenvatting {
    max-width:400px;
    background-color:eee;
}

#samenvatting h3 {
	padding:8px;
	border-bottom:1px solid #ccc;
	font-weight:bold;
	font-size:12px;
	color:#eee;
	text-transform:uppercase;
	background:url(/themes/pame/img/bg_form_h3.png) repeat-x 0 0 #989898
}

#samenvatting .form_content {
    padding-top:2px;
}

#samenvatting label {
    width:100px;
    color:#eee;
    display:inline-block;
    
}

#samenvatting span {
    width:100px;
    color:#eee;
    display:inline-block;
}

#samenvatting input {
    width:150px;
}

#samenvatting input.text {
    padding:0 5px;
	height:20px;
	width:220px;/* 230 */
	border:1px solid #ccc;
	color:#4e0b0b;
	background:url(/themes/pame/img/bg_discount_name.png) repeat-x 0 0 #fff;
	line-height:20px;
}

#samenvatting p.radio input {
    width:20px;
}

#samenvatting p.radio label {
    display:inline;
}

#samenvatting p.radio {
    display: inline-block;
}

table {
    color:#4E0B0B;
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
    width:500px;

}

div.info h6 {
    width:90%;
    height:20px;
    background-color:none repeat scroll 0 0 white;
    color:#4E0B0B;
    font-size: 12px;
    font-weight: bold;
    padding: 6px 11px; 
    text-transform: uppercase;
    border-bottom: 1px solid #4E0B0B;
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
    text-align:right;
}

div.info .title_block span {
   font-size: 16px;
   color:  #4E0B0B;
   padding: 2px 4px 2px 4px;
   text-shadow: none;
}

    
</style>
<script>
function fillData(id1,id2,id3,id4,frm) {
    $('#booking_dep').val(id1+'_'+id2);
    $('#booking_ret').val(id3+'_'+id4);
    frm.submit();
}
</script>

<form method="post" action="{$link->getModuleLink('bookflighttickets', 'Pnr', [], true)|escape:'html'}" class="block" id="bookflightticketsform">
    <input type='hidden' name="booking_dep" id="booking_dep" value=""/>
    <input type='hidden' name="booking_ret" id="booking_ret" value=""/>
    <input type='hidden' name='token' value='{$token}' />

    {foreach $schedules as $n=>$schedule}
<div class='info'>
    <p class="title_block">{l s='Flightticket'} <span class="button_large">{displayPrice price=$schedule.totalprice}</span></p>
    <div>
        <h6>{strtotime($schedule.to.departure)|date_format:'%A %d %B %Y'}</h6>
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
                <td>{strtotime($schedule.to.departure)|date_format:'%I:%M %p'}</td>
                <td>{$schedule.to.locations[0].location}</td>
                <td>{$schedule.to.arrival|date_format:'%I:%M %p'}</td>
                <td>{$schedule.to.locations[1].location}</td>
                <td>{$schedule.to.traveltime}</td>
                <td>{$schedule.to.inventory.designation}</td>
                <td>{$schedule.to.type}</td>
            </tr>
        </tbody>
        </table>
    </div>

    
    <div>
    <h6>{strtotime($schedule.back.departure)|date_format:'%A %d %B %Y'}</h6>
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
            <td>{strtotime($schedule.back.departure)|date_format:'%I:%M %p'}</td>
            <td>{$schedule.back.locations[0].location}</td>
            <td>{$schedule.back.arrival|date_format:'%I:%M %p'}</td>
            <td>{$schedule.back.locations[1].location}</td>
            <td>{$schedule.back.traveltime}</td>
            <td>{$schedule.back.inventory.designation}</td>
            <td>{$schedule.back.type}</td>
        </tr>
        <tr>
            <td colspan='7' style='text-align:right'><input type='button' name='boeken' value='Selecteer deze vlucht >' class="button_large" onclick="fillData({$schedule.to.id_schedule},{$schedule.to.id_product},{$schedule.back.id_schedule},{$schedule.back.id_product},this.form)"/></td>
        </tr>
    </tbody>
    </table>
    </div>
</div>  
    {/foreach}
</form>
{else} 
Helaas, er zijn geen resultaten gevonden.
{/if}