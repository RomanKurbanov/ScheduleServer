<?php
//���������� ������
include_once('simple_html_dom.php');
//��������� ������ �� ���������� �� ������ ����������
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
//�������� �� ������ ���� ��������
$html = file_get_html($newUrl);
//������������� �����, ����� �������� ��� ������� � ��������
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
                    //��������� ���������� ����� �������� ���, ����������� ��� � �����, �������� �� ���� �������
                    $currentDay = (int)date("N")-1;

                    //������� �� ���� ������ ������� ����� ��, ���������� �� ������ ��� �����, ���� � ������� � ��������� ��������� ����������
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
                    //������� ������, ���� �� �� ��� ����� ����. ��������� ������ �� ����� � ���� ����-������
                    function isDayEmpty($lessons){
                        //�� ��������� ���������� true
                        $res=true;
                        //��������� ������ ������ �� ����������
                        foreach($lessons as $lesson){
                            //���� ���-�� ������ ���� ����, �� ����� ������ false
                            if ($lesson->plaintext!="" && $lesson->plaintext!="&nbsp;" && $lesson->plaintext!="&nbsp;&nbsp;") return false;
                        }
                        //���� ������ �� ������� - ���������� true �� ���������
                        return $res;
                    }
                    switch($action){
                        case 'group': {
                            //��������� 7 ��������. ��� ��� ������� ������ ������� ������ �� 6 ���� - ���� ������������
                            for ($globalDay = 1; $globalDay <= 7; $globalDay++) {
                                //������� ���� ��� �������� � �� ������ �� ���� �������. � ������ ����� ��������� ������� � �������� � ��������
                                $currentDay++;
                                //� ������ ����� ����� ����������� ���� ������������� �� 1. ���� �� ������ 7, �� ���� �����������,
                                //�� �� ���� ���������� 7. �� ���� 8 ������ ��� 7, � 7 ��� �����������. ������������� 8 ���
                                //�����������. 8-7=1, ��� �������� ���������� ������� ������������

                                if ($currentDay > 7) {
                                    $currentDay = $currentDay - 7;
                                }
                                //7 - ��� �����������. ����������� �� ����������.
                                if ($currentDay == 7) {
                                    continue;
                                }

                                //output ��� ������ �� ����� �� ������� ����, ���������� ���������� � �����
                                $output = $html->find('td[day=' . $currentDay . ']');


                                //� ����� ������������ ������ ��������, � style - �������������� ������� ������� ���� ���������
                                echo '<div class="card" ><div class="card-header">';
                                //� �������� �������� � ����������� �� ����������� ��� ������ ������������ �������� ���
                                switch ($currentDay) {
                                    case 1:
                                        echo "�����������";
                                        break;
                                    case 2:
                                        echo "�������";
                                        break;
                                    case 3:
                                        echo "�����";
                                        break;
                                    case 4:
                                        echo "�������";
                                        break;
                                    case 5:
                                        echo "�������";
                                        break;
                                    case 6:
                                        echo "�������";
                                        break;
                                }
                                //���������� ����� ��������� ��������
                                echo '</div>';
                                //���� �� ��� ���-�� ����
                                //�� ���� isDayEmpty() ���������� false ���� �� ��� ���-�� ����, ����� - true
                                if (!isDayEmpty($output)) {
                                    //������������ ������ ���� ��������
                                    echo '<div class="card-content"></div><div class="list-block"><ul>';

                                    //� �� ���� ����� ��� �����, ������� - �� ������� ���� ��������, ���
                                    $output = $html->find('td[day=' . $currentDay . ']');
                                    //���� ���������� ������ ������. ����� ����� ����� ������, ������� ������ foreach ���� for
                                    for ($i = 0; $i <= sizeof($output) - 1; $i++) {
                                        /* ����� ������ ������. ��� ��� � ��� �� ������� ������ ����� 6 ����, � � ����������
                                        ������������ 7, �� ���� ���� ����� ����������. ��������� ������ ������� �� ������-����,
                                        ��� �������, � �����-������� � ������ ����� ������-����. ������� �� ���� �� ���� ������
                                        ����������� � ������ ������������ 8 ����� ��������� ����� 16.
                                        ����� ����� ��������, �� ���������, ������ �� �� ��� �����, ��� 8 (��� 10, �� �� �����)
                                        � ���� ������, �� ������ ������ �� �������. ������ ������ - ������ ��� ��������� ������
                                        ������� �����-������ � ������ �� ������� ��� � ���������� ������ ����� ������. ���-��
                                        ��� � �����.*/
                                        if (sizeof($output) > 10) {
                                            if ($i % 2 == 1) {
                                                continue;
                                            }
                                        }

                                        if (($output[$i]->plaintext) != "") {
                                            //���������, ����� �� ������ �����-�� �������� ����������, � �� ���/��������/������ �� ��� �����-������
                                            //���/��������/������ ���������, �� ������ ���� ��� ��� ������ ����
                                            if ($i != 0) {
                                                //���������� ������ ���������� �����, ����� �� ������� ������ ���
                                                $plainContagion = $output[$i]->plaintext;
                                                /*if (($plainContagion)==($output[$i-1]->plaintext)){
                                                    continue;
                                                }*/
                                                //�� ������� ���/��������/������ ������
                                                if ($plainContagion == "���" or $plainContagion == "��������" or $plainContagion == "������") {
                                                    continue;
                                                }
                                            }
                                            //������ ����� �����
                                            $iconumber = $output[$i]->{'urok'};
                                            //����������� ������� � ������/��������/���������
                                            $output[$i]->{'width'} = "100%";
                                            $comms3 = $output[$i]->find('table[class=comm3]');
                                            foreach ($comms3 as $comm3) {
                                                $comm3->{'width'} = '100%';
                                            }
                                            /*
                                            ������� ��� ������ �� ���������, ���������� 4 ����������� �������
                                            ����� ����������� � ����, ����������� �� ���, ����� ������ ����� �� ��� - ������ ������
                                            ���� �� ����������� - �� ��������� ����������� �������, � ���� css ����
                                            ����� � ���� ���� ������ ������
                                            */
                                            $trs = $output[$i]->find('tr');
                                            foreach ($trs as $tr) {
                                                if ($tr->plaintext == '&nbsp;&nbsp;&nbsp;&nbsp;') {
                                                    //������� �������, ������� ��� ����-���������� � ��� ��� ������ �������
                                                    $tr->outertext = '';
                                                }
                                            }

                                            //� ����� �������� ������� ���-�� �����, �� ���������
                                            //$cabinet="100";

                                            /*
                                            ���������� �������� ��������� � ������� ����
                                            ���� ������, � ����� ��� ��� ��������� � ������� ���� ��-�� ����-�� �������
                                            �������� � ������� ��� � ��� �� ���������, � ������� ���-�� ��� � ����� ������� ���
                                            �� �� ���� ��������, �����?
                                            */
                                            $cabs = $output[$i]->find('div[class=cab]');
                                            foreach ($cabs as $cab) {
                                                $cab->{'align'} = "right";
                                                //$cab->{'class'} = "item-after";

                                            }

                                            //����������� ��� ������� (����������� �� ���) ������ � ������
                                            $drobs = $output[$i]->find('table[class=t_urok_drob]');
                                            foreach ($drobs as $drob) {
                                                $drob->{'width'} = "100%";
                                            }

                                            //������� align �� ���� ����� ���� ������. � ��������� ��� ��������� � ������.
                                            $tds = $output[$i]->find('td');
                                            foreach ($tds as $td) {
                                                $td->{'align'} = null;
                                            }

                                            /*���������� ������ � ���������� ��������� � ������� ����
                                            �� ��� �������� ��� �����, �� ���� � ���, ��� � ������� ������� ��� ���������
                                            ������������ ��� <div>, � ��� ������� - td. ��� ��������. �� �����,
                                            ������� ��� ������������ ��� ��� � ������ ��-�������.*/
                                            $tdcabs = $output[$i]->find('td[class=cab]');
                                            foreach ($tdcabs as $tdcab) {
                                                $tdcab->{'align'} = "right";
                                                //$tdcab->{'class'} = "item-after";

                                            }

                                            /*
                                             * ����� ���� ��������� ���� ����������� ������ � �����, ���������� � ��������
                                             * ������� ������, ���������� ������ � ������� (��� �� ����� ���� �������� �����)
                                             * ����, ����� ���� ����������������� ���������� ������ � ����� � ���������� �����
                                             * �������� ������. ��� ����� ���� �����, ����� ����� ������������ ��� ������ ������
                                             * � �����.
                                             */
                                            echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-" . $iconumber . "'></i></div><div class='item-inner'>" . $output[$i] . "</div></div></li>";
                                        }

                                    }
                                    //���������� ����� ��������
                                    //��� ����� ���� ������ ����, �� ���� ��� ����� �������� ��� ������� ��� � ������
                                } else {
                                    echo '<div class="card-footer">��� �������';
                                }
                                echo '</div></div>';


                            }
                        }
                    break;
                        //���� ������ �� �������������
                        case'prep':{
                            $weekCells=$html->find('td[class="urok"]');
                        switch($vr){
                            case '1':{
                                for($a=0;$a<6;$a++){

                                    //������� ��������� ������ �� ���� ����� � �������, ��������������� ��� ������

                                    //������� ������ ������� �������, ��� �������, ��� ������ �������� ���.
                                    $currentDayCells=getPrepDayVR($weekCells,$a,'1');
                                    //�� ������ ������� �� �������� ���, ������ ���������� �������.

                                    $currentDay++;
                                    //� ������ ����� ����� ����������� ���� ������������� �� 1. ���� �� ������ 6, �� ���� �������,
                                    //�� �� ���� ���������� 6. �� ���� 7 ������ ��� 6, � 6 ��� �������. ������������� 7 ���
                                    //�����������. 7-6=1, ��� �������� ���������� ������� ������������

                                    if ($currentDay > 6) {
                                        $currentDay = $currentDay - 6;
                                    }
                                    //����� ������ ��������, � ���������
                                    echo '<div class="card" ><div class="card-header">';
                                    //� ����������� �� �������� ��� ����� � ��������� ���� ������
                                    switch ($currentDay) {
                                        case 1:
                                            echo "�����������";
                                            break;
                                        case 2:
                                            echo "�������";
                                            break;
                                        case 3:
                                            echo "�����";
                                            break;
                                        case 4:
                                            echo "�������";
                                            break;
                                        case 5:
                                            echo "�������";
                                            break;
                                        case 6:
                                            echo "�������";
                                            break;
                                    }
                                    //echo $newUrl;
                                    //���������� ����� ��������� ��������
                                    echo '</div>';

                                    if(!isDayEmpty($currentDayCells)){
                                        echo '<div class="card-content"></div><div class="list-block"><ul>';
                                    for($currentCell=1;$currentCell<=9;$currentCell++){
                                        include_once('simple_html_dom.php');
                                        if (!($currentDayCells[$currentCell-1]->plaintext!='&nbsp;&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='')){
                                            continue;
                                        }
                                        //����������� ������ � ������ � �������� � �����/���������
                                        $currentDayCells[$currentCell-1]->{'width'} = "100%";
                                        $comms33 = $currentDayCells[$currentCell-1]->find('table[class=comm3]');
                                        foreach ($comms33 as $comm33) {
                                            $comm33->{'width'} = '100%';
                                        }

                                        //������� ������ ������ � ��������
                                        //���� ��� �� ������� ����� �������, ������� ��� ���������� ������� - � ���� ����� ����� � ����� ������ �� ���
                                        $trs2 = $currentDayCells[$currentCell-1]->find('tr');
                                        foreach ($trs2 as $tr2) {
                                            if ($tr2->plaintext == '&nbsp;&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;' or $tr2->plaintext == '') {
                                                //������� �������, ������� ��� ����-���������� � ��� ��� ������ �������
                                                $tr2->outertext = '';
                                            }
                                        }

                                        //����������� ��� ������� (����������� �� ���) ������ � ������
                                        $drobs2 = $currentDayCells[$currentCell-1]->find('table[class=t_urok_drob]');
                                        foreach ($drobs2 as $drob2) {
                                            $drob2->{'width'} = "100%";
                                        }

                                        //������� align �� ���� ����� ���� ������. � ��������� ��� ��������� � ������.
                                        $tds2 = $currentDayCells[$currentCell-1]->find('td');
                                        foreach ($tds2 as $td2) {
                                            $td2->{'align'} = null;
                                        }

                                        /*���������� ������ � ���������� ��������� � ������� ����
                                            �� ��� �������� ��� �����, �� ���� � ���, ��� � ������� ������� ��� ���������
                                            ������������ ��� <div>, � ��� ������� - td. ��� ��������. �� �����,
                                            ������� ��� ������������ ��� ��� � ������ ��-�������.*/
                                        $tdcabs2 = $currentDayCells[$currentCell-1]->find('td[class=cabs]');
                                        foreach ($tdcabs2 as $tdcab2) {
                                            $tdcab2->{'align'} = "right";
                                            //$tdcab->{'class'} = "item-after";

                                        }

                                        /*
                                            ���������� �������� ��������� � ������� ����
                                            ���� ������, � ����� ��� ��� ��������� � ������� ���� ��-�� ����-�� �������
                                            �������� � ������� ��� � ��� �� ���������, � ������� ���-�� ��� � ����� ������� ���
                                            �� �� ���� ��������, �����?
                                            */
                                        $cabs2 = $currentDayCells[$currentCell-1]->find('div[class=cab]');
                                        foreach ($cabs2 as $cab2) {
                                            $cab2->{'align'} = "right";
                                            //$cab->{'class'} = "item-after";
                                        }
                                            echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-" . $currentCell . "'></i></div><div class='item-inner'>" . $currentDayCells[$currentCell-1] . "</div></div></li>";


                                    }
                                    } else {
                                        echo '<div class="card-footer">��� �������';
                                    }
                                    echo '</div></div>';

                                }

                            }
                            break;
                            case '0':{
                                $currentDay = (int)date("N")-1;
                                for($a=0;$a<7;$a++){

                                    //������� ��������� ������ �� ���� ����� � �������, ��������������� ��� ������

                                    //������� ������ ������� �������, ��� �������, ��� ������ �������� ���.
                                    $currentDayCells=getPrepDayVR($weekCells,$currentDay,'0');
                                    //�� ������ ������� �� �������� ���, ������ ���������� �������.

                                    $currentDay++;
                                    //� ������ ����� ����� ����������� ���� ������������� �� 1. ���� �� ������ 6, �� ���� �������,
                                    //�� �� ���� ���������� 6. �� ���� 7 ������ ��� 6, � 6 ��� �������. ������������� 7 ���
                                    //�����������. 7-6=1, ��� �������� ���������� ������� ������������

                                    if ($currentDay > 6) {
                                        $currentDay = $currentDay - 6;
                                    }
                                    //����� ������ ��������, � ���������
                                    echo '<div class="card" ><div class="card-header">';
                                    //� ����������� �� �������� ��� ����� � ��������� ���� ������
                                    switch ($currentDay) {
                                        case 1:
                                            echo "�����������";
                                            break;
                                        case 2:
                                            echo "�������";
                                            break;
                                        case 3:
                                            echo "�����";
                                            break;
                                        case 4:
                                            echo "�������";
                                            break;
                                        case 5:
                                            echo "�������";
                                            break;
                                        case 6:
                                            echo "�������";
                                            break;
                                    }
                                    //echo $newUrl;
                                    //���������� ����� ��������� ��������
                                    echo '</div>';


                                    //���� �� ��� ���� ����
                                    if(!isDayEmpty($currentDayCells)){
                                        echo '<div class="card-content"></div><div class="list-block"><ul>';
                                        for($currentCell=1;$currentCell<=8;$currentCell++){
                                            //include_once('simple_html_dom.php');
                                            if (!($currentDayCells[$currentCell-1]->plaintext!='&nbsp;&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='&nbsp;' && $currentDayCells[$currentCell-1]->plaintext!='')){
                                                continue;
                                            }
                                            //����������� ������ � ������ � �������� � �����/���������
                                            $currentDayCells[$currentCell-1]->{'width'} = "100%";
                                            $comms33 = $currentDayCells[$currentCell-1]->find('table[class=comm3]');
                                            foreach ($comms33 as $comm33) {
                                                $comm33->{'width'} = '100%';
                                            }

                                            //������� ������ ������ � ��������
                                            //���� ��� �� ������� ����� �������, ������� ��� ���������� ������� - � ���� ����� ����� � ����� ������ �� ���
                                            $trs2 = $currentDayCells[$currentCell-1]->find('tr');
                                            foreach ($trs2 as $tr2) {
                                                if ($tr2->plaintext == '&nbsp;&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;&nbsp;' or $tr2->plaintext == '&nbsp;' or $tr2->plaintext == '') {
                                                    //������� �������, ������� ��� ����-���������� � ��� ��� ������ �������
                                                    $tr2->outertext = '';
                                                }
                                            }

                                            //����������� ��� ������� (����������� �� ���) ������ � ������
                                            $drobs2 = $currentDayCells[$currentCell-1]->find('table[class=t_urok_drob]');
                                            foreach ($drobs2 as $drob2) {
                                                $drob2->{'width'} = "100%";
                                            }

                                            //������� align �� ���� ����� ���� ������. � ��������� ��� ��������� � ������.
                                            $tds2 = $currentDayCells[$currentCell-1]->find('td');
                                            foreach ($tds2 as $td2) {
                                                $td2->{'align'} = null;
                                            }

                                            /*���������� ������ � ���������� ��������� � ������� ����
                                                �� ��� �������� ��� �����, �� ���� � ���, ��� � ������� ������� ��� ���������
                                                ������������ ��� <div>, � ��� ������� - td. ��� ��������. �� �����,
                                                ������� ��� ������������ ��� ��� � ������ ��-�������.*/
                                            $tdcabs2 = $currentDayCells[$currentCell-1]->find('td[class=cabs]');
                                            foreach ($tdcabs2 as $tdcab2) {
                                                $tdcab2->{'align'} = "right";
                                                //$tdcab->{'class'} = "item-after";

                                            }

                                            /*
                                                ���������� �������� ��������� � ������� ����
                                                ���� ������, � ����� ��� ��� ��������� � ������� ���� ��-�� ����-�� �������
                                                �������� � ������� ��� � ��� �� ���������, � ������� ���-�� ��� � ����� ������� ���
                                                �� �� ���� ��������, �����?
                                                */
                                            $cabs2 = $currentDayCells[$currentCell-1]->find('div[class=cab]');
                                            foreach ($cabs2 as $cab2) {
                                                $cab2->{'align'} = "right";
                                                //$cab->{'class'} = "item-after";
                                            }
                                                echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-" . $currentCell . "'></i></div><div class='item-inner'>" . $currentDayCells[$currentCell-1] . "</div></div></li>";
                                            }


                                    } else {
                                        //���� �� ��� ������ ��� - ����������� ��������, ��� ������� �� ��� ���.
                                        echo '<div class="card-footer">��� �������';
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
