# 📖 Bible Notes

## Requirements

| Dependency                    | Version |
|-------------------------------|---------|
| [Node.js](https://nodejs.org) | ^19.4.0 |
| [Lando](https://lando.dev/)   | ^3.6.0  |

## Getting Started

1. Copy `.env.example` to `.env` and update the values

        cp .env.example .env

2. Start the services

       lando start

3. Install the project dependencies

       npm install

5. Pull the site data

       npm run pull

## Deploy

Push themes and plugins to the wp directory:

```
npm run push
```

Then commit and push your changes in the wp directory to deploy them.
