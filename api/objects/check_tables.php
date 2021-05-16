<?php
include_once '../objects/crud_object.php';

class CheckTables extends CrudObject {
    private $db_name;

    public function __constructor() {
        parent::__construct();
    }

    public function init() {
        $this->createStructure();
        $this->createInitalData();
    }

    // create db structure
    private function createStructure() {
        $this->db_name  = $this->conn->query('SELECT database()')->fetchColumn();

        $tbs = ['calendario', 'calendario_evento', 'evento', 'evento_tipo', 'instituicao', 'usuario', 'usuario_tipo'];

        foreach ($tbs as $tb) {
            $this->testTables($tb);
        }

        foreach ($tbs as $tb) {
            $this->testKeys($tb);
        }

        foreach ($tbs as $tb) {
            $this->testAutoincrement($tb);
        }

        foreach ($tbs as $tb) {
            $this->testFK($tb);
        }
    }

    // create inital data structure
    private function createInitalData() {
        $queryInsert = "INSERT INTO evento_tipo (id, descricao) VALUES
        (1, 'Inicio e fim de aula'),
        (2, 'Eventos Professor'),
        (3, 'Feriados'),
        (4, 'Eventos FTD'),
        (5, 'Simulado');";

        $stmt = $this->conn->prepare($queryInsert);
        $stmt->execute();

        $queryInsert = "INSERT INTO usuario_tipo (id, descricao) VALUES
        (1, 'ADMINISTRADOR'),
        (2, 'PROFESSOR');";

        $stmt = $this->conn->prepare($queryInsert);
        $stmt->execute();
            
        $queryInsert = "INSERT INTO usuario (id, nome, login, senha, usuario_tipo_id) VALUES
        (1, 'admin_ftd', 'admin', '". password_hash('admin', PASSWORD_DEFAULT) . "', 1);";
        
        $stmt = $this->conn->prepare($queryInsert);
        $stmt->execute();
    }

    private function tableExists($tableName) {
        $query = "SELECT 1 FROM information_schema.tables WHERE table_schema = '" . $this->db_name . "' AND table_name = '" . $tableName . "' LIMIT 1";

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute() === FALSE) {
            echo "Falha ao verificar se a tabela '" . $tableName . "' existe no DB '" . $this->db_name . "'";
            die();
        }

        $num = $stmt->rowCount();

        if ($num == 0) {
            return false;
        }

