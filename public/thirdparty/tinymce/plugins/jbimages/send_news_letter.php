<?php include "connect.php"; ?>
<?php 
  if(isset($_POST['submitBtn']))
  {
    $province = $_POST['province'];    
    $description = $_POST['description'];    
    $news_letter_type = $_POST['news_letter_type'];    
    $select_member = "select * from ".MEMBER." where status = '1'";
    $title = 'News Letter: ';

    if($province == "")
    {
        set_message('errorMsg', 'Province is Required.');
        header("location: send_news_letter.php");
        exit;
    }
    else if(trim($description) == ""){
        set_message('errorMsg', 'Description is Required.');
        header("location: send_news_letter.php");
        exit;
    }
    else if($province != "all")
    {   
        $select_member .= " AND province = ".$province; 
    }
    else if($news_letter_type != "all")
    {   
        $select_member .= " AND ".$news_letter_type." = 1";                 
    }    

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


    $select_non_member = "select * from ".SUBCRIBEMAIL." where 1";    

    if($news_letter_type != "all")
    {   
        $select_non_member .= " AND ".$news_letter_type." = 1";                 
    }        
    
    $select_non_member_query = mysql_query($select_non_member);        
    $select_non_member_num_rows = mysql_num_rows($select_non_member_query);
    

    if($select_non_member_num_rows > 0)
    {
      while($select_array = mysql_fetch_array($select_non_member_query))
      {
        if(! in_array($select_array['email'], $emails))
          array_push($emails, $select_array['email']);
      }
    }          

    
    if(count($emails))
    {
      @SendHTMLMail(implode(',', $emails), $title, $msg, 'ajay.makwana87@gmail.com'); 
      set_message('successMsg', 'Send Mail Successfully !');
      header("location: send_news_letter.php");
      exit;
    }      
  }
?>
<!doctype html>
<html>
  <head>
    <?php 
      $site_title = "Send News Letter - " . $SITE_NAME;
      $pagetitle = "Send News Letter"; 
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
                  'url' => 'send_news_letter.php',
                  'text' => 'Send News Letter'
              ),
          );
          ?>
          <div class="clearfix"></div>
          <?php include 'bread-crumb.php'; ?>
		  <?php include 'showMsg.php'; ?>
          <div class="box box-bordered">
            <div class="box-title">
              <h3><i class="icon-th-list"></i>Send News Letter</h3>
            </div>
			
            <div class="box-content padding">
              <form class="form-horizontal form-bordered form-validate" method="POST" action="" id="test">
                
                <div class="control-group">
                  <label class="control-label" for="name">Province</label>
                  <div class="controls">                  
                        <select name="province" id="province"  data-rule-required="true">                            
                            <option value="all">All</option>
                            <?php 
                              $select_province = "select * from ".PROVINCE." where status = '1'";
                              $select_province_query = mysql_query($select_province);
                              $select_province_num_rows = mysql_num_rows($select_province_query);
                              if($select_province_num_rows > 0)
                              {
                                while($select_province_array = mysql_fetch_array($select_province_query))
                                {
                                  $province_id = $select_province_array['id'];
                                  $province_name = $select_province_array['name'];
                                  $province_status = $select_province_array['status'];
                                  
                                  ?>  
                                    <option <?php if($province==$province_id){echo 'selected="selected"';} ?>  value="<?php echo $province_id; ?>"><?php echo $province_name; ?></option>
                                  <?php
                                }
                              }
                            ?>
                        </select>

                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="name">News Letter Type</label>
                  <div class="controls">                  
                        <select name="news_letter_type" id="news_letter_type"  data-rule-required="true">                            
                            <option value="all">All</option>
                            <option value="notification_events">Events</option>
                            <option value="notification_jobs">Jobs</option>
                            <option value="notification_competitions">Competitions</option>
                            <option value="notification_awards">Awards submissions</option>
                        </select>
                  </div>
                </div>

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