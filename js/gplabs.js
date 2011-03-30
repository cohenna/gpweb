function toggleDiv(divid, buttonid, showtext, hidetext) {
	//var text = ' Previous Message';
	var text;
	if($('#' + divid).css('display') == 'none') {
		//text = 'Hide ' + text;
		text = hidetext;
	}
	else {
		//text = 'Show ' + text;
		text = showtext;
	}
	$('#' + buttonid).val(text);
	$('#' + divid).toggle();
}	


