<?php

namespace App\Libraries;

// Hamrahpay PHP Sample
// Version : 1
class Hamrahpay
{
    private static $instance = null;
    private $api_version    = "v1";
    private $api_key      = "null";
    private $api_url        = 'https://api.hamrahpay.com/api';
    private $second_api_url = 'https://api.hamrahpay.ir/api';
    private $params         = [];
    private $pay_request_format = 'raw'; // qr code
    private $pay_url         = null;
    public function __construct($api_key)
    {
        $this->api_url          .= '/'.$this->api_version;
        $this->second_api_url   .= '/'.$this->api_version;
        $this->params['api_key'] = $api_key;
        $this->api_key           = $api_key;
    }

    public static function Instance($api_key)
    {
        if (self::$instance == null)
        {
            self::$instance = new Hamrahpay($api_key);
        }

        return self::$instance;
    }
    // This method Requests payment token
    public function PaymentRequest()
    {
        $result = $this->post_data($this->getApiUrl('/rest/pg/pay-request/'.$this->pay_request_format));
        $result = json_decode($result,true);
        if ($result['status']==1)
            $this->pay_url = $result['pay_url'];

        return $result;
    }

    // This method check that the payment_token is paid or not
    public function VerifyPayment($payment_token)
    {
        $this->params = [];
        $this->params['api_key'] = $this->api_key;
        $this->params['payment_token'] = $payment_token;
        $result =  $this->post_data($this->getApiUrl('/rest/pg/verify'));
        return json_decode($result);
    }

    // This method returns unverified payments
    public function getUnverifiedTransactions()
    {
        $this->params = [];
        $this->params['api_key'] = $this->api_key;
        $result = $this->post_data($this->getApiUrl('/rest/pg/get-unverfied-payments'));
        return json_decode($result);
    }

    // This method sends the data to api
    private function post_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $result = curl_exec($ch);
        //echo curl_error($ch);
        curl_close($ch);

        return $result;
    }

    // This method returns the api url
    private function getApiUrl($end_point,$use_emergency_url=false)
    {
        if (!$use_emergency_url)
            return $this->api_url.$end_point;
        else
        {
            return $this->second_api_url.$end_point;
        }
    }

    // This method sets the payment amount
    public function Amount($amount)
    {
        $this->params['amount'] = intval($amount);
        return $this;
    }

    // This method sets the payment allowed cards
    public function AllowedCards($cards=[])
    {
        $this->params['allowed_cards'] = json_encode($cards);
        return $this;
    }

    // This method sets the payment merchants wages in shared IPGs
    public function Wages($wages=[])
    {
        $this->params['wages'] = json_encode($wages);
        return $this;
    }

    // This method sets the callback url
    public function CallbackUrl($url)
    {
        $this->params['callback_url'] = $url;
        return $this;
    }

    // This method sets customer name
    public function CustomerName($customer_name)
    {
        $this->params['customer_name'] = $customer_name;
        return $this;
    }

    // This method sets customer email
    public function Email($email)
    {
        $this->params['email'] = $email;
        return $this;
    }

    // This method sets customer mobile number
    public function Mobile($mobile)
    {
        $this->params['mobile'] = $mobile;
        return $this;
    }

    // This method sets customer description
    public function Description($description)
    {
        $this->params['description'] = $description;
        return $this;
    }

    public function getQRCode()
    {
        $this->pay_request_format = 'qrcode';
        return $this;
    }

    public function Redirect()
    {
        if ($this->pay_url!=null)
        {
            header('Location: '.$this->pay_url);
            die;
        }
        else
        {
            return false;
        }
        
    }
}
?>