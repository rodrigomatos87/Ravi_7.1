<script type="text/javascript" src="/var/www/html/js/jquery-3.3.1.js"></script>
<script>
    function enviar() {
        //alert("teste");
        let id = "<?=$_GET['id']?>";
        let message = "<?=$_GET['message']?>";
        if (id && message) {
            let data = {
                contact_id: id,
                message: message,
            }
            $.ajax({
                url: `/whatsapp/send`,
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(data),
                error: (res) => {
                    console.log('error', res.responseJSON ? res.responseJSON.message : 'Erro processar solicitação')
                },
                success: (res) => {
                    console.log('success', 'Mensagem enviada com sucesso')
                },
            })
        }
    }
    enviar();
</script>

