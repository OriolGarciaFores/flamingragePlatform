<?php
class ControllerContentsArticle extends Controller{
    public function index()
    {
        $this->load->language('contents/article');
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));


        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }

        if (isset($this->request->get['path'])) {
            $category_id = $this->request->get['path'];
        } else {
            $category_id = "";
        }

        $category_info = $this->getArticleInfo($category_id);

        if(isset($category_info) && !empty($category_info)){
            $data = $category_info;

            $parent = $this->model_catalog_category->getCategory($data['category']['parent_id']);

            if(isset($parent) &&  $parent['name'] == 'PROYECTOS'){
            $data['name_parent'] = $parent['name'];
            }

            if($this->customer->isLogged()){
                $data['isLogged'] = true;
            }else{
                $data['isLogged'] = false;
            }

            $data['content_top'] = $this->load->controller('common/content_top');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('contents/article', $data));
        }else{
            $this->response->redirect($this->url->link('error/not_found'));
        }

    }

    private function getArticleInfo($category_id){

        if(isset($category_id) && !empty($category_id)){
            $data['category'] = $this->model_catalog_category->getCategory($category_id);
            $data['categories'] = $this->model_catalog_category->getCategories($category_id);

            if(!isset($data['category']['name']))
            {
                return false;
            }

            foreach ($data['categories'] as $key => $category){
                $data['categories'][$key]['description'] = isset($category['description']) ? html_entity_decode(substr($category['description'], 0, 5000)) : '';

                if (isset($category['image']) && !empty($category['image'])) {
                    $data['categories'][$key]['image'] = $this->model_tool_image->resize($category['image'], 0, 0);
                } else {
                    $data['categories'][$key]['image'] = $this->model_tool_image->resize('catalog/placeholder.jpg', 0, 0);
                }

            }

            return $data;
        }else{
            return false;
        }

    }
}