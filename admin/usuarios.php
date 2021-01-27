<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <? include('includes/head.php'); ?>
</head>

<body>

    <div id="content">

        <? include('includes/menu.php'); ?>
        <div class="cont ">
            <div class="adicionar">
                <div class="topo">
                    <h1>Usuarios</h1>
                    <div class="btVoltarEditar">Voltar </div>
                </div>


                <form class="formAdicionar" method="" action="">
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="tipo_usuario" value="" />
                    <input type="hidden" value="" name="id_instituicao" />
                    <ul class="camposAdicionar">
                        <li>
                            <label for="nome">Nome</label>
                            <input type="text" value="" name="nome" class=" "  />
                        </li>
                        <li>
                            <label for="nome">Email</label>
                            <input type="text" value="" name="email" class=" "  />
                        </li>
                        <li>
                            <label for="senha">Tipo Usuario</label>
                            <select name="tipo_usuario" class="selectUser" ></select>
                        </li>
                        <li class="campos tipo1">
                            <label for="nome">Login Admin</label>
                            <input type="text" value="" name="login" class=" "  />
                        </li>
                        <li class="campos tipo1">
                            <label for="senha">Senha Admin</label>
                            <input type="text" value="" name="password" class=" "/>
                        </li>
                        <li class="campos tipo2">
                            <label for="nome">Login Calendario</label>
                            <input type="text" value="" name="login_ftd" class=" "  />
                        </li>
                        <li class="campos tipo2">
                            <label for="senha">Senha Calendario</label>
                            <input type="text" value="" name="password_ftd" class=" "/>
                        </li>
                        <li>
                            <label for="senha">Nome da instituição</label>
                            <input type="text" value="" name="nome_instituicao" class=" "/>
                        </li>
                        <li>
                            <label for="uf">UF</label>
                            <select name="uf" class="selectUf"></select>
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
                    <h1>Usuarios</h1>
                    <div class="btAdicionar">Adicionar + </div>
                </div>

                <ul class="lista">
                    <li>
                        <div class="titulo">Titulo</div>
                        <div >Tipo</div>
                        <div class="bts"></div>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</body>
<script>
    function complete(data) {
        dataListar = data.usuarios;

        $('.lista li:gt(0)').remove();
        for (var i in dataListar) {
            var obj = $('<li>\
                <div class="titulo">' + dataListar[i].nome + '</div>\
                <div >' + dataListar[i].tipo_usuario.descricao + '</div>\
                <div class="bts">\
                    <div></div>\
                    <button class="btEditar" >Visualizar</button>\
                    <button class="btExcluir" >X</button>\
                </div>\
            </li>');
            $('.lista').append(obj);
            obj[0].obj = dataListar[i];
        }
        if ($('.lista li').length == 1) {
            $('.lista').append('<li> <div>Nenhum usuario cadastrado</div> </li>');
        }
        
        creatEvents();
    }

    var dataListar = [];
    var page = 'usuario';
    function listar() {
        dataListar = [];
        dispatch('GET', '/api/'+page+'/read.php', '', complete);
    }
    listar();

    dispatch('GET', '/api/tipo_usuario/read.php', '', function(data){
        for(var i in data){
            $('.selectUser').prepend('<option value="'+data[i].id+'" >'+ data[i].descricao +'</option>');
        }
    });
</script>

</html>