<?php
$_SERVER['page']['title'] = 'Преподаватели | ';
$_SERVER['page']['keyws'] = 'My Language Bar';
$_SERVER['page']['descr'] = 'Преподаватели';
$_SERVER['page']['js'] = '';

db_connect();

$catalog = new Catalog('mcat_');
$cats=$catalog->getItems();

//echo "<pre>";
//var_dump($cats);
//echo "</pre>";
db_disconnect();

?>

<!-- Modal -->


<section class="header_teachers">
    <h1>Наши преподаватели</h1>
</section>

<section class="main_teachers" style="margin-bottom: 125px;">
    <div class="container">
        <div class="d-flex justify-content-center" style="margin: 60px 0">
            <div class="main_teachers_sort">
                <a href="#" data-value="all-lang">Все языки</a><br>
                <a href="" class="language-link-filter" data-value="en">Английский</a><br>
                <a href="" class="language-link-filter" data-value="da">Датский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="" class="language-link-filter" data-value="es">Испанский</a><br>
                <a href="" class="language-link-filter" data-value="it">Итальянский</a><br>
                <a href="" class="language-link-filter" data-value="el">Греческий</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="" class="language-link-filter" data-value="de">Немецкий</a><br>
                <a href="" class="language-link-filter" data-value="fr">Французский</a><br>
                <a href="" class="language-link-filter" data-value="fi">Финский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="" class="language-link-filter" data-value="ru">Русский</a><br>
                <a href="" class="language-link-filter" data-value="pt">Португальский</a><br>
                <a href="" class="language-link-filter" data-value="sv">Шведский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="" class="language-link-filter" data-value="cs">Чешский</a><br>
                <a href="" class="language-link-filter" data-value="tr">Турецкий</a><br>
                <a href="" class="language-link-filter" data-value="ar">Арабский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="" class="language-link-filter" data-value="zh">Китайский</a><br>
                <a href="" class="language-link-filter" data-value="ko">Корейский</a><br>
                <a href="" class="language-link-filter" data-value="ja">Японский</a><br>
            </div>
        </div>

        <div class="row">
            <?php
                foreach($cats as $value) {
                    $tovs=$catalog->getItem($value['id']);
                    echo CardTeacher($tovs);
                }
            ?>
        </div>
    </div>
</section>
