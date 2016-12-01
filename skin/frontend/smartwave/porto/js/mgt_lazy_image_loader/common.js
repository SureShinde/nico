/*$.noConflict();*/
jQuery(document).ready(function($) {
  
    placeholder = skinUrl + "images/mgt_lazy_image_loader/loader.gif";
    $('img.lazy').jail({
        timeout: 1000, 
        loadHiddenImages: true, 
        placeholder : placeholder,
    });

    //bind ajax stop to lazy load images on ajax
    $(document).ajaxStop(function(){
        
        //add a check to only lazy load those images which has data-src attribute 
        //fixes issue on product page when add to cart ajax action was again showing loader
        //for the media images 
        if($('img.lazy').attr('data-src') && $('img.lazy').attr('data-src').length){

            $("img.lazy").jail({ 
                timeout: 100, 
                loadHiddenImages: true, 
                placeholder : placeholder, 
            }).removeClass("lazy");

        }
        
    });
});