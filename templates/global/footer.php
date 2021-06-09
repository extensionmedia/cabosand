<script src="<?= HTTP.HOST ?>templates/global/js/api/jquery-3.5.0.min.js"></script>
<script src="<?= HTTP.HOST ?>templates/global/js/api/jquery.table2excel.min.js"></script>

<script src="<?= HTTP.HOST ?>templates/global/js/api/Chart.min.js"></script>
<script src="<?= HTTP.HOST ?>templates/global/js/api/sweetalert2.min.js"></script>
<script src="<?= HTTP.HOST ?>templates/global/js/api/moment.min.js"></script>
<script src="<?= HTTP.HOST ?>templates/global/js/api/moment.min.fr.js"></script>
<script src="<?= HTTP.HOST ?>templates/global/js/api/Yjs.js"></script>
<script src="<?= HTTP.HOST ?>templates/global/js/app.js"></script>

<script src="<?= HTTP.HOST ?>templates/<?= APP_TEMPLATE ?>/js/load.js?version=<?= time() ?>"></script>
<script src="<?= HTTP.HOST ?>templates/<?= APP_TEMPLATE ?>/js/app.js?version=<?= time() ?>"></script>
<script src="<?= HTTP.HOST ?>templates/<?= APP_TEMPLATE ?>/js/list.js?version=<?= time() ?>"></script>
<script src="<?= HTTP.HOST ?>templates/<?= APP_TEMPLATE ?>/js/manager.js?version=<?= time() ?>"></script>

<script src="<?= HTTP.HOST ?>templates/<?= APP_TEMPLATE ?>/js/support.js?version=<?= time() ?>"></script>
<script src="<?= HTTP.HOST ?>templates/<?= APP_TEMPLATE ?>/js/calendar.js?version=<?= time() ?>"></script>
<!--
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
-->

<script>
    $(document).ready(function(){
        var timer = setInterval(() => {
            var controler = "Helpers.Session";
            var data = {
                'controler'		:	controler,
                'function'		:	'expired'
            };
            
            $.ajax({
                type		: 	"POST",
                url			: 	"pages/default/ajax/ajax.php",
                data		:	data,
                dataType	: 	"json",
            }).done(function(response){
                if(response.msg == 'Error'){
                    location.reload();
                }
            }).fail(function(xhr) {
                alert("Error");
                console.log(xhr.responseText);
                $("#preloader").remove();
            });           
        }, 1000);

    });
</script>

</body>
</html>