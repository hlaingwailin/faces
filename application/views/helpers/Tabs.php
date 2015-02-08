<?php
class Zend_View_Helper_Tabs extends Zend_View_Helper_Abstract
{

    public function Tabs()
    {
        return $this;
    }

    public function createMenuTabs($tabvalues)
    {
        $currUri = $this->getCurrentUri();

        $menuTab = '<div id="menutab">';
        $menuTab .= '<ul id="menutabnav">';
        $match = 0;
        foreach ($tabvalues as $index => $tab) {
            if (stristr($currUri, $tab['link'])) {
                $match++;
                if ($index == 0) {
                    $menuTab .= '<a href="' . $tab['link'] . '"><li class="current-tab first">' . $tab['label'] . '</li></a>';
                } else {
                    $menuTab .= '<a href="' . $tab['link'] . '"><li class="current-tab">' . $tab['label'] . '</li></a>';
                }
            } else {
                if ($index == 0) {
                    $menuTab .= '<a href="' . $tab['link'] . '"><li class="first">' . $tab['label'] . '</li></a>';
                } else {
                    $menuTab .= '<a href="' . $tab['link'] . '"><li>' . $tab['label'] . '</li></a>';
                }
            }
        }
        if ($match == 0) {
            $menuTab = '';
            $menuTab = '<div id="menutab">';
            $menuTab .= '<ul id="menutabnav">';
            foreach ($tabvalues as $index => $tab) {
                if ($index == 0) {
                    $menuTab .= '<a href="' . $tab['link'] . '"><li class="current-tab first">' . $tab['label'] . '</li></a>';
                } else {
                    $menuTab .= '<a href="' . $tab['link'] . '"><li>' . $tab['label'] . '</li></a>';
                }
            }
        }
        $menuTab .= '</ul></div><div style="width:100%;height:5px;background-color: #DDDDDD;"></div>';

        return $menuTab;
    }

    private function getCurrentUri()
    {
        $front = Zend_Controller_Front::getInstance();
        return $front->getRequest()->getRequestUri();
    }

    public function getMenuTabStyle()
    {
        $css = '
#menutab {

}
ul#menutabnav {
    list-style : none;
    margin: 0 auto;
    padding : 0;
    overflow: hidden;
    width: 100%;
}
ul#menutabnav a > li.current-tab{
   background-color : #DDDDDD;
}
ul#menutabnav a > li {
    background: none repeat scroll 0 0 #CCCCCC;
    margin-left : 5px;
    padding : 10px;
    float: left;
    overflow: hidden;
    color : black;
}
.first{
    margin-left:0px;
}
         ';
        return $css;
    }
}

?>