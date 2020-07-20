$(function(){

	submitApplication('#a-btn','#a-form', '#a-name', '#a-fname', '#a-age', '#a-language', '#a-level', '#a-course', '#a-decs', '#a-phone', '#a-skype', '#a-email');


	function submitApplication(btn, form, name, fname, age, language, level, course, decs, phone, skype, email) {
		$(btn).on('click', function(e){

			e.preventDefault();
			var data = {};
			data.name = $(this).parents().find(name).val();
			data.fname = $(this).parents().find(fname).val();
			data.age = $(this).parents().find(age).val();
			data.language = $(this).parents().find(language).val();
			data.level = $(this).parents().find(level).val();
			data.course = $(this).parents().find(course).val();
			data.decs = $(this).parents().find(decs).val();
			data.phone = $(this).parents().find(phone).val();
			data.skype = $(this).parents().find(skype).val();
			data.email = $(this).parents().find(email).val();

			var error_form = $(this).parents().find('.error_form');

			$(this).append('<div></div>');
			$.post('../handlers/u-application.php', data, function(r){
				console.log(r);
				alert("Ваша заявка успешно отправлена. Мы свяжемся с Вами в ближайшее время");
				if(r.status==true) {

					$(form).trigger('reset');

					$('.error_form').remove();
				} else {
					error_form.text(r.error);
				}
			},"json");
			return false;
		})
	}


	$('#form').on('submit',function(){
		var data={};
		data.name=$('#d_name').val();
		data.phone=$('#d_phone').val();
		data.type=$('#d_type').val();
		data.textarea = $('#textarea-message').val();

		$(this).append('<div></div>');
		$.post('handlers/h_measure.php',data,function(r){
			$('#form').find('.loader').add();
			console.log(r);
			if(r.status==true) {
				$('#form').html('<br /><br /><div style="text-align:center"><h1>Ваша заявка успешно отправлена!</h1><h3>Мы свяжемся с вами в ближайшее время!</h3><br /><br /><br /></div>');
			} else {
				$('#form').find('.error').text(r.error);
			}
			$('#form').find('.loader').remove();
		},"json");
		return false;
	});





});









