<!--

================================================================================

	CodeIgniter Profiler 

================================================================================

-->

<div id="codeigniter-profiler">
	<div id="codeigniter-profiler-inner-wrapper">
		<div id="codeigniter-profiler-header" class="clearfix">
			<h1>CodeIgniter <span>v<?php echo CI_VERSION . ' (' . ( CI_CORE ? 'Core' : 'Reactor' ) . ')' ?></span></h1>
			<ul class="clearfix">
				<li><?php echo anchor('http://codeigniter.com/user_guide', 'User Guide', 'target="_blank"') ?></li>
				<li><?php echo anchor('http://codeigniter.com/forums', 'Forums', 'target="_blank"') ?></li>
				<li><?php echo anchor('https://bitbucket.org/ellislab/codeigniter-reactor/issues', 'Bug Tracker', 'target="_blank"') ?></li>
			</ul>
		</div>
		<div id="codeigniter-profiler-content">
			<div id="codeigniter-profiler-sidebar">
				<ul>
				<?php foreach($sections as $section): ?>
					<li><?php echo anchor('#', lang('profiler_'.$section), 'data-section="'.$section.'"') ?></li>
				<?php endforeach ?>
				</ul>
			</div>
			<div id="codeigniter-profiler-body">
				<?php foreach($body as $section => $content): ?>
				<div id="codeigniter-profiler-section-<?php echo $section ?>" class="codeigniter-profiler-section">
					<span class="codeigniter-profiler-section-header clearfix">
						<h2><?php echo lang('profiler_'.$section) ?></h2>
						<span class="codeigniter-profiler-section-description"><?php echo lang('profiler_description_'.$section) ?></span>
					</span>
					<?php echo $content ?>
				</div>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div id="codeigniter-profiler-toggle-button">
		<span>Profiler</span>
	</div>
</div>

<!-- Profiler CSS -->
<style>
#codeigniter-profiler {
	position:absolute;
	top:0;
	left:0;
	width:100%;
	background:white;
	border-bottom:10px solid #494949;
	font-family: 'Lucida Grande', Helvetica, Arial, sans-serif;
	-moz-box-shadow: 0 2px 15px #999;
	-webkit-box-shadow: 0 2px 15px #999;
	box-shadow: 0 2px 15px #999;
}

/* -------------------------------------------------- */

#codeigniter-profiler-inner-wrapper {
	display:none;
	overflow:hidden;
}

/* -------------------------------------------------- */

#codeigniter-profiler-toggle-button {
	position:absolute;
	background:#494949;
	border:1px solid #494949;
	color:white;
	padding:5px 20px 0;
	right:300px;
	cursor:pointer;
	font-size:11px;
	text-transform:uppercase;
	background: #494949; /* Old browsers */
	background: -moz-linear-gradient(top, #494949 0%, #494949 50%, #333333 51%, #333333 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#494949), color-stop(50%,#494949), color-stop(51%,#333333), color-stop(100%,#333333)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #494949 0%,#494949 50%,#333333 51%,#333333 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #494949 0%,#494949 50%,#333333 51%,#333333 100%); /* Opera11.10+ */
	background: -ms-linear-gradient(top, #494949 0%,#494949 50%,#333333 51%,#333333 100%); /* IE10+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#494949', endColorstr='#333333',GradientType=0 ); /* IE6-9 */
	background: linear-gradient(top, #494949 0%,#494949 50%,#333333 51%,#333333 100%); /* W3C */
	text-shadow:0 0 3px #000;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;
	-webkit-border-bottom-left-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
	border-bottom-right-radius: 5px;
	font-family:Arial;
	letter-spacing:.5px;
	-moz-box-shadow: 0 2px 15px #999;
	-webkit-box-shadow: 0 2px 15px #999;
	box-shadow: 0 2px 10px #666;
}

/* -------------------------------------------------- */

#codeigniter-profiler-header {
	border:1px solid #000;
	border-width:0 0 1px 0;
	padding:10px;
	background:#494949;
}

#codeigniter-profiler-header h1 {
	margin:0;
	padding:0;
	border:0;
	font-size:13px;
	color:black;
	font-weight:bold;
	float:left;
	color:white;
}

