declare name "Notch Width";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare version "1.29";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "https://ccrma.stanford.edu/~jos/pasp/Phasing_2nd_Order_Allpass_Filters.html";

import("filter.lib");

width  = vslider("notchWidth",50,0,1000,1);
freq = vslider("notchFreq",440,30,10000,1);

process = hgroup("notchWidth",notchw(width,freq));