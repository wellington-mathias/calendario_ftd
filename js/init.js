var prod = false;
var scale = 1;

var inicioAno = fimAno = inicioRecesso = fimRecesso = {};
var uf = null;
var arDias = [];
var arDias2 = [];
var feriados = [];
var evtsIni = [];
var evtsFTD = [];
var simulados = [];
var volumes = [];
var evtsProf = [];
var instituicao = {};
var logoInstituicao = null;
var userFTD = null;
var novaInst = false;

var dataEventos = [];
var nomeMeses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
var nomeDias = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];

var dtHj = new Date();
var anoAtual = dtHj.getFullYear();

var qtdCapitulos = 0;
var dataUsuario = false;
var idProf = false;
var objCalendario = {
	ano_referencia: anoAtual,
};

var params = new URLSearchParams(window.location.search);

$(document).ready(function () {
	$('.txtAno').html(anoAtual);


	$('.page').hide();
	$('html').addClass('print2');
	$('#loading').removeClass('hide');
	$('#loading .sprite').html('Carregando...');

	logOut();
	validaLoginFtd();

});

function ajaxLogin(method, url, contentType, data, callback) {
	if(window.env && window.env.IONICA_BASE_URL){
		var baseUrl = window.env.IONICA_BASE_URL;
	} else {
		var baseUrl = 'https://souionica.tk/api';
	}
	var head = '';
	if (data.auth) {
		head = {
			"Authorization": data.auth
		}
	}
	$.ajax({
		method: method,
		type: method,
		url: baseUrl + url,
		contentType: contentType,
		headers: head,
		data: JSON.stringify(data),
		dataType: 'json',
		async: false
	}).done(function (res) {
		if (callback) callback(res);
		//return data;
	}).fail(function (jqXHR, textStatus, msg) {
		//console.log(jqXHR, textStatus, msg)
		if (callback) callback();
		//return [jqXHR, textStatus, msg];
	});
}

function validaLoginFtd() {
	var tokenIni = params.get('token');
	var short_token = 'gzKuqV';
	var new_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJndWlkIjoiYzcwMjU5MDEtNWE3Ny0xMWViLWI2NGYtZTczYmRmOTk0N2MzIiwidXNlcm5hbWUiOiJzY2hvb2wuYWRtaW4ueGFkcmV6IiwiZW1haWwiOiIiLCJyb2xlX2d1aWQiOiJSMDMiLCJuYW1lIjoiRGlyZXRvciIsImxhc3RuYW1lIjoiWGFkcmV6IiwiYXZhdGFyIjoiIiwiZXhwIjoxNjE0MzAyOTc2fQ.q-7QUtzvT-_6CrIvdOYTF1FVsIGa5K6JRf041aAu94s';

	var testLogin = {
		"email": "school.admin.xadrez",
		"password": "123"
	}
	var testLogin = {
		"email": "teacher.xadrez",
		"password": "123"
	}
	var testLogin = {
		"email": "student.xadrez",
		"password": "123"
	}

	if (prod || tokenIni == "TESTECPC") {
		if (params.get('token') != null) {

			ajaxLogin('POST', '/login', 'application/json', testLogin, function (data) {
				//console.log(data)
				if (data.status && data.status == 'success') {
					tokenIni = data.data.token;

					var body = { auth: tokenIni };
					ajaxLogin('GET', '/users/access-token', 'application/x-www-form-urlencoded', body, function (data) {
						//console.log(data);
						if (data.status && data.status == 'success') {
							short_token = data.data.token;

							var body = {
								"token": short_token,
								"identity_provider": "i2c"
							}
							ajaxLogin('POST', '/login-oauth', 'application/json; charset=utf-8', body, function (data) {
								//console.log(data);
								if (data.status == 'success') {
									new_token = data.data.token;

									var body = { auth: new_token };
									checkUserExist(body);
								} else {
									falhaLogin();
								}
							})
						} else {
							falhaLogin();
						}
					})
				} else {
					falhaLogin();
				}
			});

		} else {
			telaLogin();
		}

	} else {
		if (params.get('token') != null) {

			var body = {
				"token": tokenIni,
				"identity_provider": "i2c"
			}
			ajaxLogin('POST', '/login-oauth', 'application/json; charset=utf-8', body, function (data) {
				//console.log(data);
				if (data.status == 'success') {
					new_token = data.data.token;

					var body = { auth: new_token };
					checkUserExist(body);
				} else {
					falhaLogin();
				}
			})
		} else {
			falhaLogin();
		}
	}

}

function falhaLogin() {
	$('#loading').fadeOut();
	$('.falhaLogin').fadeIn();
}


function telaLogin() {
	$('#telaLogin').fadeIn(300);
	$('.formLoginProf input').off().on('focus', function (evt) {
		$(this).removeClass('erro');
	});

	$('.formLoginProf').off().on('submit', function (evt) {
		submitFormLogin(this);
		evt.preventDefault();
		return false;
	})
	//.trigger('submit')


	$('#stage').removeClass('print');
	$('#loading').addClass('hide');
	$('html').removeClass('print2');
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
	data.ambiente = 'SITE';
	console.log(data);
	dispatch('POST', '/api/usuario/login.php', data, function (data) {
		if (data.sucess && data.usuario) {
			dataUsuario = data.usuario;
			idProf = dataUsuario.id;
			objCalendario.usuario = {
				id: dataUsuario.id
			};
			logado();
			abertura();
			checkInstituicao();
			$('.bemvindo .nameUsuario').html($('.formLoginProf #login_professor').val());
			$('.formLoginProf .error').html('').hide();
		} else {
			$('.formLoginProf .error').html('Erro ao conectar').slideDown(150);
		}
	}, function (data) {
		$('.formLoginProf .error').html('Dados Invalidos').slideDown(150);
	});


}

function checkUserExist(body) {
	ajaxLogin('GET', '/users/whoami', 'application/json; charset=utf-8', body, function (data) {
		//console.log(data);
		if (data.status == 'success') {
			userFTD = data.data;
			console.log(userFTD);
			login();
		} else {
			falhaLogin();
		}
	})
}


function logado() {
	$('.page').hide();
	$('#loading').addClass('hide');
	$('#stage').fadeIn(300);


	$('#dadosGestor .btVoltar').off().on('click', function () {
		$('.page').hide();
		abertura();
	})
	$('#configCalendario .btVoltar').off().on('click', function () {
		$('.page').hide();
		page2();
	})


};



function login() {

	if (userFTD && userFTD.schools.length > 0) {
		var pass = userFTD.schools[0].name;
		var inst = {
			nome: '',
			uf: '',
			logo: ''
		};
	} else {
		var pass = 'PASSFTD';
		var inst = null;

	}

	var data = {
		usuario: userFTD.username,
		senha: pass,
		instituicao: inst,
		tipo_usuario: {
			id: 2,
			descricao: null,
		}
	}
	data.ambiente = 'SITE';

	console.log(data);
	dispatch('POST', '/api/usuario/login.php', data, function (data) {
		console.log(data);
		if (data.sucess && data.usuario) {
			loginCompleto(data.usuario);
		} else {
			novoUsuario();
		}
	});


}
function loginCompleto(data) {
	dataUsuario = data;
	idProf = dataUsuario.id;
	objCalendario.usuario = {
		id: dataUsuario.id
	};
	logado()
	abertura();
	checkInstituicao();
	$('.bemvindo .nameUsuario').html($('.formLoginProf #login_professor').val());
	$('.formLoginProf .error').html('').hide();


	$('#stage').removeClass('print');
	$('#loading').addClass('hide');
	$('html').removeClass('print2');
}

function novoUsuario() {
	if (userFTD && userFTD.schools.length > 0) {
		var pass = userFTD.schools[0].name;
		var inst = null;
	} else {
		var pass = 'PASSFTD';
		var inst = null;

	}
	novaInst = true;

	var data = {
		id: '',
		nome: userFTD.name + ' ' + userFTD.lastname,
		login_ftd: userFTD.username,
		password_ftd: pass,
		login: null,
		password: null,
		email: '',
		id_instituicao: null,
		instituicao: inst,
		tipo_usuario: {
			id: 2,
			descricao: null,
		},
		uf: '',
	}


	console.log(data);
	dispatch('PUT', '/api/usuario/create.php', data, function (data) {
		if (data.id) {

			var login = {
				usuario: userFTD.username,
				senha: pass,
				instituicao: inst,
				ambiente: 'SITE',
				tipo_usuario: {
					id: 2,
					descricao: null,
				},
			}
			dispatch('POST', '/api/usuario/login.php', login, function (data) {
				if (data.sucess && data.usuario) {
					loginCompleto(data.usuario);
				}
			});
		}
	});

}