#codeigniter-profiler-header h1 span {
	font-size:9px;
	font-weight:normal;
}

#codeigniter-profiler-header ul {
	margin:0;
	padding:0;
	list-style:none;
	float:right;
}

#codeigniter-profiler-header ul li {
	margin:0 1em 0 0;
	padding:0;
	float:left;
}

#codeigniter-profiler-header ul li a {
	font-size:11px;
	color:white;
	text-decoration:none;
}

#codeigniter-profiler-header ul li a:hover {
	color:#ccc;
}

/* -------------------------------------------------- */

#codeigniter-profiler-content {

}

/* -------------------------------------------------- */

#codeigniter-profiler-sidebar {
	width:150px;
	border-right:1px solid #535353;
	background:#D6DDE5;
	float:left;
	padding-bottom:1000px;
	margin-bottom:-1000px;
	font-size:11px;
	
}

#codeigniter-profiler-sidebar ul {
	margin: 0;
	padding: 0;
	list-style:none;
}

#codeigniter-profiler-sidebar ul li {
	margin:0;
	padding:0;
}
#codeigniter-profiler-sidebar ul li:last-child {
}

#codeigniter-profiler-sidebar ul li a {
	display:block;
	color:white;
	padding:5px;
	color:black;
	text-decoration:none;
	font-weight:bold;
}

#codeigniter-profiler-sidebar ul li a:hover {
	background-color:#8abbd7;
}

#codeigniter-profiler-sidebar ul li a.current {
	background-color: #BACCD6;
}

/* -------------------------------------------------- */

#codeigniter-profiler-body {
	padding:10px;
	overflow:auto;
	background:url('http://codeigniter.com/user_guide/images/ci_logo_flame.jpg') no-repeat bottom right scroll white;
}

.codeigniter-profiler-section {
	display:none;
}

.codeigniter-profiler-section span.codeigniter-profiler-section-header {
	display:block;
	border-bottom:1px solid #999;
	margin-bottom:10px;
}

.codeigniter-profiler-section span.codeigniter-profiler-section-header h2 {
	margin:0;
	padding:6px;
	float:left;
	font-size:18px;
	font-weight:normal;
	color:#E13300;
}

.codeigniter-profiler-section span.codeigniter-profiler-section-description {
	display:block;
	float:right;
	font-size:11px;
	font-style:italic;
	line-height:18px;
	padding:6px;
}

.codeigniter-profiler-section div.codeigniter-profiler-sub-section {

}

.codeigniter-profiler-section h3.codeigniter-profiler-sub-section-heading {
	cursor:pointer;
}

/* -------------------------------------------------- */

#codeigniter-profiler a {
	color:#0134C5;
	outline:none;
}
#codeigniter-profiler a:hover {
	color:#000;
}

#codeigniter-profiler fieldset {
	background:#eee;
	margin-bottom:10px;
}

#codeigniter-profiler label {
	background: white;
}

#codeigniter-profiler table {
	width:100%;
	background:white;
	border:1px solid #999;
	border-collapse:separate;
	border-spacing: 1px;
}

#codeigniter-profiler table tr {

}

#codeigniter-profiler table tr th, 
#codeigniter-profiler table tr td {
	font-size:12px;
	width:50%;
	overflow:auto;
}

#codeigniter-profiler table tr th:first-child, 
#codeigniter-profiler table tr td:first-child {
	font-weight:bold;
}

#codeigniter-profiler table tr th {
	background:#666666;
	color:white;
	font-weight:bold;
	padding:4px;
	text-align:left;
}

#codeigniter-profiler table tr td {
	background:#f3f3f3;
	padding:6px;
	font-weight:normal;
	color:#333;
}

