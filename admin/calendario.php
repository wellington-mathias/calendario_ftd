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
                    <h1>Calendario</h1>
                    <div class="btVoltarEditar">Voltar </div>
                </div>


                <form class="formAdicionar" method="" action="">
                    <input type="hidden" name="id" value="" />
                    <ul class="camposAdicionar">
                        <li>
                            <label for="nome_instituicao">Nome da instituição</label>
                            <input type="text" value="" name="nome_instituicao" placeholder="Nome da Escola" class="" />
                        </li>
                        <li>
                            <label for="nome_instituicao">Email Professor</label>
                            <input type="text" value="" name="email_professor" placeholder="Email" class="" />
                        </li>
                        <li>
                            <label for="logo">Logo</label>
                            <input type="file" value="" name="logo"  />
                            <div class="contLogo"></div>
                        </li>
                        <li>
                            <label for="dt_inicio_ano_letivo">Inicio Ano Letivo *</label>
                            <input type="text" value="" name="dt_inicio_ano_letivo" placeholder="dd/mm/aaaa" class="inputDate obgt" maxlength="10" />
                        </li>
                        <li>
                            <label for="data">Inicio do Recesso Escolar *</label>
                            <input type="text" value="" name="dt_inicio_recesso" placeholder="dd/mm/aaaa" class="inputDate obgt" maxlength="10" />
                        </li>
                        <li>
                            <label for="data">Fim do Recesso Escolar *</label>
                            <input type="text" value="" name="dt_fim_recesso" placeholder="dd/mm/aaaa" class="inputDate obgt" maxlength="10" />
                        </li>
                        <li>
                            <label for="data">Fim do ano letivo *</label>
                            <input type="text" value="" name="dt_fim_ano_letivo" placeholder="dd/mm/aaaa" class="inputDate obgt" maxlength="10" />
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
                    <h1>Calendarios</h1>
                </div>

                <ul class="lista">
                    <li>
                        <div class="titulo">Criado por</div>
                        <div class="bts"></div>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</body>
<script>
    function complete(data) {
        if(data.calendarios) dataListar = data.calendarios;
        
        $('.lista li:gt(0)').remove();
        for (var i in dataListar) {
            console.log(dataListar[i])
            var obj = $('<li>\
                <div class="titulo">' + dataListar[i].usuario.nome + '</div>\
                <div class="bts">\
                    <button class="btEditar" >Editar</button>\
                    <button class="btExcluir" >X</button>\
                </div>\
            </li>');
            $('.lista').append(obj);
            obj[0].obj = dataListar[i];
        }
        if ($('.lista li').length == 1) {
            $('.lista').append('<li> <div>Nenhum calendario cadastrado</div> </li>');
        }
        
        creatEvents();
    }

    var dataListar = [];
    var page = 'calendario';
    function listar() {
        dataListar = [];
        dispatch('GET', '/api/'+page+'/read.php', '', complete  );
    }
    listar();
    
</script>

</html>