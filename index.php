<?php
//Подключаем парсер
include_once('simple_html_dom.php');
//Вычисляем ссылку на расписание из данных параметров
$action=htmlspecialchars($_GET['action']);
$vr = htmlspecialchars($_GET['vr']);
switch(htmlspecialchars($_GET["action"])){
    case "group":
        $url = htmlspecialchars($_GET["url"]) . 'action=group' . "&union=". htmlspecialchars($_GET["union"]) . "&sid=". htmlspecialchars($_GET["sid"]) . "&gr=" . htmlspecialchars($_GET["gr"]) . "&year=" . htmlspecialchars($_GET["year"]) . "&vr=". htmlspecialchars($_GET["vr"]);
        break;
    case "prep":
        $url = htmlspecialchars($_GET["url"]) . 'action=prep' . '&prep='. htmlspecialchars($_GET['prep']) . "&vr=" . htmlspecialchars($_GET["vr"]) . '&count=' . htmlspecialchars($_GET["count"]). '&shed[0]=' . htmlspecialchars($_GET["shed"]). '&union[0]='. htmlspecialchars($_GET['union']). '&year[0]='.htmlspecialchars($_GET['year']);
        break;
    case "cab":
        $url = htmlspecialchars($_GET["url"]) . 'action=cab' . '&prep='. htmlspecialchars($_GET['prep']) . "&vr=" . htmlspecialchars($_GET["vr"]) . '&count=' . htmlspecialchars($_GET["count"]). '&shed[0]=' . htmlspecialchars($_GET["shed"]). '&union[0]='. htmlspecialchars($_GET['union']). '&year[0]='.htmlspecialchars($_GET['year']);
        $action="prep";
        break;
}
$newUrl=html_entity_decode($url);
//$url = htmlspecialchars($_GET["url"]) ."&union=". htmlspecialchars($_GET["union"]) . "&sid=". htmlspecialchars($_GET["sid"]) . "&gr=" . htmlspecialchars($_GET["gr"]) . "&year=" . htmlspecialchars($_GET["year"]) . "&vr=". htmlspecialchars($_GET["vr"]);
//Получаем по ссылке ХТМЛ страницы
$html = file_get_html($newUrl);
//Устанавливаем время, чтобы выводить дни начиная с текущего
date_default_timezone_set("Asia/Yekaterinburg");
//$url="http://ikis.tsogu.ru/shedule/show_shedule.php?action=group&union=0&sid=96&gr=111&year= 2015&vr=0 ";
/*foreach($output as $cell){
    echo $cell;
}
*/


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>My App</title>
    <!-- Path to Framework7 Library CSS-->
    <link rel="stylesheet" href="css/framework7.min.css">
    <!-- Path to your custom app styles-->
    <link rel="stylesheet" href="css/my-app.css">
</head>
<body>
<!-- Status bar overlay for fullscreen mode-->

