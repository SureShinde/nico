var MapManager = Class.create();
MapManager.prototype = {
    initialize: function () {
        this.listStores = new Array();
        this.map = new google.maps.Map($('map'), {zoom: 5, center: new google.maps.LatLng(0, 0), mapTypeId: google.maps.MapTypeId.ROADMAP,styles:storeOption.mapStyle});
        this.markerClusterer = new MarkerClusterer(this.map, [], {gridSize: 10,maxZoom: 15});
        this.infoPopup = new google.maps.InfoWindow({content: "", maxWidth: 293});
        this.dirService = new google.maps.DirectionsService();
        this.dirDisplay = new google.maps.DirectionsRenderer({draggable: true});
        this.dirDisplay.setMap(this.map);
        this.panorama = new google.maps.StreetViewPanorama($('map'), {enableCloseButton: true, visible: false});
        this.geocoder = new google.maps.Geocoder();
        this.streetViews = new google.maps.StreetViewService();
        this.circle = new google.maps.Circle();
        this.newunit = (unit == 'km') ? 1000 : 1609;
        this.circleMarker = new google.maps.Marker({icon: circleMarkerIcon});
        this.map.controls[google.maps.ControlPosition.LEFT_TOP].push($('box-view'));
        this.run();
    },
    run: function () {
        ListStores.each(function (el) {
            var marker = new StoreManager(el);
            this.listStores.push(marker.marker);
        }.bind(this));
        this.createStoreInfo();
        this.showMarker();
    },
    searchByArea: function (str, type) {
        switch (type) {
            case "country":
                this.listStores.each(function (el) {
                    if (el.country == null || el.country != str)
                        el.isShow = false;
                });
                break;
            case "state":
                this.listStores.each(function (el) {
                    if (el.state == null || el.state.search(str) == -1)
                        el.isShow = false;
                });
                break;
            case "city":
                this.listStores.each(function (el) {
                    if (el.city == null || el.city.search(str) === -1)
                        el.isShow = false;
                });
                break;
            case "zipcode":
                this.listStores.each(function (el) {
                    if (el.zipcode == null || el.zipcode.search(str) === -1)
                        el.isShow = false;
                });
                break;
        }
    },
    resetMap: function () {
        $$('#list-tag-ul input[type=checkbox]').each(function(el){el.checked = false;});
        this.dirDisplay.setMap(null);
        this.infoPopup.close();
        this.removeCycle();
        this.setAllStoreShow();
        this.showMarker();
        this.showStoresInfo();
    },
    filterbyTag : function(){
        this.setAllStoreShow();
        var storeIds = '';
        $$('#list-tag-ul input:checkbox:checked').each(function(el){
            storeIds += ',' + el.value;
        });
        var arrayIds = storeIds.split(',');
        if (arrayIds.length ==1 && arrayIds[0] == "") {
            this.resetMap();
        } else {
            this.listStores.each(function (el) {
                if(arrayIds.indexOf(el.id)==-1){
                    el.isShow = false;
                }
            });
            this.showStoresInfo();
            this.showMarker();
        }
    },
    setAllStoreShow: function () {
        this.listStores.each(function (el) {
            el.isShow = true;
        });
    },
    drawCycle: function (center, radius) {
        this.removeCycle();
        this.circleMarker.setPosition(center);
        this.circleMarker.setMap(this.map);
        this.circle = new google.maps.Circle({
            map: this.map,
            radius: radius * this.newunit,
            fillColor: '#cd003a',
            fillOpacity: 0.1,
            strokeColor: "#000000",
            strokeOpacity: 0.3,
            strokeWeight: 1
        });
        this.circle.bindTo('center', this.circleMarker, 'position');
    },
    removeCycle: function () {
        this.circleMarker.setMap(null);
        this.circle.setMap(null);
    },
    codeAddress: function (address, radius) {
        this.geocoder.geocode({'address': address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                this.map.setCenter(results[0].geometry.location);
                this.drawCycle(results[0].geometry.location, radius);
                this.setStoreDirections(results[0].geometry.location);
                this.getStoreByRadius(radius);
                this.createStoreInfo();
                this.showStoresInfo();
                this.showMarker();
                this.map.setCenter(results[0].geometry.location);
                this.map.setZoom(Math.round(15 - Math.log(radius) / Math.LN2));
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        }.bind(this));
    },
    changeRadius: function (radius) {
        if (this.circle.getMap()) {
            this.drawCycle(this.circle.getCenter(), radius);
            this.setStoreDirections(this.circle.getCenter());
            this.getStoreByRadius(radius);
            this.showMarker();
            this.showStoresInfo();
            this.map.setCenter(this.circle.getCenter());
            this.map.setZoom(Math.round(15 - Math.log(radius) / Math.LN2));
        }
    },
    setStoreDirections: function (location) {
        this.listStores.each(function (el) {
            el.direction = google.maps.geometry.spherical.computeDistanceBetween(location, el.getPosition());
        });
        this.listStores.sort(function (a, b) {
            return a.direction - b.direction;
        });
    },
    getStoreByRadius: function (radius) {
        this.listStores.each(function (el) {
            if (el.direction >= 0 && el.direction <= radius * this.newunit)
                el.isShow = true;
            else
                el.isShow = false;
        }.bind(this));
    },
    getDirection: function (start, end, travelMode, element) {
        this.dirDisplay.setMap(this.map);
        this.dirDisplay.setPanel(element);
        var unitSystem = (unit == 'km') ? google.maps.UnitSystem.METRIC : google.maps.UnitSystem.IMPERIAL;
        this.dirService.route({
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode[travelMode],
            unitSystem: unitSystem
        }, function (response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                this.dirDisplay.setDirections(response);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        }.bind(this));
    },
    createStoreInfo: function (page) {
        var countLabel = '';
        switch (this.listStores.length) {
            case 0:
                countLabel = storeTranslate.noneStore;
                break;
            case 1:
                countLabel = storeTranslate.oneStore;
                break;
            default:
                countLabel = this.listStores.length + storeTranslate.moreStore;
                break;
        }
        $$('.info-locator .title-list h2 span').first().update(countLabel);
        if (!this.listStores.length) {
            $('result-search').show();
        }
        $('list-store-detail').innerHTML = '';
        var i = (page)?10*page:0;
        var end = i+10;
        this.listStores.each(function (marker, index) {
            if(i>end)
                throw $break;
            i++;
            var element = $$('#content-list li').first().clone(true);
            element.setAttribute('storeIndex', index);
            element.down('.tag-store a').setAttribute('href', marker.getHref());
            element.down('.tag-store img').setAttribute('src', marker.getImageSrc());
            element.down('.view-detail').setAttribute('href', marker.getHref());
            element.down('.view-detail').update(marker.name);
            element.down('.address-store').update(marker.address);
            element.down('.phone-store').update(marker.phone);
            if (marker.direction > -1)
                element.down('.tag-store span').update(Math.round(marker.direction / this.newunit* 100) / 100 + ' ' +unit);
            new google.maps.places.Autocomplete(element.down('.originA'));
            new google.maps.places.Autocomplete(element.down('.originB'));
            element.down('.originB').value = marker.address + marker.city + marker.country;
            google.maps.event.addListener(marker, 'click', function () {
                this.infoPopup.close();
                var newElement = element.clone(true);
                new google.maps.places.Autocomplete(newElement.down('.originA'));
                new google.maps.places.Autocomplete(newElement.down('.originB'));
                this.infoPopup.setContent(newElement);
                marker.setMap(this.map);
                this.infoPopup.open(this.map, marker);
                $('store_popup').up(2).addClassName('custom-popup');
            }.bind(this));
            element.observe('click', (function () {
                this.infoPopup.close();
                var newElement = element.clone(true);
                new google.maps.places.Autocomplete(newElement.down('.originA'));
                new google.maps.places.Autocomplete(newElement.down('.originB'));
                this.infoPopup.setContent(newElement);
                marker.setMap(this.map);
                this.infoPopup.open(this.map, marker);
                $('store_popup').up(2).addClassName('custom-popup');
            }).bind(this));
            $('list-store-detail').insert({bottom: element});
        }.bind(this));
        
    },
    showStoresInfo: function () {
        var i = 0;
        $$('#list-store-detail .el-content').each(function (el) {
            if (this.listStores[el.readAttribute('storeIndex')].isShow) {
                el.show();
                i++;
            }
            else
                el.hide();
        }.bind(this));
        var countLabel = '';
        switch (i) {
            case 0:
                countLabel = storeTranslate.noneStore;
                break;
            case 1:
                countLabel = storeTranslate.oneStore;
                break;
            default:
                countLabel = i + storeTranslate.moreStore;
                break;
        }
        $$('.info-locator .title-list h2 span').first().update(countLabel);
        if (i) {
            $('result-search').hide();
        } else {
            $('result-search').show();
        }
    },
    showMarker: function () {
        var bounds = new google.maps.LatLngBounds();
        this.markerClusterer.clearMarkers();
        this.listStores.each(function (el) {
            if (el.isShow) {
                this.markerClusterer.addMarker(el);
                bounds.extend(el.getPosition());
            } else
                this.markerClusterer.removeMarker(el);
        }.bind(this));
        this.map.fitBounds(bounds);
    },
    streetView: function (marker) {
        this.streetViews.getPanorama({location: marker.position, radius: 50}, this.processSVData.bind(this));
    },
    processSVData: function processSVData(data, status) {
        this.panorama.setVisible(false);
        if (status === google.maps.StreetViewStatus.OK) {
            this.panorama.setPano(data.location.pano);
            this.panorama.setPov({
                heading: 270,
                pitch: 0
            });
            this.panorama.setVisible(true);
        } else {
            window.alert('Street View data not found for this location.');
        }
    },
    clearStreetview: function () {
        this.panorama.setVisible(false);
    },
    currentPosition: function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                this.infoPopup.setPosition(pos);
                this.infoPopup.setContent('Location found.');
                this.geocoder.geocode({latLng: latlng}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        this.infoPopup.setContent(results[0]['formatted_address']);
                        $$('#form-search-distance input.form-control').first().value = results[0]['formatted_address'];
                    };
                }.bind(this));
                this.infoPopup.setMap(this.map);
                this.map.setCenter(pos);
            }.bind(this),
                    function () {
                        this.infoPopup.setPosition(this.map.getCenter());
                        this.infoPopup.setContent(true ?
                                'Error: The Geolocation service failed.' :
                                'Error: Your browser doesn\'t support geolocation.');
                    }.bind(this));

        }
    },
};

