<?php
class ControllerContentsCategory extends Controller{

    public function index()
    {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        
        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }

        if (isset($this->request->get['category_id'])) {
           $category_id = $this->request->get['category_id'];
        } else {
            $category_id = "";
        }





       $category_info = $this->getCategoryInfo($category_id);

        if(isset($category_info) && !empty($category_info)){

            $parent_info = $this->model_catalog_category->getCategory($category_id);

            $this->document->setTitle($parent_info['meta_title']);
            $this->document->setDescription($parent_info['meta_description']);
            $this->document->setKeywords($parent_info['meta_keyword']);

            $data = $category_info;
            $this->load->model('design/banner');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('contents/category', $data));
        }else{
            $this->response->redirect($this->url->link('error/not_found', '', true));
        }

    }

    private function getCategoryInfo($category_id){

        if(isset($category_id) && !empty($category_id)){
            $data['title'] = $this->model_catalog_category->getCategory($category_id)['name'];
            $data['categories'] = $this->model_catalog_category->getCategories($category_id);
            $categories = array();
            $category_info = array();

            if(!isset($data['title']))
            {
                return false;
            }

            foreach ($data['categories'] as $key => $category){
                $category_info['description'] = isset($category['description']) ? html_entity_decode(substr($category['description'], 0, 500)) : '';

                if (isset($category['image']) && !empty($category['image'])) {
                    $category_info['image'] = $this->model_tool_image->resize($category['image'], 0, 0);
                } else {
                    $category_info['image'] = $this->model_tool_image->resize('catalog/placeholder.jpg', 0, 0);
                }

                $categories['categories'][$category['sort_order']] = array(
                    'image' => $category_info['image'],
                    'description' => $category_info['description'],
                    'name' => $category['name'],
                    'href' =>  $this->url->link('contents/article', 'path=' . $category['category_id'], true),
                    'created_at' => date("d-m-Y H:i", strtotime($category['date_added']))
                );

            }
            krsort($categories['categories']);

            return $categories;
        }else{
            return false;
        }

    }

}