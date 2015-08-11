//---------------------------------
// A square wave oscillator
//---------------------------------

T = hslider("Period",1,0.1,100.,0.1); // Period (ms)

N = 44100./1000.*T:int; // The period in samples

a = hslider("Cyclic ratio",0.5,0,1,0.1); // Cyclic ratio

i  = +(1)~%(N):-(1); // 0,1,2...,n

process = i,N*a : < : *(2) : -(1) ;
