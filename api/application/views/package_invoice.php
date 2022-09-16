<meta charset="utf-8">
<style type="text/css">
body {
	font-family: Arial
}
.pdf_main {
	background: #fff;
	margin-left: 25px;
	margin-right: 25px;
}
.clearfix {
	clear: both;
}
ul, li {
	list-style: none;
	margin: 0px;
	padding: 0px;
}
.head-main {
	float: left;
	width: 100%;
	margin-bottom: 30px;
}
.pdf_main .head-left {
	float: left;
	width: 50%;
}
.pdf_main .head-right {
	float: right;
	width: 50%;
}
.pdf_main .col-li {
	float: left;
	display: inline-block;
	text-align: center;
	padding: 0 10px;
	font-size: 12px;
	font-weight: 700;
	width:170px;
}
.pdf_main .col-li span {
	font-weight: 400;
}
.pdf_main .head-main h3 {
	text-align: right;
	margin-bottom: 20px;
	float: right;
}
.pdf_main .head-right li.last, .pdf_main .head-right li:last-child {
	padding-right: 0px;
}
.pdf_main .footer {
	background-color: #0076c0;
	float: left;
	text-align: center;
	display: block;
	padding: 12px 0 0px;
	box-sizing: border-box;
	margin-top: 30px;
	width: 650px;
}
.foot-li {
	color: #fff;
	font-size: 12px;
	font-weight: bold;
}
.foot-li.last {
	border-right: none;
}
.pdf_main table {
	border: 2px #bebcbc solid;
	border-collapse: collapse;
}
.pdf_main table tbody td {
	border: none !important;
}
.pdf_main table th {
	border: none !important;
}
.pdf_main .pdf_table {
	margin-bottom: 50px;
	margin-bottom: 30pt;
}
.pdf_main .pdf_table p {
	color: #000000;
	font-size: 11px;
	font-weight: 400;
	margin-bottom: 10px;
}
.pdf_main .pdf_table table td[colspan="3"] {
	padding-top: 24px;
}
.pdf_main .pdf_table thead th, .pdf_main .pdf_table tfoot td.grand-total,.div-thead {
	color: #ffffff;
	font-size: 14px;
	background-color: #232B40;
}
.div-thead-black{
  color: #ffffff;
  font-size: 14px;
  background-color: #000000;
}
.pdf_main .pdf_table {
	margin-bottom: 50px;
	margin-bottom: 30pt;
}
.pdf_main .pdf_table p {
	color: #000000;
	font-size: 11px;
	font-weight: 400;
	margin-bottom: 10px;
}
.signature h4.signature-heading {
	font-size: 15px;
	display: inline-block;
	margin: 0px;
}
.signature .signature-line {
	border-bottom: 1px solid black;
	display: inline-block;
	vertical-align: middle;
	width: 311px;
	margin-left: 8px;
}
.black-theme.pdf_main .pdf_table thead th {
  color: #ffffff;
  font-size: 16px;
  background-color: #000000;
  text-align:left;
}
.black-theme.pdf_main tfoot td.grand-total {
	color: #ffffff;
	background-color: #000000;
}
.black-theme.pdf_main .footer {
	background-color: #000000;
	border-bottom: 3px #000000 solid;
}
.black-theme.pdf_main .footer li {
	border-right: 0;
}
.black-theme.pdf_main table tbody td {
	border: none !important;
}
.black-theme.pdf_main table th {
	border: none !important;
}
.lenth-sec {

	margin-left: 5px;
}
.lenth-sec > label {
	font-weight: 400;
}
.lenth-sec {
	height: 31px;
	vertical-align: top;
}
tr, td, th {
	border: 1px solid #bebcbc;
}
/*.pdf_main {
	margin-left: 38px;
	margin-right: 38px
}*/
.table-style tr td, .table-style tr td{padding-top:4px; padding-bottom:4px;}
.table-style tr .border-line{padding-bottom:7px;}
.segment-main {
  width: 100%;
  border-top: 1px solid #bebcbc;
  border-left: 1px solid #bebcbc;
  border-right: 1px solid #bebcbc;
  font-size: 14px;
}

