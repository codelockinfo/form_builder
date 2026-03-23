
// Global modal centering - uses real viewport dimensions to bypass
// position:fixed quirks inside Shopify iframe on mobile devices
window.centerModal = function (modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    var content = modal.querySelector('.modal-content');
    if (!content) return;

    var vw = window.innerWidth;
    var vh = window.innerHeight;
    var padding = 16;
    var finalW = Math.min(vw - padding * 2, 800);

    content.style.position = 'fixed';
    content.style.width = finalW + 'px';
    content.style.maxWidth = finalW + 'px';
    content.style.maxHeight = Math.round(vh * 0.9) + 'px';
    content.style.overflowY = 'auto';
    content.style.boxSizing = 'border-box';
    content.style.zIndex = '10000';
    content.style.margin = '0';
    content.style.transform = 'none';
    content.style.webkitTransform = 'none';
    content.style.left = Math.round((vw - finalW) / 2) + 'px';

    // Calculate top after width is set (height resolves after paint)
    setTimeout(function () {
        var ch = content.offsetHeight;
        content.style.top = Math.max(padding, Math.round((vh - ch) / 2)) + 'px';
    }, 0);
};

$(document).ready(function () {
    // code for create new form start
    $("#myBtn_new").click(function () {
        $("#myModal_new").css("display", "block");
        window.centerModal('myModal_new');
    });
    $(".close_new").click(function () {
        $("#myModal_new").css("display", "none");
    });
    $(".close2_new").click(function () {
        $("#myModal_new").css("display", "none");
    });
    // code for create new form end
    $(".main_list_").click(function () {
        $(".main_list_").removeClass("active_form");
        $(this).addClass("active_form");
        $(".first_txt_image").removeClass("first_txt_image");
        $(this).find(".text_image_list").addClass("first_txt_image");
        var getval = $(this).data("val");
        var formname = $(this).find(".text_image_list").html();
        $(".selectedType").val(getval);
        $(".formnamehide").val(formname);
    });
});
