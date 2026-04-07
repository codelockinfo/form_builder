 
<?php

if (isset($_GET['shop']) && $_GET['shop'] != '') {
    $store = $_GET['shop'];
}
else if (isset($store) && $store != '') {
    $store = $store;
}
else {
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
        <button type="button" class="Polaris-TopBar__NavigationIcon js-mobile-nav-toggle" aria-label="Toggle menu">
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
        <div class="Polaris-TopBar__LogoContainer Polaris-TopBar__LogoDisplayControl polaris-logo-center-mobile" style="width: auto; padding-right: 20px;">
            <a class="Polaris-TopBar__LogoLink" href="#" data-polaris-unstyled="true">
                <img alt="Easy form builder shopify app" src="../assets/images/app_logo.png" class="Polaris-TopBar__Logo" style="width:40px">
            </a>
            <span style="font-weight: 600; font-size: 15px; white-space: nowrap; margin-left: 10px;">Easy form builder</span>
        </div>
        <div class="Polaris-TopBar__Contents polaris-topbar-primary-links" style="padding: 0 20px; display: flex; align-items: center; justify-content: flex-start;">
            <div style="display: flex; gap: 10px;">
                <?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_dashboard = $current_page == 'index.php' || $current_page == 'dashboard_submissions.php' || $current_page == 'submissions.php' || ($current_page == 'dashboard.php' && isset($_GET['view']) && $_GET['view'] == 'submissions');
$is_design = $current_page == 'dashboard.php' && (!isset($_GET['view']) || $_GET['view'] != 'submissions');
?>
            
             <a href="index.php?shop=<?php echo $store; ?>" class="Polaris-Button <?php echo $is_dashboard ? 'Polaris-Button--primary' : ''; ?>" style="min-height: 2.2rem;">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Dashboard</span>
                </span>
            </a>

             <a href="dashboard.php?shop=<?php echo $store; ?>" class="Polaris-Button <?php echo $is_design ? 'Polaris-Button--primary' : ''; ?>" style="min-height: 2.2rem;">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Design Form</span>
                </span>
            </a>
            </div>
        </div>
        <div class="Polaris-TopBar__Contents polaris-topbar-secondary-links">
          <div class="Polaris-TopBar__SearchField text-end" style="display: none;">
            <!-- <button class="Polaris-Button Polaris-Button--primary" type="button">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">Shopify Admin</span>
                </span>
            </button> -->
            
          
            <button class="Polaris-Button js-plan-button" type="button">
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

    <!-- Mobile sidebar navigation -->
    <div class="mobile-sidebar-overlay"></div>
    <nav class="mobile-sidebar">
        <div class="mobile-sidebar__header">
            <div class="mobile-sidebar__logo">
                <img src="../assets/images/logo.webp" alt="Easy form builder shopify app">
                <span>Easy form builder</span>
            </div>
        </div>
        <ul class="mobile-sidebar__menu">
            <li>
                <a href="index.php?shop=<?php echo $store; ?>" class="mobile-sidebar__link <?php echo $is_dashboard ? 'is-active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="dashboard.php?shop=<?php echo $store; ?>" class="mobile-sidebar__link <?php echo $is_design ? 'is-active' : ''; ?>">
                    Design Form
                </a>
            </li>
            <li>
                <button type="button" class="mobile-sidebar__link mobile-sidebar__link--button js-plan-button-mobile">
                    Plans
                </button>
            </li>
        </ul>
    </nav>
</div>
</div>

<style>
@media (max-width: 768px) {
    .polaris-topbar-primary-links,
    .polaris-topbar-secondary-links {
        display: none !important;
    }

    .Polaris-TopBar {
        position: relative;
    }

    /* Force logo container to be visible on small screens */
    .Polaris-TopBar__LogoDisplayControl {
        display: flex !important;
        align-items: center;
    }

    .polaris-logo-center-mobile {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        padding-right: 0 !important;
    }

    .polaris-logo-center-mobile .Polaris-TopBar__LogoLink img {
        width: 40px;
        height: auto;
    }
}

.mobile-sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
    z-index: 9998;
}

.mobile-sidebar-overlay.is-open {
    opacity: 1;
    visibility: visible;
}

.mobile-sidebar {
    position: fixed;
    top: 0;
    left: -260px;
    width: 260px;
    height: 100%;
    background: #ffffff;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    transition: left 0.2s ease;
    padding-top: 60px;
    display: flex;
    flex-direction: column;
}

.mobile-sidebar.is-open {
    left: 0;
}

.mobile-sidebar__header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 16px;
    border-bottom: 1px solid #dfe3e8;
}

.mobile-sidebar__logo {
    display: flex;
    align-items: center;
    gap: 8px;
}

.mobile-sidebar__logo img {
    width: 32px;
    height: 32px;
}

.mobile-sidebar__logo span {
    font-size: 15px;
    font-weight: 600;
    color: #202223;
}

.mobile-sidebar__menu {
    list-style: none;
    margin: 0;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.mobile-sidebar__link {
    display: block;
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    text-align: left;
    background: transparent;
    border: none;
    font-size: 14px;
    font-weight: 500;
    color: #202223;
    text-decoration: none;
}

.mobile-sidebar__link.is-active {
    background: #297eb0;
    color: #ffffff;
}

.mobile-sidebar__link:hover {
    background: #f6f6f7;
    text-decoration: none;
    color: #202223;
}

.mobile-sidebar__link.is-active:hover {
    background: #1e5d8a;
    color: #ffffff;
}

.mobile-sidebar__link--button {
    text-align: left;
    cursor: pointer;
}

@media (min-width: 769px) {
    /* Keep desktop header exactly as before */
    .Polaris-TopBar__NavigationIcon {
        display: none;
    }

    .mobile-sidebar,
    .mobile-sidebar-overlay {
        display: none;
    }
}
</style>

<script>
$(function () {
    var $toggle = $('.js-mobile-nav-toggle');
    var $sidebar = $('.mobile-sidebar');
    var $overlay = $('.mobile-sidebar-overlay');

    function closeMobileSidebar() {
        $sidebar.removeClass('is-open');
        $overlay.removeClass('is-open');
        $('body').removeClass('mobile-nav-open');
    }

    $toggle.on('click', function (e) {
        e.preventDefault();
        $sidebar.toggleClass('is-open');
        $overlay.toggleClass('is-open');
        $('body').toggleClass('mobile-nav-open');
    });

    $overlay.on('click', function () {
        closeMobileSidebar();
    });

    $('.mobile-sidebar__link').on('click', function () {
        // Close sidebar when navigating
        closeMobileSidebar();
    });

    // Mirror "Plans" action between top bar and mobile sidebar
    $('.js-plan-button-mobile').on('click', function (e) {
        e.preventDefault();
        var $desktopPlanBtn = $('.js-plan-button').first();
        if ($desktopPlanBtn.length) {
            $desktopPlanBtn.trigger('click');
        }
        closeMobileSidebar();
    });
});
</script>
