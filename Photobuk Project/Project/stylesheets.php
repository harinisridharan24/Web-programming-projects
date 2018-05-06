<?php
// stylesheets.php : common template for all css files file
// Harini Sridharan
?>
<?php
$cssDir = "styles"; //folder where all CSS files live

//Link each page to its CSS file
$styles = [
    'register.php' => 'register.css',
    'login.php' => 'login.css',
    'index.php' => 'index.css',
    'search.php' => 'index.css',
    'confirm_user.php' => 'index.css'
];

?>
<!-- CSS common to all pages -->
<link rel="stylesheet" type="text/css" href="foundation.min.css">
<link rel="stylesheet" type="text/css" href="foundation-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css" integrity="sha256-itWEYdFWzZPBG78bJOOiQIn06QCgN/F0wMDcC4nOhxY=" crossorigin="anonymous" />
<link rel="stylesheet" type="text/css" href="<?="common.css"?>">
<!-- CSS, specific to the current page -->
<link rel="stylesheet" type="text/css" href="<?="$styles[$this_page]"?>">
