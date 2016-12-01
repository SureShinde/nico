<?php

$installer = $this;

$installer->startSetup();


if(Mage::helper('wsacommon')->getVersion() == 1.6){
	$installer->run("

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='catalog_product';
select @attribute_set_id:=attribute_set_id from {$this->getTable('eav_attribute_set')} where entity_type_id=@entity_type_id and attribute_set_name='Default';

insert ignore into {$this->getTable('eav_attribute_group')}
    set attribute_set_id 	= @attribute_set_id,
	attribute_group_name	= 'Shipping',
	sort_order		= 99;

select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='Shipping' and attribute_set_id = @attribute_set_id;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'ship_width',
	backend_type	= 'int',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	is_visible 	= 1,
    used_in_product_listing	= 1,
    is_filterable_in_search	= 0,
	frontend_label	= 'Dimension Width';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_width';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 40;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_height',
    	backend_type	= 'int',
    	frontend_input	= 'text',
    	is_required	= 0,
    	is_user_defined	= 1,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,
    	frontend_label	= 'Dimension Height';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_height';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 30;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_length',
    	backend_type	= 'int',
    	frontend_input	= 'text',
    	is_required	= 0,
    	is_user_defined	= 1,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,
    	frontend_label	= 'Dimension Length';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_length';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 20;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_separately',
    	backend_type	= 'int',
    	frontend_input	= 'boolean',
      	is_user_defined	= 1,
	   	is_required	= 0,
	   	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,
    	frontend_label	= 'Ship Separately';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_separately';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 50;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_dimensional',
    	backend_type	= 'int',
    	frontend_input	= 'boolean',
      	is_user_defined	= 1,
	   	is_required	= 0,
	   	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,
    	frontend_label	= 'Ship with Dimensional Weight';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_dimensional';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 60;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'ship_box',
	backend_type	= 'int',
	frontend_input	= 'select',
    source_model   = 'boxmenu/boxmenu',
	is_required	= 0,
	is_visible 	= 1,
    used_in_product_listing	= 1,
    is_filterable_in_search	= 0,
	frontend_label	= 'Packing Box';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_box';


insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 70;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_num_boxes',
    	backend_type	= 'int',
    	frontend_input	= 'text',
      	is_user_defined	= 1,
	   	is_required	= 0,
	   	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,	   	
    	frontend_label	= 'Number of Packages';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_num_boxes';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 55;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_algorithm',
    	backend_type	= 'text',
    	frontend_input	= 'textarea',
      	is_user_defined	= 1,
	   	is_required	= 0,
	   	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,
    	frontend_label	= 'Multiple Box Algorithm';



insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_shared_max_qty',
    	backend_type	= 'int',
    	frontend_input	= 'text',
      	is_user_defined	= 1,
	   	is_required	= 0,
	   	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0,
    	frontend_label	= 'Maximum Qty Per Shared Box';
    	
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'ship_alternate_box',
	backend_type	= 'int',
	frontend_input	= 'select',
    source_model   = 'boxmenu/boxmenu',
    is_required	= 0,
	is_visible 	= 1,
    used_in_product_listing	= 1,
    is_filterable_in_search	= 0,
	frontend_label	= 'Alternative Packing Box';

");
	
}
else{
$installer->run("

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='catalog_product';
select @attribute_set_id:=attribute_set_id from {$this->getTable('eav_attribute_set')} where entity_type_id=@entity_type_id and attribute_set_name='Default';

insert ignore into {$this->getTable('eav_attribute_group')}
    set attribute_set_id 	= @attribute_set_id,
	attribute_group_name	= 'Shipping',
	sort_order		= 99;

select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='Shipping' and attribute_set_id = @attribute_set_id;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'ship_width',
	backend_type	= 'int',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Dimension Width';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_width';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 40;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_height',
    	backend_type	= 'int',
    	frontend_input	= 'text',
    	is_required	= 0,
    	is_user_defined	= 1,
    	frontend_label	= 'Dimension Height';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_height';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 30;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_length',
    	backend_type	= 'int',
    	frontend_input	= 'text',
    	is_required	= 0,
    	is_user_defined	= 1,
    	frontend_label	= 'Dimension Length';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_length';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 20;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_separately',
    	backend_type	= 'int',
    	frontend_input	= 'boolean',
      	is_user_defined	= 1,
	   	is_required	= 0,
    	frontend_label	= 'Ship Separately';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_separately';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 50;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_dimensional',
    	backend_type	= 'int',
    	frontend_input	= 'boolean',
      	is_user_defined	= 1,
	   	is_required	= 0,
    	frontend_label	= 'Ship with Dimensional Weight';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_dimensional';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 60;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'ship_box',
	backend_type	= 'int',
	frontend_input	= 'select',
    source_model   = 'boxmenu/boxmenu',
	is_required	= 0,
	frontend_label	= 'Packing Box';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_box';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;


insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 70;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_num_boxes',
    	backend_type	= 'int',
    	frontend_input	= 'text',
      	is_user_defined	= 1,
	   	is_required	= 0,
    	frontend_label	= 'Number of Packages';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_num_boxes';

insert ignore into {$this->getTable('eav_entity_attribute')}
    set entity_type_id 		= @entity_type_id,
	attribute_set_id 	= @attribute_set_id,
	attribute_group_id	= @attribute_group_id,
	attribute_id		= @attribute_id,
	sort_order			= 55;

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;


insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_algorithm',
    	backend_type	= 'text',
    	frontend_input	= 'textarea',
      	is_user_defined	= 1,
	   	is_required	= 0,
    	frontend_label	= 'Multiple Box Algorithm';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_algorithm';


insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_shared_max_qty',
    	backend_type	= 'int',
    	frontend_input	= 'text',
      	is_user_defined	= 1,
	   	is_required	= 0,
    	frontend_label	= 'Maximum Qty Per Shared Box';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_shared_max_qty';


insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

    	

insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
    	attribute_code 	= 'ship_alternate_box',
     	backend_type	= 'int',
		frontend_input	= 'select',
    	source_model   = 'boxmenu/boxmenu',
      	is_user_defined	= 1,
	   	is_required	= 0,
		frontend_label	= 'Alternative Packing Box';
	   	
select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_alternate_box';


insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 1,
    	is_filterable_in_search	= 0;

    	
");
}

$installer->endSetup();


