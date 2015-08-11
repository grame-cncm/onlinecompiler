declare name 			"ExGringo";
declare version 		"0.1";
declare author 		"Christophe Lebreton";
declare license 		"BSD";
declare copyright 	"(c)GRAME 2013";

import("math.lib"); 
import("maxmsp.lib"); 
import("music.lib"); 
import("oscillator.lib"); 
import("reduce.lib"); 
import("filter.lib"); 
import("effect.lib");


// DEFINITION of shared parameters /////////////////////////////////
ratio_env = vslider ("ratio_env [accy:1. 0 0.166 1][color:255 255 0][osc:/gyrosc/grav/1 -1 1]",0.5,0.,0.5,0.0001); 
speed = vslider ("speed [accy:-1 0 5.34 1][color:255 255 0] [osc:/gyrosc/rrate/0 -1 1]",1,0.001,20,0.0001); 


// PHASOR_BIN //////////////////////////////
phasor_bin =  (+(float(speed)/float(SR)) : fmod(_,1.0)) ~ _;
						

// PULSAR //////////////////////////////
pulsar = (_)<(ratio_env);

// ENVELOPPE PULSAR ////////////////////
duree_env = 1/(speed: / (ratio_env*(0.125)));

// FM SYNTHESIS example from Laurent Potier  ///////////////////////////
oscillateur(vol, freq, modul) = vol * osci(freq + modul);

FM_v1 = oscillateur(vol, freq, modul1) 
		with {
				modul1 = oscillateur(volmod, freqmod, modul1mod);
				vol = vslider ( "vol [accy:-1 0 0.5 1 ] [color:255 255 0]",0,0,1,0.0001):smooth(0.998);
				freq = vslider ( "freq [accx:1.2 0. 385 0][color:255 0 0] [osc:/gyrosc/grav/0 -1 1]",1000,100,2000,1):smooth(0.998);
				volmod = vol:*(freqmod);
			 	freqmod = vslider ("freqmod [accz:1 0. 0.1 1][color:0 255 0] [osc:/gyrosc/grav/2 -1 1]", 1.4,0.1,10,0.1):*(freq);
				modul1mod = (10);
			};

// Process ExGringo  ///////////////////////////

process = hgroup( "Gringo", FM_v1 * (phasor_bin : pulsar : amp_follower_ud(duree_env,duree_env)));