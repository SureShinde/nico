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
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Block_Adminhtml_System_Config_Form_Field_Email extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function __construct()
    {
        parent::__construct();

        $this->addColumn('email', array(
            'label' => Mage::helper('mageparts_base')->__('Email Address'),
            'style' => 'width:300px',
        ));
       
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('mageparts_base')->__('Add Email Address');
    }
    
}