var StoreManager = Class.create();
StoreManager.prototype = {
    initialize: function (option) {
        this.position = new google.maps.LatLng(option.latitude, option.longtitude);
        this.map = null;
        this.id = option.storelocator_id;
        this.name = option.name;
        this.country = option.country;
        this.state = option.state;
        this.zipcode = option.zipcode;
        this.city = option.city;
        this.address = option.address;
        this.phone = option.phone;
        this.url = option.rewrite_request_path;
        this.direction = -1;
        this.isShow = true;
        this.icon = null;
        if (option.image_icon != null && option.image_icon != '')
            this.icon = storeOption.url_icon.replace('{id}', this.id).replace('{icon}', option.image_icon);
        this.animation = google.maps.Animation.DROP;
        this.marker = new google.maps.Marker(this);
        google.maps.event.addListener(this.marker, 'click', function () {
            this.infoPopup.close();
            var newElement = element.clone(true);
            new google.maps.places.Autocomplete(newElement.down('.originA'));
            new google.maps.places.Autocomplete(newElement.down('.originB'));
            this.infoPopup.setContent(newElement);
            this.marker.setMap(mapManager.map);
            this.infoPopup.open(mapManager.map, this.marker);
            $('store_popup').up(2).addClassName('custom-popup');
        }.bind(this));
    },
    getImageSrc: function () {
        return (typeof ListStoresImage[this.id] !== "undefined") ? storeOption.mediaUrl + 'storelocator/images' + ListStoresImage[this.id] : storeOption['defaulet_img'];
    },
    getHref: function () {
        return storeOption.baseUrl + this.url;
    },
};

