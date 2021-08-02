<?php
header( 'Content-type: text/html; charset=utf-8' );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Documento sin título</title>
<style>
    .progreso { width: 100px; height: 20px; border:1px solid black; float:left;}
    .avance { height:20px; float:left; background: red; }
</style>
</head>
<body>
<div class="progreso">
    <div class="barra">
    <?php
        for($i=0;$i<10;$i++)
        {
            echo '<span style="width:10px;" class="avance"></span>';
            flush();
            ob_flush();
            sleep(5);
        }
    ?>
    </div>
</div>
</body>
</html>