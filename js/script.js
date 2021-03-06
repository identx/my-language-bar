$(function(){


	$('a[data-value="all-lang"]').on('click', function(e){
		e.preventDefault();
		$('.teacher-wrapper').show();
	})
	filterLanguage($('.language-link-filter'));

	$('#payment-form-btn').on('click', function(e){
		e.preventDefault();
		let data = {};
		data.course = $('.payment-courses-select option:selected').val();
		data.language = $('.payment-languages-select option:selected').text();
		data.user = $('.alert strong').data('id');
		data.package = $('.payment-package-select option:selected').text().split(' ур')[0];
		data.sum = $('.payment_sum').val();
		
		let desc = $('.payment-courses-select option:selected').text()+' '+$('.payment-package-select option:selected').text().split(' -')[0];
		$('.payment_desc').val(desc);
		data.desc = $('.payment_desc').val();
		let id = $('.payment_account').val(data.user);
		data.account = $('.payment_account').val();
		data.currency = $('.payment_currency').val();
		$.post('../handlers/add-transaction.php', data, function(r){
			
			$('.payment_signature').val(r['sign']);
			$('.payment_account').val(r['id']);
			

			$('#payment-form').submit();
		},"json");

		
	})

	$(document).on('change', '.payment-package-select', function(){
		getAmount();
	})

	$(document).on('change', '.payment-courses-select', function(){
		let select = $('.payment-package-select');
		select.attr('disabled', false);
		data = {};
		data.id_course = $(this).val();

		$.post('../handlers/get-package.php', data, function(r){
			select.html(r);
			getAmount();
			
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
    console.log("dada");
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
	if($('.alert').hasClass('alert-success') && $('.payment-courses-select option:selected').val() != 0) {
		$('#payment-form-btn').removeAttr('disabled');
		$('#payment-form-btn').removeClass('grey-btn');
		$('#payment-form-btn').addClass('yellow-btn');
	}else {
		$('#payment-form-btn').attr('disabled', 'disabled');
		$('#payment-form-btn').addClass('grey-btn');
		$('#payment-form-btn').removeClass('yellow-btn');
	}	
}

function getAmount() {	
	$('.payment_sum').val($('.payment-package-select option:selected').val());
}





function filterLanguage(selector) {
	$(selector).on('click', function(e){
		// alert($(this).data('value'));
		e.preventDefault();
		$('.teacher-wrapper').hide();
		let block = '.'+$(this).data('value')+'-lang';
		$(block).show();
	})
}