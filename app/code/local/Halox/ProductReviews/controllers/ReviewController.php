<?php
/**
 * Product review Ajax Controller
 *
 * @category   Halox
 * @package    Halox_ProductReviews
 * @author     Chetu Team
 */
class Halox_ProductReviews_ReviewController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action controller for ajax 
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
             
    }
}