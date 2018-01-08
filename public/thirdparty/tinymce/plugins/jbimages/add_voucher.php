<?php include "connect.php"; ?>
<?php
is_admin_login(true);

$menunum = 1;
$leftmenu = 1;

$tablename = VOUCHERS;
$pagetitle = "Voucher";
$pagename = "Voucher";

$addfile = "add_voucher.php";
$listfile = "manage_vouchers.php";

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
  $price = addslashes($_POST['price']);
  
  
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
      set_message('successMsg', 'Voucher has been added!');
      break;
    case "edit" :
      if (is_dup_edit($tablename, "title", $title, "id", $id)) {
        set_message('errorMsg', 'Duplicate Title');
        header("location: " . $listfile . "?" . $query_string . $msg);
        exit; 
      }
      $qry = "update $tablename ";
      $where = " where id = ".$id;
      set_message('successMsg', 'Voucher has been updated!');
      break;
  }

  $expire_at = addslashes($_POST['expire_at']);

  for($i=0;$i<$_POST['quantity'];$i++)    
  {
    $qry = "insert into $tablename ";

    $key = generate_password(8);  
    
    $qry .= "set        
          title = '" . $title . "',
          price = " . $price . ",
          voucher_key = '" . $key . "',
          expire_at = '" . $expire_at . "',
          created_at = NOW()
    ";  



    $err = mysql_query($qry);

  }
  

  header("location: " . $listfile);
  exit;
}

?>
<!doctype html>
<html>
  <head>
<?php $site_title = ucfirst($mode) . " Voucher - " . $SITE_NAME ?>
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
              <h1>Voucher</h1>
            </div>
            <div class="pull-right">
              <a href="manage_vouchers.php" class="btn btn-inverse btn-large margin-top-12">Back</a>
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
                  <label class="control-label" for="name">Purpose</label>
                  <div class="controls">
                    <input type="text"  class="input-xlarge" placeholder="Purpose" data-rule-required="true" id="title" name="title">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="name">Rate</label>
                  <div class="controls">
                    <select name="price" id="price" data-rule-required="true">
                      <option value="">Select Rate</option>
                      <option value="40"><?php echo CURRENCYSYMBOL;?> 40</option>
                      <option value="55"><?php echo CURRENCYSYMBOL;?> 55</option>
                      <option value="100"><?php echo CURRENCYSYMBOL;?> 100</option>
                    </select>
                  </div>
                </div>                

                <div class="control-group">
                  <label class="control-label" for="name">Voucher expiry date</label>
                  <div class="controls">
                    <input type="text" name="expire_at" id="expire_at" data-rule-required="true" class="input-xlarge datepicker_new" placeholder="Voucher expiry date" />                                          
                  </div>
                </div>                

                <div class="control-group">
                  <label class="control-label" for="name">Quantity</label>
                  <div class="controls">
                    <input type="text" name="quantity" id="quantity" data-rule-required="true" data-rule-digits="true" class="input-xlarge" placeholder="Quantity"/>                                          
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