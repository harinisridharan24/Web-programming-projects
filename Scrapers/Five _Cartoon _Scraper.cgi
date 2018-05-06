#! /usr/bin/perl -w

# Five Cratoon Scraper.cgi - demonstrate screen-scraping in Perl
# Harini Sridharan

use strict;
use CGI;
use WWW::Mechanize;     # This is the object that gets stuff
use HTML::TokeParser;   # This is the object that parses HTML

# create new web agent and get a page
my $agent = WWW::Mechanize->new();
$agent->get("https://www.arcamax.com");

# create new HTML parser and get the content from the web agent
my $stream = HTML::TokeParser->new(\$agent->{content});


# First, get the title:
# HTML is like this: <div id="ctitle">Enlightenment</div>

# get the first "div" tag to setup the loop...
my $tag = $stream->get_tag("figure");

#while ($tag->[1]{class} and $tag->[1]{class} ne 'comic') {
#    $tag = $stream->get_tag("figure");
#}

# get the text of that tag:
#my $comic_title = $stream->get_trimmed_text("/figure");


# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'comic') {
    $tag = $stream->get_tag("figure");
}

# get the cartoon:
my $toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
my $source = $toon->[1]{'src'};
my $popup = $toon->[1]{'title'};
my $caption = $toon->[1]{'alt'};


# Generate a bunch of output:
my $cgi = new CGI;

print $cgi->header(-type=>'text/html'),
      $cgi->start_html('Sample Screen Scraper');

print $cgi->h1("Arcamax"), "\n";

print $cgi->p($caption), "\n";

print $cgi->img({src=>$source, alt=>$caption}), "\n\n";

print $cgi->p('<i>(' . $popup . ')</i>');

