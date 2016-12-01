//---------------- CODE BY KELVIN --------------//   
//Var common
var map = null;
var infoPopup;
var markersArray = [];
var dirService;
var dirDisplay;
var geocoder;
var myCircle = '';
var radiusLatLng = '';

//Create map default
function initGoogleMap() {
    var coordinate_default = (country_default) ? new google.maps.LatLng(country_default[0], country_default[1]) : new google.maps.LatLng(37.066612, -97.039934);

    var mapOptions = {
        zoom: 5,
        center: coordinate_default,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    infoPopup = new google.maps.InfoWindow({
        content: "",
        maxWidth: 270
    });

    //Direction
    dirService = new google.maps.DirectionsService();
    dirDisplay = new google.maps.DirectionsRenderer({draggable: true});

    //geocoder
    geocoder = new google.maps.Geocoder();

    bounds = new google.maps.LatLngBounds();

    var autocompleteBilling = new google.maps.places.Autocomplete(document.getElementById('address'), {});
    if (document.getElementById("country")) {
        google.maps.event.addListener(autocompleteBilling, 'place_changed', function () {
            var place = autocompleteBilling.getPlace();
            for (var i = 0; i < place.address_components.length; i++) {
                if (place.address_components[i].types[0] == 'country') {
                    document.getElementById("country").value = place.address_components[i]['short_name'];
                    break;
                }
            }

        });
    }
    loadAjaxStore(params);
}

//Load store
function loadAjaxStore(params) {

    new Ajax.Request(urlStore, {
        method: 'post',
        postBody: params,
        parameters: params,
        onComplete: placeMarker

    });
}

//Place marker map
function placeMarker(xhr) {
    if (!xhr.responseText.isJSON()) {
        document.getElementById("list-store").innerHTML = xhr.responseText;
        loadAjaxStore(params + '&type=map');

    } else {
        var data = xhr.responseText.evalJSON();
        //Reset markersArray
        for (var i = 0; i < markersArray.length; i++) {
            google.maps.event.clearListeners(markersArray[i], 'click');
            markersArray[i].setMap(null);
        }
        markersArray.length = 0;
        var bounds = new google.maps.LatLngBounds();

        //Set location item on Map
        for (var i = 0; i < data.stores.length; i++) {
            var _item = data.stores[i];

            var config_marker = {
                position: new google.maps.LatLng(_item.latitude, _item.longtitude),
                map: map,
                zoom_level: _item.zoom_level,
                store_id: _item.storelocator_id,
                flat: true
            };

            if (_item.image_icon != null && _item.image_icon != '') {
                var icon_path = url_icon.replace('{id}', _item.storelocator_id);
                icon_path = icon_path.replace('{icon}', _item.image_icon);
                config_marker = Object.extend(config_marker, {icon: icon_path});
            }

            var marker = new google.maps.Marker(config_marker);

            //Event on location item
            google.maps.event.addListener(marker, 'click', function () {
                focusAndPopup(this.store_id);
            });

            bounds.extend(marker.getPosition());

            //Add marker to array
            markersArray[_item.storelocator_id] = marker;

        }

        if (markersArray.length != 0) {
            if (store_lat && store_lon && radius) {
                radiusLatLng = new google.maps.LatLng(store_lat, store_lon);
                //Search by location
                radiusLocator(true);
            } else {
                //Fix bound center
                map.fitBounds(bounds);
            }

        } else {
            //alert("here1");
            document.getElementById('store-content').innerHTML = 'Store Not Found!';
        }

        $('list-store').setStyle({'display': 'block'});
        $('store-loader').setStyle({'display': 'none'});
    }
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        // initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
        getAddressFromLatLang(position.coords.latitude,position.coords.longitude);
        var config_marker = {
                position: initialLocation,
                map: map,
            };
        // new google.maps.Marker(config_marker);
        // map.setCenter(initialLocation);
        // map.setZoom(7);
        }, function() {
        
        });
    }  
}

function getAddressFromLatLang(lat,lng){
    
    var geocoder = new google.maps.Geocoder();
    var latLng = new google.maps.LatLng(lat, lng);
        geocoder.geocode( { 'latLng': latLng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            if(!$('address').value){
                initialLocation = new google.maps.LatLng(lat,lng);
                $('address').value = results[1].formatted_address;
                $('radius').disable = false;
                var cntry = $('address').value;
                cntry.slice(-3); //Outputs: 1
                document.getElementById("country").value = cntry.slice(-3,-1);
               // $$('#store_search button').last().click();                
            }
          }
        }else{
            //alert("Geocode was not successful for the following reason: " + status);
        }
        });
    }
