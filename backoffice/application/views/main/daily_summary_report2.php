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
	<div style="text-align: center;"><img src="<?php echo DISPLAY_PATH . "img/mudahmakan.png"; ?>" width="100" style="border-radius: 5px;"></div><br>
    <div class="segment-main">
        <h5>Summary Order Report</h5>
		<!-- Header -->
        <div class="div-thead">
          	<div>
          		<div style="text-align:left;width:10%;float:left;padding:5px 0 5px 10px;">Order ID</div>
	            <div style="text-align:left;width:20%;float:left;padding:5px 0 5px 10px;">Customer</div>
	            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0">Total Qty</div>
	            <div style="text-align:center;width:20%;float:left;padding:5px 0 5px 0">Tracking No</div>
	            <div style="text-align:center;width:30%;float:left;padding:5px 0 5px 0">Shipment Date</div>
            </div>
        </div>
        <!-- body -->
        <div>
			<?php
				$count = 0;
				foreach($summary_order_list as $row_order){
			?>
			<div style="border-bottom:1px solid #bebcbc;">
				<div style="text-align:left;width:10%;float:left;padding:5px 0 5px 10px"><?php echo "#000" . $row_order['id']; ?></div>
				<div style="text-align:left;width:20%;float:left;padding:5px 0 5px 10px"><?php echo $row_order['s_name']; ?><br><?php echo $row_order['s_contact']; ?></div>
				<div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center"><?php echo $row_order['total_quantity']; ?></div>
				<div style="text-align:center;width:20%;float:left;padding:5px 0 5px 0" class="center"><?php echo $row_order['tracking_no']; ?></div>
                <?php
                    if($row_order['insert_time'] == NULL){
                        $order_date = "";
                    }else{
                        $order_date = date("Y-m-d h:ia" ,strtotime($row_order['insert_time']));
                    }
                ?>
				<div style="text-align:center;width:30%;float:left;padding:5px 0 5px 0" class="center"><?php echo $order_date; ?></div>
			</div>
			<?php
				}
			?>
        </div>
	</div>
    <br><br>
    <div class="segment-main">
        <h5>Summary Shipment Report</h5>
		<!-- Header -->
        <div class="div-thead">
          	<div>
          		<div style="text-align:left;width:10%;float:left;padding:5px 0 5px 10px;">Order ID</div>
	            <div style="text-align:left;width:20%;float:left;padding:5px 0 5px 10px;">Customer</div>
	            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0">Total Qty</div>
	            <div style="text-align:center;width:20%;float:left;padding:5px 0 5px 0">Tracking No</div>
	            <div style="text-align:center;width:30%;float:left;padding:5px 0 5px 0">Shipment Date</div>
            </div>
        </div>
        <!-- body -->
        <div>
			<?php
				$count = 0;
				foreach($summary_shipment_list as $row_order){
			?>
			<div style="border-bottom:1px solid #bebcbc;">
				<div style="text-align:left;width:10%;float:left;padding:5px 0 5px 10px"><?php echo "#000" . $row_order['id']; ?></div>
				<div style="text-align:left;width:20%;float:left;padding:5px 0 5px 10px"><?php echo $row_order['s_name']; ?><br><?php echo $row_order['s_contact']; ?></div>
				<div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center"><?php echo $row_order['total_quantity']; ?></div>
				<div style="text-align:center;width:20%;float:left;padding:5px 0 5px 0" class="center"><?php echo $row_order['tracking_no']; ?></div>
                <?php
                    if($row_order['shipped_at'] == NULL){
                        $shipment_date = "";
                    }else{
                        $shipment_date = date("Y-m-d h:ia" ,strtotime($row_order['shipped_at']));
                    }
                ?>
				<div style="text-align:center;width:30%;float:left;padding:5px 0 5px 0" class="center"><?php echo $shipment_date; ?></div>
			</div>
			<?php
				}
			?>
        </div>
	</div>
	<p style="text-align: center; font-size: 12px; margin-top: 50px;">This is a computer-generated document. No signature is required.</p>
</div>