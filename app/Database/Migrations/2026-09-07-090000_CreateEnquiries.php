<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnquiries extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 120],
            'company'           => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 180],
            'phone'             => ['type' => 'VARCHAR', 'constraint' => 40,  'null' => true],
            'service'           => ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true],
            'current_system'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'budget'            => ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true],
            'timeline'          => ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true],
            'preferred_contact' => ['type' => 'VARCHAR', 'constraint' => 40,  'null' => true],
            'message'           => ['type' => 'TEXT'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->createTable('enquiries');
    }

    public function down()
    {
        $this->forge->dropTable('enquiries');
    }
}
