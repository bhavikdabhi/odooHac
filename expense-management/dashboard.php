<?php
if(!isset($_SESSION)) session_start();

include 'includes/db.php';
include 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch currencies from REST Countries API
$currencyJsonUrl = "https://restcountries.com/v3.1/all?fields=name,currencies";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $currencyJsonUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// Prepare unique currency list for select menu
$currenciesList = [];
foreach ($data as $country) {
    if (isset($country['currencies']) && is_array($country['currencies'])) {
        foreach ($country['currencies'] as $code => $currency) {
            if (!isset($currenciesList[$code])) {
                $currenciesList[$code] = $currency['name'] ?? $code;
            }
        }
    }
}
?>

<div class="container mt-4">
    <h2>Welcome, <?= $_SESSION['name'] ?> (<?= $role ?>)</h2>

    <?php if($role=='Employee'): ?>
    <!-- Employee Expense Form -->
    <div class="card mt-3 p-3">
        <h5>Submit Expense</h5>
        <form id="expenseForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <input type="number" name="amount" class="form-control" placeholder="Amount" required>
                </div>
                <div class="col-md-3 mb-2">
                    <select name="currency" class="form-control" required>
                        <option value="">Select Currency</option>
                        <?php
                        foreach($currenciesList as $code => $name){
                            echo "<option value='{$code}'>{$name} ({$code})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" name="category" class="form-control" placeholder="Category" required>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-12 mb-2">
                    <input type="text" name="description" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-12 mb-2">
                    <input type="file" name="receipt" class="form-control">
                </div>
                <div class="col-md-12">
                    <button class="btn btn-success">Submit Expense</button>
                </div>
            </div>
        </form>
        <div id="msg"></div>
    </div>

    <!-- Employee Expenses Table -->
    <div class="card mt-3 p-3">
        <h5>Your Expenses</h5>
        <div id="expenseTable"></div>
    </div>

    <script>
        function loadExpenses(){
            $.get('ajax/fetch_expenses.php', function(data){
                $('#expenseTable').html(data);
            });
        }

        $('#expenseForm').submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url:'ajax/submit_expense.php',
                type:'POST',
                data: formData,
                contentType:false,
                processData:false,
                dataType:'json',
                success:function(res){
                    $('#msg').html('<div class="alert alert-'+(res.status=='success'?'success':'danger')+'">'+res.msg+'</div>');
                    if(res.status=='success') $('#expenseForm')[0].reset();
                    loadExpenses();
                }
            });
        });

        loadExpenses();
    </script>

    <?php elseif($role=='Manager' || $role=='Admin'): ?>

    <!-- Manager/Admin Pending Approvals -->
    <div class="card mt-3 p-3">
        <h5>Pending Approvals</h5>
        <div id="approvalTable"></div>
    </div>

    <script>
        function loadApprovals(){
            $.get('ajax/fetch_expenses.php?approvals=1', function(data){
                $('#approvalTable').html(data);
            });
        }
        loadApprovals();
    </script>

    <!-- Manager/Admin Currency Table -->
   

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
