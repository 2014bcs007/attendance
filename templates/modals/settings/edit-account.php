<?php
$id = $_GET['id'];
$category = $_GET['category'];
$account = DB::getInstance()->getRow("account", $id, "*", "Id");
?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo !$id ? "New $category account" : "Edit $account->Name"; ?></h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo $account->Name; ?>" class="form-control" required>

        <label>Code</label>
        <input type="text" name="code" class="form-control" value="<?php echo $account->Code; ?>" required>

        <label>Category:</label>
        <select name="category" class="form-control" required onChange="toggleAccountType(this)">
            <option value="">Choose..</option>
            <?php
            foreach ($ACCOUNT_CATEGORIES as $ACCOUNT) {
                $selected = $ACCOUNT == $category ? ' selected' : '';
                echo "<option value='$ACCOUNT' $selected>$ACCOUNT</option>";
            }
            ?>
        </select>
        <div id="accountType" class="<?php echo $category == "equity" ? '' : 'hidden' ?>">
            <label>Type</label><select class="form-control" name="account_type">
                <option value=""></option>
                <option value="credit" <?php echo $account->Account_Type=="credit"?"selected":""?>>Credit</option>
                <option value="debit" <?php echo $account->Account_Type=="debit"?"selected":""?>>Debit</option>
            </select>
        </div>
    </div>

    <input type="hidden" name="action" value="editAccount">

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-primary">Save</button>
</div>

<script>
    function toggleAccountType(elm) {
        $("#accountType").attr({
            'class': elm.value == "capital" || elm.value == "equity" ? `` : "hidden"
        });
    }
</script>