#! /usr/bin/perl -w

# unifiedscraper.cgi - demonstrate screen-scraping in Perl  
# display 1 cartoon from 5 different websites and also display the cartoons for the past week starting on sunday for a particular website
# Created By: Harini Sridharan

use strict;
use CGI;
use WWW::Mechanize;     # This is the object that gets stuff
use HTML::TokeParser;   # This is the object that parses HTML
use DateTime;
use Time::Piece;
use Date::Calc qw(:all);

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

#define the javascript that is placed on the head


# (hide_catch_up) - hides the comic for the past days and shows the comic of the day
# (show_catch_up) - shows all the comics for the past week starting from sunday till today

my $JSCRIPT="function hide_catch_up() { 
          document.getElementById('catch-up-scraper').style.display = 'none'; 
          document.getElementById('catch-up-comic-date').style.display = 'none'; 
          document.getElementById('hide_catch_up').style.display = 'none'; 
          document.getElementById('today-comic-date').style.display = 'block'; 
          document.getElementById('show_catch_up').style.display = 'block';          
      }
       function show_catch_up() {
          document.getElementById('catch-up-scraper').style.display = 'block';
          document.getElementById('hide_catch_up').style.display = 'block';
          document.getElementById('catch-up-comic-date').style.display = 'block'; 
          document.getElementById('today-comic-date').style.display = 'none'; 
          document.getElementById('show_catch_up').style.display = 'none';           
      }";

# Generate a bunch of output:
my $cgi = new CGI;

print $cgi->header(-type=>'text/html'),
      $cgi->start_html(-title=>'Unified Scraper',
                        -script=>$JSCRIPT
                      );

printComic("Dilbert","http://dilbert.com", $caption, $source);

print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";


print '<input id="hide_catch_up" onclick="hide_catch_up()" type="button" value="Hide catch up cartoon" style="display:none; padding: 10px; background: #269ae6; text-transform:uppercase; font-size: 16px; color: #fff; border:0px; cursor: pointer;" />';
print '<input id="show_catch_up" onclick="show_catch_up()" type="button" value="Show catch up cartoon" style="padding: 10px; background: #269ae6; text-transform:uppercase; font-size: 16px; color: #fff; border:0px; cursor: pointer; display: block;" />';

print '<br />';


print $cgi->h1("toothpastefordinner"), "\n";

print $cgi->span("Go to:"),"\n";

print $cgi->a({href=>'http://toothpastefordinner.com', },'toothpastefordinner'),"\n";

print "<div id='catch-up-scraper' style='display:none;'>";

my $date = DateTime->now(time_zone=>'America/New_York');

while ( $date->day_of_week != 7 ) {
    $date->subtract( days => 1 );
}

# URLs look like:http://www.toothpastefordinner.com/index.php?date=
# format the date the way the URL needs to look:

my @MonthNumber = qw( 00 01 02 03 04 05 06
                 07 08 09 10 11 12 );

# strftime() here is like the C version; see strftime(3)
my $displaydate = $date->strftime('%A, %e %B %Y');

# format the date as 2 digit day, 2 digit month and 2 digit year
my $year = $date->year();
$year = substr($year,-2);
my $target = sprintf("%s%02d%02d", $MonthNumber[$date->month()], $date->day(), $year);

# fetch the data:

my $url = 'http://www.toothpastefordinner.com/index.php?date=' . $target;

$agent->get($url);

$stream = HTML::TokeParser->new(\$agent->{content});
  

# grab first div, and skip all divs that either do not have a
# class, or which class is not "container"

$tag = $stream->get_tag("div");

while (! $tag->[1]{class} or
         ( $tag->[1]{class} and $tag->[1]{class} ne 'container') ) {
    $tag = $stream->get_tag("div");
}

#get the comic title
$caption = getComicTitle($stream);


# generate HTML for the cartoon:

print $cgi->h1($displaydate);

printComicTitle($url, $caption);

printImage($stream);


#get current page url

my $currentUrl = $agent->uri();

#get date from the url
my $currentUrlDate = substr($currentUrl, length($currentUrl) - 4,2);

#get today's local date
my $today = DateTime->now(time_zone=>'America/New_York')->day();

