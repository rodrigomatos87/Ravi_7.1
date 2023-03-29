<?php
$cmd1 = "mysqladmin -u root password '#H0gGLS3@XeaW702_i51z@yUlN#'";
$cmd2 = "mysql -u root -p#H0gGLS3@XeaW702_i51z@yUlN# -e 'CREATE DATABASE Ravi'";
$cmd3 = "mysql -h localhost -u root -p#H0gGLS3@XeaW702_i51z@yUlN# -e \"DELETE FROM mysql.user WHERE user='';\"";
$cmd4 = "mysql -h localhost -u root -p#H0gGLS3@XeaW702_i51z@yUlN# -e \"DELETE FROM mysql.user WHERE host='localhost.localdomain';\"";
$cmd5 = "mysql -h localhost -u root -p#H0gGLS3@XeaW702_i51z@yUlN# -e \"GRANT ALL PRIVILEGES ON * . * TO 'root'@'localhost';\"";
$cmd6 = "mysql -h localhost -u root -p#H0gGLS3@XeaW702_i51z@yUlN# -e \"FLUSH PRIVILEGES;\"";
$cmd7 = "chown -R www-data:www-data /var/www/html";

exec($cmd1);
exec($cmd2);
exec($cmd3);
exec($cmd4);
exec($cmd5);
exec($cmd6);
exec($cmd7);

function random_str_mt($size) {
    $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    $key = '';
    for ($i = 0; $i < ($size+10); $i++) { $key .= $keys[array_rand($keys)]; }
    return substr($key, 0, $size);
}

$cores_cpu = exec("cat /proc/cpuinfo | grep processor | grep -v 'model name' | wc -l");
$tokenRAVI = random_str_mt(14) . "-" . random_str_mt(14) . "-" . random_str_mt(14) . "-" . random_str_mt(14);
$versao = "7.0";
$versaoData = "10/10/2022";
$versaoDebug = 0;

if(!$cores_cpu) { $cores_cpu = 1; }

$db = mysqli_connect("localhost", "root", "#H0gGLS3@XeaW702_i51z@yUlN#", "Ravi");

$certificado_crt = "-----BEGIN CERTIFICATE-----
MIIHpzCCBY+gAwIBAgIQD1Y5RT/usDqj5/Nkmy0CnzANBgkqhkiG9w0BAQsFADBc
MQswCQYDVQQGEwJVUzEXMBUGA1UEChMORGlnaUNlcnQsIEluYy4xNDAyBgNVBAMT
K1JhcGlkU1NMIEdsb2JhbCBUTFMgUlNBNDA5NiBTSEEyNTYgMjAyMiBDQTEwHhcN
MjMwMTI1MDAwMDAwWhcNMjQwMTI3MjM1OTU5WjAfMR0wGwYDVQQDDBQqLnJhdmlz
eXN0ZW1zLmNvbS5icjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKAh
B9eE+nSM1WwolbmeLCQaF3QdkZL5fM6cQTgZCOoXFNE/cEF/oPc67vbMNafLPnGA
j7CmYbjg50HEuQBcb2XzXnDXJGfaZ9rb+D6cWv0Ao18HjP3brydXGVDBybJYdBY9
UHVDYbusU2Wyi05WvdN4agWHQPosLbSVGhkbkvqQYHpgrYDmZ/rDkkGgAqrYyxSU
YV48fH12XZXNmIUW2hMpRibxudHe/iXBL3rN/oKbL0CbG8nILsxxgEqbNh9yA44P
JcI5/YIwxbzx0ZcMSoewZ0DLuOEMakjUDUVLMTGQFOCsgMpi2eGdvcs5oCYtIgyO
FJQ46MNIXWXCj+axzCECAwEAAaOCA6AwggOcMB8GA1UdIwQYMBaAFPCchf2in32P
yWi71dSJTR2+05D/MB0GA1UdDgQWBBTGuL/EW4FUFG9ysRGM/ItGsWJ6QjAzBgNV
HREELDAqghQqLnJhdmlzeXN0ZW1zLmNvbS5icoIScmF2aXN5c3RlbXMuY29tLmJy
MA4GA1UdDwEB/wQEAwIFoDAdBgNVHSUEFjAUBggrBgEFBQcDAQYIKwYBBQUHAwIw
gZ8GA1UdHwSBlzCBlDBIoEagRIZCaHR0cDovL2NybDMuZGlnaWNlcnQuY29tL1Jh
cGlkU1NMR2xvYmFsVExTUlNBNDA5NlNIQTI1NjIwMjJDQTEuY3JsMEigRqBEhkJo
dHRwOi8vY3JsNC5kaWdpY2VydC5jb20vUmFwaWRTU0xHbG9iYWxUTFNSU0E0MDk2
U0hBMjU2MjAyMkNBMS5jcmwwPgYDVR0gBDcwNTAzBgZngQwBAgEwKTAnBggrBgEF
BQcCARYbaHR0cDovL3d3dy5kaWdpY2VydC5jb20vQ1BTMIGHBggrBgEFBQcBAQR7
MHkwJAYIKwYBBQUHMAGGGGh0dHA6Ly9vY3NwLmRpZ2ljZXJ0LmNvbTBRBggrBgEF
BQcwAoZFaHR0cDovL2NhY2VydHMuZGlnaWNlcnQuY29tL1JhcGlkU1NMR2xvYmFs
VExTUlNBNDA5NlNIQTI1NjIwMjJDQTEuY3J0MAkGA1UdEwQCMAAwggF9BgorBgEE
AdZ5AgQCBIIBbQSCAWkBZwB1AO7N0GTV2xrOxVy3nbTNE6Iyh0Z8vOzew1FIWUZx
H7WbAAABhel+6N0AAAQDAEYwRAIgRr1uQy87PRamx6o9hUAED154kzqJrCp995YY
oB+ZsGYCIB1bMhoEwO/Dw2OkCa6UEYqTy+B98vD45xYzos55a4ItAHYAc9meiRtM
lnigIH1HneayxhzQUV5xGSqMa4AQesF3crUAAAGF6X7pJwAABAMARzBFAiAgX+CB
Sfa4gfDhx1W+FbxrUxztOce1pV+y1rUOOeuAdQIhAOrOH0XGractbKPSSnoo+BgE
H981P72ooJpUU/7AfOVtAHYASLDja9qmRzQP5WoC+p0w6xxSActW3SyB2bu/qznY
hHMAAAGF6X7pAAAABAMARzBFAiEAiGg5pfIB47fYqYuCzu6P1yDON4i9AGhZtPmL
Zcvj6dcCICuo5plT5tXxfmWgmEZtulyaF8pG/JLhACrC1JyaDebhMA0GCSqGSIb3
DQEBCwUAA4ICAQAiLQy6IJa+9rhtGaluUb0xouhZaRizff0Oo3v6bORfoorKwsDe
3ppbUqF4FkI+04taC1tdoD1pBJ2Iam9oRKJYJX6iJVrlWh8QXCqnXlojUMSOZ56y
Cn0fkv3GywZ3GbgKLIaJa2v1O+CnPdZmHAh4dMOQQeDfFVOtUbaMy6wnzxZs3jCd
S+5Ivrqr5yPT4dtnwlVOtliPBBQchYuKma/ib3AFIyVqFDV5qrj5yQkgGpx31c9S
7VP/K6ymy8/ZNv2EtfvY0aSmwjEWvWmBImg+Nuh7iw2b2q+ekVvc1I1M0UlFinOz
SYhca/GAGrId54aYVUYYRII1vHzi5BVLvf+h+j46RK6kUrBBq1dibR98jWG5Dat9
Ny3lL7ccusv6TZuuHFJW7VhELOCIxPx0W9lBydeyCOeYgEJ4VafMX8WP2LWNySsB
QfTPPAl3AyfYoshuuoIG/l7P2LKBw+3F9U6YMZNWSDx33Yl6ZjF+nItbcI5Txjrh
ei7Zs8QonNva7DCCzHP1oWPYacR1CE0fc9NpmdghNtal1YK2VWIbGR0ysvJ+FZXB
QtJyxIY3C0tDIcnaNoHpmSUxlu/e1+VzpJGfTwJVOQl+3it/PDANcjStp9oPDZOH
SDOZ4bRyVAOPgph/AHQ9igl1kFBSoCv+SowuPxWLgpsP9cyL+NCOXDJLGA==
-----END CERTIFICATE-----
-----BEGIN CERTIFICATE-----
MIIFyzCCBLOgAwIBAgIQCgWbJfVLPYeUzGYxR3U4ozANBgkqhkiG9w0BAQsFADBh
MQswCQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMRkwFwYDVQQLExB3
d3cuZGlnaWNlcnQuY29tMSAwHgYDVQQDExdEaWdpQ2VydCBHbG9iYWwgUm9vdCBD
QTAeFw0yMjA1MDQwMDAwMDBaFw0zMTExMDkyMzU5NTlaMFwxCzAJBgNVBAYTAlVT
MRcwFQYDVQQKEw5EaWdpQ2VydCwgSW5jLjE0MDIGA1UEAxMrUmFwaWRTU0wgR2xv
YmFsIFRMUyBSU0E0MDk2IFNIQTI1NiAyMDIyIENBMTCCAiIwDQYJKoZIhvcNAQEB
BQADggIPADCCAgoCggIBAKY5PJhwCX2UyBb1nelu9APen53D5+C40T+BOZfSFaB0
v0WJM3BGMsuiHZX2IHtwnjUhLL25d8tgLASaUNHCBNKKUlUGRXGztuDIeXb48d64
k7Gk7u7mMRSrj+yuLSWOKnK6OGKe9+s6oaVIjHXY+QX8p2I2S3uew0bW3BFpkeAr
LBCU25iqeaoLEOGIa09DVojd3qc/RKqr4P11173R+7Ub05YYhuIcSv8e0d7qN1sO
1+lfoNMVfV9WcqPABmOasNJ+ol0hAC2PTgRLy/VZo1L0HRMr6j8cbR7q0nKwdbn4
Ar+ZMgCgCcG9zCMFsuXYl/rqobiyV+8U37dDScAebZTIF/xPEvHcmGi3xxH6g+dT
CjetOjJx8sdXUHKXGXC9ka33q7EzQIYlZISF7EkbT5dZHsO2DOMVLBdP1N1oUp0/
1f6fc8uTDduELoKBRzTTZ6OOBVHeZyFZMMdi6tA5s/jxmb74lqH1+jQ6nTU2/Mma
hGNxUuJpyhUHezgBA6sto5lNeyqc+3Cr5ehFQzUuwNsJaWbDdQk1v7lqRaqOlYjn
iomOl36J5txTs0wL7etCeMRfyPsmc+8HmH77IYVMUOcPJb+0gNuSmAkvf5QXbgPI
Zursn/UYnP9obhNbHc/9LYdQkB7CXyX9mPexnDNO7pggNA2jpbEarLmZGi4grMmf
AgMBAAGjggGCMIIBfjASBgNVHRMBAf8ECDAGAQH/AgEAMB0GA1UdDgQWBBTwnIX9
op99j8lou9XUiU0dvtOQ/zAfBgNVHSMEGDAWgBQD3lA1VtFMu2bwo+IbG8OXsj3R
VTAOBgNVHQ8BAf8EBAMCAYYwHQYDVR0lBBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMC
MHYGCCsGAQUFBwEBBGowaDAkBggrBgEFBQcwAYYYaHR0cDovL29jc3AuZGlnaWNl
cnQuY29tMEAGCCsGAQUFBzAChjRodHRwOi8vY2FjZXJ0cy5kaWdpY2VydC5jb20v
RGlnaUNlcnRHbG9iYWxSb290Q0EuY3J0MEIGA1UdHwQ7MDkwN6A1oDOGMWh0dHA6
Ly9jcmwzLmRpZ2ljZXJ0LmNvbS9EaWdpQ2VydEdsb2JhbFJvb3RDQS5jcmwwPQYD
VR0gBDYwNDALBglghkgBhv1sAgEwBwYFZ4EMAQEwCAYGZ4EMAQIBMAgGBmeBDAEC
AjAIBgZngQwBAgMwDQYJKoZIhvcNAQELBQADggEBAAfjh/s1f5dDdfm0sNm74/dW
MbbsxfYV1LoTpFt+3MSUWvSbiPQfUkoV57b5rutRJvnPP9mSlpFwcZ3e1nSUbi2o
ITGA7RCOj23I1F4zk0YJm42qAwJIqOVenR3XtyQ2VR82qhC6xslxtNf7f2Ndx2G7
Mem4wpFhyPDT2P6UJ2MnrD+FC//ZKH5/ERo96ghz8VqNlmL5RXo8Ks9rMr/Ad9xw
Y4hyRvAz5920myUffwdUqc0SvPlFnahsZg15uT5HkK48tHR0TLuLH8aRpzh4KJ/Y
p0sARNb+9i1R4Fg5zPNvHs2BbIve0vkwxAy+R4727qYzl3027w9jEFC6HMXRaDc=
-----END CERTIFICATE-----";

$certificado_key = "-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAoCEH14T6dIzVbCiVuZ4sJBoXdB2Rkvl8zpxBOBkI6hcU0T9w
QX+g9zru9sw1p8s+cYCPsKZhuODnQcS5AFxvZfNecNckZ9pn2tv4Ppxa/QCjXweM
/duvJ1cZUMHJslh0Fj1QdUNhu6xTZbKLTla903hqBYdA+iwttJUaGRuS+pBgemCt
gOZn+sOSQaACqtjLFJRhXjx8fXZdlc2YhRbaEylGJvG50d7+JcEves3+gpsvQJsb
ycguzHGASps2H3IDjg8lwjn9gjDFvPHRlwxKh7BnQMu44QxqSNQNRUsxMZAU4KyA
ymLZ4Z29yzmgJi0iDI4UlDjow0hdZcKP5rHMIQIDAQABAoIBADYKr8DWykgnd6fn
EpDwhukwPRYdHJJDzRFVvtUV7eJDI+1ywYn5bvPBWgDE7p7QgyR/RP9TR8vDa4jQ
wbcTey0nM4pVsZ2zIjXE40UOM88LNhfOTpEmYTiftpWAsXeVTqhqzQqmUQerowHB
fi5ULACAtRdkjFNiMZKud35dxf71zk1CIkcivl7hzbDsIvgn/MdqZven0+YU7j9e
nVH7hRSyElqVloys2cXYc/TjQVhzzaMTzTuW2IotcKbtLAL2AvavsJxW0HC08ff/
Nc13rMxR1d1mYJLNjrE0Sd1W3o8xlSpcbNXAiLpYkQ/2XdIGAUVkx1jQ7bBfhhBV
oXdJpgECgYEAzQbp/epzsXOdmEUb1eedH1MyzktFUonui2tJyQDnYs5slylD7xi6
+w+O85zBIRT2h+7FTwvuXBWKRTSoSSIoagDcUMAY4PwFWxcHjAcd6DgEmJIZWsEy
VlFSfP4Ij+1TQ+o21I2iOlvd+2yeFjwXmyZVTaZ5rhrpYTLYySvjUtECgYEAx/CN
rYNE7kwSOWHFJ3VNzv6SgZK2laO0Zrrn2cmTOVIIvNOAu02fXZbsPCEwsjyIIi6c
Cl7BcjgqQmtqXIpyeyY/05iOLa7Hf+M41xzRxPKDkrCvSgp79jZL/EVOqxqdY08B
9mFPsukoPE9h5rxSRuaFqwQm0CjDGP9RjboxGFECgYEAqmYIq2Pj66OvYxJs5Aav
a412OEYOw16nx2/PzyLVLCVr0uYU8+6V2HtBz+6EBL6rdqZXji7YV+f/Fy2Af+fH
tvIoKWS+SJ6sxNwLBbIUhR+pkjQ6plbTQzIrYH6xFw2jmlpaX6WnIuGfSIspiElB
RU1CsFqf3re3J3Ve/zNep0ECgYAmiNdVhMJJR0IP6ycLZtFbPrdP383u40FGt2ku
EWqdlpD2i7D46In5iLf5EtCG2aHHLMKIQSD5eZeze25hbZGI6KNOjc2BQnlSzaFL
3FMVqUPwhrsSAxlHJ8nXUihKU/PXiweuy6yHp+ZIUWhmBw+4eH90qXUtk12euL6o
GSWTMQKBgQCTLkUN0eccZULPs96XWmOaJipVrN6lB9KChSvHT4Qb5IqAOYSRrH+6
+gF8IofU0YKnbey2XKT0ODapPqnW9Nf91TTocIN+Yqj92eStbcI2y/LIMAo0Vuij
DwLsXzGELcwGj3P4P9orjhY0lxReggtFfEUV9W7IzbBkBFVScNA7DQ==
-----END RSA PRIVATE KEY-----";

