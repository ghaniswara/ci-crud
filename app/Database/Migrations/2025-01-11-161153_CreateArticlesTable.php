<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        // Create the 'articles' table
        $this->forge->addField([
            'id'            => [
                'type'           => 'INT',
                'unsigned'      => true,
                'auto_increment' => true,
            ],
            'title'         => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'slug'          => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'content'       => [
                'type' => 'TEXT',
            ],
            'author'        => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'published_date' => [
                'type' => 'DATETIME',
            ],
            'object_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');

        $this->forge->createTable('articles');
    }

    public function down()
    {
        $this->forge->dropTable('articles');
    }
}
