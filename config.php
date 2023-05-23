<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

include("/var/www/html/funcoes.php");

/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

if (permitUser("master") != 2) {
    header("Location: index.php");
}

$resClientes = mysqli_query($db, "SELECT * FROM system");
$fetClientes = mysqli_fetch_array($resClientes);

$resultClientes = mysqli_query($db, "SELECT * FROM login ORDER BY id ASC");

if (isset($_GET['aba'])) {
    if ($_GET['aba'] == 1) {
        $abaa = 1;
    } else if ($_GET['aba'] == 2) {
        $abaa = 2;
    } else if ($_GET['aba'] == 3) {
        $abaa = 3;
    } else if ($_GET['aba'] == 4) {
        $abaa = 4;
    } else if ($_GET['aba'] == 5) {
        $abaa = 5;
    }
} else {
    $abaa = 1;
}
?>

<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta name="author" content="Rodrigo Matos dos Santos"/>

    <?php favicon(); ?>

    <title>Ravi :: <?=$GLOBALS['lang'][1]?></title>

    <script src="js/qrious.min.js" integrity="sha512-pUhApVQtLbnpLtJn6DuzDD5o2xtmLJnJ7oBoMsBnzOkVkpqofGLGPaBJ6ayD2zQe3lCgCibhJBi4cj5wAxwVKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Code Mirror -->
    <link rel="stylesheet" href="node/codemirror/lib/codemirror.css">
    <link rel="stylesheet" href="node/codemirror/theme/liquibyte.css">
    <script src="node/codemirror/lib/codemirror.js"></script>
    <script src="node/codemirror/mode/javascript/javascript.js"></script>
    <script src="node/codemirror/addon/selection/active-line.js"></script>
    <script src="node/codemirror/addon/edit/matchbrackets.js"></script>
    <script src="node/codemirror/addon/display/autorefresh.js"></script>

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/stylenovo.css">
    <link rel="stylesheet" type="text/css" href="css/style5_4.css">
    <link rel="stylesheet" type="text/css" href="css/colorbox.css">

    <script type="text/javascript" src="js/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <script type="text/javascript" src="js/clipboard.min.js"></script>

    <link rel="Stylesheet" type="text/css" href="vendor/croppie/demo/prism.css" />
    <link rel="Stylesheet" type="text/css" href="vendor/croppie/croppie.css" />
    
    <script>
        var aba = 1;

        function mostrar_abas(obj) {
            //alert(obj.id);
            document.getElementById('div_aba1').style.display = "none";
            document.getElementById('div_aba2').style.display = "none";
            document.getElementById('div_aba3').style.display = "none";
            document.getElementById('div_aba4').style.display = "none";
            document.getElementById('div_aba5').style.display = "none";
            switch (obj) {
                case 'mostra_aba1':
                    aba = 1;
                    document.getElementById('mostra_aba1').focus();
                    document.getElementById('div_aba1').style.display = "block";
                    document.getElementById('mostra_aba1').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba2').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba3').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba4').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba5').style.backgroundColor = "#A8A8A8";
                    break
                case 'mostra_aba2':
                    aba = 2
                    document.getElementById('mostra_aba2').focus();
                    document.getElementById('div_aba2').style.display = "block";
                    document.getElementById('mostra_aba2').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba1').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba3').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba4').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba5').style.backgroundColor = "#A8A8A8";
                    break
                case 'mostra_aba3':
                    aba = 3;
                    document.getElementById('mostra_aba3').focus();
                    document.getElementById('div_aba3').style.display = "block";
                    document.getElementById('mostra_aba3').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba1').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba2').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba4').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba5').style.backgroundColor = "#A8A8A8";
                    <?
                    if($fetClientes["ip_config_avancado"] == 0) {
                        echo "document.getElementById('mostra_aba_ipv41').focus();";
                        echo "document.getElementById('mostrar_abas_ipv41').style.display = 'block';";
                        echo "document.getElementById('mostrar_abas_ipv42').style.display = 'none';";
                        echo "document.getElementById('mostra_aba_ipv41').style.backgroundColor = '#00c95c';";
                        echo "document.getElementById('mostra_aba_ipv42').style.backgroundColor = '#A8A8A8';";
                    } else {
                        echo "document.getElementById('mostra_aba_ipv42').focus();";
                        echo "document.getElementById('mostrar_abas_ipv41').style.display = 'none';";
                        echo "document.getElementById('mostrar_abas_ipv42').style.display = 'block';";
                        echo "document.getElementById('mostra_aba_ipv42').style.backgroundColor = '#00c95c';";
                        echo "document.getElementById('mostra_aba_ipv41').style.backgroundColor = '#A8A8A8';";
                    }
                    ?>
                    break
                case 'mostra_aba4':
                    aba = 4;
                    document.getElementById('mostra_aba4').focus();
                    document.getElementById('div_aba4').style.display = "block";
                    document.getElementById('mostra_aba4').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba1').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba2').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba3').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba5').style.backgroundColor = "#A8A8A8";
                    break
                case 'mostra_aba5':
                    aba = 5;
                    document.getElementById('mostra_aba5').focus();
                    document.getElementById('div_aba5').style.display = "block";
                    document.getElementById('mostra_aba5').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba1').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba2').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba3').style.backgroundColor = "#A8A8A8";
                    document.getElementById('mostra_aba4').style.backgroundColor = "#A8A8A8";
                    break
            }
        }

        var aba2 = 1;

        function mostrar_abas_users(obj) {
            switch (obj) {
                case 'mostra_aba_user1':
                    aba2 = 1;
                    document.getElementById('mostra_aba_user1').focus();
                    document.getElementById('div_aba_user1').style.display = "block";
                    document.getElementById('div_aba_user2').style.display = "none";
                    break;
                case 'mostra_aba_user2':
                    aba2 = 2
                    document.getElementById('mostra_aba_user2').focus();
                    document.getElementById('div_aba_user1').style.display = "none";
                    document.getElementById('div_aba_user2').style.display = "block";
                    break;
            }
        }

        function mostrar_abas_ipv4(obj) {
            //alert(obj);
            tt = 0;
            switch (obj) {
                case 'mostra_aba_ipv41':
                    //aba2 = 1;
                    document.getElementById('mostra_aba_ipv41').focus();
                    document.getElementById('mostrar_abas_ipv41').style.display = "block";
                    document.getElementById('mostrar_abas_ipv42').style.display = "none";
                    document.getElementById('mostra_aba_ipv41').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba_ipv42').style.backgroundColor = "#A8A8A8";
                    break;
                case 'mostra_aba_ipv42':
                    //aba2 = 2
                    tt = 1;
                    document.getElementById('mostra_aba_ipv42').focus();
                    document.getElementById('mostrar_abas_ipv41').style.display = "none";
                    document.getElementById('mostrar_abas_ipv42').style.display = "block";
                    document.getElementById('mostra_aba_ipv42').style.backgroundColor = "#00c95c";
                    document.getElementById('mostra_aba_ipv41').style.backgroundColor = "#A8A8A8";
                    break;
            }

            jQuery.ajax({
                type: "POST",
                url: "salvarAbas.php?tipo=" + tt + "",
                success: function (data) { }
            });

        }

        $(document).ready(function () {
            //$(".iframeuser").colorbox({iframe:true, width:"350px", height:"450px"});
            $(".iframegrupo").colorbox({iframe: true, width: "650px", height: "450px"});
            $(".inline").colorbox({inline: true, width: "50%"});
            $('.non-retina').colorbox({rel: 'group5', transition: 'none'})
            $('.retina').colorbox({rel: 'group5', transition: 'none', retinaImage: true, retinaUrl: true});
            $("#click").click(function () {
                $('#click').css({
                    "background-color": "#f00",
                    "color": "#fff",
                    "cursor": "inherit"
                }).text("Erro ao abrir a colorbox");
                return false;
            });
        });

        $(document).ready(function () {
            $("#abrirGrupo").click(function () {
                $("#InserindoGrupo1").show("slow", function () {
                    //o conteúdo está sendo mostrado...
                });
                $("#InserindoGrupoalert").show("slow", function () {
                    //o conteúdo está sendo mostrado...
                });
                $("#abrirGrupo").hide();
            });

            $("#abrirGrupoNovo").click(function () {
                //alert("teste");
                $('#abrirGrupoNovo').css({
                    "background-color": "#f00",
                    "color": "#fff",
                    "cursor": "inherit"
                }).text("Open this window again and this message will still be here.");
                return false;
            });

            $(document).ready(function () {
                $(".iframeonuu").colorbox({iframe: true, width: "350px", height: "495px"});
                $(".inline").colorbox({inline: true, width: "50%"});
                $('.non-retina').colorbox({rel: 'group5', transition: 'none'})
                $('.retina').colorbox({rel: 'group5', transition: 'none', retinaImage: true, retinaUrl: true});
                $("#click").click(function () {
                    $('#click').css({
                        "background-color": "#f00",
                        "color": "#fff",
                        "cursor": "inherit"
                    }).text("Open this window again and this message will still be here.");
                    return false;
                });
                $(".iframeuser").colorbox({iframe: true, width: "350px", height: "520px"});
            });
        });

        $(document).ready(function () {
            $("#abririp").click(function () {
                $("#Inserindoip").show("slow", function () {
                    //o conteúdo está sendo mostrado...
                });
                $("#abririp").hide();
            });
        });

        $(document).ready(function () {
            $("#abririp6").click(function () {
                $("#Inserindoip6").show("slow", function () {
                    //o conteúdo está sendo mostrado...
                });
                $("#abririp6").hide();
            });
        });

        $(document).ready(function () {
            $("#abrirTelegram").click(function () {
                $("#InserindoTelegram").show("slow", function () {
                    //o conteúdo está sendo mostrado...
                });
                $("#abrirTelegram").hide();
            });
        });

        $(document).ready(function () {
            $("#AbrirRotavpn").click(function () {
                $("#InserindoRotavpn").show("slow", function () {
                    //o conteúdo está sendo mostrado...
                });
                $("#AbrirRotavpn").hide();
            });
        });

        function bloqueioIPv6() {
            if (document.getElementById("IPV6INIT").value == "yes") {
                document.getElementById("AbreIPV6").style.display = "block";
            } else {
                document.getElementById("AbreIPV6").style.display = "none";
            }
        }

        function bloqueio4() {
            if (document.getElementById("ativar4").value == 1) {
                document.getElementById("AbreSMTPIntegra").style.display = "block";
            } else {
                document.getElementById("AbreSMTPIntegra").style.display = "none";
            }
        }

        function bloqueio5() {
            if (document.getElementById("ativar5").value == 1) {
                document.getElementById("AbreTelegram").style.display = "block";
            } else {
                document.getElementById("AbreTelegram").style.display = "none";
            }
        }

        function bloqueio6() {
            if (document.getElementById("ativar6").value == 1) {
                document.getElementById("AbreWhats").style.display = "block";
                document.getElementById("abre_api_whats").style.display = "block";
                document.getElementById("abre_api_whats1").style.display = "block";
            } else {
                document.getElementById("AbreWhats").style.display = "none";
                document.getElementById("abre_api_whats").style.display = "none";
                document.getElementById("abre_api_whats1").style.display = "none";
                document.getElementById("abre_api_whats_link").style.display = "none";
            }
        }

        function bloqueio61() {
            if (document.getElementById("api_whats").value == 1) {
                document.getElementById("abre_api_whats_link").style.display = "block";
            } else {
                document.getElementById("abre_api_whats_link").style.display = "none";
            }
        }

        function bloqueio7() {
            if (document.getElementById("ativar7").value == 1) {
                document.getElementById("AbreSSLRavi").style.display = "none";
                document.getElementById("AbreSSL").style.display = "block";
            } else if (document.getElementById("ativar7").value == 2) {
                document.getElementById("AbreSSL").style.display = "none";
                document.getElementById("AbreSSLRavi").style.display = "block";
            } else {
                document.getElementById("AbreSSL").style.display = "none";
                document.getElementById("AbreSSLRavi").style.display = "none";
            }
        }

        function bloqueio8() {
            if (document.getElementById("ativar8").value == 1) {
                document.getElementById("AbreVPN").style.display = "block";
                document.getElementById("AbreVPN2").style.display = "block";
                document.getElementById("AbrirRotavpn").style.display = "block";
            } else {
                document.getElementById("AbreVPN").style.display = "none";
                document.getElementById("AbreVPN2").style.display = "none";
                document.getElementById("AbrirRotavpn").style.display = "none";
            }
        }

        function excluir(id) {
            decisao = confirm("<?=$GLOBALS['lang'][71]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "excluirusuario.php?id=" + id + "",
                    success: function (data) {
                        alert("<?=$GLOBALS['lang'][72]?>");

                        if(data == 1) {
                            window.location.href = 'config.php?aba=1';
                        } else {
                            window.location.href = 'logout.php';
                        }
                        
                        //location.reload();
                    }
                });
            }
            return false;
        }

        function excluirGrupo(id) {
            decisao = confirm("<?=$GLOBALS['lang'][73]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "excluirGrupoUser.php?id=" + id + "",
                    success: function (data) {
                        if (data.indexOf('a') !== -1) {
                            alert("<?=$GLOBALS['lang'][74]?>");
                            window.location.href = 'config.php?aba=1&aba1=2';
                        } else {
                            alert("<?=$GLOBALS['lang'][75]?>")
                        }
                        //location.reload();
                    }
                });
            }
            return false;
        }

        function restaurarBkp(arq) {
            decisao = confirm("<?=$GLOBALS['lang'][76]?> " + arq + "?");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "RestauraBkpRavi.php?arq=" + arq + "",
                    success: function (response) {
                        var valor = response.valueOf();
                        alert(valor);
                        //location.reload();
                        //window.location.href = 'config.php?aba=4';
                        window.location.href = 'login.php?a=c';
                    }
                });
            }
            return false;
        }

        function texcluir(id) {
            decisao = confirm("<?=$GLOBALS['lang'][77]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "excluirtelegram.php?tipo=1&id=" + id + "",
                    success: function (data) {
                        //location.reload();
                        window.location.href = 'config.php?aba=2';
                    }
                });
            }
            return false;
        }

        function rotavpn_excluir(id) {
            decisao = confirm("<?=$GLOBALS['lang'][78]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "excluir_rota_vpn.php?id=" + id + "",
                    success: function (data) {
                        //location.reload();
                        window.location.href = 'config.php?aba=3';
                    }
                });
            }
            return false;
        }

        function gerarbackup() {
            jQuery.ajax({
                type: "POST",
                url: "PostGeraBackupRavi.php",
                success: function (response) {
                    alert("<?=$GLOBALS['lang'][260]?>");
                    //location.reload();
                    window.location.href = 'config.php?aba=4';
                }
            });
        }
        /*
        function rebootnode2() {
            //decisao = confirm("Você tem certeza que deseja reiniciar o Whatsapp?");
            //if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "rebootnode.php",
                    success: function (response) {
                        //$('#resp').html(response);
                        //location.reload();
                        //window.location.href = 'config.php?aba=2';
                    }
                });
            //}
            return false;
        }
        */
        function rebootnode() {
            decisao = confirm("<?=$GLOBALS['lang'][79]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "rebootnode.php",
                    success: function (response) {
                        //$('#resp').html(response);
                        //location.reload();
                        window.location.href = 'config.php?aba=2';
                    }
                });
            }
            return false;
        }

        function rebootnetwork() {
            decisao = confirm("<?=$GLOBALS['lang'][80]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "rebootrede.php",
                    success: function (response) {
                        alert("<?=$GLOBALS['lang'][261]?>");
                        //$('#resp').html(response);
                        //location.reload();
                        window.location.href = 'config.php?aba=3';
                    }
                });
            }
            return false;
        }

        function reboot() {
            decisao = confirm("<?=$GLOBALS['lang'][81]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "rebootRavi.php",
                    success: function (response) {
                        alert("<?=$GLOBALS['lang'][262]?>");
                        //$('#resp').html(response);
                        //location.reload();
                        window.location.href = 'config.php?aba=1';
                    }
                });
            }
            return false;
        }

        function poweroff() {
            decisao = confirm("<?=$GLOBALS['lang'][82]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "poweroffRavi.php",
                    success: function (response) {
                        alert("<?=$GLOBALS['lang'][263]?>");
                        //$('#resp').html(response);
                        //location.reload();
                        window.location.href = 'config.php?aba=1';
                    }
                });
            }
            return false;
        }

        function desconectarAparelho(id) {
            //alert(id);
            decisao = confirm("<?=$GLOBALS['lang'][83]?>");
            if (decisao) {
                jQuery.ajax({
                    type: "POST",
                    url: "desconectarAparelhoConfig.php?id=" + id,
                    success: function (response) {
                        //$('#resp').html(response);
                        window.location.href = 'config.php?aba=1';
                        //location.reload();
                    }
                });
            }
            return false;
        }

        function test_Telegram(id) {
            jQuery.ajax({
                type: "POST",
                url: "test_Telegram.php?tipo=1&id=" + id + "",
                success: function (response) {
                    alert(response);
                    //location.reload();
                },
            });
        }

        function test_smtp() {
            jQuery.ajax({
                type: "POST",
                url: "teste_email.php",
                success: function (response) {
                    alert(response);
                    //location.reload();
                },
            });
        }

        function achaChat_id() {
            var token = document.getElementById('token_new').value;
            window.open("https://api.telegram.org/bot" + token + "/getUpdates", "_blank");
        }

        $(document).ready(function () {
            var senha = $('#senha');
            var olho = $("#olho");

            olho.mousedown(function () {
                senha.attr("type", "text");
            });

            olho.mouseup(function () {
                senha.attr("type", "password");
            });

            $("#olho").mouseout(function () {
                $("#senha").attr("type", "password");
            });
        });

        $(document).ready(function () {
            var senhaU = $('#senhaU');
            var olhoU = $("#olhoU");

            olhoU.mousedown(function () {
                senhaU.attr("type", "text");
            });

            olhoU.mouseup(function () {
                senhaU.attr("type", "password");
            });
            // para evitar o problema de arrastar a imagem e a senha continuar exposta,
            $("#olhoU").mouseout(function () {
                $("#senhaU").attr("type", "password");
            });
        });

        $(document).ready(function () {
            var senhaP = $('#senhaP');
            var olhoP = $("#olhoP");

            olhoP.mousedown(function () {
                senhaP.attr("type", "text");
            });

            olhoP.mouseup(function () {
                senhaP.attr("type", "password");
            });
            // para evitar o problema de arrastar a imagem e a senha continuar exposta,
            $("#olhoP").mouseout(function () {
                $("#senhaP").attr("type", "password");
            });
        });

        $(document).ready(function () {
            var senhaR = $('#senhaR');
            var olhoR = $("#olhoR");

            olhoR.mousedown(function () {
                senhaR.attr("type", "text");
            });

            olhoR.mouseup(function () {
                senhaR.attr("type", "password");
            });
            // para evitar o problema de arrastar a imagem e a senha continuar exposta,
            $("#olhoR").mouseout(function () {
                $("#senhaR").attr("type", "password");
            });
        });

        function sincronizar() {
            $('#respToken').html('<img src="img/logo.gif" width="12" height="auto" alt="">&nbsp;<span style="font-size: 14px;"><?=$GLOBALS['lang'][84]?></span>');
            jQuery.ajax({
                type: "POST",
                url: "token.php",
                success: function (response) {
                    var valor = response.valueOf();
                    //alert(valor);
                    $('#respToken').html(response);
                }
            });
        }

        $(document).ready(function () {
            var senhaZ = $('#senhaZ');
            var olhoZ = $("#olhoZ");

            olhoZ.mousedown(function () {
                senhaZ.attr("type", "text");
            });

            olhoZ.mouseup(function () {
                senhaZ.attr("type", "password");
            });
            // para evitar o problema de arrastar a imagem e a senha continuar exposta,
            $("#olhoZ").mouseout(function () {
                $("#senhaZ").attr("type", "password");
            });
        });

        $(document).ready(function () {
            var senhaEmailS = $('#senhaEmailS');
            var olhoS = $("#olhoS");

            olhoS.mousedown(function () {
                senhaEmailS.attr("type", "text");
            });

            olhoS.mouseup(function () {
                senhaEmailS.attr("type", "password");
            });
            // para evitar o problema de arrastar a imagem e a senha continuar exposta,
            $("#olhoS").mouseout(function () {
                $("#senhaEmailS").attr("type", "password");
            });
        });

        function Rodape() {
            $.ajax({
                type: "POST",
                url: "rodape.php",
                success: function (data) {
                    $('#Rodape').html(data);
                }
            });
        }
        $(function () {
            setInterval(function () {
                Rodape();
            }, 10000);
        });

        function copiarToken() {
            var textoCopiado = document.getElementById("tokenRAVI");
            textoCopiado.select();
            document.execCommand("copy");
            alert("<?=$GLOBALS['lang'][85]?> " + textoCopiado.value);
        }

        function verificaMascara(event, obj) {
            if (event.keyCode != 13 && event.keyCode != 8 && event.keyCode != 27) {
                valor = obj.value;
                if (valor.indexOf('/') !== -1) {
                    if (valor.indexOf('/32') !== -1) {
                        document.getElementById("mask").value = "255.255.255.255";
                    } else if (valor.indexOf('/31') !== -1) {
                        document.getElementById("mask").value = "255.255.255.254";
                    } else if (valor.indexOf('/30') !== -1) {
                        document.getElementById("mask").value = "255.255.255.252";
                    } else if (valor.indexOf('/29') !== -1) {
                        document.getElementById("mask").value = "255.255.255.248";
                    } else if (valor.indexOf('/28') !== -1) {
                        document.getElementById("mask").value = "255.255.255.240";
                    } else if (valor.indexOf('/27') !== -1) {
                        document.getElementById("mask").value = "255.255.255.224";
                    } else if (valor.indexOf('/26') !== -1) {
                        document.getElementById("mask").value = "255.255.255.192";
                    } else if (valor.indexOf('/25') !== -1) {
                        document.getElementById("mask").value = "255.255.255.128";
                    } else if (valor.indexOf('/24') !== -1) {
                        document.getElementById("mask").value = "255.255.255.0";
                    } else if (valor.indexOf('/23') !== -1) {
                        document.getElementById("mask").value = "255.255.254.0";
                    } else if (valor.indexOf('/22') !== -1) {
                        document.getElementById("mask").value = "255.255.252.0";
                    } else if (valor.indexOf('/21') !== -1) {
                        document.getElementById("mask").value = "255.255.248.0";
                    } else if (valor.indexOf('/20') !== -1) {
                        document.getElementById("mask").value = "255.255.240.0";
                    } else if (valor.indexOf('/19') !== -1) {
                        document.getElementById("mask").value = "255.255.224.0";
                    } else if (valor.indexOf('/18') !== -1) {
                        document.getElementById("mask").value = "255.255.192.0";
                    } else if (valor.indexOf('/17') !== -1) {
                        document.getElementById("mask").value = "255.255.128.0";
                    } else if (valor.indexOf('/16') !== -1) {
                        document.getElementById("mask").value = "255.255.0.0";
                    } else if (valor.indexOf('/15') !== -1) {
                        document.getElementById("mask").value = "255.254.0.0";
                    } else if (valor.indexOf('/14') !== -1) {
                        document.getElementById("mask").value = "255.252.0.0";
                    } else if (valor.indexOf('/13') !== -1) {
                        document.getElementById("mask").value = "255.248.0.0";
                    } else if (valor.indexOf('/12') !== -1) {
                        document.getElementById("mask").value = "255.240.0.0";
                    } else if (valor.indexOf('/11') !== -1) {
                        document.getElementById("mask").value = "255.224.0.0";
                    } else if (valor.indexOf('/10') !== -1) {
                        document.getElementById("mask").value = "255.192.0.0";
                    } else if (valor.indexOf('/9') !== -1) {
                        document.getElementById("mask").value = "255.128.0.0";
                    } else if (valor.indexOf('/8') !== -1) {
                        document.getElementById("mask").value = "255.0.0.0";
                    }
                }
            }
        }

        function verificaSubDominio() {
            //alert(document.getElementById("subdom_ssl").value)
            jQuery.ajax({
                type: "POST",
                url: "https://ravisystems.com.br/webservice/dns.php?token=esAoifYR2xNbHn-M4gzlKOhFBnETy-NrfPgIyPbCsPuf-N7CA5kClp4qksp&usuario=rodrigo&senha=ravi240215&acao=pesquisar_subdom&dom=ravisystems.com.br&subdom=" + document.getElementById("subdom_ssl").value + "&tipo=A",
                success: function (response) {
                    if (response.msg == "true") {
                        document.getElementById("subdom_ssl").style.border = "solid 2px";
                        document.getElementById("subdom_ssl").style.borderColor = "red";
                        document.getElementById("ip").style.display = "none";
                    } else {
                        document.getElementById("subdom_ssl").style.border = "none";
                        document.getElementById("ip").style.display = "block";
                    }
                    //alert(response.msg);
                }
            });
        }

        function excluirSub(sub) {
            if (window.confirm("<?=$GLOBALS['lang'][86]?>")) {
                window.location.href = "excluir_subdominio_Ravi.php?sub=" + sub;
            }
        }

        function removerImagem() {
            $.ajax({
                type: 'POST',
                url: "removerFoto.php?tipo="+document.getElementById("tipoFoto").value,
                success: function(response) {
                    //$('#resp').html(response);
                    alert(response);
                    //window.location.href = 'config.php?aba='+aba+'';
                    location.reload();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        function salvarNome() {
            $.ajax({
                type: 'POST',
                url: "salvarPerfilEmpresa.php?nome="+document.getElementById("nomeApresentacao").value,
                success: function(response) {
                    //$('#resp').html(response);
                    //alert(response);
                    //window.location.href = 'config.php?aba='+aba+'';
                }
            });
        }
    </script>

    <?php
    while ($Clientes = mysqli_fetch_array($resultClientes)) {
        if ($Clientes['nome'] != '') {
            echo '
	<script>
	$(document).ready(function () {
	var senha' . $Clientes['id'] . ' = $("#senha' . $Clientes['id'] . '");
	var olho' . $Clientes['id'] . ' = $("#olho' . $Clientes['id'] . '");

	olho' . $Clientes['id'] . '.mousedown(function() {
		senha' . $Clientes['id'] . '.attr("type", "text");
	});

	olho' . $Clientes['id'] . '.mouseup(function() {
		senha' . $Clientes['id'] . '.attr("type", "password");
	});
	// para evitar o problema de arrastar a imagem e a senha continuar exposta, 
	$( "#olho' . $Clientes['id'] . '" ).mouseout(function() { 
		$("#senha' . $Clientes['id'] . '").attr("type", "password");
	});
	});
	</script>
	';
        }
    };
    ?>

</head>
<!-- addDataTable(); -->
<body onload="Rodape();">
<?php
    cabecalho();
?>
<form autocomplete="off" name="ajax_form" id="ajax_form" method="post">
    <div id="corpo">
        <div id="bordatopo">

            <div id="legendas">
                <span class="alerta_bg"><a href="javascript:reboot();" style="text-decoration: none"><div class="addNovo" ><?=$GLOBALS['lang'][87]?></div></a></span>
                <span class="alerta_bg"><a href="javascript:poweroff();" style="text-decoration: none"><div class="addNovo" ><?=$GLOBALS['lang'][88]?></div></a></span>
            </div>

            <div id="containerSensor" class="borderTopConf">
                <a href="#" tabindex="0" onclick="mostrar_abas('mostra_aba1');" id="mostra_aba1"><img src="img/sysicon.png" width="auto" height="12" alt="" style="padding-right: 2px;"/><?=$GLOBALS['lang'][89]?></a>
                <a href="#" tabindex="0" onclick="mostrar_abas('mostra_aba2');" id="mostra_aba2"><img src="img/inticon.png" width="auto" height="12" alt="" style="padding-right: 2px;"/><?=$GLOBALS['lang'][90]?></a>
                <a href="#" tabindex="0" onclick="mostrar_abas('mostra_aba3');" id="mostra_aba3"><img src="img/redeicon.png" width="auto" height="12" alt="" style="padding-right: 2px;"/><?=$GLOBALS['lang'][91]?></a>
                <a href="#" tabindex="0" onclick="mostrar_abas('mostra_aba4');" id="mostra_aba4"><img src="img/bkpicon.png" width="auto" height="12" alt="" style="padding-right: 2px;"/><?=$GLOBALS['lang'][92]?></a>
                <a href="#" tabindex="0" onclick="mostrar_abas('mostra_aba5');" id="mostra_aba5"><img src="img/cadeado.png" width="auto" height="12" alt="" style="padding-right: 2px;"/><?=$GLOBALS['lang'][93]?></a>
            </div>

            <div id="div_aba1" class="MostraSensor" style="display:block; padding-bottom: 20px;">

                <br>
                <div id="container"><?=$GLOBALS['lang'][94]?></div>

                <input type="hidden" name="atualizacaoauto" value="1">

                <script>
                    function abrefoto() {
                        document.getElementById('foto').style.display = 'block';
                        document.getElementById('imgfoto').style.display = 'none';
                        document.getElementById('newfoto').style.display = 'none';
                    }
                </script>
                <div class="QuadroConfigs">
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][95]?></div>
                    <div class="DivCongigTable">
                    <input type="text" name="nomeApresentacao" id="nomeApresentacao" class="DivCongiInput" style="width: 90%;" value="<?php echo $fetClientes['nomeApresentacao']; ?>" autocomplete="off" placeholder="<?=$GLOBALS['lang'][96]?>" onblur="javascript:salvarNome()"><br><br>
                        <div class="demo-wrap upload-demo">
                            <div class="container">
                                <div class="grid">
                                    <div>
                                        <div class="actions">
                                            <a class="btn file-btn">
                                                <input type="file" id="upload" value="Upload" accept="image/*" onchange="javascript:abrefoto();"/>
                                            </a><br><br>
                                        </div>
                                    </div>
                                    <?php
                                    if($fetClientes['foto'] == "" || $fetClientes['foto'] == "NULL") {
                                        echo '<div id="newfoto"></div><div id="imgfoto">';
                                        echo '<img src="img/semfoto.png"><br><br>';
                                        echo '</div>';
                                    } else {
                                        echo '<div id="imgfoto"></div><div id="newfoto">';
                                        echo '<img src="'.$fetClientes['foto'].'" /><br><br>';
                                        echo '</div>';
                                    }
                                    ?>
                                    <div id="foto" style="display: none">
                                    <div>
                                        <div class="upload-msg">

                                        </div>
                                        <div class="upload-demo-wrap">
                                            <div id="upload-demo"></div>
                                        </div>
                                    </div>
                                    </div>
                                    <div id="upload-result" class="UserInsert" style="display: inline-block; cursor: pointer;"><?=$GLOBALS['lang'][97]?></div>
                                    <?php
                                    if($fetClientes['foto'] != "" || $fetClientes['foto'] != "NULL") { echo ' <a href="javascript:removerImagem();"><div id="removerFoto" class="UserInsert" style="display: inline-block;">' . $GLOBALS['lang'][98] . '</div></a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="QuadroConfigs">
                    <div id="containerSensor" class="borderTopConf">
                        <a href="#" tabindex="0" onclick="mostrar_abas_users('mostra_aba_user1');" id="mostra_aba_user1"><?=$GLOBALS['lang'][99]?></a>
                        <a href="#" tabindex="0" onclick="mostrar_abas_users('mostra_aba_user2');" id="mostra_aba_user2"><?=$GLOBALS['lang'][100]?></a>
                    </div>
                    <br><br>
                    <div class="barrarolagem" style="width: 100%; max-height: 314px; display: none;" id="div_aba_user2">
                        <center><a href="criargrupos.php" style="text-decoration: none;" target="_self"><div class="UserInsert" style="display: inline-block;"><?=$GLOBALS['lang'][101]?></div></a></center>
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr>
                                <td width="200px" class="UsersTitulonone"><?=$GLOBALS['lang'][102]?>:</td>
                                <td width="100px" class="UsersTitulo"><?=$GLOBALS['lang'][3]?>:</td>
                                <td width="100px" class="UsersTitulo"><?=$GLOBALS['lang'][4]?>:</td>
                                <td width="100px" class="UsersTitulo"><?=$GLOBALS['lang'][5]?>:</td>
                                <td width="100px" class="UsersTitulo" style="border-radius: 0px 10px 0px 0px;"><?=$GLOBALS['lang'][6]?>:</td>
                                <td width="34px"></td>
                            </tr>
                            <?php
                            /*
                            DNS
                            Dispositivos
                            OLT
                            Concentradora

                            Não incluir
                            Incluir
                            Incluir e gerenciar
                            */
                            $resultGrupo = mysqli_query($db, "SELECT * FROM grupoUser ORDER BY id ASC");
                            if(!mysqli_num_rows($resultGrupo)) {
                                mysqli_query($db, "INSERT INTO grupoUser (id, nome, dns, dispositivos, olt, concentradora, pconcentradora, polt, pdispositivo) VALUES (1, 'Master', 2, 2, 2, 2, '' , '', '');");
                                $resultGrupo = mysqli_query($db, "SELECT * FROM grupoUser ORDER BY id ASC");
                            }

                            $cor1 = 'eaeaea';
                            $ArredondarBorda = 'sim';
                            $i = 0;
                            while ($grupos = mysqli_fetch_array($resultGrupo)) {
                                if ($cor1 == 'FFF') {
                                    $cor1 = 'eaeaea';
                                } else {
                                    $cor1 = 'FFF';
                                }

                                echo '<tr bgcolor="#' . $cor1 . '">';
                                if ($i == 0) {
                                    echo '<td class="UsersTd" width="200px" height="25px">&nbsp;&nbsp;' . $grupos["nome"] . '&nbsp;&nbsp;</td>';
                                    echo '<td class="UsersTd">' . $GLOBALS['lang'][103] . '</td>';
                                    echo '<td class="UsersTd">' . $GLOBALS['lang'][103] . '</td>';
                                    echo '<td class="UsersTd">' . $GLOBALS['lang'][103] . '</td>';
                                    echo '<td class="UsersTd">' . $GLOBALS['lang'][103] . '</td>';
                                    echo '<td class="UsersTd">&nbsp;</td>';
                                    echo '<td class="">&nbsp;</td>';
                                    echo '<td class="UsersTd" style="border: 0px;">&nbsp;</td>';
                                } else {
                                    echo '<td class="UsersTd" width="200px" height="25x">&nbsp;&nbsp;' . $grupos["nome"] . '&nbsp;&nbsp;</td>';
                                    if ($grupos['dns'] == 0) {
                                        // Não incluir
                                        $dns = $GLOBALS['lang'][104];
                                    } elseif ($grupos['dns'] == 1) {
                                        // Incluir
                                        $dns = $GLOBALS['lang'][105];
                                    } elseif ($grupos['dns'] == 2) {
                                        // Incluir e gerenciar
                                        $dns = $GLOBALS['lang'][103];
                                    }
                                    echo '<td class="UsersTd">' . $dns . '</td>';
                                    if ($grupos['dispositivos'] == 0) {
                                        // Não incluir
                                        $dispositivos = $GLOBALS['lang'][104];
                                    } elseif ($grupos['dispositivos'] == 1) {
                                        // Incluir
                                        $dispositivos = $GLOBALS['lang'][105];
                                    } elseif ($grupos['dispositivos'] == 2) {
                                        // Incluir e gerenciar
                                        $dispositivos = $GLOBALS['lang'][103];
                                    } elseif ($grupos['dispositivos'] == 3) {
                                        // Incluir e editar
                                        $dispositivos = $GLOBALS['lang'][107];
                                    }
                                    echo '<td class="UsersTd">' . $dispositivos . '</td>';
                                    if ($grupos['olt'] == 0) {
                                        // Não incluir
                                        $olt = $GLOBALS['lang'][104];
                                    } elseif ($grupos['olt'] == 1) {
                                        // Incluir
                                        $olt = $GLOBALS['lang'][105];
                                    } elseif ($grupos['olt'] == 2) {
                                        // Incluir e gerenciar
                                        $olt = $GLOBALS['lang'][103];
                                    } elseif ($grupos['olt'] == 3) {
                                        // Incluir e autorizar
                                        $olt = $GLOBALS['lang'][106];
                                    }
                                    echo '<td class="UsersTd">' . $olt . '</td>';
                                    if ($grupos['concentradora'] == 0) {
                                        // Não incluir
                                        $concentradora = $GLOBALS['lang'][104];
                                    } elseif ($grupos['concentradora'] == 1) {
                                        // Incluir
                                        $concentradora = $GLOBALS['lang'][105];
                                    } elseif ($grupos['concentradora'] == 2) {
                                        // Incluir e gerenciar
                                        $concentradora = $GLOBALS['lang'][103];
                                    }
                                    echo '<td class="UsersTd">' . $concentradora . '</td>';
                                    echo '<td class="UsersTd"><a href="criargrupos.php?id=' . $grupos['id'] . '"><div class="Config"><img src="img/config.png" width="16px" height="auto" alt="' . $GLOBALS['lang'][7] . '"></div></a></td>';
                                    echo '<td class="UsersTd"><a href="javascript:excluirGrupo(' . $grupos['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][108] . '"/></a></td>';
                                    echo '<td class="UsersTd" style="border: 0px;">&nbsp;</td>';
                                }
                                echo '</tr>';
                                $i++;
                            }

                            echo '</tr>';
                            ?>
                            </tbody>
                        </table>
                        <br>
                        <div id="InserindoGrupoalert" class="alertaconfig" style="display:none;"><img src="img/alertaconfig.png" width="20" height="auto" style="padding-right: 10px" alt="<?=$GLOBALS['lang'][114]?>"/><?=$GLOBALS['lang'][109]?></div>
                    </div>

                    <div class="barrarolagem" style="width: 100%; max-height: 314px; display: block;" id="div_aba_user1">
                        <center>
                            <a href="historicoUsers.php" style="text-decoration: none;"><div class="UserInsert" style="display: inline-block;"><?=$GLOBALS['lang'][110]?></div></a> 
                            <a href="addUser.php" id="abrir" class="iframeuser" style="text-decoration: none;"><div class="UserInsert" style="display: inline-block;"><?=$GLOBALS['lang'][111]?></div></a>
                        </center>

                        <?php
                        echo '<table border="0" align="center" cellpadding="0" cellspacing="0"><tbody>';
                        echo '<tr>';
                        echo '<td width="200" class="UsersTitulonone">' . $GLOBALS['lang'][102] . ':</td>';
                        echo '<td width="100" class="UsersTitulo">' . $GLOBALS['lang'][112] . ':</td>';
                        echo '<td width="100" class="UsersTitulo" style="border-radius: 0px 0px 10px 0px;">' . $GLOBALS['lang'][113] . ':</td>';
                        echo '<td width="34"></td>';
                        echo '<td width="34"></td>';
                        echo '</tr>';

                        $resultClientes = mysqli_query($db, "SELECT * FROM login ORDER BY id ASC");

                        $cor = '#FFF';
                        $ArredondarBorda = 'sim';
                        while ($Clientes = mysqli_fetch_array($resultClientes)) {
                            if ($Clientes['nome'] != '') {
                                if ($cor == '#FFF') {
                                    echo '<tr>';
                                    echo '<td class="UsersTdnone" height="25px">' . $Clientes['nome'] . '</td>';
                                    echo '<td class="UsersTd">' . $Clientes['usuario'] . '</td>';

                                    if ($ArredondarBorda == 'sim') {
                                        echo '<td class="UsersTdTipo" style="border-radius: 0px 10px 0px 0px;">';
                                        $ArredondarBorda = 'nao';
                                    } else {
                                        echo '<td class="UsersTdTipo">';
                                    }

                                    $s = mysqli_query($db, "SELECT * FROM grupoUser where id = " . $Clientes['idGrupo'] . ";");
                                    $f = mysqli_fetch_array($s);
                                    echo $f['nome'];

                                    echo '</td>';

                                    echo '<td class="UsersTdExcluir"><a href="addUser.php?id=' . $Clientes['id'] . '" class="iframeuser" style="text-decoration:none"><div class="Config"><img src="img/config.png" width="auto" height="15" alt="' . $GLOBALS['lang'][115] . '"/></div></a></td>';
                                    echo '<td class="UsersTdExcluir"><a href="javascript:excluir(' . $Clientes['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][116] . '"/></a></td>';
                                    echo '</tr>';
                                    $cor = "#eeeeee";
                                } else {
                                    echo '<tr style="background: #eeeeee;">';
                                    echo '<td class="UsersTdnone" height="25px">' . $Clientes['nome'] . '</td>';
                                    echo '<td class="UsersTd">' . $Clientes['usuario'] . '</td>';
                                    echo '<td class="UsersTdTipo">';
                                    $s = mysqli_query($db, "SELECT * FROM grupoUser where id = " . $Clientes['idGrupo'] . ";");
                                    $f = mysqli_fetch_array($s);
                                    echo $f['nome'];
                                    echo '</td>';

                                    echo '<td class="UsersTdExcluir"><a href="addUser.php?id=' . $Clientes['id'] . '" class="iframeuser" style="text-decoration:none"><div class="Config"><img src="img/config.png" width="auto" height="15" alt="' . $GLOBALS['lang'][115] . '"/></div></a></td>';
                                    echo '<td class="UsersTdExcluir"><a href="javascript:excluir(' . $Clientes['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][116] . '"/></a></td>';
                                    echo '</tr>';
                                    $cor = "#FFF";
                                };
                            };
                        };

                        echo '</tbody></table>';
                        ?>

                        <div id="InserindoUseralert" class="alertaconfig" style="display:none;"><img src="img/alertaconfig.png" width="20" height="auto" style="padding-right: 10px" alt="Alerta informativo"/><?=$GLOBALS['lang'][109]?></div>
                    </div>
                </div>

                <!-- Registro RAVI -->
                <div class="QuadroConfigs">
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][117]?></div>
                    <div id="respToken" class="respToken"></div>
                    <center>
                        <input type="text" id="tokenRAVI" name="tokenRAVI" class="DivCongiInput tokenRAVI" style="width: 60%;" value="<?php echo $fetClientes['tokenRAVI']; ?>">
                        <a href="javascript:sincronizar();" class="botaoTopo"><?=$GLOBALS['lang'][118]?></a>
                        <a href="#" class="btn botaoTopo" data-clipboard-action="copy" data-clipboard-target="#tokenRAVI"><?=$GLOBALS['lang'][119]?></a>
                    </center>
                </div>

                <!-- Aplicativos Ravi mobile ativos -->
                <div class="QuadroConfigs">
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][120]?></div>
                    <?php
                    echo '<table border="0" align="center" cellpadding="0" cellspacing="0"><tbody>';
                    echo '<tr>';
                    echo '<td width="130" class="UsersTitulonone">' . $GLOBALS['lang'][102] . ':</td>';
                    echo '<td width="130" class="UsersTitulo">' . $GLOBALS['lang'][121] . ':</td>';
                    echo '<td width="130" class="UsersTitulo" style="border-radius: 0px 0px 10px 0px;">OS:</td>';
                    echo '<td width="34"></td>';
                    echo '</tr>';

                    $sl = mysqli_query($db, "SELECT * FROM login where device_push_token != '' ORDER BY id ASC");

                    $cor = '#FFF';
                    $ArredondarBorda = 'sim';
                    while ($ft = mysqli_fetch_array($sl)) {
                        if ($ft['tokenApp']) {
                            if ($cor == '#FFF') {
                                echo '<tr>';
                                echo '<td class="UsersTdnone">' . $ft['nome'] . '</td>';
                                echo '<td class="UsersTd">' . $ft['device_name'] . '</td>';
                                if ($ArredondarBorda == 'sim') {
                                    echo '<td class="UsersTdTipo" style="border-radius: 0px 10px 0px 0px;">';
                                    $ArredondarBorda = 'nao';
                                } else {
                                    echo '<td class="UsersTdTipo">';
                                }

                                echo $ft['device_os'] . "</td>";
                                echo '<td class="UsersTdExcluir"><a href="javascript:desconectarAparelho(' . $ft['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][122] . '"/></a></td>';
                                echo '</tr>';
                                $cor = "#eeeeee";
                            } else {
                                echo '<tr style="background: #eeeeee;">';
                                echo '<td class="UsersTdnone">' . $ft['nome'] . '</td>';
                                echo '<td class="UsersTd">' . $ft['device_name'] . '</td>';
                                echo '<td class="UsersTdTipo">';
                                echo $ft['device_os'] . "</td>";

                                echo '<td class="UsersTdExcluir"><a href="javascript:desconectarAparelho(' . $ft['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][122] . '"/></a></td>';
                                echo '</tr>';
                                $cor = "#FFF";
                            };
                        };
                    };
                    echo '</tbody></table>';
                    ?>
                    <br>
                </div>

                <!-- Gerenciamento de zona horária -->
                <div class="QuadroConfigs">
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][123]?></div>
                    <select name="timezone" id="timezone" class="selectGInput" style="width: 90%; font-size: 12px; height: 33px;">
                        <option value="America/Adak" <?php if ($fetClientes['timezone'] == "America/Adak") { echo "selected"; } ?>>(GMT-10:00) America/Adak (Hawaii-Aleutian Standard Time)</option>
                        <option value="America/Atka" <?php if ($fetClientes['timezone'] == "America/Atka") { echo "selected"; } ?>>(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)</option>
                        <option value="America/Anchorage" <?php if ($fetClientes['timezone'] == "America/Anchorage") { echo "selected"; } ?>>(GMT-9:00) America/Anchorage (Alaska Standard Time)</option>
                        <option value="America/Juneau" <?php if ($fetClientes['timezone'] == "America/Juneau") { echo "selected"; } ?>>(GMT-9:00) America/Juneau (Alaska Standard Time)</option>
                        <option value="America/Nome" <?php if ($fetClientes['timezone'] == "America/Nome") { echo "selected"; } ?>>(GMT-9:00) America/Nome (Alaska Standard Time)</option>
                        <option value="America/Yakutat" <?php if ($fetClientes['timezone'] == "America/Yakutat") { echo "selected"; } ?>>(GMT-9:00) America/Yakutat (Alaska Standard Time)</option>
                        <option value="America/Dawson" <?php if ($fetClientes['timezone'] == "America/Dawson") { echo "selected"; } ?>>(GMT-8:00) America/Dawson (Pacific Standard Time)</option>
                        <option value="America/Ensenada" <?php if ($fetClientes['timezone'] == "America/Ensenada") { echo "selected"; } ?>>(GMT-8:00) America/Ensenada (Pacific Standard Time)</option>
                        <option value="America/Los_Angeles" <?php if ($fetClientes['timezone'] == "America/Los_Angeles") { echo "selected"; } ?>>(GMT-8:00) America/Los_Angeles (Pacific Standard Time)</option>
                        <option value="America/Tijuana" <?php if ($fetClientes['timezone'] == "America/Tijuana") { echo "selected"; } ?>>(GMT-8:00) America/Tijuana (Pacific Standard Time)</option>
                        <option value="America/Vancouver" <?php if ($fetClientes['timezone'] == "America/Vancouver") { echo "selected"; } ?>>(GMT-8:00) America/Vancouver (Pacific Standard Time)</option>
                        <option value="America/Whitehorse" <?php if ($fetClientes['timezone'] == "America/Whitehorse") { echo "selected"; } ?>>(GMT-8:00) America/Whitehorse (Pacific Standard Time)</option>
                        <option value="Canada/Pacific" <?php if ($fetClientes['timezone'] == "Canada/Pacific") { echo "selected"; } ?>>(GMT-8:00) Canada/Pacific (Pacific Standard Time)</option>
                        <option value="Canada/Yukon" <?php if ($fetClientes['timezone'] == "Canada/Yukon") { echo "selected"; } ?>>(GMT-8:00) Canada/Yukon (Pacific Standard Time)</option>
                        <option value="Mexico/BajaNorte" <?php if ($fetClientes['timezone'] == "Mexico/BajaNorte") { echo "selected"; } ?>>(GMT-8:00) Mexico/BajaNorte (Pacific Standard Time)</option>
                        <option value="America/Boise" <?php if ($fetClientes['timezone'] == "America/Boise") { echo "selected"; } ?>>(GMT-7:00) America/Boise (Mountain Standard Time)</option>
                        <option value="America/Cambridge_Bay" <?php if ($fetClientes['timezone'] == "America/Cambridge_Bay") { echo "selected"; } ?>>(GMT-7:00) America/Cambridge_Bay (Mountain Standard Time)</option>
                        <option value="America/Chihuahua" <?php if ($fetClientes['timezone'] == "America/Chihuahua") { echo "selected"; } ?>>(GMT-7:00) America/Chihuahua (Mountain Standard Time)</option>
                        <option value="America/Dawson_Creek" <?php if ($fetClientes['timezone'] == "America/Dawson_Creek") { echo "selected"; } ?>>(GMT-7:00) America/Dawson_Creek (Mountain Standard Time)</option>
                        <option value="America/Denver" <?php if ($fetClientes['timezone'] == "America/Denver") { echo "selected"; } ?>>(GMT-7:00) America/Denver (Mountain Standard Time)</option>
                        <option value="America/Edmonton" <?php if ($fetClientes['timezone'] == "America/Edmonton") { echo "selected"; } ?>>(GMT-7:00) America/Edmonton (Mountain Standard Time)</option>
                        <option value="America/Hermosillo" <?php if ($fetClientes['timezone'] == "America/Hermosillo") { echo "selected"; } ?>>(GMT-7:00) America/Hermosillo (Mountain Standard Time)</option>
                        <option value="America/Inuvik" <?php if ($fetClientes['timezone'] == "America/Inuvik") { echo "selected"; } ?>>(GMT-7:00) America/Inuvik (Mountain Standard Time)</option>
                        <option value="America/Mazatlan" <?php if ($fetClientes['timezone'] == "America/Mazatlan") { echo "selected"; } ?>>(GMT-7:00) America/Mazatlan (Mountain Standard Time)</option>
                        <option value="America/Phoenix" <?php if ($fetClientes['timezone'] == "America/Phoenix") { echo "selected"; } ?>>(GMT-7:00) America/Phoenix (Mountain Standard Time)</option>
                        <option value="America/Shiprock" <?php if ($fetClientes['timezone'] == "America/Shiprock") { echo "selected"; } ?>>(GMT-7:00) America/Shiprock (Mountain Standard Time)</option>
                        <option value="America/Yellowknife" <?php if ($fetClientes['timezone'] == "America/Yellowknife") { echo "selected"; } ?>>(GMT-7:00) America/Yellowknife (Mountain Standard Time)</option>
                        <option value="Canada/Mountain" <?php if ($fetClientes['timezone'] == "Canada/Mountain") { echo "selected"; } ?>>(GMT-7:00) Canada/Mountain (Mountain Standard Time)</option>
                        <option value="Mexico/BajaSur" <?php if ($fetClientes['timezone'] == "Mexico/BajaSur") { echo "selected"; } ?>>(GMT-7:00) Mexico/BajaSur (Mountain Standard Time)</option>
                        <option value="America/Belize" <?php if ($fetClientes['timezone'] == "America/Belize") { echo "selected"; } ?>>(GMT-6:00) America/Belize (Central Standard Time)</option>
                        <option value="America/Cancun" <?php if ($fetClientes['timezone'] == "America/Cancun") { echo "selected"; } ?>>(GMT-6:00) America/Cancun (Central Standard Time)</option>
                        <option value="America/Chicago" <?php if ($fetClientes['timezone'] == "America/Chicago") { echo "selected"; } ?>>(GMT-6:00) America/Chicago (Central Standard Time)</option>
                        <option value="America/Costa_Rica" <?php if ($fetClientes['timezone'] == "America/Costa_Rica") { echo "selected"; } ?>>(GMT-6:00) America/Costa_Rica (Central Standard Time)</option>
                        <option value="America/El_Salvador" <?php if ($fetClientes['timezone'] == "America/El_Salvador") { echo "selected"; } ?>>(GMT-6:00) America/El_Salvador (Central Standard Time)</option>
                        <option value="America/Guatemala" <?php if ($fetClientes['timezone'] == "America/Guatemala") { echo "selected"; } ?>>(GMT-6:00) America/Guatemala (Central Standard Time)</option>
                        <option value="America/Knox_IN" <?php if ($fetClientes['timezone'] == "America/Knox_IN") { echo "selected"; } ?>>(GMT-6:00) America/Knox_IN (Central Standard Time)</option>
                        <option value="America/Managua" <?php if ($fetClientes['timezone'] == "America/Managua") { echo "selected"; } ?>>(GMT-6:00) America/Managua (Central Standard Time)</option>
                        <option value="America/Menominee" <?php if ($fetClientes['timezone'] == "America/Menominee") { echo "selected"; } ?>>(GMT-6:00) America/Menominee (Central Standard Time)</option>
                        <option value="America/Merida" <?php if ($fetClientes['timezone'] == "America/Merida") { echo "selected"; } ?>>(GMT-6:00) America/Merida (Central Standard Time)</option>
                        <option value="America/Mexico_City" <?php if ($fetClientes['timezone'] == "America/Mexico_City") { echo "selected"; } ?>>(GMT-6:00) America/Mexico_City (Central Standard Time)</option>
                        <option value="America/Monterrey" <?php if ($fetClientes['timezone'] == "America/Monterrey") { echo "selected"; } ?>>(GMT-6:00) America/Monterrey (Central Standard Time)</option>
                        <option value="America/Rainy_River" <?php if ($fetClientes['timezone'] == "America/Rainy_River") { echo "selected"; } ?>>(GMT-6:00) America/Rainy_River (Central Standard Time)</option>
                        <option value="America/Rankin_Inlet" <?php if ($fetClientes['timezone'] == "America/Rankin_Inlet") { echo "selected"; } ?>>(GMT-6:00) America/Rankin_Inlet (Central Standard Time)</option>
                        <option value="America/Regina" <?php if ($fetClientes['timezone'] == "America/Regina") { echo "selected"; } ?>>(GMT-6:00) America/Regina (Central Standard Time)</option>
                        <option value="America/Swift_Current" <?php if ($fetClientes['timezone'] == "America/Swift_Current") { echo "selected"; } ?>>(GMT-6:00) America/Swift_Current (Central Standard Time)</option>
                        <option value="America/Tegucigalpa" <?php if ($fetClientes['timezone'] == "America/Tegucigalpa") { echo "selected"; } ?>>(GMT-6:00) America/Tegucigalpa (Central Standard Time)</option>
                        <option value="America/Winnipeg" <?php if ($fetClientes['timezone'] == "America/Winnipeg") { echo "selected"; } ?>>(GMT-6:00) America/Winnipeg (Central Standard Time)</option>
                        <option value="Canada/Central" <?php if ($fetClientes['timezone'] == "Canada/Central") { echo "selected"; } ?>>(GMT-6:00) Canada/Central (Central Standard Time)</option>
                        <option value="Canada/East-Saskatchewan" <?php if ($fetClientes['timezone'] == "Canada/East-Saskatchewan") { echo "selected"; } ?>>(GMT-6:00) Canada/East-Saskatchewan (Central Standard Time)</option>
                        <option value="Canada/Saskatchewan" <?php if ($fetClientes['timezone'] == "Canada/Saskatchewan") { echo "selected"; } ?>>(GMT-6:00) Canada/Saskatchewan (Central Standard Time)</option>
                        <option value="Chile/EasterIsland" <?php if ($fetClientes['timezone'] == "Chile/EasterIsland") { echo "selected"; } ?>>(GMT-6:00) Chile/EasterIsland (Easter Is. Time)</option>
                        <option value="Mexico/General" <?php if ($fetClientes['timezone'] == "Mexico/General") { echo "selected"; } ?>>(GMT-6:00) Mexico/General (Central Standard Time)</option>
                        <option value="America/Atikokan" <?php if ($fetClientes['timezone'] == "America/Atikokan") { echo "selected"; } ?>>(GMT-5:00) America/Atikokan (Eastern Standard Time)</option>
                        <option value="America/Bogota" <?php if ($fetClientes['timezone'] == "America/Bogota") { echo "selected"; } ?>>(GMT-5:00) America/Bogota (Colombia Time)</option>
                        <option value="America/Cayman" <?php if ($fetClientes['timezone'] == "America/Cayman") { echo "selected"; } ?>>(GMT-5:00) America/Cayman (Eastern Standard Time)</option>
                        <option value="America/Coral_Harbour" <?php if ($fetClientes['timezone'] == "America/Coral_Harbour") { echo "selected"; } ?>>(GMT-5:00) America/Coral_Harbour (Eastern Standard Time)</option>
                        <option value="America/Detroit" <?php if ($fetClientes['timezone'] == "America/Detroit") { echo "selected"; } ?>>(GMT-5:00) America/Detroit (Eastern Standard Time)</option>
                        <option value="America/Fort_Wayne" <?php if ($fetClientes['timezone'] == "America/Fort_Wayne") { echo "selected"; } ?>>(GMT-5:00) America/Fort_Wayne (Eastern Standard Time)</option>
                        <option value="America/Grand_Turk" <?php if ($fetClientes['timezone'] == "America/Grand_Turk") { echo "selected"; } ?>>(GMT-5:00) America/Grand_Turk (Eastern Standard Time)</option>
                        <option value="America/Guayaquil" <?php if ($fetClientes['timezone'] == "America/Guayaquil") { echo "selected"; } ?>>(GMT-5:00) America/Guayaquil (Ecuador Time)</option>
                        <option value="America/Havana" <?php if ($fetClientes['timezone'] == "America/Havana") { echo "selected"; } ?>>(GMT-5:00) America/Havana (Cuba Standard Time)</option>
                        <option value="America/Indianapolis" <?php if ($fetClientes['timezone'] == "America/Indianapolis") { echo "selected"; } ?>>(GMT-5:00) America/Indianapolis (Eastern Standard Time)</option>
                        <option value="America/Iqaluit" <?php if ($fetClientes['timezone'] == "America/Iqaluit") { echo "selected"; } ?>>(GMT-5:00) America/Iqaluit (Eastern Standard Time)</option>
                        <option value="America/Jamaica" <?php if ($fetClientes['timezone'] == "America/Jamaica") { echo "selected"; } ?>>(GMT-5:00) America/Jamaica (Eastern Standard Time)</option>
                        <option value="America/Lima" <?php if ($fetClientes['timezone'] == "America/Lim") { echo "selected"; } ?>>(GMT-5:00) America/Lima (Peru Time)</option>
                        <option value="America/Louisville" <?php if ($fetClientes['timezone'] == "America/Louisville") { echo "selected"; } ?>>(GMT-5:00) America/Louisville (Eastern Standard Time)</option>
                        <option value="America/Montreal" <?php if ($fetClientes['timezone'] == "America/Montreal") { echo "selected"; } ?>>(GMT-5:00) America/Montreal (Eastern Standard Time)</option>
                        <option value="America/Nassau" <?php if ($fetClientes['timezone'] == "America/Nassau") { echo "selected"; } ?>>(GMT-5:00) America/Nassau (Eastern Standard Time)</option>
                        <option value="America/New_York" <?php if ($fetClientes['timezone'] == "America/New_York") { echo "selected"; } ?>>(GMT-5:00) America/New_York (Eastern Standard Time)</option>
                        <option value="America/Nipigon" <?php if ($fetClientes['timezone'] == "America/Nipigon") { echo "selected"; } ?>>(GMT-5:00) America/Nipigon (Eastern Standard Time)</option>
                        <option value="America/Panama" <?php if ($fetClientes['timezone'] == "America/Panama") { echo "selected"; } ?>>(GMT-5:00) America/Panama (Eastern Standard Time)</option>
                        <option value="America/Pangnirtung" <?php if ($fetClientes['timezone'] == "America/Pangnirtung") { echo "selected"; } ?>>(GMT-5:00) America/Pangnirtung (Eastern Standard Time)</option>
                        <option value="America/Port-au-Prince" <?php if ($fetClientes['timezone'] == "America/Port-au-Prince") { echo "selected"; } ?>>(GMT-5:00) America/Port-au-Prince (Eastern Standard Time)</option>
                        <option value="America/Resolute" <?php if ($fetClientes['timezone'] == "America/Resolute") { echo "selected"; } ?>>(GMT-5:00) America/Resolute (Eastern Standard Time)</option>
                        <option value="America/Thunder_Bay" <?php if ($fetClientes['timezone'] == "America/Thunder_Bay") { echo "selected"; } ?>>(GMT-5:00) America/Thunder_Bay (Eastern Standard Time)</option>
                        <option value="America/Toronto" <?php if ($fetClientes['timezone'] == "America/Toronto") { echo "selected"; } ?>>(GMT-5:00) America/Toronto (Eastern Standard Time)</option>
                        <option value="Canada/Eastern" <?php if ($fetClientes['timezone'] == "Canada/Eastern") { echo "selected"; } ?>>(GMT-5:00) Canada/Eastern (Eastern Standard Time)</option>
                        <option value="America/Caracas" <?php if ($fetClientes['timezone'] == "America/Caracas") { echo "selected"; } ?>>(GMT-4:-30) America/Caracas (Venezuela Time)</option>
                        <option value="America/Anguilla" <?php if ($fetClientes['timezone'] == "America/Anguilla") { echo "selected"; } ?>>(GMT-4:00) America/Anguilla (Atlantic Standard Time)</option>
                        <option value="America/Antigua" <?php if ($fetClientes['timezone'] == "America/Antigua") { echo "selected"; } ?>>(GMT-4:00) America/Antigua (Atlantic Standard Time)</option>
                        <option value="America/Aruba" <?php if ($fetClientes['timezone'] == "America/Aruba") { echo "selected"; } ?>>(GMT-4:00) America/Aruba (Atlantic Standard Time)</option>
                        <option value="America/Asuncion" <?php if ($fetClientes['timezone'] == "America/Asuncion") { echo "selected"; } ?>>(GMT-4:00) America/Asuncion (Paraguay Time)</option>
                        <option value="America/Barbados" <?php if ($fetClientes['timezone'] == "America/Barbados") { echo "selected"; } ?>>(GMT-4:00) America/Barbados (Atlantic Standard Time)</option>
                        <option value="America/Blanc-Sablon" <?php if ($fetClientes['timezone'] == "America/Blanc-Sablon") { echo "selected"; } ?>>(GMT-4:00) America/Blanc-Sablon (Atlantic Standard Time)</option>
                        <option value="America/Boa_Vista" <?php if ($fetClientes['timezone'] == "America/Boa_Vista") { echo "selected"; } ?>>(GMT-4:00) America/Boa_Vista (Amazon Time)</option>
                        <option value="America/Campo_Grande" <?php if ($fetClientes['timezone'] == "America/Campo_Grande") { echo "selected"; } ?>>(GMT-4:00) America/Campo_Grande (Amazon Time)</option>
                        <option value="America/Cuiaba" <?php if ($fetClientes['timezone'] == "America/Cuiaba") { echo "selected"; } ?>>(GMT-4:00) America/Cuiaba (Amazon Time)</option>
                        <option value="America/Curacao" <?php if ($fetClientes['timezone'] == "America/Curacao") { echo "selected"; } ?>>(GMT-4:00) America/Curacao (Atlantic Standard Time)</option>
                        <option value="America/Dominica" <?php if ($fetClientes['timezone'] == "America/Dominica") { echo "selected"; } ?>>(GMT-4:00) America/Dominica (Atlantic Standard Time)</option>
                        <option value="America/Eirunepe" <?php if ($fetClientes['timezone'] == "America/Eirunepe") { echo "selected"; } ?>>(GMT-4:00) America/Eirunepe (Amazon Time)</option>
                        <option value="America/Glace_Bay" <?php if ($fetClientes['timezone'] == "America/Glace_Bay") { echo "selected"; } ?>>(GMT-4:00) America/Glace_Bay (Atlantic Standard Time)</option>
                        <option value="America/Goose_Bay" <?php if ($fetClientes['timezone'] == "America/Goose_Bay") { echo "selected"; } ?>>(GMT-4:00) America/Goose_Bay (Atlantic Standard Time)</option>
                        <option value="America/Grenada" <?php if ($fetClientes['timezone'] == "America/Grenada") { echo "selected"; } ?>>(GMT-4:00) America/Grenada (Atlantic Standard Time)</option>
                        <option value="America/Guadeloupe" <?php if ($fetClientes['timezone'] == "America/Guadeloupe") { echo "selected"; } ?>>(GMT-4:00) America/Guadeloupe (Atlantic Standard Time)</option>
                        <option value="America/Guyana" <?php if ($fetClientes['timezone'] == "America/Guyana") { echo "selected"; } ?>>(GMT-4:00) America/Guyana (Guyana Time)</option>
                        <option value="America/Halifax" <?php if ($fetClientes['timezone'] == "America/Halifax") { echo "selected"; } ?>>(GMT-4:00) America/Halifax (Atlantic Standard Time)</option>
                        <option value="America/La_Paz" <?php if ($fetClientes['timezone'] == "America/La_Paz") { echo "selected"; } ?>>(GMT-4:00) America/La_Paz (Bolivia Time)</option>
                        <option value="America/Manaus" <?php if ($fetClientes['timezone'] == "America/Manaus") { echo "selected"; } ?>>(GMT-4:00) America/Manaus (Amazon Time)</option>
                        <option value="America/Marigot" <?php if ($fetClientes['timezone'] == "America/Marigot") { echo "selected"; } ?>>(GMT-4:00) America/Marigot (Atlantic Standard Time)</option>
                        <option value="America/Martinique" <?php if ($fetClientes['timezone'] == "America/Martinique") { echo "selected"; } ?>>(GMT-4:00) America/Martinique (Atlantic Standard Time)</option>
                        <option value="America/Moncton" <?php if ($fetClientes['timezone'] == "America/Moncton") { echo "selected"; } ?>>(GMT-4:00) America/Moncton (Atlantic Standard Time)</option>
                        <option value="America/Montserrat" <?php if ($fetClientes['timezone'] == "America/Montserrat") { echo "selected"; } ?>>(GMT-4:00) America/Montserrat (Atlantic Standard Time)</option>
                        <option value="America/Port_of_Spain" <?php if ($fetClientes['timezone'] == "America/Port_of_Spain") { echo "selected"; } ?>>(GMT-4:00) America/Port_of_Spain (Atlantic Standard Time)</option>
                        <option value="America/Porto_Acre" <?php if ($fetClientes['timezone'] == "America/Porto_Acre") { echo "selected"; } ?>>(GMT-4:00) America/Porto_Acre (Amazon Time)</option>
                        <option value="America/Porto_Velho" <?php if ($fetClientes['timezone'] == "America/Porto_Velho") { echo "selected"; } ?>>(GMT-4:00) America/Porto_Velho (Amazon Time)</option>
                        <option value="America/Puerto_Rico" <?php if ($fetClientes['timezone'] == "America/Puerto_Rico") { echo "selected"; } ?>>(GMT-4:00) America/Puerto_Rico (Atlantic Standard Time)</option>
                        <option value="America/Rio_Branco" <?php if ($fetClientes['timezone'] == "merica/Rio_Branco") { echo "selected"; } ?>>(GMT-4:00) America/Rio_Branco (Amazon Time)</option>
                        <option value="America/Santiago" <?php if ($fetClientes['timezone'] == "America/Santiago") { echo "selected"; } ?>>(GMT-4:00) America/Santiago (Chile Time)</option>
                        <option value="America/Santo_Domingo" <?php if ($fetClientes['timezone'] == "America/Santo_Domingo") { echo "selected"; } ?>>(GMT-4:00) America/Santo_Domingo (Atlantic Standard Time)</option>
                        <option value="America/St_Barthelemy" <?php if ($fetClientes['timezone'] == "America/St_Barthelemy") { echo "selected"; } ?>>(GMT-4:00) America/St_Barthelemy (Atlantic Standard Time)</option>
                        <option value="America/St_Kitts" <?php if ($fetClientes['timezone'] == "America/St_Kitts") { echo "selected"; } ?>>(GMT-4:00) America/St_Kitts (Atlantic Standard Time)</option>
                        <option value="America/St_Lucia" <?php if ($fetClientes['timezone'] == "America/St_Lucia") { echo "selected"; } ?>>(GMT-4:00) America/St_Lucia (Atlantic Standard Time)</option>
                        <option value="America/St_Thomas" <?php if ($fetClientes['timezone'] == "America/St_Thomas") { echo "selected"; } ?>>(GMT-4:00) America/St_Thomas (Atlantic Standard Time)</option>
                        <option value="America/St_Vincent" <?php if ($fetClientes['timezone'] == "America/St_Vincent") { echo "selected"; } ?>>(GMT-4:00) America/St_Vincent (Atlantic Standard Time)</option>
                        <option value="America/Thule" <?php if ($fetClientes['timezone'] == "America/Thule") { echo "selected"; } ?>>(GMT-4:00) America/Thule (Atlantic Standard Time)</option>
                        <option value="America/Tortola" <?php if ($fetClientes['timezone'] == "America/Tortola") { echo "selected"; } ?>>(GMT-4:00) America/Tortola (Atlantic Standard Time)</option>
                        <option value="America/Virgin" <?php if ($fetClientes['timezone'] == "America/Virgin") { echo "selected"; } ?>>(GMT-4:00) America/Virgin (Atlantic Standard Time)</option>
                        <option value="Antarctica/Palmer" <?php if ($fetClientes['timezone'] == "Antarctica/Palmer") { echo "selected"; } ?>>(GMT-4:00) Antarctica/Palmer (Chile Time)</option>
                        <option value="Atlantic/Bermuda" <?php if ($fetClientes['timezone'] == "Atlantic/Bermuda") { echo "selected"; } ?>>(GMT-4:00) Atlantic/Bermuda (Atlantic Standard Time)</option>
                        <option value="Atlantic/Stanley" <?php if ($fetClientes['timezone'] == "Atlantic/Stanley") { echo "selected"; } ?>>(GMT-4:00) Atlantic/Stanley (Falkland Is. Time)</option>
                        <option value="Brazil/Acre" <?php if ($fetClientes['timezone'] == "razil/Acre") { echo "selected"; } ?>>(GMT-4:00) Brazil/Acre (Amazon Time)</option>
                        <option value="Brazil/West" <?php if ($fetClientes['timezone'] == "Brazil/West") { echo "selected"; } ?>>(GMT-4:00) Brazil/West (Amazon Time)</option>
                        <option value="Canada/Atlantic" <?php if ($fetClientes['timezone'] == "Canada/Atlantic") { echo "selected"; } ?>>(GMT-4:00) Canada/Atlantic (Atlantic Standard Time)</option>
                        <option value="Chile/Continental" <?php if ($fetClientes['timezone'] == "Chile/Continental") { echo "selected"; } ?>>(GMT-4:00) Chile/Continental (Chile Time)</option>
                        <option value="America/St_Johns" <?php if ($fetClientes['timezone'] == "America/St_Johns") { echo "selected"; } ?>>(GMT-3:-30) America/St_Johns (Newfoundland Standard Time)</option>
                        <option value="Canada/Newfoundland" <?php if ($fetClientes['timezone'] == "Canada/Newfoundland") { echo "selected"; } ?>>(GMT-3:-30) Canada/Newfoundland (Newfoundland Standard Time)</option>
                        <option value="America/Araguaina" <?php if ($fetClientes['timezone'] == "America/Araguaina") { echo "selected"; } ?>>(GMT-3:00) America/Araguaina (Brasilia Time)</option>
                        <option value="America/Bahia" <?php if ($fetClientes['timezone'] == "America/Bahia") { echo "selected"; } ?>>(GMT-3:00) America/Bahia (Brasilia Time)</option>
                        <option value="America/Belem" <?php if ($fetClientes['timezone'] == "America/Belem") { echo "selected"; } ?>>(GMT-3:00) America/Belem (Brasilia Time)</option>
                        <option value="America/Buenos_Aires" <?php if ($fetClientes['timezone'] == "America/Buenos_Aires") { echo "selected"; } ?>>(GMT-3:00) America/Buenos_Aires (Argentine Time)</option>
                        <option value="America/Catamarca" <?php if ($fetClientes['timezone'] == "America/Catamarca") { echo "selected"; } ?>>(GMT-3:00) America/Catamarca (Argentine Time)</option>
                        <option value="America/Cayenne" <?php if ($fetClientes['timezone'] == "America/Cayenne") { echo "selected"; } ?>>(GMT-3:00) America/Cayenne (French Guiana Time)</option>
                        <option value="America/Cordoba" <?php if ($fetClientes['timezone'] == "America/Cordoba") { echo "selected"; } ?>>(GMT-3:00) America/Cordoba (Argentine Time)</option>
                        <option value="America/Fortaleza" <?php if ($fetClientes['timezone'] == "America/Fortaleza") { echo "selected"; } ?>>(GMT-3:00) America/Fortaleza (Brasilia Time)</option>
                        <option value="America/Godthab" <?php if ($fetClientes['timezone'] == "America/Godthab") { echo "selected"; } ?>>(GMT-3:00) America/Godthab (Western Greenland Time)</option>
                        <option value="America/Jujuy" <?php if ($fetClientes['timezone'] == "America/Jujuy") { echo "selected"; } ?>>(GMT-3:00) America/Jujuy (Argentine Time)</option>
                        <option value="America/Maceio" <?php if ($fetClientes['timezone'] == "America/Maceio") { echo "selected"; } ?>>(GMT-3:00) America/Maceio (Brasilia Time)</option>
                        <option value="America/Mendoza" <?php if ($fetClientes['timezone'] == "America/Mendoza") { echo "selected"; } ?>>(GMT-3:00) America/Mendoza (Argentine Time)</option>
                        <option value="America/Miquelon" <?php if ($fetClientes['timezone'] == "America/Miquelon") { echo "selected"; } ?>>(GMT-3:00) America/Miquelon (Pierre & Miquelon Standard Time)</option>
                        <option value="America/Montevideo" <?php if ($fetClientes['timezone'] == "America/Montevideo") { echo "selected"; } ?>>(GMT-3:00) America/Montevideo (Uruguay Time)</option>
                        <option value="America/Paramaribo" <?php if ($fetClientes['timezone'] == "America/Paramaribo") { echo "selected"; } ?>>(GMT-3:00) America/Paramaribo (Suriname Time)</option>
                        <option value="America/Recife" <?php if ($fetClientes['timezone'] == "America/Recife") { echo "selected"; } ?>>(GMT-3:00) America/Recife (Brasilia Time)</option>
                        <option value="America/Rosario" <?php if ($fetClientes['timezone'] == "America/Rosario") { echo "selected"; } ?>>(GMT-3:00) America/Rosario (Argentine Time)</option>
                        <option value="America/Santarem" <?php if ($fetClientes['timezone'] == "America/Santarem") { echo "selected"; } ?>>(GMT-3:00) America/Santarem (Brasilia Time)</option>
                        <option value="America/Sao_Paulo" <?php if ($fetClientes['timezone'] == "America/Sao_Paulo") { echo "selected"; } ?>>(GMT-3:00) America/Sao_Paulo (Brasilia Time)</option>
                        <option value="Antarctica/Rothera" <?php if ($fetClientes['timezone'] == "Antarctica/Rothera") { echo "selected"; } ?>>(GMT-3:00) Antarctica/Rothera (Rothera Time)</option>
                        <option value="Brazil/East" <?php if ($fetClientes['timezone'] == "Brazil/East") { echo "selected"; } ?>>(GMT-3:00) Brazil/East (Brasilia Time)</option>
                        <option value="America/Noronha" <?php if ($fetClientes['timezone'] == "America/Noronha") { echo "selected"; } ?>>(GMT-2:00) America/Noronha (Fernando de Noronha Time)</option>
                        <option value="Atlantic/South_Georgia" <?php if ($fetClientes['timezone'] == "Atlantic/South_Georgia") { echo "selected"; } ?>>(GMT-2:00) Atlantic/South_Georgia (South Georgia Standard Time)</option>
                        <option value="Brazil/DeNoronha" <?php if ($fetClientes['timezone'] == "Brazil/DeNoronha") { echo "selected"; } ?>>(GMT-2:00) Brazil/DeNoronha (Fernando de Noronha Time)</option>
                        <option value="America/Scoresbysund" <?php if ($fetClientes['timezone'] == "America/Scoresbysund") { echo "selected"; } ?>>(GMT-1:00) America/Scoresbysund (Eastern Greenland Time)</option>
                        <option value="Atlantic/Azores" <?php if ($fetClientes['timezone'] == "Atlantic/Azores") { echo "selected"; } ?>>(GMT-1:00) Atlantic/Azores (Azores Time)</option>
                        <option value="Atlantic/Cape_Verde" <?php if ($fetClientes['timezone'] == "Atlantic/Cape_Verde") { echo "selected"; } ?>>(GMT-1:00) Atlantic/Cape_Verde (Cape Verde Time)</option>
                        <option value="Africa/Abidjan" <?php if ($fetClientes['timezone'] == "Africa/Abidjan") { echo "selected"; } ?>>(GMT+0:00) Africa/Abidjan (Greenwich Mean Time)</option>
                        <option value="Africa/Accra" <?php if ($fetClientes['timezone'] == "Africa/Accra") { echo "selected"; } ?>>(GMT+0:00) Africa/Accra (Ghana Mean Time)</option>
                        <option value="Africa/Bamako" <?php if ($fetClientes['timezone'] == "Africa/Bamako") { echo "selected"; } ?>>(GMT+0:00) Africa/Bamako (Greenwich Mean Time)</option>
                        <option value="Africa/Banjul" <?php if ($fetClientes['timezone'] == "Africa/Banjul") { echo "selected"; } ?>>(GMT+0:00) Africa/Banjul (Greenwich Mean Time)</option>
                        <option value="Africa/Bissau" <?php if ($fetClientes['timezone'] == "Africa/Bissau") { echo "selected"; } ?>>(GMT+0:00) Africa/Bissau (Greenwich Mean Time)</option>
                        <option value="Africa/Casablanca" <?php if ($fetClientes['timezone'] == "Africa/Casablanca") { echo "selected"; } ?>>(GMT+0:00) Africa/Casablanca (Western European Time)</option>
                        <option value="Africa/Conakry" <?php if ($fetClientes['timezone'] == "Africa/Conakry") { echo "selected"; } ?>>(GMT+0:00) Africa/Conakry (Greenwich Mean Time)</option>
                        <option value="Africa/Dakar" <?php if ($fetClientes['timezone'] == "Africa/Dakar") { echo "selected"; } ?>>(GMT+0:00) Africa/Dakar (Greenwich Mean Time)</option>
                        <option value="Africa/El_Aaiun" <?php if ($fetClientes['timezone'] == "Africa/El_Aaiun") { echo "selected"; } ?>>(GMT+0:00) Africa/El_Aaiun (Western European Time)</option>
                        <option value="Africa/Freetown" <?php if ($fetClientes['timezone'] == "Africa/Freetown") { echo "selected"; } ?>>(GMT+0:00) Africa/Freetown (Greenwich Mean Time)</option>
                        <option value="Africa/Lome" <?php if ($fetClientes['timezone'] == "Africa/Lome") { echo "selected"; } ?>>(GMT+0:00) Africa/Lome (Greenwich Mean Time)</option>
                        <option value="Africa/Monrovia" <?php if ($fetClientes['timezone'] == "Africa/Monrovia") { echo "selected"; } ?>>(GMT+0:00) Africa/Monrovia (Greenwich Mean Time)</option>
                        <option value="Africa/Nouakchott" <?php if ($fetClientes['timezone'] == "Africa/Nouakchott") { echo "selected"; } ?>>(GMT+0:00) Africa/Nouakchott (Greenwich Mean Time)</option>
                        <option value="Africa/Ouagadougou" <?php if ($fetClientes['timezone'] == "Africa/Ouagadougou") { echo "selected"; } ?>>(GMT+0:00) Africa/Ouagadougou (Greenwich Mean Time)</option>
                        <option value="Africa/Sao_Tome" <?php if ($fetClientes['timezone'] == "Africa/Sao_Tome") { echo "selected"; } ?>>(GMT+0:00) Africa/Sao_Tome (Greenwich Mean Time)</option>
                        <option value="Africa/Timbuktu" <?php if ($fetClientes['timezone'] == "Africa/Timbuktu") { echo "selected"; } ?>>(GMT+0:00) Africa/Timbuktu (Greenwich Mean Time)</option>
                        <option value="America/Danmarkshavn" <?php if ($fetClientes['timezone'] == "America/Danmarkshavn") { echo "selected"; } ?>>(GMT+0:00) America/Danmarkshavn (Greenwich Mean Time)</option>
                        <option value="Atlantic/Canary" <?php if ($fetClientes['timezone'] == "Atlantic/Canary") { echo "selected"; } ?>>(GMT+0:00) Atlantic/Canary (Western European Time)</option>
                        <option value="Atlantic/Faeroe" <?php if ($fetClientes['timezone'] == "Atlantic/Faeroe") { echo "selected"; } ?>>(GMT+0:00) Atlantic/Faeroe (Western European Time)</option>
                        <option value="Atlantic/Faroe" <?php if ($fetClientes['timezone'] == "Atlantic/Faroe") { echo "selected"; } ?>>(GMT+0:00) Atlantic/Faroe (Western European Time)</option>
                        <option value="Atlantic/Madeira" <?php if ($fetClientes['timezone'] == "Atlantic/Madeira") { echo "selected"; } ?>>(GMT+0:00) Atlantic/Madeira (Western European Time)</option>
                        <option value="Atlantic/Reykjavik" <?php if ($fetClientes['timezone'] == "Atlantic/Reykjavik") { echo "selected"; } ?>>(GMT+0:00) Atlantic/Reykjavik (Greenwich Mean Time)</option>
                        <option value="Atlantic/St_Helena" <?php if ($fetClientes['timezone'] == "Atlantic/St_Helena") { echo "selected"; } ?>>(GMT+0:00) Atlantic/St_Helena (Greenwich Mean Time)</option>
                        <option value="Europe/Belfast" <?php if ($fetClientes['timezone'] == "Europe/Belfast") { echo "selected"; } ?>>(GMT+0:00) Europe/Belfast (Greenwich Mean Time)</option>
                        <option value="Europe/Dublin" <?php if ($fetClientes['timezone'] == "Europe/Dublin") { echo "selected"; } ?>>(GMT+0:00) Europe/Dublin (Greenwich Mean Time)</option>
                        <option value="Europe/Guernsey" <?php if ($fetClientes['timezone'] == "Europe/Guernsey") { echo "selected"; } ?>>(GMT+0:00) Europe/Guernsey (Greenwich Mean Time)</option>
                        <option value="Europe/Isle_of_Man" <?php if ($fetClientes['timezone'] == "Europe/Isle_of_Man") { echo "selected"; } ?>>(GMT+0:00) Europe/Isle_of_Man (Greenwich Mean Time)</option>
                        <option value="Europe/Jersey" <?php if ($fetClientes['timezone'] == "Europe/Jersey") { echo "selected"; } ?>>(GMT+0:00) Europe/Jersey (Greenwich Mean Time)</option>
                        <option value="Europe/Lisbon" <?php if ($fetClientes['timezone'] == "Europe/Lisbon") { echo "selected"; } ?>>(GMT+0:00) Europe/Lisbon (Western European Time)</option>
                        <option value="Europe/London" <?php if ($fetClientes['timezone'] == "Europe/London") { echo "selected"; } ?>>(GMT+0:00) Europe/London (Greenwich Mean Time)</option>
                        <option value="Africa/Algiers" <?php if ($fetClientes['timezone'] == "Africa/Algiers") { echo "selected"; } ?>>(GMT+1:00) Africa/Algiers (Central European Time)</option>
                        <option value="Africa/Bangui" <?php if ($fetClientes['timezone'] == "Africa/Bangui") { echo "selected"; } ?>>(GMT+1:00) Africa/Bangui (Western African Time)</option>
                        <option value="Africa/Brazzaville" <?php if ($fetClientes['timezone'] == "Africa/Brazzaville") { echo "selected"; } ?>>(GMT+1:00) Africa/Brazzaville (Western African Time)</option>
                        <option value="Africa/Ceuta" <?php if ($fetClientes['timezone'] == "Africa/Ceuta") { echo "selected"; } ?>>(GMT+1:00) Africa/Ceuta (Central European Time)</option>
                        <option value="Africa/Douala" <?php if ($fetClientes['timezone'] == "Africa/Douala") { echo "selected"; } ?>>(GMT+1:00) Africa/Douala (Western African Time)</option>
                        <option value="Africa/Kinshasa" <?php if ($fetClientes['timezone'] == "Africa/Kinshasa") { echo "selected"; } ?>>(GMT+1:00) Africa/Kinshasa (Western African Time)</option>
                        <option value="Africa/Lagos" <?php if ($fetClientes['timezone'] == "Africa/Lagos") { echo "selected"; } ?>>(GMT+1:00) Africa/Lagos (Western African Time)</option>
                        <option value="Africa/Libreville" <?php if ($fetClientes['timezone'] == "Africa/Libreville") { echo "selected"; } ?>>(GMT+1:00) Africa/Libreville (Western African Time)</option>
                        <option value="Africa/Luanda" <?php if ($fetClientes['timezone'] == "Africa/Luanda") { echo "selected"; } ?>>(GMT+1:00) Africa/Luanda (Western African Time)</option>
                        <option value="Africa/Malabo" <?php if ($fetClientes['timezone'] == "Africa/Malabo") { echo "selected"; } ?>>(GMT+1:00) Africa/Malabo (Western African Time)</option>
                        <option value="Africa/Ndjamena" <?php if ($fetClientes['timezone'] == "Africa/Ndjamena") { echo "selected"; } ?>>(GMT+1:00) Africa/Ndjamena (Western African Time)</option>
                        <option value="Africa/Niamey" <?php if ($fetClientes['timezone'] == "Africa/Niamey") { echo "selected"; } ?>>(GMT+1:00) Africa/Niamey (Western African Time)</option>
                        <option value="Africa/Porto-Novo" <?php if ($fetClientes['timezone'] == "Africa/Porto-Novo") { echo "selected"; } ?>>(GMT+1:00) Africa/Porto-Novo (Western African Time)</option>
                        <option value="Africa/Tunis" <?php if ($fetClientes['timezone'] == "Africa/Tunis") { echo "selected"; } ?>>(GMT+1:00) Africa/Tunis (Central European Time)</option>
                        <option value="Africa/Windhoek" <?php if ($fetClientes['timezone'] == "Africa/Windhoek") { echo "selected"; } ?>>(GMT+1:00) Africa/Windhoek (Western African Time)</option>
                        <option value="Arctic/Longyearbyen" <?php if ($fetClientes['timezone'] == "Arctic/Longyearbyen") { echo "selected"; } ?>>(GMT+1:00) Arctic/Longyearbyen (Central European Time)</option>
                        <option value="Atlantic/Jan_Mayen" <?php if ($fetClientes['timezone'] == "Atlantic/Jan_Mayen") { echo "selected"; } ?>>(GMT+1:00) Atlantic/Jan_Mayen (Central European Time)</option>
                        <option value="Europe/Amsterdam" <?php if ($fetClientes['timezone'] == "Europe/Amsterdam") { echo "selected"; } ?>>(GMT+1:00) Europe/Amsterdam (Central European Time)</option>
                        <option value="Europe/Andorra" <?php if ($fetClientes['timezone'] == "Europe/Andorra") { echo "selected"; } ?>>(GMT+1:00) Europe/Andorra (Central European Time)</option>
                        <option value="Europe/Belgrade" <?php if ($fetClientes['timezone'] == "Europe/Belgrade") { echo "selected"; } ?>>(GMT+1:00) Europe/Belgrade (Central European Time)</option>
                        <option value="Europe/Berlin" <?php if ($fetClientes['timezone'] == "Europe/Berlin") { echo "selected"; } ?>>(GMT+1:00) Europe/Berlin (Central European Time)</option>
                        <option value="Europe/Bratislava" <?php if ($fetClientes['timezone'] == "Europe/Bratislava") { echo "selected"; } ?>>(GMT+1:00) Europe/Bratislava (Central European Time)</option>
                        <option value="Europe/Brussels" <?php if ($fetClientes['timezone'] == "Europe/Brussels") { echo "selected"; } ?>>(GMT+1:00) Europe/Brussels (Central European Time)</option>
                        <option value="Europe/Budapest" <?php if ($fetClientes['timezone'] == "Europe/Budapest") { echo "selected"; } ?>>(GMT+1:00) Europe/Budapest (Central European Time)</option>
                        <option value="Europe/Copenhagen" <?php if ($fetClientes['timezone'] == "Europe/Copenhagen") { echo "selected"; } ?>>(GMT+1:00) Europe/Copenhagen (Central European Time)</option>
                        <option value="Europe/Gibraltar" <?php if ($fetClientes['timezone'] == "Europe/Gibraltar") { echo "selected"; } ?>>(GMT+1:00) Europe/Gibraltar (Central European Time)</option>
                        <option value="Europe/Ljubljana" <?php if ($fetClientes['timezone'] == "Europe/Ljubljana") { echo "selected"; } ?>>(GMT+1:00) Europe/Ljubljana (Central European Time)</option>
                        <option value="Europe/Luxembourg" <?php if ($fetClientes['timezone'] == "Europe/Luxembourg") { echo "selected"; } ?>>(GMT+1:00) Europe/Luxembourg (Central European Time)</option>
                        <option value="Europe/Madrid" <?php if ($fetClientes['timezone'] == "Europe/Madrid") { echo "selected"; } ?>>(GMT+1:00) Europe/Madrid (Central European Time)</option>
                        <option value="Europe/Malta" <?php if ($fetClientes['timezone'] == "Europe/Malta") { echo "selected"; } ?>>(GMT+1:00) Europe/Malta (Central European Time)</option>
                        <option value="Europe/Monaco" <?php if ($fetClientes['timezone'] == "Europe/Monaco") { echo "selected"; } ?>>(GMT+1:00) Europe/Monaco (Central European Time)</option>
                        <option value="Europe/Oslo" <?php if ($fetClientes['timezone'] == "Europe/Oslo") { echo "selected"; } ?>>(GMT+1:00) Europe/Oslo (Central European Time)</option>
                        <option value="Europe/Paris" <?php if ($fetClientes['timezone'] == "Europe/Paris") { echo "selected"; } ?>>(GMT+1:00) Europe/Paris (Central European Time)</option>
                        <option value="Europe/Podgorica" <?php if ($fetClientes['timezone'] == "Europe/Podgorica") { echo "selected"; } ?>>(GMT+1:00) Europe/Podgorica (Central European Time)</option>
                        <option value="Europe/Prague" <?php if ($fetClientes['timezone'] == "Europe/Prague") { echo "selected"; } ?>>(GMT+1:00) Europe/Prague (Central European Time)</option>
                        <option value="Europe/Rome" <?php if ($fetClientes['timezone'] == "Europe/Rome") { echo "selected"; } ?>>(GMT+1:00) Europe/Rome (Central European Time)</option>
                        <option value="Europe/San_Marino" <?php if ($fetClientes['timezone'] == "Europe/San_Marino") { echo "selected"; } ?>>(GMT+1:00) Europe/San_Marino (Central European Time)</option>
                        <option value="Europe/Sarajevo" <?php if ($fetClientes['timezone'] == "Europe/Sarajevo") { echo "selected"; } ?>>(GMT+1:00) Europe/Sarajevo (Central European Time)</option>
                        <option value="Europe/Skopje" <?php if ($fetClientes['timezone'] == "Europe/Skopje") { echo "selected"; } ?>>(GMT+1:00) Europe/Skopje (Central European Time)</option>
                        <option value="Europe/Stockholm" <?php if ($fetClientes['timezone'] == "Europe/Stockholm") { echo "selected"; } ?>>(GMT+1:00) Europe/Stockholm (Central European Time)</option>
                        <option value="Europe/Tirane" <?php if ($fetClientes['timezone'] == "Europe/Tirane") { echo "selected"; } ?>>(GMT+1:00) Europe/Tirane (Central European Time)</option>
                        <option value="Europe/Vaduz" <?php if ($fetClientes['timezone'] == "Europe/Vaduz") { echo "selected"; } ?>>(GMT+1:00) Europe/Vaduz (Central European Time)</option>
                        <option value="Europe/Vatican" <?php if ($fetClientes['timezone'] == "Europe/Vatican") { echo "selected"; } ?>>(GMT+1:00) Europe/Vatican (Central European Time)</option>
                        <option value="Europe/Vienna" <?php if ($fetClientes['timezone'] == "Europe/Vienna") { echo "selected"; } ?>>(GMT+1:00) Europe/Vienna (Central European Time)</option>
                        <option value="Europe/Warsaw" <?php if ($fetClientes['timezone'] == "Europe/Warsaw") { echo "selected"; } ?>>(GMT+1:00) Europe/Warsaw (Central European Time)</option>
                        <option value="Europe/Zagreb" <?php if ($fetClientes['timezone'] == "Europe/Zagreb") { echo "selected"; } ?>>(GMT+1:00) Europe/Zagreb (Central European Time)</option>
                        <option value="Europe/Zurich" <?php if ($fetClientes['timezone'] == "Europe/Zurich") { echo "selected"; } ?>>(GMT+1:00) Europe/Zurich (Central European Time)</option>
                        <option value="Africa/Blantyre" <?php if ($fetClientes['timezone'] == "Africa/Blantyre") { echo "selected"; } ?>>(GMT+2:00) Africa/Blantyre (Central African Time)</option>
                        <option value="Africa/Bujumbura" <?php if ($fetClientes['timezone'] == "Africa/Bujumbura") { echo "selected"; } ?>>(GMT+2:00) Africa/Bujumbura (Central African Time)</option>
                        <option value="Africa/Cairo" <?php if ($fetClientes['timezone'] == "Africa/Cairo") { echo "selected"; } ?>>(GMT+2:00) Africa/Cairo (Eastern European Time)</option>
                        <option value="Africa/Gaborone" <?php if ($fetClientes['timezone'] == "Africa/Gaborone") { echo "selected"; } ?>>(GMT+2:00) Africa/Gaborone (Central African Time)</option>
                        <option value="Africa/Harare" <?php if ($fetClientes['timezone'] == "Africa/Harare") { echo "selected"; } ?>>(GMT+2:00) Africa/Harare (Central African Time)</option>
                        <option value="Africa/Johannesburg" <?php if ($fetClientes['timezone'] == "Africa/Johannesburg") { echo "selected"; } ?>>(GMT+2:00) Africa/Johannesburg (South Africa Standard Time)</option>
                        <option value="Africa/Kigali" <?php if ($fetClientes['timezone'] == "Africa/Kigali") { echo "selected"; } ?>>(GMT+2:00) Africa/Kigali (Central African Time)</option>
                        <option value="Africa/Lubumbashi" <?php if ($fetClientes['timezone'] == "Africa/Lubumbashi") { echo "selected"; } ?>>(GMT+2:00) Africa/Lubumbashi (Central African Time)</option>
                        <option value="Africa/Lusaka" <?php if ($fetClientes['timezone'] == "Africa/Lusaka") { echo "selected"; } ?>>(GMT+2:00) Africa/Lusaka (Central African Time)</option>
                        <option value="Africa/Maputo" <?php if ($fetClientes['timezone'] == "Africa/Maputo") { echo "selected"; } ?>>(GMT+2:00) Africa/Maputo (Central African Time)</option>
                        <option value="Africa/Maseru" <?php if ($fetClientes['timezone'] == "Africa/Maseru") { echo "selected"; } ?>>(GMT+2:00) Africa/Maseru (South Africa Standard Time)</option>
                        <option value="Africa/Mbabane" <?php if ($fetClientes['timezone'] == "Africa/Mbabane") { echo "selected"; } ?>>(GMT+2:00) Africa/Mbabane (South Africa Standard Time)</option>
                        <option value="Africa/Tripoli" <?php if ($fetClientes['timezone'] == "Africa/Tripoli") { echo "selected"; } ?>>(GMT+2:00) Africa/Tripoli (Eastern European Time)</option>
                        <option value="Asia/Amman" <?php if ($fetClientes['timezone'] == "Asia/Amman") { echo "selected"; } ?>>(GMT+2:00) Asia/Amman (Eastern European Time)</option>
                        <option value="Asia/Beirut" <?php if ($fetClientes['timezone'] == "Asia/Beirut") { echo "selected"; } ?>>(GMT+2:00) Asia/Beirut (Eastern European Time)</option>
                        <option value="Asia/Damascus" <?php if ($fetClientes['timezone'] == "Asia/Damascus") { echo "selected"; } ?>>(GMT+2:00) Asia/Damascus (Eastern European Time)</option>
                        <option value="Asia/Gaza" <?php if ($fetClientes['timezone'] == "Asia/Gaza") { echo "selected"; } ?>>(GMT+2:00) Asia/Gaza (Eastern European Time)</option>
                        <option value="Asia/Istanbul" <?php if ($fetClientes['timezone'] == "Asia/Istanbul") { echo "selected"; } ?>>(GMT+2:00) Asia/Istanbul (Eastern European Time)</option>
                        <option value="Asia/Jerusalem" <?php if ($fetClientes['timezone'] == "Asia/Jerusalem") { echo "selected"; } ?>>(GMT+2:00) Asia/Jerusalem (Israel Standard Time)</option>
                        <option value="Asia/Nicosia" <?php if ($fetClientes['timezone'] == "Asia/Nicosia") { echo "selected"; } ?>>(GMT+2:00) Asia/Nicosia (Eastern European Time)</option>
                        <option value="Asia/Tel_Aviv" <?php if ($fetClientes['timezone'] == "Asia/Tel_Aviv") { echo "selected"; } ?>>(GMT+2:00) Asia/Tel_Aviv (Israel Standard Time)</option>
                        <option value="Europe/Athens" <?php if ($fetClientes['timezone'] == "Europe/Athens") { echo "selected"; } ?>>(GMT+2:00) Europe/Athens (Eastern European Time)</option>
                        <option value="Europe/Bucharest" <?php if ($fetClientes['timezone'] == "Europe/Bucharest") { echo "selected"; } ?>>(GMT+2:00) Europe/Bucharest (Eastern European Time)</option>
                        <option value="Europe/Chisinau" <?php if ($fetClientes['timezone'] == "Europe/Chisinau") { echo "selected"; } ?>>(GMT+2:00) Europe/Chisinau (Eastern European Time)</option>
                        <option value="Europe/Helsinki" <?php if ($fetClientes['timezone'] == "Europe/Helsinki") { echo "selected"; } ?>>(GMT+2:00) Europe/Helsinki (Eastern European Time)</option>
                        <option value="Europe/Istanbul" <?php if ($fetClientes['timezone'] == "Europe/Istanbul") { echo "selected"; } ?>>(GMT+2:00) Europe/Istanbul (Eastern European Time)</option>
                        <option value="Europe/Kaliningrad" <?php if ($fetClientes['timezone'] == "Europe/Kaliningrad") { echo "selected"; } ?>>(GMT+2:00) Europe/Kaliningrad (Eastern European Time)</option>
                        <option value="Europe/Kiev" <?php if ($fetClientes['timezone'] == "Europe/Kiev") { echo "selected"; } ?>>(GMT+2:00) Europe/Kiev (Eastern European Time)</option>
                        <option value="Europe/Mariehamn" <?php if ($fetClientes['timezone'] == "Europe/Mariehamn") { echo "selected"; } ?>>(GMT+2:00) Europe/Mariehamn (Eastern European Time)</option>
                        <option value="Europe/Minsk" <?php if ($fetClientes['timezone'] == "Europe/Minsk") { echo "selected"; } ?>>(GMT+2:00) Europe/Minsk (Eastern European Time)</option>
                        <option value="Europe/Nicosia" <?php if ($fetClientes['timezone'] == "Europe/Nicosia") { echo "selected"; } ?>>(GMT+2:00) Europe/Nicosia (Eastern European Time)</option>
                        <option value="Europe/Riga" <?php if ($fetClientes['timezone'] == "Europe/Riga") { echo "selected"; } ?>>(GMT+2:00) Europe/Riga (Eastern European Time)</option>
                        <option value="Europe/Simferopol" <?php if ($fetClientes['timezone'] == "Europe/Simferopol") { echo "selected"; } ?>>(GMT+2:00) Europe/Simferopol (Eastern European Time)</option>
                        <option value="Europe/Sofia" <?php if ($fetClientes['timezone'] == "Europe/Sofia") { echo "selected"; } ?>>(GMT+2:00) Europe/Sofia (Eastern European Time)</option>
                        <option value="Europe/Tallinn" <?php if ($fetClientes['timezone'] == "Europe/Tallinn") { echo "selected"; } ?>>(GMT+2:00) Europe/Tallinn (Eastern European Time)</option>
                        <option value="Europe/Tiraspol" <?php if ($fetClientes['timezone'] == "Europe/Tiraspol") { echo "selected"; } ?>>(GMT+2:00) Europe/Tiraspol (Eastern European Time)</option>
                        <option value="Europe/Uzhgorod" <?php if ($fetClientes['timezone'] == "Europe/Uzhgorod") { echo "selected"; } ?>>(GMT+2:00) Europe/Uzhgorod (Eastern European Time)</option>
                        <option value="Europe/Vilnius" <?php if ($fetClientes['timezone'] == "Europe/Vilnius") { echo "selected"; } ?>>(GMT+2:00) Europe/Vilnius (Eastern European Time)</option>
                        <option value="Europe/Zaporozhye" <?php if ($fetClientes['timezone'] == "Europe/Zaporozhye") { echo "selected"; } ?>>(GMT+2:00) Europe/Zaporozhye (Eastern European Time)</option>
                        <option value="Africa/Addis_Ababa" <?php if ($fetClientes['timezone'] == "Africa/Addis_Ababa") { echo "selected"; } ?>>(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)</option>
                        <option value="Africa/Asmara" <?php if ($fetClientes['timezone'] == "Africa/Asmara") { echo "selected"; } ?>>(GMT+3:00) Africa/Asmara (Eastern African Time)</option>
                        <option value="Africa/Asmera" <?php if ($fetClientes['timezone'] == "Africa/Asmera") { echo "selected"; } ?>>(GMT+3:00) Africa/Asmera (Eastern African Time)</option>
                        <option value="Africa/Dar_es_Salaam" <?php if ($fetClientes['timezone'] == "Africa/Dar_es_Salaam") { echo "selected"; } ?>>(GMT+3:00) Africa/Dar_es_Salaam (Eastern African Time)</option>
                        <option value="Africa/Djibouti" <?php if ($fetClientes['timezone'] == "Africa/Djibouti") { echo "selected"; } ?>>(GMT+3:00) Africa/Djibouti (Eastern African Time)</option>
                        <option value="Africa/Kampala" <?php if ($fetClientes['timezone'] == "Africa/Kampala") { echo "selected"; } ?>>(GMT+3:00) Africa/Kampala (Eastern African Time)</option>
                        <option value="Africa/Khartoum" <?php if ($fetClientes['timezone'] == "Africa/Khartoum") { echo "selected"; } ?>>(GMT+3:00) Africa/Khartoum (Eastern African Time)</option>
                        <option value="Africa/Mogadishu" <?php if ($fetClientes['timezone'] == "Africa/Mogadishu") { echo "selected"; } ?>>(GMT+3:00) Africa/Mogadishu (Eastern African Time)</option>
                        <option value="Africa/Nairobi" <?php if ($fetClientes['timezone'] == "Africa/Nairobi") { echo "selected"; } ?>>(GMT+3:00) Africa/Nairobi (Eastern African Time)</option>
                        <option value="Antarctica/Syowa" <?php if ($fetClientes['timezone'] == "Antarctica/Syowa") { echo "selected"; } ?>>(GMT+3:00) Antarctica/Syowa (Syowa Time)</option>
                        <option value="Asia/Aden" <?php if ($fetClientes['timezone'] == "Asia/Aden") { echo "selected"; } ?>>(GMT+3:00) Asia/Aden (Arabia Standard Time)</option>
                        <option value="Asia/Baghdad" <?php if ($fetClientes['timezone'] == "Asia/Baghdad") { echo "selected"; } ?>>(GMT+3:00) Asia/Baghdad (Arabia Standard Time)</option>
                        <option value="Asia/Bahrain" <?php if ($fetClientes['timezone'] == "Asia/Bahrain") { echo "selected"; } ?>>(GMT+3:00) Asia/Bahrain (Arabia Standard Time)</option>
                        <option value="Asia/Kuwait" <?php if ($fetClientes['timezone'] == "Asia/Kuwait") { echo "selected"; } ?>>(GMT+3:00) Asia/Kuwait (Arabia Standard Time)</option>
                        <option value="Asia/Qatar" <?php if ($fetClientes['timezone'] == "Asia/Qatar") { echo "selected"; } ?>>(GMT+3:00) Asia/Qatar (Arabia Standard Time)</option>
                        <option value="Europe/Moscow" <?php if ($fetClientes['timezone'] == "Europe/Moscow") { echo "selected"; } ?>>(GMT+3:00) Europe/Moscow (Moscow Standard Time)</option>
                        <option value="Europe/Volgograd" <?php if ($fetClientes['timezone'] == "Europe/Volgograd") { echo "selected"; } ?>>(GMT+3:00) Europe/Volgograd (Volgograd Time)</option>
                        <option value="Indian/Antananarivo" <?php if ($fetClientes['timezone'] == "Indian/Antananarivo") { echo "selected"; } ?>>(GMT+3:00) Indian/Antananarivo (Eastern African Time)</option>
                        <option value="Indian/Comoro" <?php if ($fetClientes['timezone'] == "Indian/Comoro") { echo "selected"; } ?>>(GMT+3:00) Indian/Comoro (Eastern African Time)</option>
                        <option value="Indian/Mayotte" <?php if ($fetClientes['timezone'] == "Indian/Mayotte") { echo "selected"; } ?>>(GMT+3:00) Indian/Mayotte (Eastern African Time)</option>
                        <option value="Asia/Tehran" <?php if ($fetClientes['timezone'] == "Asia/Tehran") { echo "selected"; } ?>>(GMT+3:30) Asia/Tehran (Iran Standard Time)</option>
                        <option value="Asia/Baku" <?php if ($fetClientes['timezone'] == "Asia/Baku") { echo "selected"; } ?>>(GMT+4:00) Asia/Baku (Azerbaijan Time)</option>
                        <option value="Asia/Dubai" <?php if ($fetClientes['timezone'] == "Asia/Dubai") { echo "selected"; } ?>>(GMT+4:00) Asia/Dubai (Gulf Standard Time)</option>
                        <option value="Asia/Muscat" <?php if ($fetClientes['timezone'] == "Asia/Muscat") { echo "selected"; } ?>>(GMT+4:00) Asia/Muscat (Gulf Standard Time)</option>
                        <option value="Asia/Tbilisi" <?php if ($fetClientes['timezone'] == "Asia/Tbilisi") { echo "selected"; } ?>>(GMT+4:00) Asia/Tbilisi (Georgia Time)</option>
                        <option value="Asia/Yerevan" <?php if ($fetClientes['timezone'] == "Asia/Yerevan") { echo "selected"; } ?>>(GMT+4:00) Asia/Yerevan (Armenia Time)</option>
                        <option value="Europe/Samara" <?php if ($fetClientes['timezone'] == "Europe/Samara") { echo "selected"; } ?>>(GMT+4:00) Europe/Samara (Samara Time)</option>
                        <option value="Indian/Mahe" <?php if ($fetClientes['timezone'] == "Indian/Mahe") { echo "selected"; } ?>>(GMT+4:00) Indian/Mahe (Seychelles Time)</option>
                        <option value="Indian/Mauritius" <?php if ($fetClientes['timezone'] == "Indian/Mauritius") { echo "selected"; } ?>>(GMT+4:00) Indian/Mauritius (Mauritius Time)</option>
                        <option value="Indian/Reunion" <?php if ($fetClientes['timezone'] == "Indian/Reunion") { echo "selected"; } ?>>(GMT+4:00) Indian/Reunion (Reunion Time)</option>
                        <option value="Asia/Kabul" <?php if ($fetClientes['timezone'] == "Asia/Kabul") { echo "selected"; } ?>>(GMT+4:30) Asia/Kabul (Afghanistan Time)</option>
                        <option value="Asia/Aqtau" <?php if ($fetClientes['timezone'] == "Asia/Aqtau") { echo "selected"; } ?>>(GMT+5:00) Asia/Aqtau (Aqtau Time)</option>
                        <option value="Asia/Aqtobe" <?php if ($fetClientes['timezone'] == "Asia/Aqtobe") { echo "selected"; } ?>>(GMT+5:00) Asia/Aqtobe (Aqtobe Time)</option>
                        <option value="Asia/Ashgabat" <?php if ($fetClientes['timezone'] == "Asia/Ashgabat") { echo "selected"; } ?>>(GMT+5:00) Asia/Ashgabat (Turkmenistan Time)</option>
                        <option value="Asia/Ashkhabad" <?php if ($fetClientes['timezone'] == "Asia/Ashkhabad") { echo "selected"; } ?>>(GMT+5:00) Asia/Ashkhabad (Turkmenistan Time)</option>
                        <option value="Asia/Dushanbe" <?php if ($fetClientes['timezone'] == "Asia/Dushanbe") { echo "selected"; } ?>>(GMT+5:00) Asia/Dushanbe (Tajikistan Time)</option>
                        <option value="Asia/Karachi" <?php if ($fetClientes['timezone'] == "Asia/Karachi") { echo "selected"; } ?>>(GMT+5:00) Asia/Karachi (Pakistan Time)</option>
                        <option value="Asia/Oral" <?php if ($fetClientes['timezone'] == "Asia/Oral") { echo "selected"; } ?>>(GMT+5:00) Asia/Oral (Oral Time)</option>
                        <option value="Asia/Samarkand" <?php if ($fetClientes['timezone'] == "Asia/Samarkand") { echo "selected"; } ?>>(GMT+5:00) Asia/Samarkand (Uzbekistan Time)</option>
                        <option value="Asia/Tashkent" <?php if ($fetClientes['timezone'] == "Asia/Tashkent") { echo "selected"; } ?>>(GMT+5:00) Asia/Tashkent (Uzbekistan Time)</option>
                        <option value="Asia/Yekaterinburg" <?php if ($fetClientes['timezone'] == "Asia/Yekaterinburg") { echo "selected"; } ?>>(GMT+5:00) Asia/Yekaterinburg (Yekaterinburg Time)</option>
                        <option value="Indian/Kerguelen" <?php if ($fetClientes['timezone'] == "Indian/Kerguelen") { echo "selected"; } ?>>(GMT+5:00) Indian/Kerguelen (French Southern & Antarctic Lands Time)</option>
                        <option value="Indian/Maldives" <?php if ($fetClientes['timezone'] == "Indian/Maldives") { echo "selected"; } ?>>(GMT+5:00) Indian/Maldives (Maldives Time)</option>
                        <option value="Asia/Calcutta" <?php if ($fetClientes['timezone'] == "Asia/Calcutta") { echo "selected"; } ?>>(GMT+5:30) Asia/Calcutta (India Standard Time)</option>
                        <option value="Asia/Colombo" <?php if ($fetClientes['timezone'] == "Asia/Colombo") { echo "selected"; } ?>>(GMT+5:30) Asia/Colombo (India Standard Time)</option>
                        <option value="Asia/Kolkata" <?php if ($fetClientes['timezone'] == "Asia/Kolkata") { echo "selected"; } ?>>(GMT+5:30) Asia/Kolkata (India Standard Time)</option>
                        <option value="Asia/Katmandu" <?php if ($fetClientes['timezone'] == "Asia/Katmandu") { echo "selected"; } ?>>(GMT+5:45) Asia/Katmandu (Nepal Time)</option>
                        <option value="Antarctica/Mawson" <?php if ($fetClientes['timezone'] == "Antarctica/Mawson") { echo "selected"; } ?>>(GMT+6:00) Antarctica/Mawson (Mawson Time)</option>
                        <option value="Antarctica/Vostok" <?php if ($fetClientes['timezone'] == "Antarctica/Vostok") { echo "selected"; } ?>>(GMT+6:00) Antarctica/Vostok (Vostok Time)</option>
                        <option value="Asia/Almaty" <?php if ($fetClientes['timezone'] == "Asia/Almaty") { echo "selected"; } ?>>(GMT+6:00) Asia/Almaty (Alma-Ata Time)</option>
                        <option value="Asia/Bishkek" <?php if ($fetClientes['timezone'] == "Asia/Bishkek") { echo "selected"; } ?>>(GMT+6:00) Asia/Bishkek (Kirgizstan Time)</option>
                        <option value="Asia/Dacca" <?php if ($fetClientes['timezone'] == "Asia/Dacca") { echo "selected"; } ?>>(GMT+6:00) Asia/Dacca (Bangladesh Time)</option>
                        <option value="Asia/Dhaka" <?php if ($fetClientes['timezone'] == "Asia/Dhaka") { echo "selected"; } ?>>(GMT+6:00) Asia/Dhaka (Bangladesh Time)</option>
                        <option value="Asia/Novosibirsk" <?php if ($fetClientes['timezone'] == "Asia/Novosibirsk") { echo "selected"; } ?>>(GMT+6:00) Asia/Novosibirsk (Novosibirsk Time)</option>
                        <option value="Asia/Omsk" <?php if ($fetClientes['timezone'] == "Asia/Omsk") { echo "selected"; } ?>>(GMT+6:00) Asia/Omsk (Omsk Time)</option>
                        <option value="Asia/Qyzylorda" <?php if ($fetClientes['timezone'] == "Asia/Qyzylorda") { echo "selected"; } ?>>(GMT+6:00) Asia/Qyzylorda (Qyzylorda Time)</option>
                        <option value="Asia/Thimbu" <?php if ($fetClientes['timezone'] == "Asia/Thimbu") { echo "selected"; } ?>>(GMT+6:00) Asia/Thimbu (Bhutan Time)</option>
                        <option value="Asia/Thimphu" <?php if ($fetClientes['timezone'] == "Asia/Thimphu") { echo "selected"; } ?>>(GMT+6:00) Asia/Thimphu (Bhutan Time)</option>
                        <option value="Indian/Chagos" <?php if ($fetClientes['timezone'] == "Indian/Chagos") { echo "selected"; } ?>>(GMT+6:00) Indian/Chagos (Indian Ocean Territory Time)</option>
                        <option value="Asia/Rangoon" <?php if ($fetClientes['timezone'] == "Asia/Rangoon") { echo "selected"; } ?>>(GMT+6:30) Asia/Rangoon (Myanmar Time)</option>
                        <option value="Indian/Cocos" <?php if ($fetClientes['timezone'] == "Indian/Cocos") { echo "selected"; } ?>>(GMT+6:30) Indian/Cocos (Cocos Islands Time)</option>
                        <option value="Antarctica/Davis" <?php if ($fetClientes['timezone'] == "Antarctica/Davis") { echo "selected"; } ?>>(GMT+7:00) Antarctica/Davis (Davis Time)</option>
                        <option value="Asia/Bangkok" <?php if ($fetClientes['timezone'] == "Asia/Bangkok") { echo "selected"; } ?>>(GMT+7:00) Asia/Bangkok (Indochina Time)</option>
                        <option value="Asia/Ho_Chi_Minh" <?php if ($fetClientes['timezone'] == "Asia/Ho_Chi_Minh") { echo "selected"; } ?>>(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)</option>
                        <option value="Asia/Hovd" <?php if ($fetClientes['timezone'] == "Asia/Hovd") { echo "selected"; } ?>>(GMT+7:00) Asia/Hovd (Hovd Time)</option>
                        <option value="Asia/Jakarta" <?php if ($fetClientes['timezone'] == "Asia/Jakarta") { echo "selected"; } ?>>(GMT+7:00) Asia/Jakarta (West Indonesia Time)</option>
                        <option value="Asia/Krasnoyarsk" <?php if ($fetClientes['timezone'] == "Asia/Krasnoyarsk") { echo "selected"; } ?>>(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)</option>
                        <option value="Asia/Phnom_Penh" <?php if ($fetClientes['timezone'] == "Asia/Phnom_Penh") { echo "selected"; } ?>>(GMT+7:00) Asia/Phnom_Penh (Indochina Time)</option>
                        <option value="Asia/Pontianak" <?php if ($fetClientes['timezone'] == "Asia/Pontianak") { echo "selected"; } ?>>(GMT+7:00) Asia/Pontianak (West Indonesia Time)</option>
                        <option value="Asia/Saigon" <?php if ($fetClientes['timezone'] == "Asia/Saigon") { echo "selected"; } ?>>(GMT+7:00) Asia/Saigon (Indochina Time)</option>
                        <option value="Asia/Vientiane" <?php if ($fetClientes['timezone'] == "Asia/Vientiane") { echo "selected"; } ?>>(GMT+7:00) Asia/Vientiane (Indochina Time)</option>
                        <option value="Indian/Christmas" <?php if ($fetClientes['timezone'] == "Indian/Christmas") { echo "selected"; } ?>>(GMT+7:00) Indian/Christmas (Christmas Island Time)</option>
                        <option value="Antarctica/Casey" <?php if ($fetClientes['timezone'] == "Antarctica/Casey") { echo "selected"; } ?>>(GMT+8:00) Antarctica/Casey (Western Standard Time (Australia))</option>
                        <option value="Asia/Brunei" <?php if ($fetClientes['timezone'] == "Asia/Brunei") { echo "selected"; } ?>>(GMT+8:00) Asia/Brunei (Brunei Time)</option>
                        <option value="Asia/Choibalsan" <?php if ($fetClientes['timezone'] == "Asia/Choibalsan") { echo "selected"; } ?>>(GMT+8:00) Asia/Choibalsan (Choibalsan Time)</option>
                        <option value="Asia/Chongqing" <?php if ($fetClientes['timezone'] == "Asia/Chongqing") { echo "selected"; } ?>>(GMT+8:00) Asia/Chongqing (China Standard Time)</option>
                        <option value="Asia/Chungking" <?php if ($fetClientes['timezone'] == "Asia/Chungking") { echo "selected"; } ?>>(GMT+8:00) Asia/Chungking (China Standard Time)</option>
                        <option value="Asia/Harbin" <?php if ($fetClientes['timezone'] == "Asia/Harbin") { echo "selected"; } ?>>(GMT+8:00) Asia/Harbin (China Standard Time)</option>
                        <option value="Asia/Hong_Kong" <?php if ($fetClientes['timezone'] == "Asia/Hong_Kong") { echo "selected"; } ?>>(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)</option>
                        <option value="Asia/Irkutsk" <?php if ($fetClientes['timezone'] == "Asia/Irkutsk") { echo "selected"; } ?>>(GMT+8:00) Asia/Irkutsk (Irkutsk Time)</option>
                        <option value="Asia/Kashgar" <?php if ($fetClientes['timezone'] == "Asia/Kashgar") { echo "selected"; } ?>>(GMT+8:00) Asia/Kashgar (China Standard Time)</option>
                        <option value="Asia/Kuala_Lumpur" <?php if ($fetClientes['timezone'] == "Asia/Kuala_Lumpur") { echo "selected"; } ?>>(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)</option>
                        <option value="Asia/Kuching" <?php if ($fetClientes['timezone'] == "Asia/Kuching") { echo "selected"; } ?>>(GMT+8:00) Asia/Kuching (Malaysia Time)</option>
                        <option value="Asia/Macao" <?php if ($fetClientes['timezone'] == "Asia/Macao") { echo "selected"; } ?>>(GMT+8:00) Asia/Macao (China Standard Time)</option>
                        <option value="Asia/Macau" <?php if ($fetClientes['timezone'] == "Asia/Macau") { echo "selected"; } ?>>(GMT+8:00) Asia/Macau (China Standard Time)</option>
                        <option value="Asia/Makassar" <?php if ($fetClientes['timezone'] == "Asia/Makassar") { echo "selected"; } ?>>(GMT+8:00) Asia/Makassar (Central Indonesia Time)</option>
                        <option value="Asia/Manila" <?php if ($fetClientes['timezone'] == "Asia/Manila") { echo "selected"; } ?>>(GMT+8:00) Asia/Manila (Philippines Time)</option>
                        <option value="Asia/Shanghai" <?php if ($fetClientes['timezone'] == "Asia/Shanghai") { echo "selected"; } ?>>(GMT+8:00) Asia/Shanghai (China Standard Time)</option>
                        <option value="Asia/Singapore" <?php if ($fetClientes['timezone'] == "Asia/Singapore") { echo "selected"; } ?>>(GMT+8:00) Asia/Singapore (Singapore Time)</option>
                        <option value="Asia/Taipei" <?php if ($fetClientes['timezone'] == "Asia/Taipei") { echo "selected"; } ?>>(GMT+8:00) Asia/Taipei (China Standard Time)</option>
                        <option value="Asia/Ujung_Pandang" <?php if ($fetClientes['timezone'] == "Asia/Ujung_Pandang") { echo "selected"; } ?>>(GMT+8:00) Asia/Ujung_Pandang (Central Indonesia Time)</option>
                        <option value="Asia/Ulaanbaatar" <?php if ($fetClientes['timezone'] == "Asia/Ulaanbaatar") { echo "selected"; } ?>>(GMT+8:00) Asia/Ulaanbaatar (Ulaanbaatar Time)</option>
                        <option value="Asia/Ulan_Bator" <?php if ($fetClientes['timezone'] == "Asia/Ulan_Bator") { echo "selected"; } ?>>(GMT+8:00) Asia/Ulan_Bator (Ulaanbaatar Time)</option>
                        <option value="Asia/Urumqi" <?php if ($fetClientes['timezone'] == "Asia/Urumqi") { echo "selected"; } ?>>(GMT+8:00) Asia/Urumqi (China Standard Time)</option>
                        <option value="Australia/Perth" <?php if ($fetClientes['timezone'] == "Australia/Perth") { echo "selected"; } ?>>(GMT+8:00) Australia/Perth (Western Standard Time (Australia))</option>
                        <option value="Australia/West" <?php if ($fetClientes['timezone'] == "Australia/West") { echo "selected"; } ?>>(GMT+8:00) Australia/West (Western Standard Time (Australia))</option>
                        <option value="Australia/Eucla" <?php if ($fetClientes['timezone'] == "Australia/Eucla") { echo "selected"; } ?>>(GMT+8:45) Australia/Eucla (Central Western Standard Time (Australia))</option>
                        <option value="Asia/Dili" <?php if ($fetClientes['timezone'] == "Asia/Dili") { echo "selected"; } ?>>(GMT+9:00) Asia/Dili (Timor-Leste Time)</option>
                        <option value="Asia/Jayapura" <?php if ($fetClientes['timezone'] == "Asia/Jayapura") { echo "selected"; } ?>>(GMT+9:00) Asia/Jayapura (East Indonesia Time)</option>
                        <option value="Asia/Pyongyang" <?php if ($fetClientes['timezone'] == "Asia/Pyongyang") { echo "selected"; } ?>>(GMT+9:00) Asia/Pyongyang (Korea Standard Time)</option>
                        <option value="Asia/Seoul" <?php if ($fetClientes['timezone'] == "Asia/Seoul") { echo "selected"; } ?>>(GMT+9:00) Asia/Seoul (Korea Standard Time)</option>
                        <option value="Asia/Tokyo" <?php if ($fetClientes['timezone'] == "Asia/Tokyo") { echo "selected"; } ?>>(GMT+9:00) Asia/Tokyo (Japan Standard Time)</option>
                        <option value="Asia/Yakutsk" <?php if ($fetClientes['timezone'] == "Asia/Yakutsk") { echo "selected"; } ?>>(GMT+9:00) Asia/Yakutsk (Yakutsk Time)</option>
                        <option value="Australia/Adelaide" <?php if ($fetClientes['timezone'] == "Australia/Adelaide") { echo "selected"; } ?>>(GMT+9:30) Australia/Adelaide (Central Standard Time (South Australia))</option>
                        <option value="Australia/Broken_Hill" <?php if ($fetClientes['timezone'] == "Australia/Broken_Hill") { echo "selected"; } ?>>(GMT+9:30) Australia/Broken_Hill (Central Standard Time (South Australia/New South Wales))</option>
                        <option value="Australia/Darwin" <?php if ($fetClientes['timezone'] == "Australia/Darwin") { echo "selected"; } ?>>(GMT+9:30) Australia/Darwin (Central Standard Time (Northern Territory))</option>
                        <option value="Australia/North" <?php if ($fetClientes['timezone'] == "Australia/North") { echo "selected"; } ?>>(GMT+9:30) Australia/North (Central Standard Time (Northern Territory))</option>
                        <option value="Australia/South" <?php if ($fetClientes['timezone'] == "Australia/South") { echo "selected"; } ?>>(GMT+9:30) Australia/South (Central Standard Time (South Australia))</option>
                        <option value="Australia/Yancowinna" <?php if ($fetClientes['timezone'] == "Australia/Yancowinna") { echo "selected"; } ?>>(GMT+9:30) Australia/Yancowinna (Central Standard Time (South Australia/New South Wales))</option>
                        <option value="Antarctica/DumontDUrville" <?php if ($fetClientes['timezone'] == "Antarctica/DumontDUrville") { echo "selected"; } ?>>(GMT+10:00) Antarctica/DumontDUrville (Dumont-d Urville Time)</option>
                        <option value="Asia/Sakhalin" <?php if ($fetClientes['timezone'] == "Asia/Sakhalin") { echo "selected"; } ?>>(GMT+10:00) Asia/Sakhalin (Sakhalin Time)</option>
                        <option value="Asia/Vladivostok" <?php if ($fetClientes['timezone'] == "Asia/Vladivostok") { echo "selected"; } ?>>(GMT+10:00) Asia/Vladivostok (Vladivostok Time)</option>
                        <option value="Australia/ACT" <?php if ($fetClientes['timezone'] == "Australia/ACT") { echo "selected"; } ?>>(GMT+10:00) Australia/ACT (Eastern Standard Time (New South Wales))</option>
                        <option value="Australia/Brisbane" <?php if ($fetClientes['timezone'] == "Australia/Brisbane") { echo "selected"; } ?>>(GMT+10:00) Australia/Brisbane (Eastern Standard Time (Queensland))</option>
                        <option value="Australia/Canberra" <?php if ($fetClientes['timezone'] == "Australia/Canberra") { echo "selected"; } ?>>(GMT+10:00) Australia/Canberra (Eastern Standard Time (New South Wales))</option>
                        <option value="Australia/Currie" <?php if ($fetClientes['timezone'] == "Australia/Currie") { echo "selected"; } ?>>(GMT+10:00) Australia/Currie (Eastern Standard Time (New South Wales))</option>
                        <option value="Australia/Hobart" <?php if ($fetClientes['timezone'] == "Australia/Hobart") { echo "selected"; } ?>>(GMT+10:00) Australia/Hobart (Eastern Standard Time (Tasmania))</option>
                        <option value="Australia/Lindeman" <?php if ($fetClientes['timezone'] == "Australia/Lindeman") { echo "selected"; } ?>>(GMT+10:00) Australia/Lindeman (Eastern Standard Time (Queensland))</option>
                        <option value="Australia/Melbourne" <?php if ($fetClientes['timezone'] == "Australia/Melbourne") { echo "selected"; } ?>>(GMT+10:00) Australia/Melbourne (Eastern Standard Time (Victoria))</option>
                        <option value="Australia/NSW" <?php if ($fetClientes['timezone'] == "Australia/NSW") { echo "selected"; } ?>>(GMT+10:00) Australia/NSW (Eastern Standard Time (New South Wales))</option>
                        <option value="Australia/Queensland" <?php if ($fetClientes['timezone'] == "Australia/Queensland") { echo "selected"; } ?>>(GMT+10:00) Australia/Queensland (Eastern Standard Time (Queensland))</option>
                        <option value="Australia/Sydney" <?php if ($fetClientes['timezone'] == "Australia/Sydney") { echo "selected"; } ?>>(GMT+10:00) Australia/Sydney (Eastern Standard Time (New South Wales))</option>
                        <option value="Australia/Tasmania" <?php if ($fetClientes['timezone'] == "Australia/Tasmania") { echo "selected"; } ?>>(GMT+10:00) Australia/Tasmania (Eastern Standard Time (Tasmania))</option>
                        <option value="Australia/Victoria" <?php if ($fetClientes['timezone'] == "Australia/Victoria") { echo "selected"; } ?>>(GMT+10:00) Australia/Victoria (Eastern Standard Time (Victoria))</option>
                        <option value="Australia/LHI" <?php if ($fetClientes['timezone'] == "Australia/LHI") { echo "selected"; } ?>>(GMT+10:30) Australia/LHI (Lord Howe Standard Time)</option>
                        <option value="Australia/Lord_Howe" <?php if ($fetClientes['timezone'] == "Australia/Lord_Howe") { echo "selected"; } ?>>(GMT+10:30) Australia/Lord_Howe (Lord Howe Standard Time)</option>
                        <option value="Asia/Magadan" <?php if ($fetClientes['timezone'] == "Asia/Magadan") { echo "selected"; } ?>>(GMT+11:00) Asia/Magadan (Magadan Time)</option>
                        <option value="Antarctica/McMurdo" <?php if ($fetClientes['timezone'] == "Antarctica/McMurdo") { echo "selected"; } ?>>(GMT+12:00) Antarctica/McMurdo (New Zealand Standard Time)</option>
                        <option value="Antarctica/South_Pole" <?php if ($fetClientes['timezone'] == "Antarctica/South_Pole") { echo "selected"; } ?>>(GMT+12:00) Antarctica/South_Pole (New Zealand Standard Time)</option>
                        <option value="Asia/Anadyr" <?php if ($fetClientes['timezone'] == "Asia/Anadyr") { echo "selected"; } ?>>(GMT+12:00) Asia/Anadyr (Anadyr Time)</option>
                        <option value="Asia/Kamchatka" <?php if ($fetClientes['timezone'] == "Asia/Kamchatka") { echo "selected"; } ?>>(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)</option>
                    </select>
                </div>

                <!-- Gerenciamento de linguagem [1: Português, 2: Inglês, 3: Espanhol] -->
                <div class="QuadroConfigs">
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][124]?></div>
                    <select name="linguagem" id="linguagem" class="selectGInput" style="width: 90%; font-size: 12px; height: 33px;">
                        <option value="1" <?php if ($fetClientes['linguagem'] == 1) { echo "selected"; } ?>><?=$GLOBALS['lang'][125]?></option>
                        <option value="2" <?php if ($fetClientes['linguagem'] == 2) { echo "selected"; } ?>><?=$GLOBALS['lang'][126]?></option>
                        <option value="3" <?php if ($fetClientes['linguagem'] == 3) { echo "selected"; } ?>><?=$GLOBALS['lang'][127]?></option>
                    </select>
                </div>

                <br>
            </div>

            <div id="div_aba2" class="MostraSensor" style="display:none; padding-bottom: 20px;">
                <br>
                <!-- Integrações do Sistema -->
                <div id="container"><?=$GLOBALS['lang'][128]?></div>

                <div class="QuadroConfigs">

                    <table border="0" cellspacing="0" cellpadding="0" style="padding: 10px;">
                        <tbody>
                        <tr>
                            <!-- Alertas pelo Telegram: [1: Ativado, !1: Desativado] -->
                            <td align="right" class="DivConfigNome"><?=$GLOBALS['lang'][129]?>:</td>
                            <td align="left">
                                <select name="ativar5" id="ativar5" class="selectGInput" style="width: 196px; font-size: 12px; height: 33px;" onChange="javascript:bloqueio5();">
                                    <option value="1" <?php if ($fetClientes['ativaTELEGRAM'] == 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][130]?></option>
                                    <option value="2" <?php if ($fetClientes['ativaTELEGRAM'] != 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][131]?></option>
                                </select>
                                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][132]?></span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div id="AbreTelegram" style="display:<?php if ($fetClientes['ativaTELEGRAM'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                        <div class="barrarolagem2" style="max-height: 110px;">

                            <?php
                            $resultTelegram = mysqli_query($db, "SELECT * FROM telegrampadrao ORDER BY id ASC");
                            $totalTelegram = mysqli_num_rows($resultTelegram);

                            echo '<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top: 10px"><tbody>';
                            echo '<tr>';
                            echo '<td width="75" class="UsersTitulo">Chat_id: <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-telegram">' . $GLOBALS['lang'][133] . '</span></div></td>';
                            echo '<td class="UsersTitulo">Token: <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-telegram">' . $GLOBALS['lang'][134] . '</span></div></td>';
                            echo '<td width="40" class="UsersTitulo">' . $GLOBALS['lang'][135] . ': <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-telegram">' . $GLOBALS['lang'][138] . '</span></div></td>';
                            echo '<td width="40" class="UsersTitulo">' . $GLOBALS['lang'][136] . ': <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-telegram">' . $GLOBALS['lang'][139] . '</span></div></td>';
                            echo '<td width="40" class="UsersTitulo">' . $GLOBALS['lang'][137] . ':</td>';
                            echo '<td width="30" class="UsersTitulo" style="border-radius: 0px 0px 10px 0px; border-left: none;"></td>';
                            echo '<td></td>';
                            echo '</tr>';

                            $cor = '#FFF';
                            $ArredondarBorda = 'sim';
                            while ($Telegram = mysqli_fetch_array($resultTelegram)) {
                                if ($cor == '#FFF') {
                                    echo '<tr>';
                                    echo '<td class="UsersTd"><input name="tid[]" id="tid[]" type="hidden" value="' . $Telegram['id'] . '" autocomplete="off"><input name="chat_id[]" id="chat_id[]" class="imputConfNome" style="background: #FFF;" type="text" value="' . $Telegram['chat_id'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd"><input name="token[]" id="token[]" class="imputConfUsr" style="background: #FFF;" type="text" value="' . $Telegram['token'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd"><input maxlength="5" name="inicio[]" id="inicio[]" class="imputConfUsr" style="background: #FFF; width: 35px;" type="text" value="' . $Telegram['inicio'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd"><input maxlength="5" name="fim[]" id="fim[]" class="imputConfUsr" style="background: #FFF; width: 35px;" type="text" value="' . $Telegram['fim'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd">';
                                    echo '<select name="prioridade[]" id="prioridade[]" class="selectConf">';
                                    if ($Telegram['prioridade'] == 1) {
                                        echo '<option value="1" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][140] . '</option>';
                                        echo '<option value="2" style="width: 60px;">' . $GLOBALS['lang'][70] . '</option>';
                                        echo '<option value="3" style="width: 60px;">' . $GLOBALS['lang'][131] . '</option>';
                                    } else if ($Telegram['prioridade'] == 2) {
                                        echo '<option value="1" style="width: 60px;">' . $GLOBALS['lang'][140] . '</option>';
                                        echo '<option value="2" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][70] . '</option>';
                                        echo '<option value="3" style="width: 60px;">' . $GLOBALS['lang'][131] . '</option>';
                                    } else if ($Telegram['prioridade'] == 3) {
                                        echo '<option value="1" style="width: 60px;">' . $GLOBALS['lang'][140] . '</option>';
                                        echo '<option value="2" style="width: 60px;">' . $GLOBALS['lang'][70] . '</option>';
                                        echo '<option value="3" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][131] . '</option>';
                                    }
                                    echo '</select></td>';

                                    if ($ArredondarBorda == 'sim') {
                                        echo '<td class="UsersTdTipo" style="border-radius: 0px 10px 0px 0px;"><a href="javascript:test_Telegram(' . $Telegram['id'] . ');" class="DispDetalhar" style="font-size: 12px">' . $GLOBALS['lang'][141] . '</a></td>';
                                        $ArredondarBorda = 'nao';
                                    } else {
                                        echo '<td class="UsersTdTipo"><a href="javascript:test_Telegram(' . $Telegram['id'] . ');" class="DispDetalhar" style="font-size: 12px">' . $GLOBALS['lang'][141] . '</a></td>';
                                    }

                                    echo '<td class="UsersTdExcluir"><a href="javascript:texcluir(' . $Telegram['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][142] . '"/></a></td>';
                                    echo '</tr>';
                                    $cor = "#eeeeee";
                                } else {
                                    echo '<tr style="background: #eeeeee;">';
                                    echo '<td class="UsersTd"><input name="tid[]" id="tid[]" type="hidden" value="' . $Telegram['id'] . '" autocomplete="off"><input name="chat_id[]" id="chat_id[]" class="imputConfNome" style="background: #eeeeee;" type="text" value="' . $Telegram['chat_id'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd"><input name="token[]" id="token[]" class="imputConfUsr" style="background: #eeeeee;" type="text" value="' . $Telegram['token'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd"><input maxlength="5" name="inicio[]" id="inicio[]" class="imputConfUsr" style="background: #eeeeee; width: 35px;" type="text" value="' . $Telegram['inicio'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd"><input maxlength="5" name="fim[]" id="fim[]" class="imputConfUsr" style="background: #eeeeee; width: 35px;" type="text" value="' . $Telegram['fim'] . '" autocomplete="off"></td>';
                                    echo '<td class="UsersTd">';
                                    echo '<select name="prioridade[]" id="prioridade[]" class="selectConf">';
                                    if ($Telegram['prioridade'] == 1) {
                                        echo '<option value="1" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][140] . '</option>';
                                        echo '<option value="2" style="width: 60px;">' . $GLOBALS['lang'][70] . '</option>';
                                        echo '<option value="3" style="width: 60px;">' . $GLOBALS['lang'][131] . '</option>';
                                    } else if ($Telegram['prioridade'] == 2) {
                                        echo '<option value="1" style="width: 60px;">' . $GLOBALS['lang'][140] . '</option>';
                                        echo '<option value="2" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][70] . '</option>';
                                        echo '<option value="3" style="width: 60px;">' . $GLOBALS['lang'][131] . '</option>';
                                    } else if ($Telegram['prioridade'] == 3) {
                                        echo '<option value="1" style="width: 60px;">' . $GLOBALS['lang'][140] . '</option>';
                                        echo '<option value="2" style="width: 60px;">' . $GLOBALS['lang'][70] . '</option>';
                                        echo '<option value="3" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][131] . '</option>';
                                    }
                                    echo '</select></td>';
                                    echo '<td class="UsersTdTipo"><a href="javascript:test_Telegram(' . $Telegram['id'] . ');" class="DispDetalhar" style="font-size: 12px">' . $GLOBALS['lang'][141] . '</a></td>';
                                    echo '<td class="UsersTdExcluir"><a href="javascript:texcluir(' . $Telegram['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][142] . '"/></a></td>';
                                    echo '</tr>';
                                    $cor = "#FFF";
                                };
                            };

                            if ($cor == '#FFF') {
                                echo '<tr id="InserindoTelegram" style="display:none;">';
                                echo '<td class="UsersTd"><input name="chat_id_new" id="chat_id_new" class="imputConfUsr" style="background: #FFF;" type="text" value="" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input name="token_new" id="token_new" class="imputConfUsr" style="background: #FFF;" type="text" value="" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input maxlength="5" name="inicio_new" id="inicio_new" class="imputConfUsr" style="background: #FFF; width: 35px;" type="text" value="00:00" placeholder="00:00" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input maxlength="5" name="fim_new" id="fim_new" class="imputConfUsr" style="background: #FFF; width: 35px;" type="text" value="23:59" placeholder="23:59" autocomplete="off"></td>';
                                echo '<td class="UsersTd">
                                    <select name="prioridade_new" id="prioridade_new" class="selectConf">
                                        <option value="1" style="width: 60px;">' . $GLOBALS['lang'][140] . '</option>
                                        <option value="2" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][70] . '</option>
                                        <option value="3" style="width: 60px;">' . $GLOBALS['lang'][131] . '</option>
                                    </select></td>';
                                echo '<td align="center" class="UsersTdTipo"><a href="javascript:achaChat_id();"><img width="12px" height="10px" src="img/achatelegram.png"/></a></td>';
                                echo '<td width="34" class="UsersTdExcluir"><img src="img/deleteOff.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][142] . '"/></td>';
                                echo '</tr>';
                            } else {
                                echo '<tr id="InserindoTelegram" style="background: #eeeeee; display:none;">';
                                echo '<td class="UsersTd"><input name="chat_id_new" id="chat_id_new" class="imputConfUsr" style="background: #eeeeee;" type="text" value="" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input name="token_new" id="token_new" class="imputConfUsr" style="background: #eeeeee;" type="text" value="" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input maxlength="5" name="inicio_new" id="inicio_new" class="imputConfUsr" style="background: #eeeeee; width: 35px;" type="text" value="00:00" placeholder="00:00" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input maxlength="5" name="fim_new" id="fim_new" class="imputConfUsr" style="background: #eeeeee; width: 35px;" type="text" value="23:59" placeholder="23:59" autocomplete="off"></td>';
                                echo '<td class="UsersTd">
                                    <select name="prioridade_new" id="prioridade_new" class="selectConf">
                                        <option value="1" style="width: 60px;">' . $GLOBALS['lang'][140] . '</option>
                                        <option value="2" style="width: 60px;" selected="selected">' . $GLOBALS['lang'][70] . '</option>
                                        <option value="3" style="width: 60px;">' . $GLOBALS['lang'][131] . '</option>
                                    </select></td>';
                                echo '<td align="center" class="UsersTdTipo"><a href="javascript:achaChat_id();"><img width="12px" height="10px" src="img/achatelegram.png"/></a></td>';
                                echo '<td width="34" class="UsersTdExcluir"><img src="img/deleteOff.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][142] . '"/></td>';
                                echo '</tr>';
                            };

                            echo '</tbody></table>';
                            ?>
                        </div>
                        <a href="#" id="abrirTelegram" style="text-decoration: none;">
                        <!-- Inserir Telegram -->
                            <div class="UserInsert"><?=$GLOBALS['lang'][143]?></div>
                        </a>
                    </div>
                </div>

                <div class="QuadroConfigs">
                    <table border="0" cellspacing="0" cellpadding="0" style="padding: 10px;">
                        <tbody>
                        <tr>
                            <!-- Alertas por e-mail [1: Sim, !1: Não]-->
                            <td align="right" class="DivConfigNome"><?=$GLOBALS['lang'][144]?>:</td>
                            <td align="left">
                                <select name="ativar4" id="ativar4" class="selectConf" onChange="javascript:bloqueio4();">
                                    <option value="1" <?php if ($fetClientes['ativaSMTP'] == 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][145]?></option>
                                    <option value="2" <?php if ($fetClientes['ativaSMTP'] != 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][146]?></option>
                                </select>
                                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][147]?></span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div id="AbreSMTPIntegra" style="display:<?php if ($fetClientes['ativaSMTP'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                        <table border="0" align="center" cellpadding="0" cellspacing="0" style="padding: 10px;">
                            <tbody>
                            <tr>
                                <td width="98" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][148]?>:</td>
                                <td width="309" align="left"><input type="text" name="servidorSMTP" id="servidorSMTP" class="DivCongiInput" style="width: 230px;" value="<?php echo $fetClientes['servidorSMTP']; ?>" autocomplete="off">
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][149]?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="98" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][112]?>:</td>
                                <td width="309" align="left"><input type="text" name="userSMTP" id="userSMTP" class="DivCongiInput" style="width: 230px;" value="<?php echo $fetClientes['userSMTP']; ?>" autocomplete="off">
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][150]?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="98" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][151]?>:</td>
                                <td align="left">
                                    <input type="password" name="senhaEmailS" id="senhaEmailS" class="DivCongiInput" style="width: 130px; border-radius: 5px 0px 0px 5px;" value="<?php echo $fetClientes['senhaSMTP']; ?>" autocomplete="off"><span id="olhoS" class="versenha">Mostrar</span>
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][152]?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][153]?>:</td>
                                <td align="left"><input type="text" name="portaSMTP" id="portaSMTP" class="DivCongiInput" style="width: 50px;" placeholder="25 / 587" value="<?php echo $fetClientes['portaSMTP']; ?>" autocomplete="off">
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][154]?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][155]?>:</td>
                                <td align="left"><input type="checkbox" id="ssl" name="ssl" <?php if ($fetClientes['SMTPtls'] == 1) { echo "checked"; } ?>>
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][156]?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td height="30"></td>
                                <td>--------------------</td>
                            </tr>

                            <tr>
                                <td width="98" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][157]?>:</td>
                                <td width="309" align="left"><input type="text" name="emailSMTP" id="emailSMTP" class="DivCongiInput" style="width: 230px;" value="<?php echo $fetClientes['emailSMTP']; ?>" autocomplete="off">
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][158]?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="98" align="right" class="DivConfigNome"><?=$GLOBALS['lang'][159]?>:</td>
                                <td width="309" align="left">
                                    <select name="prioridadeEmail" id="prioridadeEmail" class="selectConf">
                                        <option value="1" <?php if ($fetClientes['prioridadeSMTP'] == 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][160]?></option>
                                        <option value="2" <?php if ($fetClientes['prioridadeSMTP'] != 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][161]?></option>
                                    </select>
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                        <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][162]?></span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                        <center><a href="javascript:test_smtp();" class="DispDetalhar" style="font-size: 14px"><?=$GLOBALS['lang'][163]?></a></center>
                        <br>
                    </div>
                </div>

                <div class="QuadroConfigs">
                    <table border="0" cellspacing="0" cellpadding="0" style="padding: 10px;">
                        <tbody>
                        <tr>
                            <td align="left">
                                <!-- [1: Servidor Whatsapp ativo, !1: desativado] -->
                                <select name="ativar6" id="ativar6" class="selectGInput" style="width: 280px; font-size: 12px; height: 33px;" onChange="javascript:bloqueio6();">
                                    <option value="1" <?php if ($fetClientes['ativaWHATS'] == 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][164]?></option>
                                    <option value="2" <?php if ($fetClientes['ativaWHATS'] != 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][165]?></option>
                                </select>
                                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][166]?></span></div>
                            </td>
                        </tr>
                        <tr id="abre_api_whats1" style="display:<?php if($fetClientes['ativaWHATS'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                            <td align="left" style="padding-top: 1px;">
                                <!-- [1: Offline e Alertas, 2: Alertar apenas quando Offline] -->
                                <select name="prioridadewhats" id="prioridadewhats" class="selectGInput" style="width: 280px; font-size: 12px; height: 33px;">
                                    <option value="1" <?php if ($fetClientes['prioridadewhats'] == 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][167]?></option>
                                    <option value="2" <?php if ($fetClientes['prioridadewhats'] == 2) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][168]?></option>
                                </select>
                                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/>
                                    <span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][169]?></span>
                                </div>
                            </td>
                        </tr>
                        <tr id="abre_api_whats" style="display:<?php if($fetClientes['ativaWHATS'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                            <td align="left" style="padding-top: 1px;">
                                <?php if($registroPlano != 0 && $registroPlano != 6) { ?>
                                <!-- [0: API Whatsapp desativada, 1: API Whatsapp ativa] -->
                                <select name="api_whats" id="api_whats" class="selectGInput" style="width: 280px; font-size: 12px; height: 33px;" onChange="javascript:bloqueio61();">
                                    <option value="0" <?php if($fetClientes['api_whats'] == 0) { echo "selected"; } ?> ><?=$GLOBALS['lang'][170]?></option>
                                    <option value="1" <?php if($fetClientes['api_whats'] == 1) { echo "selected"; } ?> ><?=$GLOBALS['lang'][171]?></option>
                                </select>
                                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][172]?></span></div>
                                <?php }else { ?>
                                <select name="api_whats" id="api_whats" class="selectGInput" style="width: 280px; font-size: 12px; height: 33px;">
                                    <option value="0" selected ><?=$GLOBALS['lang'][170]?></option>
                                    <option value="1" disabled ><?=$GLOBALS['lang'][171]?></option>
                                </select>
                                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-top"><?=$GLOBALS['lang'][173]?></span></div>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr id="abre_api_whats_link" style="display:<?php if($fetClientes['api_whats'] == 1 && $fetClientes['ativaWHATS'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                            <td align="left" style="padding-top: 9px;">
                                <a href="config_api_whats.php" class="btn botaoTopo" style="padding-top: 8px; padding-bottom: 8px; margin-left: 1px;"><?=$GLOBALS['lang'][174]?></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div id="AbreWhats" style="display:<?php if ($fetClientes['ativaWHATS'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                        <div id="auth" style="display: block">
                            <h5 class="mb-3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$GLOBALS['lang'][175]?></h5>
                            <ol>
                                <li><?=$GLOBALS['lang'][176]?></li>
                                <li>
                                    <div style="display:flex; align-items:center;">
                                        <?=$GLOBALS['lang'][177]?>
                                        <svg height="24px" viewBox="0 0 24 24" width="24px" style="margin: 0 5px;">
                                            <rect fill="#f2f2f2" height="24" rx="3" width="24"></rect>
                                            <path d="m12 15.5c.825 0 1.5.675 1.5 1.5s-.675 1.5-1.5 1.5-1.5-.675-1.5-1.5.675-1.5 1.5-1.5zm0-2c-.825 0-1.5-.675-1.5-1.5s.675-1.5 1.5-1.5 1.5.675 1.5 1.5-.675 1.5-1.5 1.5zm0-5c-.825 0-1.5-.675-1.5-1.5s.675-1.5 1.5-1.5 1.5.675 1.5 1.5-.675 1.5-1.5 1.5z" fill="#818b90"></path>
                                        </svg>
                                        <?=$GLOBALS['lang'][178]?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" style="margin: 0 5px;">
                                            <rect fill="#F2F2F2" width="24" height="24" rx="3"></rect>
                                            <path d="M12 18.69c-1.08 0-2.1-.25-2.99-.71L11.43 14c.24.06.4.08.56.08.92 0 1.67-.59 1.99-1.59h4.62c-.26 3.49-3.05 6.2-6.6 6.2zm-1.04-6.67c0-.57.48-1.02 1.03-1.02.57 0 1.05.45 1.05 1.02 0 .57-.47 1.03-1.05 1.03-.54.01-1.03-.46-1.03-1.03zM5.4 12c0-2.29 1.08-4.28 2.78-5.49l2.39 4.08c-.42.42-.64.91-.64 1.44 0 .52.21 1 .65 1.44l-2.44 4C6.47 16.26 5.4 14.27 5.4 12zm8.57-.49c-.33-.97-1.08-1.54-1.99-1.54-.16 0-.32.02-.57.08L9.04 5.99c.89-.44 1.89-.69 2.96-.69 3.56 0 6.36 2.72 6.59 6.21h-4.62zM12 19.8c.22 0 .42-.02.65-.04l.44.84c.08.18.25.27.47.24.21-.03.33-.17.36-.38l.14-.93c.41-.11.82-.27 1.21-.44l.69.61c.15.15.33.17.54.07.17-.1.24-.27.2-.48l-.2-.92c.35-.24.69-.52.99-.82l.86.36c.2.08.37.05.53-.14.14-.15.15-.34.03-.52l-.5-.8c.25-.35.45-.73.63-1.12l.95.05c.21.01.37-.09.44-.29.07-.2.01-.38-.16-.51l-.73-.58c.1-.4.19-.83.22-1.27l.89-.28c.2-.07.31-.22.31-.43s-.11-.35-.31-.42l-.89-.28c-.03-.44-.12-.86-.22-1.27l.73-.59c.16-.12.22-.29.16-.5-.07-.2-.23-.31-.44-.29l-.95.04c-.18-.4-.39-.77-.63-1.12l.5-.8c.12-.17.1-.36-.03-.51-.16-.18-.33-.22-.53-.14l-.86.35c-.31-.3-.65-.58-.99-.82l.2-.91c.03-.22-.03-.4-.2-.49-.18-.1-.34-.09-.48.01l-.74.66c-.39-.18-.8-.32-1.21-.43l-.14-.93a.426.426 0 00-.36-.39c-.22-.03-.39.05-.47.22l-.44.84-.43-.02h-.22c-.22 0-.42.01-.65.03l-.44-.84c-.08-.17-.25-.25-.48-.22-.2.03-.33.17-.36.39l-.13.88c-.42.12-.83.26-1.22.44l-.69-.61c-.15-.15-.33-.17-.53-.06-.18.09-.24.26-.2.49l.2.91c-.36.24-.7.52-1 .82l-.86-.35c-.19-.09-.37-.05-.52.13-.14.15-.16.34-.04.51l.5.8c-.25.35-.45.72-.64 1.12l-.94-.04c-.21-.01-.37.1-.44.3-.07.2-.02.38.16.5l.73.59c-.1.41-.19.83-.22 1.27l-.89.29c-.21.07-.31.21-.31.42 0 .22.1.36.31.43l.89.28c.03.44.1.87.22 1.27l-.73.58c-.17.12-.22.31-.16.51.07.2.23.31.44.29l.94-.05c.18.39.39.77.63 1.12l-.5.8c-.12.18-.1.37.04.52.16.18.33.22.52.14l.86-.36c.3.31.64.58.99.82l-.2.92c-.04.22.03.39.2.49.2.1.38.08.54-.07l.69-.61c.39.17.8.33 1.21.44l.13.93c.03.21.16.35.37.39.22.03.39-.06.47-.24l.44-.84c.23.02.44.04.66.04z" fill="#818b90"></path>
                                        </svg>
                                        <?=$GLOBALS['lang'][179]?>
                                    </div>
                                </li>
                                <li><?=$GLOBALS['lang'][180]?></li>
                            </ol>
                        </div>
                        <br>
                        <center>
                            <div id="result" style="display: none">
                                <style>
                                    #contacts {
                                        height: 200px;
                                        overflow-y: auto;
                                    }

                                    #contacts li {
                                        cursor: pointer;
                                        font-size: 15px;
                                        height: 20px;
                                        border: solid 1px #eaeaea;
                                        margin-top: 2px;
                                        padding: 10px;
                                        cursor: pointer;
                                    }

                                    #contacts li:hover {
                                        background-color: #F0FFF0;
                                    }

                                    .contatoSelecionado {
                                        background-color: #BFFFBF;
                                    }
                                </style>
                                <img src="img/telefone.png">&nbsp;&nbsp;<font size="4px"><?=$GLOBALS['lang'][181]?><strong><div id="bateria"></div></strong></font>
                                <br>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td><button type="button" id="botaoDesconectar" class="SalvarConfigs" onclick="javascript:desconectar();" style="display: none;"><?=$GLOBALS['lang'][182]?></button></td>
                                        <td><button type="button" id="botaoTestar" class="SalvarConfigs" onclick="javascript:testar();" style="display: block;"><?=$GLOBALS['lang'][183]?></button></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <br>
                                <br><br>
                        </center>
                        <div id="loadingDiv"></div>
                        <div id="contactsDiv" style="display: none;">
                        <!-- Selecione os contatos que deseja enviar alertas -->
                        <center><?=$GLOBALS['lang'][184]?></center>
                        <br>
                        <input style="width: 100%; margin: 8px 0; display: inline-block; border: 1px solid #ccc; box-shadow: inset 0 1px 3px #ddd; border-radius: 4px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; padding-left: 20px; padding-right: 20px; padding-top: 12px; padding-bottom: 12px;" id="searchbar" onkeyup="search_contato()" type="text" name="search" placeholder="<?=$GLOBALS['lang'][185]?>...">   
                            <br>
                            <ul id="contacts" class="list-group"></ul>
                        </div>
                        <br>
                        <div class="qr-container">
                            <center><img id="qrcode"></center>
                        </div>
                        <br>
                        <a href="javascript:rebootnode();" style="text-decoration: none;"><div class="UserInsert"><?=$GLOBALS['lang'][186]?></div></a>
                    </div>
                </div>
            </div>

            <div id="div_aba3" class="MostraSensor" style="display:none; padding-bottom: 20px;">
                <br>
                <!-- Opções avançadas do sistema -->
                <div id="container"><?=$GLOBALS['lang'][187]?></div>

                <div class="QuadroConfigs">
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][188]?>:</div>
                    <!-- [Simples | Avançado] -->
                    <div id="containerSensor" class="borderTopConf">
                        <a href="#" tabindex="0" onclick="mostrar_abas_ipv4('mostra_aba_ipv41');" id="mostra_aba_ipv41"><?=$GLOBALS['lang'][189]?></a>
                        <a href="#" tabindex="0" onclick="mostrar_abas_ipv4('mostra_aba_ipv42');" id="mostra_aba_ipv42"><?=$GLOBALS['lang'][190]?></a>
                    </div>
                    <div id="mostrar_abas_ipv41">
                        <br>
                        <div class="DivCongigTable">
                            <?php
                            echo '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"><tbody>';
                            echo '<tr>';
                            echo '<td width="120" class="UsersTitulo">IP:</td>';
                            echo '<td class="UsersTitulo">'. $GLOBALS['lang'][191] . ':</td>';
                            echo '<td class="UsersTitulo">'. $GLOBALS['lang'][192] . ':</td>';
                            echo '<td width="70" class="UsersTitulo" style="border-radius: 0px 0px 10px 0px;">' . $GLOBALS['lang'][193] . ':</td>';
                            echo '<td width="34"></td>';
                            echo '</tr>';

                            exec("cat /etc/network/interfaces", $rede);
                            exec("cat /etc/resolv.conf | awk '{ print $2 }' | awk 'NF>0'", $os_dns);
                            exec("ip link | egrep '^[0-9]' | cut -d ':' -f2 | sed 's/ //g' | grep -v lo | grep -v ppp", $as_interfaces);

                            $dns1 = $os_dns[0];
                            $dns2 = $os_dns[1];
                            if (!$dns1) { $dns1 = "8.8.8.8"; }
                            if (!$dns2) { $dns2 = "1.1.1.1"; }

                            $cor = '#FFF';
                            $ArredondarBorda = 'sim';

                            function imprime_ip($ArredondarBorda, $cor, $interface, $ip, $mask, $gw, $as_interfaces)
                            {
                                echo '<tr style="background: ' . $cor . ';">';
                                echo '<td class="UsersTd"><input name="ip[]" id="ip[]" class="imputConfNome" style="background: ' . $cor . ';" type="text" value="' . $ip . '" autocomplete="off" onkeyup="verificaMascara(event,this);"></td>';
                                echo '<td class="UsersTd"><input name="gw[]" id="gw[]" class="imputConfUsr" style="background: ' . $cor . ';" type="text" value="' . $gw . '" autocomplete="off"></td>';
                                echo '<td class="UsersTd"><input name="mask[]" id="mask" class="imputConfSenha" style="background: ' . $cor . ';" type="text" value="' . $mask . '" autocomplete="off"></td>';
                                if ($ArredondarBorda == 'sim') {
                                    echo '<td class="UsersTdTipo" style="border-radius: 0px 10px 0px 0px;">';
                                } else {
                                    echo '<td class="UsersTdTipo">';
                                }
                                echo '<select name="interface[]" id="interface[]" class="selectConf">';
                                for ($b = 0; $b < count($as_interfaces); $b++) {
                                    if ($as_interfaces[$b] == $interface) {
                                        echo '<option value="' . $as_interfaces[$b] . '" style="width: 60px;" selected="selected">' . $as_interfaces[$b] . '</option>';
                                    } else {
                                        echo '<option value="' . $as_interfaces[$b] . '" style="width: 60px;">' . $as_interfaces[$b] . '</option>';
                                    }
                                }
                                echo '</select></td>';
                                echo "<td class='UsersTdExcluir'><a href='excluirip.php?&ip=" . $ip . "'><img src='img/delete.png' class='ImgOlho' width='auto' height='15' alt='" . $GLOBALS['lang'][194] . "'/></a></td>";
                                echo '</tr>';
                                $cor = "#eeeeee";
                            }

                            $qtd_ips = 0;
                            for ($i = 0; $i < count($rede); $i++) {
                                $aux = explode(' ', preg_replace('/\s+/', ' ', $rede[$i]));
                                if($aux['0'] == "iface" && $aux['3'] != "manual") {
                                    /*echo '<pre>';
                                    print_r($aux);
                                    echo '</pre>';*/
                                    if (isset($ip)) {
                                        imprime_ip($ArredondarBorda, $cor, $interface, $ip, $netmask, $gateway, $as_interfaces);
                                        if ($cor == '#FFF') {
                                            $cor = '#eeeeee';
                                        } else {
                                            $cor = '#FFF';
                                        }
                                        if ($ArredondarBorda == 'sim') {
                                            $ArredondarBorda = 'nao';
                                        }
                                        $ip = "";
                                        $netmask = "";
                                        $gateway = "";
                                    }
                                    $iface_parts = explode(':', $aux['1']);
                                    $interface = $iface_parts['0'];
                                    $tipo = $aux['2'];
                                }
                                if(isset($aux['1'])) {
                                    if (preg_match('/address/', $aux['1'])) {
                                        if ($tipo == "inet") {
                                            //Se for ipv4 tentamos localizar a netmask anunciada junto do ip
                                            $aux1 = explode('/', $aux['2']);
                                            $ip = $aux1['0'];
                                            if($ip) { $qtd_ips = $qtd_ips + 1; }
                                            if (isset($aux1['1'])) {
                                                if ($aux1['1'] == 32) {
                                                    $netmask = "255.255.255.255";
                                                }
                                                if ($aux1['1'] == 31) {
                                                    $netmask = "255.255.255.254";
                                                }
                                                if ($aux1['1'] == 30) {
                                                    $netmask = "255.255.255.252";
                                                }
                                                if ($aux1['1'] == 29) {
                                                    $netmask = "255.255.255.248";
                                                }
                                                if ($aux1['1'] == 28) {
                                                    $netmask = "255.255.255.240";
                                                }
                                                if ($aux1['1'] == 27) {
                                                    $netmask = "255.255.255.224";
                                                }
                                                if ($aux1['1'] == 26) {
                                                    $netmask = "255.255.255.192";
                                                }
                                                if ($aux1['1'] == 25) {
                                                    $netmask = "255.255.255.128";
                                                }
                                                if ($aux1['1'] == 24) {
                                                    $netmask = "255.255.255.0";
                                                }
                                                if ($aux1['1'] == 23) {
                                                    $netmask = "255.255.254.0";
                                                }
                                                if ($aux1['1'] == 22) {
                                                    $netmask = "255.255.252.0";
                                                }
                                                if ($aux1['1'] == 21) {
                                                    $netmask = "255.255.248.0";
                                                }
                                                if ($aux1['1'] == 20) {
                                                    $netmask = "255.255.240.0";
                                                }
                                                if ($aux1['1'] == 19) {
                                                    $netmask = "255.255.224.0";
                                                }
                                                if ($aux1['1'] == 18) {
                                                    $netmask = "255.255.192.0";
                                                }
                                                if ($aux1['1'] == 17) {
                                                    $netmask = "255.255.128.0";
                                                }
                                                if ($aux1['1'] == 16) {
                                                    $netmask = "255.255.0.0";
                                                }
                                                if ($aux1['1'] == 15) {
                                                    $netmask = "255.254.0.0";
                                                }
                                                if ($aux1['1'] == 14) {
                                                    $netmask = "255.252.0.0";
                                                }
                                                if ($aux1['1'] == 13) {
                                                    $netmask = "255.248.0.0";
                                                }
                                                if ($aux1['1'] == 12) {
                                                    $netmask = "255.240.0.0";
                                                }
                                                if ($aux1['1'] == 11) {
                                                    $netmask = "255.224.0.0";
                                                }
                                                if ($aux1['1'] == 10) {
                                                    $netmask = "255.192.0.0";
                                                }
                                                if ($aux1['1'] == 9) {
                                                    $netmask = "255.128.0.0";
                                                }
                                                if ($aux1['1'] == 8) {
                                                    $netmask = "255.0.0.0";
                                                }
                                            }
                                        }else {
                                            $ip = $aux['2'];
                                            if($ip) { $qtd_ips = $qtd_ips + 1; }
                                        }
                                    }
                                    if(preg_match('/netmask/', $aux['1'])) {
                                        $netmask = $aux['2'];
                                    }
                                    if(preg_match('/gateway/', $aux['1'])) {
                                        $gateway = $aux['2'];
                                    }
                                }

                                if (count($rede) - 1 == $i && $ip) {
                                    imprime_ip($ArredondarBorda, $cor, $interface, $ip, $netmask, $gateway, $as_interfaces);
                                    if ($cor == '#FFF') {
                                        $cor = '#eeeeee';
                                    } else {
                                        $cor = '#FFF';
                                    }
                                }
                            }

                            // Entra nessa condição quando está em DHCP e não tem nenhum IP fixo na placa
                            if(!$qtd_ips) { ?>
                                <script>
                                $(function () {
                                    mostrar_abas_ipv4('mostra_aba_ipv42');
                                    document.getElementById('mostra_aba_ipv42').focus();
                                    document.getElementById('mostrar_abas_ipv41').style.display = "none";
                                    document.getElementById('mostrar_abas_ipv42').style.display = "block";
                                    document.getElementById('mostra_aba_ipv42').style.backgroundColor = "#00c95c";
                                    document.getElementById('mostra_aba_ipv41').style.backgroundColor = "#A8A8A8";
                                });
                                </script>
                            <?php 
                            }

                            echo '<tr id="Inserindoip" style="background: ' . $cor . '; display:none;">';
                            echo '<td class="UsersTd"><input name="ip[]" id="ip[]" class="imputConfNome" style="background: ' . $cor . ';" type="text" value="" autocomplete="off" onkeyup="verificaMascara(event,this);"></td>';
                            echo '<td class="UsersTd"><input name="gw[]" id="gw[]" class="imputConfUsr" style="background: ' . $cor . ';" type="text" value="" autocomplete="off"></td>';
                            echo '<td class="UsersTd"><input name="mask[]" id="mask" class="imputConfSenha" style="background: ' . $cor . ';" type="text" value="" autocomplete="off"></td>';

                            echo '<td class="UsersTdTipo">';
                            echo '<select name="interface[]" id="interface[]" class="selectConf">';
                            for ($b = 0; $b < count($as_interfaces); $b++) {
                                if ($as_interfaces[$b] == $interface) {
                                    echo '<option value="' . $as_interfaces[$b] . '" style="width: 60px;" selected="selected">' . $as_interfaces[$b] . '</option>';
                                } else {
                                    echo '<option value="' . $as_interfaces[$b] . '" style="width: 60px;">' . $as_interfaces[$b] . '</option>';
                                }
                            }
                            echo '</select></td>';

                            echo '<td width="34" class="UsersTdExcluir"><img src="img/deleteOff.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][194] . '"/></td>';
                            echo '</tr>';

                            echo '</tbody></table>';
                            ?>

                            <a href="#" id="abririp" style="text-decoration: none;"><div class="UserInsert"><?=$GLOBALS['lang'][195]?></div></a>
                        </div>
                    </div>
                    <div class="DivCongigTable" id="mostrar_abas_ipv42" style="display: none; text-align: left;">
                        <?PHP
                        echo "<br><form><textarea id='code' name='code'>";
                        echo file_get_contents("/etc/network/interfaces");
                        echo "</textarea></form>";
                        ?>
                    </div>

                    <div class="DivCongigTable">
                        <br><br><br>
                        <table border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom: 45px;">
                            <tbody>
                            <tr>
                                <td width="150" class="UsersTitulo"><?=$GLOBALS['lang'][196]?>:</td>
                                <td width="150" class="UsersTitulo" style="border-radius: 0px 0px 10px 0px;"><?=$GLOBALS['lang'][197]?>:</td>
                            </tr>
                            <tr>
                                <td class="UsersTitulo"><input name="dns1" id="dns1" class="imputConfNome" style="background: #FFF;" type="text" value="<?php echo $dns1; ?>" autocomplete="off"></td>
                                <td class="UsersTitulo"><input name="dns2" id="dns2" class="imputConfNome" style="background: #FFF;" type="text" value="<?php echo $dns2; ?>" autocomplete="off"></td>
                            </tr>
                            </tbody>
                        </table>

                        <a href="javascript:rebootnetwork();" style="text-decoration: none;"><div class="UserInsert"><?=$GLOBALS['lang'][198]?></div></a>

                        <div class="alertaconfig"><img src="img/alertaconfig.png" width="20" height="auto" style="padding-right: 10px" alt="Alerta informativo"/><?=$GLOBALS['lang'][199]?></div>
                    </div>
                </div>

                <?php
                // Local dos logs: /var/log/messages | grep ppp
                $mostrar_config_vpn = 0;
                if($fetClientes['ativaVPN'] == 1) {
                    exec("ifconfig", $int_ppp);
                    $interface_ppp = 0;
                    for ($i = 0; $i < count($int_ppp); $i++) {
                        $aux = explode(' ', $int_ppp[$i]);
                        $aux_filter = array();
                        foreach($aux as $k=>$val){
                            $val = trim($val);
                            if($val==''){ unset($aux[$k]); continue; }
                            $aux_filter[] = $val;
                        }
                        if($aux['0'] == "ppp0:" && $interface_ppp == 0) {
                            $interface_ppp = 1;
                        }else if($interface_ppp == 1) {
                            if ($aux_filter['0'] == "inet") { $vpn_address = $aux_filter['1']; }
                            if ($aux_filter['0'] == "inet6") { $vpn_address6 = $aux_filter['1']; }
                            //if ($aux_filter['0'] == "RX") { $vpn_rx_packets = (int)$aux_filter['2']; $vpn_rx_bytes = (int)$aux_filter['4']; }
                            //if ($aux_filter['0'] == "TX") { $vpn_tx_packets = (int)$aux_filter['2']; $vpn_tx_bytes = (int)$aux_filter['4']; }
                            /*echo "<pre>";
                            print_r($aux_filter);
                            echo "</pre>";*/
                        }
                    }
                    if($interface_ppp == 1) { ?>
                        <div class="QuadroConfigs" style="padding: 5px;">
                            <div style="background: url('../img/bg_vpn.png') no-repeat top; background-size: 100% auto;">
                                <table width="95%" border="0" align="center" cellspacing="0" cellpadding="0" style="font-size: 14px;">
                                    <tbody>
                                        <tr>
                                            <td height="95" align="center" class="DivConfigNome" style="font-size: 16px;">
                                                <?php
                                                if($vpn_address) { echo "IPv4: " . $vpn_address . "<br>"; }
                                                if($vpn_address6) { echo "IPv6: " . $vpn_address6 . "<br>"; }
                                                echo "<br>Data da conexão: " . date('d/m/Y H:i:s', strtotime($fetClientes['datavpn']));
                                                //if(isset($vpn_rx_packets)) { echo "RX Packets: " . $vpn_rx_packets . "<br>"; }
                                                //if(isset($vpn_tx_packets)) { echo "TX Packets: " . $vpn_tx_packets . "<br>"; }
                                                //if(isset($vpn_rx_bytes)) { echo "RX Bytes: " . $vpn_rx_bytes . "<br>"; }
                                                //if(isset($vpn_tx_bytes)) { echo "TX Bytes: " . $vpn_tx_bytes . "<br>"; }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" class="DivConfigNome"><a href="desconectar_vpn.php" style="text-decoration: none;"><img src="img/power_vpn.png" style="border: 0px; width: 70px; height: auto;"/></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <center><div style="padding-top: 15px; font-size: 14px;"><?=$GLOBALS['lang'][200]?></div><br></center>
                            </div>
                            
                    <?php }else {
                        $mostrar_config_vpn = 1;
                    }
                }else { 
                    $mostrar_config_vpn = 1; 
                }
                
                if($mostrar_config_vpn == 1) { ?>
                    <div class="QuadroConfigs">
                        <table border="0" cellspacing="0" cellpadding="0" style="padding: 10px;">
                            <tbody>
                            <tr>
                                <!-- Ativar VPN [0: Não, 1: Sim] -->
                                <td align="right" class="DivConfigNome"><?=$GLOBALS['lang'][201]?>:</td>
                                <td align="left">
                                    <select name="ativar8" id="ativar8" class="selectGInput" style="width: 196px; font-size: 12px; height: 33px;" onChange="javascript:bloqueio8();">
                                        <option value="0" <?php if ($fetClientes['ativaVPN'] == 0) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][146]?></option>
                                        <option value="1" <?php if ($fetClientes['ativaVPN'] == 1) { echo "selected"; } ?> style="width: 50px;"><?=$GLOBALS['lang'][145]?></option>
                                    </select>
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][202]?></span></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div id="AbreVPN" style="display: <?php if ($fetClientes['ativaVPN'] == 0) { echo "none"; }else { echo "block"; } ?>;">
                            <br><br>
                            <center><div id="auth" style="display: block"><?=$GLOBALS['lang'][203]?></div></center>
                            <br><br>
                            <table border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom: 15px;">
                                <tbody>
                                <tr>
                                    <td width="150" class="UsersTitulo">IP:</td>
                                    <td width="150" class="UsersTitulo"><?=$GLOBALS['lang'][112]?>:</td>
                                    <td width="150" class="UsersTitulo" style="border-radius: 0px 0px 10px 0px;"><?=$GLOBALS['lang'][151]?>:</td>
                                </tr>
                                <tr>
                                    <td class="UsersTitulo"><input name="ipvpn" id="ipvpn" class="imputConfNome" style="background: #FFF;" type="text" value="<?php echo $fetClientes['ipvpn']; ?>" autocomplete="off"></td>
                                    <td class="UsersTitulo"><input name="uservpn" id="uservpn" class="imputConfNome" style="background: #FFF;" type="text" value="<?php echo $fetClientes['uservpn']; ?>" autocomplete="off"></td>
                                    <td class="UsersTitulo"><input name="senhavpn" id="senhavpn" class="imputConfNome" style="background: #FFF;" type="password" value="<?php echo $fetClientes['senhavpn']; ?>" autocomplete="off"></td>
                                </tr>
                                </tbody>
                            </table>

                            <?php if ($fetClientes['ativaVPN'] == 1) { echo '<a href="conectar_vpn.php" style="text-decoration: none;"><div class="UserInsert">' . $GLOBALS['lang'][204] . '</div></a>'; } ?>
                            
                            <br>
                        </div>
                    
                <?php } ?>

                    <div id="AbreVPN2" class="barrarolagem2" style="max-height: 130px; display: <?php if ($fetClientes['ativaVPN'] == 0) { echo "none"; }else { echo "block"; } ?>;">
                        <?php
                        $resultRotas_vpn = mysqli_query($db, "SELECT * FROM rotas_vpn ORDER BY id ASC");
                        $totalRotas_vpn = mysqli_num_rows($resultRotas_vpn);

                        echo '<table width="75%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top: 10px"><tbody>';
                        echo '<tr>';
                        echo '<td class="UsersTitulo" style="border-radius: 0px 0px 10px 0px; border-left: none;">IP / ' . $GLOBALS['lang'][205] . ': <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-telegram">' . $GLOBALS['lang'][206] . '</span></div></td>';
                        echo '<td width="20"></td>';
                        echo '</tr>';

                        $cor = '#FFF';
                        $ArredondarBorda = 'sim';
                        while ($a_rota = mysqli_fetch_array($resultRotas_vpn)) {
                            if ($cor == '#FFF') {
                                echo '<tr>';
                                if ($ArredondarBorda == 'sim') {
                                    echo '<td class="UsersTdTipo" style="border-radius: 0px 10px 0px 0px;"><input name="rid[]" id="rid[]" type="hidden" value="' . $a_rota['id'] . '" autocomplete="off"><input name="rota_vpn[]" id="rota_vpn[]" class="imputConfNome" style="background: #FFF;" type="text" value="' . $a_rota['valor'] . '" autocomplete="off"></td>';
                                    $ArredondarBorda = 'nao';
                                } else {
                                    echo '<td class="UsersTdTipo"><input name="rid[]" id="rid[]" type="hidden" value="' . $a_rota['id'] . '" autocomplete="off"><input name="rota_vpn[]" id="rota_vpn[]" class="imputConfNome" style="background: #FFF;" type="text" value="' . $a_rota['valor'] . '" autocomplete="off"></td>';
                                }
                                echo '<td class="UsersTdExcluir"><a href="javascript:rotavpn_excluir(' . $a_rota['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][207] . '"/></a></td>';
                                echo '</tr>';
                                $cor = "#eeeeee";
                            } else {
                                echo '<tr style="background: #eeeeee;">';
                                echo '<td class="UsersTd"><input name="rid[]" id="rid[]" type="hidden" value="' . $a_rota['id'] . '" autocomplete="off"><input name="rota_vpn[]" id="rota_vpn[]" class="imputConfNome" style="background: #eeeeee;" type="text" value="' . $a_rota['valor'] . '" autocomplete="off"></td>';
                                echo '<td class="UsersTdExcluir"><a href="javascript:rotavpn_excluir(' . $a_rota['id'] . ');"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][207] . '"/></a></td>';
                                echo '</tr>';
                                $cor = "#FFF";
                            };
                        };
                        
                        if ($cor == '#FFF') {
                            echo '<tr id="InserindoRotavpn" style="display:none;">';
                            echo '<td class="UsersTd"><input name="rota_id_new" id="rota_id_new" class="imputConfUsr" style="background: #FFF;" type="text" value="" autocomplete="off"></td>';
                            echo '<td width="34" class="UsersTdExcluir"><img src="img/deleteOff.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][207] . '"/></td>';
                            echo '</tr>';
                        } else {
                            echo '<tr id="InserindoRotavpn" style="background: #eeeeee; display:none;">';
                            echo '<td class="UsersTd"><input name="rota_id_new" id="rota_id_new" class="imputConfUsr" style="background: #eeeeee;" type="text" value="" autocomplete="off"></td>';
                            echo '<td width="34" class="UsersTdExcluir"><img src="img/deleteOff.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][207] . '"/></td>';
                            echo '</tr>';
                        };

                        echo '</tbody></table>';
                        ?>
                        </div>
                        <a href="#" id="AbrirRotavpn" style="text-decoration: none; display: <?php if ($fetClientes['ativaVPN'] == 0) { echo "none"; }else { echo "block"; } ?>;">
                        <div class="UserInsert"><?=$GLOBALS['lang'][208]?></div>
                        </a><br>
                    </div>
                </div>

            <div id="div_aba4" class="MostraSensor" style="display:none; padding-bottom: 20px;">
                <br>
                <!-- Opções de backup e restauração -->
                <div id="container"><?=$GLOBALS['lang'][209]?></div>

                <div class="QuadroConfigs">
                    <!-- Customização de Backup -->
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][210]?></div>
                    <div class="DivCongigTable">
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top: 15px; margin-bottom: 15px;">
                            <tbody>
                            <tr>
                                <td class="UsersTitulo" align="left"><?=$GLOBALS['lang'][102]?></td>
                                <td class="UsersTitulo" align="left" style="border-radius: 0px 0px 10px 0px;"><?=$GLOBALS['lang'][211]?></td>
                            </tr>

                            <tr style="background: #eeeeee;">
                                <td class="UsersTitulo" height="25" align="left" valign="middle"><?=$GLOBALS['lang'][212]?></td>
                                <td class="UsersTitulo" height="25" align="left" valign="middle" style="border-radius: 0px 10px 10px 0px;">
                                    <select name="backupHistoricos" id="backupHistoricos" class="selectConf">
                                        <option value="1" style="width: 100px;" <?php if ($fetClientes['backupHistoricos'] == 1) { echo "selected";} ?>><?=$GLOBALS['lang'][213]?></option>
                                        <option value="2" style="width: 100px;" <?php if ($fetClientes['backupHistoricos'] == 2) { echo "selected"; } ?>><?=$GLOBALS['lang'][214]?></option>
                                        <option value="3" style="width: 100px;" <?php if ($fetClientes['backupHistoricos'] == 3) { echo "selected"; } ?>><?=$GLOBALS['lang'][215]?></option>
                                        <option value="4" style="width: 100px;" <?php if ($fetClientes['backupHistoricos'] == 4) { echo "selected"; } ?>><?=$GLOBALS['lang'][216]?></option>
                                        <option value="5" style="width: 100px;" <?php if ($fetClientes['backupHistoricos'] == 5) { echo "selected"; } ?>><?=$GLOBALS['lang'][217]?></option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td class="UsersTitulo" height="25" align="left" valign="middle"><?=$GLOBALS['lang'][46]?></td>
                                <td class="UsersTitulo" height="25" align="left" valign="middle" style="border-radius: 0px 10px 10px 0px;">
                                    <select name="backupConcentradoras" id="backupConcentradoras" class="selectConf">
                                        <option value="0" disabled style="width: 60px;" <?php if ($fetClientes['backupConcentradoras'] == 0) { echo "selected"; } ?>><?=$GLOBALS['lang'][218]?></option>
                                        <option value="1" style="width: 60px;" <?php if ($fetClientes['backupConcentradoras'] == 1) { echo "selected"; } ?>><?=$GLOBALS['lang'][219]?></option>
                                    </select>
                                </td>
                            </tr>

                            <tr style="background: #eeeeee;">
                                <td class="UsersTitulo" height="25" align="left" valign="middle"><?=$GLOBALS['lang'][45]?></td>
                                <td class="UsersTitulo" height="25" align="left" valign="middle" style="border-radius: 0px 10px 10px 0px;">
                                    <select name="backupOLTs" id="backupOLTs" class="selectConf">
                                        <option value="1" disabled style="width: 60px;" <?php if ($fetClientes['backupOLTs'] == 1) { echo "selected"; } ?>><?=$GLOBALS['lang'][218]?></option>
                                        <option value="2" style="width: 60px;" <?php if ($fetClientes['backupOLTs'] == 2) { echo "selected"; } ?>><?=$GLOBALS['lang'][220]?></option>
                                        <option value="3" style="width: 60px;" <?php if ($fetClientes['backupOLTs'] == 3) { echo "selected"; } ?>><?=$GLOBALS['lang'][221]?></option>
                                    </select>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="QuadroConfigs">
                    <!-- Backups -->
                    <div class="DivConfigTitulo"><?=$GLOBALS['lang'][92]?>:</div>
                    <div class="DivCongigTable">

                        <div class="area-upload">
                            <label for="upload-file" class="label-upload">
                                <div class="texto"><img src="img/upload.png" width="40" height="auto" style="margin-bottom: 15px;" alt=""/><br><?=$GLOBALS['lang'][222]?></div>
                            </label>
                            <input type="file" accept=".sql,.gz" id="upload-file" multiple/>

                            <div class="lista-uploads">
                            </div>
                        </div>

                        <?php
                        //$path = shell_exec("ls -t /var/www/html/bkpRavi/ | grep \.sql$ | sed ':a;$!N;s/\\n/|/;ta;'");
                        $path = shell_exec("ls -t /var/www/html/bkpRavi/ | grep -E '\.(sql|gz)$' | sed ':a;$!N;s/\\n/|/;ta;'");
                        $arquivo = explode('|', $path);

                        if (count(array_filter($arquivo)) >= 1) {
                            echo "<a href='javascript:gerarbackup();' class='botbkp'>" . $GLOBALS['lang'][223] . "</a><br><br>";
                            echo '<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top: 15px; margin-bottom: 15px;"><tbody>';
                            echo '<tr>';
                            echo '<td class="UsersTitulo" align="left">' . $GLOBALS['lang'][224] . '</td>';
                            echo '<td class="UsersTitulo" align="left">' . $GLOBALS['lang'][225] . '</td>';
                            echo '<td class="UsersTitulo" align="left" style="border-radius: 0px 0px 10px 0px;">' . $GLOBALS['lang'][211] . '</td>';
                            echo '<td></td>';
                            echo '</tr>';
                            $cor = '#FFF';
                            for ($i = 0; $i < count($arquivo); $i++) {
                                if ($arquivo[$i] != "" && $arquivo[$i] != "." && $arquivo[$i] != "..") {
                                    $exp = explode('_', $arquivo[$i]);
                                    $exp1 = explode('.', $exp[3]);
                                    $data = $exp[2] . " " . str_replace('-', ':', $exp1[0]);
                                    
                                    $size_exec = exec("du -hsk /var/www/html/bkpRavi/" . $arquivo[$i]);
                                    $exp2 = explode('/', $size_exec);
                                    $size = (int)$exp2[0];
                                    
                                    if ($cor == '#FFF') {
                                        echo '<tr>';
                                        echo "<td class='UsersTitulo' width='110' height='35' align='left' valign='middle'>" . $data . "</td>";
                                        echo "<td class='UsersTitulo' height='35' align='left' valign='middle'>" . $size . " KB</td>";
                                        echo '<td class="UsersTitulo" width="130" height="35" align="left" valign="middle" style="border-radius: 0px 10px 10px 0px;">';
                                        echo "<a href='bkpRavi/" . $arquivo[$i] . "' class='botbkp'>" . $GLOBALS['lang'][226] . "</a>";
                                        echo "<a href='javascript:restaurarBkp(\"" . $arquivo[$i] . "\");' class='botbkp'>" . $GLOBALS['lang'][227] . "</a>";
                                        echo '</td>';
                                        echo '<td class="UsersTdExcluir"><a href="ExcluirBackupRavi.php?arq=' . $arquivo[$i] . '"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][228] . '"/></a></td>';
                                        echo '</tr>';
                                        $cor = "#eeeeee";
                                    } else {
                                        echo '<tr style="background: #eeeeee;">';
                                        echo "<td class='UsersTitulo' height='35' align='left' valign='middle'>" . $data . "</td>";
                                        echo "<td class='UsersTitulo' height='35' align='left' valign='middle'>" . $size . " KB</td>";
                                        echo '<td class="UsersTitulo" height="35" align="left" valign="middle" style="border-radius: 0px 10px 10px 0px;">';
                                        echo "<a href='bkpRavi/" . $arquivo[$i] . "' class='botbkp'>" . $GLOBALS['lang'][226] . "</a>";
                                        echo "<a href='javascript:restaurarBkp(\"" . $arquivo[$i] . "\");' class='botbkp'>" . $GLOBALS['lang'][227] . "</a>";
                                        echo '</td>';
                                        echo '<td class="UsersTdExcluir" style="background: #FFF;"><a href="ExcluirBackupRavi.php?arq=' . $arquivo[$i] . '"><img src="img/delete.png" class="ImgOlho" width="auto" height="15" alt="' . $GLOBALS['lang'][228] . '"/></a></td>';
                                        echo '</tr>';
                                        $cor = "#FFF";
                                    }
                                }
                                if ($i == 9) {
                                    break;
                                }
                            }
                            echo '</tbody></table>';
                        } else {
                            echo "Ainda não existe nenhum backup! <a href='javascript:gerarbackup();' class='botbkp'>" . $GLOBALS['lang'][229] . "</a><br><br><br><br>";
                        }
                        ?>
                    </div>
                </div>

                <br>
            </div>

            <div id="div_aba5" class="MostraSensor" style="display:none; padding-bottom: 20px;">
                <br>
                <!-- Certificado SSL [1: Ativo próprio, 2: Ativo Ravi, 3: Desativado] -->
                <div id="container"><?=$GLOBALS['lang'][93]?></div>
                <br><br>

                <select name="ativar7" id="ativar7" class="selectGInput" style="width: 380px; font-size: 12px; height: 33px;" onChange="javascript:bloqueio7();">
                    <option value="1" <?php if ($fetClientes['ativaSSL'] == 1) { echo "selected"; } ?> style="width: 150px;"><?=$GLOBALS['lang'][230]?></option>
                    <option value="2" <?php if ($fetClientes['ativaSSL'] == 2) { echo "selected"; } ?> style="width: 150px;"><?=$GLOBALS['lang'][231]?></option>
                    <option value="3" <?php if ($fetClientes['ativaSSL'] == 3) { echo "selected"; } ?> style="width: 150px;"><?=$GLOBALS['lang'][232]?></option>
                </select>
                <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][233]?></span></div>
                <br>

                <div id="AbreSSL" style="display:<?php if ($fetClientes['ativaSSL'] == 1) { echo "block"; } else { echo "none"; } ?>;">
                    <br><br>
                    <div class="QuadroConfigs">
                        <table border="0" width="100%" cellspacing="0" cellpadding="5" style="padding: 10px;">
                            <tbody>
                            <tr>
                                <td align="left" class="DivConfigNome"><?=$GLOBALS['lang'][234]?>:
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][235]?></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <center><textarea id="certificado_crt_cli" name="certificado_crt_cli" class="DivCongiInput" style="width: 100%; height: 220px;"><?php echo $fetClientes['certificado_crt_cli']; ?></textarea></center>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="QuadroConfigs">
                        <table border="0" width="100%" cellspacing="0" cellpadding="5" style="padding: 10px;">
                            <tbody>
                            <tr>
                                <td align="left" class="DivConfigNome"><?=$GLOBALS['lang'][236]?>:
                                    <div class="tooltip"><img class="imginformacao" src="img/informacao.png" style="border: 0px;"/><span class="tooltiptext tooltip-som"><?=$GLOBALS['lang'][237]?></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <center><textarea id="certificado_key_cli" name="certificado_key_cli" class="DivCongiInput" style="width: 100%; height: 220px;"><?php echo $fetClientes['certificado_key_cli']; ?></textarea></center>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="AbreSSLRavi" style="display:<?php if ($fetClientes['ativaSSL'] == 2) { echo "block"; } else { echo "none"; } ?>;">
                    <?php if (!$fetClientes['subdom_ssl']) { ?>
                        <br>
                        <center><div class="comunicado"><?=$GLOBALS['lang'][238]?></div></center>
                    <?php } ?>
                    <br><br>
                    <div style="width: 97,34%; margin-bottom: 10px; background: #e5ddd5 url(img/bg_addSensor.gif) no-repeat center; background-size:cover; border-radius: 0px 0px 15px 15px; transition:all 0.3s ease; -webkit-border-radius: 15px 15px 15px 15px; -moz-border-radius: 0px 0px 15px 15px; -ms-border-radius: 0px 0px 15px 15px; -o-border-radius: 0px 0px 15px 15px; font-size: 16px; padding: 20px; text-align: center;">
                        <?php if (!$fetClientes['subdom_ssl']) { ?>
                            <br><span style="font-size: 18px;"><img src="img/Logo.png" width='auto' height='20' style="padding-right: 5px;" alt="Ravi Monitoramento"/> <?=$GLOBALS['lang'][239]?></span>
                            <br><br><br>
                        <?php } else { ?>
                            <br><br><span style="font-size: 18px;"><?=$GLOBALS['lang'][240]?>:</span><br>
                        <?php } ?>

                        <?php if (!$fetClientes['subdom_ssl']) { ?>
                            <table border="0" width="400" align="center" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <td align="right" valign="middle">
                                        <?php echo '<input name="subdom_ssl" id="subdom_ssl" class="criasubdominio" type="text" value="' . $fetClientes['subdom_ssl'] . '" autocomplete="off" onkeyup="javascript:verificaSubDominio()">'; ?>
                                    </td>
                                    <td align="left" valign="middle" style="color: #000; font-size: 30px;">
                                        .ravisystems.com.br
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div id="ip" style="display: none">
                                <img src="img/flecha_dns.png" width='auto' height='25' alt="" style="margin-top: 10px;"/><br>
                                <input name="subdom_ssl_ip" id="subdom_ssl_ip" class="criasubdominio_ip" type="text" placeholder="IPv4 de acesso a esse servidor" value="<?php echo $fetClientes['subdom_ssl_ip']; ?>" autocomplete="off"> 
                                <input name="subdom_ssl_ip6" id="subdom_ssl_ip6" class="criasubdominio_ip" type="text" placeholder="IPv6 de acesso a esse servidor" value="<?php echo $fetClientes['subdom_ssl_ip6']; ?>" autocomplete="off">
                            </div>
                        <?php } else { ?>
                            <div style="color: #000; font-size: 24px;">
                                <a href="https://<?php echo $fetClientes['subdom_ssl']; ?>.ravisystems.com.br" style="text-decoration: none"><strong><?php echo $fetClientes['subdom_ssl']; ?></strong>.ravisystems.com.br</a><br><br>
                                <?php if(!$fetClientes['subdom_ssl_ip6']) { ?>
                                    <input name="subdom_ssl_ip62" id="subdom_ssl_ip62" class="criasubdominio_ip" type="text" placeholder="IPv6 de acesso a esse servidor" value="" autocomplete="off"><br><br>
                                <?php } ?>
                                <a href="javascript:excluirSub('<?php echo $fetClientes['subdom_ssl']; ?>');" style="text-decoration: none"><img src="img/delete.png" width='auto' height='15' alt="<?=$GLOBALS['lang'][242]?>"/> <span style="color: #000; font-size: 16px;"><?=$GLOBALS['lang'][241]?></span></a>
                            </div>
                        <?php } ?>
                        <br>
                    </div>
                </div>
            </div>

            <center>
                <!-- Salvar modificações -->
                <button class="SalvarConfigs" type="submit"><?=$GLOBALS['lang'][243]?></button>
            </center>

        </div>
        <div id="Rodape"></div>
    </div>
    <input type="hidden" nome="tipoFoto" id="tipoFoto" value="system">
