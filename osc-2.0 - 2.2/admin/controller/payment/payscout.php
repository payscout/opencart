<?php
class ControllerPaymentPayscout extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/payscout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payscout', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_capture'] = $this->language->get('text_capture');
		$data['entry_server'] = $this->language->get('entry_server');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_token'] = $this->language->get('entry_token');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/payscout', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/payscout', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		
		if (isset($this->request->post['payscout_username'])) {
			$data['payscout_username'] = $this->request->post['payscout_username'];
		} else {
			$data['payscout_username'] = $this->config->get('payscout_username');
		}

		if (isset($this->request->post['payscout_password'])) {
			$data['payscout_password'] = $this->request->post['payscout_password'];
		} else {
			$data['payscout_password'] = $this->config->get('payscout_password');
		}
		
		if (isset($this->request->post['payscout_token'])) {
			$data['payscout_token'] = $this->request->post['payscout_token'];
		} else {
			$data['payscout_token'] = $this->config->get('payscout_token');
		}
		
		if (isset($this->request->post['payscout_server'])) {
			$data['payscout_server'] = $this->request->post['payscout_server'];
		} else {
			$data['payscout_server'] = $this->config->get('payscout_server');
		}


		if (isset($this->request->post['payscout_total'])) {
			$data['payscout_total'] = $this->request->post['payscout_total'];
		} else {
			$data['payscout_total'] = $this->config->get('payscout_total');
		}

		if (isset($this->request->post['payscout_order_status_id'])) {
			$data['payscout_order_status_id'] = $this->request->post['payscout_order_status_id'];
		} else {
			$data['payscout_order_status_id'] = $this->config->get('payscout_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payscout_geo_zone_id'])) {
			$data['payscout_geo_zone_id'] = $this->request->post['payscout_geo_zone_id'];
		} else {
			$data['payscout_geo_zone_id'] = $this->config->get('payscout_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payscout_status'])) {
			$data['payscout_status'] = $this->request->post['payscout_status'];
		} else {
			$data['payscout_status'] = $this->config->get('payscout_status');
		}

		if (isset($this->request->post['payscout_sort_order'])) {
			$data['payscout_sort_order'] = $this->request->post['payscout_sort_order'];
		} else {
			$data['payscout_sort_order'] = $this->config->get('payscout_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/payscout.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/payscout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}


		if (!$this->request->post['payscout_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['payscout_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['payscout_token']) {
			$this->error['token'] = $this->language->get('error_token');
		}

		return !$this->error;
	}
}
