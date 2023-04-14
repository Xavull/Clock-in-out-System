<?php
session_start();
if(isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0){
    header("Location:./");
    exit;
}
require_once('../DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | Investhood Online Attendance System</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/script.js"></script>
    <style>
        html, body{
            height:100%;
        }
        .card {
           position: relative;
           display: flex;
           flex-direction: column;
           min-width: 0;
           word-wrap: break-word;
           background-color: rgb(193, 193, 214);
           background-clip: border-box;
           border: 3px solid rgba(0, 0, 0, 0.125);
           color: white;
           font-size: large;
           border-radius: 0.25rem;
           background: border-box;
           border-color: red;
           border-width: thick;
       } 
        .bg-video {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            overflow: hidden;
            z-index: -1;
        }
        .bg-video video {
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
        }
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100%;
        }
        .card {
            width: 100%;
            max-width: 400px;
        }
        img.logo {
            max-height: 400px;
        }
    </style>
</head>
<body class="bg-dark bg-gradient">
   <div class="bg-video">
       <video autoplay muted loop>
           <source src="../Videos/Office .mp4" type="video/mp4">
       </video>
   </div>
   <div class="center">
       <img src="../Images/logo.png" class="logo mb-4">
       <h3 class="py-5 text-center text-light">Investhood (admin) Attendance System</h3>
       <div class="card my-3">
           <div class="card-body">
               <form action="" id="login-form">
                   <center><small>Please Enter Your Credentials.</small></center>
                   <div class="form-group">
                       <label for="username" class="control-label">Username</label>
                       <input type="text" id="username" autofocus name="username" class="form-control form-control-sm rounded-0" required>
                   </div>
                   <div class="form-group">
                       <label for="password" class="control-label">Password</label>
                       <input type="password" id="password" name="password" class="form-control form-control-sm rounded-0" required>
                   </div>
                   <div class="form-group d-flex justify-content-center">
                       <button class="btn btn-sm btn-primary rounded-0 my-1">Login</button>
                   </div>
               </form>
           </div>
       </div>
   </div>
</body>
<script>
    $(function(){
        $('#login-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Loging in...')
            $.ajax({
                url:'./../Actions.php?a=login',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.replace('./');
                        }, 2000);
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
</html>