function storeStreetView(el) {
    var marker = mapManager.listStores[el.up('.el-content').readAttribute('storeIndex')];
    mapManager.streetView(marker);
}

function storeOpenDerection(element) {
    var select = (element.down('.custom-popup').style.display != 'none');
    $$('#list-store-detail .custom-popup').invoke('hide');
    if (select) {
        element.down('.custom-popup').hide();
    } else
        element.down('.custom-popup').show();
}

function storeZoomIn(el) {
    mapManager.dirDisplay.setMap(null);
    var marker = mapManager.listStores[el.up('.el-content').readAttribute('storeIndex')];
    var map = mapManager.map;
    mapManager.infoPopup.close();
    var newElement = el.up('.el-content').clone(true);
    new google.maps.places.Autocomplete(newElement.down('.originA'));
    new google.maps.places.Autocomplete(newElement.down('.originB'));
    mapManager.infoPopup.setContent(newElement);
    mapManager.infoPopup.open(map, marker);
    $('store_popup').up(2).addClassName('custom-popup');
    new google.maps.places.Autocomplete($('search_position'));
    map.setCenter(marker.getPosition());
    map.setZoom(14);
}

function pinClickHaldle() {

}

function changeSelectMode(el) {
    if(el.up().down('.active'))
        el.up().down('.active').removeClassName('active');
    el.addClassName('active');
}
function storeGetDirection(element) {
    var start = element.down('input[isStart=true]').value;
    var end = mapManager.listStores[element.readAttribute('storeIndex')].getPosition();
    mapManager.getDirection(start, end,
            element.down('.vertical li.active').readAttribute('value'),
            element.down('.directions-panel'));
}

