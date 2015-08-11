import("effect.lib");

gain(x) = x : db2linear : smooth(0.999);

mid = ((_+_)/2)*(gain(vslider("[1] mid [unit:dB]", 0, -70, 0, 0.1)));
side = ((_-_)/2)*(gain(vslider("[2] side [unit:dB]", 0, -70, 0, 0.1)));

process = (_,_)<:hgroup("M/S",(mid,side))<:_,_,_,_:+,-;