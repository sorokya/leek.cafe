<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DefaultUser extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'username' => 'admin',
                'password_hash' => password_hash('password', PASSWORD_ARGON2ID),
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->table('users')->insert($data)->saveData();
    }
}
