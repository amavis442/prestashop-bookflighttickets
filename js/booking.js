/**
 * Created by patrick on 12/9/13.
 */
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
		$('input#flightdeparturedate').datepicker().show();
	}
	if ($('input#flightreturndate') != undefined) {
		$('input#flightreturndate').datepicker().show();
	}

	if ($('input#flightfrom') != undefined) {
		$("input#flightfrom").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: "http://ws.geonames.org/searchJSON",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						name_startsWith: request.term
					},
					success: function (data) {
						response($.map(data.geonames, function (item) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
								value: item.name
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function (event, ui) {
				/* log(ui.item ?
						"Selected: " + ui.item.label :
						"Nothing selected, input was " + this.value); */
			},
			open: function () {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function () {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});
	}

	if ($('input#flightto') != undefined) {
		$("input#flightto").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: "http://ws.geonames.org/searchJSON",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						name_startsWith: request.term
					},
					success: function (data) {
						response($.map(data.geonames, function (item) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
								value: item.name
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function (event, ui) {
				/* log(ui.item ?
						"Selected: " + ui.item.label :
						"Nothing selected, input was " + this.value); */
			},
			open: function () {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function () {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});
	}
});