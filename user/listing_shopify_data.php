<?php include_once('cls_header.php'); ?>
<div class="wrapper discount-wrapper-content wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> Products List</h5>
                </div>
                <div class="ibox-content">
                    <input type="search" name="search_keyword" id="listApiDataSearchKeyword" onkeyup="__loadshopifyListData('listApiData')" class="form-control input-sm" placeholder="search in title (min 3 char)" aria-controls="productsListDT">
                </div>
                <div class="ibox-content">
                    <select name="limit" id="listApiDataLimit" onchange="__loadshopifyListData('listApiData')">
                        <option value="10">10</option>
                        <option value="25" selected="selected">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                    </select>
                </div>                   
                <div class="ibox-content">
                    <table id="listApiData" data-listing="true" data-from="api" data-search="title" data-apiName="products" class="footable table table-stripped toggle-arrow-tiny" width="100%" cellspacing="0" >
                        <thead>
                            <tr>
                                <th>Product Image</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Vendor</th>
                                <th>Tags</th>
                                <th>Dates</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="pagination" id="listApiDataPagination"></div> 
            </div>
        </div>
    </div>
</div>
<?php include_once('cls_footer.php'); ?>