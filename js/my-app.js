// Initialize your app
var myApp = new Framework7();

// Export selectors engine
var $$ = Dom7;

// Add view
var mainView = myApp.addView('.view-main', {
    // Because we use fixed-through navbar we can enable dynamic navbar
    dynamicNavbar: true
});

// Callbacks to run specific code for specific pages, for example for About page:
myApp.onPageInit('about', function (page) {
    // run createContentPage func after link was clicked
    $$('.create-page').on('click', function () {
        createContentPage();
    });
});

// Generate dynamic page
var dynamicPageIndex = 0;
function createContentPage() {
	mainView.router.loadContent(
        '<!-- Top Navbar-->' +
        '<div class="navbar">' +
        '  <div class="navbar-inner">' +
        '    <div class="left"><a href="#" class="back link"><i class="icon icon-back"></i><span>Back</span></a></div>' +
        '    <div class="center sliding">Dynamic Page ' + (++dynamicPageIndex) + '</div>' +
        '  </div>' +
        '</div>' +
        '<div class="pages">' +
        '  <!-- Page, data-page contains page name-->' +
        '  <div data-page="dynamic-pages" class="page">' +
        '    <!-- Scrollable page content-->' +
        '       <iframe class="page-content" border="0" width="100%" height="100%" src="https://2ch.hk"></iframe>' +
        '  </div>' +
        '</div>'
    );
	return;
}


function getparams(){
    var tmp = new Array();		// ��� ���������������
    var tmp2 = new Array();		// �������
    var param = new Array();
    var tmp3 = new Array();
    var get = location.search;	// ������ GET �������
    if(get != '') {
        tmp = (get.substr(1)).split('&');	// ��������� ����������
        for(var i=0; i < tmp.length; i++) {
            tmp2 = tmp[i].split('=');		// ������ param ����� ���������
            param[tmp2[0]] = tmp2[1];		// ���� ����(��� ����������)->��������
        }

    }return param;
}

function load_info(){
    $.ajax({
        type: "POST",
        url: "shedule/funct.php",
        dataType: "json",
        data: ({ action:'load_info'}),
        cache: false,
        async: false,
        success: function(data){
            var k=0;
            for (i=0;i<data.length;i++){
                if (data[i]['err']==0){
                    $(".err_cell").hide();
                    $("#win_shed").attr('shedule'+i,data[i]['id']).attr('union'+i,data[i]['un']).attr('year'+i,data[i]['year']);
                    k++;
                }
            }
            if (k>0){
                $("#win_shed").attr('count',k);
            } else {
                $(".err_cell").show();
                $("#win_shed").attr('count',0);
            }
        }
    });
}

function load_groups(kuda,otdel,shed,ye,chto){

    $.ajax({
        type: "POST",
        url: "shedule/funct.php",
        dataType: "json",
        data: ({ action:'load', act: chto, otd: otdel, vd: shed}),
        cache: false,
        async: false,
        success: function(data){
            var select=$(kuda);
            var selectitems="";
            if (data[0][0]!='e'){
                for (i=0;i<data.length;i++){
                    selectitems+="<option value="+data[i][0]+" id=group"+data[i][0]+" sid="+shed+" union=0 year="+ye+">"+data[i][1]+"-"+data[i][2]+"-("+data[i][3]+")-"+data[i][4]+"</option>";
                }
                select.html($(kuda).html()+selectitems);
            }

        }});
}

function load_groups_union(kuda,union){

    $.ajax({
        type: "POST",
        url: "shedule/funct.php",
        dataType: "json",
        data: ({ action:'load', act: 'list_groups_union', otd: union}),
        cache: false,
        async: false,
        success: function(data){
            var select=$(kuda);
            var selectitems="";
            if (data[0][0]!='e'){
                for (i=0;i<data.length;i++){
                    selectitems+="<option value="+data[i][0]+" id=group"+data[i][0]+"  sid="+union+" union=1>"+data[i][1]+"-"+data[i][2]+"-("+data[i][3]+")-"+data[i][4]+"</option>";
                }
                select.html($(kuda).html()+selectitems);
            }

        }});
}

function load_prep(kuda,otdel,first){

    $.post("shedule/funct.php", { action:'load', act: 'list_prepods', otd: otdel, bs:0}, function(data){

        var select=$(kuda);
        var selectitems="";
        select.text("");
        if (data[0][0]!='e'){
            if (first==true){  selectitems+="<option value=0 id=prep0>&nbsp;</option>";}
            for (var i=0;i<data.length;i++){
                if ((data[i][1]!='��������')&&(data[i][1]!='�������� 1')){
                    selectitems+="<option value="+data[i][0]+" id=prep"+data[i][0]+">"+data[i][1]+"</option>";
                }
            }
            select.html(selectitems);
        }

    },"json");

}

