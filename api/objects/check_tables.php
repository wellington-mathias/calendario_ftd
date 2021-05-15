<?php
include_once '../objects/crud_object.php';

class CheckTables extends CrudObject
{

    public function __constructor()
    {
        parent::__construct();
    }

    // create method
    public function init()
    {
        $tbs = ['calendario', 'calendario_evento', 'evento', 'evento_tipo', 'instituicao', 'usuario', 'usuario_tipo'];
        for ($i = 0, $size = count($tbs); $i < $size; ++$i) {
            $this->testTb($tbs[$i]);
        }

        $this->testKeys();
    }

    private function testTb($tb)
    {

        if ($tb == 'calendario') {

            $query = "select 1 from `calendario` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `calendario` (
                `id` int(10) NOT NULL ,
                `ano_referencia` smallint(6) NOT NULL,
                `dt_inicio_ano_letivo` date NOT NULL,
                `dt_fim_ano_letivo` date NOT NULL,
                `dt_inicio_recesso` date NOT NULL,
                `dt_fim_recesso` date NOT NULL,
                `qtde_volumes_1o_ano` tinyint(4) NOT NULL,
                `qtde_volumes_2o_ano` tinyint(4) NOT NULL,
                `qtde_volumes_3o_ano` tinyint(4) NOT NULL,
                `revisao_volume_3o_ano` tinyint(4) NOT NULL,
                `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                `usuario_id` int(10) NOT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();
            }
        } else if ($tb == 'calendario_evento') {
            $query = "select 1 from `calendario_evento` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `calendario_evento` (
                    `calendario_id` int(10) NOT NULL,
                    `evento_id` int(10) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();
            }
        } else if ($tb == 'evento') {
            $query = "select 1 from `evento` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `evento` (
                    `id` int(11) NOT NULL ,
                    `dt_inicio` date NOT NULL,
                    `dt_fim` date NOT NULL,
                    `titulo` varchar(80) NOT NULL,
                    `descricao` text,
                    `uf` char(2) DEFAULT NULL,
                    `dia_letivo` tinyint(1) NOT NULL,
                    `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    `evento_tipo_id` tinyint(4) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();
            }
        } else if ($tb == 'evento_tipo') {
            $query = "select 1 from `evento_tipo` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `evento_tipo` (
                    `id` tinyint(4) NOT NULL ,
                    `descricao` varchar(30) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();

                $queryInsert = "INSERT INTO `evento_tipo` (`id`, `descricao`) VALUES
                (1, 'Inicio e fim de aula'),
                (2, 'Eventos Professor'),
                (3, 'Feriados'),
                (4, 'Eventos FTD'),
                (5, 'Simulado');";


                $stmt = $this->conn->prepare($queryInsert);
                $stmt->execute();
            }
        } else if ($tb == 'instituicao') {
            $query = "select 1 from `instituicao` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `instituicao` (
                    `id` int(10) NOT NULL ,
                    `nome` varchar(80) DEFAULT NULL,
                    `logo` mediumblob,
                    `logo_content_type` varchar(30) DEFAULT NULL,
                    `uf` char(2) DEFAULT NULL,
                    `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();
            }
        } else if ($tb == 'usuario') {
            $query = "select 1 from `usuario` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `usuario` (
                    `id` int(10) NOT NULL ,
                    `nome` varchar(60) DEFAULT NULL,
                    `email` varchar(150) DEFAULT NULL,
                    `login` varchar(255) DEFAULT NULL,
                    `senha` varchar(255) DEFAULT NULL,
                    `login_ftd` varchar(255) DEFAULT NULL,
                    `senha_ftd` varchar(255) DEFAULT NULL,
                    `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    `usuario_tipo_id` tinyint(4) NOT NULL,
                    `instituicao_id` int(10) DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();


                $queryInsert = "INSERT INTO `usuario` (`id`, `nome`, `email`, `login`, `senha`, `login_ftd`, `senha_ftd`, `dt_criacao`, `dt_alteracao`, `usuario_tipo_id`, `instituicao_id`) VALUES
                (1, 'admin_ftd', NULL, 'admin', '$2y$10$WU1eKY9rC3MY4xwMlRBMSeHcLbFLzu9o6foKKGHVPkb214jfamz..', '', NULL, '2020-12-06 16:28:08', '2021-03-23 17:57:06', 1, NULL);";

                $stmt = $this->conn->prepare($queryInsert);
                $stmt->execute();
            }
        } else if ($tb == 'usuario_tipo') {
            $query = "select 1 from `usuario_tipo` LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute() === FALSE) {
                $queryCreate = "CREATE TABLE `usuario_tipo` (
                    `id` tinyint(4) NOT NULL ,
                    `descricao` varchar(30) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                  ";

                $stmt = $this->conn->prepare($queryCreate);
                $stmt->execute();



                $queryInsert = "INSERT INTO `usuario_tipo` (`id`, `descricao`) VALUES
                (1, 'ADMINISTRADOR'),
                (2, 'PROFESSOR');";

                $stmt = $this->conn->prepare($queryInsert);
                $stmt->execute();
            }
        }
    }


    private function testKeys()
    { 



        ////////calendario
        $query = "SHOW KEYS FROM calendario WHERE Key_name = 'PRIMARY'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "ALTER TABLE `calendario`
            ADD PRIMARY KEY (`id`),
            ADD KEY `calendario_usuario_id` (`usuario_id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }


        ////////calendario_evento
        $query = "SHOW KEYS FROM calendario_evento WHERE Key_name = 'calendario_evento' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            echo 'zero';
            $query = "ALTER TABLE `calendario_evento`
             ADD UNIQUE KEY `calendario_evento` (`calendario_id`,`evento_id`) USING BTREE,
             ADD KEY `calendario_id` (`calendario_id`) USING BTREE,
             ADD KEY `evento_id` (`evento_id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }


        ////////evento
        $query = "SHOW KEYS FROM evento WHERE Key_name = 'evento_tipo_id' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "ALTER TABLE `evento`
            ADD PRIMARY KEY (`id`),
            ADD KEY `evento_tipo_id` (`evento_tipo_id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }

        ////////evento_tipo
        $query = "SHOW KEYS FROM evento_tipo WHERE Key_name = 'PRIMARY' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "ALTER TABLE `evento_tipo`
            ADD PRIMARY KEY (`id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }


        ////////instituicao
        $query = "SHOW KEYS FROM instituicao WHERE Key_name = 'PRIMARY' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "ALTER TABLE `instituicao`
            ADD PRIMARY KEY (`id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }


        ////////usuario
        $query = "SHOW KEYS FROM usuario WHERE Key_name = 'PRIMARY' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "ALTER TABLE `usuario`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `login` (`login`,`usuario_tipo_id`) USING BTREE,
            ADD UNIQUE KEY `login_ftd` (`login_ftd`,`usuario_tipo_id`) USING BTREE,
            ADD KEY `usuario_tipo_id` (`usuario_tipo_id`),
            ADD KEY `usuario_instituicao_id` (`instituicao_id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }


        ////////usuario_tipo
        $query = "SHOW KEYS FROM usuario_tipo WHERE Key_name = 'PRIMARY' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "ALTER TABLE `usuario_tipo`
            ADD PRIMARY KEY (`id`);";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }

        

        ////CONSTRAINT

        $query = "ALTER TABLE `calendario`
        ADD CONSTRAINT `calendario_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "ALTER TABLE `calendario_evento`
        ADD CONSTRAINT `calendario_id` FOREIGN KEY (`calendario_id`) REFERENCES `calendario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `evento_id` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "ALTER TABLE `evento`
        ADD CONSTRAINT `evento_tipo_id` FOREIGN KEY (`evento_tipo_id`) REFERENCES `evento_tipo` (`id`) ON UPDATE CASCADE;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "ALTER TABLE `usuario`
        ADD CONSTRAINT `usuario_instituicao_id` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicao` (`id`) ON UPDATE CASCADE,
        ADD CONSTRAINT `usuario_tipo_id` FOREIGN KEY (`usuario_tipo_id`) REFERENCES `usuario_tipo` (`id`) ON UPDATE CASCADE;
      COMMIT";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        
        $query = "ALTER TABLE `calendario`
        MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        echo "\nPDO::errorInfo():\n";
        print_r($stmt->errorInfo());
        /* 
        $query = "ALTER TABLE `evento`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        echo "\nPDO::errorInfo():\n";
        print_r($stmt->errorInfo());
        
        $query = "ALTER TABLE `evento_tipo`
        MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        echo "\nPDO::errorInfo():\n";
        print_r($stmt->errorInfo());
        
        $query = "ALTER TABLE `instituicao`
        MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "ALTER TABLE `usuario`
        MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        echo "\nPDO::errorInfo():\n";
        print_r($stmt->errorInfo());
        
        $query = "ALTER TABLE `usuario_tipo`
        MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        echo "\nPDO::errorInfo():\n";
        print_r($stmt->errorInfo()); */



        $query = "DROP TABLE usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "DROP TABLE calendario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "DROP TABLE evento";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "DROP TABLE evento_tipo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "DROP TABLE instituicao";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "DROP TABLE usuario_tipo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        

        
    }
}



/* $query = "IF OBJECT_ID('dbo.[CK_ConstraintName]', 'C') IS NULL 
        ALTER TABLE dbo.[tablename] DROP CONSTRAINT CK_ConstraintName"; */

$tables = new CheckTables();
$tables->init();
