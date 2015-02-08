<?php

class Zend_View_Helper_Facebook
{
    public $view;

    public $metaArr = array();
    public $isOgImages = false;

    public function facebook()
    {
        return $this;
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function comments($url, $width = 500)
    {
        $content = '<h3>Comments</h3>';
        $content .= '<div id="comment_box">';
        $content .= '<div class="fb-comments" data-href="' . $url . '" data-num-posts="8" data-width="' . $width . '">';
        $content .= '</div>';
        $content .= '</div>';
        return $content;
    }

    public function boxCountLike($url)
    {
        $content = '<div class="fb-like" data-href="' . $url . '" data-send="true" data-layout="box_count" data-width="200" data-show-faces="true" data-font="segoe ui">';
        $content .= '</div>';
        return $content;
    }

    public function buttonCountLike($url)
    {
        $content = '<div class="fb-like" data-href="' . $url . '" data-send="true" data-layout="button_count" data-width="200" data-show-faces="true" data-font="segoe ui">';
        $content .= '</div>';
        return $content;
    }

    public function likeBox($facebookPageUrl, $width = 200)
    {
        $content = '<div style="margin-bottom:20px;">';
        $content .= '<div class="fb-like-box" data-href="' . $facebookPageUrl . '" data-width="' . $width . '" data-show-faces="true" data-stream="false" data-header="true"></div>';
        $content .= '</div>';
        return $content;
    }

    public function meta()
    {
        $content = '';
        foreach ($this->metaArr as $meta) {
            $type = $meta['type'];
            $value = $meta['value'];
            $content .= '<meta property="og:' . $type . '" content="' . $value . '"/>';
        }
        return $content;
    }

    public function isOgImages(){
        return $this->isOgImages;
    }

    public function addMeta($type, $value)
    {
        if ($type == 'image') { // to deter mine that if og:image tags are included. if not default image will be used ..
            $this->isOgImages = true;
        }
        $this->metaArr[] = array('type' => $type, 'value' => $value);
    }
}

?>