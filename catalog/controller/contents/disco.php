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

        $category = $this->model_catalog_category->getCategory($this->request->get['category_id']);
        if(isset($category) && !empty($category) && $category['name'] == "DISCO") {

            $this->document->setTitle($category['meta_title']);
            $this->document->setDescription($category['meta_description']);
            $this->document->setKeywords($category['meta_keyword']);

            $this->load->model('design/banner');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('contents/disco', $data));
        } else{
            $this->response->redirect($this->url->link('error/not_found'));
        }
    }

}