<?php
class ControllerExtensionModuleListUrl extends Controller {
    public function index($setting) {

      if(!empty($setting)){
        $data['heading_title'] = $setting['name'];
        $data['list_urls'] = $setting['url'][$this->config->get('config_language_id')];
          return $this->load->view('extension/module/list_url', $data);

      }


    }
}