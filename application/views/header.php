<!-- Header/Navbar 
Include me on almost everypage!
-->
<div class="navbar navbar-inverse navbar-static-top">
  <div class="navbar-inner">
    <a class="brand" style="color:#46a546" href="/"><?PHP echo $site_title; ?></a>
    <ul class="nav pull-right">
	    <form class="navbar-search">
			<input type="text" name="search_query" class="search-query" placeholder="Search">
		</form>
		<?PHP if($is_logged_in) { ?>
		<li class="dropdown">
	    	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
	      		Account
	      		<b class="caret"></b>
	    	</a>
		    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
		    	<?PHP if($admin) { echo '<li><a href="/admin">Admin</a></li>'; } ?>
		    	<li><a href="/stream">My Stream</a></li>
		    	<li><a href="/stream/favorites">Favorites</a></li>
		    	<li><a href="/user">Account</a></li>
		    	<li class="divider"></li>
		    	<li><a href="/login/leave">Log Out</a></li>
		    </ul>
		</li>
		<?PHP } else { ?>
		<ul class="nav">
      		<li><a href="/login">Login</a></li>
	    </ul>
		<?PHP } ?>
    </ul>
  </div>
</div>

