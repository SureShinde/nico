/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Validation.add(
    'helpdesk-validate-time-interval', 
    'Please enter in valid format. Example 08:00-17:30',
    function (v){
        //if(Validation.get('IsEmpty').test(v)) return true;
        result = /^[0-9]{1,2}:[0-9]{2}\s*-\s*[0-9]{1,2}:[0-9]{2}$/.test(v);
        if(result){
            hours = v.match(/(\d+):/g);
            for(index = 0; index < hours.length; index++){
                hours[index] = hours[index].substring(0, hours[index].length -1);
                if (hours[index] > 23) return false;
            }
            minutes = v.match(/:(\d+)/g);
            for(index = 0; index < minutes.length; index++){
            	minutes[index] = minutes[index].substring(1, minutes[index].length);
                if (minutes[index] > 60) return false;
            }
        
        }
        return result;
    }
        
    
);


