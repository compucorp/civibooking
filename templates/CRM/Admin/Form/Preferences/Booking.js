
(function ($){

	var colorpickerInput1 = $("#slot_booked_colour");
	var colorpickerInput2 = $("#slot_provisional_colour");
	var colorpickerInput3 = $("#slot_being_edited_colour");
	var colorpickerInput4 = $("#slot_new_colour");

	colorpickerInput1.spectrum({
	    showPalette: true,
	    preferredFormat: "hex",
	    palette: [
	              ['black', 'white', 'blanchedalmond',
	              'rgb(255, 128, 0);', 'hsv 100 70 50'],
	              ['red', 'yellow', 'green', 'blue', 'violet']
	              ]
	});

	colorpickerInput2.spectrum({
		showPalette: true,
		preferredFormat: "hex",
		palette: [
		          ['black', 'white', 'blanchedalmond',
		          'rgb(255, 128, 0);', 'hsv 100 70 50'],
		          ['red', 'yellow', 'green', 'blue', 'violet']
		          ]
	});

	colorpickerInput3.spectrum({
		showPalette: true,
		preferredFormat: "hex",
		palette: [
		          ['black', 'white', 'blanchedalmond',
		          'rgb(255, 128, 0);', 'hsv 100 70 50'],
		          ['red', 'yellow', 'green', 'blue', 'violet']
		          ]
	});

	colorpickerInput4.spectrum({
		showPalette: true,
		preferredFormat: "hex",
		palette: [
		          ['black', 'white', 'blanchedalmond',
		          'rgb(255, 128, 0);', 'hsv 100 70 50'],
		          ['red', 'yellow', 'green', 'blue', 'violet']
		          ]
	});

})(jQuery);

