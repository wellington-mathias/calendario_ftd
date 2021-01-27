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
    dispatch('POST', '/api/usuario/logout.php', '', checkLogin);
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
        var evt = $(this).parent().parent()[0].obj;
        var _this = $(this);
        confirmar('Tem certeza que deseja excluir este registro?', function () {
            _this.parent().parent().remove();
            dispatch('DELETE', '/api/' + page + '/delete.php', evt, pageListar);
        })
    })

    $('.bts .btEditar').off().on('click', function () {
        var evt = $(this).parent().parent()[0].obj;
        pageAdcionar(evt);
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

    $('select[name="tipo_usuario"]').off().on('change', function () {
        var val = $(this).val();
        $('form .campos').hide();
        $('form .tipo' + val).show();
    })
    $('form .campos').hide();
    $('form .tipo1').show();

}


function confirmar(txt,func) {
    $('body').append('<div class="msgExcluir"><div class="cont"><div class="txt">'+txt+'</div><div class="btSim">Sim</div><div class="btNao">Não</div></div> </div>');
    
    $('.msgExcluir .btSim').off().on('click',function(){
        $('.msgExcluir').remove();
        func();
    })
    $('.msgExcluir .btNao').off().on('click',function(){
        $('.msgExcluir').remove();
    })
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

    if ($(form).attr('action').match('usuario') != null) {

        data.tipo_usuario = {
            id: $(form).find('select[name="tipo_usuario"]').val(),
            descricao: null,
        }

        data.instituicao = {
            id: $('.formAdicionar input[name="id_instituicao"]').val(),
            uf: $('.selectUf').val(),
            nome: $('.formAdicionar input[name="nome_instituicao"]').val(),
            logo: 'x'
        };
        if (data.instituicao.nome == '') data.instituicao.nome = 'x';
        if (data.instituicao.uf == '') data.instituicao.uf = 'x';
        if (data.instituicao.logo == '') data.instituicao.logo = 'x';
        if (data.instituicao.id) {

            dispatch('POST', '/api/instituicao/update.php', data.instituicao, function (data) {
            });
        } else {

            data.instituicao = null;
            dispatch($(form).attr('method'), $(form).attr('action'), data, function (data) {
                listar();
                pageListar();
            });

        }



    } else {

        if ($(form).attr('action').match('evento') != null) {
            data.tipo_evento = {
                id: data.tipo_evento,
                descricao: ''
            }
            data = convertToSave(data);
        } else if ($(form).attr('action').match('calendario') != null) {

        }

        dispatch($(form).attr('method'), $(form).attr('action'), data, function (data) {
            listar();
            pageListar();
        });
    }

    return false;
}

function submitFormLogin(form) {

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

    dispatch("POST", '/api/usuario/login.php', data, function (data) {
        if (data.sucess) {
            setCookie('idLogin', '1', 1);
            window.location.href = "index.php";
        } else {

        }
    }, function (data) {
        $('.formLogin .error').html('Dados Invalidos').slideDown(150);
    });


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
                if (i.match(/dt_/) !== null) {
                    if (obj[i] && obj[i].match('-') !== null) {
                        $('.formAdicionar *[name="' + i + '"]').val(formatData1(obj[i]));
                    } else {
                        $('.formAdicionar *[name="' + i + '"]').val(obj[i]);
                    }
                } else {
                    $('.formAdicionar *[name="' + i + '"]').val(obj[i]);
                }
            }
        }

        $('.formAdicionar .selectDia option').prop({ selected: false });
        if (obj.dia_letivo) {
            $('.formAdicionar .selectDia option:first').prop({ selected: true });
        } else {
            $('.formAdicionar .selectDia option:last').prop({ selected: true });
        }

        $('.formAdicionar').attr('method', 'POST');
        $('.formAdicionar').attr('action', '/api/' + page + '/update.php');

        if (obj.tipo_evento) $('select[name="tipo_evento"] option[value="' + obj.tipo_evento.id + '"]').attr('selected', 'selected');
        if (obj.tipo_usuario) $('select[name="tipo_usuario"] option[value="' + obj.tipo_usuario.id + '"]').attr('selected', 'selected');

        $('form .campos').hide();
        $('form .tipo' + obj.tipo_usuario.id).show();

        console.log(obj);

        if (page == 'usuario') {
            $('.formAdicionar .btEnviar ').hide();
            $('.formAdicionar input ').prop({ readonly: true });
            $('.formAdicionar select').prop({ disabled: true });
            if (obj.instituicao && obj.instituicao.id) {
                $('.formAdicionar input[name="id_instituicao"]').val(obj.instituicao.id),
                    $('.formAdicionar input[name="nome_instituicao"]').val(obj.instituicao.nome);
                $('.selectUf option[value="' + obj.instituicao.uf + '"]').prop({ selected: true });
            }
        }
        if (page == 'calendario') {
            $('.formAdicionar .btEnviar ').hide();
            $('.formAdicionar input ').prop({ readonly: true });
            $('.formAdicionar select').prop({ disabled: true });
            $('.formAdicionar input[name="nome_instituicao"]').val(obj.usuario.instituicao.nome);
            $('.formAdicionar input[name="email_professor"]').val(obj.usuario.email);
        }
        if (page == 'evento') {
            if ($('.btEditar:first').html() == 'Visualizar') {
                $('.formAdicionar .btEnviar ').hide();
                $('.formAdicionar input ').prop({ readonly: true });
                $('.formAdicionar select').prop({ disabled: true });
            } else {
                $('.formAdicionar .btEnviar').show();
                $('.formAdicionar input ').prop({ readonly: false });
                $('.formAdicionar select').prop({ disabled: false });
            }

        }
    } else {

        $('.formAdicionar .btEnviar ').show();
        $('.formAdicionar input ').prop({ readonly: false });
        $('.formAdicionar select').prop({ disabled: false });
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

