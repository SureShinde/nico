function show_hide_field(objName,condition)
{
	document.getElementById(objName).disabled = condition !=0 ? "disabled" : "";
}