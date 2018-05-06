#! /usr/bin/perl -w

# fivecartoonscraper.cgi - demonstrate screen-scraping in Perl
# Display 1 cartoon from 5 different webistes
# Created By: Harini Sridharan

use strict;
use CGI;
use WWW::Mechanize;     # This is the object that gets stuff
use HTML::TokeParser;   # This is the object that parses HTML

# create new web agent and get a page
my $agent = WWW::Mechanize->new();

$agent->get("http://dilbert.com/");

# create new HTML parser and get the content from the web agent
my $stream = HTML::TokeParser->new(\$agent->{content});

# get the first "div" tag to setup the loop...
my $tag = $stream->get_tag("div");

# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'img-comic-container') {
    $tag = $stream->get_tag("div");
}

# get the cartoon:
my $toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
my $source = $toon->[1]{'src'};
my $caption = $toon->[1]{'alt'};

# Generate a bunch of output:
my $cgi = new CGI;

print $cgi->header(-type=>'text/html'),
      $cgi->start_html('Five Cartoon Scraper');


printComic("Dilbert","http://dilbert.com", $caption, $source);

print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";

$agent->get("http://www.toothpastefordinner.com/");
 $stream = HTML::TokeParser->new(\$agent->{content});

# get the first "div" tag to setup the loop...
 $tag = $stream->get_tag("div");

# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'container') {
   $tag = $stream->get_tag("div");
}

# get the comic title from the anchor tag
$caption = $stream->get_tag('h4');
$caption = $stream->get_tag('a');

$caption = $stream->get_trimmed_text("/a");


# get the cartoon image:
 $toon = $stream->get_tag("p");
 $toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
 $source = $toon->[1]{'src'};

printComic("Toothpastefordinner","http://toothpastefordinner.com", $caption, $source);



print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";

$agent->get("http://www.mutts.com/");
 $stream = HTML::TokeParser->new(\$agent->{content});

# get the first "div" tag to setup the loop...
 $tag = $stream->get_tag("div");

# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'home_page_daily') {
   $tag = $stream->get_tag("div");
}

# get the cartoon:
 $toon = $stream->get_tag("figure");
 $toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
 $source = $toon->[1]{'src'};
 $caption = $toon->[1]{'title'};


printComic("Mutts","http://mutts.com", $caption, $source);

print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";


$agent->get("http://www.marriedtothesea.com/");
 $stream = HTML::TokeParser->new(\$agent->{content});

# get the first "div" tag to setup the loop...
 $tag = $stream->get_tag("div");

# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'container') {
   $tag = $stream->get_tag("div");
}

# get the comic title from the anchor tag
$caption = $stream->get_tag('h4');
$caption = $stream->get_tag('a');

$caption = $stream->get_trimmed_text("/a");


# get the cartoon image:
$toon = $stream->get_tag("p");
$toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
$source = $toon->[1]{'src'};


printComic("Marriedtothesea","http://marriedtothesea.com", $caption, $source);

print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";

$agent->get("http://www.nellucnhoj.com/");
 $stream = HTML::TokeParser->new(\$agent->{content});

# get the first "div" tag to setup the loop...
 $tag = $stream->get_tag("figure");

# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'photo-hires-item') {
   $tag = $stream->get_tag("figure");
}

# get the comic title from the anchor tag

# get the cartoon image:
$toon = $stream->get_tag("a");
$toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
$source = $toon->[1]{'src'};


$tag = $stream->get_tag("div");
while ($tag->[1]{class} and $tag->[1]{class} ne 'caption') {
   $tag = $stream->get_tag("div");
}

$caption = $stream->get_tag("b");
$caption = $stream->get_trimmed_text("/b");

printComic("nellucnhoj","http://nellucnhoj.com", $caption, $source);

print $cgi->end_html, "\n";


# print comic to the web page 
# ($websiteName) - name of the website
# ($websiteUrl) - webiste url
# ($comicTitle) - title of the comic
# ($comicImageSource) - source image of the comic
sub printComic {
    my($websiteName, $websiteUrl, $comicTitle, $comicImageSource) = @_;
    print $cgi->h1($websiteName), "\n";
    print $cgi->span("Go to:"),"\n";
    print $cgi->a({href=>$websiteUrl, target=>"_blank"},$websiteName),"\n";
    print $cgi->h3($comicTitle), "\n";
    print $cgi->img({src=>$comicImageSource, alt=>$comicTitle}), "\n\n";

}