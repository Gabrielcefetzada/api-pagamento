<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferência Realizada com Sucesso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 10px 0;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
        }
        .content p {
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 0 0 5px 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Transferência Realizada com Sucesso</h1>
    </div>
    <div class="content">
        <p>Olá {{ $transference->payee->name }},</p>
        <p>Estamos felizes em informar que você recebeu uma transferência. Abaixo estão os detalhes da transação:</p>
        <p><strong>ID da Transação:</strong> {{ $transference->id }}</p>
        <p><strong>Valor:</strong> R$ {{ ($transference->amount)/100 }}</p>
        <p><strong>Pagador:</strong> {{ $transference->payer->name }}</p>
        <p><strong>Recebedor:</strong> {{ $transference->payee->name }}</p>
        <p><strong>Data e Hora:</strong> {{ $transference->created_at->format('d/m/Y H:i:s') }}</p>
        <p>Se você não reconhece esta transação, por favor, entre em contato conosco imediatamente.</p>
        <p>Obrigado por usar nossos serviços!</p>
    </div>
    <div class="footer">
        <p>Atendimento Api de Pagamento</p>
        <p><a href="mailto:atendimento@pagamento.com">atendimento@pagamento.com</a></p>
    </div>
</div>
</body>
</html>

