<?php

 
class Halox_Fixes_Helper_Core_Unserializearray extends Mage_Core_Helper_UnserializeArray
{
    
    function isSerialized($data) {
        // if it isn't a string, it isn't serialized
        if ( ! is_string( $data ) )
            return false;
        $data = trim( $data );
        if ( 'N;' == $data )
            return true;
        $length = strlen( $data );
        if ( $length < 4 )
            return false;
        if ( ':' !== $data[1] )
            return false;
        $lastc = $data[$length-1];
        if ( ';' !== $lastc && '}' !== $lastc )
            return false;
        $token = $data[0];
        switch ( $token ) {
            case 's' :
                if ( '"' !== $data[$length-2] )
                    return false;
            case 'a' :
            case 'O' :
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b' :
            case 'i' :
            case 'd' :
                return (bool) preg_match( "/^{$token}:[0-9.E-]+;\$/", $data );
        }
        return false;
    }
 
    /**
     * We won't unserialize string if it is not serialized
     *
     * @param string $str
     * @return array
     * @throws Exception
     */
    public function unserialize($str)
    {
        if($this->isSerialized($str)) {
 
            return parent::unserialize($str);
        }
 
        return $str;
    }
}