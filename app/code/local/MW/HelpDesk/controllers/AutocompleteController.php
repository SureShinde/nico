<?php
class MW_HelpDesk_AutocompleteController extends Mage_Core_Controller_Front_Action
{
	// loaded
    public function operatorAction()
    {		
    	$members = Mage::getModel('helpdesk/deme')->getCollection();
    	$members->getSelect()->join('mw_members',
                   'main_table.member_id = mw_members.member_id',
                    array('name'=>'mw_members.name','email'=>'mw_members.email') 
                   )->join('mw_departments',
                   'main_table.department_id = mw_departments.department_id',
                    array('department_name' =>'mw_departments.name') 
                   ); 
	   	$result = array();
	   	foreach ($members as $member) {
	   		array_push( $result, array( 'name' => $member->getName(),
	   									'email' => $member->getEmail(),
	   								    'department_name'=> $member->getDepartmentName())
	   		);
	   	}

        echo json_encode($result);
        
    }
    
	public function templateAction()
    {		
    	$templates = Mage::getModel('helpdesk/template')->getCollection()
    					->addFieldToFilter('active', array('eq' => 1));
		$result = array();
		foreach ($templates as $template) {
		array_push( $result, array( 'title' => $template->getTitle(),
	   				    'message'=> $template->getMessage())
				);
		}

        echo json_encode($result);
        
    }
	 protected function _isAllowed()
    {
        return true;
    }
    //loaded for order ticket
//	public function update_orderAction()
//    {		
//    	$orders = Mage::getModel('helpdesk/ticket')->getCollection();
//    	$orders->getSelect()
//          		->where('sender = "thecongit88@gmail.com"');
//	   	$result = array();
//	   	foreach ($orders as $order) {
//	   		array_push( $result, array( 'order' => $order->getOrder())
//	   		);
//	   	}
//        echo json_encode($result);
//        
//    }
    
    public function operatorDistinctAction()
    {		
    	$members = Mage::getModel('helpdesk/member')->getCollection();
        $reponseContent = '<ul>';
        foreach ($members as $member) {
            $reponseContent .= '<li>'.$member->getName().' ['.$member->getEmail().']</li>';
//                array_push( $result, array( 'name' => $member->getName(),
//                                                                        'email' => $member->getEmail())
//                );
        }
        $reponseContent .= '</ul>';
//        echo json_encode($result);
        $this->getResponse()->setBody($reponseContent);
    }
    
//	public function update_orderDistinctAction()
//    {		
//    	$orders = Mage::getModel('helpdesk/ticket')->getCollection();
//    	$orders->getSelect()
//          		->where('sender = "thecongit88@gmail.com"');
//        $reponseContent = '<ul>';
//        foreach ($orders as $order) {
//            $reponseContent .= '<li>#'.$order->getOrder().'</li>';
//        }
//        $reponseContent .= '</ul>';
////        echo json_encode($result);
//        $this->getResponse()->setBody($reponseContent);
//    }
    
    // loaded
    public function tagsAction()
    {		
		$tags = Mage::getModel('helpdesk/tag')->getCollection(); 
	   	$result = array();
	   	foreach ($tags as $tag) {
	   		array_push( $result, $tag->getName());
	   	}

        echo json_encode($result);
    }
}