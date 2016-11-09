checked=false;
function checkedAll (theform) {
	var aa= document.getElementById(theform);
	 if (checked == false)
          {
           checked = true
          }
        else
          {
          checked = false
          }
	for (var i =0; i < aa.elements.length; i++) 
	{
		aa.elements[i].checked = checked;
	}
}
function LoadLang(val) {
	window.location.href="?r=deanos_tools/administration/&se=sc&LangID="+val;
}
function LoadPHP(val) {
	window.location.href="?r=deanos_tools/administration/&se=pe&phpbid="+val;
}

function setMember(val) {
	var myForm = document.getElementById('setadmin');
	myForm.saction.value = "sm";
	myForm.id.value = val;
	myForm.submit();
}

function setAdmin(val) {
	var myForm = document.getElementById('setadmin');
	myForm.saction.value = "sa";
	myForm.id.value = val;
	myForm.submit();
}
