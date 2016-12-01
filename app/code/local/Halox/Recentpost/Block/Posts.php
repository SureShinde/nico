<?php

class Halox_Recentpost_Block_Posts extends Mage_Core_Block_Template {

    /**
     * Retrieve post data
     * @return Array
     */
    public function getPost() {
        $wordpress_db = Mage::getModel('recentpost/wordpress_database');

        return $wordpress_db->fetchPost();
    }
    /**
     * Retrieve post meta data
     * @return Array
     */
    public function getPostImage($post_id) {
        $wordpress_db = Mage::getModel('recentpost/wordpress_database')->fetchMetaData($post_id);
        return isset($wordpress_db[0]['meta_value']) ? $wordpress_db[0]['meta_value'] : null;
    }

    /**
     * Retrieve the post excerpt
     *
     * @return string
     */
    public function getPostExcerpt($content) {
        if ($excerpt = trim(strip_tags($content))) {
            $words = explode(' ', $excerpt);

            if (count($words) > $this->getExcerptLength()) {
                $words = array_slice($words, 0, $this->getExcerptLength());
            }

            return trim(implode(' ', $words), '.,!:-?"\'£$%') . '...';
        }

        return '';
    }

    /**
     * Returns the post date formatted
     * If not format is supplied, the format specified in your Magento config will be used
     *
     * @return string
     */
    public function getPostDate($date, $format = null) {

        if ($format == null) {
            $format = $this->getDefaultDateFormat();
        }

        $format = date('F d,Y');

        $len = strlen($format);

        for ($i = 0; $i < $len; $i++) {
            $out = $this->__(Mage::getModel('core/date')->date('F d,Y', strtotime($date)));
        }

        return $out;
    }
    
     public function addBlogLink($type)
    {
        if (self::$_helper->isEnabled()) {
            $title = self::$_helper->isTitle();
            if ($this->getParentBlock()) {
                if ($type == self::LINK_TYPE_HEADER) {
                    $this->getParentBlock()->addLink($title, self::$_helper->getRoute()."/index/", $title, true);
                } else {
                    $this->getParentBlock()->addLink(
                        $title, self::$_helper->getRoute()."/index/", $title, true, array(), 15, null, 'class="top-link-blog"'
                    );
                }
            }
        }
    }

}
