<?php
class Magestore_RewardPointsRule_Block_Adminhtml_Spending_Individual_Renderer_Point extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected $_variablePattern = '/\\$([a-z0-9_]+)/i';

   public function _getValue(Varien_Object $row) {

        $format = ($this->getColumn()->getFormat()) ? $this->getColumn()->getFormat() : null;
        $defaultValue = Mage::helper('rewardpointsrule')->__('Empty'); //$this->getColumn()->getDefault();
        $htmlId = 'editable_' . $row->getId();
        $htmlId2 = 'editable2_' . $row->getId();
        $saveUrl = $this->getUrl('*/*/ajaxSave/website/retail');
        $saveUrl2 = $this->getUrl('*/*/ajaxSave/website/wholesale');

        $retailStoreId = Mage::helper('rewardpointsrule')->getStoreIdByCode('halo_retail_english');
        $wholesaleStoreId = Mage::helper('rewardpointsrule')->getStoreIdByCode('halo_wholesale_english');
        $spendingPoints_retail = Mage::getModel('catalog/product')->setStoreId($retailStoreId)->load($row->getId())->getRewardpointsSpend();
        $spendingPoints_wholesale = Mage::getModel('catalog/product')->setStoreId($wholesaleStoreId)->load($row->getId())->getRewardpointsSpend();

        $string = is_null($spendingPoints_retail) ? $defaultValue : (int) $spendingPoints_retail;
        $string2 = is_null($spendingPoints_wholesale) ? $defaultValue : (int) $spendingPoints_wholesale;
        $html = sprintf('Retail: <div id="%s" saveUrl="%s" entity="%s" oldValue="%s" class="editable" style="cursor: pointer;">%s</div>', $htmlId, $saveUrl, $row->getId(), $this->escapeHtml($string), $this->escapeHtml($string));
        $html.= sprintf('Wholesale: <div id="%s" saveUrl="%s" entity="%s" oldValue="%s" class="editable" style="cursor: pointer;">%s</div>', $htmlId2, $saveUrl2, $row->getId(), $this->escapeHtml($string2), $this->escapeHtml($string2));
        return $html . "<script>if (bindInlineEdit) bindInlineEdit('{$htmlId}'); if (bindInlineEdit) bindInlineEdit('{$htmlId2}');</script>";
    }

}
