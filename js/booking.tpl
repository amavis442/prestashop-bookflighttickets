<script type="text/javascript">
// <![CDATA[
if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
	baseUri = baseDir;

var booking = {
	update: function (id_product) {

		var arrival = $('#booking_from_' + id_product).val();
		var depart = $('#booking_to_' + id_product).val();

		var bookURL = baseDir + 'modules/booking/ajax.php';
		var diff = 1;

		if (arrival != '' && depart != '' && !(typeof arrival === 'undefined') && !(typeof depart === 'undefined')) {
			$.ajax({
				headers: { "cache-control": "no-cache" },
				url: bookURL + '?rand=' + new Date().getTime(),
				type: 'POST',
				async: true,
				cache: false,
				data: 'id_product=' + id_product + '&from=' + arrival + '&to=' + depart + '&token=' + static_token,
				success: function (jsonData, textStatus, jqXHR) {
					var json = $.parseJSON(jsonData);
					$('input:[name=quantity_' + id_product + '_0_0_0]').val(json.diff);

					if (!$('#booking_from_' + id_product).hasClass('vliegtickets') && json.diff > 0) {
						updateQty(json.diff, true, 'input:[name=quantity_' + id_product + '_0_0_0]');
					}
				},
				fail: function (msg) {
					alert(msg);
				}
			});
		} else if (arrival != '' && !(typeof arrival === 'undefined')) {
			if ($('#booking_from_' + id_product).hasClass('snappers') || $('#booking_from_' + id_product).hasClass('arrangementen')) {
				$.ajax({
					headers: { "cache-control": "no-cache" },
					url: bookURL + '?rand=' + new Date().getTime(),
					type: 'POST',
					async: true,
					cache: false,
					data: 'id_product=' + id_product + '&from=' + arrival + '&nodepart=true&token=' + static_token,
					success: function (jsonData, textStatus, jqXHR) {
						var json = $.parseJSON(jsonData);
					},
					fail: function (msg) {
						alert(msg);
					}
				});
			}
			$('#booking_to_' + id_product).datepicker("option", "minDate", arrival);
		}
	}
}


//when document is loaded...
$(document).ready(function () {
	var names = '';
	var mindate = new Date();

	if ($('.booking_date') != undefined) {
		$('.booking_date').each(function () {
			names += this.name + ' ';
			var ids = $(this).attr('id').split('_');

			$('#' + this.id).datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 3,
				onClose: function (selectedDate) {
					/* Ajax class naar de juiste controller graag */
					booking.update(ids[2]);
				}
			});
			//$( '#' + this.id).setDefaults($.datepicker.regional[ "nl" ]);
			$('#' + this.id).datepicker("option", "dateFormat", "dd-mm-yy");
			$('#' + this.id).datepicker("option", "minDate", mindate);
		});
	}

	if ($('input#flightdeparturedate') != undefined) {
		$('input#flightdeparturedate').datepicker();
		$('input#flightdeparturedate').datepicker("option", "minDate", new Date());
	}
	if ($('input#flightreturndate') != undefined) {
		$('input#flightreturndate').datepicker();
		$('input#flightreturndate').datepicker("option", "minDate", new Date());
	}

	if ($('#flightfrom') != undefined) {
		$("#flightfrom")
			.autocomplete(
				//'{if $search_ssl == 1}{$link->getModuleLink('booking','Searchflight', true)|addslashes}{else}{$link->getModuleLink('booking','searchflight')|addslashes}{/if}', {
				'/index.php?fc=module&module=booking&controller=Searchflight', {
					dataType: "json",
					minChars: 2,
					max: 10,
					width:200,
					selectFirst: false,
					scroll: false,
					extraParams: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						ajaxSearch: 1,
						id_lang: {$cookie->id_lang}
					},
					formatItem: function(data, i, max, value, term) {
						return data.country + ' > ' + data.location + ' (' + data.code + ')';
					},
					parse: function(data) {
						var mytab = new Array();
						for (var i = 0; i < data.length; i++)
							mytab[mytab.length] = { data: data[i], value: data[i].code + '::' + data[i].location };
						return mytab;
					}
				}
			)
			.result(function(event, data, formatted) {
				$('#flightfrom').val(formatted);
				$('#fl_l1').val(data.id);
			});
	};


	if ($('#flightto') != undefined) {
		$("#flightto")
			.autocomplete(
				//'{if $search_ssl == 1}{$link->getModuleLink('booking','Searchflight', true)|addslashes}{else}{$link->getModuleLink('booking','searchflight')|addslashes}{/if}', {
				'/index.php?fc=module&module=booking&controller=Searchflight', {
					dataType: "json",
					minChars: 2,
					max: 10,
					width:200,
					selectFirst: false,
					scroll: false,
					extraParams: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						ajaxSearch: 1,
						id_lang: {$cookie->id_lang}
					},
					formatItem: function(data, i, max, value, term) {
						return data.country + ' > ' + data.location + ' (' + data.code + ')';
					},
					parse: function(data) {
						var mytab = new Array();
						for (var i = 0; i < data.length; i++)
							mytab[mytab.length] = { data: data[i], value: data[i].code + '::' + data[i].location};
						return mytab;
					}
				}
			)
			.result(function(event, data, formatted) {
				$('#flightto').val(formatted);
				$('#fl_l2').val(data.id);
			});
	};
});
// ]]>
</script>