<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin - Calendario</title>
    <? include('includes/head.php'); ?>
</head>

<body>

    <div id="content" class="login">

        <div class="contLogin">
            <form class="formLogin">
                <input type="hidden" name="ambiente" value="ADMIN" />
                <ul>
                    <li>
                        <h2>Admin</h2>
                    </li>
                    <li class="error"></li>
                    <li>
                        <label for="nome">Usuario:</label>
                        <input type="text" class="obgt" name="usuario" value="admin_ftd" />
                    </li>
                    <li>
                        <label for="senha">Senha:</label>
                        <input type="password" class="obgt" name="senha" value="senha_ftd" />
                    </li>
                    <li>
                        <button>Enviar</button>
                    </li>
                </ul>
            </form>
        </div>

    </div>
</body>

</html>