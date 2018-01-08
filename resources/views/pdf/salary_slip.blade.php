<!DOCTYPE html
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>SALARY SLIP</title>
<!-- Bootstrap -->
<!-- <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=;:300italic,400italic|Poppins:400,300,500,600,700"> -->
<!-- <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet"> -->
<style>
table, tr, td {
    color:#000;
    font-size:15px;
    /*font-family: 'Poppins', Arial, Helvetica, sans-serif;*/
font-family:Arial, Helvetica, sans-serif;
    border:none;
    line-height:1.1;
    padding:0;
    margin:0;
}
td {
    padding:2px
}
strong {
    font-weight:600;
}
</style>
</head>
<body style="padding:10px; margin:5px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="color:#000; font-size:14px;"><tr><td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="color:#000; font-size:15px;">
  <tr>
    <td align="center" valign="top" height="40"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top:1px solid #aaa; border-left:1px solid #aaa;">
      <tr>
        <td width="20%" align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Company Logo</td>
        <td width="80%" align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><img src="{{ asset("images/pd-logo.png")}}" alt="logo" class="logo-default" style="max-width: 100px;margin-top: 15px !important; max-height: 250px"></td>
      </tr>
      <tr>
        <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Company Name</td>
        <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><h3 style="padding:0; margin:0;">PHPDots Technologies</h3></td>
      </tr>
      <tr>
        <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Address</td>
        <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">C-103, Pragtya Residency, Nr. Sarthi Bunglows, Bopal - Ahmedabad</td>
      </tr>
      <tr>
        <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Phone</td>
        <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> 9825096687</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="middle"><h2 style="padding:0; margin:5px 0 0;">Pay Slip For the Month of <?php 
              if($slip->month == 1){echo "January";}
              else if($slip->month == 2){echo "February";}
              else if($slip->month == 3){echo "March";}
              else if($slip->month == 4){echo "April";}
              else if($slip->month == 5){echo "May";}
              else if($slip->month == 6){echo "June";}
              else if($slip->month == 7){echo "July";}
              else if($slip->month == 8){echo "August";}
              else if($slip->month == 9){echo "September";}
              else if($slip->month == 10){echo "October";}
              else if($slip->month == 11){echo "November";}
              else if($slip->month == 12){echo "December";}
              ?>, {{$slip->year}}</h2></td>
  </tr>
  <tr>
    <td height="20" align="left" valign="top"></td>
  </tr>
  <tr>
    <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" align="center" valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top:1px solid #aaa; border-left:1px solid #aaa;">
          <tr>
            <td width="37%" align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Employee Name</td>
            <td width="63%" align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><strong>{{$slip->user_name}}</strong></td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Date Of Joining</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->joining_date}}</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Department</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Software Development</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Designation</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->designation}}</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">PAN No.</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->pan_num}}</td>
          </tr>
        </table>        </td>
        <td width="50%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="1" style="border-top:1px solid #aaa; border-left:1px solid #aaa;">
          <tr>
            <td width="37%" align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Bank A/C No.</td>
            <td width="63%" align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->account_num}}</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Bank Name</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->bank_name}}</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Working Days</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->working_days}}</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Leave Taken</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->leave_taken}}</td>
          </tr>
          <tr>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
            <td align="left" valign="top" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top">
    <table width="100%" border="0" cellspacing="0" style="border-top:1px solid #aaa; border-left:1px solid #aaa;">
      <tr>
        <th width="25%" height="32" align="left" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Particular</th>
        <th width="25%" align="center" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Amount</th>
        <th width="25%" align="left" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Deduction</th>
        <th width="25%" align="center" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Amount</th>
        </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Basic Salary</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->basic_salary}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Advance</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->advance}}</td>
        </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">HRA</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->hra}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Leave Deduction</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->leave_deduction}}</td>
        </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Conveyance Allowance</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->conveyance_allowance}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Other Deduction</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->other_deduction}}</td>
        </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Telephone Allowance</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->telephone_allowance}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">TDS</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->tds}}</td>
        </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Medical Allowance</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->medical_allowance}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Uniform Allowance</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->uniform_allowance}}</td>


        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Special Allowance</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->special_allowance}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Bonus / Incentive</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->bonus}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Arrear Salary</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->arrear_salary}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Advance Given</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->advance_given}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Leave Encashment</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->leave_encashment}}</td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><strong>Total Earnings</strong></td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><em><strong>{{$slip->total_earning}}</strong></em></td>
        <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><em><strong>Total Deductions</strong></em></td>
        <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$slip->total_deduction}}</td>
      </tr>
    </table>    </td>
  </tr>
  <tr>
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="4" cellpadding="0">
      <tr>
        <td width="20%" align="left" valign="middle">Net Pay</td>
        <td width="80%" align="left" valign="middle"><strong><b>{{$slip->net_pay}}</b></strong></td>
      </tr>
      <tr>
        <td align="left" valign="middle">Net Pay In Words</td>
        <td align="left" valign="middle"> <?php 
                                          $net_pay_words = $slip->net_pay_words; 
                                          $only = strpos($net_pay_words,"only");
                                          if($only == ''){
                                            $net_pay_words = $net_pay_words.' only';
                                            echo ucwords($net_pay_words); }
                                          else{
                                            echo ucwords($net_pay_words);}
                                           ?></td>
      </tr>

    </table></td>
  </tr>
  
  <tr>
    <td align="left" valign="top"></td>
  </tr>
  <tr>
    <td align="left" valign="top"> </td>
  </tr>
  <tr>
    <td align="left" valign="top" style="line-height:1;"><em style="line-height:1; font-size:13px; color:#666666">This is system-generated document and doesn't need signature.</em></td>
  </tr>
</table>  
<em style="line-height:1"></em></td>
  </tr>
</table>
</body>
</html>