<!doctype html>
 
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice Details</title>
<link href="https://fonts.googleapis.com/css?family=Poppins:400,400i,500,600,700" rel="stylesheet">  
 
<style type="text/css">

  body{
  -webkit-text-size-adjust: none!important;
  font-family: 'Poppins', sans-serif;
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  letter-spacing: 1px;
  margin: 2%;
}
table { background-color: transparent;  border-spacing: 0; width: 100%;}
th { color:#222; border: 1px solid #ddd; padding: 0.5em;   background:#eee; text-align:left; }
td { border: 1px solid #ddd; padding: 0.5em; }
.b-0{ border: 0;} 
.bt-0{ border-top: 0;}
.bb-0{ border-bottom: 0;}
.bl-0{ border-left: 0;}
.br-0{ border-right: 0;}
.p-0{padding: 0; }  



@media screen and (max-width:600px) {

}

@media screen and (max-width:480px) {
}
</style>
</head>
<body>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td class="p-0">
          <table cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td width="20%" align="center"><a href="#"><img src="http://reports.phpdots.com/images/pd-logo.png"/></a></td>
              <td width="80%" align="center"><h1>PHPDots Technologies</h1></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><i>{{$invoices->address}}</i></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="p-0">
          <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center"><h2><b>TAX INVOICE</b></h2></td>
                    </tr>
                    <tr>
                      <td class="p-0 b-0">
                        <table cellpadding="0" cellspacing="0" border="0">
                          <tr>
                            <td width="70%"><b>To :<br/><br/></b><?php echo nl2br($invoices->to_address);?></td>
                            <td width="30%" class="p-0">
                              <table>
                                <tr>
                                  <td colspan="2" align="center">Reference Details:</td>
                                </tr>
                                <tr>
                                  <td align="center">Invoice No</td>
                                  <td align="center">{{$invoices->invoice_no}}</td>
                                </tr>
                                <tr>
                                  <td align="center">Invoice Date</td>
                                  <td align="center">{{$invoices->invoice_date}}</td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td align="center">State code</td>
                                  <td align="center">24</td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr><td colspan="2">&nbsp;</td></tr>
                        </table>
                      </td>
                    </tr>
                  </table>
        </td>
      </tr>
      <tr>
        <td class="p-0">
          <table cellpadding="0" cellspacing="0" border="0">
            <tr>
              <th width="10%"><center>Sr. No</center></th>
              <th width="15%"><center>SAC CODE</center></th>
              <th width="50%"><center>Particulars</center></th>
              <th width="25%"><center>Amount
              <?php $currency = $invoices->currency;
                if(!empty($currency) && $currency == 'in_rs') echo "(In Rs.)";?>
              <?php  if(!empty($currency) && $currency == 'in_usd') echo "(In USD)";?>
                </center></th>
            </tr>
          <?php  
          $i=1;
          foreach($invoice_details as $detail) {?>
            <tr>
              <td><?php echo $i;?></td>
              <td>9983</td>
              <td>{{$detail->particular}}</td>
              <td align="right">{{$detail->amount}}</td>
            </tr>
          <?php $i++; }
          if($invoices->require_gst == 1){
          ?>
            <tr>
              <td></td>
              <td><b>CGST</b></td>
              <td>9.00%</td>
              <td align="right">{{$invoices->cgst_amount}}</td>
            </tr>
            <tr>
              <td></td>
              <td><b>SGST</b></td>
              <td>9.00%</td>
              <td align="right">{{$invoices->sgst_amount}}</td>
            </tr>
          <?php } ?>
            <tr>
              <td>&nbsp;</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td><b>Total</b></td>
              <td align="right"><b>{{$invoices->total_amount}}</b></td>
            </tr>
            <tr>
              <td colspan="2">Total invoice value (in words)</td>
              <td colspan="2"><b><?php  echo ucwords($invoices->total_amount_words); ?></b></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="p-0">
          <table cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>Details:</b></td>
            </tr>
            <tr>
              <td colspan="2">Cheque/DD should be in favour of PHPDOTS TECHNOLOGIES </td>
            </tr>
            <tr>
              <td><b>Payable at Ahmedabad</b></td>
              <td>PAN : {{$invoices->pan_no}}</td>
            </tr>
            <tr>
              <td>Bank Name : {{$invoices->bank_name}}</td>
              <td>GST Regn. No. :  {{$invoices->gst_regn_no}}</td>
            </tr>
            <tr>
              <td>Bank Account Number : {{$invoices->bank_account_no}}</td>
              <td>Bank SWIFT CODE : {{$invoices->bank_swift_code}}</td>
            </tr>
            <tr>
              <td>Bank IFSC CODE : <b>{{$invoices->ifsc_code}}</b></td>
              <td>Subject to Ahmedabad Juridiction</td>
            </tr>
            <tr>
              <td colspan="2">Indian FTP Service Category : Service Category</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>Thank you !</b></td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>PHPDots Technologies</b></td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>Authorised Signatory</b></td>
            </tr>
          </table>
        </td>
      </tr>
  </table>
</body>
</html>
