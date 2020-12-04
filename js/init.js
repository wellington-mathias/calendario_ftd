var scale = 1;
$(document).ready(function () {

	$('.page').hide();
	dispatch('GET', '/api/evento/read.php', {}, function (data) {
		for (var i in data.eventos) {
			if (data.eventos[i].tipo_evento.id == 3) {
				feriados.push(convertFromDB(data.eventos[i]));
			} else if (data.eventos[i].tipo_evento.id == 4 || data.eventos[i].tipo_evento.id == 5) {
				evtsFTD.push(convertFromDB(data.eventos[i]));
			}
		}

	});

	$('#loading').hide().removeClass('hide');

	$('#stage').fadeIn(300);

	$('#abertura').fadeIn();
	$('#abertura .iniciar').off().on('click', function () {
		$('#abertura').fadeOut();
		$('#dadosGestor').fadeIn();
	})
	//.trigger('click');


	$('#dadosGestor .iniciar').off().on('click', function () {
		$('#dadosGestor').fadeOut();
		$('.tituloEscola').html('Instituição: ' + $('.nomeInstituicao').val());
		evtsInicio();
		calendario();
	})
	//.trigger('click');


	$('.novoEvento input[name="dia_letivo"]:first').attr('checked', 'checked');
	/* $('.novoEvento input[name="dia_letivo"]').off().on('click',function(){
		console.log( $(this).val() , this.value )
	}) */

})

var inicioAno = fimAno = inicioRecesso = fimRecesso = {};
function evtsInicio() {

	var dia = $('.formInstituicao input[name="inicioAnoDia"]').val();
	var mes = $('.formInstituicao input[name="inicioAnoMes"]').val();
	inicioAno = {
		titulo: 'Início do ano letivo',
		dt_inicio: dia + '/' + mes + '/2021',
		dt_fim: dia + '/' + mes + '/2021',
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: null
	}
	dia = $('.formInstituicao input[name="fimAnoDia"]').val();
	mes = $('.formInstituicao input[name="fimAnoMes"]').val();
	fimAno = {
		titulo: 'Fim do ano letivo',
		dt_inicio: dia + '/' + mes + '/2021',
		dt_fim: dia + '/' + mes + '/2021',
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: null
	}
	dia = $('.formInstituicao input[name="inicioRecessoDia"]').val();
	mes = $('.formInstituicao input[name="inicioRecessoMes"]').val();
	inicioRecesso = {
		titulo: 'Início do Recesso',
		dt_inicio: dia + '/' + mes + '/2021',
		dt_fim: dia + '/' + mes + '/2021',
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: null
	}
	dia = $('.formInstituicao input[name="fimRecessoDia"]').val();
	mes = $('.formInstituicao input[name="fimRecessoMes"]').val();
	fimRecesso = {
		titulo: 'Fim do Recesso',
		dt_inicio: dia + '/' + mes + '/2021',
		dt_fim: dia + '/' + mes + '/2021',
		dia_letivo: true,
		tipo_evento: {
			id: 1,
			descricao: null
		},
		descricao: null,
		uf: null
	}
	evtsIni.push(inicioAno);
	evtsIni.push(fimAno);
	evtsIni.push(inicioRecesso);
	evtsIni.push(fimRecesso);

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
/* 
var feriados = [
	{
		dt_inicio: '01/01/2021',
		dt_fim: '01/01/2021',
		titulo: 'Confraternização Universal',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '29/01/2021',
		dt_fim: '3/02/2021',
		titulo: 'Teste',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '15/02/2021',
		dt_fim: '16/02/2021',
		titulo: 'Carnaval',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '02/04/2021',
		dt_fim: '02/04/2021',
		titulo: 'Paixão de Cristo',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '21/04/2021',
		dt_fim: '21/04/2021',
		titulo: 'Tiradentes',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '01/05/2021',
		dt_fim: '01/05/2021',
		titulo: 'Dia do Trabalho',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '03/06/2021',
		dt_fim: '03/06/2021',
		titulo: 'Corpus Christi',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '07/09/2021',
		dt_fim: '07/09/2021',
		titulo: 'Independência do Brasil',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '12/10/2021',
		dt_fim: '12/10/2021',
		titulo: 'Nossa Sr.a Aparecida - Padroeira do Brasil',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '02/11/2021',
		dt_fim: '02/11/2021',
		titulo: 'Finados',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '15/10/2021',
		dt_fim: '15/10/2021',
		titulo: 'Proclamação da República',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
	{
		dt_inicio: '25/12/2021',
		dt_fim: '25/12/2021',
		titulo: 'Natal',
		dia_letivo: false,
		uf: '',
		tipo_evento: {
			id: 3,
			descricao: null
		},
	},
];
 */
var feriados = [];
var evtsIni = [];
var evtsFTD = [];

var evtsProf = [];
var dataEventos = [];
var nomeMeses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
var nomeDias = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sabado"];

function calendario() {
	$('#stage *').removeAttr('style');
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


			this.dia_letivo = dia_letivo;


			$(this).html('<div class="txt">' + html + '</div><div class="evts"></div>').attr('class', className);

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
		contMes.find('.tableheader>div').html(nomeMeses[mes]);
		contMes.find('.removeDia').remove();
	}

	/* if (inicio) {
		for (var i in feriados) {
			var dt = feriados[i].dt_inicio.split('/');
			dataEventos.push(feriados[i]);
		}
	} */




	updateEventos();
	evtsCalendario();

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
		if (this.dia_letivo) {
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
		submitCalendario();
		$('#calendario').fadeOut();
		$('#configCalendario').fadeIn();
	})

	$('.btEditar').off().on('click', function () {
		evtsIni = [];
		$('#dadosGestor').fadeIn();
		$('#calendario').fadeOut();
	})

	$('#configCalendario li').off().on('click', function () {
		$('li').removeClass('selecionada')
		$(this).addClass('selecionada')
	})

	$('#configCalendario .gerar').off().on('click', function () {
		$('#configCalendario').fadeOut();
		$('#mesesPage option').each(function () {
			if (this.selected) $mesesPage = parseInt(this.value);
		})
		gerarCalendario();
	})
}

