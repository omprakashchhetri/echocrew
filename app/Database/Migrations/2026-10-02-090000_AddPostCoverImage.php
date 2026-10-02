<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPostCoverImage extends Migration
{
    public function up()
    {
        $this->forge->addColumn('posts', [
            'cover_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cover_alt'   => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('posts', 'cover_image');
        $this->forge->dropColumn('posts', 'cover_alt');
    }
}
