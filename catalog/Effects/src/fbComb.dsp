declare name "Feed-Back Comb Filter";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare version "1.29";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "https://ccrma.stanford.edu/~jos/pasp/Feedback_Comb_Filters.html";

import("filter.lib");

maxdel = 4096;
N = vslider("del[tooltip:current (float) comb-filter delay between 0 and maxdel]",1,0,4096,1);
b0 = vslider("b0[tooltip:gain applied to delay-line input and forwarded to output]",1,0,1,0.01);
aN = vslider("bM[tooltip:minus the gain applied to delay-line output before summing with the input and feeding to the delay line]",0,0,1,0.01);

process = hgroup("feedBackCombFilter",fb_comb(maxdel,N,b0,aN));