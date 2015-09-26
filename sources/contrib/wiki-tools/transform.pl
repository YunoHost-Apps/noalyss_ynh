#!/usr/bin/perl
#
#
#
#
#
use strict;
my $block=0;
while (my $line=<>) {
	$line=~s/\<sect\>/!/ ;
	$line=~s/\<sect1\>/!!/ ;
	$line=~s/\<sect2\>/!!!/ ;
	$line=~s/\<sect3\>/!!!!/ ;
	$line=~s/\<sect4\>/!!!!!/ ;
	$line=~s/<\/sect1>// ;
	$line=~s/<\/sect>// ;
	$line=~s/<\/sect2>// ;
	$line=~s/<\/sect3>// ;
	$line=~s/<\/sect4>// ;
	$line=~s/<p>//;
	$line=~s/<\/p>//;
	$line=~s/<itemize>//;
	$line=~s/<\/itemize>//;
	$line=~s/<\/item>//;
	$line=~s/<item>[1-9]\./#/;
	$line=~s/<item>/\*/;
	$line=~s/<toc>/(:toc:) (:num:)/;
	$line=~s/<!-- Table of contents -->//;
	if ( $line =~ /<verb>/ ) {
		$line=~s/<verb>/[@/;	
		$block=1;
	}
	if ( $line =~ /<\/verb>/ ) {
		$line=~s/<\/verb>/@]/;	
		$block=0;
	}
	$line=~s/<label id=\"(.+)\">/[[#$1]]/;
	$line=~s/<(ref id=\")(.+)(\") name=\".+\">/[[#$2|Lien]]/;
	if ( $block eq 0 ) {
		$line=~s/^ *//;
	}
	$line=~s/<url url=\"(.+)\" *name=\"(.+)\">/[[$1|$2]]/;
	$line=~s/<!doctype linuxdoc system>//;
	$line=~s/<article>//;
	$line=~s/<title>(.*)<\/title>/[++$1 ++]/;
	$line=~s/<author>//;
	$line=~s/<name>(.*)<\/name>/''$1''/;
	$line=~s/<\/author>//;
	$line=~s/<date>(.*)<\/date>/$1/;
	$line=~s/<abstract>//;
	$line=~s/<\/article>//;
	$line=~s/<tscreen>//;
	$line=~s/<\/tscreen>//;
	print "$line"; 
}
