<?php

class ControllerCommonHome extends Controller
{
    public function index()
    {
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }

        $this->load->model('tool/image');
        $this->load->model('catalog/category');
        $data['categories'] = array();
        $categories = $this->model_catalog_category->getCategories();

        if (isset($categories)) {
            foreach ($categories as $key => $category) {
                $categories[$key]['description'] = html_entity_decode(substr($category['description'], 0, 500));

                if(isset($category['image']) && !empty($category['image'])){
                    $categories[$key]['image'] = $this->model_tool_image->resize($category['image'], 0, 0);
                }else{
                    $categories[$key]['image'] = $this->model_tool_image->resize('catalog/placeholder.jpg', 0, 0);
                }

            }
            $data['categories'] = isset($categories) ? $categories : '';
        }else{
            $data['categories'] = '';
        }



        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('common/home', $data));
    }
}
