declare name        "spat";
declare version     "1.0";
declare author      "Grame";
declare license     "BSD";
declare copyright   "(c) GRAME 2006";

//==========================================================
//
//                      GMEM SPAT
//  Faust implementation of L. Pottier's spatializer
//
//==========================================================




//------------------------------------------------------
// Level of an output channel
//------------------------------------------------------
//  i = index of the channel (ranging from 0 to n-1)
//      n = total number of channels
//  a = source angle (ranging from 0 to 1)
//  d = source distance (ranging from 0 to 1)
//------------------------------------------------------

scaler(i,n,a,d) = sqrt(max(0.0, 1.0 - abs( fmod(a+0.5+float(n-i)/n, 1.0) - 0.5 ) * (n*d))) * (d/2.0+0.5);



//------------------------------------------------------
// n-output voices spatializer
//      n = number of output channels
//  a = source angle (ranging from 0 to 1)
//  d = source distance (ranging from 0 to 1)
//
// spat uses the parametric builder : par(i,n,E(i))
// which puts in parralle E(0), E(1), ...E(n-1)
//------------------------------------------------------
smooth(c)   = *(1-c) : +~*(c);
spat(n,a,d) = _ <: par(i, n, *( scaler(i, n, a, d) : smooth(0.9999) ));




//------------------------------------------------------
// Example : a mono input spatialized on 8 voices
//------------------------------------------------------

angle           = hslider("angle",    0.0, 0, 1, 0.01);
distance        = hslider("distance", 0.5, 0, 1, 0.01);

process         = vgroup("Spatializer 1x8", spat(8, angle, distance));


