<html>
<body>
  <head>
      <link rel = "stylesheet" type ="text/css" href = "./css/loginpage.css">
  </head>

<div class="container">
    <div class="body-login">
        <div class="col-md-8">
            <div class="card">
              <!-- Picture part -->
              <div class="pt-4">
                <img class="cisco-vms_pic" src="./image/cisco-logo.png" >
              </div>
                <div class="card-body">
                    <form method="POST" action="">
                        @csrf
                      <!-- Email part -->
                        <div class="form-row-style">
                            <label for="username" class="col-md-4 col-form-label text-md-right">
                              <img class="pic-icon" src="./image/male.png"></label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>


                                    <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                      
                            </div>
                        </div>
                        <!-- Password part -->
                        <div class="form-row-style">
                            <label for="password" class="col-md-4 col-form-label text-md-right"><img  class="pic-icon" src="./image/lock.png"></label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Forgot password part -->
                        <div class="form-row-sty mb-0">
                            <div class="button-style-container">


                                <button type="submit" class="login-button">
                                    {{ __('Login') }}
                                </button>
                                <!--Remember part-->
                                <div class="form-group row">
                                    <div class="remember-me col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                @if (Route::has('password.request'))
                                    <a class="forget-password" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</body>
</html>
