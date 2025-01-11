<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ArticlesTableUuid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('articles', [
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('articles', 'uuid');
    }
}