function checkInstituicao() {
	if (!dataUsuario.instituicao || !dataUsuario.instituicao.id) {

		/* formData = new FormData();
		formData.append('nome',  '' );
		formData.append('uf',  '' );
		formData.append('logo',  '' );
	
		var request = new XMLHttpRequest();
		request.open("POST", "/api/instituicao/create.php");
		request.send(formData); */
		dispatch('POST', '/api/instituicao/create.php', { nome: '', if: '', logo: '' }, function (data) {
			instituicao = { id: data.id };
			dataUsuario.instituicao = instituicao;
		});
	} else {
		instituicao = dataUsuario.instituicao;
	}
}


function sendCalendario(method, url) {
	objCalendario.dt_inicio_ano_letivo = formatData2(inicioAno.dt_inicio);
	objCalendario.dt_fim_ano_letivo = formatData2(fimAno.dt_inicio);
	objCalendario.dt_inicio_recesso = formatData2(inicioRecesso.dt_inicio);
	objCalendario.dt_fim_recesso = formatData2(fimRecesso.dt_inicio);

	dispatch(method, '/api/calendario/' + url + '.php', objCalendario, function (data) {
		if (url == 'create') {
			objCalendario.id = data.id;
		}
	}, function (data) {
		//console.log(data);
	});

	instituicao.uf = uf;
	instituicao.nome = $('.nomeInstituicao').val();
	dataUsuario.email = $('.emailProfessor').val();
	dataUsuario.instituicao = instituicao;


	dispatch('POST', '/api/usuario/update.php', dataUsuario, function (data) {
	});


	$('.formInstituicao input[name="json_data"]').val(JSON.stringify(instituicao));

	uploadFile();


	if (logoInstituicao) {
		$('#logoEscola').html('<img src="' + logoInstituicao + '" />').removeClass('off');
	} else {
		$('#logoEscola').html('<img src="' + dataUsuario.instituicao.logo + '" />').removeClass('off');
	}

}

function loadCalendarios() {
	$('#abertura .iniciar span').html('Criar Calendario');
	$('.contCalendarios .bts , .contCalendarios .tit').html('');
	dispatch('GET', '/api/calendario/read.php?usuario=' + idProf, '', function (data) {
		if (data.calendarios) {
			$('.contCalendarios .tit').html('Seus calendários');
			for (var i in data.calendarios) {
				var obj = $('<div class="btCalendario" >' + formatData1(data.calendarios[i].dt_criacao.split(' ')[0]) + '</div>');
				obj[0].obj = data.calendarios[i];
				$('.contCalendarios .bts').append(obj);
			}
			if (data.calendarios.length > 0) {
				//sendDataCalendario( data.calendarios[0] );
				$('#abertura .iniciar span').html('Editar calendário');
			}
		}
		$('.contCalendarios .btCalendario').off().on('click', function () {
			sendDataCalendario($(this)[0].obj);
		})
	});
}
function sendDataCalendario(obj) {
	objCalendario = obj;

	objCalendario.dt_inicio_ano_letivo = formatData1(objCalendario.dt_inicio_ano_letivo);
	objCalendario.dt_fim_ano_letivo = formatData1(objCalendario.dt_fim_ano_letivo);
	objCalendario.dt_inicio_recesso = formatData1(objCalendario.dt_inicio_recesso);
	objCalendario.dt_fim_recesso = formatData1(objCalendario.dt_fim_recesso);


	$('.nomeInstituicao').val((objCalendario.usuario.instituicao.nome ? objCalendario.usuario.instituicao.nome : ''));
	$('.emailProfessor').val((objCalendario.usuario.email ? objCalendario.usuario.email : ''));
	$('input[name="inicioAno"]').val(objCalendario.dt_inicio_ano_letivo);
	$('input[name="fimAno"]').val(objCalendario.dt_fim_ano_letivo);
	$('input[name="inicioRecesso"]').val(objCalendario.dt_inicio_recesso);
	$('input[name="fimRecesso"]').val(objCalendario.dt_fim_recesso);
	uf = objCalendario.usuario.instituicao.uf;
	$('#uf option[value="' + uf + '"]').prop({ selected: true });
	if (objCalendario.usuario.instituicao.logo) {
		//$('input[name="logo"]').val( objCalendario.usuario.instituicao.logo );
	}



	$('.tituloEscola').html($('.nomeInstituicao').val());

	page2();

}

function abertura() {

	loadCalendarios();

	$('.formLoginProf .error').html('').hide();

	$('#abertura').fadeIn();
	$('#abertura .iniciar').off().on('click', function () {
		if ($('.contCalendarios .btCalendario').length > 0) {
			$('.contCalendarios .btCalendario:first').trigger('click');
		} else {
			page2();
		}
	})
	//.trigger('click');

}

function page2() {
	$('#abertura').fadeOut();
	$('#dadosGestor').fadeIn();

	if (userFTD && userFTD.schools.length > 0 && novaInst) {
		$('.nomeInstituicao').val(userFTD.schools[0].name);
	}

	$('#dadosGestor .iniciar').off().on('click', function () {
		if ($('#dadosGestor .inputDate.erro').length <= 0) {
			$('#dadosGestor').fadeOut();
			$('#configCalendario').fadeIn();
			uf = $('#uf').val();

			loadEventos();
		}

	})
	//.trigger('click');

	$('#configCalendario .iniciar').off().on('click', function () {

		if ($('#configCalendario .form .cont:visible input.erro').length <= 0) {

			$('#configCalendario').fadeOut();
			$('.tituloEscola').html($('.nomeInstituicao').val());

			evtsInicio();
			calendario();

			updateEventos();
			dividirCapitulos();
			evtsCalendario();

			if (objCalendario.id) {
				sendCalendario('POST', 'update');
			} else {
				sendCalendario('PUT', 'create');
			}
		}
	})
	//.trigger('click');


	$('.novoEvento input[name="dia_letivo"]:first').attr('checked', 'checked');




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
		if ($(this).closest('#dadosGestor').length > 0) {
			var viA = $('#dadosGestor .inputDate[name="inicioAno"]').val().split('/');
			var vfA = $('#dadosGestor .inputDate[name="fimAno"]').val().split('/');
			var viR = $('#dadosGestor .inputDate[name="inicioRecesso"]').val().split('/');
			var vfR = $('#dadosGestor .inputDate[name="fimRecesso"]').val().split('/');
			var dt1 = new Date(viA[2], viA[1] - 1, viA[0] - 1);
			var dt2 = new Date(vfA[2], vfA[1] - 1, vfA[0] - 1);
			var dt3 = new Date(viR[2], viR[1] - 1, viR[0] - 1);
			var dt4 = new Date(vfR[2], vfR[1] - 1, vfR[0] - 1);

			/* $(this).removeClass('erro'); */
			$('#dadosGestor .inputDate').removeClass('erro').each(function () {

				if ($(this).attr('name') == 'inicioAno') {
					if (dt1 > dt2 || dt1 > dt3 || dt1 > dt4) {
						$(this).addClass('erro');
					}
				} else if ($(this).attr('name') == 'fimAno') {
					if (dt2 < dt1 || dt2 < dt3 || dt2 < dt4) {
						$(this).addClass('erro');
					}
				} else if ($(this).attr('name') == 'inicioRecesso') {
					if (dt3 < dt1 || dt3 > dt2 || dt3 > dt4) {
						$(this).addClass('erro');
					}
				} else if ($(this).attr('name') == 'fimRecesso') {
					if (dt4 < dt1 || dt4 > dt2 || dt4 < dt3) {
						$(this).addClass('erro');
					}
				}

			})

		}
	});


	$('#configCalendario .checks .btCheck').removeClass('on').off().on('click', function () {
		$('#configCalendario .checks .btCheck').removeClass('on');
		$(this).addClass('on');

		var t = $(this).attr('data-tipo');

		$('#configCalendario .form .ano3 span').removeClass('on');
		$('#configCalendario .form .ano3 span:eq(' + (t - 1) + ')').addClass('on');

		$('#configCalendario .form input').removeClass('erro');
		if (t == 3) {
			$('#configCalendario .form input[name="v4"]').prop({ readonly: true }).val('36');
		} else {
			$('#configCalendario .form input[name="v4"]').prop({ readonly: false });
			$('#configCalendario .form ').find('input').each(function () {
				$(this).val($(this).attr('data-value'));
			});
		}
		if (t == 4) {
			$('#configCalendario .form').hide();
		} else {
			$('#configCalendario .form').show();
		}
	})


	if (objCalendario.id) {


		var v1 = 1;
		var v2 = parseInt(objCalendario.qtde_volumes_1o_ano);
		var v3 = v2 + 1;
		var v4 = v3 - 1 + parseInt(objCalendario.qtde_volumes_2o_ano);
		var v5 = v4 + 1;
		var v6 = v5 - 1 + parseInt(objCalendario.qtde_volumes_3o_ano);
		//console.log(objCalendario.revisao_volume_3o_ano , '-', v1, v2, v3, v4, v5, v6);
		$('#configCalendario .form').find('input[name="v1"]').val(v1);
		$('#configCalendario .form').find('input[name="v2"]').val(v2);
		$('#configCalendario .form').find('input[name="v3"]').val(v3);
		$('#configCalendario .form').find('input[name="v4"]').val(v4);
		$('#configCalendario .form').find('input[name="v5"]').val(v5);
		$('#configCalendario .form').find('input[name="v6"]').val(v6);

		$('#configCalendario .checks .btCheck.t' + objCalendario.revisao_volume_3o_ano).trigger('click');

	} else {
		$('#configCalendario .form ').find('input').each(function () {
			$(this).val($(this).attr('data-value'));
		});
		$('#configCalendario .checks .btCheck:first').trigger('click');
	}

	$('#configCalendario .form input').on('input keydown keyup mousedown mouseup select contextmenu drop', function (e) {
		var replace = $(this).val().replace(/[^0-9s]/g, '');
		$(this).val(replace);
		var val = ($(this).val() ? parseInt($(this).val()) : '');
		var erro = false;
		var indParent = parseInt($(this).parent().parent().attr('data-tipo'));

		var name = parseInt($(this).attr('name').replace('v', ''));
		if ((indParent != 2 && name > 1 && name < 6) || (indParent == 2 && name > 1 && name < 4)) {
			if (name % 2 == 0) {
				if (indParent == 2) {
					if (val < 2) erro = true;
					if (val > 34) erro = true;
				} else {
					if (name == 2 && val > 32) erro = true;
					if (name == 4 && val > 34) erro = true;

				}
				if (erro) {
					$(this).addClass('erro');
				}

				if (val) {
					$(this).parent().parent().find('input[name="v' + (name + 1) + '"]').val(val + 1);

					var values = [];
					$('#configCalendario .form  input:visible').each(function () {
						values.push(parseInt($(this).val()));
					})
					var v0 = 0;
					erro = false;
					for (var i in values) {

						if (v0 < values[i]) {
							v0 = values[i];
						} else {
							erro = true;
						}
					}

					if (erro) {
						$('#configCalendario .form input').addClass('erro');
					} else {
						$('#configCalendario .form input').removeClass('erro');

					}
				}
			}
		}

	});


}

