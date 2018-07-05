const background="url(\"data:image/svg+xml;utf8," +
    "<svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='30px' width='WIDTHpx'>" +
    "<text x='1' y='20' fill='rgb(150,150,150)' font-size='20'>COUNT</text>" +
    "</svg>\")";


const getCases = function (cb) {
    $.getJSON('case?limit=4',function(cases){
        $('.header .navi').html('');

        for (var i=cases.data.length-1;i>=0; i--) {
            var a=cases.data[i].active?' class="active"':'';
            $('<div case="'+cases.data[i].id+'" class="case"><div'+a+'>'+cases.data[i].short+'</div><label>'+cases.data[i].rubrics+'</label></div>').appendTo($('.header .navi'));
        }
        if (cb) cb();
    })
}


$(function(){
    document.getElementById('q').focus();
    getCases();


    var status='waiting';
    getData = function(e,q) {
        if(!q) q=$(this);

        if(q.val().length<3) return;

        if (
            status=='getting' && typeof e=='object'
            ||
            status=='pending' && typeof e=='boolean'
        ) {
            setTimeout(getData,100,true,q);
            status='pending';
            return;
        }

        if (status!='waiting') return;

        var url=window.location.href;
        var question=url.indexOf('?');
        if (question>0) url=url.substr(0,question);
        window.history.pushState({},"", url+'?q='+q.val());

        status='getting';
        $.getJSON('./rubric?q='+q.val(),function(data){


            var count='0';
            if (data.data && data.data.total) {
                count=data.data.total+'';
            }
            q.css('background-image',background.replace('COUNT',count).replace('WIDTH',15*count.length));

            $('.results').html('');
            if (data.data && data.data.results) {

                if (q.closest('.header').length==0) {
                    q.appendTo($('.header .query'));
                    setTimeout(function(){
                        $('.query input').focus();
                    },1000);

                }

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
    }

    $('#q').keyup(getData);
    $('#q').change(getData);






    var url = new URL(window.location.href);
    var q = url.searchParams.get("q");

    if(q) {
        $('#q').val(q);
        $('#q').trigger('keyup');
    }



});


$(document).on('click', '.results .rubric div', function(e) {
    const total_animation_time = 5000;
    const start=Date.now();
    const element = $(this).closest('.row');
    const id=element.attr('id');
    const body = $('html, body');
    var destination={width:1, height: '10em', top: '3em', right: '2em','border-radius':'50%'};
    element.removeClass('row').addClass('animate').animate(destination,total_animation_time);
    element.find('*').fadeOut(total_animation_time);
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
                setTimeout(fade,1000);
            });

        });

    });
});
