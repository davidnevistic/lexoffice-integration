<?php

namespace App\Helper;

use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Google\Service\Dfareporting\Resource\Countries;

/**
 *
 */
class LexOfficeHelper
{
    public static $baseUrl = 'https://api.lexoffice.io/v1';
    public static $httpClient;

    public static function init($partnerId) {
        $isLexOfficeEnabled = TableRegistry::getTableLocator()->get("PartnersSettings")->getValueForSetting("is_lex_office_enabled", $partnerId);

        if (intval($isLexOfficeEnabled) == 0) {
            throw new BadRequestException('LexOffice is not enabled.');
        } else {
            $lexOfficeApiKey = TableRegistry::getTableLocator()->get("PartnersSettings")->getValueForSetting("lex_office_api_key", $partnerId);

            if (empty($lexOfficeApiKey)) {
                throw new BadRequestException('LexOffice API key is missing.');
            } else {
                self::$httpClient = new Client([
                    'headers' => [
                        'Authorization' => 'Bearer ' . $lexOfficeApiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
                ]);
            }
        }
    }

    public static function searchContacts($partner, $email) {
        self::init($partner->id);

        $url = self::$baseUrl . '/contacts?email=' . $email;

        $response = self::$httpClient->get($url);
        $jsonResponse = $response->getJson();
        //Log::error('searchContact ');
        //Log::error($jsonResponse);

        if (empty($jsonResponse['content'][0]['id'])) {
            //throw new NotFoundException('This contact does not exist.');
            return false;
        } else {
            //Log::error($jsonResponse['content'][0]);
            return $jsonResponse['content'][0];
        }
    }

    public static function createContact($customer) {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Zurich'));

        if (!empty($customer->gender)) {
            if ($customer->gender == 'm') {
                $gender = 'Herr';
            } else if ($customer->gender == 'f') {
                $gender = 'Frau';
            } else if ($customer->gender == 'o') {
                $gender = 'andere';
            } else {
                $gender = 'n/a';
            }
        } else {
            $gender = 'n/a';
        }

        $data = [
            "version" => 0,
            "roles" => [
                "customer" => [
                    "number" => null
                ]
            ],
            "person" => [
                "salutation" => $gender,
                "firstName" => $customer->prename,
                "lastName" => $customer->lastname
            ],
            "emailAddresses" => [
                "business" => [
                    $customer->email
                ]
            ],
            "note" => __("Automatisch erstellt durch Calenso am {0} um {1} Uhr.", $now->format('d.m.Y'), $now->format('H:i'))
        ];

        if(!empty($customer->phone)) {
            $data["phoneNumbers"] = [
                "business" => [
                    $customer->phone
                ]
            ];
        }

        $url = self::$baseUrl . '/contacts';
        
        $response = self::$httpClient->post($url, json_encode($data));
        $jsonResponse = $response->getJson();
        //Log::error($jsonResponse);
        return $jsonResponse['id'];
    }

    public static function createInvoice($customer, $contactId, $appointmentServices) {
        $now = date("Y-m-d") . "T" . date("H:i:s.BP");

        $country = TableRegistry::getTableLocator()->get("Countries")->find('all')
            ->where([
                "Countries.id" => $customer->country_id
            ])
            ->first();

        $data = [
            "archived" => false,
            "voucherDate" => $now,
            "address" => [
                "contactId" => $contactId,
                "name" => $customer->company_name,
                "street" => $customer->street,
                "city" => $customer->city,
                "zip" => $customer->zip,
                "countryCode" => strtoupper($country->identifier)
            ],
            "lineItems" => [],
            "totalPrice" => [
                "currency" => "EUR"
            ],
            "taxConditions" => [
                "taxType" => "net"
            ],
            "shippingConditions" => [
                "shippingDate" => $now,
                "shippingType" => "none"
            ],
            "title" => "Rechnung",
            "introduction" => "Ihre bestellten Positionen stellen wir Ihnen hiermit in Rechnung",
            "remark" => "Vielen Dank für Ihren Einkauf"
        ];

        // appointment services
        foreach ($appointmentServices as $service) {
            $p = [
                    "type" => "custom",
                    "name" => $service->name,
                    "quantity" => 1,
                    "unitName" => "Dienstleistung",
                    "unitPrice" => [
                        "currency" => "EUR", // The currency of the total price. Currently only EUR is supported.
                        "netAmount" => $service->price,
                        "taxRatePercentage" => 0
                    ],
                    "discountPercentage" => 0
                ];

            array_push($data['lineItems'], $p);
        }

        $url = self::$baseUrl . '/invoices';
        
        //Log::error(json_encode($data));
        $response = self::$httpClient->post($url, json_encode($data));
        $jsonResponse = $response->getJson();
        //Log::error($jsonResponse['id']);
        $invoice = self::getInvoice($jsonResponse['id']);
        return $invoice;
    }

    public static function getInvoice($invoiceId) {
        $url = self::$baseUrl . '/invoices/' . $invoiceId;
        
        $response = self::$httpClient->get($url);
        $jsonResponse = $response->getJson();
        //Log::error($jsonResponse);
    }
}
