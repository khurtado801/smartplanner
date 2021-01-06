<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Smart Planner Invoice</title>
        <link href="<?php echo url('/public/app/build/css/invoice.css'); ?>" type="text/css" rel="stylesheet" />    
    </head>
    <?php //echo '<pre>';print_r($invoicepdfdata); exit;?>
    <body>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
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
            </table>
        </div>
    </body>
</html>