mysqli_query($db, "CREATE TABLE `pais` (
    `codigo` int(10) unsigned NOT NULL DEFAULT 0,
    `fone` int(10) unsigned DEFAULT NULL,
    `iso` varchar(45) DEFAULT NULL,
    `iso3` varchar(45) DEFAULT NULL,
    `nome` varchar(100) DEFAULT NULL,
    `nomeFormal` varchar(250) DEFAULT NULL,
    PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

mysqli_query($db, "CREATE TABLE `userGrupoDispositivos` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `idUser` int(10) unsigned NOT NULL DEFAULT 0,
    `idGrupo` int(10) unsigned NOT NULL DEFAULT 1 COMMENT '1 = aberto, 2 = fechado',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `historico` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tipo` char(1) NOT NULL DEFAULT '',
    `descricao` text NOT NULL DEFAULT '',
    `data` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `idUsuario` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `grupoUser` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `nome` varchar(45) NOT NULL DEFAULT '',
    `dns` int(10) unsigned NOT NULL DEFAULT 0,
    `dispositivos` int(10) unsigned NOT NULL DEFAULT 0,
    `olt` int(10) unsigned NOT NULL DEFAULT 0,
    `concentradora` int(10) unsigned NOT NULL DEFAULT 0,
    `pconcentradora` varchar(255) DEFAULT NULL,
    `polt` varchar(255) DEFAULT NULL,
    `auth_onu_total` varchar(10) NOT NULL DEFAULT '',
    `auth_onu_mes` varchar(10) NOT NULL DEFAULT '',
    `pdispositivo` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Sondas` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(250) DEFAULT NULL,
    `tag` varchar(80) DEFAULT NULL,
    `alertar` int(1) NOT NULL DEFAULT 1,
    `prioridade` int(1) NOT NULL DEFAULT 1,
    `tipo` int(1) NOT NULL DEFAULT 1,
    `peso` int(1) NOT NULL DEFAULT 1,
    `media` int(1) NOT NULL DEFAULT 1,
    `maxSet` varchar(10) DEFAULT NULL,
    `minSet` varchar(10) DEFAULT NULL,
    `maxPer` varchar(10) DEFAULT NULL,
    `minPer` varchar(10) DEFAULT NULL,
    `qtdCol` int(1) NOT NULL DEFAULT 1,
    `un1` varchar(10) DEFAULT NULL,
    `un2` varchar(10) DEFAULT NULL,
    `un3` varchar(10) DEFAULT NULL,
    `un4` varchar(10) DEFAULT NULL,
    `Col1` varchar(100) DEFAULT NULL,
    `Col2` varchar(100) DEFAULT NULL,
    `Col3` varchar(100) DEFAULT NULL,
    `Col4` varchar(100) DEFAULT NULL,
    `descr` varchar(250) DEFAULT NULL,
    `link` varchar(150) DEFAULT NULL,
    `exec` int(1) NOT NULL DEFAULT 1,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `Sondastag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dispAuxRaiz` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `a1` int(10) unsigned NOT NULL DEFAULT 1,
    `a2` int(10) unsigned NOT NULL DEFAULT 1,
    `a3` int(10) unsigned NOT NULL DEFAULT 1,
    `a4` int(10) unsigned NOT NULL DEFAULT 1,
    `idUsuario` int(10) unsigned NOT NULL DEFAULT 0,
    `a5` int(10) unsigned NOT NULL DEFAULT 0,
    `a6` int(10) unsigned NOT NULL DEFAULT 0,
    `idGrupo` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dispAux` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `tipo` int(10) unsigned NOT NULL DEFAULT 0,
    `idSensor` int(10) unsigned NOT NULL DEFAULT 0,
    `vis` int(10) unsigned NOT NULL DEFAULT 0,
    `user` int(10) unsigned NOT NULL DEFAULT 0,
    `idGrupo` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dnsAutoritativo` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `dominio` varchar(250) NOT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicoDom` (`dominio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dnsAutoritativoAdicional` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idDom` int(10) DEFAULT NULL,
    `valor` varchar(250) NOT NULL,
    `tipo` varchar(10) DEFAULT 'A',
    `ip` varchar(500) NOT NULL,
    `comment` varchar(250) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dnsReverso` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `subdom` int(1) DEFAULT NULL,
    `idDom` int(10) DEFAULT NULL,
    `ip` varchar(250) NOT NULL,
    `valor` varchar(250) NOT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dnsReversoAuxiliar` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `arpa` varchar(250) NOT NULL,
    `ip` varchar(250) NOT NULL,
    `reverso` varchar(400) NOT NULL,
    `idDom` int(10) DEFAULT NULL,
    `dominio` varchar(250) NOT NULL,
    `atualiza` varchar(10) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicoIPrev` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `GrupoMonitor` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `Nome` varchar(250) DEFAULT NULL,
    `idGrupoPai` int(11) DEFAULT NULL,
    `autoscan` int(2) DEFAULT NULL,
    `repetir` varchar(10) DEFAULT NULL,
    `ignorar` varchar(10) DEFAULT NULL,
    `baseIP` varchar(250) DEFAULT NULL,
    `status` int(2) DEFAULT NULL,
    `abrefecha` int(2) DEFAULT NULL,
    `autosensor` varchar(2) DEFAULT NULL,
    `modelo_auto` varchar(2) DEFAULT NULL,
    `ativasnmp` varchar(2) DEFAULT '1',
    `comunidadesnmp_g` varchar(200) DEFAULT 'public',
    `portasnmp_g` varchar(10) DEFAULT '161',
    `versaosnmp_g` varchar(2) DEFAULT '2',
    `nivelsegsnmp_g` varchar(20) DEFAULT 'AuthNoPriv',
    `protocoloauthsnmp_g` varchar(5) DEFAULT 'MD5',
    `protocolocripsnmp_g` varchar(5) DEFAULT 'AES',
    `authsnmp_g` varchar(50) DEFAULT NULL,
    `criptosnmp_g` varchar(50) DEFAULT NULL,
    `ativaWHATSAPP` int(2) NOT NULL DEFAULT '1',
    `prioridadewhats` int(2) NOT NULL DEFAULT '1',
    `ativaTELEGRAM` int(2) DEFAULT NULL,
    `chat_id` varchar(45) DEFAULT NULL,
    `token` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `ordem` int(11) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Dispositivos` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `Nome` varchar(250) DEFAULT NULL,
    `ip` varchar(200) DEFAULT NULL,
    `snmpcomunit` varchar(200) DEFAULT 'public',
    `portasnmp_d` varchar(10) DEFAULT '161',
    `versaosnmp_d` varchar(2) DEFAULT '1',
    `nivelsegsnmp_d` varchar(20) DEFAULT 'AuthNoPriv',
    `protocoloauthsnmp_d` varchar(5) DEFAULT 'MD5',
    `protocolocripsnmp_d` varchar(5) DEFAULT 'AES',
    `authsnmp_d` varchar(50) DEFAULT NULL,
    `criptosnmp_d` varchar(50) DEFAULT NULL,
    `sshport` varchar(10) DEFAULT NULL,
    `sshuser` varchar(200) DEFAULT NULL,
    `sshsenha` varchar(200) DEFAULT NULL,
    `idGrupoPai` varchar(10) DEFAULT NULL,
    `HerdarPai` varchar(2) DEFAULT '1',
    `HerdarPaiSSH` varchar(2) DEFAULT '1',
    `Link` int(3) DEFAULT '443',
    `portaLink` varchar(10) DEFAULT NULL,
    `backupDisp` int(2) NOT NULL DEFAULT '0',
    `backupData` varchar(100) DEFAULT NULL,
    `equipamento` int(2) NOT NULL DEFAULT '0',
    `backupExec` int(2) NOT NULL DEFAULT '0',
    `inf` text,
    `infSigilosas` text,
    `UltimoEquipamento` int(2) DEFAULT NULL,
    `ordem` int(11) DEFAULT NULL,
    `ativa_auto` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `dispositivos_auto` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `idDisp` int(10) unsigned DEFAULT NULL,
    `tag` varchar(45) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `tag` varchar(150) NOT NULL,
    `nome` varchar(250) NOT NULL,
    `nomeReal` varchar(250) DEFAULT NULL,
    `alias` varchar(250) DEFAULT NULL,
    `descr` varchar(250) DEFAULT NULL,
    `mac` varchar(50) DEFAULT NULL,
    `valor` varchar(250) DEFAULT NULL,
    `idDispositivo` int(10) DEFAULT NULL,
    `multiplicar` int(10) DEFAULT NULL,
    `dividir` int(10) DEFAULT NULL,
    `pausar` int(1) NOT NULL DEFAULT '0',
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    `media1` varchar(50) DEFAULT NULL,
    `media2` varchar(50) DEFAULT NULL,
    `media3` varchar(50) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    `status` int(1) NOT NULL DEFAULT '0',
    `erro` varchar(2) NOT NULL DEFAULT '1',
    `unidade` varchar(30) DEFAULT NULL,
    `host` varchar(200) DEFAULT NULL,
    `usuario` varchar(200) DEFAULT NULL,
    `senha` varchar(200) DEFAULT NULL,
    `porta` int(11) DEFAULT NULL,
    `comandoSQL` varchar(1000) DEFAULT NULL,
    `banco` varchar(200) DEFAULT NULL,
    `ifSpeed` varchar(50) DEFAULT NULL,
    `ifSpeedAlert` varchar(50) DEFAULT NULL,
    `ordem` integer(11) DEFAULT NULL,
    `cronograma` varchar(10) NOT NULL DEFAULT '1m',
    `adicionais` varchar(20) DEFAULT NULL,
    `display` varchar(2) NOT NULL DEFAULT '1',
    `text` text DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `modelo_sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(250) DEFAULT NULL,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `tags_modelo_sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `id_modelo` int(10) DEFAULT NULL,
    `tag` varchar(50) DEFAULT NULL,
    `valor` varchar(50) DEFAULT NULL,
    `nome` varchar(255) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `geramedias` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `hora` varchar(50) DEFAULT NULL,
    `idSensor` int(10) DEFAULT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `medias` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idSensor` int(10) DEFAULT NULL,
    `hora` varchar(50) DEFAULT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `whats` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idcontato` varchar(150) DEFAULT NULL,
    `nome` varchar(250) DEFAULT NULL,
    `idGrupo` int(10) unsigned DEFAULT NULL,
    `tipo` VARCHAR(45) DEFAULT NULL,
    `idApi` int(10) unsigned DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `cronograma` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(50) DEFAULT NULL,
    `valor` varchar(50) DEFAULT NULL,
    `recomendado` varchar(1) NOT NULL DEFAULT '0',
    `ordem` varchar(50) NOT NULL DEFAULT '0',
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicoCron` (`valor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `rotas_vpn` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `valor` varchar(50) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Log2h` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idSensor` int(10) DEFAULT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicHorLog2h` (`data`,`idSensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Log24h` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idSensor` int(10) NOT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicHorLog24h` (`data`,`idSensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Log30d` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idSensor` int(10) DEFAULT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicHorLog30d` (`data`,`idSensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Log1a` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idSensor` int(10) DEFAULT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicHorLog1a` (`data`,`idSensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `percentil95` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idSensor` int(10) DEFAULT NULL,
    `valor1` varchar(50) DEFAULT NULL,
    `valor2` varchar(50) DEFAULT NULL,
    `valor3` varchar(50) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `ResumoSensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `off` varchar(5) NOT NULL DEFAULT '0',
    `pausado` varchar(5) NOT NULL DEFAULT '0',
    `ok` varchar(5) NOT NULL DEFAULT '0',
    `alerta` varchar(5) NOT NULL DEFAULT '0',
    `erro` varchar(5) NOT NULL DEFAULT '0',
    `total` varchar(5) NOT NULL DEFAULT '0',
    `novos` varchar(5) NOT NULL DEFAULT '0',
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Logalertas` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idSensor` int(10) DEFAULT NULL,
    `idDispositivo` int(10) DEFAULT NULL,
    `nome` varchar(60) DEFAULT NULL,
    `tag` varchar(150) DEFAULT NULL,
    `valor` varchar(250) DEFAULT NULL,
    `mensagem` varchar(200) DEFAULT NULL,
    `resolvido` varchar(5) NOT NULL DEFAULT '0',
    `dataresolvido` varchar(60) DEFAULT NULL,
    `enviado` varchar(5) NOT NULL DEFAULT '0',
    `enviadoSMTP` varchar(5) NOT NULL DEFAULT '0',
    `enviadoWHATS` varchar(5) NOT NULL DEFAULT '0',
    `enviadopush` varchar(5) NOT NULL DEFAULT '0',
    `tipo` varchar(5) DEFAULT NULL,
    `statusAlert` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicLog` (`idSensor`,`data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `ServDNS` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `Primario` varchar(100) NOT NULL,
    `Secundario` varchar(100) NOT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `alertaSensor` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idSensor` int(10) unsigned NOT NULL DEFAULT '0',
    `hora` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `login` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `usuario` varchar(45) NOT NULL DEFAULT '',
    `senha` varchar(45) NOT NULL DEFAULT '',
    `nome` varchar(200) DEFAULT NULL,
    `tipo` varchar(50) DEFAULT NULL,
    `device_uid` varchar(45) DEFAULT NULL,
    `server_address` varchar(45) DEFAULT NULL,
    `qr_server_token` varchar(255) DEFAULT NULL,
    `device_name` varchar(45) DEFAULT NULL,
    `device_os` varchar(45) DEFAULT NULL,
    `device_push_token` varchar(255) DEFAULT NULL,
    `tokenApp` varchar(45) DEFAULT NULL,
    `privDNS` int(10) unsigned NOT NULL DEFAULT 1,
    `privDispositivos` int(10) unsigned NOT NULL DEFAULT 1,
    `privOLT` int(10) unsigned NOT NULL DEFAULT 1,
    `privConcentradora` int(10) unsigned NOT NULL DEFAULT 1,
    `foto` longblob DEFAULT NULL,
    `idGrupo` int(10) unsigned NOT NULL DEFAULT 1,
    `telefone` varchar(45) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `ddi` int(10) NOT NULL DEFAULT 76,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `rbl` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `link` varchar(250) NOT NULL,
    `ativo` int(2) NOT NULL DEFAULT '1',
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `rbl-link` (`link`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `ServUpdates` (
    `ip` varchar(100) DEFAULT '177.129.163.2',
    `porta` varchar(50) DEFAULT '2222',
    `versaoN` varchar(15) DEFAULT NULL,
    `dataN` varchar(15) DEFAULT NULL,
    `debugN` varchar(5) DEFAULT NULL,
    `shellN` varchar(5) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `telegrampadrao` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `chat_id` varchar(100) DEFAULT NULL,
    `token` varchar(200) DEFAULT NULL,
    `inicio` varchar(15) DEFAULT NULL,
    `fim` varchar(15) DEFAULT NULL,
    `prioridade` varchar(2) DEFAULT '2',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `telegramdisp` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `chat_id` varchar(100) DEFAULT NULL,
    `token` varchar(200) DEFAULT NULL,
    `inicio` varchar(15) DEFAULT NULL,
    `fim` varchar(15) DEFAULT NULL,
    `prioridade` varchar(2) DEFAULT '2',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `telegramolt` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `chat_id` varchar(100) DEFAULT NULL,
    `token` varchar(200) DEFAULT NULL,
    `inicio` varchar(15) DEFAULT NULL,
    `fim` varchar(15) DEFAULT NULL,
    `prioridade` varchar(2) DEFAULT '2',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `userGrupoConcentradora` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idConcentradora` int(10) unsigned NOT NULL DEFAULT '0',
    `idUsuario` int(10) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `userGrupoMonitor` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idGrupo` int(10) unsigned NOT NULL DEFAULT '0',
    `idUsuario` int(10) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `userGrupoOlt` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idOlt` int(10) unsigned NOT NULL DEFAULT '0',
    `idUsuario` int(10) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `olts` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `datasinc` varchar(50) DEFAULT NULL,
    `temposinc` varchar(50) DEFAULT NULL,
    `nome` varchar(250) DEFAULT NULL,
    `alertaOLT` varchar(5) NOT NULL DEFAULT '-26',
    `HerdarPai` varchar(2) DEFAULT '1',
    `HerdarPaiSSH` varchar(2) DEFAULT '1',
    `snmp` varchar(50) NOT NULL DEFAULT 'public',
    `portasnmp` varchar(10) DEFAULT '161',
    `versaosnmp` varchar(2) DEFAULT '2',
    `ip` varchar(18) DEFAULT NULL,
    `marca` varchar(2) DEFAULT NULL,
    `gemport` varchar(10) DEFAULT NULL,
    `novo` int(2) NOT NULL DEFAULT '1',
    `ocronolt` int(1) NOT NULL DEFAULT '4',
    `tipo` varchar(2) NOT NULL DEFAULT '1',
    `porta` varchar(10) DEFAULT NULL,
    `login` varchar(150) DEFAULT NULL,
    `senha` varchar(150) DEFAULT NULL,
    `ativo` int(2) NOT NULL DEFAULT '1',
    `status` int(2) NOT NULL DEFAULT '0',
    `motivo` varchar(150) DEFAULT NULL,
    `adicionais` varchar(20) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `onus` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `datt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `datasinc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dataconnect` varchar(250) DEFAULT NULL,
    `idOLT` int(10) DEFAULT NULL,
    `idonu` int(10) DEFAULT NULL,
    `idpon` int(10) DEFAULT NULL,
    `idslot` int(10) DEFAULT NULL,
    `idinterface` varchar(200) DEFAULT NULL,
    `provisionamento` varchar(200) DEFAULT NULL,
    `descr` varchar(600) DEFAULT NULL,
    `login` varchar(600) DEFAULT NULL,
    `mac` varchar(100) NOT NULL,
    `ns` varchar(100) DEFAULT NULL,
    `type` varchar(70) DEFAULT NULL,
    `rxonu` varchar(10) DEFAULT NULL,
    `txonu` varchar(10) DEFAULT NULL,
    `oltrx` varchar(10) DEFAULT NULL,
    `voltagem` varchar(6) DEFAULT NULL,
    `temperatura` varchar(6) DEFAULT NULL,
    `distancia` varchar(10) DEFAULT NULL,
    `biascurrent` varchar(10) DEFAULT NULL,
    `stats` varchar(2) DEFAULT NULL,
    `log_auth` text NOT NULL DEFAULT '',
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicONU` (`idOLT`,`mac`),
    PRIMARY KEY(`id`, `mac`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `models_auth_onu` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(250) DEFAULT NULL,
    `marca` varchar(2) DEFAULT NULL,
    `modelo` varchar(2) DEFAULT NULL,
    `codigo` text NOT NULL DEFAULT '',
    UNIQUE KEY `id` (`id`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `provisionamento` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `datt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `idOLT` int(10) DEFAULT NULL,
    `marca` varchar(2) DEFAULT NULL,
    `idonu` varchar(70) DEFAULT NULL,
    `idpon` varchar(70) DEFAULT NULL,
    `idslot` varchar(70) DEFAULT NULL,
    `descr` varchar(300) DEFAULT NULL,
    `type` varchar(70) DEFAULT NULL,
    `sn` varchar(100) NOT NULL,
    `status` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `sn` (`sn`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `srvprofile` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idOLT` int(10) DEFAULT NULL,
    `idprofile` varchar(15) DEFAULT NULL,
    `nome` varchar(300) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicSrvprofile` (`idOLT`,`idprofile`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `lineprofile` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idOLT` int(10) DEFAULT NULL,
    `idprofile` varchar(15) DEFAULT NULL,
    `nome` varchar(300) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicLineprofile` (`idOLT`,`idprofile`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `vlan` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idOLT` int(10) DEFAULT NULL,
    `idvlan` varchar(15) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicVlan` (`idOLT`,`idvlan`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `rxonus` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `datt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `mac` varchar(100) NOT NULL,
    `rxonu` varchar(10) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `alertaonus` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `datt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `onu` varchar(100) NOT NULL,
    UNIQUE KEY `id` (`id`),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `configsRaviDNS` (
    `prefetchDNS` varchar(2) NOT NULL DEFAULT '1',
    `num_threads` varchar(2) NOT NULL DEFAULT '1',
    `cache_min_ttl` varchar(10) NOT NULL DEFAULT '3600',
    `cache_max_ttl` varchar(10) NOT NULL DEFAULT '14400',
    `rrset_cache_size` varchar(5) NOT NULL DEFAULT '50m',
    `msg_cache_size` varchar(5) NOT NULL DEFAULT '25m',
    `num_queries_per_thread` varchar(5) NOT NULL DEFAULT '4000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `accessControlDNS` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `ip` varchar(25) DEFAULT NULL,
    `tipo` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `redirectControlDNS` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `dominio` varchar(250) DEFAULT NULL,
    `ip` varchar(25) NOT NULL DEFAULT '0.0.0.0',
    `ativo` varchar(2) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `adicionalDNS` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(250) DEFAULT NULL,
    `ip` varchar(25) NOT NULL DEFAULT '127.0.0.1',
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `LogDNS` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total_num_queries` varchar(50) DEFAULT NULL,
    `total_num_cachehits` varchar(50) DEFAULT NULL,
    `total_num_cachemiss` varchar(50) DEFAULT NULL,
    `total_num_prefetch` varchar(50) DEFAULT NULL,
    `total_num_recursivereplies` varchar(50) DEFAULT NULL,
    `up` varchar(50) NOT NULL DEFAULT '0',
    `A` varchar(50) NOT NULL DEFAULT '0',
    `AAAA` varchar(50) NOT NULL DEFAULT '0',
    `ANY` varchar(50) NOT NULL DEFAULT '0',
    `CNAME` varchar(50) NOT NULL DEFAULT '0',
    `msg` varchar(50) NOT NULL DEFAULT '0',
    `rrset` varchar(50) NOT NULL DEFAULT '0',
    `PTR` varchar(50) NOT NULL DEFAULT '0',
    `avg` varchar(50) NOT NULL DEFAULT '0',
    `max` varchar(50) NOT NULL DEFAULT '0',
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `alertasMensalidade` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idBoleto` int(10) DEFAULT NULL,
    `expira` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `texto` varchar(255) DEFAULT NULL,
    `boleto` varchar(255) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `concentradoras` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `datasinc` varchar(50) DEFAULT NULL,
    `temposinc` varchar(50) DEFAULT NULL,
    `nome` varchar(250) DEFAULT NULL,
    `alertaPPPoE` varchar(5) NOT NULL DEFAULT '20',
    `HerdarPai` varchar(2) DEFAULT '1',
    `HerdarPaiSSH` varchar(2) DEFAULT '1',
    `snmp` varchar(50) NOT NULL DEFAULT 'public',
    `portasnmp` varchar(10) DEFAULT '161',
    `versaosnmp` varchar(2) DEFAULT '2',
    `ip` varchar(18) DEFAULT NULL,
    `marca` varchar(2) DEFAULT '1',
    `tipo` varchar(2) NOT NULL DEFAULT '1',
    `porta` varchar(10) DEFAULT NULL,
    `login` varchar(150) DEFAULT NULL,
    `senha` varchar(150) DEFAULT NULL,
    `novo` int(2) NOT NULL DEFAULT '1',
    `ativo` varchar(2) DEFAULT NULL,
    `status` int(2) NOT NULL DEFAULT '0',
    `cron` int(1) NOT NULL DEFAULT '2',
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `usersPPPoE` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idC` int(10) DEFAULT NULL,
    `interface` varchar(200) DEFAULT NULL,
    `mac` varchar(100) DEFAULT NULL,
    `datasinc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dataconect` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `datadesconect` varchar(150) DEFAULT NULL,
    `uptimeconect` varchar(50) DEFAULT NULL,
    `ppoe` varchar(250) DEFAULT NULL,
    `ip` varchar(18) DEFAULT NULL,
    `vlan` varchar(18) DEFAULT NULL,
    `down` int(100) DEFAULT NULL,
    `up` int(100) DEFAULT NULL,
    `ping` varchar(10) DEFAULT NULL,
    `jitter` varchar(10) DEFAULT NULL,
    `linkdown` int(10) NOT NULL DEFAULT '0',
    `apoio` varchar(2) NOT NULL DEFAULT '0',
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicPPoE` (`idC`,`ppoe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `LogPPPoE` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idC` int(10) DEFAULT NULL,
    `idPPPoE` int(10) DEFAULT NULL,
    `ip` varchar(18) DEFAULT NULL,
    `vlan` varchar(18) DEFAULT NULL,
    `dataconect` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `datadesconect` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `down` int(100) DEFAULT NULL,
    `up` int(100) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicLogPPoE` (`idPPPoE`,`dataconect`,`datadesconect`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `PingPPPoE` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idC` int(10) DEFAULT NULL,
    `idPPPoE` int(10) DEFAULT NULL,
    `ip` varchar(18) DEFAULT NULL,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ping` varchar(10) DEFAULT NULL,
    `jitter` varchar(10) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `trafegoPPPoE` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idC` int(10) DEFAULT NULL,
    `idPPPoE` int(10) DEFAULT NULL,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `down` varchar(50) DEFAULT NULL,
    `up` varchar(50) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `unicdatPPoE` (`idPPPoE`,`data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `Mapas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `Mapas` longtext DEFAULT NULL,
    `idMapa` int(10) unsigned DEFAULT NULL,
    `idUsuario` int(11) DEFAULT NULL,
    `Nome` varchar(255) DEFAULT NULL,
    `bg` varchar(255) DEFAULT NULL,
    `wid` int(11) DEFAULT NULL,
    `hei` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `ComandoRemoto` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ativo` int(2) NOT NULL DEFAULT '1',
    `idSensor` int(10) DEFAULT NULL,
    `tipo` int(1) NOT NULL DEFAULT 1,
    `ip` varchar(18) DEFAULT NULL,
    `porta` int(10) NOT NULL DEFAULT '23',
    `login` varchar(100) DEFAULT NULL,
    `senha` varchar(100) DEFAULT NULL,
    `statusAlert1` int(1) NOT NULL DEFAULT 1,
    `statusAlert2` int(1) NOT NULL DEFAULT 6,
    `command1` longtext DEFAULT NULL,
    `command2` longtext DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `api_whats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `descricao` varchar(150) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `models_alerta` (
    `tipo` varchar(4) DEFAULT NULL,
    `titulo` varchar(250) DEFAULT NULL,
    `mensagem` text NOT NULL DEFAULT '',
    UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `log_auth_onu` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `iduser` int(10) DEFAULT NULL,
    `idolt` int(10) DEFAULT NULL,
    `idslot` int(10) DEFAULT NULL,
    `idpon` int(10) DEFAULT NULL,
    `idonu` int(10) DEFAULT NULL,
    `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ns` varchar(150) DEFAULT NULL,
    `log_auth` text NOT NULL DEFAULT '',
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `grupos_disp` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) DEFAULT NULL,
    `foto` longblob DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `sensores_disp` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome_pt` varchar(40) DEFAULT NULL,
    `nome_en` varchar(40) DEFAULT NULL,
    `nome_es` varchar(40) DEFAULT NULL,
    `tag` varchar(40) DEFAULT NULL,
    `peso_cpu` int(1) NOT NULL DEFAULT 1,
    `descr_pt` varchar(150) DEFAULT NULL,
    `descr_en` varchar(150) DEFAULT NULL,
    `descr_es` varchar(150) DEFAULT NULL,
    `id_grupos` varchar(50) DEFAULT NULL,
    `protocolo` int(1) NOT NULL DEFAULT 1,
    `tipo` int(1) NOT NULL DEFAULT 1,
    `ativa_pesquisa` int(1) DEFAULT 1,
    `coluna_pesquisa` int(10) DEFAULT NULL,
    `valor_coluna_pesquisa` varchar(20) DEFAULT NULL,
    `coluna_valor` int(10) DEFAULT NULL,
    UNIQUE KEY `id` (`id`),
    UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `coletores_sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idsensor` int(10) DEFAULT NULL,
    `nome_pt` varchar(30) DEFAULT NULL,
    `nome_en` varchar(30) DEFAULT NULL,
    `nome_es` varchar(30) DEFAULT NULL,
    `oid_snmp` varchar(40) DEFAULT NULL,
    `valor` varchar(40) DEFAULT NULL,
    `estencao` varchar(20) DEFAULT NULL,
    `formato` int(1) NOT NULL DEFAULT 1,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `condicoes_sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idsensor` int(10) DEFAULT NULL,
    `descr_problema_pt` varchar(100) DEFAULT NULL,
    `descr_problema_en` varchar(100) DEFAULT NULL,
    `descr_problema_es` varchar(100) DEFAULT NULL,
    `descr_resolvido_pt` varchar(100) DEFAULT NULL,
    `descr_resolvido_en` varchar(100) DEFAULT NULL,
    `descr_resolvido_es` varchar(100) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `regra_condicoes_sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idsensor` int(10) DEFAULT NULL,
    `idcondicao` int(10) DEFAULT NULL,
    `idcoletor` int(10) DEFAULT NULL,
    `tipo_regra` int(1) NOT NULL DEFAULT 1,
    `formato` int(1) NOT NULL DEFAULT 1,
    `valor` varchar(20) DEFAULT NULL,
    `gravidade` int(1) NOT NULL DEFAULT 1,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `colunas_tabela_sensores` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `idsensor` int(10) DEFAULT NULL,
    `tipo_coluna` int(1) DEFAULT NULL,
    `sel_nome_dinamico` int(1) DEFAULT NULL,
    `editavel` int(1) DEFAULT NULL,
    `nome_dinamico` varchar(30) DEFAULT NULL, 
    `nome_pt` varchar(30) DEFAULT NULL,
    `nome_en` varchar(30) DEFAULT NULL,
    `nome_es` varchar(30) DEFAULT NULL,
    `oid_snmp` varchar(40) DEFAULT NULL,
    `filtro` varchar(100) DEFAULT NULL,
    `valor` varchar(40) DEFAULT NULL,
    `estencao` varchar(20) DEFAULT NULL,
    `tamanho` varchar(20) DEFAULT NULL,
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "CREATE TABLE `system` (
    `userpadrao` varchar(50) DEFAULT NULL,
    `senhapadrao` varchar(50) DEFAULT NULL,
    `portapadrao` varchar(50) DEFAULT NULL,
    `snmppadrao` varchar(200) DEFAULT 'public',
    `portasnmppadrao` varchar(10) DEFAULT '161',
    `versaosnmppadrao` varchar(2) DEFAULT '2',
    `nivelsegsnmppadrao` varchar(20) DEFAULT 'AuthNoPriv',
    `protocoloauthsnmppadrao` varchar(5) DEFAULT 'MD5',
    `protocolocripsnmppadrao` varchar(5) DEFAULT 'AES',
    `authsnmppadrao` varchar(50) DEFAULT NULL,
    `criptosnmppadrao` varchar(50) DEFAULT NULL,
    `userpadrao_olt` varchar(50) DEFAULT NULL,
    `senhapadrao_olt` varchar(50) DEFAULT NULL,
    `portapadrao_olt` varchar(50) DEFAULT NULL,
    `snmppadrao_olt` varchar(200) DEFAULT 'public',
    `portasnmppadrao_olt` varchar(10) DEFAULT '161',
    `versaosnmppadrao_olt` varchar(2) DEFAULT '2',
    `snmppadrao_pppoe` varchar(200) DEFAULT 'public',
    `portasnmppadrao_pppoe` varchar(10) DEFAULT '161',
    `versaosnmppadrao_pppoe` varchar(2) DEFAULT '2',
    `userpadrao_pppoe` varchar(50) DEFAULT NULL,
    `senhapadrao_pppoe` varchar(50) DEFAULT NULL,
    `portapadrao_pppoe` varchar(50) DEFAULT NULL,
    `qtdpiores_pppoe` varchar(4) DEFAULT '12',
    `diasoff_pppoe` varchar(2) NOT NULL DEFAULT '3',
    `dadoshistoricos_pppoe` varchar(4) DEFAULT '7d',
    `ativaPing_pppoe` varchar(5) NOT NULL DEFAULT '2',
    `ativaLinkDown_pppoe` varchar(5) NOT NULL DEFAULT '2',  
    `tamanhopacotes_pppoe` varchar(5) NOT NULL DEFAULT '32',
    `quantidadepacotes_pppoe` varchar(5) NOT NULL DEFAULT '5',
    `qtdpiores_olt` varchar(4) DEFAULT '12',
    `zoomsensor` varchar(2) DEFAULT '2',
    `mostraip` varchar(2) DEFAULT '1',
    `widthSensor` varchar(10) DEFAULT '150px',
    `alinhasensores` varchar(15) DEFAULT 'inline-block',
    `iconaddsensor` varchar(2) DEFAULT '1',
    `GrafDisp` varchar(5) DEFAULT NULL,
    `ativaSMTP` varchar(10) DEFAULT NULL,
    `userSMTP` varchar(250) DEFAULT NULL,
    `senhaSMTP` varchar(250) DEFAULT NULL,
    `servidorSMTP` varchar(250) DEFAULT NULL,
    `SMTPtls` int(1) NOT NULL DEFAULT '0',
    `portaSMTP` varchar(5) DEFAULT NULL,
    `emailSMTP` varchar(250) DEFAULT NULL,
    `prioridadeSMTP` varchar(5) NOT NULL DEFAULT '2',
    `telegramolt` int(1) NOT NULL DEFAULT '1',
    `ativaWHATS` int(2) NOT NULL DEFAULT '2',
    `prioridadewhats` int(2) NOT NULL DEFAULT '1',
    `telefone_conectado_whats` varchar(250) DEFAULT NULL,
    `ativaTELEGRAM` int(2) NOT NULL DEFAULT '2',
    `ativaTELEGRAMdisp` int(2) NOT NULL DEFAULT '1',
    `ativaTELEGRAMolt` int(2) NOT NULL DEFAULT '3',
    `ativaSOM` varchar(5) DEFAULT NULL,
    `numSOM` varchar(10) DEFAULT NULL,
    `tokenRAVI` varchar(300) DEFAULT NULL,
    `ativaDNS` integer(2) NOT NULL DEFAULT '2',
    `ip_config_avancado` integer(2) NOT NULL DEFAULT '0',
    `ativaVPN` integer(2) NOT NULL DEFAULT '0',
    `datavpn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ipvpn` varchar(250) DEFAULT NULL,
    `uservpn` varchar(250) DEFAULT NULL,
    `senhavpn` varchar(250) DEFAULT NULL,
    `ativaSSL` integer(2) NOT NULL DEFAULT '3',
    `certificado_crt` text NOT NULL DEFAULT '$certificado_crt',
    `certificado_key` text NOT NULL DEFAULT '$certificado_key',
    `certificado_crt_cli` text NOT NULL DEFAULT '',
    `certificado_key_cli` text NOT NULL DEFAULT '',
    `subdom_ssl` varchar(150) DEFAULT NULL,
    `subdom_ssl_ip` varchar(150) DEFAULT NULL,
    `subdom_ssl_ip6` varchar(150) DEFAULT NULL,
    `api_olt` integer(2) NOT NULL DEFAULT '2',
    `api_dns` integer(2) NOT NULL DEFAULT '2',
    `api_whats` int(2) NOT NULL DEFAULT '0',
    `updateRede` varchar(5) DEFAULT NULL,
    `registroTipo` varchar(2) NOT NULL DEFAULT 'G',
    `registroPlano` varchar(10) DEFAULT NULL,
    `atualizacaoauto` varchar(5) DEFAULT NULL,
    `ativoRAVI` varchar(5) DEFAULT NULL,
    `falhasRegistro` varchar(5) DEFAULT NULL,
    `versao` varchar(50) NOT NULL DEFAULT '".$versao."',
    `versaoData` varchar(50) NOT NULL DEFAULT '".$versaoData."',
    `versaoDescr` varchar(100) NOT NULL DEFAULT 'VersÃ£o ".$versao." do sistema de monitoramento Ravi',
    `versaoNova` varchar(5) DEFAULT NULL,
    `debug` varchar(5) NOT NULL DEFAULT '".$versaoDebug."',
    `dataCadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `backupUsuarios` integer(2) NOT NULL DEFAULT '1',
    `backupConfig` integer(2) NOT NULL DEFAULT '1',
    `backupHistoricos` integer(2) NOT NULL DEFAULT '1',
    `backupConcentradoras` integer(2) NOT NULL DEFAULT '1',
    `backupOLTs` integer(2) NOT NULL DEFAULT '2',
    `backupDNS` integer(2) NOT NULL DEFAULT '1',
    `padraotrafegodisp` int(1) NOT NULL DEFAULT '2',
    `padraotrafegocon` int(1) NOT NULL DEFAULT '2',
    `historicotrafegocon` int(1) NOT NULL DEFAULT '1',
    `ativaWHATSdisp` int(11) unsigned DEFAULT NULL,
    `ativaWHATSolt` int(11) unsigned DEFAULT NULL,
    `prioridadewhatsdisp` int(2) NOT NULL DEFAULT '1',
    `prioridadewhatsolt` int(2) NOT NULL DEFAULT '1',
    `ativaOokla` integer(2) NOT NULL DEFAULT '0',
    `foto` longblob DEFAULT NULL,
    `nomeApresentacao` varchar(45) DEFAULT NULL,
    `timezone` varchar(50) NOT NULL DEFAULT 'America/Sao_Paulo',
    `linguagem` int(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "TRUNCATE TABLE configsRaviDNS;");
mysqli_query($db, "TRUNCATE TABLE accessControlDNS;");
mysqli_query($db, "TRUNCATE TABLE adicionalDNS;");
mysqli_query($db, "TRUNCATE TABLE ServUpdates;");
mysqli_query($db, "TRUNCATE TABLE ServDNS;");
mysqli_query($db, "TRUNCATE TABLE cronograma;");
mysqli_query($db, "TRUNCATE TABLE rbl;");
mysqli_query($db, "TRUNCATE TABLE system;");
mysqli_query($db, "TRUNCATE TABLE grupoUser;");
mysqli_query($db, "TRUNCATE TABLE Sondas;");
mysqli_query($db, "TRUNCATE TABLE models_auth_onu;");

mysqli_query($db, "INSERT INTO configsRaviDNS (num_threads) VALUES ('".$cores_cpu."');");
mysqli_query($db, "INSERT INTO accessControlDNS (ip, tipo) VALUES ('10.0.0.0/8', '1');");
mysqli_query($db, "INSERT INTO accessControlDNS (ip, tipo) VALUES ('172.16.0.0/12', '1');");
mysqli_query($db, "INSERT INTO accessControlDNS (ip, tipo) VALUES ('192.168.0.0/16', '1');");
mysqli_query($db, "INSERT INTO accessControlDNS (ip, tipo) VALUES ('169.254.0.0/16', '1');");
mysqli_query($db, "INSERT INTO accessControlDNS (ip, tipo) VALUES ('100.64.0.0/10', '1');");
mysqli_query($db, "INSERT INTO adicionalDNS (nome, ip) VALUES ('Google', '8.8.8.8');");
mysqli_query($db, "INSERT INTO adicionalDNS (nome, ip) VALUES ('Cloudflare', '1.1.1.1');");
mysqli_query($db, "INSERT INTO ServUpdates (versaoN, dataN) VALUES ('".$versao."', '".$versaoData."');");
mysqli_query($db, "INSERT INTO ServDNS (nome, Primario, Secundario) VALUES ('Google', '8.8.8.8', '8.8.4.4');");
mysqli_query($db, "INSERT INTO ServDNS (nome, Primario, Secundario) VALUES ('OpenDNS', '208.67.222.222', '208.67.220.220');");
mysqli_query($db, "INSERT INTO ServDNS (nome, Primario, Secundario) VALUES ('GigaDNS', '189.38.95.95', '189.38.95.96');");
mysqli_query($db, "INSERT INTO ServDNS (nome, Primario, Secundario) VALUES ('Level 3', '209.244.0.3', '209.244.0.4');");
mysqli_query($db, "INSERT INTO ServDNS (nome, Primario, Secundario) VALUES ('Cloudflare', '1.1.1.1', '1.0.0.1');");

$problema_resolvido = "Dispositivo: *#nome_dispositivo* (#nome_grupo)
IP: *#ip*
Nome: *#nome_sensor*
Status: *#solucao*
Data atual: *#data_atual*
Tempo decorrido: *#tempo_decorrido*";

$problema_encontrado = "Dispositivo: *#nome_dispositivo* (#nome_grupo)
Sensor: *#nome_sensor*
IP: #ip
Problema: *#problema*
Data: *#data*";

mysqli_query($db, "INSERT INTO models_alerta (tipo, titulo, mensagem) VALUES ('up', '*PROBLEMA RESOLVIDO*', '".$problema_resolvido."');");
mysqli_query($db, "INSERT INTO models_alerta (tipo, titulo, mensagem) VALUES ('down', '*PROBLEMA ENCONTRADO*', '".$problema_encontrado."');");

mysqli_query($db, "INSERT INTO cronograma VALUES (1,'A cada 30 segundos','30s', '0', '30'),(2,'A cada minuto','1m', '1', '60'),(3,'A cada 5 minutos','5m', '0', '300'),(4,'A cada 15 minutos','15m', '0', '900'),(5,'A cada hora','1h', '0', '3600'),(6,'A cada dia','1d', '0', '86400'),(7,'A cada semana','7d', '0', '604800');");
mysqli_query($db, "INSERT INTO rbl VALUES (1,'Spamhaus SBL','sbl.spamhaus.org',1),(2,'Spamhaus DBL','dbl.spamhaus.org',1),(3,'Spamhaus PBL','pbl.spamhaus.org',1),(4,'Spamhaus XBL','xbl.spamhaus.org',1),(5,'Spamhaus Zen','zen.spamhaus.org',1),(6,'NiX Spam','ix.dnsbl.manitu.net',1),(7,'Spamcop','bl.spamcop.net',1),(8,'JunkMailFilter','hostkarma.junkemailfilter.com',1),(9,'Barracuda','b.barracudacentral.org',1),(10,'Spamrats ALL','all.spamrats.com',1),(11,'Deadbeef','bl.deadbeef.com',0),(12,'emailbasura','bl.emailbasura.org',0),(13,'mailspike','bl.mailspike.net',0),(14,'spamcannibal','bl.spamcannibal.org',0),(16,'spameatingmonkey','bl.spameatingmonkey.net',0),(17,'cymru','bogons.cymru.com',0),(18,'Spamlookup','bsb.spamlookup.net',0),(19,'Abuseat','cbl.abuseat.org',1),(20,'anti-spam.org','cbl.anti-spam.org.cn',0),(21,'anti-spam.org','cblless.anti-spam.org.cn',0),(22,'anti-spam.org','cblplus.anti-spam.org.cn',0),(23,'anti-spam.org','cdl.anti-spam.org.cn',0),(24,'mcafee','cidr.bl.mcafee.com',0),(25,'abuse.ch','combined.abuse.ch',0),(26,'njabl','combined.njabl.org',0),(27,'msrbl','combined.rbl.msrbl.net',0),(28,'wpbl','db.wpbl.info',0),(29,'mail-abuse','dialups.mail-abuse.org',0),(30,'Uceprotect 1','dnsbl-1.uceprotect.net',0),(31,'Uceprotect 2','dnsbl-2.uceprotect.net',0),(32,'Uceprotect 3','dnsbl-3.uceprotect.net',0),(33,'Abuse','dnsbl.abuse.ch',0),(34,'cyberlogic','dnsbl.cyberlogic.net',0),(35,'dronebl','dnsbl.dronebl.org',0),(36,'inps','dnsbl.inps.de',0),(37,'kempt','dnsbl.kempt.net',0),(38,'njabl','dnsbl.njabl.org',0),(39,'sorbs','dnsbl.sorbs.net',0),(40,'swinog','dnsrbl.swinog.ch',0),(41,'intelligence','dob.sibl.support-intelligence.net',0),(42,'abuse','drone.abuse.ch',0),(43,'dronebl','dsnbl.dronebl.org',0),(44,'rfc-ignorant','dsn.rfc-ignorant.org',0),(45,'aupads','duinv.aupads.org',0),(46,'blackhole','dul.blackhole.cantv.net',0),(47,'sorbs','dul.dnsbl.sorbs.net',0),(48,'dul.ru','dul.ru',0),(49,'Sorbs','dynablock.sorbs.net',0),(50,'Spamrats DYNA','dyna.spamrats.com',1),(51,'rbl','dyndns.rbl.jp',0),(52,'rothen','dynip.rothen.com',0),(53,'icm','forbidden.icm.edu.pl',0),(54,'spameatingmonkey','fresh.spameatingmonkey.net',0),(55,'abuse','httpbl.abuse.ch',0),(56,'sorbs','http.dnsbl.sorbs.net',0),(57,'rbl','images.rbl.msrbl.net',0),(58,'backscatterer','ips.backscatterer.org',0),(60,'services','korea.services.net',0),(61,'dnsbl','ksi.dnsbl.net.au',0),(62,'people','mail.people.it',0),(63,'sorbs','misc.dnsbl.sorbs.net',0),(64,'surbl','multi.surbl.org',0),(65,'pedantic','netblock.pedantic.org',0),(66,'Spamrats NOPTR','noptr.spamrats.com',1),(67,'dnsbl','ohps.dnsbl.net.au',0),(68,'dnsbl','omrs.dnsbl.net.au',0),(69,'tornevall','opm.tornevall.org',0),(70,'aupads','orvedb.aupads.org',0),(71,'osps.dnsbl','osps.dnsbl.net.au',0),(72,'osrs.dnsbl','osrs.dnsbl.net.au',0),(73,'owfs.dnsbl','owfs.dnsbl.net.au',0),(74,'owps.dnsbl','owps.dnsbl.net.au',0),(76,'msrbl','phishing.rbl.msrbl.net',0),(77,'dnsbl.solid','pool.dnsbl.solid.net',0),(78,'probes.dnsbl','probes.dnsbl.net.au',0),(79,'Gweep','proxy.bl.gweep.ca',0),(80,'Block','proxy.block.transip.nl',0),(81,'Surriel','psbl.surriel.com',0),(82,'senderbase','query.senderbase.org',0),(83,'efnetrbl','rbl.efnetrbl.org',0),(84,'interserver','rbl.interserver.net',0),(85,'mail-abuse','rbl-plus.mail-abuse.org',0),(86,'spamlab','rbl.spamlab.com',0),(87,'suresupport','rbl.suresupport.com',0),(88,'dnsbl','rdts.dnsbl.net.au',0),(89,'Gweep','relays.bl.gweep.ca',0),(90,'kundenserver','relays.bl.kundenserver.de',0),(91,'mail-abuse','relays.mail-abuse.org',0),(92,'nether','relays.nether.net',0),(93,'Mailspike','rep.mailspike.ne',0),(94,'block','residential.block.transip.nl',0),(95,'dnsbl','ricn.dnsbl.net.au',0),(96,'dnsbl','rmst.dnsbl.net.au',0),(97,'blackhole','rot.blackhole.cantv.net',0),(99,'rbl','short.rbl.jp',0),(100,'sorbs','smtp.dnsbl.sorbs.net',0),(101,'sorbs','socks.dnsbl.sorbs.net',0),(102,'dnsbl','sorbs.dnsbl.net.au',0),(103,'abuse','spam.abuse.ch',0),(104,'sorbs','spam.dnsbl.sorbs.net',0),(105,'or','spamlist.or.kr',0),(106,'imp','spamrbl.imp.ch',0),(107,'rbl','spam.rbl.msrbl.net',0),(108,'Spamrats SPAM','spam.spamrats.com',1),(109,'dnsbl','t3direct.dnsbl.net.au',0),(110,'dan','tor.dan.me.uk',0),(111,'sectoor','tor.dnsbl.sectoor.de',0),(113,'sectoor','torserver.tor.dnsbl.sectoor.de',0),(114,'lashback','ubl.lashback.com',0),(115,'unsubscore','ubl.unsubscore.com',0),(116,'spameatingmonkey','uribl.spameatingmonkey.net',0),(117,'swinog','uribl.swinog.ch',0),(118,'spameatingmonkey','urired.spameatingmonkey.net',0),(119,'rbl','url.rbl.jp',0),(120,'bit','virbl.bit.nl',0),(121,'rbl','virus.rbl.jp',0),(122,'msrbl','virus.rbl.msrbl.net',0),(123,'Sorbs DNSBL','web.dnsbl.sorbs.net',0),(124,'rfc-ignorant','whois.rfc-ignorant.org',0),(125,'Mailspike WL','wl.mailspike.ne',0),(126,'imp','wormrbl.imp.ch',0),(129,'Mailspike','z.mailspike.net',0),(130,'Sorbs','zombie.dnsbl.sorbs.net',0),(135,'woody','blacklist.woody.ch',0),(150,'sci.kun','blacklist.sci.kun.nl',0),(181,'websitewelcome','rbl.websitewelcome.com',0),(182,'redhawk','access.redhawk.org',0),(183,'spamblock','all.spamblock.unit.liu.se',0),(185,'spameatingmonkey','backscatter.spameatingmonkey.net',0),(186,'five-ten-sg','blackholes.five-ten-sg.com',0),(187,'mail-abuse','blackholes.mail-abuse.org',0);");
mysqli_query($db, "INSERT INTO system (registroTipo, registroPlano, tokenRAVI, GrafDisp, versao, versaoData, debug, ativoRAVI, updateRede, falhasRegistro) VALUES ('G', '100', '".$tokenRAVI."', '1', '".$versao."', '".$versaoData."', '".$versaoDebug."', '1', '0', '0');");
mysqli_query($db, "INSERT INTO grupoUser (id, nome, dns, dispositivos, olt, concentradora, pconcentradora, polt, pdispositivo) VALUES (1, 'Master', 2, 2, 2, 2, '' , '', '');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un3, Col1, Col2, Col3, descr, exec, link) VALUES ('Ping & Jitter', 'ping', 3, 1, 1, 1, '300', 3, ' ms', ' ms', 'Ping (Latência)', 'Perda de pacotes', 'Jitter (atrazo/tremulação)', 'Monitora a conectividade, latência e jitter usando ping', 0, 'ping.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec, link) VALUES ('HTTPing', 'http', 3, 1, 2, 1, '300', 2, ' ms', ' Kbps', 'Latência', 'Taxa de transferência', 'Monitorar a latência e a taxa de transferência de um servidor web', 0, 'http.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Jitter', 'jitter', 1, 1, 1, 1, '300', 1, ' ms', 'Jitter (atrazo/tremulação)', 'Monitora o atraso na entrega de dados em uma rede', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, un1, Col1, descr) VALUES ('Traceroute', 'traceroute', 2, 1, 3, 0, 1, ' Saltos', 'Total', 'Obtém o número de saltos e alerta se a rota mudar');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('DNS', 'dns', 3, 1, 1, 1, '300', 1, ' ms', 'Latência', 'Monitora um servidor de DNS (Serviço de resolução de domínios)', 0, 'TestDNS.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, Col2, descr, exec, link) VALUES ('Port Scan', 'portscan', 2, 1, 4, 0, 2, 'Total', 'Portas', 'Monitora portas abertas e alerta se novas portas forem abertas para um IP ou Domínio', 0, 'PortScan.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, un1, Col1, Col2, descr, exec, link) VALUES ('DNS Avançado', 'dnstop', 1, 1, 3, 0, 2, ' ms', 'Latência', 'Melhor servidor DNS', 'Monitora qual o servidor de DNS mais rápido para resolver um domínio', 0, 'TestDNStop.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, minPer, qtdCol, un1, Col1, Col2, descr, exec, link) VALUES ('MySQL', 'mysql', 3, 1, 1, 1, '300', '70', '2', ' ms', 'Latência', 'Valor', 'Monitora o banco de dados de um servidor MySQL', 0, 'MySQL.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, Col2, Col3, descr, exec, link) VALUES ('HTTP Completo', 'httpcomp', 3, 1, 5, 0, 3, 'Tempo', 'Tamanho do site completo', 'Velocidade da conexão', 'Monitora o tempo levado para abrir uma página levando em consideração a velocidade da conexão e tamanho da página', 0, 'httpcomp.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('SMTP', 'smtpconect', 3, 1, 1, 0, '80', 1, ' ms', 'Latência', 'Monitora a disponibilidade de um servidor SMTP (Protocolo simples de transporte de correio)', 0, 'SMTPTest.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, Col2, descr, exec, link) VALUES ('Consulta RBL', 'rbl', 2, 1, 5, 0, 2, 'Total de RBL', 'RBLs listadas', 'Descubra se seu IP esta em alguma Blacklist', 0, 'RBLTest.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, un1, Col1, descr, exec, link) VALUES ('Contagem FTP', 'contagemftp', 3, 1, 2, 0, 1, ' Arquivos', 'Quantidade', 'Monitora a conectividade e número de arquivos em um FTP', 0, 'ContagemFTP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec, link) VALUES ('Status Operacional', 'ifOperStatus', 2, 2, 1, 0, 1, 'Status operacional da interface', 'Monitora o status operacional de interfaces em dispositivos usando SNMP', 0, 'ifOperStatus.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec, link) VALUES ('Coolers NEXUS', 'coolersnexus', 2, 2, 1, 0, 1, 'Status operacional do cooler', 'Monitora o status operacional dos coolers de um switch CISCO na linha NEXUS usando SNMP', 0, 'coolersnexus.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec, link) VALUES ('Status Administrativo', 'statusporta', 2, 2, 1, 0, 1, 'Status administrativo da interface', 'Monitora o status administrativo de interfaces em dispositivos usando SNMP', 0, 'statusporta.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, un1, Col1, descr, exec, link) VALUES ('Velocidade Interface', 'velocidadeporta', 2, 2, 1, 0, 1, ' Mbps', 'Velocidade de negociação da Interface', 'Monitora a velocidade de negociação de uma interface usando SNMP', 0, 'velocidadeporta.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, Col1, Col2, Col3, descr, exec, link) VALUES ('Tráfego', 'trafegosnmp', 1, 2, 2, 1, '70', 3, 'Total', 'Download', 'Upload', 'Monitora o tráfego e status operacional em dispositivos via SNMP (RCF1213)', 0, 'trafegoSNMP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, Col1, descr, exec, link) VALUES ('Pacotes com erro', 'pacoteserro', 2, 2, 2, 1, '300', 1, 'Pacotes com erro', 'Monitora erros de transmissão em pacotes de entrada e saída', 0, 'pacoteserroSNMP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem', 'voltagem', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem de um dispositivo usando SNMP', 0, 'voltagem.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem de Interface', 'voltinterfacevsol', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem da interface de uma OLT V-Solution usando SNMP', 0, 'voltagemV-Solution.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem por PON', 'voltponhuawei', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem da PON de uma OLT Huawei usando SNMP', 0, 'voltagemPONHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem por PON', 'voltponfiberhome', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem da PON de uma OLT Fiberhome usando SNMP', 0, 'voltagemPonFiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Voltagem NEXUS', 'voltagemnexus', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem das fontes de um switch CISCO na linha NEXUS usando SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão Saída Controlador Solar MPPT', 'tenssaidampptvolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a tensão de saída em Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão de Saída', 'tenssaidaalgcom', 2, 2, 1, 1, '20', 1, ' V', 'Tensão de Saída', 'Monitora a tensão de saída de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão da Bateria', 'tensbatalgcom', 2, 2, 1, 1, '20', 1, ' V', 'Tensão da Bateria', 'Monitora a tensão da bateria de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão da Bateria', 'tensbatrondotec', 2, 2, 1, 1, '20', 1, ' V', 'Tensão da Bateria', 'Monitora a tensão da bateria de uma fonte nobreak Rondotec usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão da Rede AC', 'tensaorederondotec', 2, 2, 1, 1, '20', 1, ' V', 'Tensão da Rede AC', 'Monitora a tensão da rede AC de uma fonte nobreak Rondotec usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão da Rede AC Pop Protect', 'statusredevolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão da Rede AC', 'Monitora o status e tensão AC da rede elétrica para Pop Protect VOLT usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Voltagem Mod Pop Protect', 'voltmodvolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem do módulo para Pop Protect VOLT usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Voltagem Bat Pop Protect', 'voltbatvolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem da bateria para Pop Protect VOLT usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem SFP', 'voltagemsfpmk', 2, 2, 1, 1, '25', '25', 1, ' V', 'Tensão', 'Monitora a voltagem de interfaces SFP Mikrotik usando SNMP', 0, 'VoltagemSFP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem NET PROBE', 'voltagemnetprobevolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem do módulo para NET PROBE Volt usando SNMP', 0, 'voltagemnetprobevolt.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem NET PROBE', 'voltagemnetprobeplusvolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem do módulo para NET PROBE PLUS Volt usando SNMP', 0, 'voltagemnetprobeplusvolt.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem USCC', 'voltagemxpsuscc', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem da bateria de um XPS USCC usando SNMP', 0, 'voltagemxpsuscc.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem por PON', 'voltponzte', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem da PON de uma OLT ZTE usando SNMP', 0, 'voltagemPONZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem', 'voltagemceragon', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a voltagem de rádio Ceragon usando SNMP', 0, 'voltagemceragon.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Voltagem SFP', 'voltsfphuawei', 2, 2, 1, 1, '25', '25', 1, ' V', 'Tensão', 'Monitora a voltagem de interfaces SFP Mikrotik usando SNMP', 0, 'voltsfphuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão Bateria Controlador Solar MPPT', 'tensbatmpptvolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a tensão da bateria em Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Tensão Painel Controlador Solar MPPT', 'tenspainelmpptvolt', 2, 2, 1, 1, '20', 1, ' V', 'Tensão', 'Monitora a tensão do painel em Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Tensão CA', 'tensaocaxpsuscc', 2, 2, 1, 0, 1, 'Status da tensão CA', 'Monitora a tensão CA de um XPS USCC usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Carga Bateria USCC', 'cargabatxpsuscc', 2, 2, 1, 0, 1, 'Carregamento da bateria', 'Monitora o carregamento da bateria de um XPS USCC usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Descarga Bateria USCC', 'descargabatxpsuscc', 2, 2, 1, 0, 1, 'Descarregamento da bateria', 'Monitora o descarregamento da bateria de um XPS USCC usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Conexão Bateria USCC', 'bateriaxpsuscc', 2, 2, 1, 0, 1, 'Conexão da bateria', 'Monitora a conexão da bateria de um XPS USCC usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU', 'cpusnmp', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga da CPU de um sistema usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU', 'cpuoltparks', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga da CPU em uma OLT Parks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Carga CPU por Core', 'cpucoremk', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga de CPU por core de um dispositivo Mikrotik usando SNMP', 0, 'cpucoremk.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU', 'cpucambium', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga do CPU de um rádio Cambium Networks via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU', 'cpuvsol', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga da CPU de uma OLT V-Solution usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU', 'cpujuniper', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga da CPU em um router Juniper Networks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Carga de CPU da Placa', 'cpuplacahuawei', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga de CPU da Placa de uma OLT Huawei usando SNMP', 0, 'cpuPlacaHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Carga de CPU da Placa', 'cpuplacafiberhome', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga de CPU da Placa de uma OLT Fiberhome usando SNMP', 0, 'cpuPlacaFiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Carga de CPU da Placa', 'cpuplacazte', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga de CPU da placa de uma OLT ZTE usando SNMP', 0, 'cpuPlacaZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU NEXUS', 'cpunexus', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga da CPU de um switch CISCO na linha NEXUS usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU Série NE', 'cpunehuawei', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga de CPU em um router Huawei série NE usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, minSet, qtdCol, un1, Col1, descr, exec) VALUES ('Carga CPU ROUTER', 'cpurouterhuawei', 2, 2, 1, 1, '70', '70', '30', 1, ' %', 'Utilização da CPU', 'Monitora a carga de CPU em um router Huawei usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, minPer, descr, exec, link) VALUES ('Sensores Agregados', 'agregaSensor', 1, 1, 1, 1, '300', '70', 'Soma os valores de dois ou mais sensores', 0, 'agregaSensor.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, minPer, qtdCol, Col1, descr, exec, link) VALUES ('SNMP Customizado', 'snmpcustom', 2, 2, 1, 1, '300', '70', 1, 'Valor', 'Monitora um valor numérico retornado por um OID específico usando SNMP', 0, 'SNMPCustom.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, Col2, descr, exec) VALUES ('Load Average', 'loadAverageLinux', 2, 2, 2, 0, 2, 'Fila de processos', 'Load Average', 'Monitora o Load Average e fila de processos por core em sistemas Linux/Unix usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec, link) VALUES ('Status Operacional BGP', 'bgpoper', 2, 2, 1, 0, 1, 'Status Operacional', 'Monitora o status operacional de uma sessão BGP em SyOS, Cisco ou Huawei usando SNMP', 0, 'bgpoper.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec, link) VALUES ('Status Administrativo BGP', 'bgpadm', 2, 2, 1, 0, 1, 'Status Administrativo', 'Monitora o status administrativo de uma sessão BGP em SyOS, Cisco ou Huawei usando SNMP', 0, 'bgpadm.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec, link) VALUES ('Sinal SFP TX / RX', 'sinalsfpmk', 2, 2, 1, 1, '40', 2, ' dBm', ' dBm', 'Sinal ( Tx )', 'Sinal ( Rx )', 'Monitora o sinal TX e RX de interfaces SFP Mikrotik usando SNMP', 0, 'SinalSFP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('TX Power SFP Switch', 'txsfphuawei', 2, 2, 1, 1, '30', 1, ' dBm', 'TX Power', 'Monitora o sinal TX Power em interfaces SFP de um switch usando SNMP', 0, 'txsfphuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('RX Power SFP Switch', 'rxsfphuawei', 2, 2, 1, 1, '30', 1, ' dBm', 'RX Power', 'Monitora o sinal RX Power em interfaces SFP de um switch usando SNMP', 0, 'rxsfphuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('RX Power SFP Switch S6730', 'rxsfphuaweiS6730', 2, 2, 1, 1, '30', 1, ' dBm', 'RX Power', 'Monitora o sinal RX Power em interfaces SFP de um switch modelo S6730 usando SNMP', 0, 'rxsfphuaweiS6730.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Comprimento de onda SFP', 'wavelengthsfpmk', 2, 2, 1, 1, '80', 1, ' nm', 'Wavelength', 'Monitora o comprimento de onda (Wavelength) SFP Mikrotik usando SNMP', 0, 'WavelengthSFP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões PPPoE', 'pppoe', 2, 2, 1, 1, '40', '30', 1, 'Conexões PPPoE', 'Monitora a quantidade de Conexões PPPoE Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões PPPoE', 'pppoehuawei', 2, 2, 1, 1, '40', '30', 1, 'Conexões PPPoE', 'Monitora a quantidade de Conexões PPPoE em um router Huawei série NE usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões PPPoE', 'pppoejuniper', 2, 2, 1, 1, '40', '30', 1, 'Conexões PPPoE', 'Monitora a quantidade de Conexões PPPoE em um router Juniper usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões por VLAN', 'convlanhuawei', 2, 2, 1, 1, '40', '1', 1, 'Conexões VLAN', 'Monitora a quantidade de conexões por VLAN em uma concentradora usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões PPPoE', 'pppoecisco', 2, 2, 1, 1, '40', '30', 1, 'Conexões PPPoE', 'Monitora a quantidade de Conexões PPPoE em um router Cisco usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões DHCP', 'dhcpmk', 2, 2, 1, 1, '40', '30', 1, 'Conexões DHCP', 'Monitora a quantidade de Conexões DHCP Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('CCQ Overall', 'ccqmksnmp', 2, 2, 1, 1, '15', 1, ' %', 'CCQ', 'Monitora a média geral do CCQ em rádios Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('CCQ Rádio 5.8', 'ccqubntsnmp', 2, 2, 1, 1, '15', 1, ' %', 'CCQ', 'Monitora o CCQ de um rádio Ubiquiti via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Sinal', 'sinalmksnmp', 2, 2, 1, 1, '15', 1, ' dBm', 'Sinal', 'Monitora o sinal (strength) de um rádio Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Sinal Rádio 5.8', 'sinalubntsnmp', 2, 2, 1, 1, '15', 1, ' dBm', 'Sinal', 'Monitora o sinal de um rádio Ubiquiti via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Sinal Rádio 5.8', 'sinalintelbras', 2, 2, 1, 1, '15', 1, ' dBm', 'Sinal', 'Monitora o sinal de um rádio Intelbras usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Sinal', 'sinalmimosa', 2, 2, 1, 1, '15', 1, ' dBm', 'Sinal', 'Monitora o sinal de um rádio Mimosa via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Sinal', 'sinalcambium', 2, 2, 1, 1, '15', 1, ' dBm', 'Sinal', 'Monitora o sinal de um rádio Cambium Networks via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Sinal IP10', 'sinalceragon', 2, 2, 1, 1, '15', 1, ' dBm', 'Sinal', 'Monitora o sinal de um rádio Ceragon via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('Sinal IP20', 'sinal20ceragon', 2, 2, 1, 1, '15', 2, ' dBm', ' dBm', 'Sinal RX Rádio 1', 'Sinal RX Rádio 2', 'Monitora o sinal de um rádio Ceragon via SNMP', 2);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('Sinal TX A/B', 'txsiae', 2, 2, 1, 1, '15', 2, ' dBm', ' dBm', 'TX Power A', 'TX Power B', 'Monitora a potência de transmissão de um rádio Siae usando SNMP', 2);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('Sinal RX A/B', 'rxsiae', 2, 2, 1, 1, '15', 2, ' dBm', ' dBm', 'RX Power A', 'RX Power B', 'Monitora a potência de recepção de um rádio Siae usando SNMP', 2);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Noise Floor', 'noisefloormksnmp', 2, 2, 1, 1, '15', 1, ' dBm', 'Interferência', 'Monitora o nível de interferência em rádios Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Noise Floor', 'noisemimosa', 2, 2, 1, 1, '15', 1, ' dBm', 'Interferência', 'Monitora o nível de interferência em rádios Mimosa via SNMP', 2);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Noise Floor', 'noisefloorubntsnmp', 2, 2, 1, 1, '15', 1, ' dBm', 'Interferência', 'Monitora o nível de interferência em rádios Ubiquiti via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Noise Floor', 'noisefloorintelbras', 2, 2, 1, 1, '15', 1, ' dBm', 'Interferência', 'Monitora o nível de interferência em rádios Intelbras usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, un2, Col1, Col2, descr, exec, link) VALUES ('Data Rate', 'dataratemksnmp', 2, 2, 1, 1, '60', 1, ' Mbps', ' Mbps', 'TX Rate', 'RX Rate', 'Monitora a capacidade de tráfego em tx-rate e rx-rate em rádios Mikrotik via SNMP', 0, 'DataRateMK.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, un2, Col1, Col2, descr, exec, link) VALUES ('Data Rate', 'datarateubntsnmp', 2, 2, 1, 1, '60', 1, ' Mbps', ' Mbps', 'TX Rate', 'RX Rate', 'Monitora a capacidade de tráfego em tx-rate e rx-rate em Ubiquiti via SNMP', 0, 'DataRateUbnt.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('MAC Speed', 'macmimosa', 2, 2, 1, 1, '60', 1, ' Mbps', ' Mbps', 'TX Rate', 'RX Rate', 'Calcula a velocidade máxima possível em rádios Mimosa via SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('PHY Speed', 'phymimosa', 2, 2, 1, 1, '60', 1, ' Mbps', ' Mbps', 'TX Rate', 'RX Rate', 'Calcula a capacidade de tráfego em rádios Mimosa via SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões WAN', 'conexmikrotiksnmp', 2, 2, 1, 1, '40', '1', 1, 'Quantidade de conexões', 'Monitora a quantidade de clientes conectados em um rádio Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões WAN', 'conexubntsnmp', 2, 2, 1, 1, '40', '1', 1, 'Quantidade de conexões', 'Monitora a quantidade de clientes conectados em um rádio Ubiquiti via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões WAN', 'conexintelbras', 2, 2, 1, 1, '40', '1', 1, 'Quantidade de conexões', 'Monitora a quantidade de clientes conectados em um rádio Intelbras via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões Autenticadas', 'authmikrotiksnmp', 2, 2, 1, 1, '40', '1', 1, 'Quantidade de conexões', 'Monitora a quantidade de conexões autenticadas em um rádio Mikrotik via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Capacidade Airmax', 'airmaxcubntsnmp', 2, 2, 1, 1, '40', 1, ' %', 'Capacidade Airmax', 'Monitora o nível de capacidade em rádios Ubiquiti com Airmax habilitado via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Qualidade Airmax', 'airmaxqubntsnmp', 2, 2, 1, 1, '40', 1, ' %', 'Qualidade Airmax', 'Monitora o nível de qualidade em rádios Ubiquiti com Airmax habilitado via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Capacidade Cambium Networks', 'capacidadecambium', 2, 2, 1, 1, '40', 1, ' %', 'Capacidade', 'Monitora o percentual da Capacidade Downlink de um rádio Cambium Networks via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Qualidade Cambium Networks', 'qualidadecambium', 2, 2, 1, 1, '40', 1, ' %', 'Qualidade', 'Monitora o percentual de Qualidade Downlink de um rádio Cambium Networks via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura', 'temperatura', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura de um dispositivo usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura', 'tempoltparks', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura de uma OLT Parks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura CPU', 'temperaturacpu', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da CPU de um dispositivo usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura', 'temprondotec', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura de uma fonte nobreak Rondotec usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura CPU', 'temperaturamimosa', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da CPU em rádios Mimosa via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura', 'temperaturavsol', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura de uma OLT V-Solution usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura', 'temperaturafiberhome', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura do sistema de uma OLT Fiberhome usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura', 'temperaturajuniper', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura em um router Juniper Networks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Série NE', 'temperaturanehuawei', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura em um router Huawei série NE usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Interna Contr. Solar MPPT', 'tempintmpptvolt', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura interna de um Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Interna Fonte Nobreak', 'tempintalgcom', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura interna de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Externa Fonte Nobreak', 'tempextalgcom', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura externa de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Mod Pop Protect', 'tempmodvolt', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura do módulo para Pop Protect Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Amb Pop Protect', 'tempambvolt', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura do ambiente para Pop Protect Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura SFP', 'tempsfpmk', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura de interfaces SFP Mikrotik usando SNMP', 0, 'TempSFP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura SFP', 'tempsfphuawei', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura de interfaces SFP Mikrotik usando SNMP', 0, 'tempsfphuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura por PON', 'temperaturaponhuawei', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da PON de uma OLT Huawei usando SNMP', 0, 'tempPONHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura por PON', 'temperaturaponfiberhome', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da PON de uma OLT Fiberhome usando SNMP', 0, 'TemperaturaPonFiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura de Interface', 'tempinterfacevsol', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da interface de uma OLT V-Solution usando SNMP', 0, 'temperaturaV-Solution.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura de Slot', 'tempslothuawei', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura do Slot de uma OLT Huawei usando SNMP', 0, 'tempSlotHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura da placa', 'tempplacahuawei', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da placa de uma OLT Huawei usando SNMP', 0, 'tempPlacaHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura NET PROBE', 'tempnetprobevolt', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura do módulo para NET PROBE Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura NET PROBE PLUS', 'tempnetprobeplusvolt', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura do módulo para NET PROBE PLUS Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura USCC', 'tempxpsuscc', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da bateria de um XPS USCC usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura por PON', 'temperaturaponzte', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da PON de uma OLT ZTE usando SNMP', 0, 'tempPONZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura Chassis', 'tempchassiszte', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura Chassis', 'Monitora a temperatura do Chassis de uma OLT ZTE usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec, link) VALUES ('Temperatura da placa', 'tempplacazte', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura', 'Monitora a temperatura da placa de uma OLT ZTE usando SNMP', 0, 'tempPlacaZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura ODU', 'tempoduceragon', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura ODU', 'Monitora a temperatura ODU em rádio Ceragon usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, maxSet, qtdCol, un1, Col1, descr, exec) VALUES ('Temperatura IDU', 'tempiduceragon', 2, 2, 1, 1, '60', '70', 1, '° C', 'Temperatura IDU', 'Monitora a temperatura IDU em rádio Ceragon usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Conexão WAN', 'wanmimosa', 2, 2, 1, 1, 1, 'Status', 'Monitora uma conexão wireless (Conectado / Desconectado) em rádios Mimosa via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Tempo de atividade', 'uptime', 1, 2, 1, 0, 1, 'Tempo de atividade', 'Monitora o tempo de atividade de um dispositivo usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Uptime Link', 'uptimelinkmimosa', 1, 2, 1, 0, 1, 'Tempo de atividade', 'Monitora o tempo de uma conexão wireless em rádios Mimosa via SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Tempo de atividade', 'lastrebootmimosa', 1, 2, 1, 0, 1, 'Tempo de atividade', 'Monitora data e hora do último reboot de um rádio Mimosa usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('SNR Cambium Networks', 'snrcambium', 2, 2, 1, 1, '30', 2, ' dB', ' dB', 'SNR Downlink', 'SNR Uplink', 'Monitora a relação sinal-ruído (SNR) em dB de um rádio Cambium Networks via SNMP', 2);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Conexões WAN', 'associedcambium', 2, 2, 1, 1, '40', '1', 1, 'Quantidade de conexões', 'Monitora a quandidade de conexões em um rádio Cambium Networks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec, link) VALUES ('ONUs conectadas por interface', 'qtdonuinterfacevsol', 2, 2, 1, 1, '40', '1', 1, 'Total ONUs conectadas', 'Monitora a quantidade de ONUs conectadas por interface em OLT V-Solution usando SNMP', 0, 'qtdONUV-Solution.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Total ONUs conectadas', 'totalonuvsol', 2, 2, 1, 1, '40', '1', 1, 'Total ONUs conectadas', 'Monitora o total de ONUs conectadas em OLT V-Solution usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec, link) VALUES ('ONUs autorizadas por PON', 'onuponfiberhome', 2, 2, 1, 1, '40', '1', 1, 'Total ONUs autorizadas', 'Monitora a quantidade de ONUs autorizadas por PON de uma OLT Fiberhome usando SNMP', 0, 'qtdONUfiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Total ONUs conectadas', 'totalonuponfiberhome', 2, 2, 1, 1, '40', '1', 1, 'Total ONUs conectadas', 'Monitora a quantidade total de ONUs conectadas em uma OLT Fiberhome usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec, link) VALUES ('ONUs autorizadas por PON', 'onuponhuawei', 2, 2, 1, 1, '40', '1', 1, 'Total ONUs autorizadas', 'Monitora a quantidade de ONUs autorizadas por PON de uma OLT Huawei usando SNMP', 0, 'qtdONUhuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, minSet, qtdCol, Col1, descr, exec) VALUES ('Total ONUs autorizadas', 'totalonuponhuawei', 2, 2, 1, 1, '40', '1', 1, 'Total ONUs autorizadas', 'Monitora a quantidade total de ONUs autorizadas em uma OLT Huawei usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, minPer, qtdCol, un1, Col1, Col2, Col3, Col4, descr, exec, link) VALUES ('Disco Livre', 'disksnmp', 2, 2, 1, 0, '90', '80', 4, ' %', 'Percentual', 'Total', 'Usado', 'Livre', 'Monitora o espaço livre de um disco lógico ou partição montada', 0, 'diskSNMP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, Col2, Col3, Col4, descr, exec) VALUES ('Memória RAM', 'ramsnmp', 2, 2, 1, 0, '90', 4, ' %', 'Percentual', 'Total', 'Usado', 'Livre', 'Monitora o uso da memória RAM de um sistema usando SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, Col2, Col3, Col4, descr, exec) VALUES ('Memória RAM', 'ramoltparks', 2, 2, 1, 0, '90', 4, ' %', 'Percentual', 'Total', 'Usado', 'Livre', 'Monitora o uso da memória RAM de uma OLT Parks usando SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, Col2, Col3, Col4, descr, exec) VALUES ('Memória RAM ROUTER', 'ramrouterhuawei', 2, 2, 1, 0, '90', 4, ' %', 'Percentual', 'Total', 'Usado', 'Livre', 'Monitora o uso da memória RAM em um router Huawei usando SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Memória RAM', 'ramvsol', 2, 2, 1, 0, '90', 1, ' %', 'Valor', 'Monitora o uso da memória RAM de uma OLT V-Solution usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Memória RAM', 'ramjuniper', 2, 2, 1, 0, '90', 1, ' %', 'Utilização da memória RAM', 'Monitora o consumo de memória RAM em um router Juniper Networks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Memória RAM da Placa', 'ramplacahuawei', 2, 2, 1, 0, '90', 1, ' %', 'Utilização da memória RAM', 'Monitora a carga de Memória RAM da placa de uma OLT Huawei usando SNMP', 0, 'ramPlacaHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Memória RAM da Placa', 'ramplacafiberhome', 2, 2, 1, 0, '90', 1, ' %', 'Utilização da memória RAM', 'Monitora a carga de Memória RAM da placa de uma OLT Fiberhome usando SNMP', 0, 'ramPlacaFiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Memória RAM da Placa', 'ramplacazte', 2, 2, 1, 0, '90', 1, ' %', 'Utilização da memória RAM', 'Monitora a carga de Memória RAM da placa de uma OLT ZTE usando SNMP', 0, 'ramPlacaZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Ganho Efetivo', 'ganhocambium', 2, 2, 1, 1, '30', 1, ' dBi', 'Ganho efetivo', 'Monitora o ganho efetivo (dBi) em um rádio Cambium Networks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Potência', 'potenciacambium', 2, 2, 1, 1, '30', 1, ' dBm', 'Potência TX atual', 'Monitora a potência atual (dBm) em um rádio Cambium Networks usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec) VALUES ('Potência', 'potenciaceragon', 2, 2, 1, 1, '30', 1, ' dBm', 'Potência TX atual', 'Monitora a potência atual (dBm) em um rádio Ceragon usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Potência de transmissão', 'powerinterfacevsol', 2, 2, 1, 1, '30', 1, ' dBm', 'Sinal', 'Monitora a potência de transmissão (Transmit Power) de interfaces em OLT V-Solution usando SNMP', 0, 'PowerV-Solution.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Potência de transmissão por PON', 'txpowerhuawei', 2, 2, 1, 1, '30', 1, ' dBm', 'Sinal', 'Monitora a potência de transmissão (TX Power) da PON de uma OLT Huawei usando SNMP', 0, 'TXPowerHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Potência de transmissão por PON', 'txpowerfiberhome', 2, 2, 1, 1, '30', 1, ' dBm', 'Sinal', 'Monitora a potência de transmissão (TX Power) da PON de uma OLT Fiberhome usando SNMP', 0, 'TXPowerFiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Potência de transmissão por PON', 'txpowerzte', 2, 2, 1, 1, '30', 1, ' dBm', 'Sinal', 'Monitora a potência de transmissão (TX Power) da PON de uma OLT ZTE usando SNMP', 0, 'TXPowerZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Potência de transmissão por PON', 'txponoltparks', 2, 2, 1, 1, '30', 1, ' dBm', 'Sinal', 'Monitora a potência de transmissão (TX Power) da PON de uma OLT Parks usando SNMP', 0, 'TXPowerParks.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Corrente de polarização do SFP', 'biassfpmk', 2, 2, 1, 1, '80', 1, ' mA', 'Corrente de polarização', 'Monitora a corrente de polarização (SFP Bias Current) de interfaces SFP Mikrotik usando SNMP', 0, 'BiasSFP.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Corrente de polarização do SFP', 'biassfphuawei', 2, 2, 1, 1, '80', 1, ' mA', 'Corrente de polarização', 'Monitora a corrente de polarização (SFP Bias Current) de interfaces SFP em switch usando SNMP', 0, 'biassfphuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Corrente de polarização por PON', 'biasponhuawei', 2, 2, 1, 1, '80', 1, ' mA', 'Corrente de polarização', 'Monitora a corrente de polarização (Bias Current) da PON de uma OLT Huawei usando SNMP', 0, 'TxBiasCurrentHuawei.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Corrente de polarização por PON', 'biasponfiberhome', 2, 2, 1, 1, '80', 1, ' mA', 'Corrente de polarização', 'Monitora a corrente de polarização (Bias Current) da PON de uma OLT Fiberhome usando SNMP', 0, 'BiasPonFiberhome.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Corrente de polarização', 'biasinterfacevsol', 2, 2, 1, 1, '80', 1, ' mA', 'Corrente de polarização', 'Monitora a corrente de polarização (Bias Current) de interfaces em uma OLT V-Solution usando SNMP', 0, 'BiasV-Solution.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('Corrente de polarização por PON', 'biasponzte', 2, 2, 1, 1, '80', 1, ' mA', 'Corrente de polarização', 'Monitora a corrente de polarização (Bias Current) da PON de uma OLT ZTE usando SNMP', 0, 'BiasPonZTE.php');");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Corrente Bateria Contr. Solar MPPT', 'correntebatmpptvolt', 2, 2, 1, 1, '20', 1, ' A', 'Corrente da bateria', 'Monitora a corrente da bateria em Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Corrente da Bateria', 'correntebatalgcom', 2, 2, 1, 1, '20', 1, ' A', 'Corrente da bateria', 'Monitora a corrente da bateria de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Corrente de Consumo', 'correnterondotec', 2, 2, 1, 1, '20', 1, ' A', 'Corrente de consumo', 'Monitora o consumo de equipamentos ligados na saída de uma fonte nobreak Rondotec usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Corrente Painel Contr. Solar MPPT', 'correntepainelmpptvolt', 2, 2, 1, 1, '20', 1, ' A', 'Corrente do painel', 'Monitora a corrente do painel em Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Corrente Saída Contr. Solar MPPT', 'correntesaidampptvolt', 2, 2, 1, 1, '20', 1, ' A', 'Corrente de saída', 'Monitora a corrente de saída em Controlador Solar MPPT Volt usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, Col1, descr, exec) VALUES ('Corrente de Saída', 'correntesaidaalgcom', 2, 2, 1, 1, '20', 1, ' A', 'Corrente de saída', 'Monitora a corrente de saída de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Status do Carregador', 'statuscarralgcom', 2, 2, 1, 0, 1, 'Status', 'Monitora o status do carregador de uma fonte nobreak ALGcom usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Sensores Pop Protect', 'sensoresvolt', 2, 2, 1, 0, 1, 'Status', 'Monitora sensor1, sensor2 e sensor3 para Pop Protect VOLT usando SNMP', 0);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Relé Pop Protect', 'relevolt', 2, 2, 1, 0, 1, 'Status', 'Monitora Relé para Pop Protect VOLT usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Sirene Pop Protect', 'sirenevolt', 2, 2, 1, 0, 1, 'Status', 'Monitora a sirene para Pop Protect VOLT usando SNMP', 1);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, qtdCol, Col1, descr, exec) VALUES ('Alarme Pop Protect', 'alarmevolt', 2, 2, 1, 0, 1, 'Status', 'Monitora o acionamento de alarme para Pop Protect VOLT usando SNMP', 1);");

mysqli_query($db, "INSERT INTO models_auth_onu (id, nome, marca, modelo, codigo) VALUES (1,'VEIP','3','2','cd onu\r\nset whitelist phy_addr address #onu_mac password null action add slot #slot pon #pon onu #onu_numero type #onu_tipo\r\nset service_bandwidth slot #slot pon #pon onu #onu_numero type data fix 16 assure 0 max 128000\r\nset service_bandwidth slot #slot pon #pon onu #onu_numero type iptv fix 16 assure 0 max 64\r\ncd lan\r\nset epon slot #slot pon #pon onu #onu_numero port 1 service number 1\r\nset epon slot #slot pon #pon onu #onu_numero port 1 service 1 vlan_mode tag 0 33024 #vlan\r\napply onu #slot #pon #onu_numero vlan\r\ncd ..\r\ncd ..\r\nexit\r\nexit');");
mysqli_query($db, "INSERT INTO models_auth_onu (id, nome, marca, modelo, codigo) VALUES (2,'BRIDGE','3','2','cd onu\r\nset whitelist phy_addr address #onu_mac password null action add slot #slot pon #pon onu #onu_numero type #onu_tipo\r\nset service_bandwidth slot #slot pon #pon onu #onu_numero type data fix 16 assure 0 max 128000\r\nset service_bandwidth slot #slot pon #pon onu #onu_numero type iptv fix 16 assure 0 max 64\r\ncd lan\r\nset epon slot #slot pon #pon onu #onu_numero port 1 service number 1\r\nset epon slot #slot pon #pon onu #onu_numero port 1 service 1 vlan_mode tag 0 33024 #vlan\r\napply onu #slot #pon #onu_numero vlan\r\ncd ..\r\ncd ..\r\nexit\r\nexit');");
mysqli_query($db, "INSERT INTO models_auth_onu (id, nome, marca, modelo, codigo) VALUES (3,'VEIP','3','1','cd gpononu\r\nset white phy addr #onu_mac pas null ac add sl #slot li #pon o #onu_numero ty #onu_tipo\r\nset autho sl #slot li #pon ty #onu_tipo onu #onu_numero phy #onu_mac pas null\r\nset service_ba sl #slot li #pon o #onu_numero ty iptv fix 16 as 0 max 64\r\nset service_ba sl #slot li #pon o #onu_numero ty data fix 16 as 0 max 128000\r\nset service_ba sl #slot li #pon o #onu_numero ty voi fix 16 as 0 max 64\r\ncd ..\r\ncd epononu\r\ncd qinq\r\nset epon slot #slot pon #pon onu #onu_numero port 1 onuveip 1 33024 #vlan 65535 33024 65535 65535 33024 65535 65535 0 1 65535 servn null\r\nset wancfg slot #slot #pon #onu_numero index 1 mode internet type route #vlan 1 nat enable qos disable dsp pppoe proxy disable #pppoe_login #pppoe_senha null auto active enable\r\nset wanbind slot #slot #pon #onu_numero index 1 entries 5 fe1 fe2 fe3 fe4 ssid1\r\napply wancfg slot #slot #pon #onu_numero\r\napply wanbind slot #slot #pon #onu_numero\r\napply wancfg slot #slot #pon #onu_numero\r\nsave synchronation\r\ncd ..\r\ncd ..\r\nexit\r\nexit');");
mysqli_query($db, "INSERT INTO models_auth_onu (id, nome, marca, modelo, codigo) VALUES (4,'BRIDGE','3','1','cd gpononu\r\nset white phy addr #onu_mac pas null ac add slot #slot link #pon onu #onu_numero ty #onu_tipo\r\nset autho slot #slot link #pon type #onu_tipo onu #onu_numero phy #onu_mac pas null\r\nset service_ba slot #slot link #pon onu #onu_numero ty iptv fix 16 as 0 max 64\r\nset service_ba slot #slot link #pon onu #onu_numero ty data fix 16 as 0 max 128000\r\ncd ..\r\ncd epononu\r\ncd qinq\r\nset epon slot #slot pon #pon onu #onu_numero port 1 serv num 1\r\nset epon slot #slot pon #pon onu #onu_numero port 1 service 1 vlan_m tag 1 33024 #vlan\r\napply onu #slot #pon #onu_numero vlan\r\nsave synchronation\r\ncd ..\r\ncd ..\r\nexit\r\nexit');");
mysqli_query($db, "INSERT INTO models_auth_onu (id, nome, marca, modelo, codigo) VALUES (5,'BRIDGE','2','','enable\r\nconfig\r\ninterface gpon 0/#slot\r\nont add #pon sn-auth \"#ns\" omci ont-lineprofile-id #lineprofile ont-srvprofile-id #srvprofile desc \"#nome\"\r\nont port native-vlan #pon #onu_numero eth 1 vlan #vlan priority 0\r\nont port native-vlan #pon #onu_numero eth 2 vlan #vlan priority 0\r\nont port native-vlan #pon #onu_numero eth 3 vlan #vlan priority 0\r\nont port native-vlan #pon #onu_numero eth 4 vlan #vlan priority 0\r\nquit\r\nservice-port vlan #vlan gpon 0/#slot/#pon ont #onu_numero gemport #gemport multi-service user-vlan #vlan tag-transform translate inbound traffic-table index 6 outbound traffic-table index 6\r\nquit\r\nquit\r\ny');");

mysqli_query($db, "INSERT INTO `pais` VALUES (4,93,'AF','AFG','Afeganistão','República Islâmica do Afeganistão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (8,355,'AL','ALB','Albânia','República da Albânia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (10,672,'AQ','ATA','Antártida','Antártida');");
mysqli_query($db, "INSERT INTO `pais` VALUES (12,213,'DZ','DZA','Algéria','República Democrática Popular da Algéria');");
mysqli_query($db, "INSERT INTO `pais` VALUES (16,1684,'AS','ASM','Samoa Americana','Território de Samoa Americana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (20,376,'AD','AND','Andorra','Principado Andorra');");
mysqli_query($db, "INSERT INTO `pais` VALUES (24,244,'AO','AGO','Angola','República de Angola');");
mysqli_query($db, "INSERT INTO `pais` VALUES (28,1268,'AG','ATG','Antigua e Barbuda','Antigua e Barbuda');");
mysqli_query($db, "INSERT INTO `pais` VALUES (31,994,'AZ','AZE','Azerbaijão','República do Azerbaijão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (32,54,'AR','ARG','Argentina','República Argentina');");
mysqli_query($db, "INSERT INTO `pais` VALUES (36,61,'AU','AUS','Austrália','Comunidade da Austrália');");
mysqli_query($db, "INSERT INTO `pais` VALUES (40,43,'AT','AUT','Áustria','República da Áustria');");
mysqli_query($db, "INSERT INTO `pais` VALUES (44,1242,'BS','BHS','Bahamas','Comunidade de Bahamas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (48,973,'BH','BHR','Bahrein','Reino do Bahrein');");
mysqli_query($db, "INSERT INTO `pais` VALUES (50,880,'BD','BGD','Bangladesh','República Popular de Bangladesh');");
mysqli_query($db, "INSERT INTO `pais` VALUES (51,374,'AM','ARM','Armênia','República da Armênia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (52,246,'BB','BRB','Barbados','Barbados');");
mysqli_query($db, "INSERT INTO `pais` VALUES (56,32,'BE','BEL','Bélgica','Reino da Bélgica');");
mysqli_query($db, "INSERT INTO `pais` VALUES (60,1441,'BM','BMU','Bermuda','Bermuda');");
mysqli_query($db, "INSERT INTO `pais` VALUES (64,975,'BT','BTN','Butão','Reino do Butão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (68,591,'BO','BOL','Bolívia','Estado Plurinacional da Bolívia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (70,387,'BA','BIH','Bósnia e Herzegovina','Bósnia e Herzegovina');");
mysqli_query($db, "INSERT INTO `pais` VALUES (72,267,'BW','BWA','Botswana','República da Botswana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (74,47,'BV','BVT','Ilha Bouvet','Ilha Bouvet');");
mysqli_query($db, "INSERT INTO `pais` VALUES (76,55,'BR','BRA','Brasil','República Federativa do Brasil');");
mysqli_query($db, "INSERT INTO `pais` VALUES (84,501,'BZ','BLZ','Belize','Belize');");
mysqli_query($db, "INSERT INTO `pais` VALUES (86,246,'IO','IOT','Território Britânico do Oceano Índico','Território Britânico do Oceano Índico');");
mysqli_query($db, "INSERT INTO `pais` VALUES (90,677,'SB','SLB','Ilhas Salomão','Ilhas Salomão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (92,1284,'VG','VGB','Ilhas Virgens Inglesas','Ilhas Virgens');");
mysqli_query($db, "INSERT INTO `pais` VALUES (96,673,'BN','BRN','Brunei','Estado do Brunei Darussalam');");
mysqli_query($db, "INSERT INTO `pais` VALUES (100,359,'BG','BGR','Bulgária','República da Bulgária');");
mysqli_query($db, "INSERT INTO `pais` VALUES (104,95,'MM','MMR','Birmânia','República da União de Myanmar');");
mysqli_query($db, "INSERT INTO `pais` VALUES (108,257,'BI','BDI','Burundi','República do Burundi');");
mysqli_query($db, "INSERT INTO `pais` VALUES (112,375,'BY','BLR','Bielorrússia','República da Bielorrússia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (116,855,'KH','KHM','Camboja','Reino do Camboja');");
mysqli_query($db, "INSERT INTO `pais` VALUES (120,237,'CM','CMR','Camarões','República de Camarões');");
mysqli_query($db, "INSERT INTO `pais` VALUES (124,1,'CA','CAN','Canadá','Canadá');");
mysqli_query($db, "INSERT INTO `pais` VALUES (132,238,'CV','CPV','Cabo Verde','República do Cabo Verde');");
mysqli_query($db, "INSERT INTO `pais` VALUES (136,1345,'KY','CYM','Ilhas Cayman','Ilhas Cayman');");
mysqli_query($db, "INSERT INTO `pais` VALUES (140,236,'CF','CAF','República Centro-Africana','República Centro-Africana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (144,94,'LK','LKA','Sri Lanka','República Democrática Socialista do Sri Lanka');");
mysqli_query($db, "INSERT INTO `pais` VALUES (148,235,'TD','TCD','Chade','República do Chade');");
mysqli_query($db, "INSERT INTO `pais` VALUES (152,56,'CL','CHL','Chile','República do Chile');");
mysqli_query($db, "INSERT INTO `pais` VALUES (156,86,'CN','CHN','China','República Popular da China');");
mysqli_query($db, "INSERT INTO `pais` VALUES (158,886,'TW','TWN','Taiwan','Taiwan');");
mysqli_query($db, "INSERT INTO `pais` VALUES (162,61,'CX','CXR','Ilha Christmas','Território da Ilha Christmas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (166,672,'CC','CCK','Ilhas Cocos (Keeling)','Território das Ilhas Cocos (Keeling)');");
mysqli_query($db, "INSERT INTO `pais` VALUES (170,57,'CO','COL','Colômbia','República da Colômbia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (174,269,'KM','COM','Comores','União das Comores');");
mysqli_query($db, "INSERT INTO `pais` VALUES (175,269,'YT','MYT','Mayotte','Departamento de Mayotte');");
mysqli_query($db, "INSERT INTO `pais` VALUES (178,242,'CG','COG','Congo','República do Congo');");
mysqli_query($db, "INSERT INTO `pais` VALUES (180,242,'CD','COD','Congo (DR)','República Democrática do Congo');");
mysqli_query($db, "INSERT INTO `pais` VALUES (184,682,'CK','COK','Ilhas Cook','Ilhas Cook');");
mysqli_query($db, "INSERT INTO `pais` VALUES (188,506,'CR','CRI','Costa Rica','República da Costa Rica');");
mysqli_query($db, "INSERT INTO `pais` VALUES (191,385,'HR','HRV','Croácia','República da Croácia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (192,53,'CU','CUB','Cuba','República de Cuba');");
mysqli_query($db, "INSERT INTO `pais` VALUES (196,357,'CY','CYP','Chipre','República do Chipre');");
mysqli_query($db, "INSERT INTO `pais` VALUES (203,420,'CZ','CZE','República Tcheca','República Tcheca');");
mysqli_query($db, "INSERT INTO `pais` VALUES (204,229,'BJ','BEN','Benin','República do Benin');");
mysqli_query($db, "INSERT INTO `pais` VALUES (208,45,'DK','DNK','Dinamarca','Reino da Dinamarca');");
mysqli_query($db, "INSERT INTO `pais` VALUES (212,1767,'DM','DMA','Dominica','Comunidade da Dominica');");
mysqli_query($db, "INSERT INTO `pais` VALUES (214,1809,'DO','DOM','República Dominicana','República Dominicana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (218,593,'EC','ECU','Equador','República do Equador');");
mysqli_query($db, "INSERT INTO `pais` VALUES (222,503,'SV','SLV','El Salvador','República El Salvador');");
mysqli_query($db, "INSERT INTO `pais` VALUES (226,240,'GQ','GNQ','Guiné Equatorial','República do Guiné Equatorial');");
mysqli_query($db, "INSERT INTO `pais` VALUES (231,251,'ET','ETH','Etiópia','República Democrática Federal da Etiópia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (232,291,'ER','ERI','Eritreia','Estado da Eritreia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (233,372,'EE','EST','Estônia','República da Estônia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (234,298,'FO','FRO','Ilhas Faroe','Ilhas Faroe');");
mysqli_query($db, "INSERT INTO `pais` VALUES (238,500,'FK','FLK','Ilhas Malvinas','Ilhas Malvinas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (239,500,'GS','SGS','Ilhas Geórgia do Sul e Sandwich do Sul','Ilhas Geórgia do Sul e Sandwich do Sul');");
mysqli_query($db, "INSERT INTO `pais` VALUES (242,679,'FJ','FJI','Fiji','República do Fiji');");
mysqli_query($db, "INSERT INTO `pais` VALUES (246,358,'FI','FIN','Finlândia','República da Finlândia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (250,33,'FR','FRA','França','República Francesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (254,594,'GF','GUF','Guiana Francesa','Guiana Francesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (258,689,'PF','PYF','Polinésia Francesa','Polinésia Francesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (260,33,'TF','ATF','Terras Austrais e Antárticas Francesas','Território das Terras Austrais e Antárticas Francesas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (262,253,'DJ','DJI','Djibuti','República do Djibuti');");
mysqli_query($db, "INSERT INTO `pais` VALUES (266,241,'GA','GAB','Gabão','República Gabonesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (268,995,'GE','GEO','Geórgia','Geórgia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (270,220,'GM','GMB','Gâmbia','República da Gâmbia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (275,970,'PS','PSE','Palestina','Estado da Palestina');");
mysqli_query($db, "INSERT INTO `pais` VALUES (276,49,'DE','DEU','Alemanha','República Federal da Alemanha');");
mysqli_query($db, "INSERT INTO `pais` VALUES (288,233,'GH','GHA','Gana','Repúblia de Gana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (292,350,'GI','GIB','Gibraltar','Gibraltar');");
mysqli_query($db, "INSERT INTO `pais` VALUES (296,686,'KI','KIR','Kiribati','República do Kiribati');");
mysqli_query($db, "INSERT INTO `pais` VALUES (300,30,'GR','GRC','Grécia','República Helênica');");
mysqli_query($db, "INSERT INTO `pais` VALUES (304,299,'GL','GRL','Groelândia','Groelândia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (308,1473,'GD','GRD','Granada','Granada');");
mysqli_query($db, "INSERT INTO `pais` VALUES (312,590,'GP','GLP','Guadalupe','Guadalupe');");
mysqli_query($db, "INSERT INTO `pais` VALUES (316,1671,'GU','GUM','Guão','Território do Guão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (320,502,'GT','GTM','Guatemala','República da Guatemala');");
mysqli_query($db, "INSERT INTO `pais` VALUES (324,224,'GN','GIN','Guiné','República do Guiné');");
mysqli_query($db, "INSERT INTO `pais` VALUES (328,592,'GY','GUY','Guiana','República Cooperativa da Guiana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (332,509,'HT','HTI','Haiti','República do Haiti');");
mysqli_query($db, "INSERT INTO `pais` VALUES (334,672,'HM','HMD','Ilhas Heard e McDonald','Território das Ilhas Heard e McDonald');");
mysqli_query($db, "INSERT INTO `pais` VALUES (336,39,'VA','VAT','Vaticano','Estado da Cidade do Vaticano');");
mysqli_query($db, "INSERT INTO `pais` VALUES (340,504,'HN','HND','Honduras','República de Honduras');");
mysqli_query($db, "INSERT INTO `pais` VALUES (344,852,'HK','HKG','Hong Kong','Região Administrativa Especial de Hong Kong da República Popular da China');");
mysqli_query($db, "INSERT INTO `pais` VALUES (348,36,'HU','HUN','Hungria','Hungria');");
mysqli_query($db, "INSERT INTO `pais` VALUES (352,354,'IS','ISL','Islândia','Islândia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (356,91,'IN','IND','Índia','República da Índia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (360,62,'ID','IDN','Indonésia','República da Indonésia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (364,98,'IR','IRN','Iran','República Islâmica do Iran');");
mysqli_query($db, "INSERT INTO `pais` VALUES (368,964,'IQ','IRQ','Iraque','República do Iraque');");
mysqli_query($db, "INSERT INTO `pais` VALUES (372,353,'IE','IRL','Irlanda','Irlanda');");
mysqli_query($db, "INSERT INTO `pais` VALUES (376,972,'IL','ISR','Israel','Estado de Israel');");
mysqli_query($db, "INSERT INTO `pais` VALUES (380,39,'IT','ITA','Itália','República Italiana');");
mysqli_query($db, "INSERT INTO `pais` VALUES (384,225,'CI','CIV','Costa do Marfim','República da Costa do Marfim');");
mysqli_query($db, "INSERT INTO `pais` VALUES (388,1876,'JM','JAM','Jamaica','Jamaica');");
mysqli_query($db, "INSERT INTO `pais` VALUES (392,81,'JP','JPN','Japão','Japão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (398,7,'KZ','KAZ','Cazaquistão','República do Cazaquistão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (400,962,'JO','JOR','Jordânia','Reino Hachemita da Jordânia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (404,254,'KE','KEN','Quênia','República do Quênia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (408,850,'KP','PRK','Coreia do Norte','República Democrática Popular da Coreia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (410,82,'KR','KOR','Coreia do Sul','República da Coreia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (414,965,'KW','KWT','Kuwait','Estado do Kuwait');");
mysqli_query($db, "INSERT INTO `pais` VALUES (417,996,'KG','KGZ','Quirguistão','República Quirguiz');");
mysqli_query($db, "INSERT INTO `pais` VALUES (418,856,'LA','LAO','Laos','República Democrática Popular Lau');");
mysqli_query($db, "INSERT INTO `pais` VALUES (422,961,'LB','LBN','Líbano','República Libanesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (426,266,'LS','LSO','Lesoto','Reino do Lesoto');");
mysqli_query($db, "INSERT INTO `pais` VALUES (428,371,'LV','LVA','Letônia','República da Letônia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (430,231,'LR','LBR','Libéria','República da Libéria');");
mysqli_query($db, "INSERT INTO `pais` VALUES (434,218,'LY','LBY','Líbia','Líbia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (438,423,'LI','LIE','Liechtenstein','Principado de Liechtenstein');");
mysqli_query($db, "INSERT INTO `pais` VALUES (440,370,'LT','LTU','Lituânia','República da Lituânia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (442,352,'LU','LUX','Luxemburgo','Grão-Ducado do Luxemburgo');");
mysqli_query($db, "INSERT INTO `pais` VALUES (446,853,'MO','MAC','Macao','Macao');");
mysqli_query($db, "INSERT INTO `pais` VALUES (450,261,'MG','MDG','Madagascar','República de Madagascar');");
mysqli_query($db, "INSERT INTO `pais` VALUES (454,265,'MW','MWI','Malawi','República de Malawi');");
mysqli_query($db, "INSERT INTO `pais` VALUES (458,60,'MY','MYS','Malásia','Malásia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (462,960,'MV','MDV','Maldivas','Reública de Maldivas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (466,223,'ML','MLI','Mali','República do Mali');");
mysqli_query($db, "INSERT INTO `pais` VALUES (470,356,'MT','MLT','Malta','República de Malta');");
mysqli_query($db, "INSERT INTO `pais` VALUES (474,596,'MQ','MTQ','Martinica','Martinica');");
mysqli_query($db, "INSERT INTO `pais` VALUES (478,222,'MR','MRT','Mauritânia','República Islâmica da Mauritânia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (480,230,'MU','MUS','Maurício','República de Maurício');");
mysqli_query($db, "INSERT INTO `pais` VALUES (484,52,'MX','MEX','México','Estados Unidos Mexicanos');");
mysqli_query($db, "INSERT INTO `pais` VALUES (492,377,'MC','MCO','Mônaco','Principado de Mônaco');");
mysqli_query($db, "INSERT INTO `pais` VALUES (496,976,'MN','MNG','Mongólia','Mongólia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (498,373,'MD','MDA','Moldova','República de Moldova');");
mysqli_query($db, "INSERT INTO `pais` VALUES (500,1664,'MS','MSR','Montserrat','Montserrat');");
mysqli_query($db, "INSERT INTO `pais` VALUES (504,212,'MA','MAR','Marrocos','Reino de Marrocos');");
mysqli_query($db, "INSERT INTO `pais` VALUES (508,258,'MZ','MOZ','Moçambique','República de Moçambique');");
mysqli_query($db, "INSERT INTO `pais` VALUES (512,968,'OM','OMN','Omã','Sultanato de Omã');");
mysqli_query($db, "INSERT INTO `pais` VALUES (516,264,'NA','NAM','Namíbia','República da Namíbia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (520,674,'NR','NRU','Nauru','República de Nauru');");
mysqli_query($db, "INSERT INTO `pais` VALUES (524,977,'NP','NPL','Nepal','República Democrática Federativa do Nepal');");
mysqli_query($db, "INSERT INTO `pais` VALUES (528,31,'NL','NLD','Holanda','Holanda');");
mysqli_query($db, "INSERT INTO `pais` VALUES (530,599,'AN','ANT','Antilhas Holandesas','Antilhas Holandesas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (533,297,'AW','ABW','Aruba','Aruba');");
mysqli_query($db, "INSERT INTO `pais` VALUES (540,687,'NC','NCL','Nova Caledônia','Nova Caledônia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (548,678,'VU','VUT','Vanuatu','República de Vanuatu');");
mysqli_query($db, "INSERT INTO `pais` VALUES (554,64,'NZ','NZL','Nova Zelândia','Nova Zelândia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (558,505,'NI','NIC','Nicarágua','República da Nicarágua');");
mysqli_query($db, "INSERT INTO `pais` VALUES (562,227,'NE','NER','Niger','República do Niger');");
mysqli_query($db, "INSERT INTO `pais` VALUES (566,234,'NG','NGA','Nigéria','República Federativa da Nigéria');");
mysqli_query($db, "INSERT INTO `pais` VALUES (570,683,'NU','NIU','Niue','Niue');");
mysqli_query($db, "INSERT INTO `pais` VALUES (574,672,'NF','NFK','Ilha Norfolk','Território da Ilha Norfolk');");
mysqli_query($db, "INSERT INTO `pais` VALUES (578,47,'NO','NOR','Noruega','Reino da Noruega');");
mysqli_query($db, "INSERT INTO `pais` VALUES (580,1670,'MP','MNP','Ilhas Marianas do Norte','Comunidade das Ilhas Marianas do Norte');");
mysqli_query($db, "INSERT INTO `pais` VALUES (581,1,'UM','UMI','Ilhas Menores Distantes dos Estados Unidos','Ilhas Menores Distantes dos Estados Unidos');");
mysqli_query($db, "INSERT INTO `pais` VALUES (583,691,'FM','FSM','Micronésia','Estados Federados da Micronesia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (584,692,'MH','MHL','Ilhas Marshall','República das Ilhas Marshall');");
mysqli_query($db, "INSERT INTO `pais` VALUES (585,680,'PW','PLW','Palau','República de Palau');");
mysqli_query($db, "INSERT INTO `pais` VALUES (586,92,'PK','PAK','Paquistão','República Islâmica do Paquistão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (591,507,'PA','PAN','Panamá','República do Panamá');");
mysqli_query($db, "INSERT INTO `pais` VALUES (598,675,'PG','PNG','Papua-Nova Guiné','Estado Independente da Papua-Nova Guiné');");
mysqli_query($db, "INSERT INTO `pais` VALUES (600,595,'PY','PRY','Paraguai','República do Paraguai');");
mysqli_query($db, "INSERT INTO `pais` VALUES (604,51,'PE','PER','Peru','República do Peru');");
mysqli_query($db, "INSERT INTO `pais` VALUES (608,63,'PH','PHL','Filipinas','República das Filipinas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (612,672,'PN','PCN','Ilhas Picárnia','Ilhas Picárnia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (616,48,'PL','POL','Polônia','República da Polônia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (620,351,'PT','PRT','Portugal','República Portuguesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (624,245,'GW','GNB','Guiné-Bissau','República da Guiné-Bissau');");
mysqli_query($db, "INSERT INTO `pais` VALUES (626,670,'TL','TLS','Timor-Leste','República Democrática de Timor-Leste');");
mysqli_query($db, "INSERT INTO `pais` VALUES (630,1787,'PR','PRI','Porto Rico','Comunidade do Porto Rico');");
mysqli_query($db, "INSERT INTO `pais` VALUES (634,974,'QA','QAT','Catar','Estado do Catar');");
mysqli_query($db, "INSERT INTO `pais` VALUES (638,262,'RE','REU','Reunião','Polônia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (642,40,'RO','ROM','Romênia','Romênia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (643,70,'RU','RUS','Rússia','Federação Russa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (646,250,'RW','RWA','Ruanda','República da Ruanda');");
mysqli_query($db, "INSERT INTO `pais` VALUES (654,290,'SH','SHN','Santa Helena','Saint Helena');");
mysqli_query($db, "INSERT INTO `pais` VALUES (659,1869,'KN','KNA','São Cristóvão','São Cristóvão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (660,1264,'AI','AIA','Anguilla','Anguilla');");
mysqli_query($db, "INSERT INTO `pais` VALUES (662,1758,'LC','LCA','Santa Lúcia','Santa Lúcia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (666,508,'PM','SPM','São Pedro e Miquelon','Coletividade Territorial de São Pedro e Miquelon');");
mysqli_query($db, "INSERT INTO `pais` VALUES (670,1784,'VC','VCT','São Vicente e Granadinas','São Vicente e Granadinas');");
mysqli_query($db, "INSERT INTO `pais` VALUES (674,378,'SM','SMR','São Marino','República de São Marino');");
mysqli_query($db, "INSERT INTO `pais` VALUES (678,239,'ST','STP','Sao Tomé e Príncipe','República Democrática de Sao Tomé e Príncipe');");
mysqli_query($db, "INSERT INTO `pais` VALUES (682,966,'SA','SAU','Arábia Saudita','Reino da Arábia Saudita');");
mysqli_query($db, "INSERT INTO `pais` VALUES (686,221,'SN','SEN','Senegal','República do Senegal');");
mysqli_query($db, "INSERT INTO `pais` VALUES (688,381,'CS','SRB','Sérvia e Montenegro','União Estatal de Sérvia e Montenegro');");
mysqli_query($db, "INSERT INTO `pais` VALUES (690,248,'SC','SYC','Seicheles','República das Seicheles');");
mysqli_query($db, "INSERT INTO `pais` VALUES (694,232,'SL','SLE','República da Serra Leoa','República da Serra Leoa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (702,65,'SG','SGP','Singapura','República da Singapura');");
mysqli_query($db, "INSERT INTO `pais` VALUES (703,421,'SK','SVK','Eslováquia','República Eslovaca');");
mysqli_query($db, "INSERT INTO `pais` VALUES (704,84,'VN','VNM','Vietnam','República Socialista do Vietnam');");
mysqli_query($db, "INSERT INTO `pais` VALUES (705,386,'SI','SVN','Eslovênia','República da Eslovênia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (706,252,'SO','SOM','Somália','República da Somália');");
mysqli_query($db, "INSERT INTO `pais` VALUES (710,27,'ZA','ZAF','África do Sul','República da África do Sul');");
mysqli_query($db, "INSERT INTO `pais` VALUES (716,263,'ZW','ZWE','Zimbábue','República do Zimbábue');");
mysqli_query($db, "INSERT INTO `pais` VALUES (724,34,'ES','ESP','Espanha','Reino da Espanha');");
mysqli_query($db, "INSERT INTO `pais` VALUES (732,212,'EH','ESH','Saara Ocidental','Saara Ocidental');");
mysqli_query($db, "INSERT INTO `pais` VALUES (736,249,'SD','SDN','Sudão','República do Sudão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (740,597,'SR','SUR','Suriname','República do Suriname');");
mysqli_query($db, "INSERT INTO `pais` VALUES (744,47,'SJ','SJM','Esvalbarde','Esvalbarde');");
mysqli_query($db, "INSERT INTO `pais` VALUES (748,268,'SZ','SWZ','Suazilândia','Reino da Suazilândia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (752,46,'SE','SWE','Suécia','Reino da Suécia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (756,41,'CH','CHE','Suiça','Confederação Suiça');");
mysqli_query($db, "INSERT INTO `pais` VALUES (760,963,'SY','SYR','Síria','República Árabe Síria');");
mysqli_query($db, "INSERT INTO `pais` VALUES (762,992,'TJ','TJK','Tajiquistão','República do Tajiquistão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (764,66,'TH','THA','Tailândia','Reino da Tailândia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (768,228,'TG','TGO','Togo','República Togolesa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (772,690,'TK','TKL','Toquelau','Toquelau');");
mysqli_query($db, "INSERT INTO `pais` VALUES (776,676,'TO','TON','Tonga','Reino de Tonga');");
mysqli_query($db, "INSERT INTO `pais` VALUES (780,1868,'TT','TTO','Trinidad e Tobago','República da Trinidad e Tobago');");
mysqli_query($db, "INSERT INTO `pais` VALUES (784,971,'AE','ARE','Emirados Árabes','Emirados Árabes Unidos');");
mysqli_query($db, "INSERT INTO `pais` VALUES (788,216,'TN','TUN','Tunísia','República da Tunísia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (792,90,'TR','TUR','Turquia','República da Turquia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (795,7370,'TM','TKM','Turcomenistão','Turcomenistão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (796,1649,'TC','TCA','Ilhas Turks e Caicos','Ilhas Turks e Caicos');");
mysqli_query($db, "INSERT INTO `pais` VALUES (798,688,'TV','TUV','Tuvalu','Tuvalu');");
mysqli_query($db, "INSERT INTO `pais` VALUES (800,256,'UG','UGA','Uganda','República de Uganda');");
mysqli_query($db, "INSERT INTO `pais` VALUES (804,380,'UA','UKR','Ucrânia','Ucrânia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (807,389,'MK','MKD','Macedônia','República da Macedônia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (818,20,'EG','EGY','Egito','República Árabe do Egito');");
mysqli_query($db, "INSERT INTO `pais` VALUES (826,44,'GB','GBR','Reino Unido','Reino Unido da Grã-Bretanha e Irlanda do Norte');");
mysqli_query($db, "INSERT INTO `pais` VALUES (834,255,'TZ','TZA','Tanzânia','República Unida da Tanzânia');");
mysqli_query($db, "INSERT INTO `pais` VALUES (840,1,'US','USA','Estados Unidos','Estados Unidos da América');");
mysqli_query($db, "INSERT INTO `pais` VALUES (850,1340,'VI','VIR','Ilhas Virgens (USA)','Ilhas Virgens dos Estados Unidos');");
mysqli_query($db, "INSERT INTO `pais` VALUES (854,226,'BF','BFA','Burkina Faso','Burkina Faso');");
mysqli_query($db, "INSERT INTO `pais` VALUES (858,598,'UY','URY','Uruguai','República Oriental do Uruguai');");
mysqli_query($db, "INSERT INTO `pais` VALUES (860,998,'UZ','UZB','Uzbequistão','República do Uzbequistão');");
mysqli_query($db, "INSERT INTO `pais` VALUES (862,58,'VE','VEN','Venezuela','República Bolivariana da Venezuela');");
mysqli_query($db, "INSERT INTO `pais` VALUES (876,681,'WF','WLF','Wallis e Futuna','Wallis e Futuna');");
mysqli_query($db, "INSERT INTO `pais` VALUES (882,684,'WS','WSM','Samoa','Estado Independente de Samoa');");
mysqli_query($db, "INSERT INTO `pais` VALUES (887,967,'YE','YEM','Iêmen','República do Iêmen');");
mysqli_query($db, "INSERT INTO `pais` VALUES (894,260,'ZM','ZMB','Zâmbia','República do Zâmbia');");

unlink('/var/www/html/install_mariadb.php');
?>