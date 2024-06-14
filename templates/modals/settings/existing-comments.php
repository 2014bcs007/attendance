<?php
$table = $_GET['table'];
$years = DB::getInstance()->querySample("SELECT * FROM $table GROUP BY Year,Term ORDER BY Year DESC");
?>
<div class="modal-header">
    <h5 class="modal-title">Existing <?php echo str_replace('_', ' ', $table) ?>s</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="accordion lefticon-accordion custom-accordionwithicon accordion-border-box" id="accordion-grading-scale">
        <?php
        foreach ($years as $i => $year) {
        ?>
            <div class="accordion-item mt-2">
                <h2 class="accordion-header" id="accordionItem<?php echo $i ?>">
                    <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#accor_lefticonExamplecollapse3<?php echo $i ?>" aria-expanded="false" aria-controls="accor_lefticonExamplecollapse3<?php echo $i ?>">
                        <?php echo "$year->Term $year->Year"; ?>
                    </button>
                </h2>
                <div id="accor_lefticonExamplecollapse3<?php echo $i ?>" class="accordion-collapse collapse" aria-labelledby="accordionItem<?php echo $i ?>" data-parent="#accordion-grading-scale">
                    <div class="accordion-body row">
                        <form method="POST" class="form-horizontal form-inline">
                            <label>Year</label>
                            <select name="year" class="form-control" required>
                                <option value="">Choose...</option>
                                <?php
                                for ($x = $INITIAL_YEAR; $x <= $MAX_YEAR; $x++) {
                                    echo '<option value="' . $x . '">' . $x . '</option>';
                                }
                                ?>
                            </select>
                            <label>Term</label>
                            <select class="form-control" name="term" required>
                                <option value="">Choose ...</option>
                                <?php foreach ($TERMS_ARRAY as $term) {
                                    echo "<option value='$term'>$term</option>";
                                } ?>
                            </select>
                            <input type="hidden" name="action" value="cloneComments">
                            <input type="hidden" name="year_from" value="<?php echo $year->Year ?>">
                            <input type="hidden" name="term_from" value="<?php echo $year->Term ?>">
                            <input type="hidden" name="table" value="<?php echo $table ?>">

                            <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
                            <button class="btn btn-xs btn-primary">Copy <?php echo "$year->Term $year->Year" ?> comments</button>
                        </form>
                        <?php
                        $classes = DB::getInstance()->querySample("SELECT c.Class_Name, g.Class_Id FROM $table g, class c WHERE c.Class_Id=g.Class_Id AND g.Year='$year->Year' AND Term='$year->Term' GROUP BY c.Class_Id ORDER BY c.Class_Name");
                        foreach ($classes as $class) { ?>
                            <div class="col-md-4">
                                <h5><?php echo $class->Class_Name ?></h5>
                                <small>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Range</th>
                                                <th>Comment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $list = DB::getInstance()->querySample("SELECT * FROM $table WHERE Class_Id='$class->Class_Id' AND Year='$year->Year' AND Term='$year->Term'");
                                            foreach ($list as $data) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <span class=""><?php echo $data->Initial; ?></span> - <span><?php echo $data->Final; ?></span>
                                                    </td>
                                                    <td><?php echo $data->Comment; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </small>
                            </div>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>

        <?php
        }
        ?>
    </div>


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
</div>