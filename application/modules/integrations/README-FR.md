# Module eInvoice pour InvoicePlane

## Présentation

Le module **eInvoice** permet à InvoicePlane d'échanger des factures électroniques avec des plateformes de dématérialisation partenaires (PDP) et opérateurs compatibles.

Le module fournit :

* L'envoi de factures électroniques vers une PDP.
* Le suivi du statut des factures transmises.
* La synchronisation des événements de traitement.
* La récupération des factures entrantes.
* Une architecture extensible permettant l'ajout de nouveaux fournisseurs PDP.

---

# Architecture du module

## Structure des fichiers

```text
einvoice/
├── controllers/
│   ├── Einvoice.php
│   ├── Settings.php
│   ├── Sync.php
│   ├── Incoming.php
│   └── Events.php
│
├── libraries/
│   ├── IntegrationClient.php
│   ├── IntegrationClientInterface.php
│   ├── IntegrationClientRegistry.php
│   └── providers/
│       ├── SuperPdpClient.php
│       └── QontoClient.php
│
├── models/
│   ├── Merchant_clients_model.php
│   └── Merchant_responses_model.php
│
├── views/
│   ├── settings.php
│   ├── history.php
│   ├── events.php
│   ├── incoming.php
│   └── provider_form.php
│
└── migrations/
    └── einvoice.sql
```

---

# Fonctionnalités

## Envoi de factures

Le module permet d'envoyer automatiquement une facture PDF vers une plateforme partenaire.

Flux :

```text
InvoicePlane
     │
     ▼
eInvoice
     │
     ▼
PDP / Opérateur
     │
     ▼
Administration fiscale
```

Chaque envoi génère :

* un identifiant externe ;
* un statut ;
* un historique de transmission ;
* un journal des requêtes et réponses API.

---

## Synchronisation des statuts

Après l'envoi, le module peut interroger la plateforme afin de récupérer :

* facture reçue ;
* facture acceptée ;
* facture rejetée ;
* facture délivrée ;
* facture payée ;
* tout autre statut fourni par la PDP.

Les informations sont stockées dans :

```sql
ip_einvoice_responses
```

---

## Synchronisation des événements

Le contrôleur :

```text
Sync.php
```

permet de récupérer :

* les événements de facturation ;
* les changements d'état ;
* les notifications PDP.

Les événements sont historisés afin d'assurer une traçabilité complète.

---

## Réception des factures entrantes

Le contrôleur :

```text
Incoming.php
```

permet :

* d'interroger une PDP ;
* de récupérer les factures fournisseurs ;
* d'enregistrer les données reçues dans la base locale.

---

# Gestion des fournisseurs

## Architecture Provider

Chaque fournisseur doit implémenter :

```php
IntegrationClientInterface
```

Le registre :

```php
IntegrationClientRegistry
```

détecte automatiquement les nouveaux fournisseurs.

Lors du chargement :

```php
glob('*Client.php')
```

est utilisé pour enregistrer dynamiquement les fournisseurs disponibles.

---

# Fournisseurs actuellement disponibles

## SuperPDP

### Identification

```text
Code : superpdp
Nom  : SuperPDP
```

### Authentification

OAuth2 Client Credentials

Paramètres :

| Paramètre     | Description                |
| ------------- | -------------------------- |
| client_id     | Identifiant OAuth          |
| client_secret | Secret OAuth               |
| token_url     | URL de génération du token |
| api_base_url  | URL principale API         |

### Endpoints par défaut

```text
POST /v1.beta/invoices
GET  /v1.beta/invoices/{id}
GET  /v1.beta/invoices
GET  /v1.beta/invoice_events
```

### Options

```json
{
  "disable_pre_check": false
}
```

Permet de désactiver certains contrôles préalables côté SuperPDP.

---

## Qonto

### Identification

```text
Code : qonto
Nom  : Qonto PA
```

### Authentification

Bearer Token

Paramètres :

| Paramètre    | Description   |
| ------------ | ------------- |
| access_token | Jeton d'accès |
| api_base_url | URL API Qonto |

### Endpoints par défaut

```text
POST /v2/client_invoices/uploads
POST /v2/client_invoices
POST /v2/client_invoices/{id}/send_by_einvoice
GET  /v2/client_invoices/{id}
GET  /v2/supplier_invoices
```

### Particularité

Qonto nécessite :

1. Upload du PDF
2. Création de la facture
3. Envoi en e-invoicing

Le module exécute automatiquement ces trois étapes.

---

# Configuration

## Accès

Menu :

```text
Paramètres
→ eInvoice
```

---

## Activation d'un fournisseur

1. Ouvrir la configuration.
2. Sélectionner un fournisseur.
3. Saisir les informations d'authentification.
4. Activer le fournisseur.
5. Sauvegarder.

Un seul fournisseur peut être actif à la fois.

Lors de l'activation :

```php
enabled = 1
```

les autres fournisseurs sont automatiquement désactivés.

---

