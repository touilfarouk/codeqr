# Objectif de l'Application

L'application de QR Code a été développée pour ajouter une couche supplémentaire de sécurité à nos documents d'entreprise. En intégrant des QR Codes uniques à nos documents, nous pouvons non seulement tracer leur distribution mais également restreindre l'accès à des informations cruciales. Ce système vise à prévenir la duplication et la distribution non autorisées de nos documents confidentiels.

## Fonctionnalités Principales

- **Génération de QR Codes :** L'application permet de générer des QR Codes uniques pour chaque document. Ces codes sont liés à des informations spécifiques sur le document, y compris le destinataire et la date d'émission.

- **Sécurisation par Mot de Passe :** Chaque QR Code est sécurisé par un mot de passe généré aléatoirement, assurant que seuls les destinataires autorisés peuvent accéder au contenu du document.

- **Traçabilité :** L'application enregistre des détails tels que l'ID du document, le destinataire, la date d'émission, et plus encore. Cela nous permet de suivre la distribution des documents et d'identifier rapidement toute tentative d'accès non autorisé.

- **Stockage des Images QR :** Les images générées du QR Code sont stockées de manière sécurisée et peuvent être intégrées directement dans les documents ou imprimées selon les besoins.

## Structure de la Base de Données

Notre base de données contient les champs suivants pour stocker et gérer les informations relatives aux documents sécurisés :

- **ID :** Un identifiant unique pour chaque document.
- **Ref_Document :** La référence du document.
- **Date :** La date d'émission du document.
- **Destinataire :** La personne ou l'entité destinataire du document.
- **Objet :** Le sujet ou l'objet du document.
- **Password :** Un mot de passe généré aléatoirement pour sécuriser le QR Code.
- **Ref_Doc_Img :** Le chemin d'accès à l'image du QR Code généré.