</form>

<script src="js/clipboard.min.js"></script>

<script>
    var clipboard = new ClipboardJS('.btn');

    clipboard.on('success', function (e) {
        console.log(e);
    });

    clipboard.on('error', function (e) {
        console.log(e);
    });
</script>

<script src="js/jquery.validate.js"></script>
<script src="js/jquery.ubaplayer.js"></script>
<script src="js/script.js"></script>

<script>
    $(function () {
        $("#ubaPlayer").ubaPlayer({
            codecs: [{name: "MP3", codec: 'audio/mpeg;'}]
        });
    });
</script>


<script language="javascript">

    jQuery(document).ready(function () {
        $.validator.setDefaults({
            submitHandler: function () {
                //console.log(document.getElementById("code").value);
                var dados = jQuery('#ajax_form').serialize();
                //alert(aba);
                $.ajax({
                    type: 'POST',
                    url: "enviaconfig.php",
                    data: dados,
                    success: function (response) {
                        //$('#resp').html(response);
                        //console.log(response);
                        //alert("Cadastro atualizado com sucesso!");
                        alert(response);
                        window.location.href = 'config.php?aba=' + aba + '';
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        });

// validate signup form on keyup and submit
        $("#ajax_form").validate({
            rules: {
                host: {
                    required: false,
                    minlength: 1
                },
                usuario: {
                    required: false,
                    minlength: 1
                },
                senhamk: {
                    required: false,
                    minlength: 1
                },
            },
            messages: {
                host: "<font color=\"#FF4D4D\"><?=$GLOBALS['lang'][244]?></font><br>",
                usuario: "<font color=\"#FF4D4D\"><?=$GLOBALS['lang'][244]?></font><br>",
                senhamk: "<font color=\"#FF4D4D\"><?=$GLOBALS['lang'][244]?></font><br>",
            }

        });

    });

    <?php
    if ($abaa == 1) {
        if (isset($_GET['aba1'])) {
            echo "mostrar_abas_users('mostra_aba_user2');";
        } else {
            echo "mostrar_abas('mostra_aba1');";
        }
    } else if ($abaa == 2) {
        echo "mostrar_abas('mostra_aba2');";
    } else if ($abaa == 3) {
        echo "mostrar_abas('mostra_aba3');";
        if($fetClientes["ip_config_avancado"] == 0) {
            echo "document.getElementById('mostra_aba_ipv41').focus();";
            echo "document.getElementById('mostrar_abas_ipv41').style.display = 'block';";
            echo "document.getElementById('mostrar_abas_ipv42').style.display = 'none';";
        } else {
            echo "document.getElementById('mostra_aba_ipv42').focus();";
            echo "document.getElementById('mostrar_abas_ipv41').style.display = 'none';";
            echo "document.getElementById('mostrar_abas_ipv42').style.display = 'block';";
        }
    } else if ($abaa == 4) {
        echo "mostrar_abas('mostra_aba4');";
    } else if ($abaa == 5) {
        echo "mostrar_abas('mostra_aba5');";
    }
    ?>

    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        theme: 'liquibyte',
        lineNumbers:true,
        lineWrapping:true,
        autoRefresh:true,
        styleActiveLine:true,
        fixedGutter:true,
        lint:true,
        coverGutterNextToScrollbar:false,
        gutters: ['CodeMirror-lint-markers'],
        mode: 'javascript'
    });

    function checkreinstall() {
        $.ajax({
            type: "POST",
            url: "reinstallnode.php",
            success: function (data) { }
        });
    }

    var tt = 1;

    function checkUpdate() {
        if(document.getElementById("searchbar").value == "") {
            let qrcode = null
            //$('#loadingDiv').show();

            $.ajax({
                url: `/whatsapp/check-update`,
                error: (res) => {
                    //rebootnode2();
                    if(document.getElementById("ativar6").value == 1) {
                        if(tt == 12) {
                            checkreinstall();
                            tt = 0;
                        }
                        tt++;
                        console.log(tt);
                    }
                    //
                    //toast('error', res.responseJSON ? res.responseJSON.message : 'Erro processar solicitação')
                },
                success: (res) => {
                    if (res.authenticated) {
                        $('#qrcode').hide()
                        $('#auth').hide()
                        $('#result').show()
                        $('#contactsDiv').show();
                        $('#botaoDesconectar').show();
                        $("#bateria").html(res.phone ? res.phone.replace(/(\d{2})(\d{2})(\d{5})(\d{4})/, "+$1 $2 $3-$4") : "");
                        //alert("Deu Certo");
                        //getContacts()
                        contatos()
                        salvaAtivacaoWhats();
                    } else {
                        //alert("não gerou qrcode");
                        $('#qrcode').show()
                        $('#auth').show()
                        $('#result').hide()
                        $('#contactsDiv').hide();
                        $('#botaoDesconectar').hide();
                        //$('#contacts-container').hide()
                        //$('#auth-container').show()
                        if(res.qrcode) {
                            if (qrcode) {
                                qrcode.set({ value: res.qrcode })
                            } else {
                                qrcode = new QRious({
                                    size: 300,
                                    element: document.getElementById('qrcode'),
                                    value: res.qrcode
                                });
                            }
                            $('.qr-container').removeClass('loading');
                        } else {
                            $('.qr-container').addClass('loading');
                        }
                        
                    }
                },
            })
        }
    }

    function salvaAtivacaoWhats() {
        $.ajax({
            url: `salvarContatosWhats.php?acao=ativar`,
            error: (res) => {
            },
            success: (res) => {
                //alert(res);
            },
        })
    }

    function desconectar() {
        $.ajax({
            url: `/whatsapp/disconnect`,
            error: (res) => {
                //toast('error', res.responseJSON ? res.responseJSON.message : 'Erro processar solicitação')
            },
            success: (res) => {
                //console.log(res);
                alert("<?=$GLOBALS['lang'][245]?>")
                //checkUpdate()
                window.location.reload();
            },
        });
        /*
        $.ajax({
            type: "POST",
            url: "rebootnode.php",
            success: function (response) {
                //$('#resp').html(response);
                //location.reload();
                window.location.href = 'config.php?aba=2';
            }
        });
        */
    }

    function testar() {
        //alert("teste");
        $.ajax({
            url: `teste_whats.php`,
            success: (res) => {
                alert(res)
                //checkUpdate()
                //window.location.reload();
            },
        })
    }

    let contadosAdd = [];
    let contadosAddAux = [];

    function adicionarContatos(obj) {
        let tp = "";
        str = $(obj).data('name');
        let ct = $(obj).data('id') + "_" + str.replace(/[^a-z0-9]/gi,'');
        //alert($(obj).data('id') + "_" + $(obj).data('name'));
        if ($(obj).hasClass('contatoSelecionado')) {
            $(obj).removeClass('contatoSelecionado');
            var cIndex = contadosAdd.indexOf($(obj).data('id') + "_" + $(obj).data('name'));
            contadosAdd.splice(cIndex, 1);
            tp = "rem"
        } else {
            $(obj).addClass('contatoSelecionado');
            contadosAdd.push(ct);
            tp = "add"
        }

        $.ajax({
            url: 'salvarContatosWhats.php?contatos=' + ct + '&tp=' + tp,
            error: (res) => {
            },
            success: (res) => {
                //alert(res);
            },
        }).done(function (response) {
            checkUpdate<?php echo $api2['id']; ?>();
        });

    }

    function contatos() {
        $contacts = $('#contacts')
        //if (!$contacts.children('li').length) {
            $.ajax({
                url: `salvarContatosWhats.php?acao=buscar`,
                error: (res) => {
                },
                success: (res) => {
                    var arr = res.split('|');
                    contadosAddAux = [];
                    //alert(res);
                    for (var i in arr) {
                        contadosAddAux.push(arr[i]);
                    }
                },
            })
            setTimeout(function () {
                $.ajax({
                    url: `/whatsapp/get-contacts`,
                    error: (res) => {
                        //alert("erro");
                    },
                    success: (res) => {
                        let html = ''
                        let htmlN = ''
                        let htmlF = ''
                        for (let contact of res.data) {
                            let name = contact.name || contact.id.user

                            var con = contact.id._serialized;

                            k = "";
                            //alert(contadosAddAux.length);
                            for (let i = 0; i < contadosAddAux.length; i++) {
                                //alert(con+" - "+contadosAddAux[i])
                                if (con == contadosAddAux[i].toString()) {
                                    //alert(con);
                                    k = "contatoSelecionado";
                                    //contadosAdd.push(con + "_" + name);
                                    //break;
                                }
                            }
                            if (contact.isGroup) {
                                if (k != "") {
                                    htmlN += `<li onclick="javascript:adicionarContatos(this);" class="list-group-item ${k}" data-id="${contact.id._serialized}" data-name="${name}">&nbsp;<strong><?=$GLOBALS['lang'][113]?>: </strong>&nbsp;${name}</li>`
                                } else {
                                    html += `<li onclick="javascript:adicionarContatos(this);" class="list-group-item ${k}" data-id="${contact.id._serialized}" data-name="${name}">&nbsp;<strong><?=$GLOBALS['lang'][113]?>: </strong>&nbsp;${name}</li>`
                                }
                            } else {
                                if (k != "") {
                                    htmlN += `<li onclick="javascript:adicionarContatos(this);" class="list-group-item ${k}" data-id="${contact.id._serialized}" data-name="${name}"><img src="img/whats.png" width="17px"> &nbsp;&nbsp;${name} ${
                                        name !== contact.id.user ? '(' + contact.id.user + ')' : ''
                                        }</li>`
                                } else {
                                    html += `<li onclick="javascript:adicionarContatos(this);" class="list-group-item ${k}" data-id="${contact.id._serialized}" data-name="${name}"><img src="img/whats.png" width="17px"> &nbsp;&nbsp;${name} ${
                                        name !== contact.id.user ? '(' + contact.id.user + ')' : ''
                                        }</li>`
                                }
                            }
                        }
                        htmlF = htmlN + html;
                        $contacts.html(htmlF)
                    },
                })
            }, 1500);
            setTimeout(function () {
                $.ajax({
                    url: `salvarContatosWhats.php?acao=verificar&cel=`+$("#bateria").html(),
                    error: (res) => {
                    },
                    success: (res) => {
                    },
                })
            }, 2500);
            //alert(contadosAdd.length);
        //}
    }

    function search_contato() {
        let input = document.getElementById('searchbar').value;
        input = input.toLowerCase();
        let x = document.getElementsByClassName('list-group-item');

        for (i = 0; i < x.length; i++) {
            if (!x[i].innerHTML.toLowerCase().includes(input)) {
                x[i].style.display = "none";
            }
            else {
                x[i].style.display = "block";
            }
        }
    }

    checkUpdate()
    setInterval(checkUpdate, 1000)

</script>
<script src="vendor/croppie/demo/prism.js"></script>
<script src="vendor/croppie/croppie.js"></script>
<script src="vendor/croppie/demo/demo.js"></script>
<script>
    Demo.init();
</script>
<div id="resp"></div>
</body>
</html>
