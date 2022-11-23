<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use App\Models\RedisGtf;
use Illuminate\Console\Command;
use App\Services\MailChimpService;
use Carbon\Carbon;
use Exception;

class UploadMemberToMailChimp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:push_customer_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Api cap nhap thong tin khach hang';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $siteDomain = 'https://thecreattify.com';
        $storeID = 'store_k6gosw5gwhooezt3i61m';
        $pageOrder = 0;
        $listId = '6fe56ad500'; // ID list member

        $mailchimp = new MailChimpService();

        // $b = $mailchimp->service->lists->getAllLists(); //6fe56ad500
        $b = $mailchimp->service->lists->getListMembersInfo('6fe56ad500'); //6fe56ad500
        // $b = $mailchimp->service->lists->getListMember('6fe56ad500', md5('hatv1592@gmail.com')); //6fe56ad500
        // $b = $mailchimp->service->lists->setListMember('6fe56ad500', md5('hatv1592@gmail.com'), [
        //     "email_address" => "hatv1592@gmail.com",
        //     "status" => "pending",
        //     "merge_fields" => [
        //       "FNAME" => "Elinore",
        //       "LNAME" => "Grady",
        //       "BIRTHDAY" => "01/22",
        //       "ADDRESS" => [
        //           "addr1" => "123 Freddie Ave",
        //           "city" => "Atlanta",
        //           "state" => "GA",
        //           "zip" => "12345",
        //       ],
        //       "PHONE"=> "0376874179"
        //     ]
        // ]);
        dd($b);

        do {
            $pageOrder++;
            $data = RedisGtf::getRedisOrdersList(1, [], 'completed', $pageOrder, 1000, true);
            foreach ($data['results'] as $v) {
                // Import or update Customer
                try{
                    $mailchimp->service->ecommerce->setStoreCustomer($storeID, $v["email"], [
                        "id" => $v["email"],
                        "email_address" => $v["email"],
                        "opt_in_status" => true,
                        "first_name" => $v["address"]["firstName"],
                        "last_name" => $v["address"]["lastName"],
                        "phone" => $v["address"]["phone"],
                        "address" => [
                            'address1' => $v["address"]["address1"] ?? '',
                            'city' => $v["address"]["city"] ?? '',
                            'postal_code' => $v["address"]["postcode"] ?? '',
                            'country_code' => $v["address"]["country"] ?? '',
                            'province' => $v["address"]["state"] ?? ''
                        ]
                    ]
                    );
                    dump('Insert/update sucsess customer: "'. $v["id"] .'" email: ' . $v["email"]);
                }catch(Exception $e){
                    dump('Insert/update false customer: "'. $v["id"] .'" email: ' . $v["email"]);
                }

                 // Import or update member
                try{
                    $customerInfo = [
                        "id" => $v["email"],
                        "email_address" => $v["email"],
                        "status"=> "subscribed", //Possible values : "subscribed", "unsubscribed", "cleaned", "pending", or "transactional".
                        "address" => [
                            'address1' => $v["address"]["address1"] ?? '',
                            'city' => $v["address"]["city"] ?? '',
                            'postal_code' => $v["address"]["postcode"] ?? '',
                            'country_code' => $v["address"]["country"] ?? '',
                            'province' => $v["address"]["state"] ?? ''
                        ],
                        "merge_fields" => [
                            "FNAME" => $v["address"]["firstName"] ?? '',
                            "LNAME" => $v["address"]["lastName"] ?? '',
                            "ADDRESS" => [
                                'addr1' => $v["address"]["address1"] ?? '',
                                'city' => $v["address"]["city"] ?? '',
                                'zip' => $v["address"]["postcode"] ?? '',
                                'country' => $v["address"]["country"] ?? '',
                                'state' => $v["address"]["state"] ?? ''
                            ],
                            "PHONE"=> $v["address"]["phone"] ?? ''
                        ]
                    ];
                    $mailchimp->service->lists->setListMember($listId, md5($v["email"]), $customerInfo);
                    dump('Insert/update sucsess member: "'. $v["id"] .'" email: ' . $v["email"]);
                }catch(Exception $e){
                    dump('Insert/update false member: "'. $v["id"] .'" email: ' . $v["email"]);
                }
            }
            break;
        } while (empty($data) === false);
        // sleep(10);
    }
}
