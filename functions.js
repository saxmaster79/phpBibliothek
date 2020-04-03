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

function download_csv(csv, filename) {
	var csvFile;
	var downloadLink;

	// CSV FILE
	csvFile = new Blob([csv], { type: "text/csv;charset=UTF-8" });

	// Download link
	downloadLink = document.createElement("a");

	// File name
	downloadLink.download = filename;

	// We have to create a link to the file
	downloadLink.href = window.URL.createObjectURL(csvFile);

	// Make sure that the link is not displayed
	downloadLink.style.display = "none";

	// Add the link to your DOM
	document.body.appendChild(downloadLink);
	downloadLink.click();
}

function export_table_to_csv(table, filename) {
	var csv = [];
	var rows = document.querySelectorAll("table tr");

	for (var i = 0, row; row = table.rows[i]; i++) {
		var rowCsv = [];
		for (var j = 0, cell; cell = row.cells[j]; j++) {
			rowCsv.push('"'+cell.innerText.replace(/"/g, '""')+'"')
		}
		csv.push(rowCsv.join(";"));
	}
	return "\uFEFF"+csv.join("\n");
}

function exportTableToCsv(id) {
	var table = document.getElementById(id);
	var filename = "table"+id+".csv";
	var csv = export_table_to_csv(table, filename);
	download_csv(csv, filename);
}

