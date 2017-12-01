<?php
class ControllerInformationInformation extends Controller {
	public function index() {
		$this->load->language('information/information');

		$this->load->model('catalog/information');


            $this->document->setTitle($this->config->get('config_meta_title'));
            $this->document->setDescription($this->config->get('config_meta_description'));
            $this->document->setKeywords($this->config->get('config_meta_keyword'));

			$data['heading_title'] =$this->config->get('config_heading_title');


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('information/information', $data));
	}
}