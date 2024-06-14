<?php
$id = $_GET['id'];
$account = DB::getInstance()->getRow("bank_account", $id, "*", "Id");
?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo !$id ? "New bank account" : "Edit $account->Name"; ?></h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Account Name</label>
        <input type="text" name="name" value="<?php echo $account->Name; ?>" class="form-control" required>

        <label>Account Number</label>
        <input type="text" name="account_number" class="form-control" value="<?php echo $account->Account_Number; ?>" required>
        <label>Bank Name</label>
        <input type="text" name="bank" class="form-control" value="<?php echo $account->Bank; ?>" required>
    </div>

    <input type="hidden" name="action" value="editBankAccount">

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-primary">Save</button>
</div>