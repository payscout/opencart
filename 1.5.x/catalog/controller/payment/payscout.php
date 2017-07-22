<?php
class ControllerPaymentPayscout extends Controller {
	public function index() {
		$this->load->language('payment/payscout');

		$this->data['text_credit_card'] = $this->language->get('text_credit_card');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$this->data['entry_dob'] = $this->language->get('entry_dob');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 17; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		
		

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payscout.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/payscout.tpl';
		} else {
			$this->template = 'default/template/payment/payscout.tpl';
		}	

		$this->render();
	
	}

	public function send() {
		
		$url = "https://gateway.payscout.com/api/process";	
		
		if((int)$this->config->get('payscout_server') == 0)
		{
			$url = "https://mystaging.paymentecommerce.com/api/process";
		}
			

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$request = array(			
			'client_username' => $this->config->get('payscout_username'),
			'client_password' => $this->config->get('payscout_password'),
			'client_token' => $this->config->get('payscout_token'),
			'currency' => $order_info['currency_code'],
			'processing_type' => 'DEBIT',
			'pass_through' => $this->session->data['order_id'],
			'account_number' => html_entity_decode(preg_replace('/[^0-9]/', '', $this->request->post['cc_number']), ENT_QUOTES, 'UTF-8'),
			'cvv2' => html_entity_decode(preg_replace('/[^0-9]/', '', $this->request->post['cc_cvv2']), ENT_QUOTES, 'UTF-8'),
			'expiration_month' => html_entity_decode($this->request->post['cc_expire_date_month'], ENT_QUOTES, 'UTF-8'),
			'expiration_year' => html_entity_decode($this->request->post['cc_expire_date_year'], ENT_QUOTES, 'UTF-8'),
			'initial_amount' => $this->currency->format($order_info['total'], $order_info['currency_code'], false, false),
			'billing_first_name' => html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'),
			'billing_last_name' => html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'),			
			'billing_email_address' => html_entity_decode($order_info['email'], ENT_QUOTES, 'UTF-8'),
			'billing_address_line_1' => html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'),
			'billing_phone_number' => html_entity_decode(preg_replace('/[^0-9]/', '', $order_info['telephone']), ENT_QUOTES, 'UTF-8'),
			'billing_city' =>  html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'),
			'billing_state' => ($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'),
			'billing_country' => html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8'),
			'billing_postal_code' => html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8')			
		);

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
		
		$json = array();

		if (curl_error($curl)) {
			$json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);

			$this->log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
		} elseif ($response) {
			$response_object = (array)json_decode($response);
						
			
			if (isset($response_object['status']) &&  $response_object['status'] == 'approved') {
				
				$message = 'Status : ' . $response_object['status'] . "\n";
				$message .= 'Transaction ID : ' . $response_object['transaction_id'] . "\n";
				$message .= 'Message: : ' . $response_object['message'] . "\n";
				
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$this->config->get('payscout_order_status_id') . "', date_modified = NOW() WHERE order_id = '" . (int)$this->session->data['order_id'] . "'");
				
				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('payscout_order_status_id'), $message, false);				
				
				$json['success'] = $this->url->link('checkout/success', '', 'SSL');
			} else {
				
				
				$error_message = $response_object['message'];
				if(isset($response_object['message']) && $response_object['message'] == "")
				{
					$error_message = $response_object['raw_message'];
				}
				
				
					$json['error'] = $error_message;
				
			}
		} else {
			$json['error'] = 'Empty Gateway Response';

			$this->log->write('Payscout CURL ERROR: Empty Gateway Response');
		}

		curl_close($curl);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>