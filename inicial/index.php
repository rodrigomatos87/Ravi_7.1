<?
    require '../conexao.php';   

    $d = mysqli_query($db, "SELECT * FROM login");
    if(mysqli_num_rows($d) > 0) {
        header("Location: ../login.php");
    }

?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8" />
        <title id="ttitle">Ravi Monitoramento - Começando</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Premium Bootstrap v5.0.2 Landing Page Template" />
        <meta name="keywords" content="bootstrap v5.0.2, premium, marketing, multipurpose" />
        <meta content="Pichforest" name="author" />

        <!-- favicon -->
        <link rel="shortcut icon" href="../img/favicon.ico" />

        <!-- Bootstrap css -->
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <!-- Custom Css -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="../css/intlTelInput.css" rel="stylesheet" type="text/css" />
        <script>
            function langa(id) {
                //alert(id);
                location.hash = id;
                location.reload();
            }
        </script>
        <style>

        </style>
    </head>

    <body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="51">

        <!-- Pre-loader -->
        <div id="preloader">
            <div id="status">
                <div class="spinner">Carregando...</div>
            </div>
        </div>
        <!-- End Preloader-->

        <!-- START HOME -->
        <section class="bg-home5" id="home" style="padding-top: 60px !important">
            <div class="container">
                <div class="position-relative" style="z-index: 1;">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="me-4">
                                <img src="../img/Logo.png" width="150px" style="padding-bottom: 20px;"><br>
                                <h1 class="mb-4" id="mPrincipal">Seja bem vindo ao <span class="text-primary">Ravi</span>  Monitoramento</h1>
                                <p class="text-muted fs-17" id="mPrincipal1">Crie seu usuário inicial para acesso ao sistema, rápido, fácil e seguro!</p>
                                <p class="text-muted fs-17" id="mPrincipal2">O usuário será <strong>Master</strong> e terá acesso completo ao sistema.</p>
                                <div class="mt-4">
                                    <a href="https://api.whatsapp.com/send?phone=5544997544283&text=Ol%c3%a1%2c+tenho+duvidas+sobre+o+sistema+de+monitoramento+Ravi" target="_blank" class="btn btn-primary mt-2" id="btDuvidas">Dúvidas?</a>
                                    <!--
                                    <a class="btn btn-primary ms-sm-1 image-popup mt-2" href="https://www.w3schools.com/html/mov_bbb.mp4"><svg width="24" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video icons"><g><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></g></svg> Watch Now</a>
                                    -->
                                </div>
                            </div>
                        </div><!--end col-->
                        <div class="col-lg-5 ms-auto">
                            <div class="subscribe-form box-shadow mt-4 mt-lg-0">
                                <form id="ajax_form" action="#">
                                    <input type="hidden" class="form-control" id="grupo" name="grupo" value="1">
                                    <input type="hidden" name="login" id="login" value=""/>
                                    <div class="mb-4 position-relative">
                                        <label for="linguagem" class="form-label">Linguagem*</label>
                                        <select class="form-control" name="linguagem" id="linguagem" onchange="javascript:langa(this.value)">
                                            <option value="P" selected>Português</option>
                                            <option value="I">Inglês</option>
                                            <option value="E">Espanhol</option>
                                        </select>
                                    </div>
                                    <div class="mb-4 position-relative">
                                        <label id="labelNome" for="nome" class="form-label">Nome*</label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome">
                                    </div>
                                    <div class="mb-4 position-relative">
                                        <label id="labelUsuario" for="usuario" class="form-label">Usuário*</label>
                                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Seu usuário de acesso">
                                    </div>
                                    <div class="mb-4 position-relative">
                                        <label id="labelSenha" for="senha" class="form-label">Senha*</label>
                                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha">
                                    </div>
                                    <div class="mb-4 position-relative">
                                        <label id="labelCsenha" for="csenha" class="form-label">Confirmar Senha*</label>
                                        <input type="password" class="form-control" id="csenha" name="csenha" placeholder="Confirme sua senha">
                                    </div>
                                    <div class="mb-4 position-relative">
                                        <label id="labelTelefone" for="telefone" class="form-label" style="z-index: 1;">Telefone*</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone">
                                    </div>
                                    <div class="mb-4 position-relative">
                                        <label id="labelEmail" for="eemail" class="form-label">Email*</label>
                                        <input type="email" class="form-control" id="eemail" name="eemail" placeholder="Seu e-mail">
                                    </div>
                                    <div class="pt-2">
                                        <button type="submit" class="btn btn-primary w-100" id="btContinuar">Continuar</button>
                                    </div>
                                    <input id="selFone" name="selFone" type="hidden" value="">
                                </form>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            <!--end container-->
        </section><!-- END HOME -->
        <!-- START SHAPE -->
        <div class="position-relative">
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="1440" height="150" preserveAspectRatio="none" viewBox="0 0 1440 250">
                    <g mask="url(&quot;#SvgjsMask1036&quot;)" fill="none">
                        <path d="M 0,214 C 96,194 288,120.8 480,114 C 672,107.2 768,201.4 960,180 C 1152,158.6 1344,41.6 1440,7L1440 250L0 250z" fill="rgba(255, 255, 255, 1)"></path>
                    </g>
                    <defs>
                        <mask id="SvgjsMask1036">
                            <rect width="1440" height="250" fill="#ffffff"></rect>
                        </mask>
                    </defs>
                </svg>
            </div>
        </div>
        <!-- END SHAPE -->

        <!-- App Js -->
        <script src="js/app.js"></script>

    </body>
    <script src="../js/jquery-3.5.1.js"></script>
    <script src="../js/jquery.maskedinput.min.js"></script>
    <script src="../js/jquery.validate.js"></script>
    <script src="../js/intlTelInput.js"></script>
    <script>
        /*
        jQuery("#telefone")
            .mask("(99) 9999-9999?9")
            .focusout(function (event) {
                var target, phone, element;
                target = (event.currentTarget) ? event.currentTarget : event.srcElement;
                phone = target.value.replace(/\D/g, '');
                element = $(target);
                element.unmask();
                if(phone.length > 10) {
                    element.mask("(99) 99999-999?9");
                } else {
                    element.mask("(99) 9999-9999?9");
                }
            });
            */

            function enviar() {
                var data = jQuery('#ajax_form').serialize();
                $.ajax({
                    url: '../envialogin.php',
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        var valor = response.valueOf();
                        if (valor.length == 54) {
                            var url = "../index.php";
                            $(location).attr('href', url);
                        }
                    }
                })
            }
    
        $(document).ready(function () {
            $.validator.setDefaults({
                submitHandler: function() {
                    var dados = jQuery('#ajax_form').serialize();
    
                    $.ajax({
                        url: '../salvarUser.php?l=s',
                        type: 'POST',
                        data: dados,
                        success: function (response) {
                            if(response.indexOf('a') !== -1) {
                                document.getElementById("login").value = document.getElementById("usuario").value;
                                //alert(document.getElementById("login").value);
                            }
    
                        },
                    }).done(function(data){
                        enviar();
                    });
                }
            });
    
            // validate signup form on keyup and submit
            if (window.location.hash) {
                if (window.location.hash == "#I") {
                    $("#ajax_form").validate({
                        rules: {
                            senha: {
                                required: true,
                                minlength: 5
                            },
                            csenha: {
                                equalTo: "#senha"
                            },
                            nome: {
                                required: true,
                            },
                            usuario: {
                                required: true,
                            },
                            telefone: {
                                required: true,
                            },
                            eemail: {
                                required: true,
                                email: true
                            },
                        },
                        messages: {
                            nome: "<font color=\"#FF4D4D\">* Mandatory</font>",
                            usuario: "<font color=\"#FF4D4D\">* Mandatory</font>",
                            senha: "<font color=\"#FF4D4D\">* Minimum 5 characters</font>",
                            csenha: "<font color=\"#FF4D4D\">* The field is different from the password</font>",
                            telefone: "<font color=\"#FF4D4D\">* Mandatory</font>",
                            eemail: "<font color=\"#FF4D4D\">* Mandatory</font>",
                        }
                    });
                } else if (window.location.hash == "#E") {
                    $("#ajax_form").validate({
                        rules: {
                            senha: {
                                required: true,
                                minlength: 5
                            },
                            csenha: {
                                equalTo: "#senha"
                            },
                            nome: {
                                required: true,
                            },
                            usuario: {
                                required: true,
                            },
                            telefone: {
                                required: true,
                            },
                            eemail: {
                                required: true,
                                email: true
                            },
                        },
                        messages: {
                            nome: "<font color=\"#FF4D4D\">* Obligatorio</font>",
                            usuario: "<font color=\"#FF4D4D\">* Obligatorio</font>",
                            senha: "<font color=\"#FF4D4D\">* Minimo 5 caracteres</font>",
                            csenha: "<font color=\"#FF4D4D\">* El campo es diferente de la contraseña.</font>",
                            telefone: "<font color=\"#FF4D4D\">* Obligatorio</font>",
                            eemail: "<font color=\"#FF4D4D\">* Obligatorio</font>",
                        }
                    });           
                } else if (window.location.hash == "#P") {
                    $("#ajax_form").validate({
                        rules: {
                            senha: {
                                required: true,
                                minlength: 5
                            },
                            csenha: {
                                equalTo: "#senha"
                            },
                            nome: {
                                required: true,
                            },
                            usuario: {
                                required: true,
                            },
                            telefone: {
                                required: true,
                            },
                            eemail: {
                                required: true,
                                email: true
                            },
                        },
                        messages: {
                            nome: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                            usuario: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                            senha: "<font color=\"#FF4D4D\">* Minimo 5 caracteres</font>",
                            csenha: "<font color=\"#FF4D4D\">* O campo esta diferente da senha</font>",
                            telefone: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                            eemail: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                        }
                    });           
                }
            } else {
                $("#ajax_form").validate({
                    rules: {
                        senha: {
                            required: true,
                            minlength: 5
                        },
                        csenha: {
                            equalTo: "#senha"
                        },
                        nome: {
                            required: true,
                        },
                        usuario: {
                            required: true,
                        },
                        telefone: {
                            required: true,
                        },
                        eemail: {
                            required: true,
                            email: true
                        },
                    },
                    messages: {
                        nome: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                        usuario: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                        senha: "<font color=\"#FF4D4D\">* Minimo 5 caracteres</font>",
                        csenha: "<font color=\"#FF4D4D\">* O campo esta diferente da senha</font>",
                        telefone: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                        eemail: "<font color=\"#FF4D4D\">* Obrigatório</font>",
                    }
                });
            }
        });

        var language = {
                P: {
                    nome: "Nome*",
                    usuario: "Usuário*",
                    senha: "Senha*",
                    csenha: "Confirmar senha*",
                    telefone: "Telefone",
                    continuar: "Continuar",
                    duvidas: "Dúvidas?"
                },
                I: {
                    nome: "Name*",
                    usuario: "User*",
                    senha: "Password*",
                    csenha: "Confirm password*",
                    telefone: "Phone",
                    continuar: "Continue",
                    duvidas: "Help?"
                },
                E: {
                    nome: "Nombre*",
                    usuario: "Usuario*",
                    senha: "Clave*",
                    csenha: "Confirmar seña*",
                    telefone: "Teléfono",
                    continuar: "Continuar",
                    duvidas: "¿Dudas?"
                }
            };

        if (window.location.hash) {
                //alert(window.location.hash);
                if (window.location.hash == "#I") {
                    labelNome.textContent = language.I.nome;
                    $('#nome').attr('placeholder', 'Your name');
                    labelUsuario.textContent = language.I.usuario;
                    $('#usuario').attr('placeholder', 'Your login user');
                    labelSenha.textContent = language.I.senha;
                    $('#senha').attr('placeholder', 'Your password');
                    labelCsenha.textContent = language.I.csenha;
                    $('#csenha').attr('placeholder', 'Confirm your password');
                    labelTelefone.textContent = language.I.telefone;
                    //$('#telefone').attr('placeholder', 'Confirm your password');
                    $('#eemail').attr('placeholder', 'Your e-mail');
                    btContinuar.textContent = language.I.continuar;
                    document.getElementById("linguagem").value = "I";
                    document.getElementById("mPrincipal").innerHTML = 'Welcome to <span class="text-primary">Ravi</span> Monitoring';
                    document.getElementById("mPrincipal1").innerHTML = 'Create your initial user for quick, easy and secure system access!';
                    document.getElementById("mPrincipal2").innerHTML = 'The user will be <strong>Master</strong> and will have full access to the system.';
                    btDuvidas.textContent = language.I.duvidas;
                    document.getElementById("linguagem").innerHTML = '<option value="P">Portuguese</option><option value="I" selected>English</option><option value="E">Spanish</option>';
                    ttitle.textContent = 'Ravi Monitoring - Getting Started';

                    var input = document.querySelector("#telefone");
                        window.intlTelInput(input, {
                        initialCountry: "us",
                        utilsScript: "../js/utils.js",
                    });

                    selFone.value = "us";
                    
                }
                else if (window.location.hash == "#E") {
                    labelNome.textContent = language.E.nome;
                    $('#nome').attr('placeholder', 'su nombre');
                    labelUsuario.textContent = language.E.usuario;
                    $('#usuario').attr('placeholder', 'Su usuario de inicio de sesión');
                    labelSenha.textContent = language.E.senha;
                    $('#senha').attr('placeholder', 'Su contraseña');
                    labelCsenha.textContent = language.E.csenha;
                    $('#csenha').attr('placeholder', 'Confirmar la contraseña');
                    labelTelefone.textContent = language.E.telefone;
                    //$('#telefone').attr('placeholder', 'su teléfono');
                    $('#eemail').attr('placeholder', 'Su e-mail');
                    btContinuar.textContent = language.E.continuar;
                    document.getElementById("linguagem").value = "E";
                    document.getElementById("mPrincipal").innerHTML = 'Bienvenido a <span class="text-primary">Ravi</span> Monitoreo';
                    document.getElementById("mPrincipal1").innerHTML = '¡Cree su usuario inicial para un acceso rápido, fácil y seguro al sistema!';
                    document.getElementById("mPrincipal2").innerHTML = 'El usuario será <strong>Maestro</strong> y tendrá pleno acceso al sistema.';
                    btDuvidas.textContent = language.E.duvidas;
                    document.getElementById("linguagem").innerHTML = '<option value="P">Portugués</option><option value="I">Inglés</option><option value="E" selected>Español</option>';
                    ttitle.textContent = 'Monitoreo de Ravi - Primeros pasos';

                    var input = document.querySelector("#telefone");
                        window.intlTelInput(input, {
                        initialCountry: "es",
                        utilsScript: "../js/utils.js",
                    });

                    selFone.value = "es";
                }
                else if (window.location.hash == "#P") {
                    labelNome.textContent = language.P.nome;
                    $('#nome').attr('placeholder', 'Seu nome');
                    labelUsuario.textContent = language.P.usuario;
                    $('#usuario').attr('placeholder', 'Seu usuário de acesso');
                    labelSenha.textContent = language.P.senha;
                    $('#senha').attr('placeholder', 'Sua senha');
                    labelCsenha.textContent = language.P.csenha;
                    $('#csenha').attr('placeholder', 'Confirme sua senha');
                    labelTelefone.textContent = language.P.telefone;
                    //$('#telefone').attr('placeholder', 'Seu telefone');
                    $('#eemail').attr('placeholder', 'Seu e-mail');
                    btContinuar.textContent = language.P.continuar;
                    document.getElementById("linguagem").value = "P";
                    document.getElementById("mPrincipal").innerHTML = 'Seja bem vindo ao <span class="text-primary">Ravi</span> Monitoramento';
                    document.getElementById("mPrincipal1").innerHTML = 'Crie seu usuário inicial para acesso ao sistema, rápido, fácil e seguro!';
                    document.getElementById("mPrincipal2").innerHTML = 'O usuário será <strong>Master</strong> e terá acesso completo ao sistema.';
                    btDuvidas.textContent = language.P.duvidas;
                    document.getElementById("linguagem").innerHTML = '<option value="P" selected>Português</option><option value="I">Inglês</option><option value="E">Espanhol</option>';
                    ttitle.textContent = 'Ravi Monitoramento - Começando';

                    var input = document.querySelector("#telefone");
                        window.intlTelInput(input, {
                        initialCountry: "br",
                        utilsScript: "../js/utils.js",
                    });

                    selFone.value = "br";
                }
            } else {
                var input = document.querySelector("#telefone");
                window.intlTelInput(input, {
                // allowDropdown: false,
                // autoHideDialCode: false,
                // autoPlaceholder: "off",
                // dropdownContainer: document.body,
                // excludeCountries: ["us"],
                // formatOnDisplay: false,
                // geoIpLookup: function(callback) {
                //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                //     var countryCode = (resp && resp.country) ? resp.country : "";
                //     callback(countryCode);
                //   });
                // },
                // hiddenInput: "full_number",
                initialCountry: "br",
                // localizedCountries: { 'de': 'Deutschland' },
                // nationalMode: false,
                // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                // placeholderNumberType: "MOBILE",
                // preferredCountries: ['cn', 'jp'],
                // separateDialCode: true,
                utilsScript: "../js/utils.js",
                });

                selFone.value = "br";
            }
    </script>
</html>