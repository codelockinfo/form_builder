<?php    include "cls_header.php"; 
include "dashboard_header.php";

?>
 <style>
   .mce-notification{
   
       display:none;
   }
   .message{
       color: #FF0000;
   }
   
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
                                            <a href="blog_post.php?shop=<?php echo $_SESSION['store'];?>" class="Polaris-Breadcrumbs__Breadcrumb" data-polaris-unstyled="true">
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
                                           <h2 class="Polaris-Heading" style="    margin-left: 830px; margin-top: -25px;">Article</h2>
                                       </div>
                                    <form class="m-t" id="addblog_frm" name="register_frm" method="POST"  enctype="multipart/form-data" onsubmit="">
                                          

                                        <div>
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label"><label id="PolarisSelect10Label" for="PolarisSelect10" class="Polaris-Label__Text">Select Blog</label></div>
                                                </div>
                                                <div class="Polaris-Select"><select id="select10" class="Polaris-Select__Input" aria-invalid="false" name="blog">
                                                       <?php
                                                        $api = array('api_name' => 'blogs');
                                                        $blogs = $functions->cls_get_shopify_list($api, array(), 'GET');
                                                        $get_blogs = $blogs->blogs;
                                                        foreach ($get_blogs as $key => $value) {
                                                            echo '<option value="' . $value->id . '">' . $value->title . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="Polaris-Select__Content" aria-hidden="true">
                                                        <span class="Polaris-Select__SelectedOption"></span>
                                                        <span class="Polaris-Select__Icon">
                                                            <span class="Polaris-Icon">
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                                </svg>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <span class="message blogselect"></span> 
                                                    <div class="Polaris-Select__Backdrop"></div>
                                                </div>
                                            </div>
                                            <div id="PolarisPortalsContainer"></div>
                                        </div>
                                        
                                        </br>
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField11Label" for="PolarisTextField11" class="Polaris-Label__Text ">Title</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected"><div class="Polaris-Connected__Item_yiyol Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input id="title" name="title" placeholder="e.g. Blog about your latest products or deals" class="Polaris-TextField__Input" maxlength="255" aria-labelledby="PolarisTextField11Label" aria-invalid="false" value="">
                                                        <div class="Polaris-TextField__Backdrop">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <span class="message title"></span> 
                                        </div>
                                        <div class="Polaris-Card__Header">
                                            <h2 class="Polaris-Heading">Images</h2>
                                        </div>
                                        <div class="Polaris-Card__Section">
                                            <div class="Polaris-Layout maincontainer">
                                                <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary" style="width: 50%;">
                                                    <div class="Polaris-DropZone Polaris-DropZone--hasOutline Polaris-DropZone--sizeExtraLarge" >
                                                        <div class="Polaris-DropZone__Container">
                                                            <div class="Polaris-DropZone-FileUpload" id="drop-zone">
                                                                <div class="Polaris-Stack Polaris-Stack--vertical">
                                                                    <div class="Polaris-Stack__Item" id="image_preview"><img id="previewImage" class="Polaris-DropZone-FileUpload__Image Polaris-DropZone-FileUpload--sizeExtraLarge" src="" alt=""></div>
                                                                        <div class="Polaris-Stack__Item">
                                                                        <div class="Polaris-Button fileinput-button">
                                                                            <span class="Polaris-Button__Content addNewImage"><span>Add image</span>
                                                                                <input type="file" id="upload_file_edit"  name="upload_file" onchange="readURL(this);"  style="position: absolute; right: 0px; top: 0px; font-family: Arial; font-size: 118px; margin: 0px; padding: 0px; cursor: pointer; opacity: 0">
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Stack__Item"><span class="Polaris-TextStyle--variationSubdued">or drop images to upload</span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                           
                                                    </div>
                                                </div>
                                                <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary" style="width: 50%;margin-top: -2px;">
                                                        <div class="Polaris-Card__Section ">
                                                            <div class="Polaris-DropZone Polaris-DropZone--hasOutline Polaris-DropZone--sizeExtraLarge" >
                                                                <div class="Polaris-DropZone__Container ">
                                                                            <img id="ImagePreview" class="imagepre" src="<?php echo CLS_SITE_URL; ?>/assets/images/no-image.png" alt="your image" />
                                                                    </div>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="Polaris-Layout">
                                            <div class="Polaris-Layout__Section">
                                                    <h2 class="Polaris-Heading text-left text-editer" id="title"></h2> 
                                                        <input type="hidden" id="" name="store" value="<?php echo $store; ?>">
                                                        <textarea class="textdetails" name="description" value="">                                          
                                                        </textarea>
                                            </div>
                                        </div>
                                        <button type="submit" name="submit" id="addblog"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Order" class="Polaris-Button Polaris-Button--primary save_loader_show" style="float: right; margin-top: 20px; margin-bottom: 10px;">Save</button>                                                    
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php  include_once('dashboard_footer.php'); ?>
