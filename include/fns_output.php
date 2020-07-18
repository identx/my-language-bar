<?php /*?>*/

function output_header()
{
    global $opts;
    global $discount;
    $v = 17;
    $r = '<!DOCTYPE html>
    <!--[if IE 8]> <html lang="ru" class="ie8 no-js"> <![endif]-->
    <!--[if IE 9]> <html lang="ru" class="ie9 no-js"> <![endif]-->
    <!--[if !IE]><!-->
    <html lang="ru"><!--<![endif]--><!-- Begin Head --><head>
    <base href="/ru/">
    <meta name="theme-color" content="#3D1724">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" type="image/png" href="../favicon.png">
    <title>' . $_SERVER['page']['title'] . 'My Language Bar - онлайн школа иностранных языков</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="keywords" content="' . $_SERVER['page']['keyws'] . ', Text Text Text Text Text" />
    <meta name="description" content="' . $_SERVER['page']['descr'] . ',My Language Bar - онлайн школа иностранных языков." />	
    <meta name="author" content="identx">
    <meta name="MobileOptimized" content="320">
    <!--Start Style -->
    
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <link rel="stylesheet" href="../css/fonts.css?v='.$v.'">
    <link rel="stylesheet" href="../css/style.css?v='.$v.'">
    <link rel="stylesheet" href="../css/media.css?v='.$v.'">

    </head>
    <body>
    <div class="modal fade" id="modal-applic-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header" style="border: none;">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body p-1">
    <h4 class="text-center">Заполните форму заявки</h4>
    <p class="text-center">оставьте свои данные и мы свяжемся с вами в ближайшее время</p>
    <form>
    <div class="row justify-content-center main-form-block">

    <div class="col-lg-4 col-md-4 col-sm-10 col-xs-4 p-1">
    <input type="text" placeholder="Имя">
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 p-1">
    <input type="text" placeholder="Фамилия">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 p-1">
    <input type="text" placeholder="Возраст">
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 p-1">
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
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 p-1">
    <select>
    <option disabled selected="selected">Уровень владения языком</option>
    <option>Beginner</option>
    <option>Elementary</option>
    <option>Pre-Intermediate</option>
    <option>Intermediate</option>
    <option>Upper-Intermediate</option>
    </select>
    </div>';
    if($_SERVER['REQUEST_URI'] == '/courses') {
        $r.='        <div class="col-lg-10 col-md-10 col-sm-10 p-1">
        <select>
        <option value="0">GENERAL COURSE (Общий курс)</option>
        <option value="1">SPOKEN COURSE (Курс разговорного языка)</option>
        <option value="2">PREPARATION FOR EXAMS (Курс подготовки к экзаменам)</option>
        <option value="3">TRAVELING COURSE (Курс для путешествий)</option>
        <option value="4">INTENSIVE COURSE (Интенсивный курс)</option>
        <option value="5">BUSINESS COURSE (Бизнес-курс)</option>
        <option value="6">COURSE FOR KIDS (Курс для детей от 5 лет, игровая методика)</option>
        <option value="7">COURSE WITH NATIVE SPEAKERS (Курс с носителями языка)</option>
        </select>
        </div>';
    }
    $r.='<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 p-1">
    <textarea placeholder="Цели изучения языка"></textarea>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 p-1">
    <input type="tel" placeholder="Телефон">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 p-1">
    <input type="text" placeholder="Ник в Skype">
    </div>
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 p-1">
    <input type="email" placeholder="E-mail">
    </div>
    </div>
    <div class="d-flex justify-content-center">
    <button>Отправить</button>
    </div>  
    <div class="d-flex justify-content-center">
    <p class="privacy-info">Оставляя свои контактные данные, вы соглашаетесь на обработку персональных данных в соответствии с <a href="/privacy">Политикой конфиденциальности</a></p>
    </div>
    </form>
    </div>
    <div class="modal-footer" style="border: none;">
    </div>
    </div>
    </div>
    </div>

    <!-- Modal -->
    <div class="modal fade teachers-modal-video" id="teachers-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    <div class="embed-responsive embed-responsive-16by9">
    <iframe class="embed-responsive-item" src="" id="video" allowscriptaccess="always"
    allow="autoplay"></iframe>
    </div> 
    </div>   
    </div>
    </div>
    </div>

    <header>
    <nav class="navbar navbar-expand-lg navbar-light container">
    <a class="navbar-brand" href="/"><img src="../images/header-logo.svg" alt=""></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">

        <li class="nav-item">
            <a class="nav-link" href="teachers">Преподаватели</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="courses">Курсы и стоимость</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="payment">Оплата</a></li>
        <li class="nav-item">
            <a class="nav-link" href="contacts">Контакты</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="vacancies">Вакансии</a>
        </li>
    </ul>

    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <div class="nav-link btn" id="google_translate_element"></div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="skype:languagebar?call"><img src="../images/icons/skype.png" alt="">Language Bar</a>
        </li>

    </ul>
    </div>
    </nav>
    </header>
    <section class="content">';
    return $r;
}

