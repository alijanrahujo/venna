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
    <div class="head-main">
		<div class="head-left">
			<div style="width: 100%;">
                <?php
                    $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username, phone_no", array('active' => 1, 'id' => $user_id));
                    $member_name = isset($member_info['id']) ? $member_info['fullname'] : "";
                    $member_username = isset($member_info['id']) ? $member_info['username'] : "";
                    $member_phone_no = isset($member_info['id']) ? $member_info['phone_no'] : "";
                ?>
				<p><b><?php echo $member_name . " (" . $member_username . ")"; ?></b></p>
				<p class="invoice-detail" style="width: 80%;"><?php echo $member_phone_no; ?></p>
			</div>
	    </div>
	    <div class="head-right"></div>
    </div>
	<table border="2" cellpadding="10" cellspacing="0" width="100%" class="table-style">
        <thead>
            <tr>
                <td>Agent</td>
                <td>Package</td>
                <td>Upline</td>
                <td>Retail Sales</td>
                <td>Retail Amount</td>
                <td>Restock</td>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($agent_list as $row_agent){
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, package_id, referral_id", array('id' => $row_agent['user_id'], 'active' => 1));
                    $user_id = isset($user_info['id']) ? $user_info['id'] : 0;
                    $member_name = isset($user_info['id']) ? $user_info['fullname'] : "";
                    $package_id = isset($user_info['id']) ? $user_info['package_id'] : 0;
                    $referral_id = isset($user_info['id']) ? $user_info['referral_id'] : 0;

                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname", array('id' => $referral_id, 'active' => 1));
                    $referral_name = isset($referral_info['id']) ? $referral_info['fullname'] : "";

                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $package_id, 'active' => 1));
                    $package_name = isset($package_info['id']) ? $package_info['name'] : "";

                    $retail_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, COUNT(*) as total_retail_sales", array('referral_id' => $user_id, 'active' => 1, 'order_status' => "APPROVE", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                    $total_retail_sales = isset($retail_info['id']) ? $retail_info['total_retail_sales'] : 0;

                    $retail_amount_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, SUM(total_price) as total_retail_sales_amount", array('referral_id' => $user_id, 'active' => 1, 'order_status' => "APPROVE", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                    $total_retail_sales_amount = isset($retail_amount_info['id']) ? $retail_amount_info['total_retail_sales_amount'] : 0;

                    $restock_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, COUNT(*) as total_restock", array('referral_id' => $user_id, 'status' => "APPROVE", 'is_restock' => 1, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                    $total_restock = isset($restock_package_info['id']) ? $restock_package_info['total_restock'] : 0;
            ?>
            <tr>
                <td><?php echo $member_name; ?></td>
                <td><?php echo $package_name; ?></td>
                <td><?php echo $referral_name; ?></td>
                <td><?php echo $total_retail_sales; ?></td>
                <td><?php echo $total_retail_sales_amount; ?></td>
                <td><?php echo $total_restock; ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
	<br><br>
	<p style="text-align: center; font-size: 12px;">This is a computer-generated document. No signature is required.</p>
</div>