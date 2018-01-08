<?php include "connect.php"; ?>
<?php
is_admin_login(true);

$menunum = 1;
$leftmenu = 1;

$tablename = EVENTS;
$pagetitle = "Event";
$pagename = "Events";

$addfile = "add_event.php";
$listfile = "manage-events.php";

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
  $id = $_POST['id'];

  $title = addslashes($_POST['title']);
  $description = addslashes($_POST['description']);
  $event_date = addslashes($_POST['event_date']);

  $amt_for_member = addslashes($_POST['amt_for_member']);
  $amt_for_non_member = addslashes($_POST['amt_for_non_member']);
  $venue = addslashes($_POST['venue']);	
  $speaker= addslashes($_POST['speaker']); 
  $centre= addslashes($_POST['centre']); 
  $session= addslashes($_POST['session']); 
  
  $status = '0';
  if (isset($_POST['status'])) {
    $status = $_POST['status'];
  }

    if($_FILES['image']['name']!="") 
    {

        if ( ($_FILES["image"]["type"] == "image/gif")      
            ||   ($_FILES["image"]["type"] == "image/jpeg")
            ||   ($_FILES["image"]["type"] == "image/jpg")
            ||   ($_FILES["image"]["type"] == "image/png")
            )
        {
                  $filename = md5(time()) . '-' . $_FILES['image']['name'];
                  move_uploaded_file($_FILES['image']['tmp_name'], '../upload/' . $filename);         
        }
     
    }


  switch ($mode) {
    case "add" :
      if (is_dup_add($tablename, "title", $name)) {
        set_message('errorMsg', 'Duplicate Name');
        header("location: " . $listfile . "?" . $query_string . $msg);
        exit;
      }
      $qry = "insert into $tablename ";
      $where = "";
      set_message('successMsg', 'Event has been added!');
      break;
    case "edit" :
      if (is_dup_edit($tablename, "title", $title, "id", $id)) {
        set_message('errorMsg', 'Duplicate Title');
        header("location: " . $listfile . "?" . $query_string . $msg);
        exit; 
      }
      $qry = "update $tablename ";
      $where = " where id = ".$id;
      set_message('successMsg', 'Event has been updated!');
      break;
  }

  $qry .= "set  
    title = '" . $title . "',
		description = '" . $description . "',
		event_date = '" . $event_date. "',
		amt_for_member = '" . $amt_for_member. "',
		amt_for_non_member = '" . $amt_for_non_member. "',
        status = '" . $status . "',
		venue = '" . $venue . "',
    speaker = '" . $speaker . "',
    centre = '" . $centre . "',
    session = '" . $session . "'
          ";  

   if(isset($filename)) 
   {
      $qry .= ", image = '".$filename."'";
   }      

  $qry .= $where;

  

  $err = mysql_query($qry);


  if (!$err)
    echo mysql_error();

 
  header("location: " . $listfile . '?' . $query_string);
  exit;
}

if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
  $where = " where id=" . $id;
  $sel = "select * from ".$tablename." " . $where;
  $result = mysql_query($sel);

  $val = mysql_fetch_object($result);
  $id = $val->id;

  $title = stripslashes($val->title);
  $description = stripslashes($val->description);
  $event_date = stripslashes($val->event_date);
  $amt_for_member = stripslashes($val->amt_for_member);
  $amt_for_non_member = stripslashes($val->amt_for_non_member);
  $status = stripslashes($val->status);
  $venue = stripslashes($val->venue);
  $centre = stripslashes($val->centre);
  $speaker = stripslashes($val->speaker);
  $image = stripslashes($val->image);
   $session = stripslashes($val->session);
}
?>
<!doctype html>
<html>
  <head>
<?php $site_title = ucfirst($mode) . " Event - " . $SITE_NAME ?>
    <?php include "common-header.php"; ?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $(".datepicker_new").datepicker({ 
            dateFormat: 'yy-mm-d',
            changeYear: true,
            changeMonth: true,
            yearRange: '1900:' + new Date().getFullYear(),
            showButtonPanel: false
        });      
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
              <h1>Event</h1>
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
                  <label class="control-label" for="name">Title</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $title; ?>" class="input-xlarge" placeholder="Title" data-rule-required="true" id="title" name="title">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="name">Session</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $session; ?>" class="input-xlarge" placeholder="Session" data-rule-required="true" id="session" name="session">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="name">Description</label>
                  <div class="controls">
                    	<textarea name="description" id="description" placeholder="Description" data-rule-required="true"><?php echo $description; ?></textarea>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="url">Venue</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $venue; ?>" class="input-xlarge" placeholder="Venue" data-rule-required="true" id="venue" name="venue">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="url">Centre</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $centre; ?>" class="input-xlarge" placeholder="Centre" data-rule-required="true" id="centre" name="centre">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="url">Speaker</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $speaker; ?>" class="input-xlarge" placeholder="Speaker" data-rule-required="true" id="speaker" name="speaker">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="url">Event Date</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $event_date; ?>" class="input-xlarge datepicker_new" placeholder="Event Date" data-rule-required="true" id="event_date" name="event_date">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="url">Amount for Member</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $amt_for_member; ?>" class="input-xlarge" placeholder="Amount for Member" data-rule-required="true" data-rule-digits="true" id="amt_for_member" name="amt_for_member">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="url">Amount for non Member</label>
                  <div class="controls">
                    <input type="text" value="<?php echo $amt_for_non_member; ?>" class="input-xlarge" placeholder="Amount for non Member" data-rule-required="true" data-rule-digits="true" id="amt_for_non_member" name="amt_for_non_member">
                  </div>
                </div>



                <div class="control-group">
                  <label class="control-label" for="url">Image</label>
                  <div class="controls">
                    <input type="file" name="image">                    
                    
                    <?php if($mode =="edit"):?>
                    <br />
                    <img src="../upload/<?php echo $image; ?>" height="100" width="100"/>
                  <?php endif;?>
                  </div>
                </div>


                <div class="control-group">
                  <label class="control-label" for="url">Status</label>
                  <div class="controls">
                    <input type="checkbox" class="input-xlarge" value='1' placeholder="status" id="status" name="status" <?php echo isset($status) && $status == '1' ? 'checked="checked"' : '' ?>>
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