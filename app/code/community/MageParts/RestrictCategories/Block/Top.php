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

class MageParts_RestrictCategories_Block_Top extends Mage_Core_Block_Template
{

	/**
	 * We need this to avoid cache
	 *
	 * @return string
	 */
	public function _toHtml()
	{
		$html = parent::_toHtml();
		return $html;
	}
	
	/**
	 * Generate JS / CSS code for hiding / removing elements from the cateory menu(s)
	 *
	 * @return string
	 */
	public function generateCssJsCode()
	{		
		// get helper object
		$helper = Mage::helper('restrictcategories');

		// get restricted categories
		$restrictedCategories = $helper->getRestrictionRules('category_ids');
		
		// css / javascript code
		$cssCode = '';
		$jsCode = '';
		
		if (count($restrictedCategories)) {
			foreach ($restrictedCategories as $categoryId) {
				// get classname of element to hide / remove
				$className = "{$helper->getElClassPattern()}{$categoryId}";
				
				// add css / js code to remove category element
				$cssCode.= empty($cssCode) ? ".{$className}" : ",\n.{$className}";
				$jsCode.= empty($jsCode) ? "'{$className}'" : ",'{$className}'";
			}
				
			// complete css code
			$cssCode = !empty($cssCode) ? '<style type="text/css">' . $cssCode . ' { display:none; } </style>' : '';
			
			// complete javascript code
			$jsCode = !empty($jsCode) ? '<script type="text/javascript">mpRestrictCategories.categoryList = new Array(' . $jsCode . '); ' . "Event.observe(window, 'load', function() { mpRestrictCategories.run(); });" . '</script>' : '';
		}
		
		// return cs / javascript code
		return $cssCode . $jsCode;
	}
	
}