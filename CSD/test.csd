<CsoundSynthesizer>
<CsOptions>
</CsOptions>
<CsInstruments>
sr = 44100
kr =  4410
ksmps = 10
nchnls = 2
galeft  init 0
garight init 0

instr 1
idur            = p3
iamp            = ampdb(p4)
ifreq           = p5   ;  1x - negative backwards 
iat             = p6
irel            = p7
ipanStart       = p8
ipanEnd         = p9
iskiptime       = p10
irevSend        = p11/100

kpan    linseg  ipanStart, idur, ipanEnd
aAmpEnv linseg 0, iat,  iamp, irel, 0

aLFO     lfo 990, 5, 0 ;  itype

a1, a2  diskin2 "../WAV/storm.wav", ifreq, iskiptime, 1

outs a1*aAmpEnv, a2*aAmpEnv


galeft    =         galeft  +  a1 * irevSend
garight   =         garight +  a2 * irevSend
endin

instr 99                           ; global reverb ----------------------------
a1,  a2  reverbsc  galeft,  garight, 0.9, 18000, sr, 0.8, 1 
outs   a1, a2              
galeft    =    0
garight   =    0 
endin
</CsInstruments>
<CsScore>; Reverb
i99     0   30   

i1 0 30  3     1     0.71  29.69 0.73  0.67  460  0.18  
e 
</CsScore>
</CsoundSynthesizer>
