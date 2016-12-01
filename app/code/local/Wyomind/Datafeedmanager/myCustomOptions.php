<?php

class MyCustomOptions extends Wyomind_Datafeedmanager_Model_Datafeedmanager {

    public function _eval($product, $exp, $value) {
       
        switch ($exp['options'][$this->option]) {
            case "number_format" :

                $value = number_format($value, $exp['options'][$this->option + 1], $exp['options'][$this->option + 2], '');
                //skip the two next options
                $this->skipOptions(3);


                return $value;

                break;

          

            /*             * ************* DO NOT CHANGE THESE LINES ************** */
            default :
                eval('$value=' . $exp['options'][$this->option] . '($value);');
                $this->skipOptions(1);
                return $value;
                break;
            /*             * ************* DO NOT CHANGE THESE LINES ************** */
        }
    }

}
