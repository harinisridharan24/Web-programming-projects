
<?php
// header.php : common header template for all pages
// Harini Sridharan
?>
<header>

  <div class="top-bar">
    <div class="top-bar-left">
      <ul class="dropdown menu" data-dropdown-menu>
        <li><a class="site-title" href="index.php">FotoBuk</a></li>
        <li>
          <a href="index.php">Home</a>
        </li>
        <li>
          <a href="search.php">Search</a>
        </li>
      </ul>
    </div>
    <div class="top-bar-right">

      <ul class="dropdown menu" data-dropdown-menu>
        <li><span>Welcome <b><?php echo $_SESSION['username'] ?></b></span></li>
        <li>
          <a>Menu</a>
          <ul class="menu">
            <li><a id="logout" href="logout.php">Logout</a></li>
            <li><a id="manage-account">Delete Account</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </div>

</header>