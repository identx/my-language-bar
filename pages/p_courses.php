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

function compare_lastname($a, $b)
{
    return strnatcmp($a['name'], $b['name']);
}

function drawCurses($curs, $category) {

    
    usort($curs, 'compare_lastname');


    // echo "<pre>";
    // var_dump($curs);
    // echo "</pre>";
    $q = '<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 course-wrapper">
                <div class="courses-item" id="general-course">
                    <p class="text-number">'.$category["id"].'</p>
                    <img src="/images/pack/b-'.$curs[0]["imgs"][0]["name"].'" class="position-absolute"
                    style="margin-left: 40px; margin-top: 10px;" alt="">
                    <div class="courses-right-side">
                        <h4>'.$category["name"].'</h4>
                        <p class="courses-right-side-t">'.$category["scomment"].'</p>
                        <div class="courses-a d-flex">';

                            for ($i = 0; $i <= 3; $i++) {
                                if (is_null($curs[$i])) {
                                   $q.='<div class="courses-a-i" style="display: none;">';
                                   $q.='</div>';
                                } else {
                                    $q.='<div class="courses-a-i">';
                                    if ($curs[$i]["name"] == "1") {
                                        $q.='<p>'.$curs[$i]["name"].' урок</p>';
                                    } else if ($curs[$i]["name"] >= "5") {
                                        $q.='<p>'.$curs[$i]["name"].' уроков</p>';
                                    } else {
                                        $q.='<p>'.$curs[$i]["name"].' урока</p>';
                                    }
                                    $priceSale = round(($curs[$i]["price"]-$curs[$i]["price_d"])*1000/$curs[$i]["price"], 0)/10;
                                    $priceSaleYrok = round(($curs[$i]["price"]/$curs[$i]["name"])-$priceSale*($curs[$i]["price"]/$curs[$i]["name"])/100);
                                    if ($curs[$i]["price"] === $curs[$i]["price_d"]) {
                                        $q.='<p>'.$curs[$i]["price"].'<span class="symbol">₽</span></p>';
                                    } else {
                                        $q.='<p><span class="delete-price"><span class="delete-line"></span>'.$curs[$i]["price"].'<span class="symbol">₽</span></span><span>'.$curs[$i]["price_d"].'<span class="symbol">₽</span></span></p>
                                            <div class="courses-a-i-sale">-'.$priceSale.'%</div>';
                                    }
                                    
                                    
                                    $q.='<p>('.$priceSaleYrok.' <span class="symbol">₽</span>/урок)</p>';
                                    $q.='</div>';
                                }
                            }

                        $q.='<button data-toggle="modal" data-target="#modal-applic-form">Записаться</button>
                        </div>
                    </div>
                </div>
            </div>';
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
                foreach($categories as $category) {

                    $curs;
                    $idCursesTvr = 1;
                    foreach($tvrs as $tvr) {
                        if ($tvr["cat"] == $category["id"]) {
                            $curs[$idCursesTvr] = $catalog->getItem($tvr['id']);
                            $idCursesTvr++;
                        }

                    }
                    echo drawCurses($curs, $category);
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
