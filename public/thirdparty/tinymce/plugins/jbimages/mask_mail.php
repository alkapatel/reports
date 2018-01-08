<?php include "connect.php"; ?>

<?php 
  

  if(isset($_POST['submitBtn']))
  {    
    $description = $_POST['description'];    
    $members = $_POST['members'];            
    $select_member = "select * from ".MEMBER." where status = 1 AND id in (".implode(',', $members).")";
    
    $select_member_query = mysql_query($select_member);    
    $select_member_num_rows = mysql_num_rows($select_member_query);


    $emails = array();

    if($select_member_num_rows > 0)
    {      
      while($select_member_array = mysql_fetch_array($select_member_query))
      {
        array_push($emails, $select_member_array['email']);
      }
    }          


    $msg = $description;

    $title = 'Mass Mail';    

    if(count($emails))
    {            
      @SendHTMLMail(implode(',', $emails), $title, $msg, 'ajay.makwana87@gmail.com');       
      set_message('successMsg', 'Send Mail Successfully !');
      header("location: mask_mail.php");       
    } 
    else{
      set_message('errorMsg', 'Select Minimum 1 Member !');
      header("location: mask_mail.php");      
    }    
  }
?>
<!doctype html>
<html>
  <head>
    <?php 
      $site_title = "Mass Mail - " . $SITE_NAME;
      $pagetitle = "Mass Mail"; 
    ?>
    <?php include "common-header.php"; ?>
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
    
  </head>

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
                  'url' => 'mask_mail.php',
                  'text' => 'Mass Mail'
              ),
          );
          ?>
          <div class="clearfix"></div>
          <?php include 'bread-crumb.php'; ?>
		      <?php include 'showMsg.php'; ?>
          <div class="box box-bordered">
            <div class="box-title">
              <h3><i class="icon-th-list"></i>Mass Mail</h3>
            </div>
			
            <div class="box-content padding">
             <form class="form-horizontal form-bordered form-validate" method="POST" action="" id="test">                
               <table width="100%" cellpadding="5">
                      <tr>
                        <?php 
                          $select_member = "select * from ".MEMBER." where status = 1"; 
                          $select_member_query = mysql_query($select_member);                              
                          $select_member_num_rows = mysql_num_rows($select_member_query);
                          if($select_member_num_rows > 0)
                          {
                            $count = 1;                                                        
                            while($select_member_array = mysql_fetch_array($select_member_query))
                            {
                              if($count == 1)
                                echo "<tr>";

                              $name = $select_member_array['name']." ".$select_member_array['surname'];

                              ?>

                              <td><input type="checkbox" name="members[]" value="<?php echo $select_member_array['id']; ?>"/>&nbsp;<?php echo $name; ?></td>

                              <?php
                              $count++;                                                              
                              if($count == 5)
                              {  
                                echo "</tr>";
                                $count =1;
                              }  
                            }
                          }          
                        ?>                                                            
                      </tr>
                </table>       

                <br />

                <div class="control-group">
                  <label class="control-label" for="name">Description</label>
                  <div class="controls">
                      <textarea name="description" id="description" placeholder="Description" data-rule-required="true"></textarea>
                  </div>
                </div>


                <div class="form-actions">
                  <button class="btn btn-primary" name="submitBtn" type="submit">Send Mail</button>
                </div>

              </form>
            </div>			
          </div>

        </div>
      </div>
    </div>
  </body>
</html>