my $displayedDate = '';
my $displayedUrl = '';

#check if the toon being displayed is not greater than today
while($currentUrlDate ne ($today-1) ){

    print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";


    #find text matching tomorrow 
    $agent->follow_link( text_regex => qr/Tomorrow/ );

    #get the new web page content
    $stream = HTML::TokeParser->new(\$agent->{content});

    # Re-parse this page same as up above:
    # (The "grab a cartoon" thing should be in a function which
    #  is called from a loop.  This is proof of concept only.)


    #get url to calculate the date that is displayed in page

    $displayedUrl = $agent->uri();

    # find the div containinhg the comic.

    $tag = $stream->get_tag("div");

    while (! $tag->[1]{class} or
            ( $tag->[1]{class} and $tag->[1]{class} ne 'container') )  {
        $tag = $stream->get_tag("div");
    }

    printDisplayedDate($displayedUrl);

    #get the comic title
    $caption = getComicTitle($stream);

    $url = $agent->uri();

    # print the comic title within a h3 tag
    printComicTitle ($url, $caption);

    printImage($stream);

    # update the current url
    $currentUrl = $agent->uri();
    $currentUrlDate = substr($currentUrl, length($currentUrl) - 4,2);
}

print "</div>";


$agent->get("http://www.toothpastefordinner.com/");
$stream = HTML::TokeParser->new(\$agent->{content});

# get the first "div" tag to setup the loop...
 $tag = $stream->get_tag("div");

# advance to the div with the cartoon:
while ($tag->[1]{class} and $tag->[1]{class} ne 'container') {
   $tag = $stream->get_tag("div");
}

$caption = $stream->get_tag('h4');
$caption = $stream->get_tag('a');

$displayedUrl = $caption->[1]{'href'};

$caption = $stream->get_trimmed_text("/a");

# get the cartoon:
 $toon = $stream->get_tag("p");
 $toon = $stream->get_tag("img");

# get the attributes from the "img" tag:
 $source = $toon->[1]{'src'};


print "<div id='catch-up-comic-date' style = 'display:none;'>";

printDisplayedDate($displayedUrl);

printComicTitle ($displayedUrl, $caption);

print "</div>";

print "<div id ='today-comic-date'>";

print $cgi->h3($caption), "\n\n";

print "</div>";

print $cgi->img({src=>$source, alt=>$caption}), "\n\n";

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


#get comic title in the page
#($stream) - html parser token
sub getComicTitle {

    my($stream) = @_;
    my $caption = $stream->get_tag('h4');
    $caption = $stream->get_tag('a');
    $caption = $stream->get_trimmed_text("/a");
    return $caption;
}



#print comic title
#($url)-> the image url to link to the comic toon page
#($caption) -> comic title to be dosplayed.
sub printComicTitle {

    my ($url, $caption) = @_;
    print "<h3>Go to: ";
    print "<a href=\"$url\" target=\"_blank\">";
    print $caption;
    print "</a>\n\n";
    print "</h3>";
}


#print image in the page
#($stream) - html parser token
sub printImage {
    # get the comic that is being displayed.
    my ($stream) = @_;
    my $toon = $stream->get_tag("p");
    $toon = $stream->get_tag("img");

    # get attribute:
    my $source = $toon->[1]{'src'};
    print $cgi->br(), "\n";

    print $cgi->img({src=> $source}), "\n\n";
}


sub printDisplayedDate {
    my ($url) = @_;
    #calculate the date for which the comic is displayed in page
    # if date is less than 10 just get the 2nd digit, omitting the zero
    if(substr($url, length($url) - 6,1) eq 0){
        $displayedDate = $date->year().'-'.substr($url, length($url) - 5,1).'-'.substr($url, length($url) - 4,2);
    }
    else {
        $displayedDate = $date->year().'-'.substr($url, length($url) - 6,2).'-'.substr($url, length($url) - 4,2);
    }

    #convert the date to readable format 'day, date month year'
    $displayedDate = Time::Piece->strptime($displayedDate, '%Y-%m-%d');
    $displayedDate = $displayedDate->strftime('%A, %e %B %Y');

    print $cgi->h1($displayedDate);
}