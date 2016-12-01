/*$.noConflict();*/
jQuery(document).ready(function($) {
  jQuery('img.lazy').jail({
    event: 'load+scroll',
    placeholder : "/porto/skin/frontend/default/default/images/mgt_lazy_image_loader/loader.gif",
  });
});