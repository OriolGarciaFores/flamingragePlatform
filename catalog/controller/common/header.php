<?php

class ControllerCommonHeader extends Controller
{
    public function index()
    {
        // Analytics
        $this->load->model('setting/extension');

        $data['analytics'] = array();

        $analytics = $this->model_setting_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
            }
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base'] = $server;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
        $this->load->model("catalog/category");
        $categories = $this->model_catalog_category->getCategories();

        if($this->customer->isLogged()){
            $data['text_login'] = $this->language->get('text_logout');
            $data['login'] =$this->url->link('account/login', '', true);
        }else{
            $data['text_login'] = $this->language->get('text_login');
            $data['login'] = $this->url->link('account/login', '', true);
        }


        $data['home'] = $this->url->link('common/home');
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['contact'] = $this->url->link('information/contact');

        $data['categories'] = $categories;
        $this->load->model('design/banner');
        $this->load->model('design/layout');
        foreach ($categories as $key => $category) {

            $category_layout = $this->model_catalog_category->getCategoryLayoutId($category['category_id']);
            $layout = $this->model_design_layout->getRoute($category_layout);
            if(!empty($layout)){
                $data['categories'][$key]['href'] = $this->url->link($layout[0]['route'], 'category_id=' . $category['category_id']);
            }else{
                $data['categories'][$key]['href'] = $this->url->link('contents/category', 'category_id=' . $category['category_id']);
            }

        }

        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');
        $data['menu'] = $this->load->controller('common/menu');

        //TODO Mejorar al obtener el banner deseado.
        $this->load->model('setting/module');
        $modules = $this->model_design_banner->getBanner(7);
        $this->load->model('tool/image');

        if ($modules) {
            $data['title_img'] = $this->model_tool_image->resize($modules[0]['image'], 0, 0);
            $data['img_header'] = $this->model_tool_image->resize($modules[1]['image'], 0, 0);
        } else {
            $data['title_img'] = '';
            $data['img_header'] = '';
        }

        return $this->load->view('common/header', $data);
    }
}
