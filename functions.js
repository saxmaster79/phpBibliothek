function addTextField(name, button, selectId){
	
	if(document.getElementById(name+"Id")){
		var newText=document.getElementById(name+"Id");
		
		var aOption = new Option(newText.value , newText.value);
        theSelect = document.getElementById(selectId);
        theSelect.options[0] = aOption;
        theSelect.selectedIndex = 0;
    	var span=document.getElementById(name+"0");
        span.removeChild(newText);
		        
	}else {
		var span=document.getElementById(name+"0");
		var elements=document.getElementById(name+"1");
		var newText = document.createElement("input");
		newText.type="text";
		newText.id=name+"Id";
		span.appendChild(newText);
		newText.focus();	
	}
}