function loadEventos() {
	feriados = [];
	evtsFTD = [];
	simulados = [];
	evtsProf = [];

	var url_uf = '';
	if (uf) url_uf = '&uf=' + uf;

	if (objCalendario.id) {
		dispatch('GET', '/api/evento/read.php?tipo_evento=2&calendario=' + objCalendario.id, '', function (data) {
			if (data.eventos) {
				for (var i in data.eventos) {
					if (data.eventos[i].titulo != 'Evento tutorial') {
						evtsProf.push(convertFromDB(data.eventos[i]));
					}
				}
			}
		});
	}

	dispatch('GET', '/api/evento/read.php?tipo_evento=3&uf=', '', function (data) {
		if (data.eventos) {
			for (var i in data.eventos) {
				feriados.push(convertFromDB(data.eventos[i]));
			}
		}

		if (uf) {
			dispatch('GET', '/api/evento/read.php?tipo_evento=3' + url_uf, '', function (data) {
				if (data.eventos) {
					var ar = [];
					for (var i in data.eventos) {
						ar.push(convertFromDB(data.eventos[i]));
					}
					feriados = feriados.concat(ar);
				}
			});
		}
	});

	dispatch('GET', '/api/evento/read.php?tipo_evento=4&uf=', '', function (data) {
		if (data.eventos) {
			for (var i in data.eventos) {
				evtsFTD.push(convertFromDB(data.eventos[i]));
			}
			if (uf) {
				dispatch('GET', '/api/evento/read.php?tipo_evento=4' + url_uf, '', function (data) {
					if (data.eventos) {
						var ar = [];
						for (var i in data.eventos) {
							ar.push(convertFromDB(data.eventos[i]));
						}
						evtsFTD = evtsFTD.concat(ar);
					}
				});
			}
		}
	});

	dispatch('GET', '/api/evento/read.php?tipo_evento=5&uf=', '', function (data) {
		if (data.eventos) {
			for (var i in data.eventos) {
				simulados.push(convertFromDB(data.eventos[i]));
			}
			if (uf) {
				dispatch('GET', '/api/evento/read.php?tipo_evento=5' + url_uf, '', function (data) {
					if (data.eventos) {
						var ar = [];
						for (var i in data.eventos) {
							ar.push(convertFromDB(data.eventos[i]));
						}
						simulados = simulados.concat(ar);
					}
				});

			}
		}
	});

}

function qtdDias() {
	arDias = [];
	arDias2 = [];
	var d = 0;
	var travaContador = true;
	var meio = false;
	$('#calendario .ano .dia').each(function () {
		if ($(this).hasClass('iniDia') || $(this).hasClass('iniRecesso') || $(this).hasClass('fimRecesso') || $(this).hasClass('fimDia')) {
			travaContador = !travaContador;
		}

		if (
			!$(this).hasClass('sabado') &&
			!$(this).hasClass('domingo') &&
			!$(this).hasClass('foraMes') &&
			!$(this).hasClass('diasRecesso') &&
			$(this)[0].dia_letivo &&
			!travaContador
		) {
			arDias.push(this);
			if (!meio) arDias2.push(this);
			d++;
		}

		if ($(this).hasClass('iniRecesso')) meio = true;
	})
}



function dividirCapitulos() {
	volumes = [];

	objCalendario.revisao_volume_3o_ano = ($('#configCalendario .checks .btCheck.on').attr('data-tipo') - 1);
	/* if ($('#configCalendario .checks .btCheck.on').attr('data-tipo') == 2) {
		objCalendario.qtde_volumes_3o_ano = 0;
	} */

	if ($('#configCalendario .checks .btCheck.on').attr('data-tipo') != 4) {
		qtdDias();


		var v1 = parseInt($('#configCalendario .form input[name="v1"]').val());
		var v2 = parseInt($('#configCalendario .form input[name="v2"]').val());
		var v3 = parseInt($('#configCalendario .form input[name="v3"]').val());
		var v4 = parseInt($('#configCalendario .form input[name="v4"]').val());

		if (objCalendario.revisao_volume_3o_ano != 2) {
			var v5 = parseInt($('#configCalendario .form .on input[name="v5"]').val());
			var v6 = parseInt($('#configCalendario .form .on input[name="v6"]').val());
		} else {
			var v5 = 0;
			var v6 = 0;
		}

		//console.log(v1, v2, v3, v4, v5, v6);
		evtsVolumes(v1, v2, v3, v4, v5, v6);

	} else {

		objCalendario.qtde_volumes_1o_ano = 0;
		objCalendario.qtde_volumes_2o_ano = 0;
		objCalendario.qtde_volumes_3o_ano = 0;
		objCalendario.revisao_volume_3o_ano = 3;
	}

}

