declare name "Pink noise (1/f noise) generator (third-order approximation)";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "https://ccrma.stanford.edu/~jos/sasp/Example_Synthesis_1_F_Noise.html";

import("oscillator.lib");

amp = vslider("Amplitude",0.5,0,1,0.01);
process = vgroup("Pink Noise Generator",pink_noise*amp);