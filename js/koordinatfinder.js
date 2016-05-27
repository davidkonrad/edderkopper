function beregn(lat, lng) {
document.getElementById('UTMLatGoogle').value = lat.toFixed(6); 
document.getElementById('UTMLongGoogle').value = lng.toFixed(6);
var longitude = lat;
var latitude = lng;
var k0 = 0.9996;
var eqradius = 6378137;
var eccentricity = 0.00669438;

var long2 = longitude - Math.floor((longitude+180)/360)*360;
var zone = Math.floor((long2+180)/6)+1;
var lat_radian = (Math.PI/180)*latitude;
var long_radian = (Math.PI/180)*long2;
var longorigin = (zone-1)*6-180+3;

var longoriginradian = (Math.PI/180)*longorigin;
var eccentprime = eccentricity/(1-eccentricity);
var N = eqradius/Math.sqrt(1-eccentricity*Math.sin(lat_radian)*Math.sin(lat_radian));
var T = Math.tan(lat_radian)*Math.tan(lat_radian);
var A = Math.cos(lat_radian)*(long_radian - longoriginradian);
var C = eccentprime*Math.cos(lat_radian*Math.cos(lat_radian));
var M = eqradius * ((1-eccentricity / 4-3 * eccentricity * eccentricity/64-5 * eccentricity * eccentricity * eccentricity / 256) * lat_radian - (3 * eccentricity/8+3 * eccentricity * eccentricity/32+45 * eccentricity * eccentricity * eccentricity/1024) * Math.sin(2 * lat_radian) + (15 * eccentricity * eccentricity/256+45 * eccentricity * eccentricity * eccentricity/1024) *  Math.sin(4 * lat_radian) - (35 * eccentricity * eccentricity * eccentricity/3072) * Math.sin(6 * lat_radian));

var easting = Math.floor(
k0 * N * (A + (1-T + C) *  A * A * A / 6 + (5-18 * T + T * T + 72 * C-58 * eccentprime) * A * A * A * A * A / 120) + 500000);
var northing = Math.floor(
k0 * (M + N * Math.tan(lat_radian) * (A * A / 2+(5-T + 9 *  C + 4 * C * C) *  A * A * A * A / 24  + (61-58 *  T + T * T + 600 * C - 330 * eccentprime) * A * A * A * A * A * A / 720)))

document.getElementById('zonen').value = zone;
document.getElementById('Easting').value = easting;
document.getElementById('Northing').value = northing;
			
var eastingstring = easting.toString();
var northingstring = northing.toString();
var UTMnummer = eastingstring.charAt(1)+ northingstring.charAt(2);
if (zone == 32){
	if (Math.floor(easting/100000)==4){var UTMBogstav1 = "M";}
	if (Math.floor(easting/100000)==5){var UTMBogstav1 = "N";}
	if (Math.floor(easting/100000)==6){var UTMBogstav1 = "P";}
	
	if (Math.floor(northing/100000)==60){var UTMBogstav2 = "F";}
	if (Math.floor(northing/100000)==61){var UTMBogstav2 = "G";}
	if (Math.floor(northing/100000)==62){var UTMBogstav2 = "H";}
	if (Math.floor(northing/100000)==63){var UTMBogstav2 = "J";}
	if (Math.floor(northing/100000)==64){var UTMBogstav2 = "K";}
	}
if (zone == 33){
	if (Math.floor(easting/100000)==3){var UTMBogstav1 = "U";}
	if (Math.floor(easting/100000)==4){var UTMBogstav1 = "V";}
	if (Math.floor(easting/100000)==5){var UTMBogstav1 = "W";}
	
	if (Math.floor(northing/100000)==60){var UTMBogstav2 = "A";}
	if (Math.floor(northing/100000)==61){var UTMBogstav2 = "B";}
	if (Math.floor(northing/100000)==62){var UTMBogstav2 = "C";}

    }	
	
	var UTMnummer = UTMBogstav1+UTMBogstav2+eastingstring.charAt(1)+ northingstring.charAt(2);
	document.getElementById('UTMFelt').value = UTMnummer;
} 

