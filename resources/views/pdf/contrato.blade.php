<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Contrato de Compra e Venda - {{ $contract->numero }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; line-height: 1.5; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 15px; }
        .header h1 { font-size: 20px; font-weight: bold; margin: 0; color: #1a3a5c; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; background: #f3f4f6; padding: 6px 10px; margin-bottom: 10px; border-left: 4px solid #1a3a5c; }
        .grid { display: block; width: 100%; margin-bottom: 5px; }
        .label { font-weight: bold; width: 140px; display: inline-block; color: #555; }
        .value { display: inline-block; }
        .text-content { text-align: justify; margin-top: 15px; font-size: 12px; }
        .clauses { padding-left: 20px; }
        .clauses li { margin-bottom: 10px; text-align: justify; }
        
        .signature-area { margin-top: 40px; border-top: 1px dashed #ccc; padding-top: 20px; page-break-inside: avoid; }
        .signatures-container { display: table; width: 100%; margin-top: 20px; }
        .signature-box { display: table-cell; width: 50%; vertical-align: top; text-align: center; }
        .signature-img { max-width: 200px; max-height: 80px; margin: 0 auto 10px; display: block; }
        .signature-line { border-top: 1px solid #333; width: 80%; margin: 10px auto 5px; }
        .signature-name { font-weight: bold; font-size: 12px; margin-bottom: 2px; }
        .signature-doc { font-size: 11px; color: #666; }
        
        .digital-hash { margin-top: 30px; font-size: 10px; color: #777; border: 1px solid #eee; padding: 10px; background: #fafafa; background-color: #f8fafc; border-radius: 4px; text-align: center; page-break-inside: avoid; }
        .digital-hash strong { color: #333; }
        
        .footer { position: fixed; bottom: 0px; left: 0px; right: 0px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
        .page-number:before { content: counter(page); }
    </style>
</head>
<body>

    <div class="footer">
        Documento gerado pelo Portal Elite Repasse — Página <span class="page-number"></span>
    </div>

    <div class="header">
        <h1>CONTRATO DE COMPRA E VENDA DE VEÍCULO AUTOMOTOR</h1>
        <p>Número de Registro: <strong>{{ $contract->numero }}</strong> | Data: {{ $contract->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">1. IDENTIFICAÇÃO DO VENDEDOR</div>
        <div class="grid"><span class="label">Empresa:</span> <span class="value">Elite Repasse de Veículos Ltda</span></div>
        <div class="grid"><span class="label">CNPJ:</span> <span class="value">00.000.000/0001-00</span></div>
        <div class="grid"><span class="label">Endereço:</span> <span class="value">Logradouro Padrão da Elite Repasse</span></div>
    </div>

    <div class="section">
        <div class="section-title">2. IDENTIFICAÇÃO DO COMPRADOR</div>
        @php
            $buyer = $contract->dados_comprador;
            $name = $buyer['razao_social'] ?? $buyer['name'] ?? 'Não informado';
            $doc = $buyer['cnpj'] ?? $buyer['cpf'] ?? 'Não informado';
            $endereco = implode(', ', array_filter([$buyer['logradouro'] ?? '', $buyer['numero'] ?? '', $buyer['bairro'] ?? '', $buyer['cidade'] ?? '', $buyer['estado'] ?? '']));
        @endphp
        <div class="grid"><span class="label">Nome/Razão Social:</span> <span class="value">{{ $name }}</span></div>
        <div class="grid"><span class="label">Documento:</span> <span class="value">{{ $doc }}</span></div>
        <div class="grid"><span class="label">E-mail:</span> <span class="value">{{ $buyer['email'] ?? 'Não informado' }}</span></div>
        <div class="grid"><span class="label">Telefone:</span> <span class="value">{{ $buyer['phone'] ?? 'Não informado' }}</span></div>
        <div class="grid"><span class="label">Endereço:</span> <span class="value">{{ $endereco ?: 'Não informado' }}</span></div>
    </div>

    <div class="section">
        <div class="section-title">3. DADOS DO VEÍCULO (OBJETO)</div>
        @php $veh = $contract->dados_veiculo; @endphp
        <div class="grid"><span class="label">Marca/Modelo:</span> <span class="value">{{ $veh['brand'] ?? '' }} {{ $veh['model'] ?? '' }} {{ $veh['version'] ?? '' }}</span></div>
        <div class="grid"><span class="label">Ano Fab./Mod.:</span> <span class="value">{{ $veh['manufacture_year'] ?? '' }} / {{ $veh['model_year'] ?? '' }}</span></div>
        <div class="grid"><span class="label">Placa:</span> <span class="value">{{ $veh['plate'] ?? '' }}</span></div>
        <div class="grid"><span class="label">Chassi:</span> <span class="value">{{ $veh['chassis'] ?? 'Verificar no documento original' }}</span></div>
        <div class="grid"><span class="label">Renavam:</span> <span class="value">{{ $veh['renavam'] ?? 'Verificar no documento original' }}</span></div>
        <div class="grid"><span class="label">Cor / Combustível:</span> <span class="value">{{ $veh['color'] ?? '' }} / {{ $veh['fuel_type'] ?? '' }}</span></div>
        <div class="grid"><span class="label">Quilometragem:</span> <span class="value">{{ number_format($veh['mileage'] ?? 0, 0, '', '.') }} km</span></div>
    </div>

    <div class="section">
        <div class="section-title">4. VALOR E FORMA DE PAGAMENTO</div>
        <div class="grid"><span class="label">Valor de Venda:</span> <span class="value"><strong>R$ {{ number_format((float) $contract->valor_contrato, 2, ',', '.') }}</strong></span></div>
        <div class="grid"><span class="label">Forma de Pagto:</span> <span class="value">{{ $contract->forma_pagamento }}</span></div>
    </div>

    <div class="section text-content">
        <div class="section-title">5. CLÁUSULAS E CONDIÇÕES</div>
        <p>Pelo presente instrumento particular, as partes nomeadas e qualificadas acima, ajustam a promessa de compra e venda do veículo acima descrito, mediante as seguintes cláusulas:</p>
        <ol class="clauses">
            <li><strong>Objeto:</strong> O VENDEDOR vende e entrega ao COMPRADOR o veículo acima descrito, livre e desembaraçado de quaisquer ônus, dívidas, multas ou restrições até a presente data.</li>
            <li><strong>Pagamento:</strong> O valor pactuado deverá ser pago conforme a modalidade escolhida informada na cláusula 4. O recibo de transferência só será preenchido após a devida compensação bancária e confirmação dos valores na conta do VENDEDOR.</li>
            <li><strong>Vistoria:</strong> O COMPRADOR declara ter vistoriado o veículo e ter ciência do seu atual estado de conservação (bens no estado em que se encontram - modalidade repasse), responsabilizando-se por custos de manutenções futuras.</li>
            <li><strong>Transferência:</strong> Fica sob a inteira e exclusiva responsabilidade do COMPRADOR, proceder com os trâmites legais para transferência do veículo para o seu nome junto ao DETRAN em até 30 (trinta) dias após a assinatura do Recibo/ATPV-e.</li>
            <li><strong>Assinatura Eletrônica:</strong> As partes reconhecem a validade deste documento e da assinatura eletrônica, bem como a precisão dos dados, com fé técnica garantida, em concordância com a legislação brasileira vigente, Medida Provisória nº 2.200-2 e Lei Federal 14.063/2020.</li>
        </ol>
        <p>Por estarem acordados, assinam eletronicamente o presente contrato digital para que surtam seus efeitos legais e jurídicos.</p>
    </div>

    <div class="signature-area">
        <div class="signatures-container">
            <div class="signature-box">
                <br><br><br>
                <div class="signature-line"></div>
                <div class="signature-name">ELITE REPASSE DE VEÍCULOS LTDA</div>
                <div class="signature-doc">CNPJ: 00.000.000/0001-00</div>
                <div class="signature-doc">(Vendedor Principal)</div>
            </div>
            
            <div class="signature-box">
                @if($signature)
                    <img src="{{ $signature->assinatura_base64 }}" class="signature-img" alt="Assinatura Comprador">
                @else
                    <br><br><br>
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $name }}</div>
                <div class="signature-doc">Documento: {{ $doc }}</div>
                <div class="signature-doc">(Comprador)</div>
            </div>
        </div>
        
        <div class="digital-hash">
            <strong>CERTIFICADO DE ASSINATURA ELETRÔNICA</strong><br>
            Este documento foi registrado, rastreado e assinado eletronicamente.<br>
            <strong>ID do Contrato:</strong> {{ $contract->numero }} | <strong>Hash Criptográfico:</strong> {{ $contract->hash_verificacao }}<br>
            <strong>IP Vendedor:</strong> Sistema Elite Repasse | <strong>Data Gerado:</strong> {{ $contract->created_at->format('d/m/Y H:i:s') }} <br>
            <strong>IP Assinatura:</strong> {{ $contract->ip_assinatura ?? 'Aguardando' }} | <strong>Data Assinatura:</strong> {{ $contract->assinado_em ? $contract->assinado_em->format('d/m/Y H:i:s') : 'Aguardando' }}<br>
            @if($contract->lat_assinatura && $contract->lng_assinatura)
            <strong>Geolocalização (Lat/Lng):</strong> {{ $contract->lat_assinatura }}, {{ $contract->lng_assinatura }}<br>
            <strong>Local (Aproximado):</strong> {{ $contract->endereco_assinatura }}
            @endif
        </div>
    </div>

</body>
</html>