<!-- Views-->
<div class="views">
    <!-- Your main view, should have "view-main" class-->
    <div class="view view-main">
        <!-- Top Navbar-->
        <!-- Pages, because we need fixed-through navbar and toolbar, it has additional appropriate classes-->
        <div class="pages navbar-through toolbar-through">
            <!-- Page, data-page contains page name-->
            <div data-page="index" class="page">
                <!-- Scrollable page content-->

                <div class="page-content" style="-moz-hyphens: auto;-webkit-hyphens: auto;-ms-hyphens: auto;">

                    <?php
                    //Вычисляем порядковый номер текущего дня, преобразуем его в число, отнимаем от него единицу
                    $currentDay = (int)date("N")-1;

                    //Удаляем из всех дробей дробных ячеек всё, намекающее на ячейку для урока, дабы в таблице с учителями нормально пропарсить
                    $drobCellsCont=$html->find('td[class="urok1"]');
                    $drobCellsCont2=$html->find('td[class="urok2"]');
                    $drobCells=$html->find('table[class="t_urok_drob"]');
                    foreach($drobCellsCont as $dropCellCont){
                        $dropCellCont->{'class'}=null;
                    }
                    foreach($drobCellsCont2 as $drobCellCont2){
                        $drobCellCont2->{'class'}=null;
                    }
                    foreach($drobCells as $drobCell){
                        $drobCell->{'width'}="100%";
                        $drobCell->{'class'}=null;
                    }

                    function getPrepDayVR($allCells,$cellDayNumber,$vr){
                        $dailyCells = array();
                        $number=6;
                        switch($vr){
                            case '1':
                                $number=7;
                                break;
                            case "0":
                                $number=6;
                                break;
                        }
                        for($i=0;$i<8;$i++){
                            $dailyCells[]=$allCells[$cellDayNumber];
                            $cellDayNumber+=$number;
                        }
                        return $dailyCells;
                    }
                    //функция узнает, есть ли на дню какие пары. Принимает массив из ячеек в виде ХТМЛ-строки
                    function isDayEmpty($lessons){
                        //По умолчанию возвращает true
                        $res=true;
                        //Проверяет каждую ячейку на содержимое
                        foreach($lessons as $lesson){
                            //Если что-то помимо ХТМЛ есть, то сразу выдает false
                            if ($lesson->plaintext!="" && $lesson->plaintext!="&nbsp;" && $lesson->plaintext!="&nbsp;&nbsp;") return false;
                        }
                        //Если ничего не находит - возвращает true по умолчанию
                        return $res;
                    }
                    switch($action){
                        case 'group': {
                            //Проверяет 7 столбцов. Так как учебная неделя состоит только из 6 дней - один пропускается
                            for ($globalDay = 1; $globalDay <= 7; $globalDay++) {
                                //Текущий день был вычислен и мы отняли от него единицу. В начале цикла добавляем единицу и начинаем с текущего
                                $currentDay++;
                                //С каждым шагом цикла проверяемый день увеличивается на 1. Если он больше 7, то есть воскресенья,
                                //то от него отнимается 7. То есть 8 больше чем 7, а 7 это воскресенье. Следовательно 8 это
                                //понедельник. 8-7=1, что является порядковым номером понедельника

                                if ($currentDay > 7) {
                                    $currentDay = $currentDay - 7;
                                }
                                //7 - это воскресенье. Воскресенье мы пропускаем.
                                if ($currentDay == 7) {
                                    continue;
                                }

                                //output это массив из ячеек на текущий день, содержащих информацию о парах
                                $output = $html->find('td[day=' . $currentDay . ']');


                                //В цикле обозначается начало карточки, в style - автоматический перенос русских слов браузером
                                echo '<div class="card" ><div class="card-header">';
                                //В названии карточки в зависимости от порядкового дня недели выписывается название дня
                                switch ($currentDay) {
                                    case 1:
                                        echo "Понедельник";
                                        break;
                                    case 2:
                                        echo "Вторник";
                                        break;
                                    case 3:
                                        echo "Среда";
                                        break;
                                    case 4:
                                        echo "Четверг";
                                        break;
                                    case 5:
                                        echo "Пятница";
                                        break;
                                    case 6:
                                        echo "Суббота";
                                        break;
                                }
                                //Дописываем конец заголовка карточки
                                echo '</div>';
                                //Если на дню что-то есть
                                //То есть isDayEmpty() возвращает false если на дню что-то есть, иначе - true
                                if (!isDayEmpty($output)) {
                                    //Выписывается начало тела карточки
                                    echo '<div class="card-content"></div><div class="list-block"><ul>';

                                    //Я не знаю зачем оно здесь, главное - не трогать пока работает, лол
                                    $output = $html->find('td[day=' . $currentDay . ']');
                                    //Цикл перебирает каждую ячейку. Важно знать номер ячейки, поэтому вместо foreach юзаю for
                                    for ($i = 0; $i <= sizeof($output) - 1; $i++) {
                                        /* Здесь важный момент. Так как у нас на учебной неделе всего 6 дней, а в расписании
                                        показывается 7, то один день будет повторятся. Программа читает таблицу не сверху-вниз,
                                        как человек, а слева-направо и только потом сверху-вниз. Поэтому на один из дней ячейки
                                        повторяются и вместо максимальных 8 ячеек вывестись могут 16.
                                        Чтобы этого избежать, мы проверяем, больше ли на дню ячеек, чем 8 (Тут 10, но не важно)
                                        и если больше, то каждую вторую не выводим. Каждую вторую - потому как программа читает
                                        таблицу слева-напрво – ячейка из первого дня в расписании всегда будет первой. Она-то
                                        нам и нужна.*/
                                        if (sizeof($output) > 10) {
                                            if ($i % 2 == 1) {
                                                continue;
                                            }
                                        }

                                        if (($output[$i]->plaintext) != "") {
                                            //Проверяем, несет ли ячейка какую-то полезную информацию, а не ИГА/Практика/Сессия ли это какая-нибудь
                                            //ИГА/Практика/Сессия выведутся, но только один раз как первая пара
                                            if ($i != 0) {
                                                //Записываем чистое содержимое урока, чтобы не парсить каждый раз
                                                $plainContagion = $output[$i]->plaintext;
                                                /*if (($plainContagion)==($output[$i-1]->plaintext)){
                                                    continue;
                                                }*/
                                                //Не выводим ИГА/Практику/Сессию дважды
                                                if ($plainContagion == "ИГА" or $plainContagion == "Практика" or $plainContagion == "Сессия") {
                                                    continue;
                                                }
                                            }
                                            //Узнаем номер урока
                                            $iconumber = $output[$i]->{'urok'};
                                            //растягиваем таблицу с уроком/учителем/кабинетом
                                            $output[$i]->{'width'} = "100%";
                                            $comms3 = $output[$i]->find('table[class=comm3]');
                                            foreach ($comms3 as $comm3) {
                                                $comm3->{'width'} = '100%';
                                            }
                                            /*
                                            Удаляем все строки из документа, содержащие 4 неразрывных пробела
                                            Такое встречается в паре, разделенной на два, когда вместо одной из пар - пустая ячейка
                                            Если ты верстальщик - не используй неразрывные пробелы, у тебя css есть
                                            Иначе я тебе ебло обоссу сучара
                                            */
                                            $trs = $output[$i]->find('tr');
                                            foreach ($trs as $tr) {
                                                if ($tr->plaintext == '&nbsp;&nbsp;&nbsp;&nbsp;') {
                                                    //Удаляем элемент, заменяя его ХТМЛ-содержимое и сам тег пустой строкой
                                                    $tr->outertext = '';
                                                }
                                            }

                                            //Я хотел выводить кабинет как-то иначе, но передумал
                                            //$cabinet="100";

                                            /*
                                            Заставляем кабинеты прилипать к правому краю
                                            Если честно, я думаю что они прилипают к правому краю из-за чего-то другого
                                            Возможно я написал это и оно не сработало, я написал что-то еще и забыл стереть это
                                            Но всё ведь работает, верно?
                                            */
                                            $cabs = $output[$i]->find('div[class=cab]');
                                            foreach ($cabs as $cab) {
                                                $cab->{'align'} = "right";
                                                //$cab->{'class'} = "item-after";

                                            }

                                            //Растягиваем все дробные (разделенные на две) ячейки с парами
                                            $drobs = $output[$i]->find('table[class=t_urok_drob]');
                                            foreach ($drobs as $drob) {
                                                $drob->{'width'} = "100%";
                                            }

                                            //Убираем align со всех ячеек всех таблиц. В оригинале они прилипают к центру.
                                            $tds = $output[$i]->find('td');
                                            foreach ($tds as $td) {
                                                $td->{'align'} = null;
                                            }

                                            /*Заставляем ячейки с кабинетами прилипать к правому краю
                                            Ты это возможно уже видел, но дело в том, что в дробных ячейках для кабинетов
                                            используется тег <div>, а для обычных - td. Или наоборот. Не важно,
                                            главное что используются они оба и всегда по-разному.*/
                                            $tdcabs = $output[$i]->find('td[class=cab]');
                                            foreach ($tdcabs as $tdcab) {
                                                $tdcab->{'align'} = "right";
                                                //$tdcab->{'class'} = "item-after";

                                            }

                                            /*
                                             * После всех изменений ХТМЛ содержимого ячейки с парой, выписываем в карточку
                                             * элемент списка, содержащий иконку с номером (там на самом деле написано время)
                                             * пары, затем само отредактированное содержимое ячейки с парой и дописываем конец
                                             * элемента списка. Это конец шага цикла, такое будет выписываться для каждой ячейки
                                             * с парой.
                                             */
                                            echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-" . $iconumber . "'></i></div><div class='item-inner'>" . $output[$i] . "</div></div></li>";
                                        }

                                    }
                                    //Дописываем конец карточки
                                    //Это конец шага циклом выше, то есть это конец карточки для каждого дня с парами
                                } else {
                                    echo '<div class="card-footer">Нет занятий';
                                }
                                echo '</div></div>';


                            }
                        }
                    break;
                        //Если запрос по преподавателю
                        case'prep':{
                            $weekCells=$html->find('td[class="urok"]');
                        switch($vr){
                            case '1':{
                                for($a=0;$a<6;$a++){

                                    //Находим большущий массив из всех ячеек в таблице, предназначенные для уроков

                                    //Находим ячейки первого столбца, как правило, это ячейки текущего дня.
                                    $currentDayCells=getPrepDayVR($weekCells,$a,'1');
                                    //Мы отняли единицу от текущего дня, теперь прибавляем обратно.

                                    $currentDay++;
                                    //С каждым шагом цикла проверяемый день увеличивается на 1. Если он больше 6, то есть субботы,
                                    //то от него отнимается 6. То есть 7 больше чем 6, а 6 это суббота. Следовательно 7 это
                                    //понедельник. 7-6=1, что является порядковым номером понедельника

                                    if ($currentDay > 6) {
                                        $currentDay = $currentDay - 6;
                                    }
                                    //Пишем начало карточки, её заголовок
                                    echo '<div class="card" ><div class="card-header">';
                                    //В зависимости от текущего дня пишем в заголовок день недели
                                    switch ($currentDay) {
                                        case 1:
                                            echo "Понедельник";
                                            break;
                                        case 2:
                                            echo "Вторник";
                                            break;
                                        case 3:
                                            echo "Среда";
                                            break;
                                        case 4:
                                            echo "Четверг";
                                            break;
                                        case 5:
                                            echo "Пятница";
                                            break;
                                        case 6:
                                            echo "Суббота";
                                            break;
                                    }
                                    //echo $newUrl;
                                    //Дописываем конец заголовка карточки
                                    echo '</div>';

                                    if(!isDayEmpty($currentDayCells)){
                                        echo '<div class="card-content"></div><div class="list-block"><ul>';
                                    for($currentCell=1;$currentCell<=9;$currentCell++){
                                        include_once('simple_html_dom.php');
                                        if (!($currentDayCells[$currentCell-1]->plaintext!='&nbsp;&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='')){
                                            continue;
                                        }
                                        //Растягиваем ячейку с уроком и табличку с парой/кабинетом
                                        $currentDayCells[$currentCell-1]->{'width'} = "100%";
                                        $comms33 = $currentDayCells[$currentCell-1]->find('table[class=comm3]');
                                        foreach ($comms33 as $comm33) {
                                            $comm33->{'width'} = '100%';
                                        }

                                        //удаляем пустые строки в таблицах
                                        //Если это ты верстал такие таблицы, которые мне приходится парсить - у меня ведро говна с твоим именем на нем
                                        $trs2 = $currentDayCells[$currentCell-1]->find('tr');
                                        foreach ($trs2 as $tr2) {
                                            if ($tr2->plaintext == '&nbsp;&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;' or $tr2->plaintext == '') {
                                                //Удаляем элемент, заменяя его ХТМЛ-содержимое и сам тег пустой строкой
                                                $tr2->outertext = '';
                                            }
                                        }

                                        //Растягиваем все дробные (разделенные на две) ячейки с парами
                                        $drobs2 = $currentDayCells[$currentCell-1]->find('table[class=t_urok_drob]');
                                        foreach ($drobs2 as $drob2) {
                                            $drob2->{'width'} = "100%";
                                        }

                                        //Убираем align со всех ячеек всех таблиц. В оригинале они прилипают к центру.
                                        $tds2 = $currentDayCells[$currentCell-1]->find('td');
                                        foreach ($tds2 as $td2) {
                                            $td2->{'align'} = null;
                                        }

                                        /*Заставляем ячейки с кабинетами прилипать к правому краю
                                            Ты это возможно уже видел, но дело в том, что в дробных ячейках для кабинетов
                                            используется тег <div>, а для обычных - td. Или наоборот. Не важно,
                                            главное что используются они оба и всегда по-разному.*/
                                        $tdcabs2 = $currentDayCells[$currentCell-1]->find('td[class=cabs]');
                                        foreach ($tdcabs2 as $tdcab2) {
                                            $tdcab2->{'align'} = "right";
                                            //$tdcab->{'class'} = "item-after";

                                        }

                                        /*
                                            Заставляем кабинеты прилипать к правому краю
                                            Если честно, я думаю что они прилипают к правому краю из-за чего-то другого
                                            Возможно я написал это и оно не сработало, я написал что-то еще и забыл стереть это
                                            Но всё ведь работает, верно?
                                            */
                                        $cabs2 = $currentDayCells[$currentCell-1]->find('div[class=cab]');
                                        foreach ($cabs2 as $cab2) {
                                            $cab2->{'align'} = "right";
                                            //$cab->{'class'} = "item-after";
                                        }
                                            echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-" . $currentCell . "'></i></div><div class='item-inner'>" . $currentDayCells[$currentCell-1] . "</div></div></li>";


                                    }
                                    } else {
                                        echo '<div class="card-footer">Нет занятий';
                                    }
                                    echo '</div></div>';

                                }

                            }
                            break;
                            case '0':{
                                $currentDay = (int)date("N")-1;
                                for($a=0;$a<7;$a++){

                                    //Находим большущий массив из всех ячеек в таблице, предназначенные для уроков

                                    //Находим ячейки первого столбца, как правило, это ячейки текущего дня.
                                    $currentDayCells=getPrepDayVR($weekCells,$currentDay,'0');
                                    //Мы отняли единицу от текущего дня, теперь прибавляем обратно.

                                    $currentDay++;
                                    //С каждым шагом цикла проверяемый день увеличивается на 1. Если он больше 6, то есть субботы,
                                    //то от него отнимается 6. То есть 7 больше чем 6, а 6 это суббота. Следовательно 7 это
                                    //понедельник. 7-6=1, что является порядковым номером понедельника

                                    if ($currentDay > 6) {
                                        $currentDay = $currentDay - 6;
                                    }
                                    //Пишем начало карточки, её заголовок
                                    echo '<div class="card" ><div class="card-header">';
                                    //В зависимости от текущего дня пишем в заголовок день недели
                                    switch ($currentDay) {
                                        case 1:
                                            echo "Понедельник";
                                            break;
                                        case 2:
                                            echo "Вторник";
                                            break;
                                        case 3:
                                            echo "Среда";
                                            break;
                                        case 4:
                                            echo "Четверг";
                                            break;
                                        case 5:
                                            echo "Пятница";
                                            break;
                                        case 6:
                                            echo "Суббота";
                                            break;
                                    }
                                    //echo $newUrl;
                                    //Дописываем конец заголовка карточки
                                    echo '</div>';


                                    //Если на дню есть пары
                                    if(!isDayEmpty($currentDayCells)){
                                        echo '<div class="card-content"></div><div class="list-block"><ul>';
                                        for($currentCell=1;$currentCell<=8;$currentCell++){
                                            //include_once('simple_html_dom.php');
                                            if (!($currentDayCells[$currentCell-1]->plaintext!='&nbsp;&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='')){
                                                continue;
                                            }
                                            //Растягиваем ячейку с уроком и табличку с парой/кабинетом
                                            $currentDayCells[$currentCell-1]->{'width'} = "100%";
                                            $comms33 = $currentDayCells[$currentCell-1]->find('table[class=comm3]');
                                            foreach ($comms33 as $comm33) {
                                                $comm33->{'width'} = '100%';
                                            }

                                            //удаляем пустые строки в таблицах
                                            //Если это ты верстал такие таблицы, которые мне приходится парсить - у меня ведро говна с твоим именем на нем
                                            $trs2 = $currentDayCells[$currentCell-1]->find('tr');
                                            foreach ($trs2 as $tr2) {
                                                if ($tr2->plaintext == '&nbsp;&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;' or $tr2->plaintext == '') {
                                                    //Удаляем элемент, заменяя его ХТМЛ-содержимое и сам тег пустой строкой
                                                    $tr2->outertext = '';
                                                }
                                            }

                                            //Растягиваем все дробные (разделенные на две) ячейки с парами
                                            $drobs2 = $currentDayCells[$currentCell-1]->find('table[class=t_urok_drob]');
                                            foreach ($drobs2 as $drob2) {
                                                $drob2->{'width'} = "100%";
                                            }

                                            //Убираем align со всех ячеек всех таблиц. В оригинале они прилипают к центру.
                                            $tds2 = $currentDayCells[$currentCell-1]->find('td');
                                            foreach ($tds2 as $td2) {
                                                $td2->{'align'} = null;
                                            }

                                            /*Заставляем ячейки с кабинетами прилипать к правому краю
                                                Ты это возможно уже видел, но дело в том, что в дробных ячейках для кабинетов
                                                используется тег <div>, а для обычных - td. Или наоборот. Не важно,
                                                главное что используются они оба и всегда по-разному.*/
                                            $tdcabs2 = $currentDayCells[$currentCell-1]->find('td[class=cabs]');
                                            foreach ($tdcabs2 as $tdcab2) {
                                                $tdcab2->{'align'} = "right";
                                                //$tdcab->{'class'} = "item-after";

                                            }

                                            /*
                                                Заставляем кабинеты прилипать к правому краю
                                                Если честно, я думаю что они прилипают к правому краю из-за чего-то другого
                                                Возможно я написал это и оно не сработало, я написал что-то еще и забыл стереть это
                                                Но всё ведь работает, верно?
                                                */
                                            $cabs2 = $currentDayCells[$currentCell-1]->find('div[class=cab]');
                                            foreach ($cabs2 as $cab2) {
                                                $cab2->{'align'} = "right";
                                                //$cab->{'class'} = "item-after";
                                            }
                                                echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-" . $currentCell . "'></i></div><div class='item-inner'>" . $currentDayCells[$currentCell-1] . "</div></div></li>";
                                            }


                                    } else {
                                        //Если на дню ничего нет - подписываем карточку, что занятий на ней нет.
                                        echo '<div class="card-footer">Нет занятий';
                                    }
                                    echo '</div></div>';

                                }

                            }
                            break;
                        }
                        }
                        break;
                    }
                    ?>
                </div>


            </div>
        </div>
        <!-- Bottom Toolbar-->
                </div>
            </div>
            <!-- Path to Framework7 Library JS-->
            <script type="text/javascript" src="js/framework7.min.js"></script>
            <!-- Path to your app js-->
            <script type="text/javascript" src="js/my-app.js"></script>
</body>
</html>
