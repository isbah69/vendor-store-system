<?php
session_start();
include_once "connection.php";

$last_sale_id = $_SESSION['last_sale_id'] ?? 0;
$sale_success = $_SESSION['sale_success'] ?? null;
$sale_error   = $_SESSION['sale_error'] ?? null;

unset($_SESSION['sale_success'], $_SESSION['sale_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Sale</title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>

body { background: #f5f7fa; padding: 20px; }
.card { border-radius: 10px; }
#search_product { height:48px; font-size:16px; }
.search-results { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ddd; max-height:220px; overflow-y:auto; display:none; z-index:9999; }
.search-results div { padding:8px; cursor:pointer; }
.search-results div:hover { background:#f0f0f0; }
.table thead { background-color: #007bff; color: black; }
.float-end { float:right; }
.position-relative { position: relative; }

/* --- CSS for Walk-in Customer Bar --- */
.action-bar-container {
    display: flex;
    align-items: center; /* Vertically centers the content */
    border: 1px solid #ddd;
    background-color: #fff; /* White background for the bar itself */
    border-radius: 4px;
    margin-bottom: 20px; /* Space below the bar */
    /* To align the width with the col-md-3 search card */
    width: calc((100% / 12) * 3 - 30px); /* Adjusting for padding/margin of container/row */
    max-width: 100%; /* Ensures it fits within its container */
}

/* Wrapper for the select/input field */
.customer-select-wrapper {
    flex-grow: 1;
    padding: 0 5px 0 10px; /* Padding inside the bar for the text */
}

/* Styling the actual select element */
.customer-select {
    font-size: 16px;
    border: none;
    background: transparent;
    padding: 10px 0;
    width: 100%;
    height: 48px; /* Match the height of your search input */
    font-weight: 500;
    cursor: pointer;
}

/* Icon Buttons Container */
.icon-buttons {
    display: flex;
    height: 100%; /* Ensure buttons stretch vertically */
    border-left: 1px solid #ddd; /* Separator line between select and icons */
}

/* General button styling */
.icon-btn {
    border: none;
    background-color: transparent;
    cursor: pointer;
    padding: 12px 12px;
    font-size: 16px; 
    color: #007bff; /* Blue color for the icons */
    transition: background-color 0.1s;
    height: 48px; /* Set height to match the input */
    display: flex;
    align-items: center;
    justify-content: center;
    border-left: 1px solid #eee; /* Separator between icon buttons */
}

.icon-btn:first-child {
    border-left: none; /* Remove separator on the first icon button */
}

.icon-btn:hover {
    background-color: #f0f0f0;
}

/* Specific styling for the 'Add' button (Blue background from your image) */
.icon-btn.add-btn {
    background-color: #007bff; /* Primary Bootstrap Blue */
    color: white;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
    border-left: none; /* The blue background makes the light separator unnecessary */
}

.icon-btn.add-btn:hover {
    background-color: #0056b3;
}

body { background: #f5f7fa; padding: 20px; }
.card { border-radius: 10px; }
#search_product { height:48px; font-size:16px; }
.search-results { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ddd; max-height:220px; overflow-y:auto; display:none; z-index:9999; }
.search-results div { padding:8px; cursor:pointer; }
.search-results div:hover { background:#f0f0f0; }
.table thead { background-color: #007bff; color: black; }
.float-end { float:right; }
.position-relative { position: relative; }

</style>
</head>
<body>

<div class="container-fluid">
<h2 class="mb-4">🛍️ Add New Sale</h2>
<!-- Back Button -->
<a href="sales.php" class="btn btn-outline-primary mb-3">
  <i class="fa fa-arrow-left"></i> Back to Sales
</a>

<?php if (!empty($errorMsg)): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
<?php endif; ?>

<!-- WALK-IN CUSTOMER BAR: same width as search card -->
<!-- WALK-IN CUSTOMER SECTION -->
 <input type="hidden" id="customer_id" name="customer_id" value="">

<div class="action-bar-container position-relative" style="width: calc((100% / 12) * 3 - 30px); max-width: 100%;">
    <div class="customer-select-wrapper position-relative" style="width:100%;">
        <input type="text" id="walkin_customer_search" class="form-control" placeholder="Search Walkin customer..." autocomplete="off">
        <input type="hidden" id="customer_id" name="customer_id">
        <div id="customer_suggestions" class="search-results" style="display:none; position:absolute; top:100%; left:0; right:0; background:white; border:1px solid #ddd; z-index:1000; max-height:200px; overflow-y:auto;"></div>
    </div>

    <div class="icon-buttons">
        <button class="icon-btn" id="editCustomerBtn">
            <i class="fas fa-pencil-alt"></i>
        </button>
        <!-- Eye Button (update to include id so JS targets the right button) -->
<button type="button" class="icon-btn" id="viewCustomerBtn" data-toggle="modal" data-target="#customerModal">
  <i class="fas fa-eye"></i>
</button>
<a href="add_customer.php" class="icon-btn" title="Add Customer">
  <i class="fa fa-plus"></i>
</a>


<!-- ==================== WALK-IN CUSTOMER MODAL (REPLACE YOUR OLD ONE) ==================== -->
<div id="customerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">WALK-IN CUSTOMER</h4>
        <button type="button" class="btn btn-default btn-sm pull-right" id="printCustomer">
          <i class="fa fa-print"></i> Print
        </button>
      </div>

      <!-- Modal Body (cells have ids so JS can fill them) -->
      <div class="modal-body" id="customerInfo">
        <table class="table table-bordered">
          <tr><th>Company</th><td id="c_company">Walk-in Customer</td></tr>
          <tr><th>Name</th><td id="c_name">Walk-in Customer</td></tr>
          <tr><th>Customer Group</th><td id="c_group">General</td></tr>
          <tr><th>VAT Number</th><td id="c_vat"></td></tr>
          <tr><th>GST Number</th><td id="c_gst"></td></tr>
          <tr><th>Deposit</th><td id="c_deposit">0.00</td></tr>
          <tr><th>Award Points</th><td id="c_points">0</td></tr>
          <tr><th>Email</th><td id="c_email">customer@tecdiary.com</td></tr>
          <tr><th>Phone</th><td id="c_phone">0123456789</td></tr>
          <tr><th>Address</th><td id="c_address">Customer Address</td></tr>
          <tr><th>City</th><td id="c_city">Khanpur</td></tr>
          <tr><th>State</th><td id="c_state">Punjab</td></tr>
          <tr><th>Postal Code</th><td id="c_postal">46000</td></tr>
          <tr><th>Country</th><td id="c_country">Pakistan</td></tr>
        </table>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer" style="display: flex; justify-content: space-between;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>

        <div>
          <a href="customer_report.php?id=0" class="btn btn-info" id="customerReportBtn">
            <i class="fa fa-file-text-o"></i> Customer Report
          </a>
         <a href="exit_customer.php" class="btn btn-danger">
  <i class="fa fa-sign-out"></i> Exit Customer
</a>


        </div>
      </div>

    </div>
  </div>
</div>

<!-- PRINT handler (keeps your print behavior) -->
<script>
document.getElementById('printCustomer').addEventListener('click', function() {
  var printContents = document.getElementById('customerInfo').innerHTML;
  var printWindow = window.open('', '', 'width=800,height=600');
  printWindow.document.write('<html><head><title>Customer Info</title>');
  printWindow.document.write('<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">');
  printWindow.document.write('</head><body>');
  printWindow.document.write('<h3 style="text-align:center;">Customer Information</h3>');
  printWindow.document.write(printContents);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  printWindow.print();
});
</script>

<!-- Core JS: set selected customer id from search results AND load details into modal -->
<script>
/*
  Instructions:
  - Ensure jQuery is already loaded on your page (your page already includes it).
  - Ensure when a search result is clicked it has class "customer-item" and data-id attribute.
    (If your search_customer.php already outputs elements with class "customer-item" and data-id,
     the code below will automatically set the hidden input.)
  - This script:
      * sets #customer_id when a .customer-item is clicked
      * when you click the Eye button it fetches details for #customer_id and fills the modal
*/

// 1) When user clicks a search result, set the hidden customer_id and place name in search input.
//    This uses delegated event so it works with AJAX-inserted results.
$(document).on('click', '.customer-item', function(e) {
  e.preventDefault();
  var id = $(this).data('id') || $(this).attr('data-id');
  var name = $(this).data('name') || $(this).attr('data-name') || $(this).text().trim();
  // set hidden input
  $('#customer_id').val(id);
  // if you have an input showing selected customer name, set it too (common id used earlier)
  if ($('#walkin_customer_search').length) {
    $('#walkin_customer_search').val(name);
  } else if ($('#walkin_customer').length) {
    $('#walkin_customer').val(name);
  }
  // hide results dropdown if present
  $('#customer_suggestions, #customerResults, #customer_results, #customerSearchResults').hide();
});

// 2) When Eye button clicked (user opens modal), fetch selected customer details and fill table
$('#viewCustomerBtn').on('click', function() {
  var customerId = $('#customer_id').val() || '';
  if (!customerId || customerId === '0') {
    // no selected customer -> keep modal defaults (Walk-in) and ensure report link is id=0
    $('#customerReportBtn').attr('href','customer_report.php?id=0');
    return;
  }

  // fetch JSON from backend
  $.ajax({
    url: 'get_customer_details.php',
    method: 'GET',
    data: { id: customerId },
    dataType: 'json',
    success: function(res) {
      if (!res || res.status !== 'success') {
        console.warn('Customer not found or invalid response', res);
        // optionally show a message or reset to defaults
        $('#c_name').text('Walk-in Customer');
        $('#customerReportBtn').attr('href','customer_report.php?id=0');
        return;
      }

      // fill modal cells. use fallback empty string if key missing
      $('#c_company').text(res.company || 'Walk-in Customer');
      $('#c_name').text(res.name || '');
      $('#c_group').text(res.customer_group || 'General');
      $('#c_vat').text(res.vat_number || '');
      $('#c_gst').text(res.gst_number || '');
      $('#c_deposit').text(res.deposit || '0.00');
      $('#c_points').text(res.award_points || '0');
      $('#c_email').text(res.email || '');
      $('#c_phone').text(res.phone || '');
      $('#c_address').text(res.address || '');
      $('#c_city').text(res.city || '');
      $('#c_state').text(res.state || '');
      $('#c_postal').text(res.postal_code || '');
      $('#c_country').text(res.country || '');

      // update report link to selected customer
      $('#customerReportBtn').attr('href','customer_report.php?id=' + customerId);
    },
    error: function(xhr, status, err) {
      console.error('AJAX error', err);
    }
  });
});
</script>

      </div>
    </div>
  </div>
</div>

<!-- ==================== PRINT FUNCTION ==================== -->
<script>
  document.getElementById('printCustomer').addEventListener('click', function() {
    var printContents = document.getElementById('customerInfo').innerHTML;
    var printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Customer Info</title>');
    printWindow.document.write('<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h3 style="text-align:center;">Customer Information</h3>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  });
</script>
    </div>
</div>
        
    </div>
</div>

<!-- CUSTOMER INFO MODAL -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="customer_details">
        <p class="text-muted">Select a customer to view details.</p>
      </div>
    </div>
  </div>
</div>

        


    
<!-- MAIN ROW: Left search/table + Right form -->
<div class="row">
  <!-- LEFT SIDE: Search + Product Table -->
  <div class="col-md-3">
    <div class="card shadow p-4 mb-4 position-relative">
      
      <input type="text" id="search_product" class="form-control" placeholder="Search product by name...">
      <div class="search-results" id="product_results"></div>
    </div>

    <div class="card shadow p-4">
      <div class="table-responsive">
        <table class="table table-bordered mt-3" id="selected_products">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Subtotal</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- RIGHT SIDE: Sale Form -->
  <div class="col-md-9">
    <div class="card shadow p-3">
      <form action="save_sale.php" method="POST" id="saleForm">
        <div class="mb-3">
          <label class="fw-bold">Company Name</label>
          <select name="company_id" class="form-control" required>
            <option value="">-- Select Company --</option>
            <?php
            $companies = mysqli_query($con, "SELECT * FROM companies");
            while ($c = mysqli_fetch_assoc($companies)) {
              echo "<option value='".htmlspecialchars($c['company_id'])."'>".htmlspecialchars($c['name'])."</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="fw-bold">Sale Date</label>
          <input type="date" name="sale_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="mb-3">
          <label class="fw-bold">Total Amount</label>
          <input type="text" id="total_amount" name="total_amount" class="form-control text-end" readonly value="0.00">
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-primary px-4"><i class="fas fa-plus-circle"></i> Add Sale</button>
          <button type="reset" class="btn btn-secondary px-4"><i class="fas fa-undo"></i> Reset</button>
        </div>
      </form>
    </div>
  </div>
  <!-- ====================== -->
<!-- STOCK UPDATE SCRIPT -->
<!-- ====================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateProductStock(product_id, qty) {
  return $.ajax({
    url: 'update_stock.php',
    type: 'POST',
    data: { product_id: product_id, qty: qty }
  });
}

// 🟡 Attach submit event to the form
$('#saleForm').on('submit', function(e) {
  e.preventDefault(); // stop default submit first

  let ajaxCalls = [];
  let hasError = false;

  // Loop through products and update stock
  $('#selected_products tbody tr').each(function() {
    let product_id = $(this).find('input[name="product_id[]"]').val();
    let qty = $(this).find('input[name="qty[]"]').val();

    ajaxCalls.push(
      updateProductStock(product_id, qty).done(function(response) {
        if (response !== 'success') {
          hasError = true;
          if (response === 'not_enough_stock') {
            alert('⚠️ Not enough stock for product ID: ' + product_id);
          } else if (response === 'product_not_found') {
            alert('❌ Product not found: ' + product_id);
          } else {
            alert('❌ Failed to update stock for product ID: ' + product_id);
          }
        }
      })
    );
  });

  // When all stock updates are done, submit form if no errors
  $.when.apply($, ajaxCalls).done(function() {
    if (!hasError) {
      $('#saleForm').off('submit').submit(); // re-submit normally
    }
  });
});
</script>

</div> <!-- END ROW -->

<!-- SUMMARY + BUTTONS: full-width at bottom -->
 <div class="bg-white p-3 shadow">
<div class="container-fluid mt-3">
  <div class="card shadow p-3" style="max-width:25%; width:100%;">
    <table class="table table-borderless" style="width:100%; font-weight:bold; table-layout: fixed;">
      <tbody>
        <tr>
          <td>Items</td><td id="summary_items">0 (0)</td>
          <td>Total</td><td id="summary_total">₨ 0.00</td>
        </tr>
        <tr>
          <td style="position: relative;">
            Order Tax
            <i class="fa fa-pencil text-primary ms-1" style="cursor:pointer;" id="edit_tax"></i>
            <span id="summary_tax_display" class="float-end">₨ 0.00</span>

            <div id="tax_form" style="display:none; position: absolute; top: 25px; left: 0; background: white; border: 1px solid #ccc; padding: 8px; z-index: 1000; border-radius: 5px; min-width: 140px;">
              <select id="order_tax" class="form-control form-control-sm">
                <option value="0">No Tax</option>
                <option value="5">VAT @5%</option>
                <option value="10">VAT @10%</option>
                <option value="20">VAT @20%</option>
                <option value="17">sales @17%</option>
              </select>
              <button type="button" id="save_tax" class="btn btn-sm btn-primary mt-2 w-100">Save</button>
            </div>
          </td>

          <td>Discount <i class="fa fa-pencil text-primary ms-1" style="cursor:pointer;" id="edit_discount"></i></td>
          <td id="summary_discount_display">₨ 0.00</td>
        </tr>
      </tbody>
    </table>

    <div id="discount_form" style="display:none; margin-top:8px; position:relative;">
      <input type="number" id="discount_input" class="form-control mb-2" placeholder="Enter discount amount" min="0" step="0.01">
      <button type="button" class="btn btn-sm btn-success" id="save_discount">Save Discount</button>
    </div>

    <div class="mt-2" style="background:#e0e0e0; color:black; font-weight:bold; padding:8px; font-size:16px; display:flex; justify-content:space-between; align-items:center;">
      <span>Total Payable</span>
      <span>₨ <span id="total_payable">0.00</span></span>
    </div>

    <!-- Buttons -->
    <div class="mt-3">
      <button class="btn btn-warning" onclick="openSuspendModal()">Suspend</button>
      
 <!-- ── Bill Button ── -->
<button type="button" class="btn btn-success" id="billBtn">
  <i class="fas fa-file-invoice"></i> Bill
</button>

<style>
.btn-bill-trigger {
  background: linear-gradient(135deg, #1e7e34, #28a745);
  border: none;
  color: #fff;
  padding: 10px 22px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  letter-spacing: 0.4px;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(40,167,69,0.35);
}
.btn-bill-trigger:hover {
  background: linear-gradient(135deg, #155724, #1e7e34);
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(40,167,69,0.45);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

  const billBtn = document.getElementById('billBtn');

  billBtn.addEventListener('click', function () {

    let products          = [];
    let subtotal          = 0;
    let totalDiscount     = 0;
    let totalTax          = 0;

    let orderTaxPercent      = parseFloat(document.getElementById('order_tax')?.value)      || 0;
    let orderDiscountAmount  = parseFloat(document.getElementById('discount_input')?.value) || 0;

    document.querySelectorAll('#selected_products tbody tr').forEach(function (row) {
      let name     = row.querySelector('td:nth-child(1)')?.innerText?.trim()              || '';
      let price    = parseFloat(row.querySelector('input[name="price[]"]')?.value)        || 0;
      let qty      = parseInt(row.querySelector('input[name="qty[]"]')?.value)            || 0;
      let discount = parseFloat(row.querySelector('input[name="discount[]"]')?.value)     || 0;
      let tax      = parseFloat(row.querySelector('input[name="tax[]"]')?.value)          || 0;

      let lineTotal      = price * qty;
      let discountAmount = (discount / 100) * lineTotal;
      let afterDiscount  = lineTotal - discountAmount;
      let taxAmount      = (tax / 100) * afterDiscount;
      let finalTotal     = afterDiscount + taxAmount;

      subtotal      += lineTotal;
      totalDiscount += discountAmount;
      totalTax      += taxAmount;

      products.push({ name, price, qty, discount, tax, lineTotal, discountAmount, taxAmount, finalTotal });
    });

    if (products.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Products',
        text: 'Please add products before generating a bill.',
        confirmButtonColor: '#28a745',
      });
      return;
    }

    // Order-level discount + tax
    totalDiscount += orderDiscountAmount;
    let orderLevelTaxAmount = ((subtotal - totalDiscount) * orderTaxPercent) / 100;
    totalTax      += orderLevelTaxAmount;
    let grandTotal = subtotal - totalDiscount + totalTax;

    // ── Build rows ──
    let rows = '';
    products.forEach((p, i) => {
      rows += `
        <tr class="${i % 2 === 0 ? 'row-even' : 'row-odd'}">
          <td class="col-num">${i + 1}</td>
          <td class="col-name">${p.name}</td>
          <td class="col-center">${p.qty}</td>
          <td class="col-right">Rs. ${p.price.toFixed(2)}</td>
          <td class="col-center">${p.discount > 0 ? p.discount + '%' : '—'}</td>
          <td class="col-center">${p.tax > 0 ? p.tax + '%' : '—'}</td>
          <td class="col-right highlight">Rs. ${p.finalTotal.toFixed(2)}</td>
        </tr>`;
    });

    const now       = new Date();
    const dateStr   = now.toLocaleDateString('en-PK', { year: 'numeric', month: 'long', day: 'numeric' });
    const timeStr   = now.toLocaleTimeString('en-PK', { hour: '2-digit', minute: '2-digit' });
    const billNo    = 'BILL-' + now.getFullYear()
                    + String(now.getMonth() + 1).padStart(2, '0')
                    + String(now.getDate()).padStart(2, '0')
                    + '-' + String(Math.floor(Math.random() * 9000) + 1000);

    let billWindow = window.open('', '_blank', 'width=780,height=900');
    billWindow.document.write(`
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Bill — ${billNo}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 13px;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      padding: 30px 10px;
    }

    .bill {
      background: #fff;
      width: 720px;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    /* ── Header ── */
    .bill-header {
      background: linear-gradient(135deg, #155724, #1e7e34);
      color: #fff;
      padding: 28px 30px 22px;
      text-align: center;
    }
    .store-name {
      font-size: 26px;
      font-weight: 800;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }
    .store-name span { color: #a3f7b5; }
    .store-tagline {
      font-size: 12px;
      opacity: 0.8;
      letter-spacing: 0.5px;
      margin-bottom: 16px;
    }
    .bill-badge {
      display: inline-block;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 20px;
      padding: 5px 20px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    /* ── Meta ── */
    .bill-meta {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      border-bottom: 1px solid #eee;
    }
    .meta-item {
      padding: 13px 18px;
      border-right: 1px solid #eee;
    }
    .meta-item:last-child { border-right: none; }
    .meta-label {
      font-size: 10px;
      font-weight: 700;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      margin-bottom: 4px;
    }
    .meta-value {
      font-size: 13px;
      font-weight: 600;
      color: #222;
    }

    /* ── Table ── */
    .bill-table-wrap { padding: 20px 20px 0; }

    table { width: 100%; border-collapse: collapse; }

    thead tr {
      background: linear-gradient(135deg, #155724, #1e7e34);
      color: #fff;
    }
    thead th {
      padding: 11px 12px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }

    .col-num    { width: 5%;  text-align: center; }
    .col-name   { width: 30%; }
    .col-center { text-align: center; }
    .col-right  { text-align: right; }
    .highlight  { font-weight: 700; color: #155724; }

    tbody tr.row-even { background: #f6fdf7; }
    tbody tr.row-odd  { background: #fff; }
    tbody td {
      padding: 10px 12px;
      font-size: 13px;
      color: #333;
      border-bottom: 1px solid #f0f0f0;
    }

    /* ── Summary ── */
    .bill-summary {
      margin: 0 20px;
      border-top: 2px solid #eee;
      padding: 16px 0 0;
    }
    .summary-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0 40px;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 14px;
      font-size: 13px;
      color: #555;
      border-bottom: 1px dashed #eee;
    }
    .summary-row.discount { color: #c0392b; }
    .summary-row.tax      { color: #e67e22; }

    .grand-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(135deg, #155724, #1e7e34);
      color: #fff;
      border-radius: 8px;
      margin: 14px 14px 0;
      padding: 16px 20px;
      font-size: 18px;
      font-weight: 800;
      letter-spacing: 0.3px;
    }
    .grand-total-row .label { opacity: 0.9; font-size: 14px; }

    /* ── Footer ── */
    .bill-footer {
      text-align: center;
      padding: 18px 30px 22px;
      margin-top: 16px;
      border-top: 1px dashed #ddd;
      color: #999;
      font-size: 12px;
      line-height: 1.9;
    }
    .bill-footer .thank-you {
      font-size: 15px;
      font-weight: 700;
      color: #333;
      margin-bottom: 4px;
    }
    .merchant-copy {
      display: inline-block;
      background: #eef3fb;
      color: #0f3460;
      border: 1px solid #c5d8f5;
      border-radius: 20px;
      padding: 3px 16px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-top: 8px;
    }

    /* ── Print Bar ── */
    .print-bar {
      display: flex;
      justify-content: center;
      gap: 12px;
      padding: 16px 20px;
      background: #f8f9fc;
      border-top: 1px solid #eee;
    }
    .btn-print {
      background: linear-gradient(135deg, #155724, #1e7e34);
      color: #fff;
      border: none;
      padding: 11px 30px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
    }
    .btn-print:hover { opacity: 0.9; }
    .btn-close-win {
      background: #f0f2f5;
      color: #555;
      border: 1.5px solid #dde2ec;
      padding: 11px 24px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-close-win:hover { background: #e2e6ea; }

    /* ── Print Media ── */
    @media print {
      body { background: #fff; padding: 0; }
      .bill { box-shadow: none; border-radius: 0; width: 100%; }
      .print-bar { display: none; }
    }
  </style>
</head>
<body>
<div class="bill">

  <!-- Header -->
  <div class="bill-header">
    <div class="store-name"><span>Vendor</span> Store System</div>
    <div class="store-tagline">Your Trusted Retail Partner</div>
    <div class="bill-badge">&#128179; Tax Invoice</div>
  </div>

  <!-- Meta -->
  <div class="bill-meta">
    <div class="meta-item">
      <div class="meta-label">Bill No.</div>
      <div class="meta-value">${billNo}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Date</div>
      <div class="meta-value">${dateStr}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Time</div>
      <div class="meta-value">${timeStr}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Served By</div>
      <div class="meta-value">Walk-in</div>
    </div>
  </div>

  <!-- Table -->
  <div class="bill-table-wrap">
    <table>
      <thead>
        <tr>
          <th class="col-num">#</th>
          <th class="col-name">Item</th>
          <th class="col-center">Qty</th>
          <th class="col-right">Unit Price</th>
          <th class="col-center">Disc %</th>
          <th class="col-center">Tax %</th>
          <th class="col-right">Total</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>
  </div>

  <!-- Summary -->
  <div class="bill-summary">
    <div class="summary-grid">
      <div>
        <div class="summary-row">
          <span>Total Items</span>
          <span>${products.length} item(s)</span>
        </div>
        <div class="summary-row">
          <span>Total Qty</span>
          <span>${products.reduce((s, p) => s + p.qty, 0)} unit(s)</span>
        </div>
      </div>
      <div>
        <div class="summary-row">
          <span>Subtotal</span>
          <span>Rs. ${subtotal.toFixed(2)}</span>
        </div>
        <div class="summary-row discount">
          <span>Total Discount</span>
          <span>- Rs. ${totalDiscount.toFixed(2)}</span>
        </div>
        <div class="summary-row tax">
          <span>Total Tax</span>
          <span>+ Rs. ${totalTax.toFixed(2)}</span>
        </div>
      </div>
    </div>

    <div class="grand-total-row">
      <span class="label">Grand Total</span>
      <span>Rs. ${grandTotal.toFixed(2)}</span>
    </div>
  </div>

  <!-- Footer -->
  <div class="bill-footer">
    <div class="thank-you">Thank you for your business!</div>
    <div>Please retain this bill for your records. All sales are final unless otherwise stated.</div>
    <div>For queries: info@vendorstore.pk &nbsp;|&nbsp; +92-42-3500-1100</div>
    <div><span class="merchant-copy">Merchant Copy</span></div>
  </div>

  <!-- Print Bar -->
  <div class="print-bar">
    <button class="btn-print" onclick="window.print()">
      &#128438; &nbsp;Print Bill
    </button>
    <button class="btn-close-win" onclick="window.close()">
      &times; &nbsp;Close
    </button>
  </div>

</div>
</body>
</html>`);

    billWindow.document.close();
  });

});
</script>
      <!-- ── Order Button ── -->
<button type="button" id="orderBtn" class="btn-order-trigger">
  <i class="fa fa-shopping-cart"></i> &nbsp;Order Receipt
</button>

<style>
.btn-order-trigger {
  background: linear-gradient(135deg, #6f42c1, #4a2c91);
  border: none;
  color: #fff;
  padding: 10px 22px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  letter-spacing: 0.4px;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(111,66,193,0.35);
}
.btn-order-trigger:hover {
  background: linear-gradient(135deg, #4a2c91, #6f42c1);
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(111,66,193,0.45);
}
</style>

<script>
  document.getElementById('orderBtn').addEventListener('click', function () {
    let products   = [];
    let totalAmount = 0;

    document.querySelectorAll('#selected_products tbody tr').forEach(function (row) {
      let name      = row.querySelector('td:nth-child(1)')?.innerText?.trim() || '';
      let price     = parseFloat(row.querySelector('input[name="price[]"]')?.value) || 0;
      let qty       = parseInt(row.querySelector('input[name="qty[]"]')?.value)     || 0;
      let lineTotal = price * qty;
      totalAmount  += lineTotal;
      products.push({ name, price, qty, lineTotal });
    });

    if (products.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Products',
        text: 'Please add products before placing an order.',
        confirmButtonColor: '#6f42c1',
      });
      return;
    }

    // ── Build receipt rows ──
    let rows = '';
    products.forEach((p, i) => {
      rows += `
        <tr class="${i % 2 === 0 ? 'row-even' : 'row-odd'}">
          <td class="col-num">${i + 1}</td>
          <td class="col-name">${p.name}</td>
          <td class="col-qty">${p.qty}</td>
          <td class="col-price">Rs. ${p.price.toFixed(2)}</td>
          <td class="col-total">Rs. ${p.lineTotal.toFixed(2)}</td>
        </tr>`;
    });

    const now         = new Date();
    const dateStr     = now.toLocaleDateString('en-PK', { year:'numeric', month:'long', day:'numeric' });
    const timeStr     = now.toLocaleTimeString('en-PK', { hour:'2-digit', minute:'2-digit' });
    const receiptNo   = 'ORD-' + now.getFullYear() + String(now.getMonth()+1).padStart(2,'0') + String(now.getDate()).padStart(2,'0') + '-' + String(Math.floor(Math.random()*9000)+1000);

    let orderWindow = window.open('', '_blank', 'width=750,height=800');
    orderWindow.document.write(`
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order Receipt — ${receiptNo}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 13px;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      padding: 30px 10px;
    }

    .receipt {
      background: #fff;
      width: 680px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    /* ── Header ── */
    .receipt-header {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: #fff;
      padding: 28px 30px 22px;
      text-align: center;
    }
    .receipt-header .store-name {
      font-size: 24px;
      font-weight: 800;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }
    .receipt-header .store-name span { color: #a78bfa; }
    .receipt-header .store-tagline {
      font-size: 12px;
      opacity: 0.75;
      letter-spacing: 0.5px;
      margin-bottom: 16px;
    }
    .receipt-badge {
      display: inline-block;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.25);
      border-radius: 20px;
      padding: 5px 18px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    /* ── Meta Info ── */
    .receipt-meta {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 0;
      border-bottom: 1px solid #eee;
    }
    .meta-item {
      padding: 14px 20px;
      border-right: 1px solid #eee;
    }
    .meta-item:last-child { border-right: none; }
    .meta-label {
      font-size: 10px;
      font-weight: 700;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      margin-bottom: 4px;
    }
    .meta-value {
      font-size: 13px;
      font-weight: 600;
      color: #222;
    }

    /* ── Table ── */
    .receipt-table-wrap { padding: 20px 20px 0; }

    table {
      width: 100%;
      border-collapse: collapse;
    }
    thead tr {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: #fff;
    }
    thead th {
      padding: 11px 14px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.7px;
      text-transform: uppercase;
    }
    .col-num   { width: 5%;  text-align: center; }
    .col-name  { width: 38%; }
    .col-qty   { width: 12%; text-align: center; }
    .col-price { width: 20%; text-align: right; }
    .col-total { width: 25%; text-align: right; }

    tbody tr.row-even { background: #f8f9fc; }
    tbody tr.row-odd  { background: #fff; }
    tbody td {
      padding: 10px 14px;
      font-size: 13px;
      color: #333;
      border-bottom: 1px solid #f0f0f0;
    }

    /* ── Totals ── */
    .totals-section {
      margin: 0 20px;
      border-top: 2px solid #eee;
      padding: 16px 0 0;
    }
    .totals-row {
      display: flex;
      justify-content: space-between;
      padding: 5px 14px;
      font-size: 13px;
      color: #555;
    }
    .totals-row.grand {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: #fff;
      border-radius: 8px;
      margin-top: 10px;
      padding: 14px 18px;
      font-size: 16px;
      font-weight: 800;
      letter-spacing: 0.3px;
    }

    /* ── Footer ── */
    .receipt-footer {
      text-align: center;
      padding: 20px 30px 24px;
      margin-top: 16px;
      border-top: 1px dashed #ddd;
      color: #999;
      font-size: 12px;
      line-height: 1.8;
    }
    .receipt-footer .thank-you {
      font-size: 15px;
      font-weight: 700;
      color: #333;
      margin-bottom: 4px;
    }

    /* ── Print Button ── */
    .print-bar {
      display: flex;
      justify-content: center;
      gap: 12px;
      padding: 16px 20px;
      background: #f8f9fc;
      border-top: 1px solid #eee;
    }
    .btn-print {
      background: linear-gradient(135deg, #1a1a2e, #0f3460);
      color: #fff;
      border: none;
      padding: 11px 30px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      letter-spacing: 0.3px;
    }
    .btn-print:hover { opacity: 0.9; }
    .btn-close-win {
      background: #f0f2f5;
      color: #555;
      border: 1.5px solid #dde2ec;
      padding: 11px 24px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-close-win:hover { background: #e2e6ea; }

    /* ── Print Media ── */
    @media print {
      body { background: #fff; padding: 0; }
      .receipt { box-shadow: none; border-radius: 0; width: 100%; }
      .print-bar { display: none; }
    }
  </style>
</head>
<body>
<div class="receipt">

  <!-- Header -->
  <div class="receipt-header">
    <div class="store-name"><span>Vendor</span> Store System</div>
    <div class="store-tagline">Your Trusted Retail Partner</div>
    <div class="receipt-badge">&#128722; Order Receipt</div>
  </div>

  <!-- Meta Info -->
  <div class="receipt-meta">
    <div class="meta-item">
      <div class="meta-label">Receipt No.</div>
      <div class="meta-value">${receiptNo}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Date</div>
      <div class="meta-value">${dateStr}</div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Time</div>
      <div class="meta-value">${timeStr}</div>
    </div>
  </div>

  <!-- Table -->
  <div class="receipt-table-wrap">
    <table>
      <thead>
        <tr>
          <th class="col-num">#</th>
          <th class="col-name">Item</th>
          <th class="col-qty">Qty</th>
          <th class="col-price">Unit Price</th>
          <th class="col-total">Amount</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>
  </div>

  <!-- Totals -->
  <div class="totals-section">
    <div class="totals-row">
      <span>Total Items</span>
      <span>${products.length} item(s)</span>
    </div>
    <div class="totals-row">
      <span>Total Qty</span>
      <span>${products.reduce((s,p)=>s+p.qty,0)} unit(s)</span>
    </div>
    <div class="totals-row grand">
      <span>Grand Total</span>
      <span>Rs. ${totalAmount.toFixed(2)}</span>
    </div>
  </div>

  <!-- Footer -->
  <div class="receipt-footer">
    <div class="thank-you">Thank you for your order!</div>
    <div>Please retain this receipt for your records.</div>
    <div>For queries, contact: info@vendorstore.pk | +92-42-3500-1100</div>
  </div>

  <!-- Print Bar -->
  <div class="print-bar">
    <button class="btn-print" onclick="window.print()">
      &#128438; &nbsp;Print Receipt
    </button>
    <button class="btn-close-win" onclick="window.close()">
      &times; &nbsp;Close
    </button>
  </div>

</div>
</body>
</html>`);

    orderWindow.document.close();
  });
</script>

      <!-- 🟢 Payment Button -->
<button type="button" id="paymentBtn" class="btn btn-payment-trigger">
  <i class="fa fa-credit-card"></i>&nbsp; Payment
</button>

<!-- 💵 Payment Modal -->
<div id="paymentModal" class="pos-modal">
  <div class="pos-modal-header">
    <div class="modal-title-wrap">
      <i class="fa fa-credit-card"></i>
      <span>Payment Summary</span>
    </div>
    <button id="closePayment" class="close-btn"><i class="fa fa-times"></i></button>
  </div>

  <div class="pos-modal-body">

    <!-- Amount Summary Cards -->
    <div class="amount-cards">
      <div class="amount-card total-card">
        <div class="amount-label">Total Amount</div>
        <div class="amount-value" id="totalDisplay">Rs. 0.00</div>
        <input type="hidden" id="totalAmount">
      </div>
      <div class="amount-card balance-card">
        <div class="amount-label">Balance</div>
        <div class="amount-value" id="balanceDisplay">Rs. 0.00</div>
        <input type="hidden" id="balanceAmount">
      </div>
    </div>

    <!-- Paid Amount -->
    <div class="field-group">
      <label class="field-label">
        <i class="fa fa-money"></i> &nbsp;Amount Paid
      </label>
      <div class="input-wrap">
        <span class="input-prefix">Rs.</span>
        <input type="number"
               id="paidAmount"
               class="field-input"
               placeholder="0.00"
               min="0"
               step="0.01">
      </div>
    </div>

    <!-- Payment Method -->
    <div class="field-group">
      <label class="field-label">
        <i class="fa fa-university"></i> &nbsp;Payment Method
      </label>
      <div class="method-options">
        <label class="method-option active" data-value="Cash">
          <input type="radio" name="payMethod" value="Cash" checked hidden>
          <i class="fa fa-money"></i>
          <span>Cash</span>
        </label>
        <label class="method-option" data-value="Card">
          <input type="radio" name="payMethod" value="Card" hidden>
          <i class="fa fa-credit-card"></i>
          <span>Card</span>
        </label>
        <label class="method-option" data-value="Bank Transfer">
          <input type="radio" name="payMethod" value="Bank Transfer" hidden>
          <i class="fa fa-exchange"></i>
          <span>Bank</span>
        </label>
      </div>
    </div>

    <!-- Status Message -->
    <div id="payStatus" class="pay-status" style="display:none;"></div>

    <!-- Confirm Button -->
    <button id="confirmPayment" class="btn-confirm">
      <i class="fa fa-check-circle"></i> &nbsp;Confirm Payment
    </button>

  </div>
</div>

<!-- Overlay -->
<div id="modalOverlay" class="modal-overlay"></div>

<style>
/* ── Trigger Button ── */
.btn-payment-trigger {
  background: linear-gradient(135deg, #1a1a2e, #16213e);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 10px 22px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  letter-spacing: 0.4px;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(0,0,0,0.25);
}
.btn-payment-trigger:hover {
  background: linear-gradient(135deg, #16213e, #0f3460);
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* ── Overlay ── */
.modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  backdrop-filter: blur(3px);
  z-index: 9998;
}

/* ── Modal ── */
.pos-modal {
  display: none;
  position: fixed;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%) scale(0.9);
  background: #fff;
  width: 380px;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.35);
  z-index: 9999;
  animation: popIn 0.3s cubic-bezier(0.34,1.56,0.64,1) forwards;
}

@keyframes popIn {
  from { transform: translate(-50%, -50%) scale(0.85); opacity: 0; }
  to   { transform: translate(-50%, -50%) scale(1);    opacity: 1; }
}

/* ── Header ── */
.pos-modal-header {
  background: linear-gradient(135deg, #1a1a2e, #0f3460);
  color: #fff;
  padding: 16px 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-title-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.3px;
}
.modal-title-wrap .fa { font-size: 18px; opacity: 0.9; }
.close-btn {
  background: rgba(255,255,255,0.15);
  border: none;
  color: #fff;
  width: 30px; height: 30px;
  border-radius: 50%;
  font-size: 14px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s;
}
.close-btn:hover { background: rgba(255,255,255,0.3); }

/* ── Body ── */
.pos-modal-body { padding: 20px 18px; background: #f8f9fc; }

/* ── Amount Cards ── */
.amount-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 18px;
}
.amount-card {
  border-radius: 10px;
  padding: 14px 12px;
  text-align: center;
}
.total-card  { background: linear-gradient(135deg, #0f3460, #1a1a2e); color: #fff; }
.balance-card { background: linear-gradient(135deg, #155724, #1e7e34); color: #fff; }
.amount-label {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  opacity: 0.85;
  margin-bottom: 6px;
}
.amount-value {
  font-size: 20px;
  font-weight: 800;
  letter-spacing: 0.3px;
}

/* ── Fields ── */
.field-group { margin-bottom: 16px; }
.field-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #555;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 7px;
}
.input-wrap {
  display: flex;
  align-items: center;
  border: 1.5px solid #dde2ec;
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
  transition: border-color 0.2s;
}
.input-wrap:focus-within { border-color: #0f3460; box-shadow: 0 0 0 3px rgba(15,52,96,0.1); }
.input-prefix {
  padding: 0 12px;
  font-size: 13px;
  font-weight: 700;
  color: #888;
  background: #f0f2f5;
  border-right: 1.5px solid #dde2ec;
  height: 42px;
  display: flex; align-items: center;
}
.field-input {
  border: none;
  outline: none;
  width: 100%;
  padding: 10px 12px;
  font-size: 15px;
  font-weight: 600;
  color: #222;
  background: #fff;
}
.field-input::placeholder { color: #bbb; font-weight: 400; }

/* ── Payment Method Pills ── */
.method-options {
  display: flex;
  gap: 10px;
}
.method-option {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
  padding: 10px 6px;
  border: 1.5px solid #dde2ec;
  border-radius: 10px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  color: #666;
  background: #fff;
  transition: all 0.2s ease;
  text-align: center;
}
.method-option .fa { font-size: 18px; color: #aaa; transition: color 0.2s; }
.method-option:hover { border-color: #0f3460; color: #0f3460; }
.method-option:hover .fa { color: #0f3460; }
.method-option.active {
  border-color: #0f3460;
  background: #eef3fb;
  color: #0f3460;
}
.method-option.active .fa { color: #0f3460; }

/* ── Status Message ── */
.pay-status {
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 14px;
  text-align: center;
}
.pay-status.error   { background: #fde8e8; color: #c0392b; border: 1px solid #f5c6cb; }
.pay-status.success { background: #e6f9ee; color: #1e7e34; border: 1px solid #b8dfc7; }

/* ── Confirm Button ── */
.btn-confirm {
  width: 100%;
  padding: 13px;
  background: linear-gradient(135deg, #1e7e34, #28a745);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  letter-spacing: 0.3px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(40,167,69,0.35);
}
.btn-confirm:hover {
  background: linear-gradient(135deg, #155724, #1e7e34);
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(40,167,69,0.45);
}
.btn-confirm:disabled {
  background: #ccc;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}
</style>

<script>
  const paymentBtn     = document.getElementById('paymentBtn');
  const paymentModal   = document.getElementById('paymentModal');
  const modalOverlay   = document.getElementById('modalOverlay');
  const totalAmountInput   = document.getElementById('totalAmount');
  const paidAmountInput    = document.getElementById('paidAmount');
  const balanceAmountInput = document.getElementById('balanceAmount');
  const totalDisplay   = document.getElementById('totalDisplay');
  const balanceDisplay = document.getElementById('balanceDisplay');
  const closePayment   = document.getElementById('closePayment');
  const confirmPayment = document.getElementById('confirmPayment');
  const payStatus      = document.getElementById('payStatus');

  // ── Open Modal ──
  paymentBtn.addEventListener('click', () => {
    let total = 0;
    document.querySelectorAll('#selected_products tbody tr').forEach(row => {
      let price = parseFloat(row.querySelector('input[name="price[]"]')?.value) || 0;
      let qty   = parseInt(row.querySelector('input[name="qty[]"]')?.value)   || 0;
      total += price * qty;
    });

    totalAmountInput.value   = total.toFixed(2);
    totalDisplay.textContent = 'Rs. ' + total.toFixed(2);
    balanceDisplay.textContent = 'Rs. 0.00';
    balanceAmountInput.value   = '';
    paidAmountInput.value      = '';
    payStatus.style.display    = 'none';
    paymentModal.style.display = 'block';
    modalOverlay.style.display = 'block';
    paidAmountInput.focus();
  });

  // ── Live Balance Update ──
  paidAmountInput.addEventListener('input', () => {
    const total   = parseFloat(totalAmountInput.value) || 0;
    const paid    = parseFloat(paidAmountInput.value)  || 0;
    const balance = paid - total;

    balanceAmountInput.value   = balance.toFixed(2);
    balanceDisplay.textContent = 'Rs. ' + balance.toFixed(2);

    // Turn balance card red if underpaid
    const balanceCard = document.querySelector('.balance-card');
    if (paid > 0 && balance < 0) {
      balanceCard.style.background = 'linear-gradient(135deg, #c0392b, #e74c3c)';
    } else {
      balanceCard.style.background = 'linear-gradient(135deg, #155724, #1e7e34)';
    }

    payStatus.style.display = 'none';
  });

  // ── Payment Method Selection ──
  document.querySelectorAll('.method-option').forEach(option => {
    option.addEventListener('click', () => {
      document.querySelectorAll('.method-option').forEach(o => o.classList.remove('active'));
      option.classList.add('active');
      option.querySelector('input[type="radio"]').checked = true;
    });
  });

  // ── Close Modal ──
  function closeModal() {
    paymentModal.style.display = 'none';
    modalOverlay.style.display = 'none';
  }
  closePayment.addEventListener('click', closeModal);
  modalOverlay.addEventListener('click', closeModal);

  // ── Confirm Payment ──
  confirmPayment.addEventListener('click', () => {
    const total  = parseFloat(totalAmountInput.value)  || 0;
    const paid   = parseFloat(paidAmountInput.value)   || 0;
    const balance = paid - total;
    const method = document.querySelector('input[name="payMethod"]:checked').value;

    if (!paid || paid <= 0) {
      payStatus.className     = 'pay-status error';
      payStatus.innerHTML     = '<i class="fa fa-exclamation-circle"></i> &nbsp;Please enter the paid amount.';
      payStatus.style.display = 'block';
      return;
    }

    if (paid < total) {
      payStatus.className     = 'pay-status error';
      payStatus.innerHTML     = '<i class="fa fa-exclamation-circle"></i> &nbsp;Paid amount is less than total!';
      payStatus.style.display = 'block';
      return;
    }

    payStatus.className     = 'pay-status success';
    payStatus.innerHTML     = `<i class="fa fa-check-circle"></i> &nbsp;Payment of <b>Rs. ${paid.toFixed(2)}</b> received via <b>${method}</b>. Change: <b>Rs. ${balance.toFixed(2)}</b>`;
    payStatus.style.display = 'block';
    confirmPayment.disabled = true;

    setTimeout(() => {
      closeModal();
      confirmPayment.disabled = false;
    }, 2500);
  });
</script>
<!-- ── Suspend Modal ── -->
<div id="suspendModal" class="suspend-overlay">
  <div class="suspend-box">

    <!-- Header -->
    <div class="suspend-header">
      <div class="suspend-title">
        <i class="fa fa-pause-circle"></i>
        <span>Suspend Sale</span>
      </div>
      <button class="suspend-close-btn" onclick="closeSuspendModal()">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <!-- Body -->
    <div class="suspend-body">

      <!-- Info Banner -->
      <div class="suspend-info-banner">
        <i class="fa fa-info-circle"></i>
        <span>This sale will be suspended and can be resumed later. Please enter a reference note to identify it.</span>
      </div>

      <!-- Note Field -->
      <div class="suspend-field">
        <label class="suspend-label">
          <i class="fa fa-pencil"></i> &nbsp;Reference Note
        </label>
        <textarea id="suspendNote"
                  class="suspend-textarea"
                  placeholder="e.g. Customer will return in 10 minutes..."
                  rows="3"></textarea>
        <div id="suspendError" class="suspend-error" style="display:none;">
          <i class="fa fa-exclamation-circle"></i> Please enter a reference note.
        </div>
      </div>

    </div>

    <!-- Footer -->
    <div class="suspend-footer">
      <button class="btn-suspend-cancel" onclick="closeSuspendModal()">
        <i class="fa fa-times"></i> &nbsp;Cancel
      </button>
      <button class="btn-suspend-submit" onclick="submitSuspend()">
        <i class="fa fa-pause-circle"></i> &nbsp;Suspend Sale
      </button>
    </div>

  </div>
</div>

<style>
/* ── Overlay ── */
.suspend-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(3px);
  z-index: 9999;
  justify-content: center;
  align-items: center;
}

/* ── Box ── */
.suspend-box {
  background: #fff;
  width: 420px;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
  animation: suspendPopIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

@keyframes suspendPopIn {
  from { transform: translate(-50%, -50%) scale(0.85); opacity: 0; }
  to   { transform: translate(-50%, -50%) scale(1);    opacity: 1; }
}

/* ── Header ── */
.suspend-header {
  background: linear-gradient(135deg, #1a1a2e, #0f3460);
  color: #fff;
  padding: 16px 18px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.suspend-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.3px;
}

.suspend-title .fa {
  font-size: 20px;
  opacity: 0.9;
}

.suspend-close-btn {
  background: rgba(255, 255, 255, 0.15);
  border: none;
  color: #fff;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.suspend-close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* ── Body ── */
.suspend-body {
  padding: 20px 18px;
  background: #f8f9fc;
}

/* ── Info Banner ── */
.suspend-info-banner {
  background: #eef3fb;
  border-left: 4px solid #0f3460;
  border-radius: 6px;
  padding: 12px 14px;
  font-size: 13px;
  color: #444;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 18px;
  line-height: 1.5;
}

.suspend-info-banner .fa {
  color: #0f3460;
  font-size: 16px;
  margin-top: 1px;
  flex-shrink: 0;
}

/* ── Field ── */
.suspend-field { margin-bottom: 6px; }

.suspend-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #555;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.suspend-textarea {
  width: 100%;
  padding: 11px 13px;
  font-size: 14px;
  color: #333;
  border: 1.5px solid #dde2ec;
  border-radius: 8px;
  background: #fff;
  resize: none;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: inherit;
  box-sizing: border-box;
}

.suspend-textarea:focus {
  border-color: #0f3460;
  box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
}

.suspend-textarea::placeholder {
  color: #bbb;
  font-style: italic;
}

/* ── Error ── */
.suspend-error {
  background: #fde8e8;
  color: #c0392b;
  border: 1px solid #f5c6cb;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 13px;
  font-weight: 600;
  margin-top: 8px;
}

/* ── Footer ── */
.suspend-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 14px 18px;
  background: #fff;
  border-top: 1px solid #eee;
}

.btn-suspend-cancel {
  padding: 10px 20px;
  background: #f0f2f5;
  color: #555;
  border: 1.5px solid #dde2ec;
  border-radius: 7px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-suspend-cancel:hover {
  background: #e2e6ea;
  color: #333;
}

.btn-suspend-submit {
  padding: 10px 22px;
  background: linear-gradient(135deg, #0f3460, #1a1a2e);
  color: #fff;
  border: none;
  border-radius: 7px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  letter-spacing: 0.3px;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(15, 52, 96, 0.3);
}

.btn-suspend-submit:hover {
  background: linear-gradient(135deg, #1a1a2e, #0f3460);
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(15, 52, 96, 0.4);
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // ── Open ──
  function openSuspendModal() {
    document.getElementById('suspendModal').style.display = 'flex';
    document.getElementById('suspendNote').focus();
  }

  // ── Close ──
  function closeSuspendModal() {
    document.getElementById('suspendModal').style.display = 'none';
    document.getElementById('suspendNote').value = '';
    document.getElementById('suspendError').style.display = 'none';
  }

  // ── Submit ──
  function submitSuspend() {
    const note = document.getElementById('suspendNote').value.trim();
    const errorBox = document.getElementById('suspendError');

    if (note === '') {
      errorBox.style.display = 'block';
      document.getElementById('suspendNote').focus();
      return;
    }

    errorBox.style.display = 'none';
    closeSuspendModal();

    Swal.fire({
      icon: 'success',
      title: 'Sale Suspended',
      html: `Reference note saved:<br><b style="color:#0f3460;">"${note}"</b>`,
      confirmButtonText: 'OK',
      confirmButtonColor: '#0f3460',
      borderRadius: '14px',
      timer: 3000,
      timerProgressBar: true,
    });
  }

  // ── Close on overlay click ──
  document.getElementById('suspendModal').addEventListener('click', function (e) {
    if (e.target === this) closeSuspendModal();
  });

  // ── Cancel Sale ──
  function confirmCancelAction() {
    Swal.fire({
      title: 'Cancel Sale?',
      text: 'This will clear the current sale. This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="fa fa-trash"></i> Yes, Cancel Sale',
      cancelButtonText: '<i class="fa fa-arrow-left"></i> Go Back',
      confirmButtonColor: '#c0392b',
      cancelButtonColor: '#6c757d',
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          icon: 'info',
          title: 'Sale Cancelled',
          text: 'The current sale has been cleared.',
          confirmButtonColor: '#0f3460',
          timer: 2000,
          timerProgressBar: true,
        }).then(() => {
          location.reload();
        });
      }
    });
  }
</script>
<script>
$(document).ready(function () {
  let orderTaxPercent = 0;
  let discountAmount = 0;

  // Search product
  $('#search_product').on('input', function () {
    let query = $(this).val();
    if (query.length > 1) {
      $.ajax({
        url: 'search_product.php',
        method: 'POST',
        data: { query: query },
        success: function (data) {
          $('#product_results').html(data).show();
        }
      });
    } else {
      $('#product_results').hide();
    }
  });

  $(document).on('click', '.search-item', function () {
    let id = $(this).data('id');
    let name = $(this).data('name');
    let price = parseFloat($(this).data('price')) || 0;
    addProductRow(id, name, price);
    $('#product_results').hide();
    $('#search_product').val('');
  });

  function addProductRow(id, name, price) {
    let exists = false;
    $('#selected_products tbody tr').each(function () {
      let pid = $(this).find('input[name="product_id[]"]').val();
      if (pid == id) { exists = true; }
    });
    if (exists) return;

    let row = `<tr>
      <td>${name}<input type="hidden" name="product_id[]" value="${id}"></td>
      <td><span class="price">${price.toFixed(2)}</span><input type="hidden" name="price[]" value="${price.toFixed(2)}"></td>
      <td><input type="number" name="qty[]" value="1" min="1" class="form-control qty" style="width:80px; text-align:center;"></td>
      <td class="subtotal">${price.toFixed(2)}</td>
      <td><button type="button" class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></button></td>
    </tr>`;
    $('#selected_products tbody').append(row);
    updateTotalsAndSummary();
  }

  $(document).on('input', '.qty', function () {
    let row = $(this).closest('tr');
    let price = parseFloat(row.find('input[name="price[]"]').val()) || 0;
    let qty = parseFloat($(this).val()) || 0;
    let subtotal = price * qty;
    row.find('.subtotal').text(subtotal.toFixed(2));
    updateTotalsAndSummary();
  });

  $(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();
    updateTotalsAndSummary();
  });

  function updateTotalsAndSummary() {
    let total = 0;
    let itemCount = 0;
    let qtyCount = 0;

    $('#selected_products tbody tr').each(function () {
      let subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
      let qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
      total += subtotal;
      qtyCount += qty;
      itemCount++;
    });

    let taxAmount = total * (orderTaxPercent / 100);
    let finalTotal = total + taxAmount - discountAmount;
    if (finalTotal < 0) finalTotal = 0;

    $('#total_amount').val(finalTotal.toFixed(2));
    $('#summary_items').text(itemCount + ' (' + qtyCount + ')');
    $('#summary_total').text('₨ ' + total.toFixed(2));
    $('#summary_tax_display').text('₨ ' + taxAmount.toFixed(2));
    $('#summary_discount_display').text('₨ ' + discountAmount.toFixed(2));
    $('#total_payable').text(finalTotal.toFixed(2));
  }

  $('#edit_tax').on('click', function (e) {
    e.stopPropagation();
    $('#tax_form').toggle();
    $('#order_tax').val(orderTaxPercent);
  });
  $('#save_tax').on('click', function (e) {
    e.stopPropagation();
    orderTaxPercent = parseFloat($('#order_tax').val()) || 0;
    $('#tax_form').hide();
    updateTotalsAndSummary();
  });

  $('#edit_discount').on('click', function (e) {
    e.stopPropagation();
    $('#discount_form').toggle();
    $('#discount_input').val(discountAmount);
  });
  $('#save_discount').on('click', function (e) {
    e.stopPropagation();
    discountAmount = parseFloat($('#discount_input').val()) || 0;
    $('#discount_form').hide();
    updateTotalsAndSummary();
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#tax_form, #edit_tax').length) $('#tax_form').hide();
    if (!$(e.target).closest('#discount_form, #edit_discount').length) $('#discount_form').hide();
  });

  updateTotalsAndSummary();
});


</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- Bootstrap JS (required for modals to work) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

    // Search customers as you type
    $('#walkin_customer_search').keyup(function(){
        let query = $(this).val();
        if(query != ''){
            $.ajax({
                url: "search_customer.php",
                method: "POST",
                data: {query: query},
                success: function(data){
                    $('#customer_suggestions').show();
                    $('#customer_suggestions').html(data);
                }
            });
        } else {
            $('#customer_suggestions').hide();
        }
    });

    // Select a customer from suggestions
    $(document).on('click', '#customer_suggestions li', function(){
        let name = $(this).text();
        let id = $(this).data('id');
        $('#walkin_customer_search').val(name);
        $('#customer_id').val(id);
        $('#customer_suggestions').hide();
    });

    // Edit button - fetch and show customer details
    $('#editCustomerBtn').click(function(){
        let id = $('#customer_id').val();
        if(id){
            $.ajax({
                url: "fetch_customer_details.php",
                method: "POST",
                data: {customer_id: id},
                success: function(data){
                    $('#customer_details').html(data);
                    $('#customerModal').modal('show');
                }
            });
        } else {
            alert('Please select a customer first.');
        }
    });

});
</script>

<script>
$(document).ready(function() {
  // When a customer is clicked from search results
  $(document).on('click', '.customer-item', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    const name = $(this).data('name');

    $('#customer_id').val(id);
    $('#walkin_customer_search').val(name);
    $('#customer_suggestions').hide();
  });

  // When View Customer button clicked (Eye button)
  $('#viewCustomerBtn').on('click', function() {
    const customerId = $('#customer_id').val();
    if (!customerId || customerId === '0') {
      alert('Please select a customer first!');
      return;
    }

    $.ajax({
      url: 'get_customer_details.php',
      method: 'GET',
      data: { id: customerId },
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          $('#c_name').text(res.name || 'N/A');
          $('#c_email').text(res.email || 'N/A');
          $('#c_phone').text(res.phone || 'N/A');
          $('#c_address').text(res.address || 'N/A');
          $('#customerModal').modal('show');
        } else {
          alert('Customer not found');
        }
      },
      error: function() {
        alert('Error fetching customer details.');
      }
    });
  });

  // Print button (keeps your existing functionality)
  $('#printCustomer').click(function() {
    const printContents = document.getElementById('customerInfo').innerHTML;
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Customer Info</title>');
    printWindow.document.write('<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h3 style="text-align:center;">Customer Information</h3>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  });
});
</script>

</body>
</html>
