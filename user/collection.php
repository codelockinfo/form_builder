<?php 
      include "cls_header.php";
      include_once('dashboard_header.php');
?>
<html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/polaris.min.css" rel="stylesheet"> 
    </head>    
<body>
<div class="Polaris-Page">
    <div class="Polaris-Page__Content">
        <div class="Polaris-Layout">
            <div class="Polaris-Layout__AnnotatedSection">
                <div class="Polaris-Layout__AnnotationWrapper">
                    <div class="Polaris-Layout__AnnotationContent">
                            <div class="Polaris-Card__Section">     
                            <div class="Polaris-Card">
                                <div class="Polaris-Card__Header">
                                    <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                        <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                            <h2 class="Polaris-Heading">Collection's list</h2>
                                              <div class="btncollection">
                                                    <a  class="Polaris-Button Polaris-Button--primary save_loader_show"  onclick="loading_show('.save_loader_show')" href="addcollection.php?store=<?php echo $_SESSION['store']; ?>">
                                                        <span>Add Collection</span>
                                                    </a>
                                                </div>
                                        </div>                                       
                                    </div>
                                </div>
                                <div class="Polaris-Card__Section">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <div class="Polaris-TextField__Prefix" id="TextField-Browse-collection">
                                                <span class="Polaris-Icon Polaris-Icon--hasBackdrop" aria-label="">
                                                   <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M8 12c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm9.707 4.293l-4.82-4.82C13.585 10.493 14 9.296 14 8c0-3.313-2.687-6-6-6S2 4.687 2 8s2.687 6 6 6c1.296 0 2.492-.415 3.473-1.113l4.82 4.82c.195.195.45.293.707.293s.512-.098.707-.293c.39-.39.39-1.023 0-1.414z" fill="#95a7b7" fill-rule="evenodd"></path></svg>
                                                </span>
                                            </div>
                                            <input type="text" id="collectionDataSearchKeyword" name="collections_list" class="Polaris-TextField__Input browse_buy_product_search" onkeyup="js_loadShopifyDATA('collectionData')" aria-invalid="false" placeholder="Search collections" autocomplete="off">                                                                        
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                    <div class="Polaris-Labelled__HelpText">Type at least 3 characters</div>                                  
                                    <div class="Polaris-Card mt-4">  
                                        <input type="hidden" name="limit" value="<?php echo CLS_PAGE_PER; ?>" id="collectionsDataLimit" selected="selected">
                                        <div class="Polaris-DataTable">
                                            <div class="table-responsive">
                                                <table id="collectionData" data-listing="true" data-from="table" data-apiName="custom_collections" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Image</th>
                                                            <th>Title</th>
                                                            <th>Description</th>                                                   
                                                            <th width="220">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="allcollectionData">                                                     
                                                </tbody>
                                                </table>
                                            </div> 
                                               <div class="cls-page-pagination mb-4" id="collectionDataPagination">
                                                                                                      
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
  <script>
            $(document).ready(function () {
                var routineName = 'get_store_collection';
                var shopify_api = $('#collectionData').attr('data-apiName');
                get_api_data(routineName, shopify_api);
            });
        </script>
</html>    
<?php  include_once('dashboard_footer.php'); ?>