        return true;
    }

    private function createTable($tb) {
        if ($tb == 'calendario') {
            $queryCreate = "CREATE TABLE `calendario` (
                `id` int(10) NOT NULL,
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
        } else if ($tb == 'calendario_evento') {
            $queryCreate = "CREATE TABLE `calendario_evento` (
                `calendario_id` int(10) NOT NULL,
                `evento_id` int(10) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            $stmt = $this->conn->prepare($queryCreate);
            $stmt->execute();
        } else if ($tb == 'evento') {
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
        } else if ($tb == 'evento_tipo') {
            $queryCreate = "CREATE TABLE `evento_tipo` (
                `id` tinyint(4) NOT NULL ,
                `descricao` varchar(30) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


            $stmt = $this->conn->prepare($queryCreate);
            $stmt->execute();
        } else if ($tb == 'instituicao') {
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
        } else if ($tb == 'usuario') {
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
        } else if ($tb == 'usuario_tipo') {
            $queryCreate = "CREATE TABLE `usuario_tipo` (
                `id` tinyint(4) NOT NULL ,
                `descricao` varchar(30) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ";

            $stmt = $this->conn->prepare($queryCreate);
            $stmt->execute();
        }
    }

    private function indexExists($tableName, $indexName) {
        $query = "SHOW KEYS FROM " . $tableName . " WHERE Key_name = '" . $indexName . "'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return false;
        }

        return true;
    }

    private function createPrimaryKey($tableName, $colunmName) {
        $query = "ALTER TABLE " . $tableName . " ADD PRIMARY KEY (" . $colunmName . ") USING BTREE";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    private function createKey($tableName, $indexName, $colunmName) {
        $query = "ALTER TABLE " . $tableName . " ADD KEY " . $indexName . " (" . $colunmName . ") USING BTREE";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    private function createUniqueKey($tableName, $indexName, $colunmNames) {
        $query = "ALTER TABLE " . $tableName . " ADD UNIQUE KEY " . $indexName . " (" . implode(", ", $colunmNames) . ") USING BTREE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    private function foreignkeyExists($tableName, $indexName) {
        $query = "SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = '" . $this->db_name . "' AND TABLE_NAME = '" . $tableName . "' AND CONSTRAINT_NAME = '$indexName'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return false;
        }

        return true;
    }

    private function createForeignKey($tableName, $tableIndex, $foreignKeyName, $foreignTableName, $foreignTableIndex, $onDeleteCascade, $onUpdateCascade) {
        $query = "ALTER TABLE " . $tableName . " ADD CONSTRAINT " . $tableIndex . " FOREIGN KEY (" . $foreignKeyName . ") REFERENCES " . $foreignTableName . " (" . $foreignTableIndex . ")";

        if ($onDeleteCascade) $query .= " ON DELETE CASCADE";
        if ($onUpdateCascade)$query .= " ON UPDATE CASCADE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    function autoIncrementExists($tableName, $colunmName) {
        $query = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $this->db_name . "' AND TABLE_NAME = '" . $tableName . "' AND COLUMN_NAME = '" . $colunmName . "' AND EXTRA like '%auto_increment%'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return false;
        }

        return true;
    }

    private function createAutoiIncrement($tableName, $colunmName, $colunmType, $colunmSize) {
        $query = "ALTER TABLE " . $tableName . " MODIFY " . $colunmName . " " . $colunmType . "(" . $colunmSize . ") NOT NULL AUTO_INCREMENT";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    private function testTables($tableName) {
        if (!$this->tableExists($tableName)) $this->createTable($tableName);
    }

    private function testKeys($tableName) {
        if ($tableName == 'calendario') {
            if (!$this->indexExists($tableName, "PRIMARY")) $this->createPrimaryKey($tableName, "id");
            if (!$this->indexExists($tableName, "calendario_usuario_id")) $this->createKey($tableName, "calendario_usuario_id", "usuario_id");
        } else if ($tableName == 'calendario_evento') {
            if (!$this->indexExists($tableName, "calendario_evento")) $this->createUniqueKey($tableName, "calendario_evento", array("calendario_id", "evento_id"));
            if (!$this->indexExists($tableName, "calendario_id")) $this->createKey($tableName, "calendario_id", "calendario_id");
            if (!$this->indexExists($tableName, "evento_id")) $this->createKey($tableName, "evento_id", "evento_id");
        } else if ($tableName == 'evento') {
            if (!$this->indexExists($tableName, "PRIMARY")) $this->createPrimaryKey($tableName, "id");
            if (!$this->indexExists($tableName, "evento_tipo_id")) $this->createKey($tableName, "evento_tipo_id", "evento_tipo_id");
        } else if ($tableName == 'evento_tipo') {
            if (!$this->indexExists($tableName, "PRIMARY")) $this->createPrimaryKey($tableName, "id");
        } else if ($tableName == 'instituicao') {
            if (!$this->indexExists($tableName, "PRIMARY")) $this->createPrimaryKey($tableName, "id");
        } else if ($tableName == 'usuario') {
            if (!$this->indexExists($tableName, "PRIMARY")) $this->createPrimaryKey($tableName, "id");
            if (!$this->indexExists($tableName, "login")) $this->createUniqueKey($tableName, "login", array("login", "usuario_tipo_id"));
            if (!$this->indexExists($tableName, "login_ftd")) $this->createUniqueKey($tableName, "login_ftd", array("login_ftd", "usuario_tipo_id"));
            if (!$this->indexExists($tableName, "usuario_tipo_id")) $this->createKey($tableName, "usuario_tipo_id", "usuario_tipo_id");
            if (!$this->indexExists($tableName, "usuario_instituicao_id")) $this->createKey($tableName, "usuario_instituicao_id", "instituicao_id");
        } else if ($tableName == 'usuario_tipo') {
            if (!$this->indexExists($tableName, "PRIMARY")) $this->createPrimaryKey($tableName, "id");
        }
    }

    private function testFK($tableName) {
        if ($tableName == 'calendario') {
            if (!$this->foreignkeyExists($tableName, "calendario_usuario_id")) $this->createForeignKey($tableName, "calendario_usuario_id", "usuario_id", "usuario", "id", true, true);
        } if ($tableName == 'calendario_evento') {
            if (!$this->foreignkeyExists($tableName, "calendario_id")) $this->createForeignKey($tableName, "calendario_id", "calendario_id", "calendario", "id", true, true);
            if (!$this->foreignkeyExists($tableName, "evento_id")) $this->createForeignKey($tableName, "evento_id", "evento_id", "evento", "id", true, true);
        } if ($tableName == 'evento') {
            if (!$this->foreignkeyExists($tableName, "evento_tipo_id")) $this->createForeignKey($tableName, "evento_tipo_id", "evento_tipo_id", "evento_tipo", "id", false, true);
        } if ($tableName == 'usuario') {
            if (!$this->foreignkeyExists($tableName, "usuario_instituicao_id")) $this->createForeignKey($tableName, "usuario_instituicao_id", "instituicao_id", "instituicao", "id", false, true);
            if (!$this->foreignkeyExists($tableName, "usuario_tipo_id")) $this->createForeignKey($tableName, "usuario_tipo_id", "usuario_tipo_id", "usuario_tipo", "id", false, true);
        }
    }

    private function testAutoincrement($tableName) {
        if ($tableName == 'calendario') {
            if (!$this->autoIncrementExists($tableName, "id")) $this->createAutoiIncrement($tableName, "id", "int", "10");
        } if ($tableName == 'evento') {
            if (!$this->autoIncrementExists($tableName, "id")) $this->createAutoiIncrement($tableName, "id", "int", "11");
        } if ($tableName == 'evento_tipo') {
            if (!$this->autoIncrementExists($tableName, "id")) $this->createAutoiIncrement($tableName, "id", "tinyint", "4");
        } if ($tableName == 'instituicao') {
            if (!$this->autoIncrementExists($tableName, "id")) $this->createAutoiIncrement($tableName, "id", "int", "10");
        } if ($tableName == 'usuario') {
            if (!$this->autoIncrementExists($tableName, "id")) $this->createAutoiIncrement($tableName, "id", "int", "10");
        } if ($tableName == 'usuario_tipo') {
            if (!$this->autoIncrementExists($tableName, "id")) $this->createAutoiIncrement($tableName, "id", "tinyint", "4");
        }
    }
}

$tables = new CheckTables();
$tables->init();