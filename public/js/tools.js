function ajaxTest() {
    var url = "run";
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'html',
        success: function (data)
        {
            if (data.length === 1)
            {
                $('#reponseAjax').text('00' + data);
            } else if (data.length === 2)
            {
                $('#reponseAjax').text('0' + data);
            } else {
                $('#reponseAjax').text(data);
            }
        },
        error: function (resultat, statut, erreur)
        {
            console.log(erreur);
        }
    });
}


function readText() {
    var url = "readText";
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'html',
        success: function (data)
        {
            $('#reponseTextAjax').text(data);
        },
        error: function (resultat, statut, erreur)
        {
            console.log(erreur);
        }
    });
}

