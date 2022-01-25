<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
@if ($email)
    <div class="row" id="registrationForm">
    <div class="col-lg-4 offset-lg-4 col-12"><div class="alert alert-primary" role="alert">
       Please Register!
    </div>
    <form class="shadow" style="border-radius: 10px;padding: 1rem;">
    
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">UserName</label>
                <input type="text" class="form-control" id="name" aria-describedby="emailHelp">
                <div id="nameError" class="form-text" style="color: red;display: none;" >This field is required</div>
                
              </div>
            <div class="mb-3">
             
              <input type="email" hidden value="{{$email}}" disabled class="form-control" id="email" aria-describedby="emailHelp">
              
            
            </div>
           
            
            <div class="mb-3">
              <label for="exampleInputPassword1" class="form-label">Password</label>
              <input type="password" class="form-control" id="password">
              
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password">
              </div>
              <div id="passwordError" class="form-text" style="color: red;display: none;">password and confirm password doesn't match</div>

           
            <button data-url="{{ route('confirm_registration') }}" id="signup" style="background:green;border:1px solid green;margin-top: 2rem;" type="submit" class="btn btn-primary">Signup</button>
            <div class="spinner-border text-success" role="status" style="display: none;margin-top: 2rem;" id="spinner">
                <span class="visually-hidden">Loading...</span>
              </div>
          </form>
    </div>
</div>

<div class="row" id="verificationForm" style="display:none">
    <div class="col-lg-4 offset-lg-4 col-12"><div class="alert alert-primary" role="alert" id="verificationMessage">
      
    </div>
    <form class="shadow" style="border-radius: 10px;padding: 1rem;">
    <div id="errorCode" class="form-text" style="color: red;display: none;" ></div>
    
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Code</label>
                <input type="text" class="form-control" id="code" aria-describedby="emailHelp">
                <div id="codeError" class="form-text" style="color: red;display: none;" >This field is required</div>
                
              </div>
      
           
            <button data-url="{{ route('verify_code') }}" id="verify" style="background:green;border:1px solid green;margin-top: 2rem;" type="submit" class="btn btn-primary">Verify</button>
            <div class="spinner-border text-success" role="status" style="display: none;margin-top: 2rem;" id="spinner2">
                <span class="visually-hidden">Loading...</span>
              </div>
          </form>
    </div>
</div>

<div class="row" id="registration_success" style="display:none">
  <div class="col-lg-4 offset-lg-4 col-8 offset-2" style="text-align:center,margin-top:2rem">
      <p>Thanks For Registering with us.</p>
  </div>
</div>
<script>
    let hasValidate = false;
    $(document).ready(function(){
        $('#signup').click(function(){
            
            $spinner =  $('#spinner');
            $(this).fadeOut(0,function(){
              $spinner.fadeIn();
            })
            event.preventDefault()
            // getting inputs
            $email = $('#email').val()
            $name = $('#name').val()
            $number = $('#number').val()
            $password = $('#password').val()
            $role = $('#role').val()
            $confirm_password = $('#confirm_password').val()

            // errors
           
            $nameError = $('#nameError');
            $passwordError = $('#passwordError');

            if(hasValidate){
        
            $nameError.hide()
            $passwordError.hide()
            }
 
            isValid = true;
            hasValidate = true
            

            if(!$name){
               isValid = false
               $nameError.show()
            }


            

            if($password != $confirm_password){
              
               isValid = false
               $passwordError.show()
            }

           
            if(!isValid){
                
                $spinner.fadeOut(0,function(){
                    $('#signup').fadeIn();
                })
                return False
            }

           
         
        
            const data = {
                'user_name':$name,
                'email':$email,
                'password':$password,
                'password_confirmation':$confirm_password
            }

            $.ajax({
                url:$(this).data('url'),
                type:'POST',
                data:data,
                success:function(response){
                  hasValidate = false;
                  $spinner.fadeOut();
                  $('#verificationMessage').text(response.message);
                  $('#registrationForm').fadeOut(0,function(){
                      $('#verificationForm').fadeIn(0);
                  })
               
                },
                error:function(error){
                
                  $('#spinner').fadeOut(0,function(){
                      $('#signup').fadeIn(0)
                  })

                
                  try {
                  $error = $('#error')
                  $error.text(error.responseJSON.message)
                  $error.fadeIn()
                  } catch (error) {
                      //
                  }
                 
                  
                }
            })




            
        })

        $('#verify').click(function(){
            event.preventDefault()
            $code = $('#code').val();
            $codeError = $('#codeError');
            $spinner =  $('#spinner2');
            $(this).fadeOut(0,function(){
              $spinner.fadeIn();
            })
            if(!$code){
                $codeError.fadeIn(0);
            }

            if(hasValidate){
        
              $codeError.fadeOut();
                   }

            isValid = true;
            hasValidate = true
        

            if(!$code){
                $codeError.fadeIn(0);
                isValid = false
            }

            if(!isValid){
                
                $spinner.fadeOut(0,function(){
                    $('#verify').fadeIn();
                })
                return False
            }

            const data = {
                'code':$code
            }
            $.ajax({
                url:$(this).data('url'),
                type:'POST',
                data:data,
                success:function(response){
                $spinner.fadeOut();
                 $('#verificationForm').fadeOut(0,function(){
                     $('#registration_success').fadeIn();
                 });
               
                },
                error:function(error){
                    $('#spinner2').fadeOut(0,function(){
                      $('#verify').fadeIn(0)
                  })
                 $('#errorCode').text(error.message);
                 $('#errorCode').fadeIn();
                 console.log(error.responseJSON)
                  
                }
            })









        })
    })
</script>
@else
<div class="row" id="registration_success" style="display:none">
  <div class="col-lg-4 offset-lg-4 col-8 offset-2" style="text-align:center,margin-top:2rem">
      <p>Seems link is broken. Please check email for full link</p>
  </div>
</div>

@endif


</body>
</html>