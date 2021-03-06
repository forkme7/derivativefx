<?php

/*
Copyright Luxo 2008

This file is part of derivativeFX.

    derivativeFX is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    derivativeFX is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with derivativeFX.  If not, see <http://www.gnu.org/licenses/>.
    
    */

include( "/data/project/derivative/public_html/functions.php" );
i18n();
include( "/data/project/derivative/public_html/language.php" );
header( "Content-Type: application/x-javascript" );
?>

var originals = 1;
var origvalues = new Array();

function skipcookie() {
	if ( $( 'checkskip' ).checked == true ) {
		if ( navigator.cookieEnabled == true ) {
			var ablauf = new Date();
			var zeitabl = ablauf.getTime() + (21 * 24 * 60 * 60 * 1000);
			ablauf.setTime( zeitabl );
			document.cookie = "skipcheck=true; expires=" + ablauf.toGMTString();
			//window.alert("cookie set!");
		}
		else {
			// Please enable cookies to skip the login check in the next time.
			window.alert( "<?php echo $lng['x']['skco']; ?>" );
		}
	}
}


function getname( name ) {
	if ( name ) {
		$( "firstfield" ).value = name;

		loadlic( '1', name );
	}
}


function loadlic( name, image ) {
	if ( !origvalues[name] ) {
		origvalues[name] = "File:";
	}

	if ( origvalues[name] != image ) {
		//png, gif, jpg, jpeg, xcf, pdf, mid, sxw, sxi, sxc, sxd, ogg, svg, djvu.
		if ( image.match( /(.*)\.(png|gif|jpg|jpeg|xcf|pdf|mid|ogg|ogv|svg|djvu|tif|tiff)/gi ) ) {

			image = supname( image );

			document.getElementsByName( "original_" + name )[0].value = image;


			origvalues[name] = image;
			// Laden anzeigen
			$( 'loading' ).show();
			$( 'sendtonext' ).hide();
			$( 'bodyid' ).className = "bodyload";
			if ( navigator.appName.indexOf( "Explorer" ) == -1 ) { // für nicht-IE-Browser
				var url = 'licence.php?format=JSON&image=' + encodeURIComponent( image );
			} else {
				var url = 'licence.php?format=IEEX&image=' + encodeURIComponent( image );     //Internet Explorer Fix
			}

			new Ajax.Request( url, {
				method: 'get',
				onSuccess: function( transport ) {

					$( "loadtext" ).firstChild.data = "process data...";
					licence = transport.responseText;


					if ( navigator.appName.indexOf( "Explorer" ) == -1 ) //für nicht-IE-Browser
					{
						var pob = eval( "(" + transport.responseText + ")" );
						licence = pob.licenses;
						var thumburl = pob.tumburl;
					}
					else {
						var thumburl = "/tsthumb/tsthumb?f=" + encodeURIComponent( $( "original_" + name ).value.substr( 5 ) ) + "&w=120&domain=commons.wikimedia.org";
						$( "loadtext" ).firstChild.data = "(IE Fix..)";
					}


					//Falls kindknoten vorhanden, diese löschen
					if ( $( "lic" + name ).hasChildNodes() == true ) {
						//löschen
						do {
							var Knoten = $( "lic" + name ).firstChild;
							$( "lic" + name ).removeChild( Knoten );

						} while ( $( "lic" + name ).hasChildNodes() == true );
					}

					if ( licence != "NOTEXIST" ) {
						if ( licence != "DELETE" ) {
							if ( licence != "NOLIC" ) {
								// Lizenzen in array aufsplitten
								var rawlicenses = licence.split( "|" );

								if ( navigator.appName.indexOf( "Explorer" ) == -1 ) //für nicht-IE-Browser
								{
									$( "origlizid_" + name ).defaultValue = licence;
								}
								else //IE-Hack
								{
									document.imageselect.origliz_1.value = licence;
								}

								// var outputlic = rawlicenses.join(", ");
								var outputlic = "";


								for ( i = 0; i
									< rawlicenses.length; i++ ) {

									if ( rawlicenses[i] != "" ) {
										// licprevi( rawlicenses[i] );


										var clear = "";
										var myA = document.createElement( "a" );
										var mysrc = document.createAttribute( "href" );
										mysrc.nodeValue = "https://commons.wikimedia.org/wiki/Template:" + rawlicenses[i] + "";
										myA.setAttributeNode( mysrc );
										myA.setAttribute( "target","_blank" );
										var Textlink = document.createTextNode( rawlicenses[i] );
										myA.appendChild( Textlink );

										if ( i > 0 ) {
											$( "lic" + name ).appendChild( document.createTextNode( ", " ) );
										}

										$( "lic" + name ).appendChild( myA );


									}

								}

								$( "lic" + name ).firstChild.data = outputlic;
								$( "lic" + name ).className = "license";

								var titlewo = image.substr( 6 );
								$( 'img' + name ).src = thumburl;
								$( 'img' + name ).show()

								// $('sendform').enable('sendtonext'); // weiter erlauben
							} else {
								// No license found!
								$( "lic" + name ).appendChild( document.createTextNode( "No valid license found!" ) );
								$( "lic" + name ).className = "delete";
								var titlewo = image.substr( 6 );
								$( 'img' + name ).src = thumburl;
								$( 'img' + name ).show()

								if ( navigator.appName.indexOf( "Explorer" ) == -1 ) { // für nicht-IE-Browser
									$( "origlizid_" + name ).defaultValue = "unknown";
								} else {
									document.imageselect.origliz_1.value = "unknown";
								}
							}
						} else { // This image has been requested for deletion!
							$( "lic" + name ).appendChild( document.createTextNode( "<?php echo $lng['x']['reqdel']; ?>" ) );
							$( "lic" + name ).className = "delete";
							var titlewo = image.substr( 6 );
							$( 'img' + name ).src = thumburl;
							$( 'img' + name ).show()

							if ( navigator.appName.indexOf( "Explorer" ) == -1 ) { // für nicht-IE-Browser
								$( "origlizid_" + name ).defaultValue = "Delete";
							} else {
								document.imageselect.origliz_1.value = "Delete";
							}

						}
					} else {
						if ( navigator.appName.indexOf( "Explorer" ) == -1 ) { // für nicht-IE-Browser
							$( "origlizid_" + name ).defaultValue = " ";
						} else {
							document.imageselect.origliz_1.value = " ";
						}

						$( "lic" + name ).appendChild( document.createTextNode( "<?php echo $lng['en']['notex']; ?>" ) );
						$( "lic" + name ).className = "notexist";
						$( 'img' + name ).hide()
					}

					$( 'loading' ).hide(); // Loading wieder verstecken
					$( 'sendtonext' ).show();
					$( 'bodyid' ).className = "bodynorm";
				}
			} );

			$( 'sendform' ).enable();
		}
	}
}

