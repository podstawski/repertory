$(function(){
    document.getElementById('q').focus();


    var status='waiting';

    const background="url(\"data:image/svg+xml;utf8," +
        "<svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='30px' width='WIDTHpx'>" +
        "<text x='1' y='20' fill='rgb(150,150,150)' font-size='20'>COUNT</text>" +
        "</svg>\")";

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
    $.post('case',{rubric:$(this).closest('.row').attr('id')},function(data){
        console.log(data);
    });
});
