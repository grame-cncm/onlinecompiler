declare name "Graphic Equalizer";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)
declare reference "http://asa.aip.org/publications.html";

import("filter.lib");

M = 2; //number of bands per octave
process = mth_octave_filterbank_demo(M);