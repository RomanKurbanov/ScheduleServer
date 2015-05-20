<?php
//���������� ������
include_once('simple_html_dom.php');
//��������� ������ �� ���������� �� ������ ����������
$url = htmlspecialchars($_GET["url"]) ."&union=". htmlspecialchars($_GET["union"]) . "&sid=". htmlspecialchars($_GET["sid"]) . "&gr=" . htmlspecialchars($_GET["gr"]) . "&year=" . htmlspecialchars($_GET["year"]) . "&vr=". htmlspecialchars($_GET["vr"]);
//�������� �� ������ ���� ��������
$html = file_get_html($url);
//������������� �����, ����� �������� ��� ������� � ��������
date_default_timezone_set('UTC');
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
                <div class="page-content">
                    <?php
                    //��������� ���������� ����� �������� ���, ����������� ��� � �����, �������� �� ���� �������
                    $currentDay = (int)date("N")-1;
                    //������� ������, ���� �� �� ��� ����� ����. ��������� ������ �� ����� � ���� ����-������
                    function isDayEmpty($lessons){
                        //�� ��������� ���������� true
                        $res=true;
                        //��������� ������ ������ �� ����������
                        foreach($lessons as $lesson){
                            //���� ���-�� ������ ���� ����, �� ����� ������ false
                            if ($lesson->plaintext!="") return false;
                        }
                        //���� ������ �� ������� - ���������� true �� ���������
                        return $res;
                    }
                    //��������� 7 ��������. ��� ��� ������� ������ ������� ������ �� 6 ���� - ���� ������������
                    for($globalDay=1;$globalDay<=7;$globalDay++){
                        //������� ���� ��� �������� � �� ������ �� ���� �������. � ������ ����� ��������� ������� � �������� � ��������
                        $currentDay++;
                        //� ������ ����� ����� ����������� ���� ������������� �� 1. ���� �� ������ 7, �� ���� �����������,
                        //�� �� ���� ���������� 7. �� ���� 8 ������ ��� 7, � 7 ��� �����������. ������������� 8 ���
                        //�����������. 8-7=1, ��� �������� ���������� ������� ������������
                        if ($currentDay>7){
                            $currentDay= $currentDay-7;
                        }
                        //output ��� ������ �� ����� �� ������� ����, ���������� ���������� � �����
                        $output = $html->find('td[day='.$currentDay.']');
                        //���� �� ��� ���-�� ����
                        //�� ���� isDayEmpty() ���������� false ���� �� ��� ���-�� ����, ����� - true
                        if (!isDayEmpty($output)){
                            //� ����� ������������ ������ ��������, � style - �������������� ������� ������� ���� ���������
                            echo '<div class="card" style="-moz-hyphens: auto;-webkit-hyphens: auto;-ms-hyphens: auto;"><div class="card-header">';
                            //� �������� �������� � ����������� �� ����������� ��� ������ ������������ �������� ���
                            switch($currentDay){
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
                            //������������ ������ ���� ��������
                            echo '</div><div class="card-content"></div><div class="list-block"><ul>';
                            //� �� ���� ����� ��� �����, ������� - �� ������� ���� ��������, ���
                            $output = $html->find('td[day='.$currentDay.']');
                            //���� ���������� ������ ������. ����� ����� ����� ������, ������� ������ foreach ���� for
                            for ($i = 0; $i <= sizeof($output)-1; $i++) {
                                /* ����� ������ ������. ��� ��� � ��� �� ������� ������ ����� 6 ����, � � ����������
                                ������������ 7, �� ���� ���� ����� ����������. ��������� ������ ������� �� ������-����,
                                ��� �������, � �����-������� � ������ ����� ������-����. ������� �� ���� �� ���� ������
                                ����������� � ������ ������������ 8 ����� ��������� ����� 16.
                                ����� ����� ��������, �� ���������, ������ �� �� ��� �����, ��� 8 (��� 10, �� �� �����)
                                � ���� ������, �� ������ ������ �� �������. ������ ������ - ������ ��� ��������� ������
                                ������� �����-������ � ������ �� ������� ��� � ���������� ������ ����� ������. ���-��
                                ��� � �����.*/
                                if (sizeof($output)>10){
                                    if ($i%2==1) {
                                        continue;
                                    }
                                }

                                if (($output[$i]->plaintext) != "") {
                                    //���������, ����� �� ������ �����-�� �������� ����������, � �� ���/��������/������ �� ��� �����-������
                                    //���/��������/������ ���������, �� ������ ���� ��� ��� ������ ����
                                    if ($i!=0) {
                                        //���������� ������ ���������� �����, ����� �� ������� ������ ���
                                        $plainContagion = $output[$i]->plaintext;
                                        /*if (($plainContagion)==($output[$i-1]->plaintext)){
                                            continue;
                                        }*/
                                        //�� ������� ���/��������/������ ������
                                        if ($plainContagion=="���" or $plainContagion=="��������" or $plainContagion=="������"){
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
                                    $trs=$output[$i]->find('tr');
                                    foreach ($trs as $tr) {
                                        if ($tr->plaintext=='&nbsp;&nbsp;&nbsp;&nbsp;'){
                                            //������� �������, ������� ��� ����-���������� � ��� ��� ������ �������
                                            $tr->outertext='';
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
                                    foreach ($drobs as $drob){
                                        $drob->{'width'}="100%";
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
                                    echo "<li><div class='item-content'><div class='item-media'><i class='icon icon-".$iconumber."'></i></div><div class='item-inner'>" . $output[$i] . "</div></div></li>";
                                }

                            }
                            //���������� ����� ��������
                            //��� ����� ���� ������ ����, �� ���� ��� ����� �������� ��� ������� ��� � ������
                            echo '</div></div>';
                        }


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
