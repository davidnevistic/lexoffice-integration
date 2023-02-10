<?php

namespace App\Model\Table;


use App\Helper\CalensoHelper;
use App\Helper\LexOfficeHelper;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;


/**
 * Class AppointmentsTable
 * @package App\Model\Table
 */
class AppointmentsTable extends Table
{
    /**
     * @param $data
     * @param $bookingErrors
     * @param $notificationHelperArray
     * @param $partner
     * @param $employee
     * @param $appointment
     * @param $authorization
     * @param $i
     * @throws \Stripe\Error\Api
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function bookAppointments($data, &$bookingErrors, &$notificationHelperArray, $partner, $employee, &$appointment, $authorization, $i)
    {

        // lexOffice invoice
        if ($config['online_payment']['config']['online_payment_enabled'] === true && !empty($data['payment_type'] == 'lex_office_invoice')) {
                $lexOfficeEnabledForAppointments = TableRegistry::getTableLocator()->get('PartnersSettings')->find('bySettingIdAndPartner', [
                    'setting_id' => 295, // is_lex_office_enabled
                    'partner_id' => $partner->id
                ]);

            if (!empty($lexOfficeEnabledForAppointments) && $lexOfficeEnabledForAppointments->value == "1" && $price > 0) {
                // tax
                $taxes = TableRegistry::getTableLocator()->get('AppointmentServices')->calculateTaxesWithCoupon($serviceIds, $coupon);

                if (!empty($taxes)) {
                    $notificationHelper->tax = CalensoHelper::tax(TableRegistry::getTableLocator()->get('AppointmentServices')->calculateTaxesWithCoupon($serviceIds, $coupon), $partner);
                    $notificationHelper->taxInformation = CalensoHelper::taxToString(TableRegistry::getTableLocator()->get('AppointmentServices')->calculateTaxesWithCoupon($serviceIds, $coupon), $partner);
                }

                LexOfficeHelper::init($partner->id);
                // check if contact already exists
                $contact = LexOfficeHelper::searchContacts($partner, $notificationHelper->customer->email);
                if ($contact == false) {
                    $contactId = LexOfficeHelper::createContact($notificationHelper->customer);
                    $invoice = LexOfficeHelper::createInvoice($notificationHelper->customer, $contactId, $notificationHelper->appointmentServices);
                } else {
                    $invoice = LexOfficeHelper::createInvoice($notificationHelper->customer, $contact['id'], $notificationHelper->appointmentServices);
                }

                $invoiceUrl = 'https://app.lexoffice.de/permalink/invoices/view/' . $invoice[0]['id'];

                // save appointment
                $appointment->lex_office_invoice_id = $invoice[0]['id'];
                $appointment->partner_id = $partner->id;
                $appointment->charged_price = $price;
                $appointment->price = $originalPrice;
                $appointment->payment_type = 'lex_office_invoice';
                $appointment->receipt_url = $invoiceUrl;

                $notificationHelper->price = $price;
                TableRegistry::getTableLocator()->get('Appointments')->save($appointment);                            
            }
        }
    }
}