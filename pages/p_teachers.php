<?php
$_SERVER['page']['title'] = 'Преподаватели | ';
$_SERVER['page']['keyws'] = 'My Language Bar';
$_SERVER['page']['descr'] = 'Преподаватели';
$_SERVER['page']['js'] = '';

db_connect();

$catalog = new Catalog('mcat_');

$catalog->setItemsSort(array( 'price_d'=>1, 'count'=>0, 'id'=>0));

$cats=$catalog->getItems();

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
                <a href="#">Все языки</a><br>
                <a href="">Английский</a><br>
                <a href="">Датский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="">Испанский</a><br>
                <a href="">Итальянский</a><br>
                <a href="">Греческий</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="">Немецкий</a><br>
                <a href="">Французский</a><br>
                <a href="">Финский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="">Русский</a><br>
                <a href="">Португальский</a><br>
                <a href="">Шведский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="">Чешский</a><br>
                <a href="">Турецкий</a><br>
                <a href="">Арабский</a><br>
            </div>
            <div class="main_teachers_sort">
                <a href="">Китайский</a><br>
                <a href="">Корейский</a><br>
                <a href="">Японский</a><br>
            </div>
        </div>

        <div class="row justify-content-center">
            <?php
            foreach($cats as $value) {
                $tovs=$catalog->getItem($value['id']);
                echo CardTeacher($tovs);
            }
            ?>
        </div>
    </div>
</section>