function evtsVolumes(v1, v2, v3, v4, v5, v6) {
	//console.log(v1, v2, v3, v4, v5, v6);

	objCalendario.qtde_volumes_1o_ano = (v2 - v1 + 1);
	objCalendario.qtde_volumes_2o_ano = (v4 - v3 + 1);





	var diaDiv = arDias.length / (v2 - v1 + 1);
	for (var i = 0; i < (v2 - v1 + 1); i++) {
		var dia = arDias[parseInt(diaDiv * i)];
		var data = dia.dataDia + '/' + dia.dataMes + '/' + dia.dataAno;
		var tit = 'Início vol. ' + (v1 + i) + ' (1<sup>o</sup> EM)';

		var obj = {
			volume: (v1 + i),
			titulo: tit,
			dt_inicio: data,
			dt_fim: data,
			dia_letivo: true,
			tipo_evento: {
				id: 6,
				descricao: null
			},
			descricao: null,
			uf: uf,
		}
		volumes.push(obj);
	}

	var diaDiv = arDias.length / (v4 - v3 + 1);
	for (var i = 0; i < (v4 - v3 + 1); i++) {
		var dia = arDias[parseInt(diaDiv * i)];
		var data = dia.dataDia + '/' + dia.dataMes + '/' + dia.dataAno;
		var tit = 'Início vol. ' + (v3 + i) + ' (2<sup>o</sup> EM)';

		var obj = {
			volume: (v3 + i),
			titulo: tit,
			dt_inicio: data,
			dt_fim: data,
			dia_letivo: true,
			tipo_evento: {
				id: 6,
				descricao: null
			},
			descricao: null,
			uf: uf,
		}
		volumes.push(obj);
	}
	if (objCalendario.revisao_volume_3o_ano != 2) {
		console.log(v6, v5);
		console.log(objCalendario);
		objCalendario.qtde_volumes_3o_ano = (v6 - v5 + 1);

		if (objCalendario.revisao_volume_3o_ano == 1) {
			var diaDiv = arDias2.length / (v6 - v5 + 1);
		} else {
			var diaDiv = arDias.length / (v6 - v5 + 1);
		}
		for (var i = 0; i < (v6 - v5 + 1); i++) {
			if (objCalendario.revisao_volume_3o_ano == 1) {
				var dia = arDias2[parseInt(diaDiv * i)];
			} else {
				var dia = arDias[parseInt(diaDiv * i)];
			}
			var data = dia.dataDia + '/' + dia.dataMes + '/' + dia.dataAno;
			var tit = 'Início vol. ' + (v5 + i) + ' (3<sup>o</sup> EM)';

			var obj = {
				volume: (v5 + i),
				titulo: tit,
				dt_inicio: data,
				dt_fim: data,
				dia_letivo: true,
				tipo_evento: {
					id: 6,
					descricao: null
				},
				descricao: null,
				uf: uf,
			}
			volumes.push(obj);
		}
	}
	updateVolumes();
}


function evtsInicio() {
	var load = false;
	if (load) {
		var data = objCalendario.dt_inicio_ano_letivo;
	} else {
		var data = $('.formInstituicao input[name="inicioAno"]').val();
	}

	inicioAno = {
		titulo: 'Início do ano letivo',
		dt_inicio: data,
		dt_fim: data,
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: uf,
	}

	if (load) {
		var data = objCalendario.dt_fim_ano_letivo;
	} else {
		var data = $('.formInstituicao input[name="fimAno"]').val();
	}
	fimAno = {
		titulo: 'Fim do ano letivo',
		dt_inicio: data,
		dt_fim: data,
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: uf,
	}

	if (load) {
		var data = objCalendario.dt_inicio_recesso;
	} else {
		var data = $('.formInstituicao input[name="inicioRecesso"]').val();
	}
	inicioRecesso = {
		titulo: 'Início do Recesso',
		dt_inicio: data,
		dt_fim: data,
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: uf,
	}

	if (load) {
		var data = objCalendario.dt_fim_ano_letivo;
	} else {
		var data = $('.formInstituicao input[name="fimRecesso"]').val();
	}
	fimRecesso = {
		titulo: 'Fim do Recesso',
		dt_inicio: data,
		dt_fim: data,
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: uf,
	}
	evtsIni.push(inicioAno);
	evtsIni.push(fimAno);
	evtsIni.push(inicioRecesso);
	evtsIni.push(fimRecesso);


	objCalendario.dt_inicio_ano_letivo = inicioAno.dt_inicio;
	objCalendario.dt_fim_ano_letivo = fimAno.dt_inicio;
	objCalendario.dt_inicio_recesso = inicioRecesso.dt_inicio;
	objCalendario.dt_fim_recesso = fimRecesso.dt_inicio;

	$('#calendario .ano .copyMes:eq(' + inicioAno.dt_inicio + ') .dia.iniDia').removeClass('iniDia evt1 evt2 evt3 evt4 evt5');
	$('#calendario .ano .copyMes:eq(' + fimAno.dt_inicio + ') .dia.fimDia').removeClass('fimDia evt1 evt2 evt3 evt4 evt5');
	$('#calendario .ano .copyMes:eq(' + inicioRecesso.dt_inicio + ') .dia.iniRecesso').removeClass('iniRecesso');
	$('#calendario .ano .copyMes:eq(' + fimRecesso.dt_inicio + ') .dia.fimRecesso').removeClass('fimRecesso');

}


function splitDt(len, dt) {
	var num = parseInt(dt.split('/')[len]);
	//console.log(num);
	return num;
}

function calendario() {
	$('.page').fadeOut();

	if ($('#calendario .ano .copyMes').length > 0) {
		var inicio = false;
	} else {
		dataEventos = [];
		$('#calendario .ano').html('');
		var inicio = true;
	}


	for (var mes = 0; mes < 12; mes++) {
		var objMes = {};
		objMes.nome = nomeMeses[mes];
		objMes.eventos = [];
		if (inicio) {
			//dataEventos.push(objMes);
			var contMes = $('#calendario>.copyMes').clone().appendTo('.ano').addClass('hide ').addClass('mes' + (mes + 1));
		} else {
			var contMes = $('#calendario .ano .copyMes:eq(' + mes + ')')
		}

		var ano = anoAtual;
		var dt = new Date(ano, mes, 1);
		var inicioSemana = dt.getDay();


		var dt2 = new Date(ano, mes - 1, 1);
		var lastDayOfMonth = new Date(dt.getFullYear(), dt.getMonth() + 1, 0);
		var lastDayOfMonth2 = new Date(dt2.getFullYear(), dt2.getMonth() + 1, 0);

		var dia_letivo = false;

		var removeDiasMes = false;

		contMes.find('.numDias>div').each(function (i) {

			this.dataDia = false;
			this.dataMes = false;
			this.dataAno = false;
			this.dia_letivo = true;

			var html = '';
			var dia = i + 1 - inicioSemana;
			var dt_fim = new Date(ano, mes, dia);
			var className = 'dia ' + nomeDias[dt_fim.getDay()].toLowerCase();
			if (i >= inicioSemana && dt_fim.getMonth() == dt.getMonth()) {
				this.dataDia = dia;
				this.dataMes = (mes + 1);
				this.dataAno = dt_fim.getFullYear();
			} else {
				className += ' foraMes ';
				if (dia - lastDayOfMonth.getDate() - 1 >= 0) {
					this.dataDia = (dia - lastDayOfMonth.getDate());
					this.dataMes = (mes == 11 ? 1 : mes + 2);
					this.dataAno = dt_fim.getFullYear();
				} else {
					this.dataDia = (dia + lastDayOfMonth2.getDate());
					this.dataMes = (mes == 0 ? 12 : mes);
					this.dataAno = dt_fim.getFullYear();
				}
			}
			/* html += this.dataDia+"/"+this.dataMes+"/"+this.dataAno; */
			html += this.dataDia;
			className += ' dia' + this.dataDia;
			className += ' diaM' + this.dataMes + ' ';

			this.dataDia = (this.dataDia < 10 ? '0' + this.dataDia : this.dataDia);
			this.dataMes = (this.dataMes < 10 ? '0' + this.dataMes : this.dataMes);

			/* console.log(  inicioAno.dt_inicio , fimAno.dt_inicio , inicioAno.dt_inicio , fimAno.dt_inicio ); */


			var hj = new Date(this.dataAno, this.dataMes - 1, this.dataDia);
			var iAno = new Date(splitDt(2, inicioAno.dt_inicio), splitDt(1, inicioAno.dt_inicio) - 1, splitDt(0, inicioAno.dt_inicio));
			var fAno = new Date(splitDt(2, fimAno.dt_inicio), splitDt(1, fimAno.dt_inicio) - 1, splitDt(0, fimAno.dt_inicio));
			var iRec = new Date(splitDt(2, inicioRecesso.dt_inicio), splitDt(1, inicioRecesso.dt_inicio) - 1, splitDt(0, inicioRecesso.dt_inicio));
			var fRec = new Date(splitDt(2, fimRecesso.dt_inicio), splitDt(1, fimRecesso.dt_inicio) - 1, splitDt(0, fimRecesso.dt_inicio));

			if (
				(this.dataAno == splitDt(2, inicioAno.dt_inicio)) &&
				(hj >= iAno && hj <= fAno) &&
				(hj <= iRec || hj >= fRec)
			) {
				if (hj.getDay() != 0 && hj.getDay() != 6 && className.match('foraMes') == null) {
					dia_letivo = true;
					className += ' diaLetivo ';
				} else {
					dia_letivo = false;
				}
			} else {
				dia_letivo = false;
			}
			if (hj > iRec && hj < fRec) {
				className += ' diasRecesso ';
			}
			if (hj < iAno || hj > fAno) {
				className += ' diasRecesso ';
			}
			if (className.match('foraMes') == null) {
				if (hj.getDate() == iAno.getDate() && hj.getMonth() == iAno.getMonth() && hj.getFullYear() == iAno.getFullYear()) {
					className += ' iniDia ';
				}
				if (hj.getDate() == fAno.getDate() && hj.getMonth() == fAno.getMonth() && hj.getFullYear() == fAno.getFullYear()) {
					className += ' fimDia ';
				}
				if (hj.getDate() == iRec.getDate() && hj.getMonth() == iRec.getMonth() && hj.getFullYear() == iRec.getFullYear()) {
					className += ' iniRecesso ';
				}
				if (hj.getDate() == fRec.getDate() && hj.getMonth() == fRec.getMonth() && hj.getFullYear() == fRec.getFullYear()) {
					className += ' fimRecesso ';
				}
			}


			this.dia_letivo = dia_letivo;


			$(this).html('<div class="txt">' + html + '</div><div class="evts"></div><div class="vols"></div>').attr('class', className);

			if (this.dataDia > 1 && $(this).index() % 7 == 0 &&
				(this.dataMes == $(this).parent().parent().parent().index() + 2 ||
					(this.dataMes == 1 && $(this).parent().parent().parent().index() == 11))
			) {
				removeDiasMes = true;
			}
			if (removeDiasMes) {
				$(this).addClass('removeDia');
			}


		})
		contMes.find('.tableheader>div.nomeMes span').html(nomeMeses[mes]);
		contMes.find('.tableheader>div.nomeAno span').html(anoAtual);
		contMes.find('.removeDia').remove();
	}

	$('#calendario').fadeIn(500, function () {
		// $stepTuto = 0
		// tutorial($stepTuto);

		$('#tutorial .continuar').off().on('click', function () {
			if (!$tTutorial) {
				tutorial($stepTuto);
			}
		})

		$('.btTutorial').off().on('click', function () {
			$stepTuto = 0
			tutorial($stepTuto);
		})
	});

}
// #tutorial .cursor.p1{top:320; left: 500;}
// #tutorial .cursor.p2{top:600; left: 680;}
// #tutorial .cursor.p3{top:320; left: 500;}
// #tutorial .cursor.p3{top:320; left: 730;}
// #tutorial .cursor.p3{top:330; left: 690;}
// #tutorial .cursor.p3{top:390; left: 670;}

