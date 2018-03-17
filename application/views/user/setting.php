  <br><br>
    <div class="container">
        <h1 class="judul">Setting</h1>
      <div class="row">
        <div class="col-md-3">
            <div class="user-dashboard-sidebar-menu">
              <ul class="list-group">
                  <li class="list-group-item active shadow">
                    <a class="link_href" href="#">Change Password</a>
                  </li>
              </ul>
            </div>
        </div>
         <div class="col-md-9">
          <div class="dashboard-content-item">
            <h4 class="dashboard-title">Change Password</h4>
              <hr>

              <form action="<?php echo base_url('user/proses_new_password') ?>" method="post">

                <?php if($this->session->flashdata('msg_berhasil')){ ?>
                  <div class="alert alert-success alert-dismissible">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Success</strong><br> <?php echo $this->session->flashdata('msg_berhasil');?>
                 </div>
                <?php }else if(validation_errors()) { ?>
                  <div class="alert alert-warning alert-dismissible">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Warning!</strong><br> <?php echo validation_errors(); ?>
                 </div>
              <?php  } ?>

                <label for="username" class="text">Username</label>
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                    </span>
                    <input type="username" name="username" class="form-control" disabled="on" value="<?= $this->session->userdata('name')?>">
                </div><br>

                <label for="password" class="text">New Password</label>
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa fa-key" aria-hidden="true"></i>
                    </span>
                    <input type="password" name="new_password"  class="form-control" required="">
                </div><br>

                <label for="confirm_password" class="text">Confirm new password</label>
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa fa-key" aria-hidden="true"></i>
                    </span>
                    <input type="password" name="confirm_new_password"  class="form-control" required="">
                </div><br>

                <?php if(isset($token_generate)){ ?>
                  <input type="hidden" name="token"  class="form-control" value="<?= $token_generate?>">
                <?php }else {
                  redirect(base_url('user/setting'));
                }?>

                <div class="form-group">
                  <button type="submit" name="submit" class="form-control btn btn-primary"> <i class="fa fa-send" aria-hidden="true"></i> Update Password</button>
                </div>
              </form>
           </div>
         </div>
       </div>
         <!-- end page content -->
      </div>
    <!-- </div> -->
