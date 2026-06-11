# Backend SuperPDP pour InvoicePlane

Ce backend utilise les elements verifies pendant les tests :

- OAuth2 client credentials : `POST https://api.superpdp.tech/oauth2/token`
- Verification session : `GET /v1.beta/oauth2_sessions/me`
- Envoi facture : `POST /v1.beta/invoices`
- Payload : PDF Factur-X brut avec `Content-Type: application/pdf`
- Statut : `GET /v1.beta/invoices/{id}`
- Evenements : `GET /v1.beta/invoice_events`

## Reglages dans InvoicePlane

Dans `index.php/pdp/settings` :

```text
Backend : SuperPDP
Activer : oui
Authentification : OAuth2 client credentials
URL API de base : https://api.superpdp.tech
OAuth token URL : https://api.superpdp.tech/oauth2/token
Endpoint envoi facture : /v1.beta/invoices
Endpoint statut facture : /v1.beta/invoices/{id}
Endpoint reception fournisseurs : /v1.beta/invoices
Endpoint evenements/statuts : /v1.beta/invoice_events
Client ID : fourni par SuperPDP
Client Secret : fourni par SuperPDP
SuperPDP disable_pre_check : 0 en production, 1 pour tests
```

## Important sur `disable_pre_check`

`disable_pre_check=1` permet de tester l'envoi meme si le destinataire n'est pas encore dans l'annuaire Peppol.
En production, il faut utiliser `0` et renseigner correctement l'adresse electronique acheteur dans le Factur-X.

## Donnees Factur-X obligatoires validees par SuperPDP

Le PDF envoye doit contenir `factur-x.xml`. Verification :

```bash
pdfdetach -list Facture.pdf
```

SuperPDP controle notamment :

- `Seller.ElectronicAddress`
- `Seller.LegalRegistrationIdentifier`
- correspondance entre l'entreprise de la session OAuth2 et le vendeur du XML
- adresse electronique acheteur presente dans l'annuaire Peppol, sauf si `disable_pre_check=1`

Pour la France :

```text
schemeID 0002 = SIREN
schemeID 0009 = SIRET
```

## Test manuel equivalent

```bash
TOKEN=$(curl -s -X POST "https://api.superpdp.tech/oauth2/token" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -u "CLIENT_ID:CLIENT_SECRET" \
  -d "grant_type=client_credentials" | jq -r '.access_token')

curl -X POST "https://api.superpdp.tech/v1.beta/invoices?external_id=IP-2508&disable_pre_check=true" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/pdf" \
  --data-binary "@Facture.pdf"
```

Reponse attendue :

```json
{
  "id": 68597,
  "events": [{"status_code":"api:uploaded","status_text":"Téléversée"}],
  "direction":"out",
  "external_id":"IP-2508"
}
```

Le module stocke l'id distant dans `ip_pdp_transmissions.external_id` et, si la migration 004 est appliquee, les champs `status_code`, `status_text`, `direction`, `invoiceplane_external_id`.
