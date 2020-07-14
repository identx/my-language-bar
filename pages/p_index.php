<?php
$_SERVER['page']['title'] = '';
$_SERVER['page']['keyws'] = 'My Language Bar';
$_SERVER['page']['descr'] = 'My Language Bar - онлайн школа иностранных языков';
$_SERVER['page']['js'] = '';


$catalog = new Catalog('mcat_');
$teachers = $catalog->getItems();

$sql = "SELECT * FROM `review`";
$res = db_query($sql);


function dropdownCoursesBlock() {
    $res = ' <div class="col-5 ml-3">
    <a href="/courses#general-course">Общий курс<br></a>
    <a href="/courses#business-course">Бизнес-курс<br></a>
    <a href="/courses#course-for-kinds">Для детей<br></a>
    <a href="/courses#traveling-course">Для путешествий<br></a>
    </div>
    <div class="col-6 p-0">
    <a href="/courses#intensive-course">Интенсивный курс<br></a>
    <a href="/courses#preparation-for-exams">Подготовка к экзаменам<br></a>
    <a href="/courses#course-with-native-speakers">С носителями языка<br></a>
    <a href="/courses#spoken-course">Разговорный язык<br></a>
    </div>';
    return $res;
}


function GetDropdownTeacher($cat, $t, $lang) {
    $catalog = new Catalog('mcat_');
    $countries = $catalog->getBrands();

    foreach ($t as $teacher) {
        $add_attrs_teacher = $catalog->getItem($teacher['id']);
        $img_teacher = $add_attrs_teacher['imgs'][0]['name'];
        $language = $add_attrs_teacher['attrs']['yazyki-prepadaet']['val'];

        $lang_arr = explode(', ', $language);

        foreach ($countries as $country) {
            if($country['id'] == $add_attrs_teacher['brand']) {
                $cntry = $country['name'];
            }
        }
        if(in_array($lang, $lang_arr)) {
         $res .= '<div class="dropdown-teacher">
         <div class="teacher-img-block">
         <img src="/images/tovars/s-'.$img_teacher.'" alt="">
         </div>
         <a href="/teacher">'.$teacher['name'].'</a>
         <p>'.$cntry.', '.$add_attrs_teacher['attrs']['gorod']['val'].'</p>
         </div>';

     } }  
     return $res;
 }







 ?>


 <section class="first-block">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                <h3 class="text-center">Изучай и практикуй иностранные языки в нашем online-баре</h3>
                <button class="btn-probnoe" data-toggle="modal" data-target="#modal-applic-form">Записаться на
                    пробное занятие
                </button>
                <p>Полноценное занятие <br> длительностью 45 минут</p>
            </div>
        </div>
    </div>
</section>

<div class="flags-img">
    <div class="container">
        <img class="img-fluid" src="/images/flags.png" alt="">
    </div>
</div>

<section class="src-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                <h3 class="d-md-none d-sm-block">Приветствуем новых друзей в нашей языковой школе!</h3>
                <img class="img-fluid" src="/images/womans.jpg">
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="src-1-text">
                    <h3 class="d-none d-md-block">Приветствуем новых друзей в нашей языковой школе!</h3>
                    <p>Давайте знакомиться! Мы, создатели «Language Bar», Александра и Виктория, всегда были влюблены в
                    иностранные языки и интересовались культурами других стран.</p>
                    <span class="more-text">Читать далее ↓</span>
                    <div class="hide-text"> <p>Наш источник вдохновения — преподавание языков и знакомства с людьми со всего мира. Основываясь
                        на
                        своём многолетнем опыте преподавания иностранных языков, путешествий и общения за границей мы
                        решили
                        создать зону свободного общения «Language Bar» где каждый сможет свободно изучать и практиковать
                    любой иностранный язык, включая русский. </p></div>
                    <p class="src-1-more " style="margin-bottom: 22px;">Больше о нас и школе можно
                    узнать здесь</p>


                    <a href="" class="btn youtube-btn">Youtube-канал Language Bar</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="src-2">
    <div class="container">
        <h3 class="text-center">Фирменный коктейль для <br> эффективного обучения</h3>
        <p class="text-center">Больше, чем просто курсы. Дружеская атмосфера, поддержка и
        постоянная практика</p>
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-5 col-12">
                <div class="advantages-item">
                    <div class="advantages-item-number">1</div>
                    <div class="advantages-item-title">С нами удобно</div>
                    <div class="advantages-item-info">Изучай реальный, “живой” язык с опытными преподавателями нашей школы. В режиме онлайн, находясь в любой точке мира</div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-12">
                <div class="advantages-item">
                    <div class="advantages-item-number">2</div>
                    <div class="advantages-item-title">Стань частью лингвосообщества</div>
                    <div class="advantages-item-info">Окружение и дружеская атмосфера — главный ингридиент в нашем баре. Здесь ты обязательно найдешь друзей-единомышленников </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-12">
                <div class="advantages-item">
                    <div class="advantages-item-number">3</div>
                    <div class="advantages-item-title">Стань частью лингвосообщества</div>
                    <div class="advantages-item-info">Мы предлагаем систему скидок, возможность оплаты от 2-х занятий и регулярно проводим розыгрыши бесплатных уроков</div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-12">
                <div class="advantages-item">
                    <div class="advantages-item-number">4</div>
                    <div class="advantages-item-title">Не только занятия</div>
                    <div class="advantages-item-info">Прямые эфиры в Instagram с носителями языков, канал на Youtube для наших учеников, общение в чатах с преподавателями и другими студентами</div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-12">
                <div class="advantages-item">
                    <div class="advantages-item-number">5</div>
                    <div class="advantages-item-title">Мы в ответе за тех, кого обучаем</div>
                    <div class="advantages-item-info">Видеть результат - лучшая мотивация. Поэтому мы периодически проводим дополнительные тесты, чтобы наглядно показать твой прогресс и следить за качеством работы преподавателей</div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-12">
             <div class="advantages-item">
                <div class="advantages-item-number">6</div>
                <div class="advantages-item-title">Учись у лучших</div>
                <div class="advantages-item-info">Наши преподаватели — участники международных стажировок, конференций. Имеют сертификаты об отличном уровне владения языком, дающие право преподавания за рубежом</div>
            </div>
        </div>

    </div>