function load_cabs(kuda,korpus,probel){
    $.post("shedule/funct.php", { action:'load', act: 'cabs', otd:korpus,bs:0}, function(data){
        var select=$(kuda);
        var selectitems="";
        var m="";
        var k="";
        select.text("");
        if (probel==true){
            selectitems="<option value=0 id=cab0>&nbsp;</option>";
            selectitems+="<option value='all' id=caball>��� ��������</option>";
            if (data[0][0]!='e'){
                for (var i=0;i<data.length;i++){
                    if (data[i][3]==1) {m="�";} else {m="";}
                    if (data[i][4]==1) {k="�";} else {k="";}
                    selectitems+="<option value="+data[i][0]+" id=cab"+data[i][0]+">"+data[i][2]+"-"+data[i][1]+m+k+"</option>";
                }
                select.html(selectitems);
            }
        } else {
            selectitems="";
            if (data[0][0]!='e'){
                for (var i=0;i<data.length;i++){
                    if (data[i][3]==1) {m="�";} else {m="";}
                    if (data[i][4]==1) {k="�";} else {k="";}
                    selectitems+="<option value="+data[i][0]+" id=cab"+data[i][0]+">"+data[i][2]+"-"+data[i][1]+m+k+"</option>";
                }
                select.html(selectitems);
            }
        }
    },"json");
}

function load_info2(){
    $.ajax({
        type: "POST",
        url: "shedule/funct.php",
        dataType: "json",
        data: ({ action:'load_info'}),
        cache: false,
        async: false,
        success: function(data){
            var k=0;
            for (i=0;i<data.length;i++){
                if (data[i]['err']==0){
                    $("#shedule_on_main").attr('shedule'+i,data[i]['id']).attr('union'+i,data[i]['un']).attr('year'+i,data[i]['year']);
                    k++;
                }
            }
            if (k>0){
                $("#shedule_on_main").attr('count',k);
            } else {
                $("#shedule_on_main").attr('count',0);
            }
        }
    });
}

function load_groups2(kuda,otdel,shed,chto){

    $.ajax({
        type: "POST",
        url: "shedule/funct.php",
        dataType: "json",
        data: ({ action:'load', act: chto, otd: otdel, vd: shed}),
        cache: false,
        async: false,
        success: function(data){
            var select=$(kuda);
            var selectitems="";
            if (data[0][0]!='e'){
                for (i=0;i<data.length;i++){
                    if (data[i][5]!='otd'){
                        selectitems+="<tr><td><a href='http://www.ikis.tsogu.ru/shedule/show_shedule.php?action=group&union=0&sid="+shed+"&gr="+data[i][0]+"&vr=1' class=grlink target='_blank'>"+data[i][1]+"-"+data[i][2]+"-("+data[i][3]+")-"+data[i][4]+"</a></td></tr>";
                    } else {
                        selectitems+="<tr><td class=markotdel>"+data[i][6]+"</td></tr>";
                        selectitems+="<tr><td><a href='http://www.ikis.tsogu.ru/shedule/show_shedule.php?action=group&union=0&sid="+shed+"&gr="+data[i][0]+"&vr=1' class=grlink target='_blank'>"+data[i][1]+"-"+data[i][2]+"-("+data[i][3]+")-"+data[i][4]+"</a></td></tr>";
                    }
                }
                select.html($(kuda).html()+selectitems);
            }

        }});
}

function load_groups_union2(kuda,union){

    $.ajax({
        type: "POST",
        url: "shedule/funct.php",
        dataType: "json",
        data: ({ action:'load', act: 'list_groups_union', otd: union}),
        cache: false,
        async: false,
        success: function(data){
            var select=$(kuda);
            var selectitems="";
            if (data[0][0]!='e'){
                for (i=0;i<data.length;i++){
                    if (data[i][5]!='otd'){
                        selectitems+="<tr><td><a href='http://www.ikis.tsogu.ru/shedule/show_shedule.php?action=group&union=1&sid="+union+"&gr="+data[i][0]+"&vr=1' class=grlink target='_blank'>"+data[i][1]+"-"+data[i][2]+"-("+data[i][3]+")-"+data[i][4]+"</a></td></tr>";
                    } else {
                        selectitems+="<tr><td class=markotdel>"+data[i][6]+"</td></tr>";
                        selectitems+="<tr><td><a href='http://www.ikis.tsogu.ru/shedule/show_shedule.php?action=group&union=1&sid="+union+"&gr="+data[i][0]+"&vr=1' class=grlink target='_blank'>"+data[i][1]+"-"+data[i][2]+"-("+data[i][3]+")-"+data[i][4]+"</a></td></tr>";
                    }}
                select.html($(kuda).html()+selectitems);
            }

        }});
}