function addChangeAddress(element) {
    var start = element.down('.originA').value;
    element.down('.originA').value = element.down('.originB').value;
    element.down('.originB').value = start;
    if (element.down('.originA').readOnly) {
        element.down('.originA').removeAttribute('readOnly');
        element.down('.originA').setAttribute('isStart', true);
        element.down('.originB').removeAttribute('isStart');
        element.down('.originB').setAttribute('readOnly', true);
    }else{
        element.down('.originB').removeAttribute('readOnly');
        element.down('.originB').setAttribute('isStart', true);
        element.down('.originA').removeAttribute('isStart');
        element.down('.originA').setAttribute('readOnly', true);
    }
}
function showDistance() {
    if (!$('search-distance').hasClassName('active')) {
        $('search-distance').addClassName('active');
        $('form-search-distance').removeClassName('hide');
        $('form-search-area').addClassName('hide');
        $('search-area').removeClassName('active');
    }
}
function showArea() {
    if (!$('search-area').hasClassName('active')) {
        $('search-area').addClassName('active');
        $('form-search-area').removeClassName('hide');
        $('form-search-distance').addClassName('hide');
        $('search-distance').removeClassName('active');
    }
}

function hoverStart(element) {
    var marker = mapManager.listStores[element.readAttribute('storeIndex')];
    if (typeof marker === 'object') {
        marker.setAnimation(google.maps.Animation.BOUNCE);
    }
}
function hoverStop(element) {
    var marker = mapManager.listStores[element.readAttribute('storeIndex')];
    if (typeof marker === 'object') {
        marker.setAnimation(null);
    }
}
