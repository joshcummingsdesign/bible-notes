# 📖 Bible Notes

## Requirements

| Dependency                    | Version |
| ----------------------------- | ------- |
| [Node.js](https://nodejs.org) | ^17.9.1 |
| [Lando](https://lando.dev/)   | ^3.6.0  |

## Getting Started

1.  Copy `.env.example` to `.env` and update the values

        cp .env.example .env

2.  Start the services

        lando start

3.  Install the project dependencies

        npm install

4.  Clone the wp directory (credentials in Pantheon)

## Pulling Site Data

```sh
npm run pull
```

## Deploy

Push themes and plugins to the wp directory:

```
npm run push
```

Then commit and push your changes in the wp directory to deploy them.
