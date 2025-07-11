#!/bin/bash
# Benchmark with ApacheBench
# Example of use: ./bench/ab-test.sh

ab -n 1000 -c 50 http://localhost:8080/metrics 