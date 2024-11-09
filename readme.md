# Megio core
Most powerful tool for creating webs, apps & APIs.

- Docs: https://megio.dev
- Demo: https://demo.megio.dev (u: admin@test.cz p: Test1234)

## Installation guide
https://megio.dev/docs/getting-started/installation

## How to run tests
```bash
cp .env.example .env

# Init environment
make test-setup

# Run all tests
make test-full

# Or run specific test
make test-single FILE=tests/Collection/UpdateRowsTest.php
```
