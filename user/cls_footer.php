<?php 
$current_user = (!is_object($current_user)) ? $current_user : (array)$current_user;
if(isset($current_user['store_idea'])  != 'staff' && isset($current_user['store_idea']) != 'staff_business') { ?>
<?php } ?>
</div>
<?php if (MODE == 'live') { ?>
    <?php include('../../appstation/slider/footer_slider.php'); ?>
<?php } ?>
<div class="inline-flash-wrapper animated bounceInUp"><div class="inline-flash"><p class="inline-flash__message"></p></div></div>
<?php if (MODE == 'live' && $current_user['store_idea'] != 'staff' && $current_user['store_idea'] != 'staff_business') { ?>
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/591d836c76be7313d291d64c/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
        Tawk_API.visitor = {
            name: '<?php  echo $current_user['store_name']; ?>',            
            email: '<?php echo $current_user['email']; ?>'
        };
    </script>
<?php } elseif (MODE == 'live') { ?>
    <script type='text/javascript'>
        (function () {
            var widget_id = 'f3znHLGH3h';
            var d = document;
            var w = window;
            function l() {
                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = '//code.jivosite.com/script/widget/' + widget_id;
                var ss = document.getElementsByTagName('script')[0];
                ss.parentNode.insertBefore(s, ss);
            }
            if (d.readyState == 'complete') {
                l();
            } else {
                if (w.attachEvent) {
                    w.attachEvent('onload', l);
                } else {
                    w.addEventListener('load', l, false);
                }
            }
        })();
    </script>
<?php }
?>
<script>
    $('.openChatBox').click(function () {
        if (typeof (Tawk_API) !== "undefined") {
            Tawk_API.toggle();
        }
        $('.globalClass_ET .hoverl_6R').click();
    });
</script>
</body>
</html>