//Focus show poup
function focusAndPopup(store_id, dir) {
    var marker = markersArray[store_id];
    if (typeof marker !== 'object') {
        return;
    }

    if (!dir && !$('s_store-' + store_id).hasClassName('active')) {
        map.setZoom(Number(marker.zoom_level));
        dirDisplay.setMap(null);
    }
    if ((radiusLatLng != '') && !dir) {
        $$('#list-store li .nav').invoke('setStyle', {'display': 'none'});
        $('s_store-' + store_id).down('.nav').setStyle({'display': 'block'}).removeClassName('up');
        document.getElementById('s_position-' + store_id).value = document.getElementById('address').value;
        var request = {
            origin: radiusLatLng,
            destination: marker.getPosition(),
            travelMode: google.maps.TravelMode.DRIVING
        };
        if (unit == 'km') {
            request = Object.extend(request, {unitSystem: google.maps.UnitSystem.METRIC});
        } else {
            request = Object.extend(request, {unitSystem: google.maps.UnitSystem.IMPERIAL});
        }
        dirService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                dirDisplay.setMap(map);
                dirDisplay.setPanel(document.getElementById('store_navigation-' + store_id));
                dirDisplay.setDirections(response);

            } else {

                alert("It can't be able to calculate this distance.\nBecause the distance between the two location is too far.");
            }

        });
    } else {
        navigation(store_id);
    }

    //Current location
    removeAndActive(store_id);
    //Move sidebar
    moveSidebarMoveTop(store_id);
    //Move location item to Center
    map.panTo(marker.getPosition());

    var store = $('s_store-' + store_id);
    var info = '<div id="store_id-' + store_id + '">';
    info += store.down('.info').innerHTML;
    info += '<p class="store_detail">' + store.down('.store_detail').innerHTML + '</p>';
    info += '<p>';
    info += '<input class="position" value="" id="position-' + store_id + '" type="text">';
    info += '<input class="sbutton" onclick="calcRoute(' + store_id + ',' + marker.getPosition().lat() + ',' + marker.getPosition().lng() + ',0); return false;" src="' + store.down('.sbutton').getAttribute('src') + '" type="image">';
    info += '</p>';
    info += '</div>';

    infoPopup.close();
    infoPopup.setContent("<div class='store_popup'>" + info + "</div>");
    infoPopup.store_id = store_id;
    infoPopup.open(map, marker);

    //Autocomplete search    
    google.maps.event.addListener(infoPopup, 'domready', function () {
        //Autocomplete address
        new google.maps.places.Autocomplete((document.getElementById('position-' + this.store_id)));
        //Offset address
        document.getElementById('position-' + this.store_id).value = document.getElementById('s_position-' + this.store_id).value;
    });


}

//Search radius by location
function radiusLocator(sort) {
    if (typeof myCircle !== 'object') {
        myCircle = new google.maps.Circle({
            center: radiusLatLng,
            map: map,
            radius: radius,
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#B9D3EE",
            fillOpacity: 0.35
        });
    }
    var myBounds = myCircle.getBounds();

    //Hide and remove inside location item
    var count = 0;
    for (var i in markersArray) {
        if (typeof markersArray[i] !== 'object')
            continue;
        count++;
        if (!myBounds.contains(markersArray[i].getPosition())) {
            count--;
            markersArray[i].setMap(null);
            $('s_store-' + markersArray[i].store_id).hide();
        } else if (sort_store == 'distance') {
            document.getElementById('s_store-' + markersArray[i].store_id).setAttribute('distance', google.maps.geometry.spherical.computeDistanceBetween(radiusLatLng, markersArray[i].getPosition()));
        }
    }
    if (count <= 0) {
        document.getElementById('store-content').innerHTML = 'Store Not Found!';
        return;
    }
    
    if (sort_store == 'distance' && sort) {
        var listStore = $$("#list-store li");
        $("list-store").innerHTML = '';
        listStore.sort(function (a, b) {
            return parseFloat(a.getAttribute('distance')) - parseFloat(b.getAttribute('distance'));
        }).each(function (index, el) {
            if (index) {
                var style = index.getAttribute('style') ? 'style="' + index.getAttribute('style') + '"' : '';
                $("list-store").insert('<li id="' + index.getAttribute('id') + '" class="item" ' + style + '>' + index.innerHTML + '</li>');
            }
        });
    }

    //Set center bound
    map.setCenter(radiusLatLng);
    map.setZoom(Math.round(14 - Math.log(radius / 1000) / Math.LN2));
}

