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


});









