declare name        "simpleAmp";

import("effect.lib"); // for smooth()

// amp takes x, and multiples by the smoothed hslider value
amp = hslider("level", 0, 0, 1, 0.001) : smooth(0.999);

// we define process to take a mono input and multiply by amp, then output mono
process = _ * amp : _ ;
