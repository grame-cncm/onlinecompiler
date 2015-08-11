declare name        "simpleDelay";

import("effect.lib"); // for smooth
import("music.lib"); // for delay1s

// amplitude value
amp = hslider("level", 0, 0, 1, 0.001) : smooth(0.999);

// the length of the delay
delayAmount = hslider("delay (samples)", 0, 0, 48000, 1);

// delay1s is a one second delay, takes samples as parameter
// multiply by amp to change the delay volume
mydelay = amp * delay1s(delayAmount);

// we define process to take a mono input, split it, delay one half,
// then add back the original, and finally output mono
process = _ <: mydelay + _ ;
