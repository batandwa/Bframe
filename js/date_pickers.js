addEvent(window, "load", datePickers_load);

function datePickers_load()
{
	getElementsByClass("date_picker").click(datePicker_click);
}

function datePicker_click(e)
{
	var targ;
	if (!e) { var e = window.event; }
	if (e.target) { targ = e.target; }
	else if (e.srcElement) { targ = e.srcElement; }
	
	if(targ.rel != null && targ.rel!="")
	{	
		displayDatePicker(targ.rel, this, "ymd", "-");
	}
	return false;
}

