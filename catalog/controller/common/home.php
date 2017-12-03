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
            $i = 0;
            foreach ($categories as $category) {

                $childs = $this->model_catalog_category->getCategories($category['category_id']);
                if(!isset($childs) || empty($childs)){
                    $childs[] = $category;
                }

                if (isset($childs) && !empty($childs)) {
                    foreach ($childs as $key => $child) {

                        $child['description'] = isset($child['description']) ? html_entity_decode(substr($child['description'], 0, 500)) : '';

                        if (isset($child['image']) && !empty($child['image'])) {
                            $child['image'] = $this->model_tool_image->resize($child['image'],$this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
                        } else {
                            $child['image'] = $this->model_tool_image->resize('catalog/placeholder.jpg', 0, 0);
                        }
                        $data['categories'][$i] = $child;
                        $i = $i + 1;
                    }

                }

            }
        } else {
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
