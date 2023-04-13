<?php

namespace App\Services;

class OrdersService
{
    public function byBusiness($query) {
        return $query->where('business', $data['business_id']);
    }

    /**
     * @param $transactionId
     * @return mixed
     */
    public function checkTransaction($transactionId)
    {
        $username = config('services.bank.username');
        $password = config('services.bank.password');
        $url = config('services.bank.url');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url . "getOrderStatusExtended.do",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => array(
                'userName'    => $username,
                'password'    => $password,
                'orderId' => $transactionId,
            ),
        ));

        $response = curl_exec($curl);
        return json_decode($response);
    }
}
