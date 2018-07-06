const background="url(\"data:image/svg+xml;utf8," +
    "<svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='30px' width='WIDTHpx'>" +
    "<text x='1' y='20' fill='rgb(150,150,150)' font-size='20'>COUNT</text>" +
    "</svg>\")";

const NO_NAME_CASE = 'brak nazwy przypadku';

var html_width=0;

const clean=function(cb){
    $('.results').fadeOut(1000,function () {
        $('.results').html('').show();
        if (cb) cb();
    });

    $('table.repertory').fadeOut(1000,function () {
        $('table.repertory').html('').show();
        html_width=$('html').width();
    });

    mvqInput2header();
}

const getCases = function (cb) {
    $.getJSON('case?limit=5',function(cases){
        $('.header .navi').html('<div class="more">&hellip;</div>');

        for (var i=cases.data.length-1;i>=0; i--) {
            var a=cases.data[i].active?' class="active"':'';
            $('<div case="'+cases.data[i].id+'" class="case" title="'+(cases.data[i].name||NO_NAME_CASE)+'"><div'+a+'>'+cases.data[i].short+'</div><label>'+cases.data[i].rubrics+'</label></div>').appendTo($('.header .navi'));
        }
        if (cb) cb();
    });
}

const pushHistory = function(qs) {
    var url=window.location.href;
    var question=url.indexOf('?');
    if (question>0) url=url.substr(0,question);
    window.history.pushState({},"", url+'?'+qs);
}

const mvqInput2header = function(q) {
    if (!q) q=$('#q');
    if (q.closest('.header').length==0) {
        q.appendTo($('.header .query'));
        setTimeout(function(){
            $('.query input').focus();
        },100);

    }
}

var status='waiting';
var lastQ='';
const getSearchResults = function(e,q) {
    if(!q) q=$(this);

    if(q.val().length<3) return;

    if (
        status=='getting' && typeof e=='object'
        ||
        status=='pending' && typeof e=='boolean'
    ) {
        setTimeout(getSearchResults,100,true,q);
        status='pending';
        return;
    }

    if (status!='waiting') return;

    pushHistory('q='+q.val());

    if(lastQ==q.val()) {
        status='waiting';
        return;
    }
    status='getting';
    clean(function(){
        $.getJSON('./rubric?q='+q.val(),function(data){

            var count='0';
            if (data.data && data.data.total) {
                count=data.data.total+'';
            }
            q.css('background-image',background.replace('COUNT',count).replace('WIDTH',15*count.length));

            $('.results').html('');
            if (data.data && data.data.results) {

                mvqInput2header(q);

                var i=0;

                var append = function () {
                    if (i>=data.data.results.length) {
                        status='waiting';
                        return;
                    }
                    var evenodd=(i%2==1)?'even':'odd';
                    var rec=data.data.results[i++];

                    $('<div id="'+rec.id32+'" class="blank row '+evenodd+'"><div class="rubric col-md-10"><div class="pl">'+rec.pl+'</div><div class="en">'+rec.en+'</div></div><div class="rc col-md-2 hidden-xs">'+rec.rc+'</div></div>').appendTo('.results').fadeIn(100,append);

                }
                append();

            } else {
                status='waiting';
            }


        })
    });
}

const addRubricToCase = function(e) {
    const total_animation_time = 5000;
    const start=Date.now();
    const element = $(this).closest('.row');
    const id=element.attr('id');
    const body = $('html, body');

    var destination={width:1, height: element.height()*1.5, top: '3em', right: '2em','border-radius':'50%'};
    element.removeClass('row').addClass('animate').animate(destination,total_animation_time);
    element.find('*').fadeOut(total_animation_time);
    var i=0;
    $('.results .row').each(function(){
        const evenodd = (i++)%2==0?'odd':'even';
        $(this).removeClass('odd').removeClass('even').addClass(evenodd);
    });
    body.animate({scrollTop:0}, total_animation_time);
    $.post('case',{rubric:id},function(data){
        getCases(function(){
            const time_left=total_animation_time-(Date.now()-start);
            element.stop();
            body.stop();
            const active = $('.header .navi .active');
            const w2=$('.header .navi .active').width()/2;
            element.css({left:element.offset().left, right:''});

            body.animate({scrollTop:0}, time_left>0?time_left:10);
            delete destination.right;
            destination.top = Math.round(active.offset().top+w2);
            destination.left = Math.round(active.offset().left+w2);
            destination.height = 1;

            element.animate(destination,time_left>0?time_left:10,function () {
                element.hide();
                active.css('background-color','rgba(255,255,0,0.5)');
                const step=0.01;

                var fade=function(){
                    var color=active.css('background-color').split(',');
                    var c3 = parseFloat(color[3]) - step;
                    if (c3<0) c3=0;
                    color[3]=c3+')';
                    active.css('background-color',color.join(','));

                    if (c3>0) setTimeout(fade,1/step);
                }
                setTimeout(fade,500);
            });

        });

    });
}

