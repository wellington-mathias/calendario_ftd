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
                    <h1>Usuarios</h1>
                    <div class="btVoltarEditar">Voltar </div>
                </div>


                <form class="formAdicionar" method="" action="">
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="tipo_usuario" value="" />
                    <ul class="camposAdicionar">
                        <li>
                            <label for="nome">Nome</label>
                            <input type="text" value="" name="nome" class=" obgt"  />
                        </li>
                        <li>
                            <label for="nome">Login</label>
                            <input type="text" value="" name="login" class=" obgt"  />
                        </li>
                        <li>
                            <label for="senha">Senha</label>
                            <input type="text" value="" name="password" class=" obgt"/>
                        </li>
                        <li>
                            <label for="senha">Tipo Usuario</label>
                            <select name="tipo_usuario" class="selectUser" ></select>
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
                        <div>id</div>
                        <div class="titulo">Titulo</div>
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
                <div>' + dataListar[i].id + '</div>\
                <div class="titulo">' + dataListar[i].nome + '</div>\
                <div class="bts">\
                    <button class="btEditar" >Editar</button>\
                    <button class="btExcluir" >X</button>\
                </div>\
            </li>');
            $('.lista').append(obj);
            obj[0].usuario = dataListar[i];
        }
        if ($('.lista li').length == 1) {
            $('.lista').append('<li> <div>Nenhum usuario cadastrado</div> </li>');
        }
        
        creatEvents();
    }

    var dataListar = [];
    var page = 'usuario';
    function listar() {
        dispatch('GET', '/api/'+page+'/read.php', '', complete);
    }
    listar();

    dispatch('GET', '/api/tipo_usuario/read.php', '', function(data){
        for(var i in data){
            $('.selectUser').append('<option value="'+data[i].id+'" >'+ data[i].descricao +'</option>');
        }
    });
</script>

</html>