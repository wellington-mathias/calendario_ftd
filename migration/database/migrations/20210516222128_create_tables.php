<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {

        $exist = $this->hasTable("evento_tipo");
        if (!$exist) {
            $table = $this->table("evento_tipo");
            $table->addColumn('descricao', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->create();
        }

        $exist = $this->hasTable("usuario_tipo");
        if (!$exist) {
            $table = $this->table("usuario_tipo");
            $table->addColumn('descricao', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->create();
        }


        $exist = $this->hasTable("instituicao");
        if (!$exist) {
            $table = $this->table("instituicao");
            $table->addColumn('nome', 'char', ['default' => NULL])
                ->addColumn('logo', 'mediumblob')
                ->addColumn('logo_content_type', 'char', ['default' => NULL])
                ->addColumn('uf', 'char', ['limit' => 2, 'default' => NULL, 'null' => true])
                ->addTimestamps('dt_criacao', 'dt_alteracao')
                ->create();
        }


        $exist = $this->hasTable("calendario");
        if (!$exist) {
            $table = $this->table("calendario");
            $table->addColumn('ano_referencia', 'integer', ['limit' => 6])
                ->addColumn('dt_inicio_ano_letivo', 'date')
                ->addColumn('dt_fim_ano_letivo', 'date')
                ->addColumn('dt_inicio_recesso', 'date')
                ->addColumn('dt_fim_recesso', 'date')
                ->addColumn('qtde_volumes_1o_ano', 'integer', ['limit' => 4])
                ->addColumn('qtde_volumes_2o_ano', 'integer', ['limit' => 4])
                ->addColumn('qtde_volumes_3o_ano', 'integer', ['limit' => 4])
                ->addColumn('revisao_volume_3o_ano', 'integer', ['limit' => 4])
                ->addTimestamps('dt_criacao', 'dt_alteracao')
                ->addColumn('usuario_id', 'integer', ['limit' => 10])
                ->create();
        }


        $exist = $this->hasTable('calendario_evento');
        if (!$exist) {
            $table = $this->table('calendario_evento', ['id' => false]);
            $table->addColumn('calendario_id', 'integer', ['limit' => 10])
                ->addColumn('evento_id', 'integer', ['limit' => 10])
                ->addIndex(['calendario_id', 'evento_id'], ['unique' => true, 'name' => 'calendario_evento'])
                ->addIndex(['calendario_id'], ['unique' => false, 'name' => 'calendario_id'])
                ->addIndex(['evento_id'], ['unique' => false, 'name' => 'evento_id'])
                ->create();
        }


        $exist = $this->hasTable("evento");
        if (!$exist) {
            $table = $this->table("evento");
            $table->addColumn('ano_referencia', 'integer', ['limit' => 50])
                ->addColumn('dt_inicio', 'date')
                ->addColumn('dt_fim', 'date')
                ->addColumn('titulo', 'char')
                ->addColumn('descricao', 'text')
                ->addColumn('uf', 'char', ['limit' => 2, 'default' => NULL, 'null' => true])
                ->addColumn('dia_letivo', 'integer', ['limit' => 1])
                ->addTimestamps('dt_criacao', 'dt_alteracao')
                ->addColumn('evento_tipo_id', 'integer', ['limit' => 4])
                ->addIndex(['evento_tipo_id'], ['unique' => false, 'name' => 'evento_tipo_id'])
                ->create();
        }


        $exist = $this->hasTable("usuario");
        if (!$exist) {
            $table = $this->table("usuario");
            $table->addColumn('nome', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->addColumn('email', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->addColumn('login', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->addColumn('senha', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->addColumn('login_ftd', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->addColumn('senha_ftd', 'char', ['limit' => 255, 'default' => NULL, 'null' => true])
                ->addColumn('usuario_tipo_id', 'integer', ['limit' => 4])
                ->addColumn('instituicao_id', 'integer', ['limit' => 10, 'default' => NULL, 'null' => true])
                ->addTimestamps('dt_criacao', 'dt_alteracao')
                ->addIndex(['login', 'usuario_tipo_id'], ['name' => 'login', 'unique' => true])
                ->addIndex(['login_ftd', 'usuario_tipo_id'], ['name' => 'login_ftd', 'unique' => true])
                ->addIndex(['usuario_tipo_id'], ['unique' => false, 'name' => 'usuario_tipo_id'])
                ->addIndex(['instituicao_id'], ['unique' => false, 'name' => 'usuario_instituicao_id'])
                ->create();

            $this->foreignKeys();
        }
    }

    private function foreignKeys()
    {
        $table = $this->table('calendario');
        $exists = $table->hasForeignKey('calendario_usuario_id');
        if (!$exists) {
            $table->addForeignKey('usuario_id', 'usuario', 'id', ['constraint' => 'calendario_usuario_id', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->update();
        }

        $table = $this->table('calendario_evento');
        $exists = $table->hasForeignKey('calendario_id');
        if (!$exists) {
            $table->addForeignKey('calendario_id', 'calendario', 'id', ['constraint' => 'calendario_id', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('evento_id', 'evento', 'id', ['constraint' => 'evento_id', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->update();
        }

        $table = $this->table('usuario');
        $exists = $table->hasForeignKey('usuario_instituicao_id');
        if (!$exists) {
            $table->addForeignKey('instituicao_id', 'instituicao', 'id', ['constraint' => 'usuario_instituicao_id', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('usuario_tipo_id', 'usuario_tipo', 'id', ['constraint' => 'usuario_tipo_id', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->update();
        }

        $table = $this->table('evento');
        $exists = $table->hasForeignKey('evento_tipo_id');
        if (!$exists) {
            $table->addForeignKey('evento_tipo_id', 'evento_tipo', 'id', ['constraint' => 'evento_tipo_id', 'update' => 'CASCADE'])
                ->update();
        }
        $this->initialData();
    }
    private function initialData()
    {

        $data = [
            ['id' => 1, 'descricao' => 'Inicio e fim de aula'],
            ['id' => 2, 'descricao' => 'Eventos Professor'],
            ['id' => 3, 'descricao' => 'Feriados'],
            ['id' => 4, 'descricao' => 'Eventos FTD'],
            ['id' => 5, 'descricao' => 'Simulado']
        ];
        $this->table('evento_tipo')->insert($data)->saveData();

        $data = [
            ['id' => 1, 'descricao' => 'ADMINISTRADOR'],
            ['id' => 2, 'descricao' => 'PROFESSOR']
        ];
        $this->table('usuario_tipo')->insert($data)->saveData();


        $data = [
            'nome' => 'admin_ftd',
            'email' => '',
            'login' => 'ADMINISTRADOR',
            'senha' => password_hash('admin', PASSWORD_DEFAULT),
            'login_ftd' => '',
            'senha_ftd' => '',
            'usuario_tipo_id' => '1'
        ];


        $this->table('usuario')->insert($data)->saveData();
    }
}
