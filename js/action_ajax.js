var baseUrl = '/ftd/calendario/html';
var baseUrl = '';
function dispatch(method, url, data, callback,error) {
    $.ajax({
        method: method,
        type: method,
        url: baseUrl + url,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(data),
        async: false,
        beforeSend: function () {
        },
    }).done(function (data) {
        if (callback) callback(data);
        return data;
    }).fail(function (jqXHR, textStatus, msg) {
        //console.log(textStatus, msg);
        if (error) {
            error(msg);
        } else {
            callback(msg);
        }
        return [jqXHR, textStatus, msg];
    });
}

function convertToSave(data) {
    var d1 = formatData2(data.dt_inicio);
    var d2 = formatData2(data.dt_fim);
    var obj = {
        titulo: (data.titulo ? data.titulo : null),
        dt_inicio: (d1 == '' ?  '0000-00-00' : d1 ),
        dt_fim: (d2 == '' ?  '0000-00-00' : d2 ),
        descricao: (data.descricao ? data.descricao : null),
        tipo_evento: {
            id: (data.tipo_evento.id ? data.tipo_evento.id : null),
            descricao: (data.tipo_evento.descricao ? data.tipo_evento.descricao : null),
        },
        id: (data.id ? data.id : null),
        uf: (data.uf ? data.uf : null),
        dia_letivo: (data.dia_letivo ? data.dia_letivo : null),
    }
    
    return obj;
}

function convertFromDB(data) {
    var d1 = formatData1(data.dt_inicio);
    var d2 = formatData1(data.dt_fim);
    var obj = {
        titulo: (data.titulo ? data.titulo : null),
        dt_inicio: d1,
        dt_fim: d2,
        descricao: (data.descricao ? data.descricao : null),
        tipo_evento: {
            id: (data.tipo_evento.id ? data.tipo_evento.id : null),
            descricao: (data.tipo_evento.descricao ? data.tipo_evento.descricao : null),
        },
        id: (data.id ? data.id : null),
        uf: (data.uf ? data.uf : null),
        dia_letivo: (data.dia_letivo ? data.dia_letivo : null),
    }
    
    return obj;
}

function formatData1(data){
    var d = data.split('-');
    if(d.length == 3){
        var r = d[2] + '/' + d[1] + '/' + d[0];
    } else {
        var r = '';
    }

    return r;

}
function formatData2(data){
    var d = data.split('/');
    if(d.length == 3){
        var r = d[2] + '-' + d[1] + '-' + d[0];
    } else {
        var r = '';
    }

    return r;

}