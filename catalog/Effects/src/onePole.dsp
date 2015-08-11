declare name "One Pole Filter";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare version "1.29";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "https://ccrma.stanford.edu/~jos/filters/One_Pole.html";

import("filter.lib");

p = vslider("poleLocation",0,-1,1,0.01);

process = vgroup("onePole",pole(p));