function tutorial(passo) {
	$stepTuto++
	$('#tutorial').fadeIn(500);
	$('#tutorial .mensagem').hide();
	$('.btsTopo, .setaDir, .setaEsq, .btTutorial').hide();

	var mes = $('.copyMes:visible').index() + 1;
	var obj = $('.copyMes:visible .quarta:eq(2)');
	var dia = $('.copyMes:visible .quarta:eq(2) .txt').text() * 1;
	//console.log(dia, mes)
	var evento = '';
	$tTutorial = true;
	setTimeout(function () { $tTutorial = false; }, 3300)
	switch (passo) {
		case 0:
			var t = obj.offset().top - $('.ano').offset().top + 40;
			var l = obj.offset().left - $('.ano').offset().left + 70;
			TweenMax.fromTo($('.cursor'), 1.3, { top: 300, left: 1200 }, { top: t, left: l })

			$('#tutorial .m1').delay(1000).fadeIn(500, function () {
			});
			break;
		case 1:
			obj.trigger('click');
			TweenMax.to($('.cursor'), .7, {
				top: 450, left: 520, onComplete: function () {
					TweenMax.to($('.cursor'), .7, {
						delay: 1, top: 330, left: 700, onComplete: function () {
						}
					})
				}
			})
			$('#tutorial .m2').delay(700).fadeIn(500, function () {
				$('.tituloEvt').val('Reunião Professores')
			});

			break;
		case 2:
			$('.btCancelar').trigger('click');
			var t = obj.offset().top - $('.ano').offset().top + 40;
			var l = obj.offset().left - $('.ano').offset().left + 70;

			TweenMax.fromTo($('.cursor'), 1.3, { top: 330, left: 700 }, {
				top: t, left: l, onComplete: function () {
					TweenMax.to($('.cursor'), 1.3, { delay: .2, top: 370, left: l + 200 })
					//console.log(dia, dia + 1, dia + 2, mes)
					setTimeout(function () { classDrag($('.dia' + dia + '.diaM' + mes), $('.dia' + dia + '.diaM' + mes)); }, 000)
					setTimeout(function () { classDrag($('.dia' + dia + '.diaM' + mes), $('.dia' + (dia + 1) + '.diaM' + mes)); }, 500)
					setTimeout(function () {
						classDrag($('.dia' + dia + '.diaM' + mes), $('.dia' + (dia + 2) + '.diaM' + mes));
					}, 1000)
				}
			})

			$('#tutorial .m3').delay(1000).fadeIn(500, function () { });
			break;
		case 3:
			addEvento(dia, mes, dia + 2, mes);
			TweenMax.to($('.cursor'), 1, {
				top: 450, left: 520, onComplete: function () {
					$('.tituloEvt').val('Evento tutorial');
					TweenMax.to($('.cursor'), 1, {
						delay: .3, top: 670, left: 600, onComplete: function () {
						}
					})
				}
			})
			break;
		case 4:
			$('.btEvento').trigger('click');
			setTimeout(function () {

				$('.copyMes:visible .infoMes .diaEvento .txtEvento span').each(function () {
					var str = $(this).text();
					if (str == 'Evento tutorial') {
						evento = $(this).parent().parent()
						// $(evento).attr('class')
					}
				})

				var t = $(evento).offset().top - $('.ano').offset().top + 40;
				var l = $(evento).offset().left - $('.ano').offset().left + 70;
				//console.log(t, l)

				TweenMax.to($('.cursor'), 1, { delay: .5, top: t, left: l });
				$('#tutorial .m4').delay(1000).fadeIn(500, function () {
				});
			}, 500)

			break;
		case 5:
			$('.copyMes:visible .infoMes .diaEvento .txtEvento span').each(function () {
				var str = $(this).text();
				if (str == 'Evento tutorial') {
					evento = $(this).parent().parent()
				}
			})
			$(evento).trigger('click');
			TweenMax.to($('.cursor'), 1, {
				top: 670, left: 400, onComplete: function () {
				}
			});

			break;
		case 6:
			$('.btExcluir').trigger('click');
			TweenMax.fromTo($('.cursor'), 1, { top: 670, left: 400 }, {
				top: 500, left: -100, onComplete: function () {
				}
			});
			$('#tutorial').delay(500).fadeOut();
			$('.btsTopo, .setaDir, .setaEsq, .btTutorial').fadeIn()

			break;
	}
}

function evtsCalendario() {
	$('.ano .copyMes').addClass('hide');
	$('.ano .copyMes:first').removeClass('hide');


	$('.setaEsq').hide().off().on('click', function () {
		$('.setaDir').show();
		var i = $('.ano .copyMes:visible').index();
		if (i - 1 == 0) $('.setaEsq').hide();
		$('.ano .copyMes:eq(' + ((i - 1) % 12) + ')').removeClass('hide');
		$('.ano .copyMes:eq(' + (i) + ')').addClass('hide');
	})
	$('.setaDir').show().off().on('click', function () {
		$('.setaEsq').show();
		var i = $('.ano .copyMes:visible').index();
		if (i + 1 == 11) $('.setaDir').hide();
		$('.ano .copyMes:eq(' + ((i + 1) % 12) + ')').removeClass('hide');
		$('.ano .copyMes:eq(' + (i) + ')').addClass('hide');
	})

	$('.ano .dias.numDias>div').not('.blockClick').off().on('click', function (evt) {
		var msgm = '';
		var simulado = false;
		if ($(this).find('.evt3').length > 0 || $(this).hasClass('diasRecesso')) {
			msgm = 'Esta data é um Feriado e não é considerada um dia letivo.  <br> Deseja incluir o evento mesmo assim? ';
		} else if ($(this).find('.evt2').length > 0 || $(this).find('.evt4').length > 0) {
			msgm = 'Está data já tem um evento agendado. <br>Deseja incluir outro evento mesmo assim? ';
		} else if ($(this).find('.evt5').length > 0) {
			simulado = true;
		}
		var d = this.dataDia;
		var m = this.dataMes;
		msgmAlerta(msgm, function () {
			addEvento(d, m, d, m, simulado);
		})
		evt.preventDefault();
	})
	$dragStart = false;
	$('.ano .dias.numDias>div').not('.blockClick').on('mousedown touchstart', function () {
		$dragStart = $(this);
	})
	$('.ano .dias.numDias>div').not('.blockClick').on('mouseover touchmove', function () {
		if ($dragStart && $dragStart[0] != $(this)[0]) {
			classDrag($dragStart, $(this));
		}
	})
	$('.ano .dias.numDias>div').not('.blockClick').on('mouseup touchend', function () {
		if ($dragStart && $dragStart[0] != $(this)[0]) {
			if ($(this).index() < $dragStart.index()) {
				var diaF = $dragStart[0].dataDia;
				var mesF = $dragStart[0].dataMes;
				var dia = this.dataDia;
				var mes = this.dataMes;
			} else {
				var dia = $dragStart[0].dataDia;
				var mes = $dragStart[0].dataMes;
				var diaF = this.dataDia;
				var mesF = this.dataMes;
			}
			classDrag($dragStart, $(this));
			addEvento(dia, mes, diaF, mesF);
		}
		$dragStart = false;
	})

	$('.btGerarCalendario').off().on('click', function () {
		$('#calendario').fadeOut();
		$('#configPaginas').fadeIn();
	})

	$('.btEditar').off().on('click', function () {
		evtsIni = [];
		$('#dadosGestor').fadeIn();
		$('#calendario').fadeOut();
	})

	$('#configPaginas .btX').off().on('click', function () {
		$('#calendario').fadeIn();
		$('#configPaginas').fadeOut();
	})

	$('#configPaginas .gerar').off().on('click', function () {
		$('#configPaginas').fadeOut();
		$('#mesesPage option').each(function () {
			if (this.selected) $mesesPage = parseInt(this.value);
		})
		gerarCalendario();
	})
}

