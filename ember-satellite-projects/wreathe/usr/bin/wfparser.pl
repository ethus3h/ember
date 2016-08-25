#wfparser.pl : Wreathe File Parser version 1.0 (10 December 2012 and 11 December 2012 (after midnight of 10 December 2012))! This script is released under the Affero GPL version 3 (run wfscan-help to see the license), with the added provisions : author attribution is required, and misrepresentation of the origin of the material is prohibited. http://futuramerlin.com/ Copyright © 2012 Futuramerlin. This program is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more details. You should have received a copy of the GNU Affero General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>. Based on Wreathe File Scanner 4.1.
use strict;
use warnings;
use utf8;
use Encode;
use open OUT => ':utf8';
use open IN => ':bytes';
#open files
$/ = undef;
open(INPUT, ".Wreathe-File-Parser/input.scdat") or print "Input error (input.scdat): $!"."!\n";
open(OUTPUT, ">>.Wreathe-File-Parser/output.scdat") or print "Output error (output.scdat): $!"."!\n";
open(LOG, ">>.Wreathe-File-Parser/log.scdat") or print "Log error (log.scdat): $!"."!\n";
#write errors to the log
print LOG "$!";
#variables
my $test = <INPUT>;
$test = decode("utf8", $test);
#nls to line break tags
#$test =~ s/\n/<br>/g;
#sgml opening tags to spaces
$test =~ s/<\w+>/ /g;
#closing sgml tags to spaces
$test =~ s/<\\\W+>/ /g;
#control characters to spaces
$test =~ s/^C/ /g;
#non-word characters to spaces
#$test =~ s/\W/ /g;
#replacement character to space
$test =~ s/\x{FFFD}/ /g;
#remove extra spaces
#$test =~ s/ +/ /g;
#make it lowercase
#$test =~ s/A/a/g;
#$test =~ s/B/b/g;
#$test =~ s/C/c/g;
#$test =~ s/D/d/g;
#$test =~ s/E/e/g;
#$test =~ s/F/f/g;
#$test =~ s/G/g/g;
#$test =~ s/H/h/g;
#$test =~ s/I/i/g;
#$test =~ s/J/j/g;
#$test =~ s/K/k/g;
#$test =~ s/L/l/g;
#$test =~ s/M/m/g;
#$test =~ s/N/n/g;
#$test =~ s/O/o/g;
#$test =~ s/P/p/g;
#$test =~ s/Q/q/g;
#$test =~ s/R/r/g;
#$test =~ s/S/s/g;
#$test =~ s/T/t/g;
#$test =~ s/U/u/g;
#$test =~ s/V/v/g;
#$test =~ s/W/w/g;
#$test =~ s/X/x/g;
#$test =~ s/Y/y/g;
#$test =~ s/Z/z/g;
#remove unnecesary spaces
#$test =~ s/ ,/,/g;
#uncomment the next line to print the output
#print "$test\n";
#append nl
$test = $test . "\n";
#write it to the file
print OUTPUT $test;
#write errors to the log
print LOG "$!";
close INPUT;
close OUTPUT;
close LOG;
