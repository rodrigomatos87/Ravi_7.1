<?php
include("/var/www/html/cron/apoio/conexao.php");

/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/

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

mysqli_query($db, "UPDATE system SET certificado_crt = '".$certificado_crt."', certificado_key = '".$certificado_key."';");
exec("php -f /var/www/html/cron/apoio/nginx.php > /dev/null &");

sleep(5);

mysqli_query($db, "ALTER TABLE onus ADD login varchar(600) DEFAULT NULL AFTER descr;");
mysqli_query($db, "ALTER TABLE Logalertas ADD enviadopush varchar(5) NOT NULL DEFAULT '0' AFTER enviadoWHATS;");

mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, minPer, qtdCol, un1, Col1, descr, exec, link) VALUES ('RX Power SFP Switch S6730', 'rxsfphuaweiS6730', 2, 2, 1, 1, '30', 1, ' dBm', 'RX Power', 'Monitora o sinal RX Power em interfaces SFP de um switch modelo S6730 usando SNMP', 0, 'rxsfphuaweiS6730.php');");

mysqli_query($db, "CREATE TABLE `grupos_disp` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) DEFAULT NULL,
    `foto` longblob DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('Sinal TX A/B', 'txsiae', 2, 2, 1, 1, '15', 2, ' dBm', ' dBm', 'TX Power A', 'TX Power B', 'Monitora a potência de transmissão de um rádio Siae usando SNMP', 2);");
mysqli_query($db, "INSERT INTO Sondas (nome, tag, alertar, tipo, peso, media, maxPer, qtdCol, un1, un2, Col1, Col2, descr, exec) VALUES ('Sinal RX A/B', 'rxsiae', 2, 2, 1, 1, '15', 2, ' dBm', ' dBm', 'RX Power A', 'RX Power B', 'Monitora a potência de recepção de um rádio Siae usando SNMP', 2);");

mysqli_query($db, "ALTER TABLE Sensores MODIFY ifSpeed varchar(50) DEFAULT NULL;");
mysqli_query($db, "ALTER TABLE Sensores MODIFY ifSpeedAlert varchar(50) DEFAULT NULL;");

mysqli_query($db, "ALTER TABLE onus MODIFY idOLT int(10) DEFAULT NULL;");
mysqli_query($db, "ALTER TABLE onus MODIFY idonu int(10) DEFAULT NULL;");
mysqli_query($db, "ALTER TABLE onus MODIFY idpon int(10) DEFAULT NULL;");
mysqli_query($db, "ALTER TABLE onus MODIFY idslot int(10) DEFAULT NULL;");
mysqli_query($db, "ALTER TABLE Logalertas ADD statusAlert varchar(2) DEFAULT NULL AFTER tipo;");
mysqli_query($db, "ALTER TABLE system ADD ativaWHATSdisp int(11) DEFAULT NULL AFTER historicotrafegocon;");
mysqli_query($db, "ALTER TABLE system ADD ativaWHATSolt int(10) unsigned DEFAULT NULL AFTER ativaWHATSdisp;");
mysqli_query($db, "ALTER TABLE system ADD ativaOokla integer(2) NOT NULL DEFAULT '0' AFTER ativaWHATSolt;");
mysqli_query($db, "ALTER TABLE system ADD foto longblob DEFAULT NULL AFTER ativaOokla;");
mysqli_query($db, "ALTER TABLE system ADD nomeApresentacao varchar(45) DEFAULT NULL AFTER foto;");
mysqli_query($db, "ALTER TABLE models_auth_onu ADD modelo varchar(2) DEFAULT NULL AFTER marca;");
mysqli_query($db, "ALTER TABLE system ADD subdom_ssl_ip6 varchar(150) DEFAULT NULL AFTER subdom_ssl_ip;");
mysqli_query($db, "ALTER TABLE whats ADD idApi int(10) unsigned DEFAULT NULL AFTER tipo;");
mysqli_query($db, "ALTER TABLE onus DROP PRIMARY KEY, ADD PRIMARY KEY(id, mac);");

mysqli_query($db, "ALTER TABLE system ADD prioridadewhats int(2) NOT NULL DEFAULT '1' AFTER ativaWHATS;");
mysqli_query($db, "ALTER TABLE system ADD prioridadewhatsdisp int(2) NOT NULL DEFAULT '1' AFTER ativaWHATSolt;");
mysqli_query($db, "ALTER TABLE system ADD prioridadewhatsolt int(2) NOT NULL DEFAULT '1' AFTER prioridadewhatsdisp;");
mysqli_query($db, "ALTER TABLE system ADD timezone varchar(50) NOT NULL DEFAULT 'America/Sao_Paulo' AFTER nomeApresentacao;");
mysqli_query($db, "ALTER TABLE system ADD linguagem int(2) NOT NULL DEFAULT '1' AFTER timezone;");

mysqli_query($db, "ALTER TABLE GrupoMonitor ADD prioridadewhats int(2) NOT NULL DEFAULT '1' AFTER ativaWHATSAPP;");

mysqli_query($db, "CREATE TABLE `pais` (
    `codigo` int(10) unsigned NOT NULL DEFAULT 0,
    `fone` int(10) unsigned DEFAULT NULL,
    `iso` varchar(45) DEFAULT NULL,
    `iso3` varchar(45) DEFAULT NULL,
    `nome` varchar(100) DEFAULT NULL,
    `nomeFormal` varchar(250) DEFAULT NULL,
    PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

mysqli_query($db, "ALTER TABLE GrupoMonitor ADD ativaWHATSAPP int(2) unsigned DEFAULT NULL AFTER criptosnmp_g;");
mysqli_query($db, "ALTER TABLE system ADD api_whats int(2) NOT NULL DEFAULT '0' AFTER api_dns;");
mysqli_query($db, "ALTER TABLE Dispositivos ADD backupDisp int(2) NOT NULL DEFAULT '0' AFTER portaLink;");
mysqli_query($db, "ALTER TABLE Dispositivos ADD ativa_auto int(10) unsigned DEFAULT NULL AFTER ordem;");

mysqli_query($db, "ALTER TABLE login ADD ddi int(10) NOT NULL DEFAULT '76' AFTER email;");

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


//mysqli_query($db, "UPDATE system SET backupConcentradoras = '1';");
//mysqli_query($db, "UPDATE system SET widthSensor = '150px', zoomsensor = '2';");
mysqli_query($db, "UPDATE system SET versao = '7.0', versaoData = '10/10/2022', versaoNova = '0', debug = '12';");

mysqli_close($db);
?>