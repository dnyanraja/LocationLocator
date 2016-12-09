jQuery(document).ready(function($) {            
      setTimeout(function(){
          var ulon = document.getElementById('long').innerHTML;
          var ulat = document.getElementById('latt').innerHTML;
          var unit = document.getElementById('unit').innerHTML;
          //var urad = document.getElementById('radius').innerHTML;

            $(".loc").each(function(){
                var llat = $(this).find(".llat").text(); 
                var llon = $(this).find(".llon").text(); 

              //alert('this is radius selected ' + lolo_object.radius); // here lolo_object.radius called from localized script

              if(unit == 'Miles'){
                  var result =  findDistancemi(ulat, ulon, llat, llon);
                  if(result > lolo_object.radius){       // check if the distance is within the radius              
                      $(this).css({"display":"none"});
                  }
                }else{
                  var result =  findDistancekm(ulat, ulon, llat, llon);
                  if(result > lolo_object.radius){  // check if the distance is within the radius                    
                      $(this).css({"display":"none"});
                  }
                }
                $(this).find('.distance').html(result.toFixed(0));
                result = 0;  
              });
    }, 6000);

});

document.onreadystatechange = function(){              
          var y = document.getElementById("latt");
          var x = document.getElementById("long");
          window.navigator.geolocation.getCurrentPosition(function(pos) {
                y.innerHTML = pos.coords.latitude;
                x.innerHTML = pos.coords.longitude;
              });
     }

  /* main function */
function findDistancekm( t1, n1, t2, n2) {
      var Rm = 3961; // mean radius of the earth (miles) at 39 degrees from the equator
      var Rk = 6373; // mean radius of the earth (km) at 39 degrees from the equator    
    // convert coordinates to radians
    lat1 = deg2rad(t1);
    lon1 = deg2rad(n1);
    lat2 = deg2rad(t2);
    lon2 = deg2rad(n2);
    
    // find the differences between the coordinates
    dlat = lat2 - lat1;
    dlon = lon2 - lon1;
    
    // here's the heavy lifting
    a  = Math.pow(Math.sin(dlat/2),2) + Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin(dlon/2),2);
    c  = 2 * Math.atan2(Math.sqrt(a),Math.sqrt(1-a)); // great circle distance in radians
    dm = c * Rm; // great circle distance in miles
    dk = c * Rk; // great circle distance in km
    
    // round the results down to the nearest 1/1000
    mi = round(dm); //miles
    km = round(dk); //km
    
     return km;
}
function findDistancemi( t1, n1, t2, n2) {
      var Rm = 3961; // mean radius of the earth (miles) at 39 degrees from the equator
      var Rk = 6373; // mean radius of the earth (km) at 39 degrees from the equator    
    // convert coordinates to radians
    lat1 = deg2rad(t1);
    lon1 = deg2rad(n1);
    lat2 = deg2rad(t2);
    lon2 = deg2rad(n2);
    
    // find the differences between the coordinates
    dlat = lat2 - lat1;
    dlon = lon2 - lon1;
    
    // here's the heavy lifting
    a  = Math.pow(Math.sin(dlat/2),2) + Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin(dlon/2),2);
    c  = 2 * Math.atan2(Math.sqrt(a),Math.sqrt(1-a)); // great circle distance in radians
    dm = c * Rm; // great circle distance in miles
    dk = c * Rk; // great circle distance in km
    
    // round the results down to the nearest 1/1000
    mi = round(dm); //miles
    km = round(dk); //km
    
     return mi;
}
  // convert degrees to radians
function deg2rad(deg) {
    rad = deg * Math.PI/180; // radians = degrees * pi/180
    return rad;
}

// round to the nearest 1/1000
function round(x) {
    return Math.round( x * 1000) / 1000;
}

/*
function distance(lat1, lon1, lat2, lon2) {
  var R = 6371; // Radius of the earth in km
  var dLat = (lat2 - lat1) * Math.PI / 180;  // deg2rad below
  var dLon = (lon2 - lon1) * Math.PI / 180;
  var a = 
     0.5 - Math.cos(dLat)/2 + 
     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
     (1 - Math.cos(dLon))/2;

  return R * 2 * Math.asin(Math.sqrt(a));
}

window.navigator.geolocation.getCurrentPosition(function(pos) {
  console.log(pos); 
  console.log(
    distance(pos.coords.longitude, pos.coords.latitude, 42.37, 71.03)
  ); 
});*/