$(document).ready(function(){

    var a=getparams();
    if (a['id']=='33'){

        if ((a['action']!='group')&&(a['action']!='cab')&&(a['action']!='prep')&&(a['action']!='show_graph')&&(a['action']!='show_all_cabs')){
            load_info();

            var count=$("#win_shed").attr('count');
            if (count!=0){
                for (var i=0;i<count;i++){
                    var sh=$("#win_shed").attr('shedule'+i);
                    var ye=$("#win_shed").attr('year'+i);
                    var union=$("#win_shed").attr('union'+i);
                    if ((sh!='')&&(union!='')){

                        if (union==0){
                            load_groups("#groups",0,sh,ye,'list_groups');
                        } else {
                            load_groups_union("#groups",sh);
                        }


                    }

                }

            }


            load_prep('#preps',0,true);
            load_cabs('#cabs',0,true);

        } else {

            if (a['action']=='show_all_cabs'){
                $('#caball').val(a['date']);
            }
        }

    }

    if ((a['action']!='group')&&(a['action']!='cab')&&(a['action']!='prep')&&(a['action']!='show_graph')&&(a['action']!='show_all_cabs')){
        load_info2();
        var count=$("#shedule_on_main").attr('count');
        if (count!=0){
            for (var i=0;i<count;i++){
                var sh=$("#shedule_on_main").attr('shedule'+i);
                var union=$("#shedule_on_main").attr('union'+i);
                if ((sh!='')&&(union!='')){

                    if (union==0){
                        load_groups2("#t_selgroups",0,sh,'list_groups');
                    } else {
                        load_groups_union2("#t_selgroups",sh);
                    }


                }

            }

        }
    }

    if ((a['action']=='prep')||(a['action']=='cab')){

        $("#dt_ot").datepicker({
            numberOfMonths: 1,
            dateFormat: 'dd.mm.yy',
            firstDay: 1
        });

        $("#dt_do").datepicker({
            numberOfMonths: 1,
            dateFormat: 'dd.mm.yy',
            firstDay: 1
        });

    }

    $('#reldates').click(function(){

        location.href=location.href+"&dtot="+$("#dt_ot").val()+"&dtdo="+$("#dt_do").val();

    });


    $('#sh_sel').click(function(){

        $('#selgroups').slideToggle();

    });

    $('#close_selgroups').click(function(){
        $('#selgroups').slideToggle();

    });

    $('#cabal_show').click(function(){
        document.location.href="show_shedule.php?action=show_all_cabs&date="+$('#caball option:selected').text();
    });


    var dynamicLinkGroups = "EmptyGroup.html";
    var dynamicLinkPreps = "EmptyPrep.html";
    var dynamicLinkCabs = "EmptyCabs.html";


    $('#groups').change(function(){
        if ($(this).val()!=0){
            var gr=$('#groups').val();
            var bs=$('#groups option:selected').attr('bs');
            var union=$('#groups option:selected').attr('union');
            var sid=$('#groups option:selected').attr('sid');
            var y=$('#groups option:selected').attr('year');
            var varshed = $('#varshed').attr('checked')?1:0;
            document.getElementById('scheduleFrame').src="http://ikis.tsogu.ru/shedule/show_shedule.php?action=group&union="+union+"&sid="+sid+"&gr="+gr+"&year="+y+"&vr="+varshed;
            document.getElementById('scheduleFrame').contentDocument.location.reload();
        } else {

        }
    });

    $('#preps').change(function(){
        if ($(this).val()!=0){
            $('#groups').val(0);
            $('#cabs').val(0);
            $('#trgroups').hide();
            $('#trpreps').show();
            $('#trcabs').hide();
            document.getElementById('click_to_show').href="#scheduleModal";
        } else {
            $('#trpreps').hide();
            if ($('#groups').val()==0 && $('#cabs').val()==0){
                document.getElementById('click_to_show').href="#emptyModal";
            }
        }
    });

    $('#cabs').change(function(){
        if (($(this).val()!=0)&&($(this).val()!='all')){
            $('#groups').val(0);
            $('#preps').val(0);
            $('#trpreps').hide();
            $('#trgroups').hide();
            $('#trcabs').show();
            document.getElementById('click_to_show').href="#scheduleModal";
        } else {
            $('#trcabs').hide();
            if ($('#preps').val()==0 && $('#groups').val()==0){
                document.getElementById('click_to_show').href="#emptyModal";
            }
        }

    });






    function getSelectedText(elementId) {
        var elt = document.getElementById(elementId);

        if (elt.selectedIndex == -1)
            return null;

        return elt.options[elt.selectedIndex].text;
    }



    $('#click_to_show').click(function(){
        if ($('#cabs').val()!=0){
            if ($('#cabs').val()!='all'){
                var xid=$('#cabs').val();
                var count=$('#win_shed').attr('count');
                var str='';
                var varshed = $('#varshed').attr('checked')?1:0;
                for (var i=0;i<count;i++){
                    str=str+'&shed['+i+']='+$('#win_shed').attr('shedule'+i)+'&union['+i+']='+$('#win_shed').attr('union'+i)+'&year['+i+']='+$('#win_shed').attr('year'+i);
                }
                document.getElementById('scheduleFrame').src="http://ikis.tsogu.ru/shedule/show_shedule.php?action=cab&prep="+xid+"&vr="+varshed+"&count="+count+str;
                document.getElementById('scheduleFrame').contentDocument.location.reload();

                //alert(document.getElementById('scheduleFrame').src);
                //$(window.location).attr('href', "http://ikis.tsogu.ru/shedule/show_shedule.php?action=cab&prep="+xid+"&vr="+varshed+"&count="+count+str);
                return false;
            } else {
                var count=$('#win_shed').attr('count');
                var str='';
                for (var i=0;i<count;i++){
                    str=str+'&shed['+i+']='+$('#win_shed').attr('shedule'+i)+'&union['+i+']='+$('#win_shed').attr('union'+i)+'&year['+i+']='+$('#win_shed').attr('year'+i);
                }
                document.getElementById('scheduleFrame').src="http://ikis.tsogu.ru/shedule/show_shedule.php?action=show_all_cabs";
                document.getElementById('scheduleFrame').contentDocument.location.reload();

                //alert(document.getElementById('scheduleFrame').src);
                //$(window.location).attr('href', "http://ikis.tsogu.ru/shedule/show_shedule.php?action=show_all_cabs");
                return false;
            }
        }
        if ($('#preps').val()!=0){
            var xid=$('#preps').val();
            var count=$('#win_shed').attr('count');
            var str='';
            var varshed = $('#varshed').attr('checked')?1:0;
            for (var i=0;i<count;i++){
                str=str+'&shed['+i+']='+$('#win_shed').attr('shedule'+i)+'&union['+i+']='+$('#win_shed').attr('union'+i)+'&year['+i+']='+$('#win_shed').attr('year'+i);
            }
            document.getElementById('scheduleFrame').src="http://ikis.tsogu.ru/shedule/show_shedule.php?action=prep&prep="+xid+"&vr="+varshed+"&count="+count+str;
            document.getElementById('scheduleFrame').contentDocument.location.reload();

            //alert(document.getElementById('scheduleFrame').src);
            //$(window.location).attr('href', "http://ikis.tsogu.ru/shedule/show_shedule.php?action=prep&prep="+xid+"&vr="+varshed+"&count="+count+str);
            return false;

        }
        if ($('#groups').val()!=0){
            var gr=$('#groups').val();
            var bs=$('#groups option:selected').attr('bs');
            var union=$('#groups option:selected').attr('union');
            var sid=$('#groups option:selected').attr('sid');
            var y=$('#groups option:selected').attr('year');
            var varshed = $('#varshed').attr('checked')?1:0;
            document.getElementById('scheduleFrame').src="http://ikis.tsogu.ru/shedule/show_shedule.php?action=group&union="+union+"&sid="+sid+"&gr="+gr+"&year="+y+"&vr="+varshed;
            document.getElementById('scheduleFrame').contentDocument.location.reload();
            //$(window.location).attr('href', "http://ikis.tsogu.ru/shedule/show_shedule.php?action=group&union="+union+"&sid="+sid+"&gr="+gr+"&year="+y+"&vr="+varshed);
            return false;
        }


    });

    $(".para_gr").mousemove(function(){
        $(this).addClass("mark2");
    });

    $(".para_gr").mouseout(function(){
        $(this).removeClass("mark2");
    });
});