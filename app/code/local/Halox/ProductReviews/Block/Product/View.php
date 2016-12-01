<?php
/**
 * Product review collection block
 *
 * @category   Halox
 * @package    Halox_ProductReviews
 * @author     Chetu Team
 */
class Halox_ProductReviews_Block_Product_View extends Mage_Review_Block_Product_View_List
{
 /**
  * 
  * @param type $count
  * @return type collection
  */
  public function getReviewsCollection($count=0)
    {
        $start = $this->getRequest()->getParam('start');
        if(empty($start)){
            $start = 1;
        }
        if (null === $this->_reviewsCollection) {           
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder()
                ->setPageSize(5)
                ->setCurPage($start);
        }
        return $this->_reviewsCollection;
   }
   /**
    * 
    * @return type integer
    */
   public function getReviewsCollectionSize()
    {
        $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();        
      
        return $this->_reviewsCollection->getSize();
   }  
   /*
    * get product by id
    */
   public function getProduct(){
   $Id = $this->getRequest()->getParam('id');
   $product = Mage::registry('current_product');
   
   if(empty($Id) && !empty($product)){ 
      $Id =  $product->getId();
   }
   return Mage::getModel('catalog/product')->load($Id);
    
   }
   /**
    * 
    * @param type $reviewId
    * @return type collection
    */
   public function getReviewVote($reviewId){       
     $votesCollection = Mage::getModel('rating/rating_option_vote')
    ->getResourceCollection()
    ->setReviewFilter($reviewId)
    ->setStoreFilter(Mage::app()->getStore()->getId())
    ->load();
     return $votesCollection;
       
   }
}
 
