<?php 
/**
 * Mas_Maslandingpro extension by Makarovsoft.com
 * 
 * @category   	Mas
 * @package		Mas_Maslandingpro
 * @copyright  	Copyright (c) 2014
 * @license		http://makarovsoft.com/license.txt
 * @author		makarovsoft.com
 */
/**
 * Maslandingpro module install script
 *
 * @category	Mas
 * @package		Mas_Maslandingpro
 * 
 */
$this->startSetup();
                
$this->run("
ALTER TABLE `{$this->getTable('mascnotes/note')}` MODIFY `note` MEDIUMTEXT NOT NULL;
");
$this->endSetup();