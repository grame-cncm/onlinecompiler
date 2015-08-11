declare name "Feed-Forward Comb Filter";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare version "1.29";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "https://ccrma.stanford.edu/~jos/pasp/Feedforward_Comb_Filters.html";

import("filter.lib");

maxdel = 4096;
M = vslider("M",1,0,4096,1);
b0 = vslider("b0[tooltip:gain applied to delay-line input]",1,0,1,0.01);
bM = vslider("bM[tooltip:gain applied to delay-line output and then summed with input]",0,0,1,0.01);

process = hgroup("feedForwardCombFilter",ff_comb(maxdel,M,b0,bM));