# Synchronisation manuelle

## Synchronisation complète

URL :

```text
einvoice/sync/run/{merchant_client_id}
```

Actions réalisées :

* récupération des factures entrantes ;
* récupération des événements ;
* mise à jour de l'historique.

---

## Synchronisation des factures entrantes

URL :

```text
einvoice/incoming/sync/{merchant_client_id}
```

Actions :

* interrogation PDP ;
* récupération des documents ;
* enregistrement local.

---

# Base de données

## Table ip_merchant_clients

Contient les configurations PDP.

### Champs

| Champ         | Description             |
| ------------- | ----------------------- |
| id            | Identifiant             |
| merchant_type | Type de fournisseur     |
| label         | Nom affiché             |
| enabled       | Actif ou non            |
| auth_type     | Type d'authentification |
| settings_json | Paramètres              |
| created_at    | Création                |
| updated_at    | Modification            |

---

## Table ip_einvoice_responses

Historique des échanges.

### Champs

| Champ              | Description           |
| ------------------ | --------------------- |
| id                 | Identifiant           |
| merchant_client_id | Fournisseur           |
| direction          | out / in              |
| record_type        | Type d'enregistrement |
| invoice_id         | Facture locale        |
| external_id        | Identifiant PDP       |
| status             | Statut                |
| message            | Message retour        |
| http_code          | Code HTTP             |
| request_json       | Requête               |
| response_json      | Réponse               |
| created_at         | Création              |
| updated_at         | Modification          |

---

# Ajout d'un nouveau fournisseur

Créer un fichier dont le nom se termine par `Client.php` :

```text
libraries/providers/MyClient.php
```

La classe fournit sa logique API et décrit les champs que le formulaire générique
doit afficher. Aucun contrôleur ni aucune vue ne doit être modifié.

```php
class MyClient implements IntegrationClientInterface
{
    public static function clientCode(): string
    {
        return 'myprovider';
    }

    public static function clientName(): string
    {
        return 'My Provider';
    }

    public static function authType(): string
    {
        return 'bearer';
    }

    public static function defaultSettings(): array
    {
        return [
            'api_token'   => '',
            'api_base_url' => 'https://api.example.com',
        ];
    }

    public static function settingsSchema(): array
    {
        return [
            'api_token' => [
                'type'      => 'password',
                'label'     => 'API Token',
                'required'  => true,
                'sensitive' => true,
            ],
            'api_base_url' => [
                'type'     => 'url',
                'label'    => 'api_base_url',
                'required' => true,
            ],
        ];
    }

    public function authenticate(array $settings): bool
    {
        return true;
    }

    public function sendInvoice(string $documentPath, array $metadata): array
    {
        return [];
    }

    public function getInvoiceStatus(string $externalId): array
    {
        return [];
    }

    public function receiveInvoices(array $filters = []): array
    {
        return [];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        return [];
    }

    public function buildInvoicePayload($invoice, array $items, array $metadata = []): array
    {
        return $metadata;
    }

    public function fetchToken(array $settings): string
    {
        return $settings['api_token'] ?? '';
    }
}
```

Types de champs disponibles : `text`, `password`, `url`, `path`, `checkbox`
et `select`. Le registre détecte le fichier, crée l'entrée en base et le formulaire
est construit à partir de `settingsSchema()`. Les champs sensibles ne sont jamais
réaffichés et une valeur vide conserve le secret enregistré.

---

# Sécurité

Le module supporte :

* OAuth2 Client Credentials ;
* Bearer Token ;
* API Key (prévu par l'architecture).

Les paramètres sensibles sont stockés dans :

```text
settings_json
```

de la table :

```text
ip_merchant_clients
```

---

# Journalisation

Chaque échange API conserve :

* la requête complète ;
* la réponse complète ;
* le code HTTP ;
* le statut métier ;
* l'identifiant externe.

Cela facilite :

* l'audit ;
* le support ;
* le diagnostic des erreurs.

---

# Cas d'usage

## Envoi d'une facture

```text
Facture PDF
      │
      ▼
eInvoice
      │
      ▼
SuperPDP / Qonto
      │
      ▼
Statut retourné
      │
      ▼
Historique InvoicePlane
```

---

## Réception d'une facture fournisseur

```text
PDP
 │
 ▼
Incoming Sync
 │
 ▼
Base locale
 │
 ▼
Consultation utilisateur
```

---

# Compatibilité actuelle

| Fournisseur | Envoi | Statut | Réception | Événements |
| ----------- | ----- | ------ | --------- | ---------- |
| SuperPDP    | Oui   | Oui    | Oui       | Oui        |
| Qonto       | Oui   | Oui    | Oui       | Oui        |

---

# Conclusion

Le module eInvoice fournit une couche d'intégration générique entre InvoicePlane et les plateformes de facturation électronique. Son architecture basée sur des fournisseurs (Providers) permet d'ajouter facilement de nouveaux opérateurs tout en conservant une interface unique pour l'utilisateur final.