function addEvento(dia, mes, diaF, mesF, simulado) {
	$('.novoEvento').fadeIn();
	$('.novoEvento .tituloEvt').val('');
	$('.novoEvento .dIni').text(dia + '/' + mes + '/' + anoAtual);
	$('.novoEvento .dFim').text(diaF + '/' + mesF + '/' + anoAtual);

	$('.novoEvento .contForm .editar').hide();
	$('.novoEvento .contForm .adicionar').show();
	$('.novoEvento .haveraAula').show();
	$('.novoEvento .haveraAula.a1').prop({ checked: true });

	if (simulado) {
		$('.novoEvento .simulado').removeClass('on').show();
		$('.novoEvento .simulado').off().on('click', function () {
			$(this).toggleClass('on');
			if ($(this).hasClass('on')) {
				$('.novoEvento .haveraAula').hide();
				$('.novoEvento .haveraAula.a1').prop({ checked: true });
			} else {
				$('.novoEvento .haveraAula').show();
			}
		})
	} else {
		$('.novoEvento .contForm .simulado').hide();
	}

	$('.novoEvento .btEvento').off().on('click', function () {

		var obj = {
			titulo: $('.novoEvento .tituloEvt').val(),
			dt_inicio: dia + '/' + mes + '/' + anoAtual,
			dt_fim: diaF + '/' + mesF + '/' + anoAtual,
			dia_letivo: !!parseInt($('.novoEvento input[name="dia_letivo"]:checked').attr('data-value')),
			simulado: ($('.novoEvento .simulado').hasClass('on') ? 1 : 0),
			tipo_evento: {
				id: 2,
				descricao: null
			},
			descricao: ($('.novoEvento .simulado').hasClass('on') ? 1 : 0),
			uf: uf
		}


		$('.ano .numDias>div.drag').removeClass('drag');
		evtsProf.push(obj);
		dispatch('PUT', '/api/evento/create.php', convertToSave(obj), function (data) {
			//console.log(obj)
			obj.id = data.id;
			updateEventos();
			dividirCapitulos();
			$('.novoEvento').fadeOut();
			dispatch('PUT', '/api/calendario/addEvento.php', { evento_id: obj.id, id: objCalendario.id }, function (data) { })
		})


	})
	$('.novoEvento .btExcluir').hide();
	$('.novoEvento .btCancelar').off().on('click', function () {
		$('.novoEvento input').val('');
		$('.novoEvento').fadeOut();
		$('.ano .numDias>div').removeClass('drag');
	});


}

function edtEvento(dia) {
	var obj = dia.evento;
	$('.novoEvento').fadeIn();

	$('.novoEvento .tituloEvt').val(obj.titulo);
	$('.novoEvento .dtInicio').val(obj.dt_inicio);
	$('.novoEvento .dtFim').val(obj.dt_fim);


	$('.novoEvento .contForm .editar').show();
	$('.novoEvento .contForm .adicionar').hide();

	if (obj.descricao == '1') {
		$('.novoEvento .simulado').addClass('on').show();
		$('.novoEvento .haveraAula').hide();
		$('.novoEvento .simulado').off().on('click', function () {
			$(this).toggleClass('on');
			if ($(this).hasClass('on')) {
				$('.novoEvento .haveraAula').hide();
				$('.novoEvento .haveraAula.a1').prop({ checked: true });
			} else {
				$('.novoEvento .haveraAula').show();
			}
		})
	} else {
		$('.novoEvento .contForm .simulado').hide();
	}


	$('.novoEvento .btEvento').off().on('click', function () {
		dia.evento = '';
		dia.contEvt = '';
		$(dia).removeClass('evt0 evt1 evt2 evt3 evt4');

		obj.dt_inicio = $('.novoEvento .dtInicio').val();
		obj.dt_fim = $('.novoEvento .dtFim').val();
		obj.titulo = $('.novoEvento .tituloEvt').val();
		obj.dia_letivo = !!parseInt($('.novoEvento input[name="dia_letivo"]:checked').attr('data-value'));
		obj.simulado = ($('.novoEvento .simulado').hasClass('on') ? 1 : 0);
		obj.descricao = ($('.novoEvento .simulado').hasClass('on') ? 1 : 0);


		dispatch('POST', '/api/evento/update.php', convertToSave(obj), function (data) {
			//console.log(data)
			updateEventos();
			$('.novoEvento').fadeOut();
		});
	})

	$('.novoEvento .btExcluir').show().off().on('click', function () {

		//console.log (obj.dt_inicio , obj.dt_fim);
		if (obj.dt_inicio != obj.dt_fim) {
			var dI = obj.dt_inicio.split('/');
			var dF = obj.dt_fim.split('/');

			var dt1 = new Date(parseInt(dI[2]), parseInt(dI[1] - 1), parseInt(dI[0]));
			var dt2 = new Date(parseInt(dF[2]), parseInt(dF[1] - 1), parseInt(dF[0]));
			const diffTime = Math.abs(dt1 - dt2);
			const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
			for (var i = 0; i <= diffDays; i++) {
				var dt = new Date(dt1.getFullYear(), dt1.getMonth(), dt1.getDate() + i);
				$('.ano .numDias .dia' + parseInt(dt.getDate()) + '.diaM' + parseInt(dt.getMonth() + 1))[0].dia_letivo = true;;

			}

		} else {
			$('.dia' + parseInt(obj.dt_inicio.split('/')[0]) + '.diaM' + parseInt(obj.dt_inicio.split('/')[1]))[0].dia_letivo = true;
		}


		$(this).off();
		var index = dia.contEvt.indexOf(obj);
		if (index > -1) {
			dia.contEvt.splice(index, 1);
		}

		$('.novoEvento').fadeOut();
		dispatch('DELETE', '/api/evento/delete.php', { id: obj.id }, function (data) {
			//console.log(data);
			updateEventos();
			dividirCapitulos();
		});
	});

	$('.novoEvento .btCancelar').off().on('click', function () {
		$('.novoEvento input ').val('');
		$('.novoEvento').fadeOut();
		$('.ano .numDias>div').removeClass('drag');
	});
}

function msgmAlerta(txt, callback) {
	if (txt != '') {
		$('.alertaMsgm .msgm').html(txt);
		$('.alertaMsgm .btOk').off().on('click', function () {
			$('.alertaMsgm .btOk').off();
			callback();
			$('.alertaMsgm').fadeOut()
		})
		$('.alertaMsgm .btCancelar').off().on('click', function () {
			$('.alertaMsgm .btCancelar').off();
			$('.alertaMsgm').fadeOut()
		})
		$('.alertaMsgm').fadeIn();
	} else {
		callback();
	}
}

