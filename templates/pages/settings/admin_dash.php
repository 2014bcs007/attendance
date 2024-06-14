<!DOCTYPE html>
<html lang="en">
    <head>
    <?php require_once'includes/adminheader.php'?>

    </head>
    <body>
        <div id="wrapper">
            <?php include("includes/adminlink.php"); ?>
            <div id="page-wrapper" style="min-height:750px">
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                            if (Input::exists() && Input::get("backup") == "backup") {
                                $user = Input::get("user");
                                DB::getInstance()->backup_database("*");
                                DB::getInstance()->logs($user . " backed up the database");
                                Redirect::go_to("index.php?page=" . $crypt->encode("dashboard"));
                            }
                            if (isset($_GET["action"]) && $_GET["action"] == "delete_file") {
                                $file_name = $_GET["file_name"];
                                unlink("backup_files/" . $file_name);
                                Redirect::to("index.php?page=" . $crypt->encode("dashboard"));
                            }
                            ?>
                            <h1 class="page-header">
                                Adminstrator Dashboard <small>Statistics Overview</small>
                            </h1>
                            <ol class="breadcrumb">
                                <li class="active">
                                    <i class="fa fa-dashboard"></i> Dashboard
                                </li>
                            </ol>
                        </div>
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-gear fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>Classes and Streams</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-wrench fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"></div>
                                            <div>Access Previleges</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <span class="pull-left">Activate</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a data-toggle='modal' href='#modal-form-modules'>
                                <div class="panel panel-yellow">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-database fa-5x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"><?php echo $str; ?></div>
                                                <div>Modules</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <span class="pull-left">Activate now</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="javascript:;">
                                <div class="panel panel-red">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <i class="fa fa-key fa-5x"></i>
                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <div class="huge"><?php echo $trs; ?></div>
                                                <div> PRO settings</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <span class="pull-left">Activate</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div id="modal-form-modules" class="modal fade" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <form method="POST" action=""> 
                                    <div class="modal-body">
                                        <div class="row"> 
                                            <div class="col-sm-12">
                                                <h3 class="m-t-none m-b">Modules paid for</h3>
                                                <div class="form-group">
                                                    <select name="modules[]" class="form-control" multiple required>
                                                        <?php
                                                        echo '<option value="Academics">Academics</option>';
                                                        ?>
                                                    </select>
                                                </div>
                                                <label><input type="radio" name="modules[]" value="Fees Balance">Fees Balance</label>
                                                <label><input type="radio" name="modules[]" value="Finance">Finance</label>
                                                <div>
                                                    <a class="btn btn-sm btn-warning pull-right"><strong>Cancel</strong></a>
                                                    <button class="btn btn-sm btn-primary pull-left" name="edit_expense" value="edit_expense" type="submit"><strong><i class="fa fa-edit "></i> Update</strong></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    System backups
                                </div>
                                <div class="panel-body">
                                    <form role='form' action='' method='post'>
                                        <button type="submit" name="backup" value="backup" class="btn btn-success">BACK UP NOW</button>
                                        <input type='hidden' name='user' value='<?php echo $_SESSION['fname'] . '   ' . $_SESSION['lname']; ?>'>
                                    </form>
                                    <table class="table table-bordered">
                                        <?php
                                        if ($handle = opendir('backup_files/')) {
                                            $i = 1;
                                            while (false !== ($entry = readdir($handle))) {
                                                if ($entry != "." && $entry != "..") {
                                                    $total_files = count(scandir("backup_files")) - 2;
                                                    $delete_hidden = ($i == $total_files || $total_files == 1) ? " hidden" : "";
                                                    ?>
                                                    <tr>
                                                        <td  style='padding:5px;'><?php echo $entry ?> </td>
                                                        <td><a style='margin:4px;' class='label label-primary' href='backup_files/<?php echo $entry ?>'><i class='fa fa-download'></i> DOWNLOAD</a></td>
                                                        <td><a style='margin:4px;' class='label label-danger <?php echo $delete_hidden ?>' href='index.php?page=<?php echo $crypt->encode("dashboard") . "&action=delete_file&file_name=" . $entry ?>' onclick="return confirm('Do you really want to delete this backup file?')"><i class='fa fa-times-circle'></i> DELETE</a></td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                            }
                                            closedir($handle);
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 col-xs-12">                     
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Activation Settings
                                </div>
                                <div class="panel-body">
                                    <div id="txttt"></div>

                                </div>
                            </div>            
                        </div>
                        <?php
                        include("pages/users/system_logs.php");
                        ?>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->
        </div>
        <!-- /#wrapper -->
        <?php require_once'includes/footer.php'?>
    </body>
</html>
