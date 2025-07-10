# ShieldPHP

Middleware PHP para proteger aplicações web contra abusos, ataques e comportamentos maliciosos.

## Funcionalidades

- Rate limiting por IP
- Regras de bloqueio
- Detecção de padrões maliciosos (XSS, SQLi, etc.)
- Cache inteligente via Redis

## Instalação

```bash
composer install
```

## Desenvolvimento com Docker

```bash
docker-compose up --build
```

## Licença

MIT

## Métricas e Observabilidade

O ShieldPHP expõe métricas no formato Prometheus em `/metrics`.

### Exemplo de métricas expostas

```
# HELP shieldphp_blocked_requests_total Total blocked requests
# TYPE shieldphp_blocked_requests_total counter
shieldphp_blocked_requests_total 42

# HELP shieldphp_blocked_requests_rate_limit Blocked requests by rate limit
# TYPE shieldphp_blocked_requests_rate_limit counter
shieldphp_blocked_requests_rate_limit 10

# HELP shieldphp_blocked_requests_waf Blocked requests by WAF
# TYPE shieldphp_blocked_requests_waf counter
shieldphp_blocked_requests_waf 32

# HELP shieldphp_blocked_ips_total Total unique blocked IPs
# TYPE shieldphp_blocked_ips_total gauge
shieldphp_blocked_ips_total 5
```

### Como visualizar no Grafana

1. Suba o ambiente com Docker Compose:
   ```bash
   docker-compose up --build
   ```
2. Acesse o Prometheus em [http://localhost:9090](http://localhost:9090)
3. Acesse o Grafana em [http://localhost:3000](http://localhost:3000) (login padrão: admin / admin)
4. Adicione o Prometheus como fonte de dados no Grafana (URL: `http://prometheus:9090`)
5. Crie dashboards usando as métricas:
   - `shieldphp_blocked_requests_total`
   - `shieldphp_blocked_requests_rate_limit`
   - `shieldphp_blocked_requests_waf`
   - `shieldphp_blocked_ips_total`

### Exemplo de painel no Grafana

- Gráfico de requisições bloqueadas ao longo do tempo:
  - Query: `increase(shieldphp_blocked_requests_total[5m])`
- Contador de IPs bloqueados:
  - Query: `shieldphp_blocked_ips_total`

## Configurações disponíveis

- `env`: Ambiente de execução (`dev`, `prod`)
- `debug`: Ativa/desativa logs de debug
- `log_file`: Caminho do arquivo de log
- Regras customizadas: via `rules.json` ou `rules.yaml`

Consulte os arquivos de exemplo e a documentação das classes para detalhes sobre cada configuração e regra.
