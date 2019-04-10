<html>
<head>

</head>
<body>
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-5">
                <div class="user-form">
                    <form action="/loginMobile" method="get" id="form">
                        {{ csrf_field() }}
                        {{--                            <h3> @if($warning) {{$warning}} @else Login to Start @endif</h3><br><br>--}}
                        {{-- <a class="social-btn facebook-btn"  href="{{route('user.facebook')}}">Continue with Facebook</a>
                        <hr class="or" />--}}
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="code" id="code" />
                        <input type="hidden" id="country" value="+880" required autocomplete="off" />
                        <div class="form-group phone">
                            <input class="form-control" autocomplete="off" required id="phone" name="phone" type="text" placeholder="Mobile Number">
                        </div>
                        <div class="error-validation p-2" style="display: none"></div>
                        @if (count($errors) > 0)
                            <div class="error-validation">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>* {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </form>
                    <button onclick="userExist()" class="common-btn">Log In</button>


                    {{--<div class="login-link">

                        <p>Don't have Romoni account?</p>
                        <a href="{{route('user.registration')}}">Signup</a>
                    </div>--}}
                    <div style="display: flex;margin-top: 1rem">

                        <p>Don't have Romoni account?&nbsp&nbsp</p>
                        <a class="signup-txt" href="###">Signup</a>
                    </div>
                </div>
            </div>
            {{--<form class="pt-3" method="POST"  action="/login">
                           @csrf

                           <div class="form-group">
                               <input type="number" name="phone" class="form-control form-control-lg" placeholder="Phone">
                           </div>

                           <div class="form-group">
                               <button type="submit" class="btn btn-romoni btn-block">Log in</button>
                           </div>

           </form>--}}

        </div>
    </div>
</div>


</body>

<script type="text/javascript" src="https://sdk.accountkit.com/en_US/sdk.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>

    AccountKit_OnInteractive = function() {
        AccountKit.init({
            appId: "{{ env('Account_kit_id') }}",
            state: document.getElementById('_token').value,
            version: 'v1.0',
            xfbml       : true
        });
    };

    function loginCallback(response) {
        console.log(response);

        if (response.status === "PARTIALLY_AUTHENTICATED") {
            document.getElementById('code').value = response.code;
            document.getElementById('_token').value = response.state;
            document.getElementById('form').submit();
        }

        else if (response.status === "NOT_AUTHENTICATED") {
            // handle authentication failure
            alert('You are not Authenticated');
        }
        else if (response.status === "BAD_PARAMS") {
            // handle bad parameters
            alert('wrong inputs');
        }
    }



    // phone form submission handler
    function smsLogin() {
        var countryCode = document.getElementById('country').value;
        var phoneNumber = document.getElementById('phone').value;
        AccountKit.login(
            'PHONE',
            {countryCode: countryCode, phoneNumber: phoneNumber},
            document.get
        );
    }
    // email form submission handler
    function emailLogin() {
        var emailAddress = document.getElementById("email").value;
        AccountKit.login('EMAIL', {emailAddress: emailAddress}, loginCallback);
    }

    function userExist() {


        let phoneNumber = document.getElementById('phone').value;
        console.log(phoneNumber);
        axios.post('/checkIfExist',{
            user: phoneNumber,
            "_token": "{{ csrf_token() }}",
        })
            .then(response =>
            {
                console.log(response.data === true);
                if(response.data === true)
                {
                    smsLogin();
                }
                else
                {
                    $('.error-validation').show();
                    $('.error-validation').text('* Register First');
                }

            }).catch(error => {
            console.log(error.response)
        });
    }

</script>

</html>

