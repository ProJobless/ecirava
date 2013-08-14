<!-- 
	05/14/2013 - Casually Inept
	This file should be included on every page between the <head> tags
	so that they have the required JS and CSS documents included
-->

<!--JQuery and Extensions-->
<?PHP if($jquery) { echo '<script type="text/javascript" src="/resources/js/jquery/1.9.1.js"></script>'; } ?>

<!--Bootstrap (CSS and JS)-->
<?PHP
if($typeahead)  { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-typeahead.js"></script>';  }
if($transition) { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-transition.js"></script>'; }
if($tooltip)    { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-tooltip.js"></script>';    }
if($tab)        { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-tab.js"></script>';        }
if($scrollspy)  { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-scrollspy.js"></script>';  }
if($popover)    { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-popover.js"></script>';    }
if($modal)      { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-modal.js"></script>';      }
if($dropdown)   { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-dropdown.js"></script>';   }
if($collapse)   { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-collapse.js"></script>';   }
if($carousel)   { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-carousel.js"></script>';   }
if($button)     { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-button.js"></script>';     }
if($affix)      { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-affix.js"></script>';      }
if($alert)      { echo '<script type="text/javascript" src="/resources/js/bootstrap/bootstrap-alert.js"></script>';      }
?>

<link rel="stylesheet" type="text/css" href="/resources/css/magic-bootstrap-min.css" />

<!--Additional CSS-->
<link rel="stylesheet" type="text/css" href="/resources/css/main.css" />
<?PHP if(isset($additional_css)) { echo $additional_css; } ?> <!--Controller Specific loading-->


<!--Additional JS-->
<script rel="text/javascript" src="/resources/js/main.js"; /></script>
<?PHP if(isset($additional_js)) { echo $additional_js; } ?> <!--Controller Specific Loading-->

<!---Google Analytics-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-29045369-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })(); 

</script>
<!--Google Checkout-->