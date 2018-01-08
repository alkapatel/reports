<?php include "connect.php"; ?>
<?php
is_admin_login(true);
$menunum = 1;
$leftmenu = 1;

$tablename = NEWS;
$pagetitle = "News";
$pagename = "News";

$addfile = "add_news.php";
$listfile = "manage-news.php";

if (isset($_REQUEST['id']))
  $id = $_REQUEST['id'];

if (isset($_REQUEST['start']) && $_REQUEST['start'] != "") {
  $start = $_REQUEST['start'];
  $query_string .= "start=" . $start;
}

if (isset($_REQUEST['search']) && $_REQUEST['search'] != "") {
  $search = $_REQUEST['search'];
  $query_string .= "search=" . $search . "&";
}
if (isset($_REQUEST["ord"]) && $_REQUEST["ord"] != "") {
  $ord = $_REQUEST["ord"];
  $query_string .= "ord=" . $ord . "&";
}
if (isset($_REQUEST["perpage"]) && $_REQUEST['perpage'] != "") {
  $perpage = $_REQUEST["perpage"];
  $query_string .= "perpage=" . $perpage . "&";
}

$mode = "add";
if (isset($_REQUEST['mode']))
  $mode = $_REQUEST['mode'];

if (isset($_POST['submitBtn'])) {

/*echo "<pre>";
  print_r ($_POST);
  print_r ($_FILES);
echo "</pre>";
exit;*/

  $id = $_POST['id'];

  $title = addslashes($_POST['title']);
  $description = addslashes($_POST['description']);
  $category = $_POST['category'];
  
  $status = '0';
  if (isset($_POST['status'])) {
    $status = $_POST['status'];
  }


  switch ($mode) {
    case "add" :
      if (is_dup_add($tablename, "title", $title)) {
        set_message('errorMsg', 'Duplicate Title');
        header("location: " . $listfile . "?" . $query_string . $msg);
        exit;
      }
      $qry = "insert into $tablename ";
      $where = "";
      set_message('successMsg', 'News has been added!');
      break;
    case "edit" :
      if (is_dup_edit($tablename, "title", $title, "id", $id)) {
        set_message('errorMsg', 'Duplicate Title');
        header("location: " . $listfile . "?" . $query_string . $msg);
        exit; 
      }
      $qry = "update $tablename ";
      $where = " where id = ".$id;
      set_message('successMsg', 'News has been updated!');
      break;
  }

  $qry .= "set
        category_id = " . $category . ",
        title = '" . $title . "',
        description = '" . $description . "'
  ";  
  $qry .= $where;

  $err = mysql_query($qry);

  if (!$err)
    echo mysql_error();

  if ($mode == "add"){
    $id = mysql_insert_id();
  }

  if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '' && $_FILES['image']['error'] == '0'){

    $filename = md5(time()) . '-' . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], '../upload/news/' . $filename);

    $qry = "update $tablename set image='".$filename."' where id=" . $id;
    mysql_query($qry);
  }

 
  header("location: " . $listfile . '?' . $query_string);
  exit;
}

if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
  $where = " where id=" . $id;
  $sel = "select * from ".$tablename." ".$where;

  $result = mysql_query($sel);

  $val = mysql_fetch_object($result);
  $id = $val->id;

  $title = stripslashes($val->title);
  $description = stripslashes($val->description);
  $image = stripslashes($val->image);
  $category = $val->category_id;  
}

// echo $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR."plugin";
?>
<!doctype html>
<html>
  <head>
<?php $site_title = ucfirst($mode) . " News - " . $SITE_NAME ?>
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
              <h1>News</h1>
            </div>
            <div class="pull-right">
              <a href="manage-events.php" class="btn btn-inverse btn-large margin-top-12">Back</a>
            </div>
          </div>

<?php
$breadCrumbArray = array(
    array(
        'url' => $listfile,
        'text' => $pagetitle
    ),
    array(
        'url' => '',
        'text' => ucfirst($mode) . ' '.$pagetitle
    )
);
?>
          <div class="clearfix"></div>
          <?php include 'bread-crumb.php'; ?>

          <div class="box box-bordered">
            <div class="box-title">
              <h3><i class="icon-th-list"></i> <?php echo ucfirst($mode).' '.$pagetitle; ?></h3>
            </div>
            <div class="box-content padding">
              <form class="form-horizontal form-bordered form-validate" method="POST" action="" id="test" enctype="multipart/form-data">
                <input type="hidden" name="start" value="<?php echo $start; ?>" />  
                <input type="hidden" name="perpage" value="<?php echo $perpage ?>" /> 
                <input type="hidden" name="ord" value="<?php echo $ord; ?>" />  
                <input type="hidden" name="search" value="<?php echo $search; ?>" />  
                <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
                <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />

                <div class="control-group">
                  <label class="control-label" for="name">Category</label>
                  <div class="controls">
                    <select name="category" id="category" data-rule-required="true">
                      <option value="">select category</option>
                      <?php
                        $sel_news_category = "SELECT * FROM ".NEWS_CATEGORY;
                        $sel_news_category_query = mysql_query($sel_news_category);
                        $sel_news_category_num_rows = mysql_num_rows($sel_news_category_query);
                        if($sel_news_category_num_rows > 0)
                        {
                          while($sel_news_category_arr = mysql_fetch_array($sel_news_category_query))
                          {
                            $category_name = $sel_news_category_arr['title'];
                            $category_id = $sel_news_category_arr['id'];
                            ?>
                              <option value="<?php echo $category_id; ?>" <?php echo ($category == $category_id) ? 'selected="selected"':''; ?>><?php echo $category_name; ?></option>
                            <?php
                          }
                        }
                      ?>                      
                    </select>
                  </div>
                </div>
                

                <div class="control-group">
                  <label class="control-label" for="name">Title</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $title; ?>" class="input-xlarge" placeholder="Title" data-rule-required="true" id="title" name="title">
                  </div>
                </div>


                <div class="control-group">
                  <label class="control-label" for="name">Description</label>
                  <div class="controls">
                      <textarea name="description" id="description" placeholder="Description" data-rule-required="true"><?php echo $description; ?></textarea>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="name">Image</label>
                  <div class="controls">
                      <?php if ($image != ''): ?>
                        <img src="../image.php?<?php echo $image; ?>?width=70&height=70&cropratio=1:1&image=<?php echo $SITE_URL; ?>/upload/news/<?php echo $image ?>" />
                      <?php endif; ?>
                      <input name="image" type="file" class="input-xlarge" placeholder="News Image">
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