</div>
</section>

<section class="teach-block">
    <div class="container">
        <div class=" teach-line-block row justify-content-end">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="teach-6-img">
                    <img  class="img-fluid" src="/images/board 1.png" alt="">
                </div>
            </div>

            <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                <div class="d-flex flex-direction-center">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="teach-block-cube mt-4">
                                <div class="teach-line d-flex flex-direction-center">
                                    <p>Мы преподаем русский язык в качестве иностранного</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="teach-block-cube mt-4">
                                <div class="teach-line d-flex flex-direction-center">
                                    <p>Поможем подготовиться к сдаче ЕГЭ, ОГЭ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="teach-block-cube mt-5">
                                <div class="teach-line d-flex flex-direction-center">
                                    <p>Мы преподаем русский язык в качестве иностранного</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <img class="teach-block-book d-lg-block d-none" src="/images/books-index.png" alt="">
            </div>
        </div>
    </div>
</section>

<section class="src-3">

    <div class="container">

        <h3 class="text-center">17 языков + 8 курсов по каждому на любую цель!</h3>
        <p class="text-center">Хочешь путешествовать без затруднений или непринужденно общаться с бизнес партнерами?</p>
        <p class="text-center" style="margin-bottom: 63px;">Может быть есть другая цель? Не проблема — у нас есть курсы
        под любые потребности!</p>

        


        <div class="row">

            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="dropdown">
                    <button class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
                        <img src="/images/cocktail/1.png" alt="">
                        <span>Английский</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                            <?=dropdownCoursesBlock()?>
                        </div>
                        <div>
                            <div class="dropdown-teacher-block">

                                <?=GetDropdownTeacher($catalog, $teachers, 'en'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="dropdown">
                    <button class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
                        <img src="/images/cocktail/2.png" alt="">
                        <span>Немецкий</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                         <?=dropdownCoursesBlock()?>
                     </div>
                     <div>
                        <div class="dropdown-teacher-block">

                            <?=GetDropdownTeacher($catalog, $teachers, 'de');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="dropdown">
                <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
                    <img src="/images/cocktail/3.png" alt="">
                    <span>Шведский</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                         <?=dropdownCoursesBlock()?>
                     </div>
                     <div>
                        <div class="dropdown-teacher-block">

                            <?=GetDropdownTeacher($catalog, $teachers, 'sv');?>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
        <div class="dropdown">
            <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
                <img src="/images/cocktail/4.png" alt="">
                <span>Арабский</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                 <?=dropdownCoursesBlock()?>
             </div>
             <div>
                <div class="dropdown-teacher-block">
                    <?=GetDropdownTeacher($catalog, $teachers, 'ar');?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/5.png" alt="">
            <span>Датский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
              <?=dropdownCoursesBlock()?>
          </div>
          <div>
            <div class="dropdown-teacher-block">
             <?=GetDropdownTeacher($catalog, $teachers, 'da');?>
         </div>
     </div>
 </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/6.png" alt="">
            <span>Французский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                <?=dropdownCoursesBlock()?>
            </div>
            <div>
                <div class="dropdown-teacher-block">
                    <?=GetDropdownTeacher($catalog, $teachers, 'fr');?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/7.png" alt="">
            <span>Чешский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
             <?=dropdownCoursesBlock()?>
         </div>
         <div>
            <div class="dropdown-teacher-block">
             <?=GetDropdownTeacher($catalog, $teachers, 'cs');?>
         </div>
     </div>
 </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/8.png" alt="">
            <span>Китайский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
             <?=dropdownCoursesBlock()?>
         </div>
         <div>
            <div class="dropdown-teacher-block">
                <?=GetDropdownTeacher($catalog, $teachers, 'zh');?>
            </div>
        </div>
    </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/9.png" alt="">
            <span>Испанский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                <?=dropdownCoursesBlock()?>
            </div>
            <div>
                <div class="dropdown-teacher-block">
                    <?=GetDropdownTeacher($catalog, $teachers, 'es');?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/10.png" alt="">
            <span>Финский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
             <?=dropdownCoursesBlock()?>
         </div>
         <div>
            <div class="dropdown-teacher-block">
                <?=GetDropdownTeacher($catalog, $teachers, 'fi');?>
            </div>
        </div>
    </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/11.png" alt="">
            <span>Русский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
             <?=dropdownCoursesBlock()?>
         </div>
         <div>
            <div class="dropdown-teacher-block">
                <?=GetDropdownTeacher($catalog, $teachers, 'ru');?>
            </div>
        </div>
    </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/12.png" alt="">
            <span>Корейский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                <?=dropdownCoursesBlock()?>
            </div>
            <div>
                <div class="dropdown-teacher-block">
                  <?=GetDropdownTeacher($catalog, $teachers, 'ko');?>
              </div>
          </div>
      </div>
  </div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/13.png" alt="">
            <span>Итальянский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
             <?=dropdownCoursesBlock()?>
         </div>
         <div>
            <div class="dropdown-teacher-block">
               <?=GetDropdownTeacher($catalog, $teachers, 'it');?>
           </div>
       </div>
   </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/14.png" alt="">
            <span>Португальский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                <?=dropdownCoursesBlock()?>
            </div>
            <div>
                <div class="dropdown-teacher-block">
                    <?=GetDropdownTeacher($catalog, $teachers, 'pt');?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/15.png" alt="">
            <span>Турецкий</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
             <?=dropdownCoursesBlock()?>
         </div>
         <div>
            <div class="dropdown-teacher-block">
               <?=GetDropdownTeacher($catalog, $teachers, 'tr');?>
           </div>
       </div>
   </div>
</div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/16.png" alt="">
            <span>Японский</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                <?=dropdownCoursesBlock()?>
            </div>
            <div>
                <div class="dropdown-teacher-block">
                    <?=GetDropdownTeacher($catalog, $teachers, 'ja');?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
    <div class="dropdown">
        <button type="button" class="btn dropdown-toggle" data-flip="false" data-toggle="dropdown">
            <img src="/images/cocktail/17.png" alt="">
            <span>Греческий</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="row" style="padding-top: 8px; padding-bottom: 20px;">
                <?=dropdownCoursesBlock()?>
            </div>
            <div>
                <div class="dropdown-teacher-block">
                    <?=GetDropdownTeacher($catalog, $teachers, 'el');?>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</section>

<section class="back-bar">
    <div class="container">

        <h3>Территория свободного общения</h3>
        <p>Почувствуй нашу атмосферу!</p>
        <div class="d-flex justify-content-center">
            <button class="btn-probnoe back-bar-btn" data-toggle="modal" data-target="#modal-applic-form">Записаться на пробное
                занятие
            </button>
        </div>
    </div>
</section>

<section class="src-4">
    <div class="container">
        <h3 class="text-center">Отзывы постоянных гостей нашего бара</h3>
        <div class="d-flex justify-content-center">
            <p class="text-center">Мы очень ценим обратную связь от студентов. Будем благодарны, если ты оставишь
                отзыв о своем опыте обучения в Language Bar. Больше отзывов смотри <a href="">здесь</a></p>
            </div>
        </div>

    </section>

    <section class="card-carousel-block">

        <div class="">
            <div class="card-carousel d-flex align-items-end">

                <?php foreach($res as $review) { ?>
                    <div class="card" id="<?=$review['id']?>">
                        <img style="border-radius: 50%;"src="../<?=$review['img']?>" alt="">
                        <p class="name-author"><?=$review['name']?></p>
                        <p class="country-city-author"><?=$review['anounce']?></p>
                        <p><?=$review['text']?>
                        </p>
                    </div>
                <?php   } ?>
                
                <div class="card" id="2">
                    <img src="/images/mask.png" alt="">
                    <p class="name-author">Иванова Марина</p>
                    <p class="country-city-author">Россия, Пермь</p>
                    <p>Свое первое знакомство с испанским
                        языком я начала именно тут. Хочу выразить
                        огромную благодарность школе Language Bar
                        за индивидуальный подход к каждому
                        ученику. Постоянная практика сначала
                        давалась тяжело, но меня поддерживали
                        и помогали с трудностями. Я полюбила
                        испанский и больше не боюсь общаться...
                    </p>
                </div>
                <div class="card" id="3">
                    <img src="/images/mask.png" alt="">
                    <p class="name-author">Иванова Марина</p>
                    <p class="country-city-author">Россия, Пермь</p>
                    <p>Свое первое знакомство с испанским
                        языком я начала именно тут. Хочу выразить
                        огромную благодарность школе Language Bar
                        за индивидуальный подход к каждому
                        ученику. Постоянная практика сначала
                        давалась тяжело, но меня поддерживали
                        и помогали с трудностями. Я полюбила
                        испанский и больше не боюсь общаться...
                    </p>
                </div>
                <div class="card" id="4">
                    <img src="/images/mask.png" alt="">
                    <p class="name-author">Иванова Марина</p>
                    <p class="country-city-author">Россия, Пермь</p>
                    <p>Свое первое знакомство с испанским
                        языком я начала именно тут. Хочу выразить
                        огромную благодарность школе Language Bar
                        за индивидуальный подход к каждому
                        ученику. Постоянная практика сначала
                        давалась тяжело, но меня поддерживали
                        и помогали с трудностями. Я полюбила
                        испанский и больше не боюсь общаться...
                    </p>
                </div>
                <div class="card" id="5">
                    <img src="/images/mask.png" alt="">
                    <p class="name-author">Иванова Марина</p>
                    <p class="country-city-author">Россия, Пермь</p>
                    <p>Свое первое знакомство с испанским
                        языком я начала именно тут. Хочу выразить
                        огромную благодарность школе Language Bar
                        за индивидуальный подход к каждому
                        ученику. Постоянная практика сначала
                        давалась тяжело, но меня поддерживали
                        и помогали с трудностями. Я полюбила
                        испанский и больше не боюсь общаться...
                    </p>
                </div>
            </div>
            <a href="#" class="visuallyhidden card-controller">Carousel controller</a>
        </div>
    </section>
    <section class="review-slider-480">
      <div class="container ">
        <div class="row">

            <div class="owl-theme owl-carousel review-slider">
                <div class="item">
                    <div class="card-carousel d-flex align-items-end">
                        <div class="card">
                            <img src="/images/mask.png" alt="">
                            <p class="name-author">Иванова Марина</p>
                            <p class="country-city-author">Россия, Пермь</p>
                            <p>Свое первое знакомство с испанским
                                языком я начала именно тут. Хочу выразить
                                огромную благодарность школе Language Bar
                                за индивидуальный подход к каждому
                                ученику. Постоянная практика сначала
                                давалась тяжело, но меня поддерживали
                                и помогали с трудностями. Я полюбила
                                испанский и больше не боюсь общаться...
                            </p>
                        </div>
                    </div>
                </div>
                <div class="item"><div class="card-carousel d-flex align-items-end">
                    <div class="card">
                        <img src="/images/mask.png" alt="">
                        <p class="name-author">Иванова Марина</p>
                        <p class="country-city-author">Россия, Пермь</p>
                        <p>Свое первое знакомство с испанским
                            языком я начала именно тут. Хочу выразить
                            огромную благодарность школе Language Bar
                            за индивидуальный подход к каждому
                            ученику. Постоянная практика сначала
                            давалась тяжело, но меня поддерживали
                            и помогали с трудностями. Я полюбила
                            испанский и больше не боюсь общаться...
                        </p>
                    </div>
                </div></div>
                <div class="item"><div class="card-carousel d-flex align-items-end">
                    <div class="card">
                        <img src="/images/mask.png" alt="">
                        <p class="name-author">Иванова Марина</p>
                        <p class="country-city-author">Россия, Пермь</p>
                        <p>Свое первое знакомство с испанским
                            языком я начала именно тут. Хочу выразить
                            огромную благодарность школе Language Bar
                            за индивидуальный подход к каждому
                            ученику. Постоянная практика сначала
                            давалась тяжело, но меня поддерживали
                            и помогали с трудностями. Я полюбила
                            испанский и больше не боюсь общаться...
                        </p>
                    </div>
                </div></div>
            </div>
        </div>
    </div>
</section>
