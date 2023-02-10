<?php
use Migrations\AbstractMigration;

class AlterAppointmentsAddLexOfficeInvoiceId extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('appointments');

        $table->addColumn('lex_office_invoice_id', 'uuid', [
            'default' => null,
            'null' => true
        ]);

        $table->addIndex(
            ['lex_office_invoice_id'], ['name' => 'idx_appointments_lex_office_invoice_id']
        );

        $table->update();
    }
}
