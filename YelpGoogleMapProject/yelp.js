var map;
var mapLatitude;
var mapLongitude;
var latLongValues;
var testMarker;
var restaurantMarker;

var northEastLatitude;
var northEastLongitude;
var southWestLatitude;
var southWestLongitude;

var markersArray = [];

function initialize () {
	
}

function sendRequest () {
	clearOverlays();	
   var originalMapBounds = getMapBounds();	
   var xhr = new XMLHttpRequest();
   var searchFieldValue = document.getElementById("searchField").value;
   xhr.open("GET", "proxy.php?term="+searchFieldValue+"&bounds="+southWestLatitude+","+southWestLongitude+"|"+northEastLatitude+","+northEastLongitude+"&limit=10");
   xhr.setRequestHeader("Accept","application/json");
   xhr.onreadystatechange = function () {
       if (this.readyState == 4) {
          var json = JSON.parse(this.responseText);
          var str = JSON.stringify(json,undefined,2);
          mapLatitude = json.region.center.latitude;
          mapLongitude = json.region.center.longitude;
          latLongValues = new google.maps.LatLng(mapLatitude,mapLongitude);
          map.setCenter(latLongValues);
          //addMarker();
          var businessArray = json.businesses;
          var restaurantInfo = "<hr>";
          for(var counter in businessArray) {
        	  var restaurantLatitude = businessArray[counter].location.coordinate.latitude;
        	  var restaurantLongitude = businessArray[counter].location.coordinate.longitude;
        	  restaurantMarker = new google.maps.LatLng(restaurantLatitude,restaurantLongitude);
        	  var labelNumber = Number(counter) + 1; 
        	  addRestaurantMarker(labelNumber+"");
        	  var restaurantName = "<h3><b><a href='"+businessArray[counter].url+"' target='_blank'>"+businessArray[counter].name+"</a></b></h3><br/>";
        	  var restaurantImageUrl = "<img src = '"+businessArray[counter].image_url+"' width='300' height='250'/><br/>";
        	  var restaurantSnippetText = "<p>"+businessArray[counter].snippet_text+"</p>";
        	  var restaurantRatingUrl = "<img src = '"+businessArray[counter].rating_img_url+"' width='75' height='15'/><br/><br/><hr>";
        	  restaurantInfo = restaurantInfo + restaurantName + restaurantImageUrl + restaurantSnippetText + restaurantRatingUrl;  
          }
          document.getElementById("output").innerHTML = restaurantInfo;
       }
   };
   xhr.send(null);
}

function initMap() {
	map = new google.maps.Map(document.getElementById('googleMap'), {
		center: {lat: 32.75, lng: -97.13},
        zoom: 16
	});
}

/*function addMarker() {
	testMarker = new google.maps.Marker({
		position : latLongValues,
		map : map
	});
}*/

function addRestaurantMarker(markerLabel) {
	testMarker = new google.maps.Marker({
		position : restaurantMarker,
		map : map,
		label : markerLabel
	});
	markersArray.push(testMarker);
}

function getMapBounds() {
	
	northEastLatitude = map.getBounds().getNorthEast().lat();
	northEastLongitude = map.getBounds().getNorthEast().lng();
	southWestLatitude = map.getBounds().getSouthWest().lat();
	southWestLongitude = map.getBounds().getSouthWest().lng();
}

function clearOverlays() {
	  for (var i = 0; i < markersArray.length; i++ ) {
	    markersArray[i].setMap(null);
	  }
	  markersArray.length = 0;
	}

