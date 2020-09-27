#[Hamrahpay](https://hamrahpay.com) CodeIgniter Library

Codeigniter 4.x library for Hamrahpay Online Payment Gateway

## Installation
Copy `Hamrahpay.php` to `app/Libraries` of your own project.

## Usage

Simple Example Of Usage in Home.php

Change ```YOUR-API-KEY``` to your API Key
	
```php
<?php 
namespace App\Controllers;

use App\Libraries\Hamrahpay;

class Home extends BaseController
{
	
	public function index()
	{
		$api_key        = 'YOUR-API-KEY';
		$callback_url   = site_url('home/callback');
		$hamrahpay		= Hamrahpay::Instance($api_key);
		$result = $hamrahpay->CallbackUrl($callback_url)
			->Amount(10000)
			->CustomerName('علی')
			->Description("تست")
			->Email('test@example.com')
			->Mobile('09121234567')
			->PaymentRequest();

			if (!empty($result['status']) && $result['status']==1)
				$hamrahpay->Redirect();
			
			var_dump($result);
	}

	//--------------------------------------------------------------------
	
	public function callback()
	{
		$api_key        = 'YOUR-API-KEY';
		$hamrahpay		= Hamrahpay::Instance($api_key);
		$payment_token  = $_GET['payment_token'];
		$status         = $_GET['status'];

		if ($status=='OK')
		{
			$result = $hamrahpay->VerifyPayment($payment_token);
			$result = json_decode($result,true);
			if ($result['status']==100) // succeed , first time verification
			{
				echo "<br> Reference Number:".$result['reference_number'];
				echo "<br> Reserve Number:".$result['reserve_number'];
				echo "<br> Amount:".$result['amount'];
			}
			else if ($result['status']==101) // succeed, after first time verification
			{
				echo "<br> Amount:".$result['amount'];
			}
			else
			{
				// show error message
				echo $result['error_message'];
			}
		}
		else // NOK
		{
			// show error
			echo "NOK";
		}
	}

}

```


[Hamrahpay](https://hamrahpay.com)