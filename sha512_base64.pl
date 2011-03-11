#!/usr/bin/env perl

use strict;

use Digest::SHA qw(sha512_base64 sha512);
use URI::Escape;

sub base64_pad {
    my ($b64_digest) = @_;
    while (length($b64_digest) % 4) {
        $b64_digest .= '=';
    }
    return $b64_digest;
}

print uri_escape(base64_pad(sha512_base64($ARGV[0])));
