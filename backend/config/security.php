<?php

return [
    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Lista de IPs autorizados a acessar a aplicação.
    | Suporta diferentes formatos:
    | - IP exato: '192.168.1.100'
    | - Range CIDR: '192.168.1.0/24'
    | - Wildcard: '192.168.1.*'
    | - Localhost: 'localhost', '127.0.0.1', '::1'
    |
    */
    'whitelist_ips' => [
        // IPs locais de desenvolvimento
        '127.0.0.1',
        '::1',
        'localhost',

        // IPs específicos de produção
        // '203.0.113.10',
        // '203.0.113.11',

        // Range de IPs da rede interna
        // '192.168.1.0/24',
        // '10.0.0.0/8',

        // IPs com wildcard
        // '192.168.1.*',

        // Adicione aqui os IPs autorizados do seu ambiente
        // Exemplo de IPs que podem acessar:
        // env('ALLOWED_IP_1'),
        // env('ALLOWED_IP_2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist Enabled
    |--------------------------------------------------------------------------
    |
    | Define se o middleware de whitelist de IP está ativo.
    | Útil para desabilitar em desenvolvimento local.
    |
    */
    'ip_whitelist_enabled' => env('IP_WHITELIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | IPs de proxies confiáveis (Load Balancers, CDN, etc)
    | para obter o IP real do cliente
    |
    */
    'trusted_proxies' => [
        // Cloudflare IPs
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '108.162.192.0/18',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '162.158.0.0/15',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '188.114.96.0/20',
        '190.93.240.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',

        // AWS Load Balancer (exemplo)
        // '10.0.0.0/8',

        // Nginx/Apache proxy local
        // '127.0.0.1',
    ],
];
