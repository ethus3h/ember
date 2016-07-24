use strict;
use warnings;
#open files
$/ = undef;
open(INPUT, "input.scdat") or print "Input error (input.scdat): $!"."!\n";
open(INPUT, "url.scdat") or print "URL error (url.scdat): $!"."!\n";
open(OUTPUT, ">>output.scdat") or print "Output error (output.scdat): $!"."!\n";
open(LOG, ">>log.scdat") or print "Log error (log.scdat): $!"."!\n";
#write errors to the log
print LOG "$!";
#variables
my $url = "url";
my $test = "Test of *(#R*n <html> this, but given the amount of <em>TIme</EM>";
my $data = "$test";
my $links = "$data";
#extract links
#HTML::SimpleLinkExtor($links);
#nls to spaces
$test =~ s/\n/ /g;
#html opening tags to spaces
$test =~ s/<\w+>/ /g;
#closing html tags to spaces
$test =~ s/<\\\W+>/ /g;
#everything that isn't a letter, number, or undescore to a space
$test =~ s/\W/ /g;
#remove extra spaces
$test =~ s/ +/ /g;
#make it lowercase
$test =~ s/A/a/g;
$test =~ s/B/b/g;
$test =~ s/C/c/g;
$test =~ s/D/d/g;
$test =~ s/E/e/g;
$test =~ s/F/f/g;
$test =~ s/G/g/g;
$test =~ s/H/h/g;
$test =~ s/I/i/g;
$test =~ s/J/j/g;
$test =~ s/K/k/g;
$test =~ s/L/l/g;
$test =~ s/M/m/g;
$test =~ s/N/n/g;
$test =~ s/O/o/g;
$test =~ s/P/p/g;
$test =~ s/Q/q/g;
$test =~ s/R/r/g;
$test =~ s/S/s/g;
$test =~ s/T/t/g;
$test =~ s/U/u/g;
$test =~ s/V/v/g;
$test =~ s/W/w/g;
$test =~ s/X/x/g;
$test =~ s/Y/y/g;
$test =~ s/Z/z/g;
#append comma
$test = $test . ",";
#remove unnecesary spaces
$test =~ s/ ,/,/g;
#append url
$test = $test . $url;
#print it
print "$test\n";
#append nl
$test = $test . "\n";
#write it to the file
print OUTPUT $test;
#write errors to the log
print LOG "$!";
close INPUT;
close OUTPUT;
close LOG;
