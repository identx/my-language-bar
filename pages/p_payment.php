<?php
$_SERVER['page']['title'] = 'Оплата | ';
$_SERVER['page']['keyws'] = 'My Language Bar';
$_SERVER['page']['descr'] = 'My Language Bar - онлайн школа иностранных языков';
$_SERVER['page']['js'] = '';

$catalog = new Catalog('mcur_');
$courses = $catalog->getCats($root=0);

?>

<section class="header_payment">
	<h1>Оплата</h1>
</section>
<section class="payment-block">
	<div class="container">
		<h3 class="text-center"> Уважаемые друзья нашей школы! </h3>
		<p class="text-center">Просим вас производить оплату заблаговременно</p>
		<div class="row">
			<div class="col-xl-6 col-lg-6 col-12">
				<div class="payment-page-item">
					<div class="payment-page-item-number">1</div>

					<div class="payment-page-item-info">Наши преподаватели не имеют права проводить неоплаченные 
						занятия, соответственно, если за сутки до назначенного занятия 
					оплата не была произведена - занятие будет отменено</div>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-12">
				<div class="payment-page-item">
					<div class="payment-page-item-number">2</div>

					<div class="payment-page-item-info">Если ученик не является на урок без предупреждения, стоимость занятия будет списана как если бы занятие было проведено.
					</div>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-12">
				<div class="payment-page-item payment-page-item-large">
					<div class="payment-page-item-number">3</div>

					<div class="payment-page-item-info">Если Вы знаете, что не сможете присутствовать на назначенном занятии, просим вас проинформировать нас об отмене за сутки, 
					чтобы наши преподаватели не теряли времени впустую и могли заполнить освободившееся окно или просто отдохнуть, учитывая отмену заблаговременно. </div>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-12">
				<div class="payment-page-item payment-page-item-large">
					<div class="payment-page-item-number">4</div>

					<div class="payment-page-item-info">Наши преподаватели не имеют права проводить неоплаченные 
						занятия, соответственно, если за сутки до назначенного занятия 
					оплата не была произведена - занятие будет отменено</div>
				</div>
			</div>
			<div class="col-xl-12">
				<p class="text-center">Спасибо за понимание! Ждём вас в «Language Bar»!</p>
			</div>
		</div>
	</div>
</div>
</section>
<section class="payment-form">
	<div class="container">
		<div class="row">
			<div class="col-xl-12">
				<div class="payment-form-block">
					<p class="text-center payment-form-title">
						Укажите курс и количество часов
					</p>
					<p class="payment-form-text"><img class="course-icon" src="/images/icons/clock-3.svg" alt="">Урок в любом курсе длится 60 минут</p>
					<p class="payment-form-text"><img class="course-icon" src="/images/icons/coin.svg" alt="">Оплата принимается от 2-х уроков</p>
					<!-- <form action="http://languagebar.ru/libs/php-sdk-master/initPaymentApi.php" method="get" target="_blank" id="payment-form"> -->
						<form action="https://unitpay.money/pay/299021-05457?sum&account&desc&signature" method="get" target="_blank" id="payment-form">
						<div class="check-info"></div>
						<input type="text" id="payment-form-code" placeholder="Код учащегося">
						<button id="check-code" class="check-code" disabled="disabled">Проверить</button>
						<select name="" id="" class="payment-languages-select">
							<option value="">Язык</option>
							<option>Английский</option>
							<option>Немецкий</option>
							<option>Шведский</option>
							<option>Арабский</option>
							<option>Датский</option>
							<option>Французский</option>
							<option>Чешский</option>
							<option>Китайский</option>
							<option>Испанский</option>
							<option>Финский</option>
							<option>Русский</option>
							<option>Корейский</option>
							<option>Итальянский</option>
							<option>Португальский</option>
							<option>Турецкий</option>
							<option>Японский</option>
							<option>Греческий</option>
						</select>
						<select name="" id="" class="payment-courses-select">
							<option value="" disabled="disabled" selected="selected">Курс</option>
							<?php foreach($courses as $course) { ?>
								<option value="<?=$course['id']?>"><?=$course['name']?></option>
							<?php } ?>
						</select>
						<select name="" id="" class="payment-package-select" disabled="disabled">
							<option value="" >Пакет</option>
						</select>
						<input type="hidden" name="sum" class="payment_sum" value="0">
						<input type="hidden" name="account" class="payment_account" value="none">
						<input type="hidden" name="desc" class="payment_desc" value="Оплата курса">		
						<input type="hidden" name="signature" class="payment_signature" value="0">
						<button type="submit"  id="payment-form-btn" class="main-btn grey-btn" disabled="disabled">Перейти на страницу оплаты</button>

					</form>

				</div>
			</div>
		</div>
	</div>
</section>

