<?php
class ControllerExtensionPaymentPayscout extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/payscout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_payscout', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}
		
		if (isset($this->error['token'])) {
			$data['error_token'] = $this->error['token'];
		} else {
			$data['error_token'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/payscout', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/payscout', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_payscout_username'])) {
			$data['payment_payscout_username'] = $this->request->post['payment_payscout_username'];
		} else {
			$data['payment_payscout_username'] = $this->config->get('payment_payscout_username');
		}

		if (isset($this->request->post['payment_payscout_password'])) {
			$data['payment_payscout_password'] = $this->request->post['payment_payscout_password'];
		} else {
			$data['payment_payscout_password'] = $this->config->get('payment_payscout_password');
		}
		
		if (isset($this->request->post['payment_payscout_token'])) {
			$data['payment_payscout_token'] = $this->request->post['payment_payscout_token'];
		} else {
			$data['payment_payscout_token'] = $this->config->get('payment_payscout_token');
		}
		
		if (isset($this->request->post['payment_payscout_server'])) {
			$data['payment_payscout_server'] = $this->request->post['payment_payscout_server'];
		} else {
			$data['payment_payscout_server'] = $this->config->get('payment_payscout_server');
		}

		if (isset($this->request->post['payment_payscout_total'])) {
			$data['payment_payscout_total'] = $this->request->post['payment_payscout_total'];
		} else {
			$data['payment_payscout_total'] = $this->config->get('payment_payscout_total');
		}

		if (isset($this->request->post['payment_payscout_order_status_id'])) {
			$data['payment_payscout_order_status_id'] = $this->request->post['payment_payscout_order_status_id'];
		} else {
			$data['payment_payscout_order_status_id'] = $this->config->get('payment_payscout_order_status_id');
		}


		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_payment_payscout_geo_zone_id'])) {
			$data['payment_payscout_geo_zone_id'] = $this->request->post['payment_payscout_geo_zone_id'];
		} else {
			$data['payment_payscout_geo_zone_id'] = $this->config->get('payment_payscout_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_payscout_status'])) {
			$data['payment_payscout_status'] = $this->request->post['payment_payscout_status'];
		} else {
			$data['payment_payscout_status'] = $this->config->get('payment_payscout_status');
		}

		if (isset($this->request->post['payment_payscout_sort_order'])) {
			$data['payment_payscout_sort_order'] = $this->request->post['payment_payscout_sort_order'];
		} else {
			$data['payment_payscout_sort_order'] = $this->config->get('payment_payscout_sort_order');
		}

		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/payscout', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/payscout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_payscout_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['payment_payscout_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['payment_payscout_token']) {
			$this->error['token'] = $this->language->get('error_token');
		}

		return !$this->error;
	}
}