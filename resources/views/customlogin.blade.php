<html>
<head>
    <link
        rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
        crossorigin="anonymous"
    >
</head>
<body>
<div class="p-5">
    <div id="app">

        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

       <h3>Facebook account kit integration.</h3>
        <div class="d-flex">
            <div class="text-success pr-0 mr-0 mt-1">+88</div>
            <div class="pl-0 ml-1">
                <input v-model="phone" placeholder="Enter Phone number">
                <button @click="smsLogin()" :disabled="warningBtn.length>0">Login</button>
                <div :class="warningBtn"> @{{ isnumbervalid }}</div>
            </div>
        </div>



    </div>

</div>


</body>

<script type="text/javascript" src="https://sdk.accountkit.com/en_US/sdk.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/vue"></script>
<script>
    AccountKit_OnInteractive = function() {
        console.log('AccountKit_OnInteractive called');
        AccountKit.init({
            appId: parseInt( "{{ env('FB_ACCOUNTKIT_APP_ID') }}"),
            state: document.getElementById('_token').value,
            version: 'v1.1',
            xfbml       : true
        });
    };
/*
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
            loginCallback
        );
    }

*/

</script>

<script>
    const csrf_token = document.getElementById('_token');

    const vue = new  Vue({
        el: '#app',
        data() {
            return {
                test: 500,
                phone: '',
                warningBtn: ''
            }
        },
        computed: {
           isnumbervalid() {
               if(this.phone.length === 0) {
                   this.warningBtn = 'text-primary';
                   return 'Enter Phone Number';
               }
               const rule = /(^()?(01){1}[23456789]{1}(\d){8})$/i.test(this.phone);
               if (this.phone.length < 11) {
                   this.warningBtn = 'text-success';
                   return 'Enter 11 digits'
               }

               if (rule === false) {
                   this.warningBtn = 'text-danger';
                   return 'Enter valid number';
               }
               this.warningBtn = '';
           }
        },
        methods: {
            smsLogin() {
               const countryCode = '+880';
               const phoneNumber = this.phone.slice(1, this.phone.length);
               AccountKit.login(
                    'PHONE',
                    {
                      countryCode: countryCode,
                      phoneNumber: phoneNumber
                    },
                    this.loginCallback
                );
            },
            loginCallback(response) {
                // console.log('loginCallback response  ===== ', response, 'csrf_token_val');
                const PROJECT_URL = '{{ env('VHOST_URL_BY_NGROK') }}';
                if (response.status === "PARTIALLY_AUTHENTICATED") {
                    const code = response.code;
                    const state = response.state;
                    const formvalues = { code: code, state: state };
                    console.log('Form value === ', formvalues);

                    axios.post( `${PROJECT_URL}/api/otp-login`, formvalues)
                        .then(res => {
                            console.log('Result ======== ', res);
                            /*
                                Here,
                                we will determine if we give the user access or not,
                                by storing his token in localstorage.
                             */
                        }).catch(err => {
                            console.log('Error occured ====== ', err.response);
                    });

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
        }
    });
</script>

</html>

