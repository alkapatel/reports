<?php include "connect.php"; ?>
<?php
is_admin_login(true);
$tablename = static_pages;
$pagetitle = "Add Static Page";
$pagename = "Static";

	


$listfile = "static-pages.php";



if (isset($_POST['submitBtn'])) {

	 $desc = $_POST['desc'];
   $name = $_POST['name'];
   $status = isset($_POST['status']) ? 1:0;
   if(trim($name) == "" || trim($desc) == "")
   {
    set_message('errorMsg', 'Name and Description are Required!');    
   } 
   else
   {
     $query = "INSERT INTO ".$tablename." SET page_name = '".addslashes($name)."', content = '".addslashes($desc)."', status = '".$status."'";

     mysql_query($query);

     set_message('successMsg', 'Record Added successfully!');
     header("location: ".$listfile);exit;    
   } 	 
}    



?>

<!doctype html>
<html>
  <head>
    <?php $site_title = "Add Page - " . $SITE_NAME ?>
    <?php include "common-header.php"; ?>
  </head>
<script src="tinymce/tinymce.min.js"></script>
    <script>
tinymce.init({
    selector: "textarea",
    theme: "modern",
    plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
      "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
      "save table contextmenu directionality emoticons template paste textcolor", "jbimages"
      ],
      content_css: "css/content.css",
      toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 
      style_formats: [
     {title: 'Bold text', inline: 'b'},
     {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
     {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
     {title: 'Example 1', inline: 'span', classes: 'example1'},
     {title: 'Example 2', inline: 'span', classes: 'example2'},
     {title: 'Table styles'},
     {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
    ]
   });    
    
      </script>

  <body>
    <?php include "navigation.php"; ?>
	
    <div class="container" id="content">
      <div id="main" class="no-left-margin">
        <div class="container">
          <div class="page-header">
            <div class="pull-left">
              <h1><?php echo $pagetitle; ?></h1>
            </div>
          </div>

          <?php
          $breadCrumbArray = array(
              array(
                  'url' => 'edit_profile.php',
                  'text' => $pagetitle
              ),
          );
          ?>
          <div class="clearfix"></div>
          <?php include 'bread-crumb.php'; ?>
		  <?php include 'showMsg.php'; ?>
          <div class="box box-bordered">
            <div class="box-title">
              <h3><i class="icon-th-list"></i><?php echo $pagetitle; ?></h3>
            </div>
			
            <div class="box-content padding">
              <form class="form-horizontal form-bordered form-validate" method="POST" action="" id="test">

                <div class="control-group">
                  <label class="control-label" for="name">Name</label>
                  <div class="controls">
                    <input type="text" id="name" name="name" value="<?php echo $_REQUEST['name']; ?>"/>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label" for="name">Description</label>
                  <div class="controls">
                    <textarea id="textarea" name="desc" class="input-xxlarge" ><?php echo $_REQUEST['desc']; ?></textarea>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="name">Status</label>
                  <div class="controls">
                    <input type="checkbox" name="status" <?php echo (isset($_REQUEST['status'])) ? 'checked="checked"':''; ?>/>
                  </div>
                </div>

                <div class="form-actions">
                  <button class="btn btn-primary" name="submitBtn" type="submit">Save changes</button>
				          <a class="btn btn-inverse" href="<?php echo $listfile; ?>" type="button">Cancel</a>
                </div>
              </form>
            </div>												
          </div>
        </div>
      </div>
    </div>
  </body>
</html>