<html>
<body>
  <head>
      <link rel = "stylesheet" type ="text/css" href = "./css/signuppage.css">
  </head>
@extends('layouts.app')

@section('content')
<div class="form-container">

          <div class="img-container">
          <img  src="./images/cisco-logo.png" >
          </div>


          <form method="POST" action="{{ route('register') }}">
              @csrf

          <div class="input-container">
               <label for="name" class="input-text-style">{{ __('Full Name:') }}</label>
              <div class="col-md-6">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  autocomplete="name" autofocus>

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
            </div>

          <div class="input-container">
            <label for="email" class="input-text-style">{{ __('E-Mail Address:') }}</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>


          <div class="input-container">
            <label for="ic_number" class="input-text-style">IC number: </label>

            <div class="col-md-6">
                <input id="ic_number" type="text" class="form-control @error('ic_number') is-invalid @enderror" name="ic_number" value="{{ old('ic_number') }}"  autocomplete="ic_number">

                @error('ic_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>


          <div class="input-container">
            <label for="phone_number" class="input-text-style">Phone number:</label>

            <div class="col-md-6">
                <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}"  autocomplete="email">

                @error('phone_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>

          <div class="input-container">
            <label for="username" class="input-text-style"> Username: </label>

            <div class="col-md-6">
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}"  autocomplete="username">

                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>
          <div class="input-container">
            <label for="password" class="input-text-style">{{ __('Password:') }}</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>

          <div class="input-container">
            <label for="password-confirm" class="input-text-style">{{ __('Confirm Password:') }}</label>

            <div class="col-md-6">
                <input id="password-confirm" type="password"  class="form-control" name="password_confirmation"  autocomplete="new-password">
            </div>
        </div>

        <div class="submit-container">
        <button type="submit" class="registerbutton-style" >
            {{ __('Register') }}
        </button>
        </div>


  </div>
  @endsection
  </body>
  </html>