function updateEventos() {


	$arCores = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

	$('.ano .copyMes .dia .evts').html('');
	$('.ano .copyMes .infoMes').html('');


	var ar = [
		evtsIni,  //0
		evtsProf, //1
		feriados, //2
		evtsFTD,  //3
		simulados,//4
	]

	for (var m = 0; m < ar.length; m++) {
		for (var d in ar[m]) {
			evt = ar[m][d];
			var dtI = evt.dt_inicio.split('/');
			var dtF = evt.dt_fim.split('/');

			var dt1 = new Date(parseInt(dtI[2]), parseInt(dtI[1] - 1), parseInt(dtI[0]));
			var dt2 = new Date(parseInt(dtF[2]), parseInt(dtF[1] - 1), parseInt(dtF[0]));

			var diaI = $('.ano .numDias .dia' + parseInt(dtI[0]) + '.diaM' + parseInt(dtI[1]));
			var diaF = $('.ano .numDias .dia' + parseInt(dtF[0]) + '.diaM' + parseInt(dtF[1]));

			//console.log (dtI , dtF , dtI != dtF , evt.descricao == '1' );
			if (evt.dt_inicio != evt.dt_fim) {

				const diffTime = Math.abs(dt1 - dt2);
				const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

				for (var i = 0; i <= diffDays; i++) {
					var dt = new Date(dt1.getFullYear(), dt1.getMonth(), dt1.getDate() + i);
					var d = $('.ano .numDias .dia' + parseInt(dt.getDate()) + '.diaM' + parseInt(dt.getMonth() + 1));

					if (evt.tipo_evento.id == 2) {
						d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + ' corProf' + $arCores[dt1.getMonth()] + '"></div>');
					} else if (evt.tipo_evento.id == 5) {
						if (i > 0) {
							d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + ' semico "></div>');
						} else {
							d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + ' "></div>');
						}
					} else {
						d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + '"></div>');
						d.removeClass('diaLetivo');
					}
					if (!evt.dia_letivo) {
						d.each(function () {
							this.dia_letivo = false;
						})
					}
				}

			} else {
				var d = $('.ano .numDias .dia' + parseInt(dt1.getDate()) + '.diaM' + parseInt(dt1.getMonth() + 1));
				if (evt.tipo_evento.id == 2) {
					if (evt.descricao == '1') {
						d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + ' provaSimulado"></div>');
					} else {
						d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + ' corProf' + $arCores[dt1.getMonth()] + '"></div>');
					}
				} else {
					d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + '"></div>');
					diaI.removeClass('diaLetivo');
				}

				if (!evt.dia_letivo) {
					diaI.each(function () {
						this.dia_letivo = false;
					})
				}
			}


			var clickEdita = m == 1;

			if (diaI[0] !== diaF[0]) {
				var txt = '<strong>' + evt.dt_inicio + ' - ' + evt.dt_fim + '</strong>' + '<br /><span>' + evt.titulo + '</span>';
			} else {
				var txt = '<strong>' + evt.dt_inicio + '</strong>' + '<br /><span>' + evt.titulo + '</span>';
			}
			if (evt.tipo_evento.id == 2 && evt.descricao == '1') {
				var divEvt = $('<div class="diaEvento" data-dia="' + parseInt(dtI[0]) + '" >\
					<div class="cor evt' + evt.tipo_evento.id + ' provaSimulado"></div>\
					<div class="txtEvento">'+ txt + '</div></div>');
			} else {
				var divEvt = $('<div class="diaEvento" data-dia="' + parseInt(dtI[0]) + '" >\
					<div class="cor evt' + evt.tipo_evento.id + ' corProf' + $arCores[dt1.getMonth()] + '"></div>\
					<div class="txtEvento">'+ txt + '</div></div>');

			}

			if (evt.tipo_evento.id == 2) {
				$arCores[dt1.getMonth()] = ($arCores[dt1.getMonth()] == 5 ? 1 : $arCores[dt1.getMonth()] + 1);
			}
			$('.ano .mes' + (parseInt(dtI[1])) + ' .infoMes').append(divEvt);

			if (clickEdita) {
				divEvt[0].evento = evt;
				divEvt[0].contEvt = ar[m];
			}

			if (dt1.getMonth() != dt2.getMonth()) {

				var txt = '<strong>' + evt.dt_inicio + ' - ' + evt.dt_fim + '</strong>' + '<br /><span>' + evt.titulo + '</span>';
				var divEvt = $('<div class="diaEvento" data-dia="' + parseInt(dtI[0]) + '" >\
					<div class="cor evt' + evt.tipo_evento.id + '"></div>\
					<div class="txtEvento">'+ txt + '</div></div>');


				$('.ano .mes' + (dt2.getMonth() + 1) + ' .infoMes').append(divEvt);

				if (clickEdita) {
					divEvt[0].evento = evt;
					divEvt[0].contEvt = ar[m];
				}
			}

		}

	}

	$('.ano .infoMes').each(function () {
		$(this).find('.diaEvento').sort(function (a, b) {
			if (parseInt($(a).attr('data-dia')) < parseInt($(b).attr('data-dia'))) {
				return -1;
			} else {
				return 1;
			}
		}).appendTo($(this));
	})

	$('.diaEvento').off().on('click', function () {
		if (this.evento) {
			edtEvento(this);
		}
	})

}

function updateVolumes() {

	$('.ano .copyMes .dia .vols').html('');
	$('.ano .copyMes .infoMesVol').html('');
	var ar = [
		volumes
	]

	for (var m = 0; m < ar.length; m++) {
		for (var d in ar[m]) {
			evt = ar[m][d];
			var dtI = evt.dt_inicio.split('/');

			var diaI = $('.ano .numDias .dia' + parseInt(dtI[0]) + '.diaM' + parseInt(dtI[1]));

			diaI.find('.vols').append('<div class="evt' + evt.tipo_evento.id + '">' + evt.volume + '</div>');
			diaI.removeClass('diaLetivo');

			var txt = '<strong>' + evt.dt_inicio + '</strong>' + '<br /><span>' + evt.titulo + '</span>';

			var divEvt = $('<div class="diaEvento" data-dia="' + parseInt(dtI[0]) + '" >\
				<div class="cor evt' + evt.tipo_evento.id + '"></div>\
				<div class="txtEvento">'+ txt + '</div></div>');

			$('.ano .mes' + (parseInt(dtI[1])) + ' .infoMesVol').append(divEvt);


		}

	}



	$('.ano .infoMesVol').each(function () {
		$(this).find('.diaEvento').sort(function (a, b) {
			if (parseInt($(a).attr('data-dia')) < parseInt($(b).attr('data-dia'))) {
				return -1;
			} else {
				return 1;
			}
		}).appendTo($(this));
	})

	$('.ano .infoMesVol').each(function () {
		var diaVol = -1;
		var arGroup = [];
		var txtEvt = '';
		var dataEvt = '';
		var appends = [];

		function appendDiv() {

			if (arGroup.length > 0) {
				txtEvt = '';
				dataEvt = '';
				for (var i in arGroup) {
					dataEvt = arGroup[i].find('.txtEvento strong').html();
					txtEvt += arGroup[i].find('.txtEvento span').html();
					if (i != arGroup.length - 1) txtEvt += '<br>';
					arGroup[i].addClass('remove');
				}
				obj = $('<div class="diaEvento" data-dia="' + dataEvt.split('/')[0] + '"><div class="cor evt6"></div>\
					<div class="txtEvento">\
					<strong>'+ dataEvt + '</strong><br>\
					<span>'+ txtEvt + '</span></div></div>')
				//_this.append(obj);
				appends.push(obj);
			}
			arGroup = [];
		}
		$(this).find('.diaEvento').each(function () {

			if ($(this).attr('data-dia') != diaVol) {
				diaVol = parseInt($(this).attr('data-dia'));
				appendDiv();
			}
			if (parseInt($(this).attr('data-dia')) == diaVol) {
				arGroup.push($(this));
			}
			if ($(this).index() == $(this).parent().find('.diaEvento').length - 1) {
				appendDiv();
			}

		})
		for (var i in appends) {
			$(this).append(appends[i]);
		}
	})
	$('.ano .infoMesVol .remove').remove();

	$('.ano .infoMesVol').each(function () {
		$(this).find('.diaEvento').sort(function (a, b) {
			if (parseInt($(a).attr('data-dia')) < parseInt($(b).attr('data-dia'))) {
				return -1;
			} else {
				return 1;
			}
		}).appendTo($(this));
	})



	$('.diaEvento').off().on('click', function () {
		if (this.evento) {
			edtEvento(this);
		}
	})

}