#codeigniter-profiler kbd {
	color: #A70000;
	font-family: Lucida Grande,Verdana,Geneva,Sans-serif;
	font-style: normal;
	font-weight: bold;
}

#codeigniter-profiler var {
	color: #8F5B00;
	font-family: Lucida Grande,Verdana,Geneva,Sans-serif;
	font-style: normal;
	font-weight: bold;
}

#codeigniter-profiler dfn {
	color: #00620C;
	font-family: Lucida Grande,Verdana,Geneva,Sans-serif;
	font-style: normal;
	font-weight: bold;
}

#codeigniter-profiler .important {
	background: none repeat scroll 0 0 #FBE6F2;
	border: 1px solid #D893A1;
	color: #333333;
	margin: 10px 0 5px;
	padding: 10px;
}



/* =Clearfix (all browsers)
--------------------------------*/
.clearfix:after {
content: ".";
display: block;
height: 0;
clear: both;
visibility: hidden;
}
/* IE6 */ 
* html .clearfix {height: 1%;}
/* IE7 */
*:first-child+html .clearfix {min-height: 1px;}
</style>

<!-- Profiler JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script>
(function() {

	// make sure this jquery doesn't conflict
	var j  = jQuery.noConflict();

	// CodeIgniter Profiler
	var CI = CI || {};
	
	CI.Profiler = function() {
		if(this.initialize()) {
			CI.ProfilerInstance = this;
			return this;
		}
		return CI.ProfilerInstance;
	};

	CI.Profiler.prototype = {

		isLoaded: false,
		isOpen: false,

		adjustHeight: function() {
			var h = j(window).height() - 250;
			if(h > this.sideBar.height() - 20) {
				this.body.css('height', h+'px');
			}
		},

		initialize: function() {
			if(this.isLoaded) {
				return false;
			}

			var me = this;

			// get all these now for convenience
			me.wrapper 				= j('#codeigniter-profiler-inner-wrapper'),
			me.toggleButton			= j('#codeigniter-profiler-toggle-button'),
			me.sideBar				= j('#codeigniter-profiler-sidebar'),
			me.navLinks 			= j('#codeigniter-profiler-sidebar ul li a'),
			me.navLink1 			= j('#codeigniter-profiler-sidebar ul li:first-child a'),
			me.body					= j('#codeigniter-profiler-body'),
			me.sections 			= j('.codeigniter-profiler-section'),
			me.section1 			= j('.codeigniter-profiler-section:first-child'),
			me.subToggle			= j('.codeigniter-profiler-sub-section-heading'),
			me.sectionIdPre 		= '#codeigniter-profiler-section-';

			me.adjustHeight();

			j(window).resize(function() {
				me.adjustHeight();
			});

			me.toggleButton.click(function(event) { me.toggle(); });
			me.setupNav();

			return true;
		},

		resetNav: function() {
			if(this.isLoaded) {
				this.navLinks.removeClass('current');
				this.sections.hide();
			}
			return this;
		},

		setupNav: function() {
			if(this.isLoaded) {
				return false;
			}

			var me = this;
			me.navLinks.click(function(event) {
				event.preventDefault();
				me.switchSection(j(this));
			});
			me.isLoaded = true;
			me.switchSection(me.navLink1);
			return true;
		},

		switchSection: function(el) {
			if(this.isLoaded) {
				this.resetNav();
				el.addClass('current');
				j(this.sectionIdPre+el.data('section')).show();
				this.adjustHeight();
			}
			return this;
		},

		toggle: function() {
			if(this.isLoaded) {
				var me = this;
				// set isOpen in a callback to verify that slideToggle() finished
				me.wrapper.slideToggle(500, function() {
					me.isOpen = (me.wrapper.css('display') != 'none');
				});
			}
		}

	};

	// initialize the Profiler on window load
	window.addEventListener('load', function() { var p = new CI.Profiler; }, false);

})();
</script>

<!--

================================================================================

	END CodeIgniter Profiler 

================================================================================

-->
