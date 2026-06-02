<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
   <title>Login | Royal Canin</title>
   
   <!----------------- fonts ------------------------>
   <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font.css') }}">
   
   <!----------------- stylesheets ------------------------>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
   <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/login.min.css') }}">
</head>

<body>

<!-- Header with Royal Canin Logo -->
<header class="header py-4">
   <div class="container">
      <div class="row justify-content-end">
         <div class="col-auto">
            <img src="{{ asset('assets/img/rc-logo.png') }}" class="img-fluid royal-canin-logo" alt="Royal Canin Logo">
         </div>
      </div>
   </div>
</header>

<!-- Main Login Section -->
<main class="login-main">
   <div class="container-fluid">
      <div class="row g-0 min-vh-75 align-items-center">
         
         <!-- CENTER COLUMN: Login Box -->
         <div class="col-lg-4 col-md-7 col-12 order-lg-2 order-md-2 order-1">
            <div class="login-wrapper">
               
               <!-- Top Logo Section -->
               <div class="login-header mb-4">
                  <div class="d-flex align-items-center justify-content-center">
                     <!-- GI Horizons Logo -->
                     <div class="gi-logo-wrapper me-3">
                        <img src="{{ asset('assets/img/logo-title.png') }}" class="img-fluid gi-logo" alt="GI Horizons Logo">
                     </div>
                  </div>
               </div>

               <!-- Login Form Container -->
               <div class="login-box">
                  <h2 class="mb-4">LOGIN</h2>
                  
                  <div id="message" class="mb-3"></div>
                  
                  <form id="login-form" class="form" action="{{ route('login.process') }}" method="POST">
    @csrf
    
    <div class="mb-4">
        <label class="form-label">Email Address</label>
        <input type="email" name="email_id" class="form-control custom-input" placeholder="Email Address" value="{{ old('email_id') }}" required>
    </div>
    
    <div class="mt-4">
        <input type="submit" class="btn btn-site w-100" name="submit" value="Send Login Link">
    </div>
</form>
                  </form>
               </div>

               <!-- Registration Link -->
               <div class="text-center mt-4">
                  <p class="redirect-link mb-0">If not registered yet, <a href="{{ route('register') }}" title="">click here</a></p>
               </div>

            </div>
         </div>

      </div>
   </div>
</main>

<!--------------- important javascript  ---------------->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
   $('document').ready(function() {
      $('.alert').hide();
      
      // CSRF token setup for AJAX
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      
      /* ---------------------------------------
      # --------- LOGIN FORM VALIDATION -------
      ----------------------------------------- */
      $('#login-form').validate({
         rules: {
            email_id: {
               required: true,
               email: true,
            }
         },
         messages: {
            email_id: {
               required: '* Please enter your Registered Email ID',
               email: '* Please enter valid email',
            }
         },
         submitHandler: function(form) {
            $.ajax({
                  url: $(form).attr('action'),
                  type: 'POST',
                  data: $(form).serialize(),
                  beforeSend: function() {
                     $('#message').html('<div class="alert alert-info"><img src="{{ asset('assets/img/loader.gif') }}" width="40px" /> &nbsp; Verifying ... </div>');
                  },
                  success: function(response) {
                  if (response.success) {
                     $('#message').html('<div class="alert alert-success">Login successful! Redirecting...</div>');
                     window.setTimeout(function() {
                           window.location.href = 'webcast';
                     }, 1000);
                  }
               },
               error: function(xhr) {
                  var errorMsg = 'An error occurred. Please try again.';
                  if (xhr.responseJSON && xhr.responseJSON.message) {
                     errorMsg = xhr.responseJSON.message;
                  }
                  $('#message').html('<div class="alert alert-danger">' + errorMsg + '</div>');
               }
            });
         }
      });
   });
</script>

</body>
</html>