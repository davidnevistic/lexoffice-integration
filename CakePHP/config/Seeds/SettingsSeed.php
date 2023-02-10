<?php

use Migrations\AbstractSeed;

/**
 * Settings seed.
 */
class SettingsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '295',
                'key' => 'is_lex_office_enabled',
                'default_value' => 0,
                'category' => 'setting',
                'type' => 'boolean'
            ],
            [
                'id' => '296',
                'key' => 'lex_office_api_key',
                'default_value' => null,
                'category' => 'setting',
                'type' => 'string'
            ]
        ];

        $table = $this->table('settings');
        $this->execute('DELETE FROM settings');
        $table->insert($data)->save();
    }
}
