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
}
    
</style>

<div class='info'>
    <p class="title_block">{l s='Price information'}</p>
    <table class='std'>
        <tr>
            <td>Ticket</td>
            <td>{$p1->name}</td>
            <td>{displayWtPriceWithCurrency price=$p1->price convert=1}</td>
        </tr>
        {if $p2}
        <tr>
            <td>Ticket</td>
            <td>{$p2->name}</td>
            <td>{displayWtPriceWithCurrency price=$p2->price convert=1}</td>
        </tr>
        {/if}
        <tr>
            <td colspan='3' style='text-align:right'>
                {displayWtPriceWithCurrency price=$total convert=1}
            </td>
        </tr>
    </table>
</div>

<div class='info'>
    <p class="title_block">{l s='Check your data'}</p>
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


    {foreach $passengers as $k=>$passenger}
    <div>
    <h6>{l s='Traveler'} #{$k+1}</h6>
    <table class='infotable'>
            <tr><td class='info'>Voornaam</td><td>{$passenger.firstname}</td><td class='info'>Achternaam</td><td>{$passenger.lastname}</td></tr>
            <tr><td class='info'>Geboortedatum</td><td>{$passenger.days}-{$passenger.months}-{$passenger.years}</td><td class='info'>Geslacht</td><td>{$passenger.gender}</td></tr>
            <tr><td class='info'>ID card</td><td>{$passenger.id_number}</td><td></td><td></td></tr>
    </tbody>
    </table>
    <h6>{l s='Contact'}</h6>
    <table class='infotable'>
            <tr><td class='info'>Postcode</td><td>{$passenger.postalcode}</td><td class='info'>Adres</td><td>{$passenger.address1}</td>
            <tr><td class='info'>Woonplaats</td><td>{$passenger.city}</td><td class='info'>Telefoon</td><td>{$passenger.phone}</td></tr>
            <tr><td class='info'>Email</td><td colspan='3'>{$passenger.email}</td></tr>
    </table>
    </div>
    {/foreach}
</div>
    
    
<p>    
<form method='post' action='{$link->getModuleLink('bookflighttickets','Reservation')}'>
<input type='submit' class="button_large" id='makeReservation' name='makeReservation' value="{l s='Payment >'}" />
<!--  // 
<a href='{$link->getModuleLink('bookflighttickets','Reservation')}' style='color:white' class="button_large" >Reserveren</a>
// -->
</form>
</p>