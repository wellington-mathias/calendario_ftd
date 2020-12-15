<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin - Calendario</title>
    <? include('includes/head.php'); ?>
</head>

<body>
    <div id="content">

        <? include('includes/menu.php'); ?>
        <div class="cont ">
            <div class="adicionar">
                <div class="topo">
                    <h1>Feriados</h1>
                    <div class="btVoltarEditar">Voltar </div>
                </div>


                <form class="formAdicionar" method="" action="">
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="tipo_evento" value="3" />
                    <ul class="camposAdicionar">
                        <li>
                            <label for="titulo">Titulo</label>
                            <input type="text" value="" name="titulo" class=" obgt"  />
                        </li>
                        <li>
                            <label for="data">Data inicio</label>
                            <input type="text" value="" name="dt_inicio" placeholder="dd/mm/aaaa" class="inputDate obgt" maxlength="10"  />
                        </li>
                        <li>
                            <label for="data">Data fim</label>
                            <input type="text" value="" name="dt_fim" placeholder="dd/mm/aaaa" class="inputDate obgt" maxlength="10"  />
                        </li>
                        <li>
                            <label for="descricao">Descrição</label>
                            <textarea name="descricao" value=""></textarea>
                        </li>
                        <li>
                            <label for="uf">UF</label>
                            <select name="uf" class="selectUf" ></select>
                        </li>
                        <li>
                            <label for="">Dia letivo</label>
                            <div class="contRadio">
                                <select name="dia_letivo" class="selectDia">
                                    <option value="1" >Sim</option>
                                    <option value="0" >Não</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <button class="btEnviar">Enviar</button>
                        </li>
                    </ul>
                </form>
            </div>
            <div class="listar">
                <div class="filter">
                </div>
                <div class="topo">
                    <h1>Feriados</h1>
                    <div class="btAdicionar">Adicionar + </div>
                </div>

                <ul class="lista">
                    <li>
                        <div>data</div>
                        <div class="titulo">Titulo</div>
                        <div>UF</div>
                        <div class="bts"></div>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</body>
<script>
    function complete(data) {
        if(data.eventos) dataListar = data.eventos;

        $('.lista li:gt(0)').remove();
        for (var i in dataListar) {
            //if (dataListar[i].tipo_evento.id == 3 ) {
                var dt = dataListar[i].dt_inicio.split('-');
                var obj = $('<li>\
                    <div>' + (dt[2] + '/' + dt[1] + '/' + dt[0]) + '</div>\
                    <div class="titulo">' + dataListar[i].titulo + '</div>\
                    <div>' + (dataListar[i].uf ? dataListar[i].uf : '') + '</div>\
                    <div class="bts">\
                        <button class="btEditar" >Editar</button>\
                        <button class="btExcluir" >X</button>\
                    </div>\
                </li>');
                $('.lista').append(obj);
                obj[0].obj = dataListar[i];
           // }
        }
        if ($('.lista li').length == 1) {
            $('.lista').append('<li> <div>Nenhum evento cadastrado</div> </li>');
        }
        
        creatEvents();
    }

    var dataListar = [];
    var page = 'evento';
    function listar() {
        dispatch('GET', '/api/'+page+'/read.php?tipo_evento=3', '', complete);
    }
    listar();
</script>

</html>