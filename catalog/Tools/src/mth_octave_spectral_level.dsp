declare name "Spectral Level";
declare author "Julius O. Smith (jos at ccrma.stanford.edu)";
declare copyright "Julius O. Smith III";
declare license "STK-4.3"; // Synthesis Tool Kit 4.3 (MIT style license)


import("filter.lib");

M = 1; //number of bands per octave
process = mth_octave_spectral_level_demo(M);