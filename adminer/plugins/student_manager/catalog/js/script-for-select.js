$(function(){
	data = {};
	data.value = $('input[name=a_7_2_poluchatel]').val();

	
	
	
	$.ajax({
		method : 'POST',
		url : 'hand/h_replace_input.php',
		data : data,
		dataType : 'json',
		success : function(response){
			if(response.status == false) {
				
			}else {
				$('input[name=a_7_2_poluchatel]').replaceWith('<select name="a_7_2_poluchatel" class="attr_val" id="replaceSelect"><option selected="selected">Выберите получателя:</option></select>');
				$('#replaceSelect').append(response.option);
			}

		}
	})



})

