<?php 
include_once('cls_header.php'); 

if (isset($_GET['shop']) && $_GET['shop'] != '') {
    include_once('dashboard_header.php');
} else {
    echo "Store not found";die;
}
?>

<style>
/* Dashboard Submissions Page Styles - Shopify Polaris Design */
.dashboard-submissions-page {
    background-color: #f6f6f7;
    min-height: calc(100vh - 60px);
    padding: 20px;
}

.dashboard-submissions-page .Polaris-Page {
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-submissions-page .Polaris-Page-Header {
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    padding: 20px 24px;
    margin-bottom: 0;
    border-bottom: 1px solid #e1e3e5;
}

.dashboard-submissions-page .Polaris-Header-Title {
    font-size: 28px;
    font-weight: 600;
    color: #202223;
    margin: 0;
    letter-spacing: -0.02em;
}

/* Tab Navigation */
.dashboard-tabs {
    background: #ffffff;
    border-bottom: 1px solid #e1e3e5;
    padding: 0 24px;
    display: flex;
    gap: 0;
}

.dashboard-tab {
    padding: 16px 24px;
    font-size: 14px;
    font-weight: 500;
    color: #6d7175;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    background: transparent;
    border: none;
    position: relative;
}

.dashboard-tab:hover {
    color: #202223;
    background: #f6f6f7;
}

.dashboard-tab.active {
    color: #008060;
    border-bottom-color: #008060;
    font-weight: 600;
}

.dashboard-tab-content {
    display: none;
}

.dashboard-tab-content.active {
    display: block;
}

.form-list-container {
    background: #ffffff;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
    overflow: visible;
    border: 1px solid #e1e3e5;
    border-top: none;
}

.form-list-header {
    background: #f9fafb;
    border-bottom: 1px solid #e1e3e5;
    padding: 12px 24px;
}

.form-list-header .Polaris-CheckableButton__Label {
    font-size: 13px;
    font-weight: 600;
    color: #202223;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.set_all_form_submissions {
    padding: 0;
}

/* Hide any checkboxes or toggle switches in submissions view */
.set_all_form_submissions .Polaris-Choice,
.set_all_form_submissions .svgicon,
.set_all_form_submissions .switch {
    display: none !important;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderWrapper {
    border-bottom: 1px solid #e1e3e5;
    transition: background-color 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: visible;
    position: relative;
    background: #ffffff;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderWrapper:last-child {
    border-bottom: none;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderWrapper:hover {
    background-color: #f6f6f7;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderContentWrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    flex: 1;
    gap: 20px;
    min-width: 0;
    overflow: visible;
}

.set_all_form_submissions .Polaris-ResourceList__CheckableButtonWrapper {
    flex: 1;
    min-width: 0;
}

.set_all_form_submissions .Polaris-CheckableButton {
    width: 100%;
}

.set_all_form_submissions .clsmain_form {
    display: flex;
    flex-direction: column;
    gap: 0px;
    margin: 0;
}

.set_all_form_submissions .sp-font-size {
    font-size: 15px;
    font-weight: 500;
    color: #202223;
    line-height: 1.5;
    margin: 0;
    letter-spacing: -0.01em;
}

.set_all_form_submissions .form-id-display {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    margin-top: 4px;
}

.set_all_form_submissions .form-id-display span:first-child {
    font-weight: 400;
    color: #6d7175;
    font-size: 13px;
}

.set_all_form_submissions .form-id-value {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', 'Consolas', monospace;
    font-size: 12px;
    background: #f6f6f7;
    padding: 2px 8px;
    border-radius: 4px;
    color: #6d7175;
    cursor: pointer;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 400;
    border: 1px solid transparent;
}

.set_all_form_submissions .form-id-value:hover {
    background: #e1e3e5;
    color: #202223;
    border-color: #c9cccf;
}

.set_all_form_submissions .Polaris-ResourceList__AlternateToolWrapper {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    flex-shrink: 0;
    justify-content: flex-end;
}

.set_all_form_submissions .main_right_ {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    flex-shrink: 0;
    min-width: fit-content;
}

.set_all_form_submissions .indexButton {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
    white-space: nowrap;
    padding-right:35px;
}

.set_all_form_submissions .indexButton button {
    min-height: 36px;
    padding: 8px 16px;
    border: 1px solid #c9cccf;
    border-radius: 6px;
    background: #ffffff;
    color: #202223;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    flex-shrink: 0;
    min-width: fit-content;
    font-family: -apple-system, BlinkMacSystemFont, 'San Francisco', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
}

.set_all_form_submissions .indexButton button:hover {
    background: #f6f6f7;
    border-color: #8c9196;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
}

.set_all_form_submissions .indexButton button:active {
    background: #e1e3e5;
    box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
}

.set_all_form_submissions .indexButton button a {
    color: #202223;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.set_all_form_submissions .indexButton button a:hover {
    color: #202223;
    text-decoration: none;
}

.set_all_form_submissions .indexButton button a:focus {
    outline: none;
}

/* Analytics Dashboard Styles */
.analytics-dashboard {
    background: #ffffff;
    padding: 24px;
}

.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.analytics-filters {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-label {
    font-size: 13px;
    font-weight: 500;
    color: #202223;
}

.filter-select {
    padding: 6px 12px;
    border: 1px solid #c9cccf;
    border-radius: 6px;
    font-size: 13px;
    background: #ffffff;
    color: #202223;
    cursor: pointer;
    min-width: 120px;
}

.filter-select:hover {
    border-color: #8c9196;
}

.filter-select:focus {
    outline: none;
    border-color: #008060;
    box-shadow: 0 0 0 1px #008060;
}

.analytics-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.stat-card {
    background: #f6f6f7;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e1e3e5;
}

.stat-card-title {
    font-size: 12px;
    font-weight: 500;
    color: #6d7175;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}

.stat-card-value {
    font-size: 32px;
    font-weight: 600;
    color: #202223;
    line-height: 1.2;
}

.stat-card-subtitle {
    font-size: 13px;
    color: #6d7175;
    margin-top: 4px;
}

.stat-card.views {
    border-left: 4px solid #008060;
}

.stat-card.fills {
    border-left: 4px solid #006fbb;
}

.stat-card.submits {
    border-left: 4px solid #eb1256;
}

.analytics-charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.chart-container {
    background: #ffffff;
    border: 1px solid #e1e3e5;
    border-radius: 8px;
    padding: 20px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.chart-title {
    font-size: 16px;
    font-weight: 600;
    color: #202223;
}

.chart-wrapper {
    position: relative;
    height: 300px;
}

/* Empty state */
.set_all_form_submissions:empty::before {
    content: "No forms found";
    display: block;
    padding: 40px 24px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

/* Loading state */
.form-list-loading {
    padding: 40px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

.analytics-loading {
    padding: 60px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

@media (max-width: 768px) {
    .dashboard-submissions-page {
        padding: 12px;
    }
    
    .dashboard-submissions-page .Polaris-Page-Header {
        padding: 16px;
    }
    
    .dashboard-submissions-page .Polaris-Header-Title {
        font-size: 24px;
    }
    
    .set_all_form_submissions .main_right_ {
        flex-direction: column;
        gap: 12px;
    }
    
    .set_all_form_submissions .indexButton {
        width: 100%;
    }
    
    .set_all_form_submissions .indexButton button {
        flex: 1;
    }
    
    .analytics-charts {
        grid-template-columns: 1fr;
    }
    
    .analytics-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-submissions-page">
    <div class="Polaris-Page">
        <div class="Polaris-Page-Header Polaris-Page-Header--hasActionMenu Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
            <div class="Polaris-Page-Header__Row padding_all_">
                <div class="Polaris-Page-Header__TitleWrapper">
                    <h1 class="Polaris-Header-Title">Dashboard</h1>
                </div>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <div class="dashboard-tabs">
            <button class="dashboard-tab active" data-tab="submissions">Submissions</button>
            <button class="dashboard-tab" data-tab="reports">Reports</button>
        </div>
        
        <!-- Submissions Tab -->
        <div class="dashboard-tab-content active" id="submissions-tab">
            <div class="form-list-container">
                <div class="form-list-header">
                    <div class="Polaris-ResourceList__CheckableButtonWrapper">
                        <span class="Polaris-CheckableButton__Label">Form List</span>
                    </div>
                </div>
                
                <div class="set_all_form_submissions">
                    <div class="form-list-loading">Loading forms...</div>
                </div>
            </div>
        </div>
        
        <!-- Reports Tab -->
        <div class="dashboard-tab-content" id="reports-tab">
            <div class="analytics-dashboard">
                <div class="analytics-header">
                    <h2 style="margin: 0; font-size: 20px; font-weight: 600; color: #202223;">Analytics Overview</h2>
                    <div class="analytics-filters">
                        <div class="filter-group">
                            <label class="filter-label">Form:</label>
                            <select class="filter-select" id="analytics-form-filter">
                                <option value="0">All Forms</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Date Range:</label>
                            <select class="filter-select" id="analytics-date-range">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="filter-group" id="custom-date-range" style="display: none;">
                            <input type="date" class="filter-select" id="date-from" style="min-width: 140px;">
                            <span style="color: #6d7175;">to</span>
                            <input type="date" class="filter-select" id="date-to" style="min-width: 140px;">
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="analytics-stats">
                    <div class="stat-card views">
                        <div class="stat-card-title">Form Views</div>
                        <div class="stat-card-value" id="stat-views">0</div>
                        <div class="stat-card-subtitle">Today: <span id="stat-views-today">0</span></div>
                    </div>
                    <div class="stat-card fills">
                        <div class="stat-card-title">Form Fills</div>
                        <div class="stat-card-value" id="stat-fills">0</div>
                        <div class="stat-card-subtitle">Today: <span id="stat-fills-today">0</span></div>
                    </div>
                    <div class="stat-card submits">
                        <div class="stat-card-title">Form Submissions</div>
                        <div class="stat-card-value" id="stat-submits">0</div>
                        <div class="stat-card-subtitle">Today: <span id="stat-submits-today">0</span></div>
                    </div>
                </div>
                
                <!-- Charts -->
                <div class="analytics-charts">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Form Activity Over Time</h3>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Form Performance</h3>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="analytics-loading" id="analytics-loading" style="display: none;">Loading analytics...</div>
            </div>
        </div>
    </div>
</div>

<?php include_once('dashboard_footer.php'); ?>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/677e4e7faf5bfec1dbe85825/1ih2m597m';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    let activityChart = null;
    let performanceChart = null;
    
    $(document).ready(function(){
        // Ensure store is defined or retrieved
        if(typeof store === 'undefined' || store === '') {
             var urlParams = new URLSearchParams(window.location.search);
             store = urlParams.get('shop');
        }
       
        
        // Tab switching
        $('.dashboard-tab').on('click', function() {
            var tabName = $(this).data('tab');
            
            // Update active tab
            $('.dashboard-tab').removeClass('active');
            $(this).addClass('active');
            
            // Update active content
            $('.dashboard-tab-content').removeClass('active');
            $('#' + tabName + '-tab').addClass('active');
            
            // Load analytics if Reports tab is selected
            if (tabName === 'reports') {
                loadAnalytics();
            }
        });
        
        // Date range filter change
        $('#analytics-date-range').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#custom-date-range').show();
            } else {
                $('#custom-date-range').hide();
                loadAnalytics();
            }
        });
        
        // Custom date range change
        $('#date-from, #date-to').on('change', function() {
            if ($('#date-from').val() && $('#date-to').val()) {
                loadAnalytics();
            }
        });
        
        // Form filter change
        $('#analytics-form-filter').on('change', function() {
            loadAnalytics();
        });
        
        // Load forms for filter dropdown
        loadFormsForFilter();
        
        // Load forms for submissions tab
        if (typeof getAllFormSubmissions === 'function') {
            getAllFormSubmissions();
        } else {
            // Fallback if function doesn't exist
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: { 'routine_name': 'getAllFormFunction', store: store, view_type: 'submissions_dashboard' },
                success: function (comeback) {
                   
                    var comeback = JSON.parse(comeback);
                    if (comeback['code'] != undefined && comeback['code'] == '403') {
                        if (typeof redirect403 === 'function') {
                            redirect403();
                        }
                    } else {
                        $(".set_all_form_submissions").html(comeback['outcome'] || '<div class="form-list-loading">No forms found</div>');
                        // Initialize view button handlers after forms are loaded
                        initViewFormButtons();
                    }
                },
                error: function(xhr, status, error) {
                    $(".set_all_form_submissions").html('<div class="form-list-loading">Error loading forms. Please try again.</div>');
                }
            });
        }
        
    });
    
    function loadFormsForFilter() {
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: { 'routine_name': 'getAllFormFunction', store: store },
            success: function (comeback) {
                var comeback = JSON.parse(comeback);
                if (comeback['code'] != undefined && comeback['code'] == '403') {
                    return;
                }
                
                if (comeback['outcome'] && typeof comeback['outcome'] === 'string') {
                    // Parse HTML to extract form IDs and names
                    var $html = $('<div>').html(comeback['outcome']);
                    $html.find('.Polaris-ResourceList__HeaderWrapper').each(function() {
                        var $wrapper = $(this);
                        var formId = $wrapper.find('[data-form-id]').data('form-id') || 
                                    $wrapper.find('input[type="hidden"]').val() ||
                                    $wrapper.find('.form-id-value').text().trim();
                        var formName = $wrapper.find('.sp-font-size').text().trim() || 'Untitled Form';
                        
                        if (formId) {
                            $('#analytics-form-filter').append('<option value="' + formId + '">' + formName + '</option>');
                        }
                    });
                }
            }
        });
    }
    
    function loadAnalytics() {
        $('#analytics-loading').show();
        
        var formId = $('#analytics-form-filter').val() || 0;
        var dateRange = $('#analytics-date-range').val();
        var dateFrom = '';
        var dateTo = '';
        
        if (dateRange === 'custom') {
            dateFrom = $('#date-from').val();
            dateTo = $('#date-to').val();
        } else {
            var days = parseInt(dateRange);
            var today = new Date();
            dateTo = today.toISOString().split('T')[0];
            var fromDate = new Date(today);
            fromDate.setDate(today.getDate() - days);
            dateFrom = fromDate.toISOString().split('T')[0];
        }
        
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: {
                'routine_name': 'getFormAnalyticsData',
                'store': store,
                'form_id': formId,
                'date_from': dateFrom,
                'date_to': dateTo
            },
            success: function (response) {
                $('#analytics-loading').hide();
                
                if (response && response['status'] == 1 && response['data']) {
                    var data = response['data'];
                    
                    // Update statistics
                    $('#stat-views').text(formatNumber(data.total_views || 0));
                    $('#stat-views-today').text(formatNumber(data.today_views || 0));
                    $('#stat-fills').text(formatNumber(data.total_fills || 0));
                    $('#stat-fills-today').text(formatNumber(data.today_fills || 0));
                    $('#stat-submits').text(formatNumber(data.total_submits || 0));
                    $('#stat-submits-today').text(formatNumber(data.today_submits || 0));
                    
                    // Update charts
                    updateActivityChart(data.daily_data || {});
                    updatePerformanceChart(data.form_data || {}, data.form_names || {});
                } else {
                }
            },
            error: function(xhr, status, error) {
                $('#analytics-loading').hide();
            }
        });
    }
    
    function updateActivityChart(dailyData) {
        var ctx = document.getElementById('activityChart');
        if (!ctx) return;
        
        // Sort dates
        var sortedDates = Object.keys(dailyData).sort();
        var labels = sortedDates.map(function(date) {
            var d = new Date(date);
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        
        var viewsData = sortedDates.map(function(date) { return dailyData[date].views || 0; });
        var fillsData = sortedDates.map(function(date) { return dailyData[date].fills || 0; });
        var submitsData = sortedDates.map(function(date) { return dailyData[date].submits || 0; });
        
        if (activityChart) {
            activityChart.destroy();
        }
        
        activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Views',
                        data: viewsData,
                        borderColor: '#008060',
                        backgroundColor: 'rgba(0, 128, 96, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Fills',
                        data: fillsData,
                        borderColor: '#006fbb',
                        backgroundColor: 'rgba(0, 111, 187, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Submissions',
                        data: submitsData,
                        borderColor: '#eb1256',
                        backgroundColor: 'rgba(235, 18, 86, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    
    function updatePerformanceChart(formData, formNames) {
        var ctx = document.getElementById('performanceChart');
        if (!ctx) return;
        
        var formIds = Object.keys(formData);
        var viewsData = formIds.map(function(id) { return formData[id].views || 0; });
        var fillsData = formIds.map(function(id) { return formData[id].fills || 0; });
        var submitsData = formIds.map(function(id) { return formData[id].submits || 0; });
        
        // Use form names if available, otherwise fallback to "Form {id}"
        var labels = formIds.map(function(id) {
            if (formNames && formNames[id]) {
                return formNames[id];
            }
            return 'Form ' + id;
        });
        
        if (performanceChart) {
            performanceChart.destroy();
        }
        
        performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Views',
                        data: viewsData,
                        backgroundColor: '#008060',
                    },
                    {
                        label: 'Fills',
                        data: fillsData,
                        backgroundColor: '#006fbb',
                    },
                    {
                        label: 'Submissions',
                        data: submitsData,
                        backgroundColor: '#eb1256',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label || '';
                            },
                            label: function(context) {
                                var label = context.dataset.label || '';
                                var value = context.parsed.y || 0;
                                return label + ': ' + value;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                    },
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    
    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }
</script>

