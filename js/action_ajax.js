function dispatch(method, url, data, callback) {
    $.ajax({
        method: method,
        type: method,
        //url: '/ftd/calendario/html'+url,
        url: url,
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
        console.log(textStatus, msg);
        return [jqXHR, textStatus, msg];
    });
}

function convertToSave(data) {
    var d1 = data.dt_inicio.split('/');
    var d2 = data.dt_fim.split('/');
    var obj = {
        titulo: (data.titulo ? data.titulo : null),
        dt_inicio: (d1.length == 3 ? d1[2] + '-' + d1[1] + '-' + d1[0] : '0000-00-00'),
        dt_fim: (d2.length == 3 ? d2[2] + '-' + d2[1] + '-' + d2[0] : '0000-00-00'),
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
    var d1 = data.dt_inicio.split('-');
    var d2 = data.dt_fim.split('-');
    var obj = {
        titulo: (data.titulo ? data.titulo : null),
        dt_inicio: (d1.length == 3 ? d1[2] + '/' + d1[1] + '/' + d1[0] : ''),
        dt_fim: (d2.length == 3 ? d2[2] + '/' + d2[1] + '/' + d2[0] : ''),
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