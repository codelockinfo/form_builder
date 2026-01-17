 
<?php 
if(isset($_GET['shop']) && $_GET['shop'] != ''){
    $store = $_GET['shop'];
}else if(isset($store) && $store != ''){
    $store = $store;
}else{
    $store = "dashboardmanage.myshopify.com";
}
?>
</head>
    <body>
<div style="height:60px">
  <div class="Polaris-Frame Polaris-Frame--hasTopBar" data-polaris-layer="true">
    <div class="Polaris-Frame__Skip">
      <a href="#AppFrameMain">Skip to content</a>
    </div>
    <div class="Polaris-Frame__TopBar" data-polaris-layer="true" data-polaris-top-bar="true" id="AppFrameTopBar">
      <div class="Polaris-TopBar">
        <button type="button" class="Polaris-TopBar__NavigationIcon" aria-label="Toggle menu">
          <div class="Polaris-TopBar__IconWrapper">
            <span class="Polaris-Icon">
              <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
              </span>
              <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                <path fill-rule="evenodd" d="M3 4.75a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5h-12.5a.75.75 0 0 1-.75-.75Z">
                </path>
                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5h-12.5a.75.75 0 0 1-.75-.75Z">
                </path>
                <path fill-rule="evenodd" d="M3 15.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5h-12.5a.75.75 0 0 1-.75-.75Z">
                </path>
              </svg>
            </span>
          </div>
        </button>
        <div class="Polaris-TopBar__LogoContainer Polaris-TopBar__LogoDisplayControl" style="width: auto; padding-right: 20px;">
            <a class="Polaris-TopBar__LogoLink" href="#" data-polaris-unstyled="true">
                <img alt="Easy form builder shopify app" src="../assets/images/logo.webp" class="Polaris-TopBar__Logo" style="width:40px">
            </a>
            <span style="font-weight: 600; font-size: 15px; white-space: nowrap; margin-left: 10px;">Easy form builder</span>
        </div>
        <div class="Polaris-TopBar__Contents" style="padding: 0 20px; display: flex; align-items: center; justify-content: flex-start;">
            <div style="display: flex; gap: 10px;">
                <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $is_dashboard = ($current_page == 'dashboard.php' && isset($_GET['view']) && $_GET['view'] == 'submissions') || $current_page == 'dashboard_submissions.php' || $current_page == 'submissions.php';
            $is_design = $current_page == 'index.php' || ($current_page == 'dashboard.php' && (!isset($_GET['view']) || $_GET['view'] != 'submissions'));
            ?>
            
             <a href="dashboard_submissions.php?shop=<?php echo $store; ?>" class="Polaris-Button <?php echo $is_dashboard ? 'Polaris-Button--primary' : ''; ?>" style="min-height: 2.2rem;">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Dashboard</span>
                </span>
            </a>

             <a href="index.php?shop=<?php echo $store; ?>" class="Polaris-Button <?php echo $is_design ? 'Polaris-Button--primary' : ''; ?>" style="min-height: 2.2rem;">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Design Form</span>
                </span>
            </a>
            </div>
        </div>
          <div class="Polaris-TopBar__Contents">
          <div class="Polaris-TopBar__SearchField text-end">
            <!-- <button class="Polaris-Button Polaris-Button--primary" type="button">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Shopify Admin</span>
                </span>
            </button> -->
            
          
            <button class="Polaris-Button" type="button">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Plans</span>
                </span>
            </button>
           
        </div>
        <!-- <div class="Polaris-TopBar__SecondaryMenu">
            <div class="Polaris-ButtonGroup">
                <div class="Polaris-ButtonGroup__Item">
                    <button class="Polaris-Button" type="button">
                    <span class="Polaris-Button__Content">
                        <span class="Polaris-Button__Text">Cancel</span>
                    </span>
                </button>
            </div>
  
            <div class="Polaris-ButtonGroup__Item">
                <button class="Polaris-Button Polaris-Button--primary" type="button">
                    <span class="Polaris-Button__Content">
                        <span class="Polaris-Button__Text">Save</span>
                    </span>
                </button>
            </div>
            </div>
        </div> -->
           
    </div>
    </div>
</div>
</div>
</div>
