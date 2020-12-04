function checkLogin() {
    var cookie = getCookie('idLogin');
    if (!cookie) {
        if (window.location.pathname.match('login') == null) {
            window.location.href = "login.php";
        }
    }
}
checkLogin();

function logOut() {
    eraseCookie('idLogin');
    checkLogin();
}

var UFs = ['AC - Acre ', 'AL - Alagoas ', 'AP - Amapá ', 'AM - Amazonas ', 'BA - Bahia ', 'CE - Ceará ', 'DF - Distrito Federal ', 'ES - Espírito Santo ', 'GO - Goiás ', 'MA - Maranhão ', 'MT - Mato Grosso ', 'MS - Mato Grosso do Sul ', 'MG - Minas Gerais ', 'PA - Pará ', 'PB - Paraíba ', 'PR - Paraná ', 'PE - Pernambuco ', 'PI - Piauí ', 'RJ - Rio de Janeiro ', 'RN - Rio Grande do Norte ', 'RS - Rio Grande do Sul ', 'RO - Rondônia ', 'RR - Roraima ', 'SC - Santa Catarina ', 'SP - São Paulo ', 'SE - Sergipe ', 'TO - Tocantins']


$(document).ready(function () {

    creatEvents();

    selectUfs();
})

function creatEvents() {

    $('.btAdicionar').off().on('click', function () {
        pageAdcionar();
    })

    $('.btVoltarEditar').off().on('click', function () {
        pageListar();
    })

    $('.formAdicionar').off().on('submit', function (e) {
        submitForm(this);
        e.preventDefault();
        return false;
    })
    $('.formLogin').off().on('submit', function (e) {
        submitFormLogin(this);
        e.preventDefault();
        return false;
    })

    $('.bts .btExcluir').off().on('click', function () {
        var evt = $(this).parent().parent()[0].evento;
        $(this).parent().parent().remove();
        dispatch('DELETE', '/api/' + page + '/delete.php', evt, pageListar);
    })

    $('.bts .btEditar').off().on('click', function () {
        if($(this).parent().parent()[0].evento){
            var evt = $(this).parent().parent()[0].evento;
            pageAdcionar(convertFromDB(evt));
        } else if($(this).parent().parent()[0].usuario){
            var evt = $(this).parent().parent()[0].usuario;
            pageAdcionar(evt);
        }
    })

    $('.menu .btSair').off().on('click', function () {
        logOut();
    })

    $('input').off().on('focus click', function () {
        $(this).removeClass('erro');
    })
    $('.inputDate').on('input keydown keyup mousedown mouseup select contextmenu drop', function (e) {
        var replace = $(this).val().replace(/[^0-9\/]/g, '');
        $(this).val(replace);
        var val = $(this).val();

        if (val.length == 3 && val.match('/') == null) {
            val = val.substr(0, 2) + '/' + val.substr(2, 1);
        }
        if (val.length == 6 && val.match(/\//g) != null && val.match(/\//g).length == 1) {
            val = val.substr(0, 5) + '/' + val.substr(5, 1);
        }

        $(this).val(val);
    });
}


function submitForm(form) {
    if ($('.inputDate[name="dt_fim"]').val() == '') {
        $('.inputDate[name="dt_fim"]').val($('.inputDate[name="dt_inicio"]').val())
    }
    $(form).find('.obgt').each(function () {
        if ($(this).val() == '') {
            $(this).addClass('erro');
        }
    })


    $(form).find('.inputDate').each(function () {
        if (!$(this).val().match(/[0-9]?[0-9]\/[0-1]?[0-9]\/20[0-9]{2}/i)) {
            $(this).addClass('erro');
        }
    });

    if ($(form).find('.erro').length > 0) {
        return false;
    }

    var data = getFormData($(form));
    if ($(form).attr('action').match('evento') != null) {
        data.tipo_evento = {
            id: data.tipo_evento,
            descricao: ''
        }
        data = convertToSave(data);
    } else if ($(form).attr('action').match('usuario') != null) {
        data.tipo_usuario = {
            id: data.tipo_usuario,
            descricao: ''
        }
        console.log(data.tipo_usuario);
    }
    dispatch($(form).attr('method'), $(form).attr('action'), data, function (data) {
        listar();
        pageListar();
    });


    return false;
}

function submitFormLogin(form) {
    if ($('.inputDate[name="dt_fim"]').val() == '') {
        $('.inputDate[name="dt_fim"]').val($('.inputDate[name="dt_inicio"]').val())
    }
    $(form).find('.obgt').each(function () {
        if ($(this).val() == '') {
            $(this).addClass('erro');
        }
    })


    if ($(form).find('.erro').length > 0) {
        return false;
    }

    var data = getFormData($(form));

    //console.log( $(form).attr('method') , $(form).attr('action') , convertToSave(data));
    //dispatch("GET", $(form).attr('action'), convertToSave(data), function (data) {
    setCookie('idLogin', '1', 1);
    window.location.href = "index.php";
    //});


    return false;
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
function eraseCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}


function selectUfs() {
    $('.selectUf option').remove();
    $('.selectUf').append('<option value="" ></option>');
    for (var i in UFs) {
        $('.selectUf').append('<option value="' + (UFs[i].split(' - ')[0]) + '" >' + UFs[i] + '</option>');
    }
}



function pageListar() {
    $('.formAdicionar input , .formAdicionar textarea').each(function () {
        if ($(this).attr('name') != 'tipo_evento') $(this).val('');
    })
    $('.cont .listar').slideDown(300);
    $('.cont .adicionar').slideUp(300);
}

function pageAdcionar(obj) {
    if (obj) {
        for (var i in obj) {
            if (i != 'tipo_evento' && i != 'tipo_usuario') {
                $('.formAdicionar *[name="' + i + '"]').val(obj[i]);
            }
        }
        $('.formAdicionar input[name="dia_letivo"]').removeAttr('checked');
        if (obj.dia_letivo) {
            $('.formAdicionar .diaL1').attr('checked', 'checked');
        } else {
            $('.formAdicionar .diaL0').attr('checked', 'checked');
        }

        $('.formAdicionar').attr('method', 'POST');
        $('.formAdicionar').attr('action', '/api/' + page + '/update.php');
        
        if(obj.tipo_evento) $('select[name="tipo_evento"] option[value="' + obj.tipo_evento.id + '"]').attr('selected', 'selected');
        if(obj.tipo_usuario) $('select[name="tipo_usuario"] option[value="' + obj.tipo_usuario.id + '"]').attr('selected', 'selected');
    } else {
        $('.formAdicionar input[name="dia_letivo"]').removeAttr('checked');
        $('.formAdicionar .diaL1').attr('checked', 'checked');
        $('.formAdicionar').attr('method', 'PUT');
        $('.formAdicionar').attr('action', '/api/' + page + '/create.php');
        $('select[name="tipo_evento"] option:first').attr('selected', 'selected');
        $('select[name="tipo_usuario"] option:first').attr('selected', 'selected');
    }
    $('.cont .listar').slideUp(300);
    $('.cont .adicionar').slideDown(300);
}

function getFormData(form) {
    var unindexed_array = form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

