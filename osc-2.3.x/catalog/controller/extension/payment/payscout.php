<?php
class ControllerExtensionPaymentPayscout extends Controller {
	public function index() {
		$this->load->language('extension/payment/payscout');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');
		$data['entry_ssn'] = $this->language->get('entry_ssn');		
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_billing_dob'] = $this->language->get('entry_billing_dob');
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}
				
		return $this->load->view('extension/payment/payscout', $data);
	}

	public function send() {
		$json = array();
		$url = "https://my.payscout.com/api/process";
		$error = 0;
		$client_username = $this->config->get('payscout_username');
		$client_password = $this->config->get('payscout_password');
		$client_token = $this->config->get('payscout_token');
		
		if((int)$this->config->get('payscout_server') == 0)
		{
			$url = "https://preprod.payscout.com/api/process";
			
			$client_username = 'victorAPI';
			$client_password = 'scout123!';
			$client_token = 'dd59e50945c2f2153ebf2559bc3fb4c5';
		}

		$this->load->model('checkout/order');
		$this->load->language('extension/payment/payscout');
		$error = 0;
		
		if(!isset($_POST['cc_billing_ssn']))
		{
			$json['error']['cc_billing_ssn'] = true;
			$error++;
		}else if(isset($_POST['cc_billing_ssn']) && trim($_POST['cc_billing_ssn'])=="")
		{
			$json['error']['cc_billing_ssn'] = true;
			$error++;
		}
		
		if(!isset($_POST['cc_billing_dob']))
		{
			$json['error']['dob'] = true;
			$error++;
		}else if(isset($_POST['cc_billing_dob']) && trim($_POST['cc_billing_dob'])=="")
		{
			$json['error']['dob'] = true;
			$error++;
		}
		
		if(!isset($_POST['cc_billing_ssn']))
		{
			$json['error']['cc_billing_ssn'] = true;
			$error++;
		}else if(isset($_POST['cc_billing_ssn']) && trim($_POST['cc_billing_ssn'])=="")
		{
			$json['error']['cc_billing_ssn'] = true;
			$error++;
		}
		
		if(!isset($_POST['cc_number']))
		{
			$json['error']['cc_number'] = true;
			$error++;
		}else if(isset($_POST['cc_number']) && trim($_POST['cc_number'])=="")
		{
			$json['error']['cc_number'] = true;
			$error++;
		}
		
		if(!isset($_POST['cc_cvv2']))
		{
			$json['error']['cc_cvv2'] = true;
			$error++;
		}else if(isset($_POST['cc_cvv2']) && trim($_POST['cc_cvv2'])=="")
		{
			$json['error']['cc_cvv2'] = true;
			$error++;
		}
				
		
		if($error == 0)
		{
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			
			$ssn = false;
			
			if(isset($this->request->post['cc_billing_ssn']) && strlen(trim($this->request->post['cc_billing_ssn'])) == 4)
			{
				$ssn = trim($this->request->post['cc_billing_ssn']);
			}

			$request = array(			
			'client_username' => $client_username,
			'client_password' => $client_password,
			'client_token' => $client_token,
			'currency' => $order_info['currency_code'],
			'processing_type' => 'DEBIT',
			'pass_through' => $this->session->data['order_id'],
			'ip_address' => $this->request->server['REMOTE_ADDR'],
			'account_number' => html_entity_decode(preg_replace('/[^0-9]/', '', $this->request->post['cc_number']), ENT_QUOTES, 'UTF-8'),
			'cvv2' => html_entity_decode(preg_replace('/[^0-9]/', '', $this->request->post['cc_cvv2']), ENT_QUOTES, 'UTF-8'),
			'expiration_month' => html_entity_decode($this->request->post['cc_expire_date_month'], ENT_QUOTES, 'UTF-8'),
			'expiration_year' => html_entity_decode($this->request->post['cc_expire_date_year'], ENT_QUOTES, 'UTF-8'),
			'initial_amount' => number_format(floatval($order_info['total']), 2, '.', ''),
			'billing_first_name' => html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'),
			'billing_last_name' => html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'),			
			'billing_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
			'billing_address_line_1' => html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'),
			'billing_date_of_birth' => date('Ymd', strtotime($this->request->post['cc_billing_dob'])),
			'billing_phone_number' => html_entity_decode(preg_replace('/[^0-9]/', '', $order_info['telephone']), ENT_QUOTES, 'UTF-8'),
			'billing_city' =>  html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'),
			'billing_state' => ($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
			'billing_country' => html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'),
			'billing_postal_code' => html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8')			
		);
		
		if($ssn)
		{
			$ssn_info = array('billing_social_security_number' => $ssn);
			$request = array_merge($request, $ssn_info);
		}

		if ($this->cart->hasShipping()) {
			$shipping_info = array(
				'shipping_first_name' => html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8'),
				'shipping_last_name' => html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8'),
				'shipping_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
				'shipping_address_line_1' => html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8'),
				'shipping_cell_phone_number' => html_entity_decode(preg_replace('/[^0-9]/','', $order_info['telephone']), ENT_QUOTES, 'UTF-8'),
				'shipping_city' => html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8'),
				'shipping_state' => ($order_info['shipping_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
				'shipping_country' => html_entity_decode($order_info['shipping_iso_code_2'], ENT_QUOTES, 'UTF-8'),
				'shipping_postal_code' => html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8'),
			);
		} else {
			$shipping_info = array(
				'shipping_first_name' => html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'),
				'shipping_last_name' => html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'),
				'shipping_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
				'shipping_address_line_1' => html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'),
				'shipping_cell_phone_number' => html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8'),
				'shipping_city' => html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'),
				'shipping_state' => ($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
				'shipping_country' => html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'),
				'shipping_postal_code' => html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8'),
			);
		}

		$request = array_merge($request, $shipping_info);		
		
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 40);
		curl_setopt($curl, CURLOPT_TIMEOUT, 120);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request, '', '&'));

		$response = curl_exec($curl);		

		if (curl_error($curl)) {
			$json['error']['message'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
			
		} elseif ($response) {
			$response_object = (array)json_decode($response);
			
			if(count($response_object)){			

			if (isset($response_object['status']) &&  $response_object['status'] == 'approved') {
				
				$message = 'Status : ' . $response_object['status'] . "\n";
				$message .= 'Transaction ID : ' . $response_object['transaction_id'] . "\n";
				$message .= 'Message: : ' . $response_object['message'] . "\n";
				
				
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payscout_order_status_id'), $message, false);

				$json['success'] = $this->url->link('checkout/success', '', true);
			} else {
				
				
				$error_message = '';
				if(isset($response_object['message'])) $error_message = $response_object['message'];
				if(!isset($response_object['message']))
				{
					if(isset($response_object['raw_message'])) $error_message = $response_object['raw_message'];
				}
				
				
					$json['error']['message'] = $error_message;
				
			 }
		  }else{
			  $json['error']['message'] = 'Empty Gateway Response';
			  
			 }
		} else {
			$json['error']['message'] = 'Empty Gateway Response';			
		}

		curl_close($curl);
		
	 }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