var lastCaseId = 0;

const displayCase = function(e,caseId) {
    clean();

    if (!caseId && this && $(this).length && $(this).length>0) {
        caseId=$(this).attr('case');
        if ($(this).find('.active').length==1 && $('table.repertory').html().length>0) {
            const active=$(this).find('.active');
            $.post('case/active',function(){
                active.removeClass('active');
            });
            return;
        }
    }

    if (caseId) lastCaseId=caseId;
    else caseId=lastCaseId;

    $.getJSON('case/repertorize/'+caseId,function(rep){
        pushHistory('c='+caseId);
        getCases();
        const data=rep.data;

        var tr='<tr><th colspan="2"><input id="casename" case="'+data.case.id+'" placeholder="Nazwa przypadku" value="'+(data.case.name?data.case.name:'')+'"/></th>';
        for(var i=0; i<data.remedies.length; i++) {
            tr+='<th><div><span>'+data.remedies[i].name+'</span></div></th>';
        }
        tr+='</tr>';
        $('table.repertory').append(tr);

        tr='<tr><td colspan="2"></td>';
        for(var i=0; i<data.remedies.length; i++) {
            tr+='<td class="score">'+data.remedies[i].score+'</td>';
        }
        tr+='</tr>';
        $('table.repertory').append(tr);


        for (var i=0;i<data.rubrics.length; i++) {
            tr='<tr class="data-row">';
            var a='<a data-toggle="modal" data-cb="displayCase" data-target="#confirm-delete" data-header="Rubryka" data-href="deleteRubric" data-id="'+caseId+','+data.rubrics[i].rubric+'" href="" class="x">x</a>';

            tr+='<td>'+a+'<div class="pl confirm-text" title="'+data.rubrics[i].en+'">'+data.rubrics[i].pl+'</div></td>';
            tr+='<td class="weight">'+data.rubrics[i].weight+'</td>';

            for (var j=0; j<data.rubrics[i].remedies.length; j++) {
                tr+='<td class="score'+data.rubrics[i].remedies[j]+'">'+'</td>';
            }
            tr+='</tr>';
            $('table.repertory').append(tr);

        }

        $('div.repertory').width(html_width-20);

    });
}

const changeCaseName = function(e) {
    if (e.type=='keyup' && e.keyCode!=13) return;
    $.post('case/name/'+$(this).attr('case'),{name:$(this).val()});
}

const displayAllCases = function (e) {
    clean(function () {
        $.getJSON('case',function(cases){

            for (var i=0;i<cases.data.length; i++) {
                var eo=(i%2)==0?'odd':'even';
                var html='<div case="'+cases.data[i].id+'" class="data-row row '+eo+'">';
                html+='<div class="case confirm-text col-md-10">'+(cases.data[i].name||NO_NAME_CASE)+'</div>';
                html+='<div class="rubrics col-md-1">'+cases.data[i].rubrics+'</div>';
                html+='<div title="usuń" class="x col-md-1">';
                html+='<a data-toggle="modal" data-cb="refreshCases" data-target="#confirm-delete" data-header="Przypadek" data-href="deleteCase" data-id="'+cases.data[i].id+'" href="">x</a>';
                html+='</div></div>';
                $(html).appendTo($('.results'));
            }

        });
    });

}

const refreshCases = function(){
    displayAllCases();
    getCases();
}

const deleteCase = function(caseId,cb) {
    $.ajax({
        url: 'case/'+caseId,
        type: 'DELETE',
        success: function(){
            $('#confirm-delete').fadeOut(cb);
        }
    });
}

const deleteRubric = function (caseId, rubricId, cb) {
    $.ajax({
        url: 'case/rubric/'+caseId+','+rubricId,
        type: 'DELETE',
        success: function(){
            $('#confirm-delete').fadeOut(cb);
        }
    });
}

$(function(){
    document.getElementById('q').focus();
    getCases();

    $('#q').keyup(getSearchResults);
    $('#q').click(getSearchResults);



    var url = new URL(window.location.href);
    var q = url.searchParams.get("q");
    var c = url.searchParams.get("c");
    if(q) {
        $('#q').val(q);
        $('#q').trigger('keyup');
    }

    if (c) displayCase(null,c);


    $('#confirm-delete').on('show.bs.modal', function(e) {
        var self=$(this);
        self.find('.modal-header').text($(e.relatedTarget).attr('data-header'));
        self.find('.modal-body').text($(e.relatedTarget).closest('.data-row').find('.confirm-text').text());
        self.find('.btn-ok').attr('href', 'javascript:'+$(e.relatedTarget).data('href')+'('+$(e.relatedTarget).data('id')+','+$(e.relatedTarget).data('cb')+')');
    });

});


$(document).on('click', '.results .rubric div', addRubricToCase);
$(document).on('click', '.header .navi .case', displayCase);
$(document).on('change', '#casename', changeCaseName);
$(document).on('keyup', '#casename', changeCaseName);
$(document).on('click', '.header .navi .more', displayAllCases);