declare name        "AmplitudeModulation";
declare version     "1.0";
declare author      "Harry van Haaren";
declare license     "LGPLv3+";

import ("oscillator.lib");

freq = hslider( "Freq", 220, 55, 880, 1 );

vol = hslider ( "Vol", 0, 0, 1.0, 0.01);

ampMod = ( _ , oscrs(freq) ) :>  * ;

process = vol * ampMod;