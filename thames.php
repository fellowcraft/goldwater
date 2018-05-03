<?php
/*
flags:
-w   writes to file
*/

// --------------------- cryptographically secure seeding ----------------------
$iseed1 = random_int(1,2147483562);
$iseed2 = random_int(1,2147483398); 
stats_rand_setall($iseed1,$iseed2);

// --------------------- orc ---------------------------------------------------
$orchestra = '
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

a1, a2  diskin2 "../WAV/thames2.wav", ifreq, iskiptime, 1

i1 = birnd(1)
if (i1 > 0) then
outs a2*aAmpEnv, a1*aAmpEnv
else
outs a1*aAmpEnv, a2*aAmpEnv
endif




galeft    =         galeft  +  a1 * irevSend
garight   =         garight +  a2 * irevSend
endin

instr 99                           ; global reverb ----------------------------
a1,  a2  reverbsc  galeft,  garight, 0.9, 18000, sr, 0.8, 1 
outs   a1, a2              
galeft    =    0
garight   =    0 
endin
';
// --------------------- init vars ---------------------------------------------
$tailT   = 4;
$startT  = 60*0;
$endT    = 60*3+42;
$TT      = 60*60*4;
$Events  = intval($TT*1);         // events  per second
// --------------------------- sco head ----------------------------------------
$scoreHeader =  '; Reverb
i99     0   '.($TT+$tailT).'   

'
;



// --------------------------- main p1-px fields -------------------------------
// start time
function p2() {
Global $TT;
return round(stats_rand_gen_funiform(1,$TT),1);
}

// duration
$TDur = 1;
function idur() {
Global $TDur;
//$TDur = round(14.4-stats_rand_gen_beta(5,1)*14,1);

$TDur = round(stats_rand_gen_funiform(0.1,60),1); 

return $TDur;
}

// amplitude
function iamp() {
return stats_rand_gen_iuniform(-36,6);
// return -1;
}

function ifreq() {

// if(rand(0,1)) { return 1; } else { return -1; } 

return round(stats_rand_gen_funiform(.5,1.5),3); 

// return 1;
}

$rndat = 1;

function iat() {
Global $TDur;
Global $rndat;
$rndat = stats_rand_gen_funiform(0.01,$TDur);

return round($rndat,2);
}

function irel() {
Global $TDur;
Global $rndat;
return round($TDur-$rndat,2);
}


function ipanStart() {
return round(stats_rand_gen_funiform(0,1),2); 
//return .5;
}

function ipanEnd() {
return round(stats_rand_gen_funiform(0,1),2); 
//return .5;
}

function iskiptime() {
Global $startT;
Global $endT;
return round(stats_rand_gen_funiform($startT,$endT),3); 
}

function irevSend() {
return round(stats_rand_gen_funiform(0,1),2); 
//return 0;
}


// ---------------------------------- generate data ----------------------------
$scoreData = '';
function gen_scoreData($Events){

if($Events == 0) exit;

Global $scoreData;
$P=5;

for($i=0;$i<$Events;$i++) {
$scoreData = $scoreData.
"i1 ".
str_pad(p2(),$P) ." ".
str_pad(idur(),$P) ." ".
str_pad(iamp(),$P+1) ." ".
str_pad(ifreq(),$P) ." ".
str_pad(iat(),$P) ." ".
str_pad(irel(),$P) ." ".
str_pad(ipanStart(),$P) ." ".
str_pad(ipanEnd(),$P) ." ".
str_pad(iskiptime(),$P+2) ." ".
str_pad(irevSend(),$P) ." ".
PHP_EOL
;
}
}

// ----------------------------------------------------------------------------

gen_scoreData($Events);

// ----------------------------------------------------------------------------

$csd = "<CsoundSynthesizer>
<CsOptions>
</CsOptions>
<CsInstruments>"
.$orchestra.
"</CsInstruments>
<CsScore>"
.$scoreHeader
.$scoreData."
e 
</CsScore>
</CsoundSynthesizer>";


// ----------------------------------- exit options ---------------------------

function write_to_file() {
global $csd;
$Now = New DateTime();
$filename = $Now->Format('YmdHis').'.csd';
$myfile = fopen("CSD/".$filename, "w") or die("Unable to open file!");
fwrite($myfile, $csd);
fclose($myfile);
}

function display() {
global $csd;
echo $csd;
}

if (isset($argv[1])) {
foreach($argv as $arg) { 
if($arg == "-w") {
write_to_file(); } 
} 
} else {
display(); }

?>
