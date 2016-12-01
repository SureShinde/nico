<?php

class MW_HelpDesk_Model_Gateway extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('helpdesk/gateway');
    }

    public function connect($request) {
        $dataConnect = array('host' => $request['host'],
            'user' => $request['email'],
            'password' => $request['password'],
        );

        if ($request['ssl'] == 1) {
            $dataConnect['ssl'] = 'SSL';
        }
        if ($request['port'] != '') {
            $dataConnect['port'] = $request['port'];
        }
        if ($request['login'] != '') {
            $dataConnect['user'] = $request['login'];
        }

        try {
            if ($request['type'] == 1) {
                return new Zend_Mail_Storage_Imap($dataConnect);
            } else {
                return new Zend_Mail_Storage_Pop3($dataConnect);
            }
        } catch (Exception $e) {
            echo 'The Gateway' . $dataConnect['host'] . 'is failed connect!';
        }
    }

}