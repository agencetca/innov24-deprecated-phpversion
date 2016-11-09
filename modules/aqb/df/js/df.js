var aDFAllowAjaxTo = new Array();
var aDFValues = new Array();
var aDFRequestedFieldValues = new Array();
var aDFIsCustom =  new Array();

function AqbDFUpdateDependentField(sFieldName, sParentValue, sParentFieldName) {
	try {
		/*if (sParentFieldName != undefined && sParentFieldName.length) {
			var aParentElements = document.getElementsByName(sParentFieldName);
			for (var i =0; i < aParentElements.length; i++) {
				if (aParentElements[i].type == 'select-one') {
					aParentElements[i].value = sParentValue;
				}
			}
		}*/
		var elDependentFields = new Array();
		var aElements = document.getElementsByName(sFieldName);
		for (var i =0; i < aElements.length; i++) {
			if (aElements[i].type == 'select-one') {
				elDependentFields.push(aElements[i]);
			}
		}
		if (elDependentFields.length == 0) return;
		if (sParentValue == '') {
			AqbDFResetFields(elDependentFields);
			return;
		}
		var aDependentFieldValues = AqbDFGetFieldValues(sFieldName, sParentValue);
		AqbDFSetFieldsValues(elDependentFields, aDependentFieldValues);
		for (var i =0; i < elDependentFields.length; i++) {
			elDependentFields[i].disabled = false;
			if (elDependentFields[i].onchange != undefined) elDependentFields[i].onchange(elDependentFields[i].value);
		}
	}catch(err) {
		if (err == 1 && aDFAllowAjaxTo[sFieldName]) { //try AJAX in first time
			AqbDFResetFields(elDependentFields);
			for (var i =0; i < elDependentFields.length; i++) elDependentFields[i].options.add(new Option(sAqbDFLoading, '', true, false));
			AqbDFLoadFieldValues(sFieldName, sParentValue);
			return;
		} else if (err == 2 && aDFAllowAjaxTo[sFieldName] && !aDFRequestedFieldValues[sFieldName+sParentValue]) { //try AJAX again
			AqbDFResetFields(elDependentFields);
			for (var i =0; i < elDependentFields.length; i++) elDependentFields[i].options.add(new Option(sAqbDFLoading, '', true, false));
			AqbDFLoadFieldValues(sFieldName, sParentValue);
			return;
		}
		AqbDFResetFields(elDependentFields);
		return;
	}
}
function AqbDFGetFieldValues(sFieldName, sPart) {
	if (aDFValues[sFieldName] == undefined) throw 1; //wasn't loaded at all
	if (aDFValues[sFieldName][sPart] == undefined) throw 2; //was already loading something
	if (aDFValues[sFieldName][sPart]['value'].length == 0) throw 3; //loaded zero set

	return aDFValues[sFieldName][sPart];
}
function AqbDFSetFieldsValues(elFields, aFieldValues) {
	for (var i =0; i < elFields.length; i++) {
		elFields[i].innerHTML = '';
		for (var j = 0; j < aFieldValues['value'].length; j++)
			elFields[i].options.add(new Option(aFieldValues['name'][j], aFieldValues['value'][j], true, false));
	}
}
function AqbDFResetFields(elFields){
	for (var i =0; i < elFields.length; i++) {
		elFields[i].innerHTML = '';
		if (elFields[i].onchange != undefined) elFields[i].onchange('');
		elFields[i].disabled = true;
	}
}
function AqbDFLoadFieldValues(sFieldName, sParentValue) {
	jQuery.getScript(sAqbDFHomeUrl+"action_get_values/"+sFieldName+'/'+sParentValue+'/'+aDFIsCustom[sFieldName]+'/');
	aDFRequestedFieldValues[sFieldName+sParentValue] = true;
}