<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Smart Planner Invoice</title>
<link href="<?php echo url('/public/app/build/css/invoice.css'); ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo url('/public/app/build/css/bootstrap.css'); ?>" type="text/css" rel="stylesheet" />
</head>
<?php //echo '<pre>';print_r($invoicepdfdata); exit;?>
<body>
<div class="container">
  <div class="invoice-box"> 
    <!--<table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title"> <h2> SMART PLANNER </h2> </td>
                                <td>
                                    <p> Name: <?php echo $invoicepdfdata['firstname'].' '.$invoicepdfdata['lastname']; ?> </p> <br>
                                    <p> Created: <?php echo $invoicepdfdata['subscribe_date']; ?> </p> <br>
                                    <p> Due: <?php echo $invoicepdfdata['expire_date']; ?> </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td> <p>Plan </p> </td>
                    <td> <p>Price </p> </td>
                </tr>

                <tr class="item last">
                    <td> <p> <?php echo $invoicepdfdata['name']; ?> (<?php echo $invoicepdfdata['duration']; ?> Months) </p> </td>
                    <td> <p>$ <?php echo $invoicepdfdata['price']; ?> </p> </td>
                </tr>

                <tr class="total">
                    <td></td>
                    <td> <p> Total: <strong>$ </strong><?php echo $invoicepdfdata['price']; ?> </p> </td>
                </tr>
            </table>-->
    <div class="row">
      <div class="invoice_heading">
        <div class="col-xs-6 no-padding-left">
          <div class="logo"> <img class="img-responsive" title="Evolved Educator" alt="Company Logo" src="<?php echo url('public/app/build/images/logo.png');?>"> </div>
        </div>
        <div class="col-xs-6 no-padding-right">
          <ul class="invoice_client_info">
            <li> <span>Name : </span><?php echo $invoicepdfdata['firstname'].' '.$invoicepdfdata['lastname']; ?></li>
            <li> <span>Created : </span><?php echo $invoicepdfdata['subscribe_date']; ?></li>
            <li> <span>Renew : </span><?php echo $invoicepdfdata['expire_date']; ?></li>
          </ul>
        </div>
      </div>
      <div class="invoice_body">
        <div class="invoice_title">
          <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-9 no-padding-left">
              <h3>Plan</h3>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-3 no-padding-right">
              <h3>Price</h3>
            </div>
          </div>
        </div>
        <div class="invoice_description">
          <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-9 no-padding-left">
              <h4> <?php echo $invoicepdfdata['name']; ?> (<?php echo $invoicepdfdata['duration']; ?> Months) </h4>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-3 no-padding-right">
              <h4>$ <?php echo $invoicepdfdata['price']; ?> </h4>
            </div>
          </div>
        </div>
        <div class="invoice_total">
          <div class="col-xs-offset-5 col-xs-7 col-sm-offset-7 col-sm-4 col-md-offset-8 col-md-4">
            <h3><span>Total : </span>$ <?php echo $invoicepdfdata['price']; ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
