<?php
$_SERVER['page']['title'] = 'Курсы | ';
$_SERVER['page']['keyws'] = 'My Language Bar';
$_SERVER['page']['descr'] = 'My Language Bar - онлайн школа иностранных языков';
$_SERVER['page']['js'] = '';

db_connect();

$catalog = new Catalog('mcur_');
$tvrs=$catalog->showHiddens(true);
$tvrs=$catalog->getItems();

$categories = $catalog->getCats();



$idCurses = 0;

function drawCurses($tovs, $idCurses, $category) {

    $q = '<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 course-wrapper">
                <div class="courses-item" id="general-course">
                    <p class="text-number">'.$idCurses.'</p>
                    <img src="/images/pack/b-'.$tovs[0]["imgs"]["name"].'" class="position-absolute"
                    style="margin-left: 40px; margin-top: 10px;" alt="">
                    <div class="courses-right-side">
                        <h4>'.$category["name"].'</h4>
                        <p class="courses-right-side-t">'.$category["scomment"].'</p>
                        <div class="courses-a d-flex">
                            <div class="courses-a-i">
                                <p>2 урока</p>
                                <p>1 600 <span class="symbol">₽</span></p>
                                <p>(800 <span class="symbol">₽</span>/урок)</p>
                            </div>
                            <div class="courses-a-i">
                                <p>4 урока</p>
                                <p>3 200 <span class="symbol">₽</span></p>
                                <p>(800 <span class="symbol">₽</span>/урок)</p>
                            </div>
                            <div class="courses-a-i">
                                <p>8 уроков</p>
                                <p><span class="delete-price"><span class="delete-line"></span>6 400 <span class="symbol">₽</span></span><span>6 160 <span class="symbol">₽</span></span></p>
                                <p>(770 <span class="symbol">₽</span>/урок)</p>
                                <div class="courses-a-i-sale">-3,7%</div>
                            </div>
                            <div class="courses-a-i">
                                <p>16 уроков</p>
                                <p><span class="delete-price"><span class="delete-line"></span><span class="delete-line"></span>12 840 <span class="symbol">₽</span></span><span>11 840 <span class="symbol">₽</span></span></p>
                                <p>(770 <span class="symbol">₽</span>/урок)</p>
                                <div class="courses-a-i-sale">-7,5%</div>
                            </div>
                            <button data-toggle="modal" data-target="#modal-applic-form">Записаться</button>
                        </div>

                    </div>
                </div>
            </div>';
//    echo "<pre>";
//    var_dump($tovs);
//    echo "</pre>";
    return $q;
}

db_disconnect();
?>

<section class="header_courses">
    <h1>наши курсы</h1>
</section>

<section class="courses">
    <div class="container">
        <h3 class="text-center">8 курсов по каждому из 17 языков</h3>
        <p><img class="course-icon" src="/images/icons/clock-3.svg" alt="">Урок в любом курсе длится 60 минут</p>
        <p class="mb-5"><img class="course-icon" src="/images/icons/coin.svg" alt="">Оплата принимается от 2-х уроков</p>
        <div class="row justify-content-center courses-block">
            <?php
                $idCurses = 0;
                foreach($categories as $category) {

                    $curs;
                    $idCursesTvr = 1;
                    foreach($tvrs as $tvr) {
                        if ($tvr["cat"] == $category["id"]) {
                            $curs[$idCurses] = $catalog->getItem($tvr['id']);
                            $idCursesTvr++;
                        }
                    }
                    echo drawCurses($curs, $idCurses, $category);
                    $idCurses++;
                }

            ?>
        </div>
    </div>
</section>

<section class="footer_teacher">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="teacher-form-desc">
                    <h3>Подберем курс<br>под твои цели</h3>
                    <p>Будем рады видеть нового<br> гостя в нашем баре!</p>
                    <div class="arrow-to-form"><img src="/images/arrow-to-form.svg" alt=""></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 teacher-form">
                <h4>Заполните форму заявки</h4>
                <p>оставьте свои данные и мы свяжемся<br>
                с вами в ближайшее время</p>
                <form action="" id="courses-page-form">
                    <input type="text" placeholder="Имя">
                    <input type="tel" placeholder="Телефон">
                    <select>
                        <option disabled selected="selected">Язык для изучения</option>
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
                    <select class="courses-select">
                        <option value="" disabled>Курс</option>
                        <option value="0">GENERAL COURSE (Общий курс)</option>
                        <option value="1">SPOKEN COURSE (Курс разговорного языка)</option>
                        <option value="2">PREPARATION FOR EXAMS (Курс подготовки к экзаменам)</option>
                        <option value="3">TRAVELING COURSE (Курс для путешествий)</option>
                        <option value="4">INTENSIVE COURSE (Интенсивный курс)</option>
                        <option value="5">BUSINESS COURSE (Бизнес-курс)</option>
                        <option value="6">COURSE FOR KIDS (Курс для детей от 5 лет, игровая методика)</option>
                        <option value="7">COURSE WITH NATIVE SPEAKERS (Курс с носителями языка)</option>
                    </select>
                    <textarea name="" id="" placeholder="Цели изучения языка"></textarea>
                    <button>Отправить заявку</button>
                    <p class="privacy-info">Оставляя свои контактные данные, вы соглашаетесь на обработку персональных данных в соответствии с <a href="/privacy" style="color: #f5e457">Политикой конфиденциальности</a></p>
                </form>
            </div>
        </div>
    </div>
</section>