.invoice-detail {
	font-size: 14px;
}
</style>
<div class="pdf_main">
    <?php
        if($package_info['company_id'] == 2){
    ?>
	<div style="text-align: center;"><img src="<?php echo DISPLAY_PATH . "img/branding-logo.jpg"; ?>" width="100" style="border-radius: 5px;"></div><br>
    <?php
        }
    ?>
    <div class="head-main">
		<div class="head-left">
			<div style="width: 100%;">
                <?php
                    $company_id = $package_info['company_id'];

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, name, phone_no, address", array('active' => 1, 'id' => $company_id));
                    $brand_name = isset($company_info['id']) ? $company_info['name'] : "";
                    $brand_phone_no = isset($company_info['id']) ? $company_info['phone_no'] : "";
                    $brand_address = isset($company_info['id']) ? $company_info['address'] : "";
                ?>
				<p><b><?php echo $brand_name; ?></b></p>
				<p class="invoice-detail" style="width: 80%;"><?php echo $brand_address; ?>
				<br><br><?php echo $brand_phone_no; ?></p>
			</div>
	    </div>
	    <div class="head-right">
			<div style="width: 100%;">
				<p><b>Invoice <?php echo "#000" . $package_info['id']; ?></b></p>
				<p class="invoice-detail">Date : <?php echo date("d M Y", strtotime($package_info['insert_time'])); ?>
                <?php
                    $user_id = $package_info['user_id'];
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, address_line1, phone_no", array('id' => $user_id, 'active' => 1));
                ?>
				<br>Name : <?php echo $user_info['fullname']; ?>
				<br>Address : <?php echo $user_info['address_line1']; ?>
				<br>Contact No : <?php echo $user_info['phone_no']; ?></p>
			</div>
	    </div>
    </div>
	<div class="segment-main">
		<!-- Header -->
        <div class="div-thead">
          	<div>
          		<div style="text-align:left;width:5%;float:left;padding:5px 0 5px 10px;">#</div>
	            <div style="text-align:left;width:35%;float:left;padding:5px 0 5px 10px;">Item</div>
	            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0">Price</div>
	            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0">Qty</div>
	            <div style="text-align:center;width:20%;float:left;padding:5px 0 5px 0">Total</div>
            </div>
        </div>
        <!-- body -->
        <div>
			<?php
				$purchase_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, unit_price", array('id' => $package_info['package_id'], 'active' => 1));
                $package_name = isset($purchase_package_info['id']) ? $purchase_package_info['name'] : "";
                $package_price = isset($purchase_package_info['id']) ? $purchase_package_info['unit_price'] : "";
                $grand_total = $package_info['amount'] * $package_price;

                if($package_info['subtotal'] != 0.00){
                    $grand_total = $package_info['subtotal'];
                }
			?>
			<div style="border-bottom:0px;">
				<div style="text-align:left;width:5%;float:left;padding:5px 0 5px 10px">1</div>
				<div style="text-align:left;width:35%;float:left;padding:5px 0 5px 10px"><?php echo $package_name; ?></div>
				<div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center">RM<?php echo $package_price; ?></div>
				<div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center"><?php echo $package_info['amount']; ?></div>
				<div style="text-align:center;width:20%;float:left;padding:5px 0 5px 0" class="center">RM<?php echo number_format($grand_total, 2); ?></div>
			</div>
        </div>
	</div>
    <table border="2" cellpadding="10" cellspacing="0" width="100%" class="table-style">
          <tr>
            <td rowspan="2" style="width: 50%;"></td>
            <td class="align-right">Grand Total</td>
            <td class="align-left">RM<?php echo number_format($grand_total, 2); ?></td>
          </tr>
    </table>
	<br><br>
	<p style="text-align: center; font-size: 12px;">This is a computer-generated document. No signature is required.</p>
</div>