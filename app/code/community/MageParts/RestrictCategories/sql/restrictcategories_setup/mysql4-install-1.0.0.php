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

// we need to do this for the extension to show up in the `core_resources`, and actually being counted as installed, on every version of Magento.

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

// Create tables
$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('mageparts_restrictcategories_rule')}`;
CREATE TABLE `{$this->getTable('mageparts_restrictcategories_rule')}` (
  `rule_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned NOT NULL,
  `customer_ids` text(0),
  PRIMARY KEY  (`rule_id`),
  CONSTRAINT `FK_RC_CATEGORY_ENTITY` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('catalog_category_entity')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
-- DROP TABLE IF EXISTS `{$this->getTable('mageparts_restrictcategories_rule_store')}`;
CREATE TABLE `{$this->getTable('mageparts_restrictcategories_rule_store')}` (
  `rule_id` int(11) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`rule_id`,`store_id`),
  CONSTRAINT `FK_RC_STORE_RULE` FOREIGN KEY (`rule_id`) REFERENCES `{$this->getTable('mageparts_restrictcategories_rule')}` (`rule_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_RC_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('mageparts_restrictcategories_rule_customer_group')}`;
CREATE TABLE `{$this->getTable('mageparts_restrictcategories_rule_customer_group')}` (
  `rule_id` int(11) unsigned NOT NULL,
  `customer_group_id` smallint(3) unsigned NOT NULL,
  PRIMARY KEY (`rule_id`,`customer_group_id`),
  CONSTRAINT `FK_RC_CG_RULE` FOREIGN KEY (`rule_id`) REFERENCES `{$this->getTable('mageparts_restrictcategories_rule')}` (`rule_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_RC_CG_GROUP` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer_group')}` (`customer_group_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();