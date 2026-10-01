<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlogManagementColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('posts', [
            'excerpt'      => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'published_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addColumn('comments', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'approved'],
        ]);

        $this->forge->addColumn('enquiries', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('posts', 'excerpt');
        $this->forge->dropColumn('posts', 'published_at');
        $this->forge->dropColumn('comments', 'status');
        $this->forge->dropColumn('enquiries', 'status');
    }
}
