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

var dataEventos = [];
var nomeMeses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
var nomeDias = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sabado"];

var qtdCapitulos = 0;
var dataUsuario = false;
var idProf = false;
var objCalendario = {
	ano_referencia: '2021',
};

$(document).ready(function () {

	logOut();

	$('.page').hide();
	$('#loading').hide().removeClass('hide');
	$('#stage').fadeIn(300);
	login();
	//abertura();

});


function login() {
	$('#telaLogin').fadeIn(300);
	$('.formLoginProf input').off().on('focus', function (evt) {
		$(this).removeClass('erro');
	});

	$('.formLoginProf').off().on('submit', function (evt) {
		submitFormLogin(this);
		evt.preventDefault();
		return false;
	})
	// .trigger('submit')
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
	//console.log(data);
	data.ambiente = 'SITE';

	dispatch('POST', '/api/usuario/login.php', data, function (data) {
		if (data.sucess && data.usuario) {
			dataUsuario = data.usuario;
			console.log(dataUsuario);
			idProf = dataUsuario.id;
			objCalendario.usuario = {
				id: dataUsuario.id
			};
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

function checkInstituicao(){
	if(!dataUsuario.instituicao || !dataUsuario.instituicao.id){
		dispatch('PUT', '/api/instituicao/create.php', {nome:'x', logo:'x' , uf:'x'}, function (data) {
			instituicao.id = data.id;
		});
	}
}

function sendCalendario(method, url) {
	objCalendario.dt_inicio_ano_letivo = formatData2(inicioAno.dt_inicio);
	objCalendario.dt_fim_ano_letivo = formatData2(fimAno.dt_inicio);
	objCalendario.dt_inicio_recesso = formatData2(inicioRecesso.dt_inicio);
	objCalendario.dt_fim_recesso = formatData2(fimRecesso.dt_inicio);

	//console.log(objCalendario);
	//console.log(method, '/api/calendario/' + url + '.php' );
	dispatch(method, '/api/calendario/' + url + '.php', objCalendario, function (data) {
		console.log(data);
		objCalendario.id = data.id;
	}, function (data) {
		console.log(data);
	});

	instituicao.uf = uf;
	instituicao.nome = $('.nomeInstituicao').val();
	dataUsuario.instituicao = instituicao;

	//console.log(dataUsuario)
	dispatch('POST', '/api/usuario/update.php', dataUsuario, function (data) {
		console.log(data);
	});
	dispatch('POST', '/api/instituicao/update.php', instituicao, function (data) {
		console.log(data);
	});

}

function loadCalendarios() {
	dispatch('GET', '/api/calendario/read.php?usuario=' + idProf, '', function (data) {
		if (data.calendario) {
			console.log(data.calendario);
		}
	}, function (data) {
		console.log(data);
	});
}

function abertura() {

	loadCalendarios();

	$('.formLoginProf .error').html('').hide();

	$('#abertura').fadeIn();
	$('#abertura .iniciar').off().on('click', function () {
		$('#abertura').fadeOut();
		$('#dadosGestor').fadeIn();

	})
	//.trigger('click');


	$('#dadosGestor .iniciar').off().on('click', function () {
		$('#dadosGestor').fadeOut();
		$('#configCalendario').fadeIn();
		uf = $('#uf').val();

		loadEventos();

	})
	//.trigger('click');

	$('#configCalendario .iniciar').off().on('click', function () {
		$('#configCalendario').fadeOut();
		$('.tituloEscola').html($('.nomeInstituicao').val());

		evtsInicio();
		calendario();
		dividirCapitulos();
		sendCalendario('PUT', 'create');

		updateEventos();
		evtsCalendario();
	})
	// .trigger('click');


	$('.novoEvento input[name="dia_letivo"]:first').attr('checked', 'checked');
	/* $('.novoEvento input[name="dia_letivo"]').off().on('click',function(){
		console.log( $(this).val() , this.value )
	}) */


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

	$('.selectVol .titulo').off().on('click', function () {
		if ($(this).parent().hasClass('on')) {
			$(this).parent().removeClass('on');
		} else {
			$('.selectVol').removeClass('on');
			$(this).parent().addClass('on');
		}


		if ($(this).parent().hasClass('s2')) {
			$(this).parent().parent().parent().addClass('on');
		}
		$('.selectVol').each(function () {
			if (!$(this).hasClass('on')) $(this).find('.cont').slideUp(300);
		})
		if ($(this).parent().hasClass('on')) {
			$(this).parent().find('.cont:first').slideDown(300);
		}
	})

}

function loadEventos() {
	feriados = [];
	evtsFTD = [];
	simulados = [];
	/* dispatch('GET', '/api/evento/read.php?uf=' + uf, {}, function (data) {

		for (var i in data.eventos) {
			if (!data.eventos[i].uf || data.eventos[i].uf == uf) {

				if (data.eventos[i].tipo_evento.id == 1) {
					//evtsIni.push(convertFromDB(data.eventos[i]));
				} else if (data.eventos[i].tipo_evento.id == 2) {
					//evtsProf.push(convertFromDB(data.eventos[i]));
				} else if (data.eventos[i].tipo_evento.id == 3) {
					feriados.push(convertFromDB(data.eventos[i]));
					//console.log(data.eventos[i].titulo, data.eventos[i].uf , uf);
					//console.log( !data.eventos[i].uf , data.eventos[i].uf == uf );
				} else if (data.eventos[i].tipo_evento.id == 4) {
					evtsFTD.push(convertFromDB(data.eventos[i]));
				} else if (data.eventos[i].tipo_evento.id == 5) {
					//simulados.push(convertFromDB(data.eventos[i]));
				}

			}
		}

	}); */

	uf = 'SP';
	var url_uf = '';
	//if (uf) url_uf = '&uf=' + uf;
	dispatch('GET', '/api/evento/read.php?tipo_evento=3' + url_uf, '', function (data) {
		if (data.eventos) {
			for (var i in data.eventos) {
				feriados.push(convertFromDB(data.eventos[i]));
			}
		}
	});

	dispatch('GET', '/api/evento/read.php?tipo_evento=4' + url_uf, '', function (data) {
		if (data.eventos) {
			for (var i in data.eventos) {
				evtsFTD.push(convertFromDB(data.eventos[i]));
			}
		}
	});

	dispatch('GET', '/api/evento/read.php?tipo_evento=5' + url_uf, '', function (data) {
		if (data.eventos) {
			for (var i in data.eventos) {
				simulados.push(convertFromDB(data.eventos[i]));
			}
		}
	});

}

function dividirCapitulos() {

	if ($('.selectVol.on:last').attr('data-tipo')) {
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

		var v1 = parseInt($('.selectVol.on:last').find('input[name="v1"]').val());
		var v2 = parseInt($('.selectVol.on:last').find('input[name="v2"]').val());
		var v3 = parseInt($('.selectVol.on:last').find('input[name="v3"]').val());
		var v4 = parseInt($('.selectVol.on:last').find('input[name="v4"]').val());


		objCalendario.qtde_volumes_1o_ano = (v2 - v1 + 1);
		objCalendario.qtde_volumes_2o_ano = (v4 - v3 + 1);
		
		if ($('.selectVol.on:last').attr('data-tipo') == 2) {
			objCalendario.revisao_volume_3o_ano = 2;
		} else if ($('.selectVol.on:last').attr('data-tipo') == 3) {
			objCalendario.revisao_volume_3o_ano = 1;
		} else {
			objCalendario.revisao_volume_3o_ano = 0;
		}
		if ($('.selectVol.on:last').attr('data-tipo') == 2) {
			objCalendario.qtde_volumes_3o_ano = 0;
		}

		var diaDiv = arDias.length / (v2 - v1 + 1);
		for (var i = 0; i < (v2 - v1 + 1); i++) {
			var dia = arDias[parseInt(diaDiv * i)];
			var data = dia.dataDia + '/' + dia.dataMes + '/' + dia.dataAno;
			var tit = 'Início vol. ' + (v1 + i) + ' (1º EM)';

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
			var tit = 'Início vol. ' + (v3 + i) + ' (2º EM)';

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
		if ($('.selectVol.on:last').attr('data-tipo') != 2) {
			var v5 = parseInt($('.selectVol.on:last').find('input[name="v5"]').val());
			var v6 = parseInt($('.selectVol.on:last').find('input[name="v6"]').val());
			objCalendario.qtde_volumes_3o_ano = (v6 - v5 + 1);
			
			if ($('.selectVol.on:last').attr('data-tipo') == 3) {
				var diaDiv = arDias2.length / (v6 - v5 + 1);
			} else {
				var diaDiv = arDias.length / (v6 - v5 + 1);
			}
			for (var i = 0; i < (v6 - v5 + 1); i++) {
				if ($('.selectVol.on:last').attr('data-tipo') == 3) {
					var dia = arDias2[parseInt(diaDiv * i)];
				} else {
					var dia = arDias[parseInt(diaDiv * i)];
				}
				var data = dia.dataDia + '/' + dia.dataMes + '/' + dia.dataAno;
				var tit = 'Início vol. ' + (v5 + i) + ' (3º EM)';

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

	} else {

		objCalendario.qtde_volumes_1o_ano = 0;
		objCalendario.qtde_volumes_2o_ano = 0;
		objCalendario.qtde_volumes_3o_ano = 0;
		objCalendario.revisao_volume_3o_ano = 0;
	}

}

function evtsInicio() {

	var data = $('.formInstituicao input[name="inicioAno"]').val();
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
	var data = $('.formInstituicao input[name="fimAno"]').val();
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
	var data = $('.formInstituicao input[name="inicioRecesso"]').val();
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
	var data = $('.formInstituicao input[name="fimRecesso"]').val();
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
		evtsProf = [];
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

		var ano = 2021;
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
			this.dia_letivo = false;

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
				dia_letivo = true;
				className += ' diaLetivo ';
			} else {
				dia_letivo = false;
			}
			if (hj >= iRec && hj <= fRec) {
				className += ' diasRecesso ';
			}
			if (hj < iAno || hj > fAno) {
				className += ' diasRecesso ';
			}
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
		contMes.find('.tableheader>div.nomeAno span').html('2001');
		contMes.find('.removeDia').remove();
	}


	$('#calendario').fadeIn(500);

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
		//if (this.dia_letivo) {
		if (!$(this).hasClass('diasRecesso')) {
			addEvento(this.dataDia, this.dataMes, this.dataDia, this.dataMes)
		}
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
		volumes = [];
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

function addEvento(dia, mes, diaF, mesF) {
	$('.novoEvento').fadeIn();
	$('.novoEvento .tituloEvt').val('');
	$('.novoEvento textarea').val('');
	$('.novoEvento .dIni').text(dia + '/' + mes);
	$('.novoEvento .dFim').text(diaF + '/' + mesF);

	$('.novoEvento .contForm .editar').hide();
	$('.novoEvento .contForm .adicionar').show();

	$('.novoEvento .btEvento').off().on('click', function () {

		var obj = {
			titulo: $('.novoEvento .tituloEvt').val(),
			dt_inicio: dia + '/' + mes + '/2021',
			dt_fim: diaF + '/' + mesF + '/2021',
			dia_letivo: !!parseInt($('.novoEvento input[name="dia_letivo"]:checked').attr('data-value')),
			tipo_evento: {
				id: 2,
				descricao: null
			},
			descricao: $('.novoEvento textarea').val(),
			uf: uf
		}

		if (dia != diaF) {
			$('.ano .numDias>div.drag').addClass('evt' + 2).removeClass('drag');
		}
		evtsProf.push(obj);
		dispatch('PUT', '/api/evento/create.php', convertToSave(obj), function (data) {
			//console.log(obj)
			obj.id = data.id;
			updateEventos();
			$('.novoEvento').fadeOut();
			dispatch('PUT', '/api/calendario/addEvento.php', { evento_id: obj.id, id: objCalendario.id }, function (data) {

			})
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

	$('.novoEvento .btEvento').off().on('click', function () {
		dia.evento = '';
		dia.contEvt = '';
		$(dia).removeClass('evt0 evt1 evt2 evt3 evt4');

		obj.dt_inicio = $('.novoEvento .dtInicio').val();
		obj.dt_fim = $('.novoEvento .dtFim').val();
		obj.titulo = $('.novoEvento .tituloEvt').val();
		obj.dia_letivo = !!parseInt($('.novoEvento input[name="dia_letivo"]:checked').attr('data-value'));


		dispatch('POST', '/api/evento/update.php', convertToSave(obj), function (data) {
			//console.log(data)
			updateEventos();
			$('.novoEvento').fadeOut();
		});
	})

	$('.novoEvento .btExcluir').show().off().on('click', function () {
		var index = dia.contEvt.indexOf(obj);
		if (index > -1) {
			dia.contEvt.splice(index, 1);
		}

		dispatch('DELETE', '/api/evento/delete.php', { id: obj.id }, function (data) {
			//console.log(data);
			updateEventos();
			$('.novoEvento').fadeOut();
		});
	});

	$('.novoEvento .btCancelar').off().on('click', function () {
		$('.novoEvento input').val('');
		$('.novoEvento').fadeOut();
		$('.ano .numDias>div').removeClass('drag');
	});
}

function updateEventos() {

	$('.ano .copyMes .dia .evts').html('');
	$('.ano .copyMes .dia .vols').html('');
	$('.ano .copyMes .infoMes').html('');
	$('.ano .copyMes .infoMesVol').html('');
	var ar = [
		evtsIni,  //0
		evtsProf, //1
		feriados, //2
		evtsFTD,  //3
		simulados,//4
		volumes,  //5
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

			if (dtI != dtF) {

				const diffTime = Math.abs(dt1 - dt2);
				const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

				for (var i = 0; i <= diffDays; i++) {
					var dt = new Date(dt1.getFullYear(), dt1.getMonth(), dt1.getDate() + i);
					var d = $('.ano .numDias .dia' + parseInt(dt.getDate()) + '.diaM' + parseInt(dt.getMonth() + 1));

					if (m == 5) {
						d.find('.vols').append('<div class="evt' + evt.tipo_evento.id + '">' + evt.volume + '</div>');
					} else {
						d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + '"></div>');
					}
					d.removeClass('diaLetivo');
					if (!evt.dia_letivo) d[0].dia_letivo = false;
				}

			} else {
				if (m == 5) {
					diaI.find('.vols').append('<div class="evt' + evt.tipo_evento.id + '">' + evt.volume + '</div>');
				} else {
					diaI.find('.evts').append('<div class="evt' + evt.tipo_evento.id + '"></div>');
				}
				diaI.removeClass('diaLetivo');
				if (!evt.dia_letivo) diaI[0].dia_letivo = false;
			}


			var clickEdita = m == 1;

			if (diaI[0] !== diaF[0]) {
				var txt = '<strong>' + evt.dt_inicio + ' - ' + evt.dt_fim + '</strong>' + '<br />' + evt.titulo;
			} else {
				var txt = '<strong>' + evt.dt_inicio + '</strong>' + '<br />' + evt.titulo;
			}
			var divEvt = $('<div class="diaEvento" data-dia="' + parseInt(dtI[0]) + '" >\
				<div class="cor evt' + evt.tipo_evento.id + '"></div>\
				<div class="txtEvento">'+ txt + '</div></div>');
			if (m == 5) {
				$('.ano .mes' + (parseInt(dtI[1])) + ' .infoMesVol').append(divEvt);
			} else {
				$('.ano .mes' + (parseInt(dtI[1])) + ' .infoMes').append(divEvt);
			}
			if (clickEdita) {
				divEvt[0].evento = evt;
				divEvt[0].contEvt = ar[m];
			}

			if (dt1.getMonth() != dt2.getMonth()) {
				//var txt = '<strong>' + parseInt(dtI[0]) + '/' + parseInt(dtI[1]) + ' - ' + parseInt(dtF[0]) + '/' + parseInt(dtF[1]) + '</strong>' + '<br />' + evt.titulo;
				var txt = '<strong>' + evt.dt_inicio + ' - ' + evt.dt_fim + '</strong>' + '<br />' + evt.titulo;
				var divEvt = $('<div class="diaEvento" data-dia="' + parseInt(dtI[0]) + '" >\
					<div class="cor evt' + evt.tipo_evento.id + '"></div>\
					<div class="txtEvento">'+ txt + '</div></div>');

				if (m == 5) {
					$('.ano .mes' + (dt2.getMonth() + 1) + ' .infoMesVol').append(divEvt);
				} else {
					$('.ano .mes' + (dt2.getMonth() + 1) + ' .infoMes').append(divEvt);
				}
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

function ordenarDivs(attr) {

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
	$('#loading').fadeIn();

	cloneImage(0, function () {
		gerarPdf();
	});

	//var imageData = canvas.toDataURL("image/png");
	//$pdf.addImage(imageData, 'JPEG', 0, 0);
	//$pdf.save("download.pdf");

}

function cloneImage(j) {
	$('#pagePrint .cont').html('').show();
	$('#cont2').hide();
	for (var a = j; a < j + $mesesPage; a++) {
		$('#calendario .ano .copyMes:eq(' + (a) + ')').clone().appendTo($('#pagePrint .cont'));
	}
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
			var imageData = canvas.toDataURL("image/png");

			$pdf.addImage(imageData, 'JPEG', 0, 0);

			j += ($mesesPage % 12 == 0 ? 12 : $mesesPage % 12);

			if (j < 12) {
				$pdf.addPage();
				cloneImage(j);
			} else {
				$pdf.save("calendario.pdf");
				$('#loading').fadeOut();
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
			//instituicao.logo = e.target.result;
		};
		instituicao.logo = input.files[0];

		reader.readAsDataURL(input.files[0]);
	}
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

