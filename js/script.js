$(function(){


	$('.payment-courses-select').on('change', function(){
		let select = $('.payment-package-select');
		select.html('<option disabled="disabled">Пакет</option>');
		select.attr('disabled', false);
		data = {};
		data.id_course = $(this).val();

		$.post('../handlers/get-package.php', data, function(r){
			select.html(r);

			
		},"json");

		checkCodeValue();
		checkPayBtn();

	})


	$(document).on('click', '#check-code', function(e){
		e.preventDefault();
		data = {};
		data.code = $('#payment-form-code').val();
		let info = $('.check-info');
		$.post('../handlers/check-code.php', data, function(r){
			console.log(r);
			if(r.status==true) {
				info.html(r['info']);
			checkPayBtn();
			} else {
				info.html(r['info']);
			}
		},"json");
		
		
	})

	
	$('#payment-form-code').on('change input', function(){
		checkCodeValue();
	})

	$('.review-slider').owlCarousel( {
		items : 1,
		loop:true,
		margin:10,
		nav:false,
		// autoplay: true,
		autoplayTimeout:5000,
		autoplayHoverPause:true,
		smartSpeed: 1000,
		autoHeight: true,
	})

	$('.more-text').on('click', function() {

		$('.hide-text').slideDown('easing');
		$(this).hide();
	})

	$('input[type="tel"]').mask('+7(999) 999-99-99');



	if($(window).width() > 575) {
		$(".dropdown").on('shown.bs.dropdown', function () {

    //Start
    $(this).children().css('width','327px');
    $(this).children('.dropdown-toggle').addClass('btn-active');

});

		$(".dropdown").on('hide.bs.dropdown', function () {

			$(this).children().css('width','100%');
			$(this).children('.dropdown-toggle').removeClass('btn-active');

		});
	}


})

function checkCodeValue(){
	if($('#payment-form-code').val().length != 0) {
		$('#check-code').removeAttr('disabled');
		$('#check-code').removeClass('grey-btn');
		$('#check-code').addClass('yellow-btn');
	} else {
		$('#check-code').attr('disabled', 'disabled');
		$('#check-code').addClass('grey-btn');
		$('#check-code').removeClass('yellow-btn');
		
	}
}

function checkPayBtn() {
	if($('.alert').hasClass('alert-success') && $('.payment-courses-select').val() != 0) {
		$('#payment-form-btn').removeAttr('disabled');
		$('#payment-form-btn').removeClass('grey-btn');
		$('#payment-form-btn').addClass('yellow-btn');
		console.log($('.payment-courses-select').val());
	}else {
		$('#payment-form-btn').attr('disabled', 'disabled');
		$('#payment-form-btn').addClass('grey-btn');
		$('#payment-form-btn').removeClass('yellow-btn');
	}

	
}





