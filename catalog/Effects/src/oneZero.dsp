declare name "One Zero Filter";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare version "1.29";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "https://ccrma.stanford.edu/~jos/filters/One_Zero.html";

import("filter.lib");

z = vslider("zeroPosition",0,0,1,0.01);

process = vgroup("oneZero",zero(z));