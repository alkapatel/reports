<?php require_once('connect.php'); ?>
<?php require_once('header1.php'); ?>

<!-- include jQuery + carouFredSel plugin -->
		<script type="text/javascript" language="javascript" src="jquery-1.8.2.min.js"></script>
<script type="text/javascript" language="javascript" src="jquery.carouFredSel-6.2.1-packed.js"></script>
		<!-- optionally include helper plugins -->
<script type="text/javascript" language="javascript" src="helper-plugins/jquery.mousewheel.min.js"></script>
<script type="text/javascript" language="javascript" src="helper-plugins/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" language="javascript" src="helper-plugins/jquery.transit.min.js"></script>
<script type="text/javascript" language="javascript" src="helper-plugins/jquery.ba-throttle-debounce.min.js"></script>

		<!-- fire plugin onDocumentReady -->
<script type="text/javascript" language="javascript">
			$(function() {

				//	Basic carousel, no options
				$('#foo0').carouFredSel();

				//	Basic carousel + timer, using CSS-transitions
				$('#foo1').carouFredSel({
					auto: {
						pauseOnHover: 'resume',
						progress: '#timer1'
					}
				}, {
					transition: true
				});


				//	Responsive layout, resizing the items
				$('#foo4').carouFredSel({
					responsive: true,
					width: '100%',
					scroll: 2,
					items: {
						width: 240,
					//	height: '30%',	//	optionally resize item-height
						visible: {
							min: 2,
							max: 7
						}
					}
				});

				//	Fuild layout, centering the items
				$('#foo5').carouFredSel({
					width: '100%',
					scroll: 2
				});

			});
		</script>

		<style type="text/css" media="all">
			
			#intro {
				width: 580px;
				margin: 0 auto;
			}
			.wrapper {
	width: 960px;
	margin: auto auto 0px;
	padding: 5px 0px;
			}
			.list_carousel {
				background-color: #fff;
				margin: 0 0 10px 10px;
				width: 920px;
			}
			.list_carousel ul {
				margin: 0;
				padding: 0;
				list-style: none;
				display: block;
			}
			.list_carousel li {
	color: #999;
	text-align: center;
	border: 5px solid #999;
	width: 200px;
	height: 140px;
	padding: 0;
	margin: 6px;
	display: block;
	float: left;
			}
			.list_carousel.responsive {
				width: auto;
				margin-left: 0;
			}
			.clearfix {
				float: none;
				clear: both;
			}
			.prev {
				float: left;
				margin-left: 10px;
			}
			.next {
				float: right;
				margin-right: 10px;
			}
			.pager {
				float: left;
				width: 200px;
				text-align: center;
			}
			.pager a {
				margin: 0 5px;
				text-decoration: none;
			}
			.pager a.selected {
				text-decoration: underline;
			}
			.timer {
				background-color: #999;
				height: 6px;
				width: 0px;
			}
		</style>
		<script type="text/javascript" src="jquery-1.7.2.min.js">
</script>
<script type="text/javascript">
$(function() {
setInterval("rotateImages()", 3000);
});
function rotateImages() {
var curPhoto = $("#photoshow div.current");
var nextPhoto = curPhoto.next();
if(nextPhoto.length == 0) {
nextPhoto = $("#photoshow div:first");
}
curPhoto.removeClass('current').addClass('previous');
nextPhoto.css({opacity:0.0}).addClass('current').animate({opacity: 1.0}, 1000, function() {
curPhoto.removeClass('previous');
});

}
</script>
<style>
#photoshow {
width:510px;
height: 245px;
border:2px solid #000;
overflow:hidden;

}
#photoshow div {
position:absolute;
z-index:0;
width:510px;
height: 245px;
overflow:hidden;
}
#photoshow div.previous {
z-index: 1;
}
#photoshow div.current {
z-index: 2;
}
</style>
<!-- [if lt IE 7]>
<script>
	document.createElement("slider");
</script>
<![endif] -->
<!-- ####################################################################################################### -->
<div class="wrapper col2"></div>
<!-- ####################################################################################################### -->


<div class="wrapper col6">
<div class="col6" id="headcontent">
	<h1>Welcome to the Writers' Guild of South Africa</h1>
    <p><img class="imgr" src="images/7011.jpg"  alt="" width="228" height="130" /><span style="font-family: 'Caviar Dreams'; font-size: 14px;"><strong>A warm welcome to all South Africa's   performance writers (and writers-to-be) to the new and improved WGSA   website, where you can find out about the WGSA and what we do and most   importantly WHY YOU SHOULD BE PART of it. </strong></span> </p><br />
    <p><span style="font-family: 'Caviar Dreams'; font-size: 14px;"><strong>The WGSA is here to protect, empower and develop performance writers in the local film, television, radio, stage, animation and new media (internet - mobile and digital distribution, and gaming) industries.</strong></span>
      
    </p>
    <p style="font-family: Arial, 'Arial Narrow'; font-size: 14px;">&nbsp;</p>
</div>
</div>


<div class="slider" id="slider" style="float: left;">
    <div class="slides" id="slides">
    <div class="slide" id="slide">
    	<img src="images/writers.png" width="515" height="185"  alt=""/>
         <div id="photoshow">
<div class="current">
         <img src="images/slider.png" width="512" height="245" /></div>
                    <div><img src="images/slide1.png" width="514" height="245" /></div>

                    <div><img src="images/slide2.png" width="512" height="245" /></div>

                    <div><img src="images/slide3.png" width="512" height="245" /></div>

         
        </div>
        
    </div>
     <div class="nav" id="nav">
            <table width="435" border="0" align="right" cellpadding="3" cellspacing="3" class="table1">
          <tr>
            <td width="34%" height="139" align="center" valign="middle"><a href="events.php"><img src="images/index_17.png" width="138" height="134"  alt="Events"/></a></td>
            <td width="34%" align="center" valign="middle"><a href="script_registry.php"><img src="images/index_19.png" width="138" height="134"  alt="Script Register"/></a></td>
            <td width="34%" align="center" valign="middle"><a href="awards.php"><img src="images/index_21.png"  alt="WGSA Muse Awards" width="138" height="134" border="0"/></a></td>
          </tr>
          <tr>
            <td align="center" valign="middle"><a href="becomeamember.php"><img src="images/index_28.png"  alt="Become A Member" width="138" height="133" border="0"/></a></td>
            <td align="center" valign="middle"><a href="competition.php"><img src="images/index_33.png" width="138" height="134"  alt="Comptetitons"/></a></td>
            <td align="center" valign="middle"><a href="find_writer.php"><img src="images/index_27.png" width="138" height="134"  alt="Find A Writer"/></a></td>
          </tr>
          <tr>
            <td height="147" align="center" valign="middle"><a href="find_script.php"><img src="images/index_32.png" width="138" height="140"  alt="The Script Shoppe
"/></a></td>
            <td align="center" valign="middle"><a href="collection_agency.php"><img src="images/index_26.png" width="138" height="140"  alt="Collection Agency"/></a></td>
            <td align="center" valign="middle"><a href="addtodatabase.php"><img src="images/index_34.png"  alt="Add me to the Database" width="138" height="139" border="0"/></a></td>
          </tr>
       </table>     
   </div>

  </div>
</div>

  
<?php include 'footer1.php'; ?>