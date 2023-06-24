<?php
include "login_header.php";
$store = (isset($_GET['store']) && $_GET['store'] != '') ? $_GET['store'] : "managedashboard.myshopify.com";
?>
   <body>   
        <div class="Polaris-Page login-frm" style="max-width: 49.8rem;">
            <div class="Polaris-Page__Content">
                <div class="Polaris-Layout">
                    <div class="Polaris-Layout__AnnotatedSection">
                        <div class="Polaris-Layout__AnnotationWrapper">
                            <div class="Polaris-Layout__AnnotationContent">
                                <div class="Polaris-Card">
                                    <div class="Polaris-Card__Section">
                                        <div class="Polaris-Layout">
                                            <div class="Polaris-Layout__Section">
                                                <div class="Polaris-Card__Header">                                                 
                                                    <h2 class="Polaris-Heading text-center">Welcome to <?php  echo CLS_SITE_NAME; ?></h2>
                                                </div> 
                                                <?php if ($ologin->errors) {
                                                    echo '<div class="alert alert-danger fade in">';
                                                    foreach ($ologin->errors as $error) {
                                                        echo $error . '<br>';
                                                    }
                                                    echo '</div>';
                                                }
                                                ?>
                                                <div class="Polaris-Card__Section">
                                                    <span class="error"></span>
                                                    <form class="m-t" role="form" action="" method="post">
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                                            </div>
                                                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required="">
                                                        </div>
                                                        <div class="form-group input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text password"> <i class="fa fa-lock"></i> </span>
                                                            </div>
                                                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required="">
                                                        </div>
                                                        <a href="password-reset.php?store=<?php echo $store; ?>"><small style="font-size: 100% ! important;">Forgot password?</small></a>
                                                        <button type="submit" name="login" id="submitLogin"  class="Polaris-Button Polaris-Button--primary" style="float: right;">Login</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    </body>
    <?php include_once('cls_footer.php'); ?>
