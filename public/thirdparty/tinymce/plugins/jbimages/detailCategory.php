<?php require_once('connect.php'); ?>
<?php require_once('header.php'); ?>
<?php $tablename = static_pages; ?>

<style>
	.news-link{
		color: blue;		
		text-decoration: underline;
	}	
</style>

<!-- ####################################################################################################### -->
<div class="wrapper col2"></div>
<!-- ####################################################################################################### -->
<div class="wrapper col3">
  <div class="container">  	
  	<h1>Category Title:  <?php echo get_single_value(NEWS_CATEGORY,'title', 'id = '.$_REQUEST['id'])?></h1>
  	<table style="font-size: 18px;" cellpadding="5">
  	
     <?php
     	$tablename = NEWS;
	  	$sel = "select * from " . $tablename . " where category_id='".$_REQUEST['id']."' ";
	  	$result = mysql_query($sel);
	  	$count = 1 ;
                
	  	while ($val = mysql_fetch_object($result)):
	 ?> 		
	  	<tr>
  			<th><?php echo $count; ?>.</th>
  			<td><a href="detailNews.php?id=<?php echo $val->id;?>" class="news-link"><?php echo $val->title; ?></a></td>
  		</tr>
  	 <?php $count++;?>	
	 <?php endwhile; ?>       
	 
	 <?php if($count == 1):?>
			<tr>
				<td colspan="2">No News Available.</td>
			</tr>	 	
	 <?php endif;?>	

	 <?php //require_once('right_sidebar.php'); ?>    		

	</table> 
     <br class="clear" />
  </div>
</div>

<?php require_once('footer1.php'); ?>