function more() {
	if ( navigator.appName.indexOf( "Explorer" ) == -1 ) { // nurfür nicht-IE-Browser
		originals = originals + 1;
		//Add one more
		/*
		 <div id="original_2">
		 Original 2:
		 <br>
		 <input size="50" name="original_2" value="Image:">
		 <br>
		 Licence of this file: please add title
		 </div>*/

		var myDiv = document.createElement( "div" );
		var divid = document.createAttribute( "id" );
		divid.nodeValue = "original_" + originals;
		myDiv.setAttributeNode( divid );

		// id angehängt. <br>
		var mybr1 = document.createElement( "br" );
		myDiv.appendChild( mybr1 );

		// 1. Text:
		var firstText = document.createTextNode( "<?php echo $lng['x']['orig']; ?> " + originals + ":" );
		myDiv.appendChild( firstText );

		// <br>
		var mybr2 = document.createElement( "br" );
		myDiv.appendChild( mybr2 );

		// input
		var myinput = document.createElement( "input" );

		// Size = 50
		var mysize = document.createAttribute( "size" );
		mysize.nodeValue = "50";
		myinput.setAttributeNode( mysize );

		// name = original_x
		var myname = document.createAttribute( "name" );
		myname.nodeValue = "original_" + originals;
		myinput.setAttributeNode( myname );

		// value = Image:
		var myvalue = document.createAttribute( "value" );
		myvalue.nodeValue = "File:";
		myinput.setAttributeNode( myvalue );

		if ( navigator.appName.indexOf( "Explorer" ) == -1 ) { // DOM
			// onmouseover = loadlic()
			var myonmouse = document.createAttribute( "onkeyup" );
			myonmouse.nodeValue = "loadlic('" + originals + "',this.value)";
			myinput.setAttributeNode( myonmouse );

			// onmouseover = loadlic()
			var myonchange = document.createAttribute( "onchange" );
			myonchange.nodeValue = "loadlic('" + originals + "',this.value)";
			myinput.setAttributeNode( myonchange );
		} else { // IE - buggy
			myinput.attachEvent( "onkeyup", "loadlic('" + originals + "',this.value)" );
		}

		myDiv.appendChild( myinput );

		// hidden-feld
		var myinput2 = document.createElement( "input" );

		// Size = 50
		var mytype = document.createAttribute( "type" );
		mytype.nodeValue = "hidden";
		myinput2.setAttributeNode( mytype );

		// name = original_x
		var myname2 = document.createAttribute( "name" );
		myname2.nodeValue = "origliz_" + originals;
		myinput2.setAttributeNode( myname2 );

		// id
		var mynameid = document.createAttribute( "id" );
		mynameid.nodeValue = "origlizid_" + originals;
		myinput2.setAttributeNode( mynameid );

		// value = Image:
		var myvalue2 = document.createAttribute( "value" );
		myvalue2.nodeValue = "";
		myinput2.setAttributeNode( myvalue2 );


		myDiv.appendChild( myinput2 );


		// <br>
		var mybr3 = document.createElement( "br" );
		myDiv.appendChild( mybr3 );

		// text 2
		var secondtext = document.createTextNode( "<?php echo $lng['x']['lotf']; ?>: " );
		myDiv.appendChild( secondtext );

		// span
		var myspan = document.createElement( "span" );
		var myspanid = document.createAttribute( "id" );
		myspanid.nodeValue = "lic" + originals;
		myspan.setAttributeNode( myspanid );

		var myspanclass = document.createAttribute( "class" );
		myspanclass.nodeValue = "license";
		myspan.setAttributeNode( myspanclass );

		var lictext = document.createTextNode( "<?php echo $lng['x']['pan']; ?>" );
		myspan.appendChild( lictext );
		myDiv.appendChild( myspan );

		// br
		var mybr4 = document.createElement( "br" );
		myDiv.appendChild( mybr4 );

		// bild
		var myimg = document.createElement( "img" )
		var imgurl = document.createAttribute( "src" );
		imgurl.nodeValue = "loader.gif";
		var imgsty = document.createAttribute( "style" );
		imgsty.nodeValue = "display:none";
		var imgstyid = document.createAttribute( "id" );
		imgstyid.nodeValue = "img" + originals;

		myimg.setAttributeNode( imgurl );

		if ( navigator.appName.indexOf( "Internet Explorer" ) != -1 ) {
			// MSIE-Hack *roll*
			myimg.style.display = "none";
		} else {
			myimg.setAttributeNode( imgsty ); // Funktioniert nicht im IE!
		}

		myimg.setAttributeNode( imgstyid );
		myDiv.appendChild( myimg );

		// Aussen anhängen
		$( "placeformore" ).appendChild( myDiv );
	} else {
		window.alert( "Sorry, this function doesn't work yet with Internet Explorer." );
	}
}

function supname( image ) {
	// name überprüfen
	var checkerc = image.substr( 0, 6 );
	if ( checkerc.toLowerCase() == "image:" ) {
		image = image.substr( 6 );
	}

	// file: vorsetzen falls nicht vorhanden
	var checkerc = image.substr( 0, 5 );
	if ( checkerc.toLowerCase() != "file:" ) {
	//var confirm = window.confirm("You wrote '"+image+"'.\n Did you mean 'File:"+image+"'?");
		var confirm = true;
		image = "File:" + image;
	}

	return image;
}