//Direction to my location
function calcRoute(store_id, lat_end, lon_end, flag) {

    var address = '';
    if (flag) {
        address = document.getElementById('s_position-' + store_id).value;
        //Offset address
        if (document.getElementById('position-' + store_id)) {
            document.getElementById('position-' + store_id).value = address;
        }
    } else {
        address = document.getElementById('position-' + store_id).value;
        //Offset address
        document.getElementById('s_position-' + store_id).value = address;
    }
    if (!address) {
        focusAndPopup(store_id);
        alert('Please enter an address!');
        return;
    }
    geocoder.geocode({'address': address}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var request = {
                origin: results[0].geometry.location,
                destination: new google.maps.LatLng(parseFloat(lat_end), parseFloat(lon_end)),
                travelMode: google.maps.TravelMode.DRIVING
            };
            if (unit == 'km') {
                request = Object.extend(request, {unitSystem: google.maps.UnitSystem.METRIC});
            } else {
                request = Object.extend(request, {unitSystem: google.maps.UnitSystem.IMPERIAL});
            }

            dirService.route(request, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    $('store_navigation-' + store_id).setStyle({'display': 'block'});
                    $('s_store-' + store_id).down('.nav').setStyle({'display': 'block'});
                    dirDisplay.setMap(map);
                    dirDisplay.setPanel(document.getElementById('store_navigation-' + store_id));
                    dirDisplay.setDirections(response);
                    focusAndPopup(store_id, true);
                } else {
                    focusAndPopup(store_id);
                    alert("It can't be able to calculate this distance.\nBecause the distance between the two location is too far.");
                }

            });
        } else {
            focusAndPopup(store_id);
            alert("Can't find the coordinates for this location");
        }
    });

}

function fillterByTag(storeids, tagid) {
    $$('.store-locator-tag a').invoke('removeClassName', 'tag_active');
    $('storelocator_tag_' + tagid).addClassName('tag_active');

    //removeAndActive
    removeAndActive();
    var bounds = new google.maps.LatLngBounds();
    infoPopup.close();
    dirDisplay.setMap(null);

    if (storeids != 'all') {
        var arr = storeids.split(',');

        //Hide and remove inside location item
        for (var i in markersArray) {
            if (typeof markersArray[i] !== 'object')
                continue;

            markersArray[i].setMap(null);
            $('s_store-' + markersArray[i].store_id).removeClassName('item').hide();

            for (var j = 0; j < arr.length; j++) {
                if (markersArray[i].store_id == arr[j]) {
                    markersArray[i].setMap(map);
                    $('s_store-' + markersArray[i].store_id).addClassName('item').show();
                    bounds.extend(markersArray[i].getPosition());
                    break;
                }
            }

        }
    } else {
        for (var i in markersArray) {
            if (typeof markersArray[i] === 'object') {
                if (!$('s_store-' + markersArray[i].store_id).hasClassName('item')) {
                    markersArray[i].setMap(map);
                    $('s_store-' + markersArray[i].store_id).addClassName('item').show();
                }
                bounds.extend(markersArray[i].getPosition());
            }
        }
    }
    if (radiusLatLng != '') {
        radiusLocator(false);
    } else {
        map.fitBounds(bounds);
    }
}

function removeAndActive(store_id) {

    //Active current item
    var els = $$('#list-store li');
    els.invoke('removeClassName', 'active');

    if (store_id) {
        $('s_store-' + store_id).addClassName('active');
    }

    els.each(function (el) {
        if (!el.hasClassName('active')) {
            el.down('.store_navigation').innerHTML = '';
        }
    });
}

//Scroll to current item
function moveSidebarMoveTop(store_id) {
    var first_item = $('list-store').down('.item').cumulativeOffset();
    var current_item = $('s_store-' + store_id).cumulativeOffset();
    document.getElementById('list-store').scrollTop = current_item[1] - first_item[1];
}

function navigation(store_id) {
    $$('#list-store li .store_navigation').invoke('setStyle', {'display': 'none'});
    if (!$('s_store-' + store_id).down('.nav').hasClassName('up')) {
        $('s_store-' + store_id).down('.nav').addClassName('up');
        $('store_navigation-' + store_id).setStyle({'display': 'block'});
    } else {
        $('s_store-' + store_id).down('.nav').removeClassName('up');
    }
    moveSidebarMoveTop(store_id);
}

//Hover location
function hoverStart(store_id) {
    store_id = Number(store_id);
    var marker = markersArray[store_id];
    if (typeof marker === 'object') {

        if (!document.getElementById('s_position-' + store_id).getAttribute('autocomplete')) {
            new google.maps.places.Autocomplete(document.getElementById('s_position-' + store_id));
        }
        marker.setAnimation(google.maps.Animation.BOUNCE);
    }

}
function hoverStop(store_id) {
    store_id = Number(store_id);
    var marker = markersArray[store_id];
    if (typeof marker === 'object') {
        marker.setAnimation(null);
    }
}