function submitCalendario() {
	/* console.log(evtsIni);
	console.log(dataEventos); */
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
			uf: null
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
	$('.ano .copyMes .infoMes').html('');
	var ar = [
		feriados,
		evtsIni,
		evtsFTD,
		evtsProf
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
					d.find('.evts').append('<div class="evt' + evt.tipo_evento.id + '"></div>');
					d.removeClass('diaLetivo');
					d[0].dia_letivo = false;
				}

			} else {
				diaI.find('.evts').append('<div class="evt' + evt.tipo_evento.id + '"></div>');
				diaI.removeClass('diaLetivo');
				diaI[0].dia_letivo = false;
			}


			var clickEdita = m == 3;

			if (diaI[0] !== diaF[0]) {
				var txt = '<strong>' + parseInt(dtI[0]) + '/' + parseInt(dtI[1]) + ' - ' + parseInt(dtF[0]) + '/' + parseInt(dtF[1]) + '</strong>' + ' - ' + evt.titulo;
			} else {
				var txt = '<strong>' + parseInt(dtI[0]) + '/' + parseInt(dtI[1]) + '</strong>' + ' - ' + evt.titulo;
			}
			var divEvt = $('<div class="diaEvento">\
				<div class="cor evt' + evt.tipo_evento.id + '"></div>\
				<div class="txtEvento">'+ txt + '</div></div>');
			$('.ano .mes' + (parseInt(dtI[1])) + ' .infoMes').append(divEvt);
			if (clickEdita) {
				divEvt[0].evento = evt;
				divEvt[0].contEvt = ar[m];
			}

			if (dt1.getMonth() != dt2.getMonth()) {
				var txt = '<strong>' + parseInt(dtI[0]) + '/' + parseInt(dtI[1]) + ' - ' + parseInt(dtF[0]) + '/' + parseInt(dtF[1]) + '</strong>' + ' - ' + evt.titulo;
				var divEvt = $('<div class="diaEvento">\
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

	/* if (this.dataDia == dt1.getDate() && this.dataMes == dt1.getMonth() + 1) {
		$(this).find('.evts').append('<div class="evt' + evt.tipo + '"></div>');
		if (!addMes) {
			
		}
		addMes = true;
	} */




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
		});
	} else {
		$pdf = new jsPDF({
			orientation: "portrait",
			format: 'a3',
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
		};

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