function classDrag(obj1, obj2) {
	$('.ano .numDias>div').removeClass('drag');

	var mult = (parseInt(obj1.index()) - parseInt(obj2.index())) / Math.abs(parseInt(obj1.index()) - parseInt(obj2.index()));

	if (mult > 0) {
		var v0 = parseInt(obj2.index());
		var v1 = parseInt(obj1.index());
	} else {
		var v0 = parseInt(obj1.index());
		var v1 = parseInt(obj2.index());
	}

	for (var i = v0; i <= v1; i += 1) {
		//if ( $('.copyMes:visible .numDias .dia:eq('+i+')')[0].diaUtil ) {
		$('.copyMes:visible .numDias .dia:eq(' + i + ')').addClass('drag');
		//}
	}

}

function gerarCalendario() {
	$('#calendario').fadeOut();
	$('#pagePrint').fadeIn();
	$('#pagePrint').attr('class', 'qtd' + $mesesPage);
	$('#pagePrint .cont').html('');

	/* for(var i =0; i< 12; i+= ($mesesPage%12 == 0 ? 12 : $mesesPage%12 ) ){
		$('#pagePrint .cont').append('<div class="page" id="page'+j+'"></div>');
		for(var a =i; a< i+$mesesPage; a++){
			$('#calendario .ano .copyMes:eq('+(a)+')').clone().appendTo( $('#pagePrint .cont .page:last') );
		}
		j++;
	} */

	if ($mesesPage == 1 || $mesesPage == 4) {
		$pdf = new jsPDF({
			orientation: "landscape",
			format: 'a3',
			//unit:'mm',
			//format: [ 420, 297 ],
		});
	} else {
		$pdf = new jsPDF({
			orientation: "portrait",
			format: 'a3',
			//unit:'mm',
			//format: [ 297, 420 ],
		});
	}
	$('#cont2').html('');
	$('#stage').addClass('print');
	$('#loading').removeClass('hide');
	$('#loading .sprite').html('Gerando PDF');

	cloneImage(0, function () {
		gerarPdf();
	});


}

function cloneImage(j) {
	$('#pagePrint .cont').html('').show();
	$('#cont2').hide();

	for (var a = j; a < j + $mesesPage; a++) {
		$('#calendario .ano .copyMes:eq(' + (a) + ')').clone().appendTo($('#pagePrint .cont'));
	}
	$('#pagePrint .cont .copyMes .infoMes .diaEvento').each(function (i) {
		//console.log(i, $('#pagePrint .cont .copyMes:eq('+i+') .infoMes .diaEvento').length );
		if ($mesesPage == 12 && $('#pagePrint .cont .copyMes:eq('+i+') .infoMes .diaEvento').length <= 5) {
			$('#pagePrint .cont .copyMes:eq('+i+') .infoMes .diaEvento').addClass('w100');
		}
	})

	$('#calendario .btsTopo').clone().appendTo($('#pagePrint .cont'));
	$('#pagePrint .btsTopo .btGerarCalendario').remove();
	$('#pagePrint .btsTopo .btEditar').remove();

	$('#pagePrint .copyMes .foraMes').find('.txt').html('');
	$('#pagePrint .copyMes .foraMes').find('.evts').html('');
	$('#pagePrint .copyMes .foraMes').attr('class', 'dia foraMes');
	$('#pagePrint .copyMes').removeClass('hide');

	setTimeout(function () {
		html2canvas(document.querySelector('#pagePrint .cont')).then(canvas => {
			$('#cont2').html(canvas);
			var imageData = canvas.toDataURL("image/jpg");

			$pdf.addImage(imageData, 'JPEG', 0, 0);

			j += ($mesesPage % 12 == 0 ? 12 : $mesesPage % 12);

			if (j < 12) {
				$pdf.addPage();
				cloneImage(j);
			} else {
				$pdf.save("calendario.pdf");
				$('#loading').addClass('hide');
				$('#calendario').fadeIn();
				$('#pagePrint').fadeOut();
				$('#stage').removeClass('print');
			}
		});
	}, 1000);
}

function gerarPdf() {


	$('#pagePrint .cont').hide();
	$('#pagePrint #cont2').show();

}

function drop(container) {
	var obj = container;
	var t = obj.offset().top;
	var l = obj.offset().left;
	var w = obj.width() * scale;
	var h = obj.height() * scale;

	if ($x > l && $x < l + w && $y > t && $y < t + h) {
		return true
	} else {
		return false
	}
}

function DragDrop(objDrag, objDrop) {
	$(objDrag).off().on('mousedown touchstart', function (evt) {
		if ($canDrag) {
			$canDrag = false;
			$drag = $(this).parent();
			$drag.parent().addClass('off');
			$xIni = (evt.clientX - $(this).offset().left) / scale;
		}
		evt.preventDefault();
	})

	$('body').off().on('mousemove touchmove', function (evt) {
		if ($drag) {
			$moveu = true;

			if (evt.clientX || evt.clientX === 0) {
				$xMouse = (evt.clientX - $('#stage').offset().left) / scale;
				$yMouse = (evt.clientY - $('#stage').offset().top) / scale;

				$x = evt.clientX / scale;
				$y = evt.clientY / scale;
			} else {
				$xMouse = (evt.originalEvent.touches[0].pageX - $('#stage').offset().left) / scale;
				$yMouse = (evt.originalEvent.touches[0].pageY - $('#stage').offset().top) / scale;

				$x = evt.originalEvent.touches[0].pageX / scale;
				$y = evt.originalEvent.touches[0].pageY / scale;
			}

			var l = $xMouse - $xIni;
			if (l > 0) {
				l = 0;
			}
			if (l < ($('#fases').width() - $('#stage').width()) * -1) {
				l = ($('#fases').width() - $('#stage').width()) * -1
			}

			$('#fases').css({ 'left': l });
			// $('#fases .brilho').css({'left':l*-1});

			if ($('.maoDrag').is(':visible')) $('.maoDrag').fadeOut();
		}
		evt.preventDefault();
	});

	$('#jogo').on('mouseup touchend mouseleave', function (evt) {
		// $(this).css('cursor','default');
		if ($drag) {

			if ($('#personagem').attr('data-anima') != 'anda' && $('#personagem').attr('data-anima') != 'recolhe') {
				var animaAntiga = $('#personagem').attr('data-anima');

				if ($('#personagem').offset().left / scale < 0) {
					lPersonagem = parseInt($('#personagem').css('left')) + $('#personagem').offset().left * -1 / scale
					$('#personagem .imgPers').css('transform', 'scale(1, 1)')
					$('#personagem').attr('data-anima', 'anda')
					TweenMax.to($('#personagem'), 1, { left: lPersonagem, onComplete: function () { $('#personagem').attr('data-anima', animaAntiga) } });

				}

				if ($('#personagem').offset().left / scale > 900) {
					lPersonagem = 800 + parseInt($('#fases').css('left')) * -1;
					$('#personagem .imgPers').css('transform', 'scale(-1, 1)')
					$('#personagem').attr('data-anima', 'anda')
					TweenMax.to($('#personagem'), 1, { left: lPersonagem, onComplete: function () { $('#personagem').attr('data-anima', animaAntiga) } });

				}
			}

			$canDrag = true;
			$moveu = false;
			$drag = false;
		}
	});

	// $('#stage').off().on('mouseleave',function(){
	// 	$('body').css('cursor','default')
	// 	if($drag && $moveu){
	// 		$('.linhaHerois').fadeOut();
	// 		tweenAnimation = TweenLite.to($drag,.5,{ease:Back.linear, top:$yIni, left:$xIni, onComplete:function(){
	// 			$drag = false;
	// 			$canDrag = true;
	// 		}})
	// 	}
	// })
}

function generateImage(input) {


	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#logoEscola').html('<img src="' + e.target.result + '" />').removeClass('off');
			logoInstituicao = e.target.result;
		};
		reader.readAsDataURL(input.files[0]);
		//console.log( input.files[0] );
		dataUsuario.instituicao.logo = input.files[0]
	}

}
var formData;
function uploadFile() {

	// define new form
	formData = new FormData();
	formData.append('nome', instituicao.nome);
	formData.append('id', instituicao.id);
	formData.append('uf', instituicao.uf);
	formData.append('logo', instituicao.logo);
	//console.log(  instituicao.logo );

	var request = new XMLHttpRequest();
	request.open("POST", baseUrl + "/api/instituicao/update.php");
	request.send(formData);

}


function getFormData(form) {
	var unindexed_array = form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function (n, i) {
		indexed_array[n['name']] = n['value'];
	});

	return indexed_array;
}


function randomNumber(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
}

function shuffle(o) {
	for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
	return o;
};

function stopAll() {
	$('body').find('audio').each(function () {
		$(this)[0].pause()
		$(this)[0].currentTime = 0;
	})
}



function logOut() {
	dispatch('POST', '/api/usuario/logout.php');
}

