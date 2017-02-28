/*global
$, window */
$(function () {
    'use strict';
    $('.ui.checkbox').checkbox();
    $('.wikiuser').on('change', function () {
        $('input.id').remove();
        $('.wikiuser').parent('div').addClass('loading').find('.icon').removeClass('red green checkmark remove');
        $.getJSON('https://es.wikipedia.org/w/api.php?callback=?&action=query&format=json&list=users&usprop=registration%7Cgender&ususers=' + $('.wikiuser').val())
            .done(function (data) {
                try {
                    var $input = $('<input>').attr({
                            type: 'hidden',
                            name: 'person[wikiid]',
                            'class': 'id'
                        }),
                        userinfo = data.query.users[0];
                    if (userinfo.hasOwnProperty('missing')) {
                        window.alert('Nombre de usuario no registrado en Wikipedia!');
                        $('.wikiuser').parent('div').removeClass('loading').find('.icon').addClass('red remove');
                        return;
                    }
                    $input.val(userinfo.userid);
                    $('form').append($input);
                    $('.wikiuser').parent('div').removeClass('loading').find('.icon').addClass('green checkmark');
                } catch (e) {
                    window.alert('Error al recuperar el nombre de usuario de Wikipedia!');
                    $('.wikiuser').parent('div').removeClass('loading').find('.icon').addClass('red remove');
                    return;
                }
            });
    });
    $('form#inscription').on('submit.wiki', function (evt) {
        evt.preventDefault();
        if ($('input.id').length === 0) {
            window.alert('Por favor, chequea el nombre de usuario de Wikipedia');
            return false;
        }
        $('form#inscription').off('submit.wiki').submit();
    });
    $('form#inscription').on('submit', function () {
        $('form#inscription').triggerHandler('submit.wiki');
    });

    $('.message .close')
        .on('click', function () {
            $(this)
                .closest('.message')
                .transition('fade');
        });

    $('.participant').on('change', function () {
        $(this).val(+$(this).is(':checked'));
        var $element = $(this);
        $.post('/coordination/updateParticipants', $element);
    });

});
