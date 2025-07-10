#!/bin/bash
# Benchmark com ApacheBench
# Exemplo de uso: ./bench/ab-test.sh

ab -n 1000 -c 50 http://localhost:8080/metrics 