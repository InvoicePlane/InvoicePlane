# Interface simplifiee des reglages PDP

Cette version masque automatiquement les champs inutiles selon le backend et le type d'authentification.

## SuperPDP

Quand le backend `SuperPDP` est choisi, l'interface force l'authentification sur `OAuth2 client credentials` et affiche uniquement les champs utiles :

- URL API de base
- Endpoint envoi facture
- Endpoint statut facture
- Endpoint reception fournisseurs
- Endpoint evenements/statuts
- Desactiver le pre-check annuaire Peppol
- OAuth token URL
- Client ID
- Client Secret
- Scope OAuth

Les champs suivants sont masques car inutiles pour SuperPDP :

- Access token statique
- API key
- Header API key
- Champ fichier multipart
- Payload JSON additionnel

## Valeurs SuperPDP pre-remplies

- URL API de base : `https://api.superpdp.tech`
- OAuth token URL : `https://api.superpdp.tech/oauth2/token`
- Endpoint envoi : `/v1.beta/invoices`
- Endpoint statut : `/v1.beta/invoices/{id}`
- Endpoint reception : `/v1.beta/invoices`
- Endpoint evenements : `/v1.beta/invoice_events`

## Test vs production

Pour les tests, `disable_pre_check` peut etre coche afin d'eviter le blocage annuaire Peppol.

En production, il faut le laisser decoche et renseigner correctement l'adresse electronique du client dans le Factur-X.
