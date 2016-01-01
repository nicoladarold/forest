const API_BASEURL = 'http://www.awesomesite.com';
const API_DISCOVER = '/forest/discover/__forestId__/__treeNumber__';

// display result in table
function displayResult(data) {
    $('#results tbody').empty();
    var response = jQuery.parseJSON(data);
    // for each response row
    for (var i=0; i<response.length; i++) {
        // if result has 2 items, it could be a correct response
        if (response[i].length == 2) {
            // if each result is made of 2 results, the response was really correct
            if ((response[i][0].length == 2) && (response[i][1].length == 2)) {
                var row = $('<tr><td>' + i + '</td>' + 
                    '<td>(' + response[i][0][0] + ',' + response[i][0][1] + ')</td>' + 
                    '<td>(' + response[i][1][0] + ',' + response[i][1][1] + ')</td>' +
                    '</tr>');
                $('#results tbody').append(row);
            }
        }
    }
}



function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}


$(document).ready(function(){

	
	$('#btnCalculate').click(function(){
		var forestId = $('#txtForestId').val();
		var treeNumber = $('#txtTreeNumber').val();
		
		if (isNumeric(forestId) && isNumeric(treeNumber)) {
			var url = API_BASEURL + API_DISCOVER;
			url = url.replace('__forestId__', forestId);
			url = url.replace('__treeNumber__', treeNumber);
			
			// call API
			$.ajax({
				url: url
			})
			.done(function(data){
				displayResult(data);
			})
			.fail(function(){
				displayResult(data);
			});
		}
	});
	
	
	
	
	
	
});
