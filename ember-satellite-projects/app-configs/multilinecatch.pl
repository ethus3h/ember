# Name:		multilinecatch-002.pl
# Version:	002
# Author:	LifeIsPain < idontlikespam (at) orvp [dot] net >
# Date:		2010-04-29
# Description:	Restrict multi line pastes. If more than 3 lines, don't allow text to go through on first enter

# Version History
# 001  2009-06-02 Initial Code
# 002  2010-04-29 Allow sequential line feeds
# 003  2016-07-31 Make it only need 1 line feed (kyan)

use strict;
use warnings;
use Xchat qw(:all);

register('Multi-line catch', '003', 'Restrict multi line pastes');
hook_print('Key Press', \&check_multiline);

my $lastcheck = '';

sub check_multiline {
	if ($_[0][0] == 65293 || $_[0][0] == 65421) {
		my $checkstring = get_info('inputbox');
		if ($checkstring ne $lastcheck && 0 < ($checkstring =~ tr/\n//s)) {
			$lastcheck = get_info('inputbox');
			prnt("The input box contains line break(s). If you really want to send this message, press Enter again.");
			return EAT_XCHAT;
		}
	}
	return EAT_NONE;
}
