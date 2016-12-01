<?php
class Magestore_Storelocator_Model_Adminhtml_Facebookcomment
{
    public function getCommentText(){
        $comment = 'To register a Facebook API key, please follow the guide <a href="'.Mage::getBlockSingleton('adminhtml/widget')->getUrl('adminhtml/storelocatoradmin_guide/index/').'">here</a>';
        return $comment;
    }
}
