/*global
$, window */
$(function () {
    'use strict';
    $('.ui.checkbox').checkbox();
    $('.wikiuser').on('change', function () {
        $('input.id').remove();
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
                        return;
                    }
                    $input.val(userinfo.userid);
                    $('form').append($input);
                } catch (e) {
                    window.alert('Error al recuperar el nombre de usuario de Wikipedia!');
                    return;
                }
            });
    });
    $('form').on('submit.wiki', function (evt) {
        evt.preventDefault();
        if ($('input.id').length === 0) {
            window.alert('Por favor, chequea el nombre de usuario de Wikipedia');
            return false;
        }
        $('form').off('submit.wiki').submit();
    });
    $('form').on('submit', function () {
        $('form').triggerHandler('submit.wiki');
    });


});
