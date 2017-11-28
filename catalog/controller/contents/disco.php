<?php
class ControllerContentsDisco extends Controller{

    public function index()
    {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));



        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }



        $this->load->model('design/banner');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('contents/disco', $data));
    }

}