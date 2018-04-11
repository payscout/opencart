<?php 
class ControllerPaymentPayscout extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('payment/payscout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payscout', $this->request->post);				

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_edit'] = $this->language->get('text_edit');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_test'] = $this->language->get('text_test');
		$this->data['text_live'] = $this->language->get('text_live');		

		$this->data['entry_transaction'] = $this->language->get('entry_transaction');
		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_token'] = $this->language->get('entry_token');
		$this->data['entry_total'] = $this->language->get('entry_total');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['help_total'] = $this->language->get('help_total');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}		

		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
		if (isset($this->error['token'])) {
			$this->data['error_token'] = $this->error['token'];
		} else {
			$this->data['error_token'] = '';
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/payscout', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('payment/payscout', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		
		if (isset($this->request->post['payscout_username'])) {
			$this->data['payscout_username'] = $this->request->post['payscout_username'];
		} else {
			$this->data['payscout_username'] = $this->config->get('payscout_username');
		}

		if (isset($this->request->post['payscout_password'])) {
			$this->data['payscout_password'] = $this->request->post['payscout_password'];
		} else {
			$this->data['payscout_password'] = $this->config->get('payscout_password');
		}
		
		if (isset($this->request->post['payscout_token'])) {
			$this->data['payscout_token'] = $this->request->post['payscout_token'];
		} else {
			$this->data['payscout_token'] = $this->config->get('payscout_token');
		}
		
		if (isset($this->request->post['payscout_server'])) {
			$this->data['payscout_server'] = $this->request->post['payscout_server'];
		} else {
			$this->data['payscout_server'] = $this->config->get('payscout_server');
		}


		if (isset($this->request->post['payscout_total'])) {
			$this->data['payscout_total'] = $this->request->post['payscout_total'];
		} else {
			$this->data['payscout_total'] = $this->config->get('payscout_total');
		}

		if (isset($this->request->post['payscout_order_status_id'])) {
			$this->data['payscout_order_status_id'] = $this->request->post['payscout_order_status_id'];
		} else {
			$this->data['payscout_order_status_id'] = $this->config->get('payscout_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payscout_geo_zone_id'])) {
			$this->data['payscout_geo_zone_id'] = $this->request->post['payscout_geo_zone_id'];
		} else {
			$this->data['payscout_geo_zone_id'] = $this->config->get('payscout_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payscout_status'])) {
			$this->data['payscout_status'] = $this->request->post['payscout_status'];
		} else {
			$this->data['payscout_status'] = $this->config->get('payscout_status');
		}

		if (isset($this->request->post['payscout_sort_order'])) {
			$this->data['payscout_sort_order'] = $this->request->post['payscout_sort_order'];
		} else {
			$this->data['payscout_sort_order'] = $this->config->get('payscout_sort_order');
		}

		$this->template = 'payment/payscout.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
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

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>