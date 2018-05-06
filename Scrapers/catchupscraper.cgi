#! /usr/bin/perl -w

# Scrape2.cgi - demonstrate screen-scraping in Perl by following a link in the web page
# Displays cartoons for the past week starting on Sunday till today by following the 'tomorrow' link in toothpastefordinner.com website
#
# Created By: Harini Sridharan

use strict;
use CGI;
use WWW::Mechanize;
use HTML::TokeParser;
use DateTime;
use Time::Piece;
use Date::Calc qw(:all);


# To get a week's worth of cartoons, we make a link to last
# Sunday and then follow the "next" links.

# find last Sunday via dattime function and setting time zone to U.S East coast
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


#format the date as 2 digit day, 2 digit month and 2 digit year
my $year = $date->year();
$year = substr($year,-2);
my $target = sprintf("%s%02d%02d", $MonthNumber[$date->month()], $date->day(), $year);

# fetch the data:
my $agent = WWW::Mechanize->new();

my $url = 'http://www.toothpastefordinner.com/index.php?date=' . $target;

$agent->get($url);

my $stream = HTML::TokeParser->new(\$agent->{content});
  

# grab first div, and skip all divs that either do not have a
# class, or which class is not "container"

my $tag = $stream->get_tag("div");

while (! $tag->[1]{class} or
         ( $tag->[1]{class} and $tag->[1]{class} ne 'container') ) {
    $tag = $stream->get_tag("div");
}

#get the comic title
my $comic_title = getComicTitle($stream);


# create CGI object and generate HTML:
my $cgi = new CGI;

print $cgi->header(-type=>'text/html'),
      $cgi->start_html("Toothpaste for Dinner's Screen Scraper");

print $cgi->h1("Cartoons for the past week (starting Sunday) for toothpastefordinner.com");

print $cgi->h1($displaydate);

printComicTitle($url, $comic_title);

printImage($stream);

#get current page url

my $currentUrl = $agent->uri();

#get date from the url
my $currentUrlDate = substr($currentUrl, length($currentUrl) - 4,2);

#get today's date
my $today = DateTime->now(time_zone=>'America/New_York')->day();

#check if the toon going to be displayed is not greater than today
while($currentUrlDate ne $today ){

    print $cgi->div({style=>"margin:20px 0;border-bottom:1px solid #000"},""), "\n";

    #find text matching tomorrow  via regrex as the text on the button is 'Tomorrow >>'
    $agent->follow_link( text_regex => qr/Tomorrow/ );
    #get the new web page content
    $stream = HTML::TokeParser->new(\$agent->{content});

    # Re-parse this page same as up above:
    
    
    #get url to calculate the date that is displayed in page

    my $displayedDate = '';
    my $dislayedUrl = $agent->uri();
    #calculate the date for which the comic is displayed in page
    # if date is less than 10 just get the 2nd digit, omitting the zero
    if(substr($dislayedUrl, length($dislayedUrl) - 6,1) eq 0){
        $displayedDate = $date->year().'-'.substr($dislayedUrl, length($dislayedUrl) - 5,1).'-'.substr($dislayedUrl, length($dislayedUrl) - 4,2);
    }
    else {
        $displayedDate = $date->year().'-'.substr($dislayedUrl, length($dislayedUrl) - 6,2).'-'.substr($dislayedUrl, length($dislayedUrl) - 4,2);
    }

    #convert the date to readable format 'day, date month year'
    $displayedDate = Time::Piece->strptime($displayedDate, '%Y-%m-%d');
    $displayedDate = $displayedDate->strftime('%A, %e %B %Y');

    print $cgi->h1($displayedDate);

    # find the div containinhg the comic.

    $tag = $stream->get_tag("div");

    while (! $tag->[1]{class} or
            ( $tag->[1]{class} and $tag->[1]{class} ne 'container') )  {
        $tag = $stream->get_tag("div");
    }


    #get the comic title
    $comic_title = getComicTitle($stream);

    $url = $agent->uri();

    # print the comic title within a h3 tag
    printComicTitle ($url, $comic_title);

    printImage($stream);

    
    # update the current url
    $currentUrl = $agent->uri();
    $currentUrlDate = substr($currentUrl, length($currentUrl) - 4,2);
}


print $cgi->end_html, "\n";


#get comic title in the page
#($stream) - html parser token
sub getComicTitle {

    my($stream) = @_;
    my $comic_title = $stream->get_tag('h4');
    $comic_title = $stream->get_tag('a');
    $comic_title = $stream->get_trimmed_text("/a");
    return $comic_title;
}



#print comic title
#($url)-> the image url to link to the comic toon page
#($comic_title) -> comic title to be dosplayed.
sub printComicTitle {

    my ($url, $comic_title) = @_;
    print "<h3>Go to: ";
    print "<a href=\"$url\" target=\"_blank\">";
    print $comic_title;
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

