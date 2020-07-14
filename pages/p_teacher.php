<?php
$_SERVER['page']['title'] = 'Преподаватель - ';
$_SERVER['page']['keyws'] = 'My Language Bar';
$_SERVER['page']['descr'] = 'Преподаватель';
$_SERVER['page']['js'] = '';

db_connect();

$catalog = new Catalog('mcat_');
$tovs=$catalog->getItem($_GET["id"]);;

$brands = $catalog->getBrands();
foreach ($brands as $brand) {

    if ($brand["id"] === $tovs["brand"])
        $tovs["country"] = $brand["name"];
}



db_disconnect();
?>

<!-- Modal -->


<section class="header_teachers">
    <h1>Преподаватель</h1>
</section>

<?php


echo '
<section class="teacher-main">
    <div class="container" style="margin-top: 70px;">
        <div class="row">
            <div class="col-2">
                <a href="teachers">← Назад</a>
            </div>
            <div class="col-2">
                <img src="/images/tovars/b-'.$tovs["imgs"][0]["name"].'" alt="" style="width: 100%;">
                <img src="/images/youtube 1.png" alt="" width="64" class="video-btn" data-toggle="modal"
                data-src="'.$tovs["alias"].'" data-target="#teachers-modal">
            </div>
            <div class="col-4">
                <div class="teachers-text-block">
                    <h1> '.$tovs["name"] .'<img class="teachers-flag" src="/images/icon-flag/1.png" alt=""></h1>
                    <p>Преподает: '.returnLanguage($tovs["attrs"]["yazyki-prepadaet"]["val"], true).'</p>
                    <p>Говорит на: '.returnLanguage($tovs["attrs"]["yazyki-govorit"]["val"], false).'</p>  
					<p>'.$tovs["country"].', '.$tovs['attrs']['gorod']["val"].' (GMT +'.$tovs["count"].' часа)</p>
                    <p>17:00 (разница 5 часов)</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" style="margin-top: 29px">
            <div class="col-8 pr-0">
                <div class="teacher-desc">
                    <p>'. str_replace('*', '', $tovs["attrs"]["opisanie"]["val"])  .'</p>
                </div>
            </div>
        </div>
        <h3 class="text-center mt-5 mb-4">Увидимся на курсах:</h3>
        <div class="row justify-content-center offer-links-block" style="margin-bottom: 142px;">'.returnLanguage($tovs["attrs"]["yazyki-prepadaet"]["val"], "teacherCurse").'
        </div>
    </div>
</section>';
?>

<section class="footer_teacher">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="teacher-form-desc">
                    <h3>Подберем курс под твои цели</h3>
                    <p>Будем рады видеть нового<br> гостя в нашем баре!</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 teacher-form">
                <h4>Заполните форму заявки</h4>
                <p>оставьте свои данные и мы свяжемся<br>
                    с вами в ближайшее время</p>
                <form action="">
                    <input type="text" placeholder="Имя">
                    <input type="text" placeholder="Телефон">
                    <select name="" id="">
                        <option value="">Язык для изучения</option>
                    </select>
                    <select name="" id="">
                        <option value="">Курс</option>
                    </select>
                    <textarea name="" id="">Цели изучения языка</textarea>
                    <button>Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
</section>