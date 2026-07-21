<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive Admin Dashboard Template">
    <meta name="keywords" content="admin,dashboard">
    <meta name="author" content="stacks">
    <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <!-- Title -->
    <title>跳转提示</title>
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="__Home__/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__Home__/plugins/font-awesome/css/all.min.css" rel="stylesheet">
    <!-- Theme Styles -->
    <link href="__Home__/css/lime.min.css" rel="stylesheet">
    <link href="__Home__/css/custom.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    <script src="__Home__/js/jquery-3.1.1.min.js"></script>
    <script>
    var wait = <?php echo($wait);?>;
    $(document).ready(function() {
        returnPage();
    });

    function returnPage() {
        if (wait == 0) {
            
            window.location.href = '<?php echo($url);?>';
        } else {
            
            $("#redirectUrlBtn").html("   " + wait + "秒后自动跳转!");
            wait--;
            setTimeout(function() {
                    returnPage();
                },
                1000)
        }
    }
    </script>
</head>
<body>
    {include file="index@public/menu"}
    {include file="index@public/header"}
<div class="lime-container">
        <div class="lime-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 15%">
                        <?php switch ($code) {?>
                        <?php case 1:?>
                        <!-- 成功 -->
                        
                        <center><img src="__Home__/images/success.png" style="text-align:center"></center>
                        <center style="margin-top: 50px;">
                            <h3><?php echo(strip_tags($msg));?><br></h3>
                        </center>


                        
                        <?php break;?>
                        <?php case 0:?>
                        
                        <!-- 错误 -->
                        <center><img src="__Home__/images/error.png" style="text-align:center"></center>
                        <center style="margin-top: 50px;">
                            <h3><?php echo(strip_tags($msg));?><br></h3>
                        </center>

                       
                        <?php break;?>
                        <?php } ?>
                        
                        <center>
                            <p id="redirectUrlBtn" style="font-size: 20px;">5</p>
                            <h5>如您的浏览器未跳转请点击👉<a href="<?php echo($url);?>">跳转</a>👈</h5>
                        </center>
                        <!-- <img src="./s.png"> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="lime-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 20%">
                        <span class="footer-text">2019 © 版权所有：{$_SERVER['HTTP_HOST']}   </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>