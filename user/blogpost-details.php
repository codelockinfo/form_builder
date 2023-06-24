<?php 
    include "cls_header.php"; 
        include_once('dashboard_header.php');   
    $blogpost_id = isset($_GET['blogpost_id']) ? $_GET['blogpost_id'] : ''; 
?>
    </style>
    <div class="Polaris-Page login-frm">
        <div class="Polaris-Page__Content">
            <div class="Polaris-Layout">
                <div class="Polaris-Layout__AnnotatedSection">
                    <div class="Polaris-Layout__AnnotationWrapper">
                        <div class="Polaris-Layout__AnnotationContent">
                            <div class="Polaris-Card">
                                <div class="Polaris-Card__Section">
                                    <nav role="navigation" class="product-detail-view">
                                        <a href="blog_post.php?store=<?php echo $_SESSION['store'];?>" class="Polaris-Breadcrumbs__Breadcrumb" data-polaris-unstyled="true">
                                            <span class="Polaris-Breadcrumbs__Icon">
                                                <span class="Polaris-Icon">
                                                    <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                                    <path d="M12 16a.997.997 0 0 1-.707-.293l-5-5a.999.999 0 0 1 0-1.414l5-5a.999.999 0 1 1 1.414 1.414L8.414 10l4.293 4.293A.999.999 0 0 1 12 16" fill-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                            <span class="Polaris-Breadcrumbs__Content">Back</span>
                                        </a>
                                    </nav>
                                     <div>
                                           <h2 class="Polaris-Heading" style="margin-left: 845px; margin-top: -25px;">Blog</h2>
                                       </div>
                                     <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text">Title</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected"><div class="Polaris-Connected__Item_yiyol Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input id="title" readonly name="title" placeholder="" class="Polaris-TextField__Input title" maxlength="255" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="">
                                                        <div class="Polaris-TextField__Backdrop">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </br>
                                      <div class="block2">
                                    <div class="Polaris-Card__Header">
                                            <h2 class="Polaris-Heading">Images</h2>
                                        </div>
                                                <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary" style="width: 50%;margin-top: -2px;">
                                                        <div class="Polaris-Card__Section ">
                                                            <div class="Polaris-DropZone Polaris-DropZone--hasOutline Polaris-DropZone--sizeExtraLarge" >
                                                                <div class="Polaris-DropZone__Container ">
                                                                            <img id="ImagePreview" class="imagepre" src="" alt="your image" style="height:100%;width:100%;" />
                                                                    </div>
                                                            </div>
                                            </div>
                                        </div>
                                </div>
                                    <div class="Polaris-Layout">
                                        <div class="Polaris-Layout__Section">
                                           <form class="m-t" id="register_frm" name="register_frm" method="POST"  enctype="multipart/form-data" onsubmit="">
                                                    <input type="hidden" id="" name="blogpost_id" value="<?php echo $blogpost_id; ?>">
                                                    <input type="hidden" id="" name="for_data" value="<?php echo 'blogpost'; ?>">
                                                    <textarea class="textdetails" name="description" value="">                                          
                                                    </textarea>
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
</body>
</html>
<script>
    var routine_name = 'blogpost_select';
    var blogpost_id = "<?php echo $blogpost_id; ?>";
    get_textarea_value(routine_name,store,blogpost_id,"blog");
</script>
    <?php  include_once('dashboard_footer.php'); ?>