function output_footer()
{
    $r = '<footer>
    <div class="flags-img">
    <div class="container">
    <img class="img-fluid" src="/images//flags.png" alt="">
    </div>
    </div>
    <div class="container top-footer">
    <div class="row justify-content-between">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
    <a href="/" class="footer-logo"> <img src="../images/footer-logo.svg" class="img-fluid" alt="" ></a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
    <ul class="footer-links">
    <li><a href="/teachers">Преподаватели</a></li>
    <li><a href="">Отзывы</a></li>
    <li><a href="/courses">Курсы и стоимость</a></li>
    <li><a href="/payment">Оплата</a></li>
    </ul>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 contact">
    <p class="footer-links">Контакты</p>
    <div class="row justify-content-between footer-contacts">
    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-6">
    <ul>

    <li><img src="/images//icons/mail-wh.png" alt=""><a href="mailto:mylanguagebar@gmail.com">mylanguagebar@gmail.com</a></li>
    <li><img src="/images//icons/skype-wh.png" alt=""><a href="skype:languagebar?call">Language Bar</a></li>
    </ul>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6">
    <ul>
    <li><img src="/images//icons/instagram-wh.png" alt="">
    <a href="" rel="nofollow" target="_blank">instagram_profile</a></li>
    <li><img src="/images//icons/facebook-wh.png" alt=""><a href="" rel="nofollow" target="_blank">facebook_profile</a></li>
    <li><img src="/images//icons/youtube-wh.png" alt=""><a href="" rel="nofollow" target="_blank">youtube-channel</a></li>
    </ul>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="d-flex justify-content-center footer-identx">
    <div id="identx" style="width: 240px; padding-top: 5px; clear: both; margin: auto;"><style>
    a.hv:hover{text-decoration:underline}
    a.hv{text-decoration:none}<br />
    p:first-letter{color:#43CB83;}<br />
    </style><span style="line-height: 1.6; font-size: 11px; font-family: Arial Narrow, sans-serif; width: 106px; display: block; float: left; text-align: right; margin-right: 5px;">
    <a class="hv" style="color: #fff; font-size: 11px;" title="identx.ru" href="//identx.ru/?utm_source=copyright&amp;utm_medium=languagebar" target="_blank" rel="noopener">создание сайтов</a>
    <a class="hv" style="color: #fff; font-size: 11px;" title="identx.ru" href="//identx.ru/реклама-яндекс-google/?utm_source=copyright&amp;utm_medium=languagebar" target="_blank" rel="noopener">реклама в интернете</a>
    </span>
    <a class="hv" style="font-family: Arial Narrow, sans-serif; font-size: 16px; color: #fff;" title="identx.ru" href="//identx.ru/?utm_source=copyright&amp;utm_medium=languagebar" target="_blank" rel="noopener">
    <img style="float: left;" src="../images/logo-i.svg" alt="айдентика">
    <p style="float: left; margin-top: 7px; margin-left: 5 px !important;"><span style="color:#43CB83; margin-left: 5px;">A</span>йдентика</p></a>

    </div>
    </div>
    </footer>


    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.maskedinput.min.js"></script>

    
    <script type="text/javascript">
			function googleTranslateElementInit() {
			new google.translate.TranslateElement({pageLanguage: \'en\', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, \'google_translate_element\');
			}
	</script>
	<script type="text/javascript" src="http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>


    <script src="../js/script.js?v='.$v.'""></script>';

    if ($_SERVER['REQUEST_URI'] == '/') {
        $r .= '<script src="../js/carousel.js"></script>';
    } else if ($_SERVER['REQUEST_URI'] == '/teachers') {
        $r .= '<script src="../js/teachers-video.js"></script>';
    }


    $r .= '
    </body>
    </html>';

    return $r;
}

function returnLanguage($langs, $isLink) {
    $q = "";
    $langs = explode(", ", $langs);

    $numItems = count($langs);
    $i = 0;

    foreach ($langs as $lang) {
        $i++;

        $language;
        $languageimg;
        switch ($lang) {
            case "en":
            $language = "Английский";
            $languageimg = "1.png";
            break;
            case "de":
            $language = "Немецкий";
            $languageimg = "2.png";
            break;
            case "sv":
            $language = "Шведский";
            $languageimg = "3.png";
            break;
            case "ar":
            $language = "Арабский";
            $languageimg = "4.png";
            break;
            case "da":
            $language = "Датский";
            $languageimg = "5.png";
            break;
            case "fr":
            $language = "Французский";
            $languageimg = "6.png";
            break;
            case "cs":
            $language = "Чешский";
            $languageimg = "7.png";
            break;
            case "zh":
            $language = "Китайский";
            $languageimg = "8.png";
            break;
            case "es":
            $language = "Испанский";
            $languageimg = "9.png";
            break;
            case "fi":
            $language = "Финский";
            $languageimg = "10.png";
            break;
            case "ru":
            $language = "Русский";
            $languageimg = "11.png";
            break;
            case "ko":
            $language = "Корейский";
            $languageimg = "12.png";
            break;
            case "it":
            $language = "Итальянский";
            $languageimg = "13.png";
            break;
            case "pt":
            $language = "Португальский";
            $languageimg = "14.png";
            break;
            case "tr":
            $language = "Турецкий";
            $languageimg = "15.png";
            break;
            case "ja":
            $language = "Японский";
            $languageimg = "16.png";
            break;
            case "el":
            $language = "Греческий";
            $languageimg = "17.png";
            break;
        }



        if($isLink === "teacherCurse") {
            $q .= '<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
            <p class="teacher-cur"><img src="../images/cocktail/'.$languageimg.'" alt="">'.$language.'</p>
            <div class="d-flex justify-content-around">
            <div>
            <a href="">Общий курс</a><br>
            <a href="">Бизнес-курс</a><br>
            <a href="">Для детей</a><br>
            <a href="">Для путешествий</a><br>
            </div>
            <div>
            <a href="">Интенсивный курс</a><br>
            <a href="">Подготовка к экзаменам</a><br>
            <a href="">С носителями языка</a><br>
            <a href="">Разговорный язык</a><br>
            </div>
            </div>
            </div>';
        } else {
            if($isLink === true) {
               
                     $q .= '<a href="" class="language-link language-link-filter" data-value="'.$lang.'">'.$language.'</a>';

            } else {
                $q .= '<span class="lang-info">'.$language.'</span>';
            }
        }


        if(++$i === $numItems && $isLink !== "teacherCurse") {
            $q .= ", ";
        }
    }
    return $q;
}


function CardTeacher($tovs) {

    $catalog = new Catalog('mcat_');
    $brands = $catalog->getBrands();
    foreach ($brands as $brand) {
        if ($brand["id"] === $tovs["brand"])
            $tovs["country"] = $brand;
    }
    $new_lang_arr = [];
    foreach (explode(', ', $tovs["attrs"]["yazyki-prepadaet"]["val"]) as $lang_val) {
        $new_lang_arr[] = $lang_val.'-lang';
    }

    $q = '<div class="col-lg-6 col-md-10 col-sm-12 col-xs-12 p-1 teacher-wrapper '.implode(' ', $new_lang_arr).'">
    <div class="teachers-item d-flex">
    <div class="teachers-img">
    <a href="teacher?id='.$tovs["id"].'"><img src="../images/tovars/b-'.$tovs["imgs"][0]["name"].'" alt=""></a>
    <img src="../images/youtube 1.png" alt="" class="video-btn" data-toggle="modal"
<<<<<<< HEAD
    data-src="'.str_replace('https://www.youtube.com/watch?v=','https://www.youtube.com/embed/',$tovs["alias"]).'" data-target="#teachers-modal">
=======
    data-src="'.str_replace('https://www.youtube.com/watch?v=','https://www.youtube.com/embed/',$tovs["alias"]).'?autoplay=1" data-target="#teachers-modal">
>>>>>>> master
    </div>
    <div class="teachers-text-block">
    <span class="teacher-name"><a href="teacher?id='.$tovs["id"].'">'. $tovs["name"] .'</a> <img class="teachers-flag" src="../images/brands/'.$tovs["country"]["img"].'" alt=""></span>
    <p>Преподает: '.returnLanguage($tovs["attrs"]["yazyki-prepadaet"]["val"], true).'</p>
    <p>Говорит на: '.returnLanguage($tovs["attrs"]["yazyki-govorit"]["val"], false).'</p>
    <p class="teachers-decs d-none d-sm-block">'.  stristr($tovs["attrs"]["opisanie"]["val"], '*', true) .' <a href="teacher?id='.$tovs["id"].'" class="more-teacher">Подробнее →</a></p>
    </div>
    </div>
    </div>';

    return $q;
}


function makePager($iCurr, $iEnd, $link)
{
    $iLeft = 2;
    $iRight = 2;
    $res = '';
    $get = '';
    // $get=strlen($_SERVER['QUERY_STRING'])>0?'?'.urldecode($_SERVER['QUERY_STRING']):'';
    if ($iCurr != 1) {
        $res .= '<a href="/' . $link . ($iCurr - 1 > 1 ? '?page=' . ($iCurr - 1) : '') . $get . '"><span class="pagi">&lt;</span></a>';
    }
    if ($iCurr - $iLeft > 1) $res .= '<a class="pagi" href="' . $link . '/' . $get . '">1</a>';
    if ($iCurr - $iLeft > 2) $res .= '&hellip;';
    if ($iCurr <= $iLeft) {
        for ($i = 1; $i <= $iCurr + $iRight; $i++) {
            if ($i > $iEnd)
                break;
            $res .= $i == $iCurr ? '<span class="pagi_curr">' . $i . '</span>' : '<a class="pagi"  href="/' . $link . ($i == 1 ? $get : '?page=' . $i . $get . '') . '">' . $i . '</a>';
        }
    } else {
        for ($i = $iCurr - $iLeft; $i <= min($iCurr + $iRight, $iEnd); $i++) {
            $res .= $i == $iCurr ? '<span class="pagi_curr">' . $i . '</span>' : '<a class="pagi"  href="/' . $link . ($i == 1 ? $get : '?page=' . $i . $get . '') . '">' . $i . '</a>';
        }
    }
    if ($iEnd - $iCurr - $iRight > 1) $res .= '<span style=" margin: 0px 4px;">&hellip;</span>';
    if ($iEnd - $iCurr - $iRight > 0) $res .= '<a class="pagi" href="' . $link . ($i == 1 ? $get : '?page=' . $iEnd . $get) . '">' . $iEnd . '</a>';
    if ((($iCurr != $iEnd) && ($iEnd > $iRight)) || $iCurr == 1) {
        $res .= '<a class="pagi" href="/' . $link . '?page=' . ($iCurr + 1) . $get . '">&gt;</a>';
    }
    return $res;
}