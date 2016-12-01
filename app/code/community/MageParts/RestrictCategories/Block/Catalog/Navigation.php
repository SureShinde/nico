<?php
/**
 * MageParts
 * 
 * NOTICE OF LICENSE
 * 
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright 
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates. 
 * For information regarding modifications see http://www.magentocommerce.com.
 *  
 * DISCLAIMER
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE 
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category   MageParts
 * @package    MageParts_RestrictCategories
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author 	   MageParts Crew
 */

class MageParts_RestrictCategories_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation
{
		
	/**
	 * This function is used for version 1.4.1.0 +
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @param int $level
	 * @param boolean $isLast
	 * @param boolean $isFirst
	 * @param boolean $isOutermost
	 * @param string $outermostItemClass
	 * @param string $childrenWrapClass
	 * @param boolean $noEventAttributes
	 * @return string
	 */
	public function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
        $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
	{
		$html = parent::_renderCategoryMenuItemHtml($category, $level, $isLast, $isFirst, $isOutermost, $outermostItemClass, $childrenWrapClass, $noEventAttributes);
		
		if (Mage::helper('restrictcategories')->getMagentoVersion() >= 1.410) {
			return $this->addCategoryItemUniqueClass($category->getId(), $html);
		}
		
		return $html;
	}
	
	/**
	 * Get category element HTML
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @param int $level
	 * @param boolean $last
	 * @return string
	 */
	public function drawItem($category, $level = 0, $last = false) 
	{
		$html = parent::drawItem($category, $level, $last);
		
		// since the _renderCategoryMenuItemHtml function was introduced in Magento 1.4.1.0 we need to make our classname addition here for lower versions of Magento
		if (Mage::helper('restrictcategories')->getMagentoVersion() < 1.410) {
			return $this->addCategoryItemUniqueClass($category->getId(), $html);
		}
		
		return $html;
	}
	
	/**
	 * Add a unique classname to each ategory element
	 *
	 * @param int $id
	 * @param string $el
	 * @return string
	 */
	public function addCategoryItemUniqueClass($id, $el) 
	{
		$html = preg_replace('/class="/', 'class="' . Mage::helper('restrictcategories')->getElClassPattern() . $id . ' ', $el, 1);
		return $html;
	}
	
}