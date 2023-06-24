<?php
include_once('cls_header.php');
$where_query = array(["","store_user_id","=",$cls_functions->get_store_client_id()]);
$comeback = $cls_functions->get_rank(CLS_TABLE_SHIPMENT_METHOD,$where_query);
$comeback = json_decode($comeback);
$rank_shipping_method_cnt = $comeback->data;
?>
<div class="Polaris-Page Polaris-Page--fullWidth">
    <div class="Polaris-Page__Content">
        <div class="Polaris-Layout">
            <div class="Polaris-Layout__AnnotatedSection">
                <div class="Polaris-Layout__AnnotationWrapper">
                    <div class="Polaris-Layout__AnnotationContent">
                        <?php if ($rank_shipping_method_cnt > 0) { ?>
                            <div class="Polaris-Card">
                                <div class="Polaris-Card__Header">
                                    <h2 class="Polaris-Heading">Shipping methods list</h2>
                                </div>
                                <div class="Polaris-Card__Section">
                                    <div class="Polaris-Page__MainContent">
                                        <div class="Polaris-Page__TitleAndActions">
                                            <div class="Polaris-Page__Title">
                                                <div class="Polaris-Connected">
                                                    <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="Select2Label" for="Select2" class="Polaris-Label__Text">Data Limit</label></div>
                                                        </div>
                                                        <div class="Polaris-Select">
                                                            <select id="listApiDataLimit" onchange="__loadshopifyListData('listApiData')" class="Polaris-Select__Input" aria-invalid="false">
                                                                <option value="2">2</option>
                                                                <option value="10">10</option>
                                                                <option value="25" selected="selected">25</option>
                                                                <option value="50">50</option>
                                                                <option value="100">100</option>
                                                                <option value="250">250</option>
                                                            </select>
                                                            <div class="Polaris-Select__Icon"><span class="Polaris-Icon"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path></svg></span></div>
                                                            <div class="Polaris-Select__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField">
                                                            <div class="Polaris-TextField__Prefix" id="TextField-Browse-collection">
                                                                <span class="Polaris-Icon Polaris-Icon--hasBackdrop" aria-label="">
                                                                    <?php echo CLS_SVG_SEARCH; ?>
                                                                </span>
                                                            </div>
                                                            <input type="text" id="listApiDataSearchKeyword" name="product_list" class="Polaris-TextField__Input browse_buy_product_search" onkeyup="__loadshopifyListData('listApiData')" aria-invalid="false" placeholder="Search method" autocomplete="off">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection shipping-tag-btn">
                                                        <div class="Polaris-Labelled--hidden">
                                                            <button type="button" class="Polaris-Button Polaris-Button--sizeSlim tip" data-hover="Reset" onclick="cls_search_method_reset()">
                                                                <span class="Polaris-Button__Content">
                                                                    <span class="Polaris-Icon Polaris-Icon--hasBackdrop">
                                                                        <?php echo CLS_CLS_SVG_RESET; ?>
                                                                    </span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a  class="Polaris-Button Polaris-Button--primary deactive-hider-btn" href="shipping-method.php?shop=<?php echo $shop; ?>"><span class="Polaris-Button__Content"><span>Create new shipping method</span></span></a>
                                    </div>
                                    <div class="Polaris-Labelled__HelpText">Type at least 3 characters</div>
                                    <div class="Polaris-DataTable">
                                        <div class="table-result">
                                            <table id="listApiData" data-listing="true" data-from="table" data-search="inner_name|display_name|tag_prefix" data-apiName="<?php echo CLS_TABLE_SHIPMENT_METHOD; ?>" class="footable table table-stripped toggle-arrow-tiny" width="100%" cellspacing="0" >                                          
                                                <thead>
                                                    <tr>
                                                        <th>Internal name</th>
                                                        <th>Display name</th>
                                                        <th>Tag</th>
                                                        <th>2 or More items with the same shipping tag</th>
                                                        <th>None of the items are tagged with the shipping tag</th>
                                                        <th>Some of the items are tagged with the shipping tag and some not</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="pagination" id="listApiDataPagination"></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="Polaris-EmptyState">
                                <div class="Polaris-EmptyState__Section">
                                    <div class="Polaris-EmptyState__DetailsContainer">
                                        <div class="Polaris-EmptyState__Details">
                                            <div class="Polaris-TextContainer">
                                                <p class="Polaris-DisplayText Polaris-DisplayText--sizeMedium">No Shipping Methods Yet</p>
                                                <div class="Polaris-EmptyState__Content">
                                                    <p>You haven't created any shipping methods yet.Let us take you through creating the first one</p>
                                                </div>
                                            </div>
                                            <div class="Polaris-EmptyState__Actions">
                                                <div class="Polaris-ButtonGroup">
                                                    <div class="Polaris-ButtonGroup__Item"><a  class="Polaris-Button Polaris-Button--primary deactive-hider-btn" href="shipping-method.php?shop=<?php echo $shop; ?>"><span class="Polaris-Button__Content"><span>Create a shipping method</span></span></a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="Polaris-EmptyState__ImageContainer"><img src="../assets/images/empty-state.png" role="presentation" alt="" class="Polaris-EmptyState__Image"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('cls_footer.php'); ?>