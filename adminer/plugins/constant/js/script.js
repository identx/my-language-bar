var loading;

$(function(){



$('.button').on('click', function(){
	progress_show();
	var f = $('.form_one').serializeArray();
	x_addConstant(JSON.stringify(f), callBack);
	// x_returnNumber(function(html){
	// 	alert(html)
	// });
})

$('body').on('click','.del', function(){
	var conf = confirm('Вы хотите удалить запись?');
	if(conf){
		progress_show();
		var data = $(this).data('id');
		x_deleteConstant(data, callBack);
	}else {
		return false;
	}
})

$('body').on('click','.edit', function(){

	progress_show();	
	var v = $(this).data('edit');
	var val = $('input#val-'+v).val();
	var attr = $('input#attr-'+v).val();
	var arr = {v, val, attr};
	x_editConstant(JSON.stringify(arr), callBack);
})

})

function callBack(html) {
		$('.table').html(html);
		progress_hide();
	}

function callBack2(html) {
		$('.dell').append(html,'<br/>');
		progress_hide();
	}





function progress_show(){
	loading=setTimeout(function(){$('#loading').show();},100);
}

function progress_hide(){
	clearTimeout(loading);
	$('#loading').hide();
}

