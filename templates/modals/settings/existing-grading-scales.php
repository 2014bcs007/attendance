<?php
$years = DB::getInstance()->querySample("SELECT * FROM grading GROUP BY Year ORDER BY Year DESC");
?>
<div class="modal-header">
    <h5 class="modal-title">Existing grading scale</h5>
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
                        <?php echo "$year->Year"; ?>
                    </button>
                </h2>
                <div id="accor_lefticonExamplecollapse3<?php echo $i ?>" class="accordion-collapse collapse" aria-labelledby="accordionItem<?php echo $i ?>" data-parent="#accordion-grading-scale">
                    <div class="accordion-body row">
                        <form method="POST">
                            <input type="hidden" name="action" value="cloneGradingScale">
                            <input type="hidden" name="year" value="<?php echo $year->Year?>">

                            <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
                            <button class="btn btn-xs btn-primary">Copy <?php echo $year->Year?> grading scale to <?php echo date('Y')?></button>
                        </form>
                        <?php
                        $classes = DB::getInstance()->querySample("SELECT c.Class_Name, g.Class_Id FROM grading g, class c WHERE c.Class_Id=g.Class_Id AND g.Year='$year->Year' GROUP BY c.Class_Id ORDER BY c.Class_Name");
                        foreach ($classes as $class) { ?>
                            <div class="col-md-4">
                            <h5><?php echo $class->Class_Name ?></h5>
                            <small><table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Range</th>
                                        <th>Grade</th>
                                        <th>Descriptor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $list = DB::getInstance()->querySample("SELECT * FROM grading WHERE Class_Id='$class->Class_Id' AND Year='$year->Year'");
                                    foreach ($list as $data) {
                                    ?>
                                        <tr>
                                            <td>
                                                <span class=""><?php echo $data->Initial_Marks; ?></span> - <span><?php echo $data->Final_Marks - 0.99; ?></span>
                                            </td>
                                            <td><?php echo $data->Score; ?></td>
                                            <td><?php echo $data->Descriptor; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table></small>
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